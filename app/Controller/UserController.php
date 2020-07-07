<?php
/**
 * User: TmmT
 * Date: 2020/6/11
 * Time: 19:22
 * Email: 1090718046@qq.com
 * Github: https://github.com/nkTmmT
 */

namespace App\Controller;

use App\annotation\ParamsAnnotation;
use App\Model\FoodInfo;
use App\Model\User;
use App\Model\UserAnli;
use App\Model\UserAte;
use App\Model\UserCollect;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\RateLimit\Annotation\RateLimit;

/**
 * Class UserController
 * @Controller(prefix="user")
 */
class UserController extends BasicController
{
    /**
     * 利用微信code登陆系统
     * @ParamsAnnotation(rules={"code":"required|string"}, attributes={"code":"微信登陆码"})
     * @PostMapping(path="login")
     * @RateLimit(create=10, capacity=30) 限流, 令牌生成每秒10个, 峰值每秒30次
     * @return array
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function login()
    {
        $msg = '登陆失败!';
        $code = $this->request->post('code', '');
        $apiResult = $this->miniProgram->auth->session($code);
        if (!empty($apiResult) && empty($apiResult['errcode'])){//登陆成功
            $openid = $apiResult['openid'] ?? '';
            $unionid = $apiResult['unionid'] ?? '';
            $sessionKey = $apiResult['session_key'] ?? '';
            $accessToken = md5(password_hash($sessionKey, PASSWORD_DEFAULT));//随机生成token
            /**
             * @var $user User
             */
            $user = User::updateOrCreate(
                ['openid' => $openid],
                ['openid' => $openid, 'unionid' => $unionid]
            );
            if (!$user){
                return $this->formatResponse(1, [], $msg.'数据库保存数据出错!');
            }
            $this->cache->set($accessToken, array_merge($apiResult, ['user_id' => $user->id]), 24*60*60);//保存在缓存中
            $result = [
                'accessToken' => $accessToken,
                'hasInfo' => !empty($user->username) ? true : false
            ];
            return $this->formatResponse(0, $result, '登陆成功!');
        }else{
            $msg = $apiResult['errmsg'];
        }
        return $this->formatResponse(1, [], $msg);
    }
    
    /**
     * 保存用户的开放信息
     * @ParamsAnnotation(rules={"userInfo":"required|array"}, attributes={"userInfo":"用户信息"})
     * @PostMapping(path="info")
     * @RateLimit(create=10, capacity=30) 限流, 令牌生成每秒10个, 峰值每秒30次
     * @return array
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Exception
     */
    public function info()
    {
        $userInfo = $this->request->post('userInfo', []);
        $user = $this->getUser();
        $user->avatar_url = $userInfo['avatarUrl'];
        $user->city = $userInfo['city'];
        $user->province = $userInfo['province'];
        $user->country = $userInfo['country'];
        $user->gender = $userInfo['gender'];
        $user->username = $userInfo['nickName'];
        if (!empty($user->getDirty()) && !$user->save()){ //如果字段没有修改则不需要更新
            return $this->formatResponse(1, [], '数据库保存数据出错!');
        }
        return $this->formatResponse(0, [], '用户信息保存成功!');
    }
    
    /**
     * @GetMapping(path="my-collect")
     * @RateLimit(create=10, capacity=30) 限流, 令牌生成每秒10个, 峰值每秒30次
     * @return array
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Swoole\Exception
     */
    public function myCollect()
    {
        $user = $this->getUser();
        /**
         * @var $collection UserCollect[]
         */
        //最多只显示七条记录,load提前加载关系,避免n+1问题, load方法会将id转换为字符进行in查询(会导致索引失效)
        $collection = $user->userCollect()->orderBy('updated_at', 'desc')->limit(7)->get()->load('foodInfo');
        $result = [];
        foreach ($collection as $item){
            /**
             * @var FoodInfo $foodInfo
             */
            $foodInfo = $item->foodInfo;
            $result[] = [
                'id' => $foodInfo->id,
                'name' => $foodInfo->name,
                'image' => $foodInfo->image,
                'reason' => $foodInfo->reason,
                'collect' => true
            ];
        }
        return $this->formatResponse(0, $result, '获取成功!');
    }
    
    /**
     * @GetMapping(path="my-ate")
     * @RateLimit(create=10, capacity=30) 限流, 令牌生成每秒10个, 峰值每秒30次
     * @return array
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Swoole\Exception
     */
    public function myAte()
    {
        $user = $this->getUser();
        /**
         * @var $ate UserAte[]
         */
        //最多只显示七条记录,load提前加载关系,避免n+1问题, load方法会将id转换为字符进行in查询(会导致索引失效)
        $ate = $user->userAte()->orderBy('created_at', 'desc')->limit(7)->get()->load('foodInfo');
        $result = [];
        foreach ($ate as $item){
            /**
             * @var FoodInfo $foodInfo
             */
            $foodInfo = $item->foodInfo;
            $result[] = [
                'id' => $foodInfo->id,
                'name' => $foodInfo->name,
                'image' => $foodInfo->image,
                'reason' => $foodInfo->reason,
                'collect' => $item->isCollect()
            ];
        }
        return $this->formatResponse(0, $result, '获取成功!');
    }
    
    /**
     * 安利美食
     * @ParamsAnnotation(rules={"name":"required|string", "reason":"required|string", "image":"required|image"}, attributes={"name":"食物名字", "reason":"安利理由", "image":"美食主图" })
     * @PostMapping(path="anli")
     * @RateLimit(create=1, capacity=3) 限流, 令牌生成每秒1个, 峰值每秒3次
     * @return array
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Swoole\Exception
     */
    public function anli()
    {
        $documentRoot = $this->config->get('server.settings.document_root');
        $name = $this->request->post('name', '');
        $reason = $this->request->post('reason', '');
        $file = $this->request->file('image');
        if (!$file->isValid()) {
            return $this->formatResponse(1, [], '您上传的图片无效!');
        }
        $user = $this->getUser();
        if (empty($user->username)){
            return $this->formatResponse(1, [], '您没有权限!');
        }
        $textResult = $this->miniProgram->content_security->checkText($name.'----'.$reason);//文本安全内容检测
        if (!empty($textResult['errcode'])){//说明验证失败了
            return $this->formatResponse(1, [], '您上传的文字内容包含未允许信息!');
        }
        $imageResult = $this->miniProgram->content_security->checkImage($file->getRealPath()); //图片安全内容检测
        if (!empty($imageResult['errcode'])){//说明验证失败了
            return $this->formatResponse(1, [], '您上传的图片包含敏感信息!');
        }
        $fileName = $file->getBasename();//临时原文件名
        $extension = $file->getExtension();//原文件名的后缀名
        $url = DIRECTORY_SEPARATOR.'foodimg'.DIRECTORY_SEPARATOR.md5(date('YmdHis').$fileName).'.'.$extension;
        $path = $documentRoot.$url;
        $file->moveTo($path);//保存到服务器地址
        // 通过 isMoved(): bool 方法判断方法是否已移动
        if (!$file->isMoved()) {//保存文件出错,记录日志,linux常见问题为文件读写权限不够
            return $this->formatResponse(1, [], '安利失败!');
        }
        $image = str_replace(DIRECTORY_SEPARATOR, '/', $url);
        $anli = new UserAnli();
        $anli->name = $name;
        $anli->reason = $reason;
        $anli->image = $image;
        $anli->reference_id = $user->id;
        if (!$anli->save()){//保存数据库出错,记录日志
            return $this->formatResponse(1, [], '安利失败!');
        }
        return $this->formatResponse(0, [], '安利成功!');
    }
    
}