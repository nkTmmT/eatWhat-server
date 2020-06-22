<?php
/**
 * User: TmmT
 * Date: 2020/6/11
 * Time: 19:22
 * Email: 1090718046@qq.com
 * Github: https://github.com/nkTmmT
 */

namespace App\Controller;

use App\Model\FoodInfo;
use App\Model\User;
use App\Model\UserAnli;
use App\Model\UserAte;
use App\Model\UserCollect;
use Hyperf\Contract\ConfigInterface;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\RequestMapping;

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
                $accessToken = md5(password_hash($sessionKey, PASSWORD_DEFAULT));//随机生成token
                $user = User::query()->where('openid', $openid)->first();
                //如果该用户为新用户(即数据库不存在)则新建user记录
                if (!$user){
                    $user = User::firstOrCreate(
                        ['openid' => $openid],
                        ['openid' => $openid, 'unionid' => $unionid]
                    );
                    if (!$user){
                        return $this->formatResponse(1, [], $msg.'数据库保存数据出错!');
                    }
                }
                $this->cache->set($accessToken, array_merge($apiResult, ['user_id' => $user->id]), 24*60*60);//保存在缓存中
                $result = [
                    'accessToken' => $accessToken,
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
    
    /**
     * @PostMapping(path="info")
     * @return array
     */
    public function info()
    {
        /*
         *  userInfo	UserInfo	用户信息对象，不包含 openid 等敏感信息
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
        $userInfo = $info['userInfo'];
        /**
         * 更新用户信息
         * @var $user User
         */
        $user = User::query()->where('openid', $cacheInfo['openid'])->first();
        $user->avatar_url = $userInfo['avatarUrl'];
        $user->city = $userInfo['city'];
        $user->province = $userInfo['province'];
        $user->country = $userInfo['country'];
        $user->gender = $userInfo['gender'];
        $user->username = $userInfo['nickName'];
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
        $user = $this->getUser();
        /**
         * @var $collection UserCollect[]
         */
        $collection = $user->userCollect()->orderBy('created_at', 'desc')->limit(7)->get();//最多只显示七条记录
        $result = [];
        foreach ($collection as $item){
            /**
             * @var FoodInfo $foodInfo
             */
            $foodInfo = $item->foodInfo()->first();
            $result[] = [
                'id' => $foodInfo->id,
                'name' => $foodInfo->name,
                'image' => $foodInfo->image,
                'collect' => true
            ];
        }
//        $result = [
//            [
//                'id' => 1,
//                'name' => '麻辣香锅',
//                'image' => '',
//                'collect' => true
//            ],[
//                'id' => 2,
//                'name' => '螺蛳粉',
//                'image' => '',
//                'collect' => false
//            ],
//        ];
        return $this->formatResponse(0, $result, '获取成功!');
    }
    
    /**
     * @GetMapping(path="my-ate")
     * @return array
     */
    public function myAte()
    {
        $user = $this->getUser();
        /**
         * @var $ate UserAte[]
         */
        $ate = $user->userAte()->orderBy('created_at', 'desc')->limit(7)->get();//最多只显示七条记录
        $result = [];
        foreach ($ate as $item){
            /**
             * @var FoodInfo $foodInfo
             */
            $foodInfo = $item->foodInfo()->first();
            $result[] = [
                'id' => $foodInfo->id,
                'name' => $foodInfo->name,
                'image' => $foodInfo->image,
                'collect' => $item->isCollect()
            ];
        }
//        $result = [
//            [
//                'id' => 1,
//                'name' => '麻辣香锅',
//                'image' => '',
//                'collect' => true
//            ],[
//                'id' => 2,
//                'name' => '螺蛳粉',
//                'image' => '',
//                'collect' => false
//            ],
//        ];
        return $this->formatResponse(0, $result, '获取成功!');
    }
    
    /**
     * @RequestMapping(path="anli")
     */
    public function anli(ConfigInterface $config)
    {
        $documentRoot = $config->get('server.settings.document_root');
//        $files = $this->request->getUploadedFiles();
        $name = $this->request->post('name', '');
        if (empty($name)){
            return $this->formatResponse(1, [], '请输入推荐食物的名字!');
        }
        $reason = $this->request->post('reason', '');
        if (empty($reason)){
            return $this->formatResponse(1, [], '请输入安利该食物的理由!');
        }
        if (!$this->request->hasFile('image')) {
            return $this->formatResponse(1, [], '请上传主图!');
        }
        if (!$this->request->file('image')->isValid()) {
            return $this->formatResponse(1, [], '您上传的图片无效!');
        }
        $file = $this->request->file('image');
        $name = $file->getBasename();//原文件名的后缀名
        $extension = $file->getExtension();//原文件名的后缀名
        $url = DIRECTORY_SEPARATOR.'foodimg'.DIRECTORY_SEPARATOR.date('Ymd').DIRECTORY_SEPARATOR.md5($name).'.'.$extension;
        $path = $documentRoot.$url;
        $file->moveTo($path);//保存到服务器地址
        // 通过 isMoved(): bool 方法判断方法是否已移动
        if (!$file->isMoved()) {//保存文件出错,记录日志,linux常见问题为文件读写权限不够
            echo '文件保存路径为'.$path;
        }
        $image = 'https://'.$this->request->header('host', 'www.tmmt.online').str_replace(DIRECTORY_SEPARATOR, '/', $url);
        $anli = new UserAnli();
        $anli->name = $name;
        $anli->reason = $reason;
        $anli->image = $image;
        $anli->reference_id = $this->getUser()->id;
        if (!$anli->save()){//保存数据库出错,记录日志
        
        }
        return $this->formatResponse(0, [], '安利成功!');
    }
}