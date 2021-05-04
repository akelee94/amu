<?php
/**
 * Created by 我是阿沐.
 * Date: 2021/05/04
 * Time: 下午16:02
 * QQ: 2511221051@qq.com
 */

// 设置php脚本执行时间
set_time_limit(0);

// 申明测试的平道名称
$channel_names = ['mumu_test1', 'mumu_test2', 'mumu_test3'];
//当前执行时间
$cur_time = time();
try {
    // 实例化redis
    $redis = new Redis();

    // 创建redis链接
    $redis->pconnect('127.0.0.1', 6379);
//echo "Server is running: " . $redis->ping();
    //阻塞获取消息
    while (true) {

        // 阻塞获取消息 $redis redis的实例  $channel_name 频道名称  $msg 生产者生成的消息体
        $redis->subscribe($channel_names, function ($redis, $channel_name, $msg) {
            switch ($channel_name) {
                case 'mumu_test1':
                    echo "channel:".$channel_name.",message:".$msg."\n";
                    break;
                case 'mumu_test2':

                    break;
                case 'mumu_test3':

                    break;
            }
            if (!$msg) { //当没有收到消息时 就休眠1s钟
                echo "channel:".$channel_name.",message: not appoint channel name"."\n";
                sleep(1);
            }
        });
        // 本地测试 运行超过10分钟 则自动结束 并关闭redis链接
        if (time() - $cur_time > 10*60){
            $redis -> close();
            break;
        }
    }
} catch (Exception $e) {
    echo $e->getMessage();
}