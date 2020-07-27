<?php

declare(strict_types=1);

namespace App\Aspect;

use App\Annotation\ParamsAnnotation;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use Psr\Container\ContainerInterface;
use Hyperf\Di\Aop\ProceedingJoinPoint;

/**
 * 基于aop的api参数验证
 * @Aspect
 */
class ParamsValidate extends AbstractAspect
{
    /**
     * @var ContainerInterface
     */
    protected $container;
    
    /**
     * @var RequestInterface|mixed
     */
    protected $request;
    
    /**
     * @var ResponseInterface|mixed
     */
    protected $response;
    
    /**
     * @var ValidatorFactoryInterface
     */
    protected $validationFactory;
    
    
    public $annotations = [
        ParamsAnnotation::class
    ];
    
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->request = $container->get(RequestInterface::class);
        $this->response = $container->get(ResponseInterface::class);
        $this->validationFactory = $container->get(ValidatorFactoryInterface::class);
    }
    
    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        $paramsAnnotation = $proceedingJoinPoint->getAnnotationMetadata()->method[ParamsAnnotation::class];
        /**
         * @var $paramsAnnotation ParamsAnnotation
         */
        $rules = $paramsAnnotation->rules;
        $messages = $paramsAnnotation->messages ?? [];
        $attributes = $paramsAnnotation->attributes ?? [];
        $validator = $this->validationFactory->make($this->request->all(), $rules, $messages, $attributes);
        if ($validator->fails()) {//自定义错误返回,如需抛异常处理可用validate方法
            return $this->response->json(['code' => 1, 'data' => [], 'msg' => $validator->errors()->first()]);
        }
        return $proceedingJoinPoint->process();
    }
}
