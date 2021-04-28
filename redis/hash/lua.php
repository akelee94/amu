<?php
// 实例化redis
$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
//echo "Server is running: " . $redis->ping();

$lua = <<<SCRIPT
    local _key    = KEYS[1]
    local limit   = ARGV[1]
    local num     = ARGV[2]
    
    local current_num = redis.call('get', _key)
    current_num = tonumber(current_num) or 0
    num   = tonumber(num)
    limit = tonumber(limit)
    
    if (current_num + num) <= limit then
        local ret, err = redis.call('set', _key, current_num + num)
        if ret then
            redis.call('expire', _key, 120)
            return 1
        end
    end
    
   return 0
SCRIPT;

$prop_id = 123456;
$key = 'prop:count:record:'.$prop_id;
for ($i = 1; $i < 10; $i++) {
  $result = $redis->eval($lua, array($key, 5, 1), 1);
  if ($result == 0) {
      echo '当前道具id为'.$prop_id.'已被抽奖完毕，可以考虑兜底数据返回给用户';
      break;
  }
}

$redis->close();

