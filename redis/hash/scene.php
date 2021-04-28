<?php
// 实例化redis
$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
//echo "Server is running: " . $redis->ping();

// 抽奖道具列表
const DRAW_PROP_LIST = [
    [
        'prop_id' => 123,
        'prop_name' => '精选课堂笔记',
        'limit' => 10,
        'chance' => 15,
    ],
    [
        'prop_id' => 1234,
        'prop_name' => '阿沐经验总结pdf',
        'limit' => 5,
        'chance' => 10,
    ],
    [
        'prop_id' => 12345,
        'prop_name' => 'python入门实战教程',
        'limit' => 3,
        'chance' => 5,
    ],
    [
        'prop_id' => 123456,
        'prop_name' => 'k8s实践书籍',
        'limit' => 1,
        'chance' => 70,
    ],
];

/**
 * @desc 通过概率选中其中一个数据
 * @param $array
 * @return bool|int|string
 */
function randomChance($array)
{
    if (!$array || count($array) == 0) return false;

    //按照大小排序
    asort($array);
    // 从1到100中随机一个数据
    $random = rand(1, 100);
    $sum = 0;

    $flag = '';
    foreach ($array as $key => $data) {
        $sum += $data;
        if ($random <= $sum) {
            $flag = $key;
            break;
        }
    }
    if ($flag == '') { // 如果传递进来的值的和小于100 ，则取概率最大的。
        $keys = array_keys($array);
        $flag = $keys[count($keys) - 1];
    }
    return $flag;
}

$reward = DRAW_PROP_LIST[randomChance(array_column(DRAW_PROP_LIST, 'chance'))];

$key = "prop:count:record";

for ($i = 1; $i < 10; $i ++) {
    $count = $redis->hIncrBy($key, $reward['prop_id'], 1);

    echo $count.'-';

    if ($count > $reward['limit']) {
        echo '当前道具id为'.$reward['prop_id'].'已被抽奖完毕，可以考虑兜底数据返回给用户';
        break;
    }
}
$redis->expire($key, 120);
$redis->close();