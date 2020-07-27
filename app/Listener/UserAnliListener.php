<?php
/**
 * User: TmmT
 * Date: 2020/7/23
 * Time: 06:40
 * Email: 1090718046@qq.com
 * Github: https://github.com/nkTmmT
 */

namespace App\Listener;


use App\Event\AnliPass;
use App\Event\AnliRefuse;
use App\Service\QueueService;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Psr\Container\ContainerInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * @Listener()
 * Class FoodInfoListener
 */
class UserAnliListener implements ListenerInterface
{
    /**
     * @var $cache CacheInterface //貌似不能用Inject注解注入
     */
    private $cache;
    
    /**
     * @var $service QueueService
     */
    private $service;
    
    public function __construct(ContainerInterface $container)
    {
        $this->cache = $container->get(CacheInterface::class);
//        $this->cache = $container->get(QueueService::class);
    }
    
    /**
     * @return string[] returns the events that you want to listen
     */
    public function listen(): array
    {
        return [//应该每个事件都要自定义,不能简单监听数据库改变事件
            AnliPass::class,
            AnliRefuse::class,
        ];
    }
    
    /**
     * Handle the Event when the event is triggered, all listeners will
     * complete before the event is returned to the EventDispatcher.
     */
    public function process(object $event)
    {
        if ($event instanceof AnliPass) { //通过安利
            /**
             * @var $event AnliPass
             */
            $anli = $event->anli;
            var_dump($anli);
        } elseif ($event instanceof AnliRefuse) {
            /**
             * @var $event AnliRefuse
             */
            $anli = $event->anli;
            $reason = $event->reason;
            var_dump($anli);
            var_dump($reason);
        }
    }
}