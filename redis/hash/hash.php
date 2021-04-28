<?php
// 实例化redis
$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
//echo "Server is running: " . $redis->ping();

$data = [
    'user_id'    => 1001,
    'user_name'  => '李阿沐',
    'user_email' => '2511221051@qq.com',
    'user_desc'  => '我是阿沐',
];

$key = sprintf('user:info:%u', $data['user_id']);

//向 hash 表中批量添加数据：hMset
$result = $redis->hMSet($key, $data);
$redis->expire($key,120);

if ($result) exit('批量设置用户信息成功！');

exit('批量设置用户信息失败！');

$redis->close();