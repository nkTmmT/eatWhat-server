<?php
/**
 * User: TmmT
 * Date: 2020/6/11
 * Time: 19:22
 * Email: 1090718046@qq.com
 * Github: https://github.com/nkTmmT
 */

namespace App\Controller;

use Hyperf\Config\Annotation\Value;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;

/**
 * Class UserController
 * @Controller(prefix="user")
 */
class UserController extends BasicController
{
    /**
     * @Value("config.key")
     */
    private $configValue;
    
    public function login()
    {
        $msg = '登陆失败!';
        $code = $this->request->post('code', '');
        if (empty($code)){
            return $this->formatResponse(1, [], $msg);
        }
        try{
            $apiResult = $this->miniProgram->auth->session($code);
            if ($apiResult['errcode'] == 0){//登陆成功
                $openid = $apiResult['openid'];
                $unionid = $apiResult['unionid'];
                $sessionKey = $apiResult['session_key']; //保存在缓存中
                $result = [
                    'accessToken' => ''
                ];
                return $this->formatResponse(0, $result, '登陆成功!');
            }else{
                $msg = $apiResult['errmsg'];
            }
        }catch (\Throwable $throwable){
        }
        return $this->formatResponse(1, [], $msg);
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