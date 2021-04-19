<?php
/**
 * Created by 我是阿沐.
 * Date: 2021/04/19
 * Time: 下午13:14
 * QQ: 2511221051@qq.com
 */


/**
 * @desc 令牌桶算法
 * Class TokenBucket
 * @package amu\redis\currentlimiting
 */
class TokenBucket
{
    /**
     * @Desc 桶内的总容量
     * @var int
     */
    public $capacity_size = 100;

    /**
     * @desc 令牌放入的速度(个/秒)
     * @var int
     */
    public $create_token_rate = 10;

    /**
     * @desc 当前可用令牌的数量
     * @var int
     */
    public $tokens = 100;

    /**
     * @desc 上一次请求的时间
     * @var int
     */
    private $last_request_time = 0;

    /**
     * @desc 构造函数
     * TokenBucket constructor.
     */
    public function __construct($config = [])
    {
        $this->capacity_size = isset($config['size']) ? $config['size'] : $this->capacity_size;

        $this->create_token_rate = isset($config['rate']) ? $config['rate'] : $this->create_token_rate;

        $this->tokens = isset($config['tokens']) ? $config['tokens'] : $this->tokens;

        $this->last_request_time = time();

    }

    /**
     * @desc 令牌桶方法
     * @return bool
     */
    public function token()
    {
        // 当前请求时间戳
        $curr_time = time();

        // 计算当前生产的令牌数
        $inside_token = ($curr_time - $this->last_request_time) * $this->create_token_rate;

        // 计算桶内还能使用的令牌数量
        $this->tokens = min($this->capacity_size, $this->tokens +  $inside_token);

        $this->last_req_time = $curr_time;

        // 假如桶内的令牌数少于1个 则拒绝获取
        if ($this->tokens < 1) return  false;

        // 令牌数还有 则移除一个
        $this->tokens = $this->tokens - 1;

        return true;
    }
}