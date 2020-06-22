<?php

declare(strict_types=1);

namespace App\Middleware\Auth;

use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface as HttpResponse;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * 检查access_token的中间件
 * Class AuthTokenMiddleware
 */
class AuthTokenMiddleware implements MiddlewareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;
    
    /**
     * @Inject()
     * @var RequestInterface
     */
    protected $request;
    
    /**
     * @Inject()
     * @var HttpResponse
     */
    protected $response;
    
    /**
     * @Inject()
     * @var CacheInterface
     */
    protected $cache;
    
    /**
     * @var array 不需要检查access_token参数的地址
     */
    private $exceptRoute = [
        '/user/login'
    ];

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
//        var_dump($request);
        $pathInfo = $this->request->getPathInfo();
        if (in_array($pathInfo, $this->exceptRoute)){
            return $handler->handle($request);
        }
        $accessToken = $this->request->query('access_token', '');
        if (empty($accessToken)){
            return $this->response->json(
                [
                    'code' => 1,
                    'data' => [],
                    'msg' => 'token为空',
                ]
            );
        }
        $flag = $this->cache->has($accessToken);
        if (!$flag){
            return $this->response->json(
                [
                    'code' => 1,
                    'data' => [],
                    'msg' => 'token验证不通过',
                ]
            );
        }
        
        return $handler->handle($request);
    }
}