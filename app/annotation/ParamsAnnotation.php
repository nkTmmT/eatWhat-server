<?php
/**
 * User: TmmT
 * Date: 2020/7/7
 * Time: 23:58
 * Email: 1090718046@qq.com
 * Github: https://github.com/nkTmmT
 */

namespace App\annotation;


use Doctrine\Common\Annotations\Annotation\Target;
use Hyperf\Di\Annotation\AbstractAnnotation;

/**
 * 用于接口请求参数校验
 * @Annotation()
 * @Target("METHOD")
 */
class ParamsAnnotation extends AbstractAnnotation
{
    /**
     * @var array 验证规则
     */
    public $rules;
    /**
     * @var array 验证错误信息
     */
    public $messages;
    /**
     * @var array 自定义验证属性名
     */
    public $attributes;
    
    public function __construct($value = null)
    {
        parent::__construct($value);
        $this->bindMainProperty('rules', $value);
    }
}