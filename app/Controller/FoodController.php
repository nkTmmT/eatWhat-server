<?php
/**
 * User: TmmT
 * Date: 2020/6/11
 * Time: 12:06
 * Email: 1090718046@qq.com
 * Github: https://github.com/nkTmmT
 */

namespace App\Controller;

use App\Constants\RedisKey;
use App\Model\FoodInfo;
use App\Model\UserAte;
use App\Model\UserCollect;
use Hyperf\Contract\ConfigInterface;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\RateLimit\Annotation\RateLimit;

/**
 * Class FoodController
 * @Controller(prefix="food")
 */
class FoodController extends BasicController
{
    /**
     * 摇一摇获取食物信息
     * @RateLimit(create=100, capacity=300) 限流, 令牌生成每秒100个, 峰值每秒300次
     * @GetMapping(path="food")
     * @var $config ConfigInterface
     * @return array
     * @throws \Exception
     */
    public function food(ConfigInterface $config){
        $foodTotalNum = $this->cache->get(RedisKey::FOOD_NUM, 0);
        if (empty($foodTotalNum)){
            $foodTotalNum = FoodInfo::query()->count();
            $this->cache->set(RedisKey::FOOD_NUM, $foodTotalNum);
        }
        if ($foodTotalNum < 1){
            return $this->formatResponse(1, [], '获取失败!');
        }
        $index = random_int(1 , $foodTotalNum);
        /**
         * @var $food FoodInfo
         */
        $food = FoodInfo::findFromCache($index);
        $urlPrefix = 'https://'.$this->request->header('host', $config->get('host'));
        $data = [
            'id' => $food->id,
            'name'=> $food->name,
            'reason' => $food->reason,
            'image' => stristr($food->image, 'http') ? $food->image : $urlPrefix.$food->image //如果是网络图片则不添加请求头,否则加请求头
        ];
        return $this->formatResponse(0, $data, '获取成功!');
    }
    
    /**
     * 收藏食物信息
     * @PostMapping(path="collect")
     * @RateLimit(create=10, capacity=30) 限流, 令牌生成每秒10个, 峰值每秒30次
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Swoole\Exception
     * @return array
     */
    public function collect()
    {
        $id = $this->request->post('id', 0);
        if ($id == 0){
            return $this->formatResponse(1, [], '你传入的数据有误!');
        }
        //执行收藏的具体逻辑
        $user = $this->getUser();
        $collect = UserCollect::updateOrCreate(
            ['user_id' => $user->id, 'food_id' => $id],
            ['updated_at' => date('Y-m-d H:i:s')]
        );
        if (!$collect){//出错记录日志
        
        }
        return $this->formatResponse(0, [], '收藏成功!');
    }
    
    /**
     * 记录吃过的食物
     * @PostMapping(path="ate")
     * @RateLimit(create=10, capacity=30) 限流, 令牌生成每秒10个, 峰值每秒30次
     * @return array
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Swoole\Exception
     */
    public function ate()
    {
        $id = $this->request->post('id', 0);
        $type = $this->request->post('type', 0);
        if ($id <= 0){
            return $this->formatResponse(1, [], '你传入的id有误!');
        }
        if (!in_array($type,[1, 2, 3]) ){
            return $this->formatResponse(1, [], '你传入的类型有误!');
        }
        //执行收藏的具体逻辑
        $user = $this->getUser();
        $ate = UserAte::create(
            ['user_id' => $user->id, 'food_id' => $id, 'type' => $type]
        );
        if (!$ate){//出错记录日志
        
        }
        return $this->formatResponse(0, [], '记录吃过的食物成功!');
    }
}