<?php
/**
 * User: TmmT
 * Date: 2020/6/11
 * Time: 18:20
 * Email: 1090718046@qq.com
 * Github: https://github.com/nkTmmT
 */

namespace App\Controller;
use EasyWeChat\Factory;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Guzzle\ClientFactory;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;

class BasicController
{
    /**
     * @Inject()
     * @var RequestInterface
     */
    protected $request;
    
    /**
     * @Inject()
     * @var ResponseInterface
     */
    protected $response;
    
    /**
     * @Inject()
     * @var ClientFactory
     */
    protected $clientFactory;
    
    protected $miniProgram;
    
    public function __construct(ConfigInterface $config)
    {
        $this->miniProgram = Factory::miniProgram($config->get('wechat.miniProgram'));
    }
    
    /**
     * @param $url string 请求地址
     * @param $method string http 请求方法如 GET, POST
     * @param array $requestOptions
     * @param array $clientOptions
     * @return array|mixed|null
     */
    public function sendHttpRequest($url, $method = 'GET', $requestOptions = [], $clientOptions = [])
    {
        $client = $this->clientFactory->create($clientOptions);
        $response = $client->request($method, $url, $requestOptions);
        return $response->getBody()->getMetadata();
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