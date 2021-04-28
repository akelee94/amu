<?php

require_once './listCase.php';
require_once './consume.php';

$model = new listCase();

// push数据进队列
$result =  $model->push('amumu',['id' => 1001,'name' => '我是阿沐']);
var_dump($result);die;

// push延迟队列
//$result =  $model->pushDelay('amumu2', ['id' => 1001,'name' => '我是阿沐']);
//var_dump($result);die;

//$consume = new consume();

// 然后执行队列消费
//$result = $model->handle('amumu', [$consume, 'receive']);
//var_dump($result);


//延迟队列消费
//$consume = new consume();
//$result = $model->handleDelay('amumu2', [$consume, 'delay']);
//var_dump($result);

$redis->close();