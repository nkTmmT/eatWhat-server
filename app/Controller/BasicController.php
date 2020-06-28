<?php
/**
 * User: TmmT
 * Date: 2020/6/11
 * Time: 18:20
 * Email: 1090718046@qq.com
 * Github: https://github.com/nkTmmT
 */

namespace App\Controller;
use App\Model\User;
use EasyWeChat\Factory;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Guzzle\ClientFactory;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Psr\SimpleCache\CacheInterface;
use Swoole\Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class BasicController
{
    /**
     * @var RequestInterface
     */
    protected $request;
    
    /**
     * @var ResponseInterface
     */
    protected $response;
    
    /**
     * @var ConfigInterface
     */
    protected $config;
    
    /**
     * @Inject()
     * @var ClientFactory
     */
    protected $clientFactory;
    
    /**
     * @var CacheInterface
     */
    protected $cache;
    
    /**
     * @var \EasyWeChat\MiniProgram\Application
     */
    protected $miniProgram;
    
    public function __construct(ConfigInterface $config, RequestInterface $request, ResponseInterface $response, CacheInterface $cache)
    {
        $this->request = $request;
        $this->response = $response;
        $this->config = $config;
        $this->cache = $cache;
        $get = $this->request->getQueryParams();
        $post = $this->request->getParsedBody();
        $cookie = $this->request->getCookieParams();
        /**
         * @var \Hyperf\HttpMessage\Upload\UploadedFile [] $files
         */
        $files = $this->request->getUploadedFiles();//Hyperf\HttpMessage\Upload\UploadedFile[]
        $castUploadFiles = [];
        foreach ($files as $file){ //由于easyWechat 用的上传文件类型为Symfony\Component\HttpFoundation\File\UploadedFile,需要进行转换
            //在思否上进行分享该方法
            $castUploadFiles[] = new UploadedFile($file->getRealPath(), $file->getBasename(), $file->getMimeType(), $file->getError());
        }
        $server = $this->request->getServerParams();
        $xml = $this->request->getBody()->getContents();
        $this->miniProgram = Factory::miniProgram($config->get('wechat.miniProgram'));
        $this->miniProgram['cache'] = $cache;//EasyWeChat 默认使用 文件缓存，替换为 Redis 缓存
        $this->miniProgram['request'] = new Request($get,$post,[],$cookie,$castUploadFiles,$server,$xml);
    }
    
    /**
     * @param $url string 请求地址
     * @param $method string http 请求方法如 GET, POST
     * @param array $requestOptions
     * @param array $clientOptions
     * @return array|mixed|null
     * @throws
     */
    public function sendHttpRequest($url, $method = 'GET', $requestOptions = [], $clientOptions = [])
    {
        $client = $this->clientFactory->create($clientOptions);
        $response = $client->request($method, $url, $requestOptions);
        return $response->getBody()->getMetadata();
    }
    
    /**
     * @return mixed
     * @throws Exception
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getTokenCache()
    {
        $cacheInfo = $this->cache->get($this->request->query('access_token', ''), null);
        if (empty($cacheInfo)){
            throw new Exception();
        }
        return $cacheInfo;
    }
    
    /**
     * 获取当前请求对应的用户
     * @return User | null
     * @throws Exception
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getUser()
    {
        $cacheInfo = $this->getTokenCache();
        return User::findFromCache($cacheInfo['user_id']);
    }
    
    /**
     * @param int $code 请求结果码, 0为请求正常, 1为请求出错或异常
     * @param array $data 返回的数据
     * @param string $msg 请求结果信息,一般用于前端显示
     * @return array
     */
    public function formatResponse($code = 0, $data = [], $msg = 'ok!'){
        $result = [
            'code' => $code,
            'data' => $data,
            'msg' => $msg
        ];
        return $result;
    }
}