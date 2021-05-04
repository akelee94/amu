<?php
/**
 * Created by 我是阿沐.
 * Date: 2021/05/04
 * Time: 下午16:04
 * QQ: 2511221051@qq.com
 */

// 申明测试的平道名称
$channel_names = ['mumu_test1', 'mumu_test2', 'mumu_test3', 'mumu_test4'];

$channel_name = $channel_names[rand(0,3)];

try {
    // 实例化redis类
    $redis = new Redis();
    // 建立redis链接
    $redis->connect('127.0.0.1', 6379);

    for ($i = 0; $i < 10; $i++) {

        $data = array('key' => 'key' . ($i+1), 'msg' => 'I am li a mu !');

        $ret = $redis->publish($channel_name, json_encode($data));

        print_r($ret);
    }
    $redis -> close();
} catch (Exception $e) {
    echo $e->getMessage();
}