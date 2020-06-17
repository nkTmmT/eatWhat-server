<?php
/**
 * User: TmmT
 * Date: 2020/6/16
 * Time: 12:03
 * Email: 1090718046@qq.com
 * Github: https://github.com/nkTmmT
 */
declare(strict_types=1);
$appId = 'wx6ccac7748b44e026';
$appSecret = '37c3a51364fd72121c715fee7a1adc9d';
return [
    'miniProgram' => [
        'app_id' => $appId,
        'secret' => $appSecret,
        'log' => [
            'level' => 'debug',
            'file' => __DIR__.'/../../runtime/log/wechat.log',
        ],
    ]
];