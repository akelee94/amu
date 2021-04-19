<?php

require_once './Counter.php';
require_once './LeakyBucket.php';
require_once './TokenBucket.php';
require_once './RedisTokenBucket.php';

$error_count = $success_count = 0;

// 计数器
//$counter = new Counter();
//for ($i = 0; $i < 200; $i++) {
//    $result = $counter->counter();
//    if (!$result) {
//        $error_count++;
//        continue;
//    }
//    $success_count++;
//}
//
//echo sprintf("请求成功%u次，请求失败%u次", $success_count, $error_count);


//漏桶算法
//$leaky_bucket = new LeakyBucket();
//for ($i = 0; $i < 500; $i++) {
//    $result = $leaky_bucket->leaky();
//
//    if ($result) {
//        $success_count++;
//        continue;
//    }
//    $error_count++;
//}
//
//echo sprintf("请求成功%u次，请求失败%u次", $success_count, $error_count);


// 令牌桶
//$token_bucket = new TokenBucket();
//for ($i = 0; $i < 500; $i++) {
//    $result = $token_bucket->token();
//    if ($result) {
//        $success_count++;
//        continue;
//    }
//    $error_count++;
//}
//echo sprintf("请求成功%u次，请求失败%u次", $success_count, $error_count);


// redis版本令牌

//$redis_token = new RedisTokenBucket();
//
//// 重置令牌桶
//$redis_token->reset();
//
//// 拿出桶内的全部令牌  并记录成功数
//$get_succ = 0;
//for ($i = 0; $i < 20; $i++) {
//    if ($redis_token->get()) $get_succ = $get_succ + 1;
//}
//
//// 向令牌桶添加15个令牌 会有5个添加失败
//$insert_num = $redis_token->insert(15);
//
//echo sprintf("获取令牌成功数%u，添加令牌成功个数%u", $get_succ, $insert_num);