<?php
/**
 * Created by 我是阿沐.
 * Date: 2021/04/19
 * Time: 下午21:01
 * QQ: 2511221051@qq.com
 */

namespace amu\redis\currentlimiting;

use Library\Traits\Instance;

/**
 * @desc  redis实现令牌桶操作类
 * Class RedisTokenBucket
 * @package amu\redis\currentlimiting
 */
class RedisTokenBucket
{
    use Instance;

    /**
     * @desc redis实例
     * @var null
     */
    public $redis = null;

    /**
     * @desc 连接ip地址
     * @var string
     */
    private $host = '127.0.0.1';

    /**
     * @desc 连接端口号
     * @var int
     */
    private $port = 6379;

    /**
     * @desc 密码
     * @var string
     */
    private $password = '';

    /**
     * @desc 令牌桶名称(意思就是队列的名称)
     * @var null
     */
    private $queue_name = 'test';

    /**
     * @desc 令牌桶最大令牌容量
     * @var int
     */
    private $max_volume = 10;

    /**
     * @desc 构造函数
     * listCase constructor.
     */
    public function __construct($config = [])
    {
        $this->host = isset($config['host']) ? $config['host'] : $this->host;

        $this->port = isset($config['port']) ? $config['port'] : $this->port;

        $this->password = isset($config['password']) ? $config['password'] : $this->password;

        $this->queue_name = isset($config['queue_name']) ? $config['queue_name'] : $this->queue_name;

        $this->max_volume = isset($config['max_volume']) ? $config['max_volume'] : $this->max_volume;

        try {

            $this->redis = new \Redis();

            $this->redis->connect($this->host, $this->port);

            $this->redis->auth($this->password);

        } catch (\Exception $exception) {

            throw new \Exception($exception->getMessage());
        }
    }

    /**
     * @desc 网令牌桶内添加令牌操作
     * @param int $num 添加的数量
     * @return int
     */
    public function insert($num = 0)
    {
        // 当前桶内令牌剩余数量
        $curr_count = intval($this->redis->lLen($this->queue_name));

        // 计算当前桶内最大可添加多少令牌  若大于桶内令牌 则相减获取应添加数量  否则直接添加
        $num = $this->max_volume >= $curr_count + $num ? $num : $this->max_volume - $curr_count;

        // 若不能添加令牌 则返回添加0个元素
        if ($num <= 0) return 0;

        //添加令牌操作 生成令牌数据
        $tokens = array_fill(0, $num, 1);

        // 批量添加令牌进入队列
        $result = $this->redis->lPush($this->queue_name, ...$tokens);

        if ($result) return $num;

        return 0;
    }

    /**
     * @desc 队列中获取令牌
     * @return bool
     */
    public function get()
    {
        return $this->redis->rPop($this->queue_name) ? true : false;
    }

    /**
     * @desc 初始化令牌桶 将桶内容量用完
     * @return bool
     */
    public function reset()
    {
        //删除队列(桶)
        $this->redis->del($this->queue_name);

        // 重新添加桶内令牌
        $this->insert($this->max_volume);

        return true;
    }
}