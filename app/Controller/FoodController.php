<?php
/**
 * User: TmmT
 * Date: 2020/6/11
 * Time: 12:06
 * Email: 1090718046@qq.com
 * Github: https://github.com/nkTmmT
 */

namespace App\Controller;

use Hyperf\HttpServer\Annotation\AutoController;

/**
 * Class FoodController
 * @package App\Controller
 * @AutoController()
 */
class FoodController extends AbstractController
{
    public function getFood(){
        return $this->response(0, [], '你好');
    }
}