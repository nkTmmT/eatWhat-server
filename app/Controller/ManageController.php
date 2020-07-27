<?php
/**
 * User: TmmT
 * Date: 2020/7/23
 * Time: 19:01
 * Email: 1090718046@qq.com
 * Github: https://github.com/nkTmmT
 */

namespace App\Controller;

use App\Annotation\ParamsAnnotation;
use App\Event\AnliPass;
use App\Event\AnliRefuse;
use App\Exception\BusinessException;
use App\Model\FoodInfo;
use App\Model\RefuseReason;
use App\Model\UserAnli;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Class ManageController
 * @Controller(prefix="manage")
 */
class ManageController extends BasicController
{
    
    /**
     * @Inject()
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;
    
    /**
     * ManageController constructor.
     * @param ContainerInterface $container
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $user = $this->getUser();
        if (!$user->isAdmin()) {
            throw new BusinessException(1, '你没有权限');
        }
    }
    
    /**
     * admin用户获取所有未处理的用户安利
     * @GetMapping(path="anli")
     */
    public function anli()
    {
        $anli = UserAnli::query()->select(['id', 'name', 'reason', 'image', 'created_at'])->where('status', '=', UserAnli::STATUS_NORMAL)->withCasts([
            'created_at' => 'datetime:Y-m-d'
        ])->get();
        return $this->success($anli);
    }
    
    /**
     * 通过用户安利内容
     * @ParamsAnnotation(rules={"id": "required|numeric|min:1"}, attributes={"id":"安利id"})
     * @PostMapping(path="accept")
     * @return array
     */
    public function passAnli()
    {
        $id = $this->request->post('id');
        Db::beginTransaction();
        try {
            /**
             * @var $anli UserAnli
             */
            $anli = UserAnli::query()->where('status', '=', UserAnli::STATUS_NORMAL)->find($id);
            $anli->status = UserAnli::STATUS_SUCCESS;
            if (!$anli->save()) {
                throw new BusinessException(1, '业务繁忙');
            }
            $foodInfo = new FoodInfo();
            $foodInfo->name = $anli->name;
            $foodInfo->image = $anli->image;
            $foodInfo->reason = $anli->reason;
            $foodInfo->reference_id = $anli->reference_id;
            if (!$foodInfo->save()) {//抛异常,记录日志
                throw new BusinessException(1, '业务繁忙');
            }
            Db::commit();
            //发送订阅消息,告知用户已通过并感谢(触发事件,由事件解耦处理)
            $this->eventDispatcher->dispatch(new AnliPass($anli));
            return $this->success();
        } catch (\Throwable $ex) {
            Db::rollBack();
            return $this->fail($ex->getCode(), $ex->getMessage());
        }
    }
    
    /**
     * 不予通过审核
     * @ParamsAnnotation(rules={"id": "required|numeric|min:1", "reasonId": "required|numeric|min:1"}, attributes={"id":"安利id", "reasonId":"理由id"})
     * @PostMapping(path="refuse")
     * @return array
     */
    public function notPassAnli()
    {
        $id = $this->request->post('id');
        $reasonId = $this->request->post('reasonId');
        /**
         * @var $reason RefuseReason
         */
        $reason = RefuseReason::query()->find($reasonId);
        /**
         * @var $anli UserAnli
         */
        $anli = UserAnli::query()->where('status', '=', UserAnli::STATUS_NORMAL)->find($id);
        $anli->status = UserAnli::STATUS_FAIL;
        if (!$anli->save()) {
            return $this->fail(1, '业务繁忙');
        }
        //发送订阅消息,告知未通过理由(触发事件,由事件解耦处理)
        $this->eventDispatcher->dispatch(new AnliRefuse($anli, $reason));
        return $this->success();
    }
    
    /**
     * 获取用于前端显示拒绝通过的理由
     * @GetMapping(path="reasons")
     * @return array
     */
    public function refuseReasons()
    {
        $reasons = RefuseReason::query()->select(['id', 'reason'])->get();
        return $this->success($reasons);
    }
}