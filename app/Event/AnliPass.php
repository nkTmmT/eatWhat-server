<?php
/**
 * User: TmmT
 * Date: 2020/7/24
 * Time: 20:19
 * Email: 1090718046@qq.com
 * Github: https://github.com/nkTmmT
 */

namespace App\Event;


use App\Model\UserAnli;

class AnliPass
{
    public $anli;
    
    public function __construct(UserAnli $anli)
    {
        $this->anli = $anli;
    }
}