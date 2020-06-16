<?php
/**
 * User: TmmT
 * Date: 2020/6/11
 * Time: 12:06
 * Email: 1090718046@qq.com
 * Github: https://github.com/nkTmmT
 */

namespace App\Controller;

use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Contract\RequestInterface;

/**
 * Class FoodController
 * @Controller(prefix="food")
 */
class FoodController extends BasicController
{
    /**
     * 摇一摇获取食物信息
     * @GetMapping(path="food")
     */
    public function food(){
        $result = [
            'code'=>0,
            'data'=>[
                'id' => 1,
                'name'=>'黄焖鸡米饭',
                'image' => 'https://bkimg.cdn.bcebos.com/pic/738b4710b912c8fcc3287c65f6039245d78821ba?x-bce-process=image/watermark,g_7,image_d2F0ZXIvYmFpa2U5Mg==,xp_5,yp_5'
            ],
            'msg'=>'你好'
        ];
        return $result;
    }
    
    /**
     * 收藏食物信息
     * @PostMapping(path="collect")
     * @return array
     */
    public function collect()
    {
        $id = $this->request->post('id', 0);
        if ($id == 0){
            return $this->formatResponse(1, [], '你传入的数据有误!');
        }
        //执行收藏的具体逻辑
        return $this->formatResponse(0, [], '收藏成功!');
    }
}