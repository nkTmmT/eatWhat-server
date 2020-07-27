<?php
declare(strict_types=1);

/**
 * User: TmmT
 * Date: 2020/7/23
 * Time: 7:33
 * Email: 1090718046@qq.com
 * Github: https://github.com/nkTmmT
 */

namespace App\Job;

use Hyperf\AsyncQueue\Job;

class MessageJob extends Job
{
    public $params;
    
    public function __construct($params)
    {
        // 这里最好是普通数据，不要使用携带 IO 的对象，比如 PDO 对象
        $this->params = $params;
    }
    
    /**
     * Handle the job.
     */
    public function handle()
    {
        //发送消息
    }
}