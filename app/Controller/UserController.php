<?php
/**
 * User: TmmT
 * Date: 2020/6/11
 * Time: 19:22
 * Email: 1090718046@qq.com
 * Github: https://github.com/nkTmmT
 */

namespace App\Controller;

use App\Model\User;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\Utils\ApplicationContext;
use Psr\SimpleCache\CacheInterface;

/**
 * Class UserController
 * @Controller(prefix="user")
 */
class UserController extends BasicController
{
    /**
     * @PostMapping(path="login")
     * @return array
     */
    public function login()
    {
        $msg = '登陆失败!';
        $code = $this->request->post('code', '');
        if (empty($code)){
            return $this->formatResponse(1, [], $msg.'code为空');
        }
        try{
            $apiResult = $this->miniProgram->auth->session($code);
            if (!empty($apiResult) && empty($apiResult['errcode'])){//登陆成功
                $openid = $apiResult['openid'] ?? '';
                $unionid = $apiResult['unionid'] ?? '';
                $sessionKey = $apiResult['session_key'] ?? '';
                $accessToken = password_hash($sessionKey, PASSWORD_DEFAULT);
                //新建user记录
                $user = User::firstOrCreate(
                    ['openid' => $openid],
                    ['openid' => $openid, 'unionid' => $unionid]
                );
                if (!$user){
                    return $this->formatResponse(1, [], $msg.'数据库保存数据出错!');
                }
                $this->cache->set($accessToken, $apiResult);//保存在缓存中
                $result = [
                    'accessToken' => $accessToken,
                    'openid' => $openid,
                    'unionid' => $unionid,
                    'sessionKey' => $sessionKey,
                    'apiResult' => $apiResult
                ];
                return $this->formatResponse(0, $result, '登陆成功!');
            }else{
                $msg = $apiResult['errmsg'];
            }
        }catch (\Throwable $throwable){
            $msg .= '系统异常,'.$throwable->getMessage();
        }catch (\Psr\SimpleCache\InvalidArgumentException $exception){ //缓存异常,会导致所有需要token的接口访问异常
            $msg .= '缓存异常,'.$exception->getMessage();
        }
        return $this->formatResponse(1, [], $msg);
    }
    
    public function info()
    {
        /*
         * userInfo	UserInfo	用户信息对象，不包含 openid 等敏感信息
            rawData	string	不包括敏感信息的原始数据字符串，用于计算签名
            signature	string	使用 sha1( rawData + sessionkey ) 得到字符串，用于校验用户信息，详见 用户数据的签名验证和加解密
            encryptedData	string	包括敏感数据在内的完整用户信息的加密数据，详见 用户数据的签名验证和加解密
            iv	string	加密算法的初始向量，详见 用户数据的签名验证和加解密
         */
        $info = $this->request->post('info', []);
        if (empty($info)){
            return $this->formatResponse(1, [], '加密的信息为空');
        }
        $accessToken = $this->request->query('access_token', '');
        try{
            $cacheInfo = $this->cache->get($accessToken);
        }catch (\Psr\SimpleCache\InvalidArgumentException $exception){
            return $this->formatResponse(1, [], '缓存系统出错!');
        }
        /**
         * @var $user User
         */
        $user = User::query()->where('openid', $cacheInfo['openid'])->first();
        $user->avatar_url = $info['avatarUrl'];
        $user->city = $info['city'];
        $user->province = $info['province'];
        $user->country = $info['country'];
        $user->gender = $info['gender'];
        $user->username = $info['nickName'];
        if (!$user->save()){
            return $this->formatResponse(1, [], '数据库保存数据出错!');
        }
        return $this->formatResponse(0, [], '用户信息保存成功!');
    }
    /**
     * @GetMapping(path="my-collect")
     * @return array
     */
    public function myCollect()
    {
        $result = [
            [
                'id' => 1,
                'name' => '麻辣香锅',
                'image' => '',
                'collect' => true
            ],[
                'id' => 2,
                'name' => '螺蛳粉',
                'image' => '',
                'collect' => false
            ],
        ];
        return $this->formatResponse(0, $result, '获取成功!');
    }
    
    /**
     * @GetMapping(path="my-ate")
     * @return array
     */
    public function myAte()
    {
        $result = [
            [
                'id' => 1,
                'name' => '麻辣香锅',
                'image' => '',
                'collect' => true
            ],[
                'id' => 2,
                'name' => '螺蛳粉',
                'image' => '',
                'collect' => false
            ],
        ];
        return $this->formatResponse(0, $result, '获取成功!');
    }
}