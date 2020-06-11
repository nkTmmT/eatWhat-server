<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 */

namespace App\Controller;

use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Psr\Container\ContainerInterface;

abstract class AbstractController
{
    /**
     * @Inject
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @Inject
     * @var RequestInterface
     */
    protected $request;

    /**
     * @Inject
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @param int $code 请求结果码, 0为请求正常, 1为请求出错或异常
     * @param array $data 返回的数据
     * @param string $msg 请求结果信息,一般用于前端显示
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function response($code = 0, $data = [], $msg = 'ok!'){
        $data = [
            'code' => $code,
            'data' => $data,
            'msg' => $msg
        ];
        return $this->response->json($data);
    }
}
