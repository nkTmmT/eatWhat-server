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
    
    private $food = [
        [
            'id' => 1,
            'name'=>'黄焖鸡米饭',
            'image' => 'https://bkimg.cdn.bcebos.com/pic/738b4710b912c8fcc3287c65f6039245d78821ba?x-bce-process=image/watermark,g_7,image_d2F0ZXIvYmFpa2U5Mg==,xp_5,yp_5'
        ],
        [
            'id' => 2,
            'name'=>'螺蛳粉',
            'image' => 'https://bkimg.cdn.bcebos.com/pic/4610b912c8fcc3cec3fd2cd2730dc188d43f86948ab3?x-bce-process=image/watermark,g_7,image_d2F0ZXIvYmFpa2U5Mg==,xp_5,yp_5'
        ],
        [
            'id' => 3,
            'name'=>'东坡肉',
            'image' => 'https://bkimg.cdn.bcebos.com/pic/d4628535e5dde7114053daeaaaefce1b9d16612d?x-bce-process=image/watermark,g_7,image_d2F0ZXIvYmFpa2UxMTY=,xp_5,yp_5'
        ],
        [
            'id' => 4,
            'name'=>'煲仔饭',
            'image' => 'https://bkimg.cdn.bcebos.com/pic/f3d3572c11dfa9ec0d86fb9f68d0f703908fc18a?x-bce-process=image/watermark,g_7,image_d2F0ZXIvYmFpa2U5Mg==,xp_5,yp_5'
        ],
        [
            'id' => 5,
            'name'=>'红烧肉',
            'image' => 'https://bkimg.cdn.bcebos.com/pic/d62a6059252dd42a65f3ccbf0e3b5bb5c9eab828?x-bce-process=image/watermark,g_7,image_d2F0ZXIvYmFpa2U4MA==,xp_5,yp_5'
        ],
        [
            'id' => 6,
            'name'=>'麻辣香锅',
            'image' => 'https://bkimg.cdn.bcebos.com/pic/2cf5e0fe9925bc31ef93e93554df8db1ca137055?x-bce-process=image/watermark,g_7,image_d2F0ZXIvYmFpa2U5Mg==,xp_5,yp_5'
        ],
        [
            'id' => 7,
            'name'=>'回锅肉',
            'image' => 'https://bkimg.cdn.bcebos.com/pic/f2deb48f8c5494ee95c8197a21f5e0fe98257eee?x-bce-process=image/watermark,g_7,image_d2F0ZXIvYmFpa2U5Mg==,xp_5,yp_5'
        ],
        [
            'id' => 8,
            'name'=>'鱼香肉丝',
            'image' => 'https://bkimg.cdn.bcebos.com/pic/b3119313b07eca80344f07e49a2397dda144836e?x-bce-process=image/watermark,g_7,image_d2F0ZXIvYmFpa2U4MA==,xp_5,yp_5'
        ],
        [
            'id' => 9,
            'name'=>'火锅',
            'image' => 'https://bkimg.cdn.bcebos.com/pic/91529822720e0cf3c9e46ed90746f21fbe09aa84?x-bce-process=image/watermark,g_7,image_d2F0ZXIvYmFpa2U5Mg==,xp_5,yp_5'
        ],
        [
            'id' => 10,
            'name'=>'宫保鸡丁',
            'image' => 'https://bkimg.cdn.bcebos.com/pic/8cb1cb13495409232ab9ca0e9c58d109b2de49ea?x-bce-process=image/watermark,g_7,image_d2F0ZXIvYmFpa2U4MA==,xp_5,yp_5'
        ],
    ];
    
    /**
     * 摇一摇获取食物信息
     * @GetMapping(path="food")
     */
    public function food(){
        $index = random_int(0 , count($this->food)-1);
        $data = $this->food[$index];
        return $this->formatResponse(0, $data, '获取成功!');
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