<?php
/**
 * User: TmmT
 * Date: 2020/6/11
 * Time: 12:06
 * Email: 1090718046@qq.com
 * Github: https://github.com/nkTmmT
 */

namespace App\Controller;

use App\Model\UserAte;
use App\Model\UserCollect;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;

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
            'reason' => '黄焖鸡三绝：一品汤，汤色红亮、口感滋糯、老道醇厚、油而不腻.、回味无穷；二品肉，成品靓丽、肉质滑嫩；三品米，香气浓郁、劲道有韧劲',
            'image' => 'https://bkimg.cdn.bcebos.com/pic/738b4710b912c8fcc3287c65f6039245d78821ba?x-bce-process=image/watermark,g_7,image_d2F0ZXIvYmFpa2U5Mg==,xp_5,yp_5'
        ],
        [
            'id' => 2,
            'name'=>'螺蛳粉',
            'reason' => '螺蛳粉除了鲜、酸、爽、烫，辣味也是它的独特之处。它由柳州特有的软韧爽口的米粉，加上酸笋、花生、油炸腐竹、黄花菜、萝卜干、鲜嫩青菜等配料及浓郁适度的酸辣味和煮烂螺蛳的汤水调合而成，因有奇特鲜美的螺蛳汤，使人吃一想二。未尝其味先观其色便会令人垂涎欲滴，红通通的是漂浮在上面的一层辣椒油，绿油油的是时令青菜，鲜美的螺蛳汤渗透每一根粉条，螺蛳粉中的辣椒油与普通的辣不同，此种带着有侵略性的辣，嫩滑香酥得让人忘了本位',
            'image' => 'https://bkimg.cdn.bcebos.com/pic/4610b912c8fcc3cec3fd2cd2730dc188d43f86948ab3?x-bce-process=image/watermark,g_7,image_d2F0ZXIvYmFpa2U5Mg==,xp_5,yp_5'
        ],
        [
            'id' => 3,
            'name'=>'东坡肉',
            'reason' => '薄皮嫩肉，色泽红亮，味醇汁浓，酥烂而形不碎，香糯而不腻口',
            'image' => 'https://bkimg.cdn.bcebos.com/pic/d4628535e5dde7114053daeaaaefce1b9d16612d?x-bce-process=image/watermark,g_7,image_d2F0ZXIvYmFpa2UxMTY=,xp_5,yp_5'
        ],
        [
            'id' => 4,
            'name'=>'煲仔饭',
            'reason' => '在饥肠漉漉的早上谈煲仔饭，对消化系统的刺激无异于一株虚幻的青梅树之于冒火的口腔，望梅止渴，然后更渴。不过，大街上有无数的港式茶餐厅，太阳明晃晃的中午，怀着半个早晨的期许，坐在冷气清凉的玻璃后面吃火热的煲仔饭也是件美事',
            'image' => 'https://bkimg.cdn.bcebos.com/pic/f3d3572c11dfa9ec0d86fb9f68d0f703908fc18a?x-bce-process=image/watermark,g_7,image_d2F0ZXIvYmFpa2U5Mg==,xp_5,yp_5'
        ],
        [
            'id' => 5,
            'name'=>'红烧肉',
            'reason' => '肥瘦相间，香甜松软，入口即化。',
            'image' => 'https://bkimg.cdn.bcebos.com/pic/d62a6059252dd42a65f3ccbf0e3b5bb5c9eab828?x-bce-process=image/watermark,g_7,image_d2F0ZXIvYmFpa2U4MA==,xp_5,yp_5'
        ],
        [
            'id' => 6,
            'name'=>'麻辣香锅',
            'reason' => '1、麻辣香锅以麻辣为 主，其特色明显，口味独特，其他大众餐厅无法比拟；
2、麻辣香锅消费群体独特，消费意识明显，不用和其他餐厅分享客源；
3、麻辣香锅不用找大 厨，不用建复杂的厨房，操作简单，投资小、收益快；
4、麻辣香锅秘制配方，其他人不易跟风，市场大、竞争小。
',
            'image' => 'https://bkimg.cdn.bcebos.com/pic/2cf5e0fe9925bc31ef93e93554df8db1ca137055?x-bce-process=image/watermark,g_7,image_d2F0ZXIvYmFpa2U5Mg==,xp_5,yp_5'
        ],
        [
            'id' => 7,
            'name'=>'回锅肉',
            'reason' => '回锅肉口味独特，色泽红亮，肥而不腻，入口浓香。
所谓回锅，就是再次烹调的意思。回锅肉作为一道传统川菜，在川菜中的地位是非常重要的，川菜考级经常用回锅肉作为首选菜肴。回锅肉一直被认为是川菜之首，川菜之化身，提到川菜必然想到回锅肉。它色香味俱全，颜色养眼，是下饭菜之首选',
            'image' => 'https://bkimg.cdn.bcebos.com/pic/f2deb48f8c5494ee95c8197a21f5e0fe98257eee?x-bce-process=image/watermark,g_7,image_d2F0ZXIvYmFpa2U5Mg==,xp_5,yp_5'
        ],
        [
            'id' => 8,
            'name'=>'鱼香肉丝',
            'reason' => '鱼香肉丝选料精细，成菜色泽红润、富鱼香味，吃起来具有咸甜酸辣兼备的特点，肉丝质地柔滑软嫩。',
            'image' => 'https://bkimg.cdn.bcebos.com/pic/b3119313b07eca80344f07e49a2397dda144836e?x-bce-process=image/watermark,g_7,image_d2F0ZXIvYmFpa2U4MA==,xp_5,yp_5'
        ],
        [
            'id' => 9,
            'name'=>'火锅',
            'reason' => '火锅不仅是美食，而且蕴含着饮食文化的内涵，为人们品尝倍添雅趣。吃火锅时，男女老少、亲朋好友围着热气腾腾的火锅，把臂共话，举箸大啖，温情荡漾，洋溢着热烈融洽的气氛，适合了大团圆这一中国传统文化',
            'image' => 'https://bkimg.cdn.bcebos.com/pic/91529822720e0cf3c9e46ed90746f21fbe09aa84?x-bce-process=image/watermark,g_7,image_d2F0ZXIvYmFpa2U5Mg==,xp_5,yp_5'
        ],
        [
            'id' => 10,
            'name'=>'宫保鸡丁',
            'reason' => '宫保鸡丁的特色是辣中有甜，甜中有辣，鸡肉的鲜嫩配合花生的香脆，入口鲜辣酥香，红而不辣，辣而不猛，肉质滑脆。
宫保鸡丁入口之后，舌尖先感觉微麻、浅辣，而后冲击味蕾的是一股甜意，咀嚼时又会有些“酸酸”的感觉，麻、辣、酸、甜包裹下的鸡丁、葱段、花生米使人欲罢不能',
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
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Swoole\Exception
     * @return array
     */
    public function collect()
    {
        $id = $this->request->post('id', 0);
        if ($id == 0){
            return $this->formatResponse(1, [], '你传入的数据有误!');
        }
        //执行收藏的具体逻辑
        $user = $this->getUser();
        $collect = UserCollect::firstOrCreate(
            ['user_id' => $user->id, 'food_id' => $id],
            ['user_id' => $user->id, 'food_id' => $id]
        );
        if ($collect){//出错记录日志
        
        }
        return $this->formatResponse(0, [], '收藏成功!');
    }
    
    /**
     * 记录吃过的食物
     * @PostMapping(path="ate")
     * @return array
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Swoole\Exception
     */
    public function ate()
    {
        $id = $this->request->post('id', 0);
        $type = $this->request->post('type', 0);
        if ($id <= 0){
            return $this->formatResponse(1, [], '你传入的id有误!');
        }
        if (!in_array($type,[1, 2, 3]) ){
            return $this->formatResponse(1, [], '你传入的类型有误!');
        }
        //执行收藏的具体逻辑
        $user = $this->getUser();
        $ate = UserAte::firstOrCreate(
            ['user_id' => $user->id, 'food_id' => $id, 'type' => $type],
            ['user_id' => $user->id, 'food_id' => $id, 'type' => $type]
        );
        if ($ate){//出错记录日志
        
        }
        return $this->formatResponse(0, [], '记录吃过的食物成功!');
    }
}