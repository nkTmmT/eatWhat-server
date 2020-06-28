<?php
/**
 * User: TmmT
 * Date: 2020/6/28
 * Time: 20:02
 * Email: 1090718046@qq.com
 * Github: https://github.com/nkTmmT
 */

namespace App\Listener;


use App\Constants\RedisKey;
use App\Model\FoodInfo;
use Hyperf\Database\Model\Events\Created;
use Hyperf\Database\Model\Events\Deleted;
use Hyperf\Database\Model\Events\Event;
use Hyperf\Database\Model\Events\Saved;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Logger\LoggerFactory;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * @Listener()
 * Class FoodInfoListener
 */
class FoodInfoListener implements ListenerInterface
{
    /**
     * @var $cache CacheInterface //貌似不能用Inject注解注入
     */
    private $cache;
    
    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }
    
    /**
     * @return string[] returns the events that you want to listen
     */
    public function listen(): array
    {
        return [
            Deleted::class,
            Saved::class,
        ];
    }
    
    /**
     * Handle the Event when the event is triggered, all listeners will
     * complete before the event is returned to the EventDispatcher.
     */
    public function process(object $event)
    {
        if ($event instanceof Event && $event->getModel()->getTable() == 'food_info'){ //监听food_info数据表改变,更新FOOD_NUM缓存的值
            $count = FoodInfo::query()->count();
            $this->cache->set(RedisKey::FOOD_NUM, $count);
        }
    }
}