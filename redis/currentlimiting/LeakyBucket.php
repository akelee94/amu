<?php
/**
 * Created by 我是阿沐.
 * Date: 2021/04/18
 * Time: 下午21:45
 * QQ: 2511221051@qq.com
 */

namespace amu\redis\currentlimiting;

use Library\Traits\Instance;

/**
 * @desc 漏斗算法的实现
 * Class LeakyBucket
 * @package amu\redis\currentlimiting
 */
class LeakyBucket
{
    use Instance;

    /**
     * @Desc 桶内的总容量
     * @var int
     */
    public $capacity = 100;

    /**
     * @desc 水桶水的流出速率
     * @var int
     */
    public $rate = 10;

    /**
     * @desc 当前的水量 即是累计的请求数
     * @var int
     */
    public $water = 0;

    /**
     * @desc 上一次请求的时间
     * @var int
     */
    private $last_request_time = 0;

    /**
     * @desc 构造函数
     * LeakyBucket constructor.
     */
    public function __construct($config = [])
    {
        $this->capacity = isset($config['capacity'])?$config['capacity']:$this->capacity;
        $this->rate     = isset($config['rate'])?$config['rate']:$this->rate;
        $this->water    = isset($config['water'])?$config['water']:$this->water;
        $this->last_request_time = time();
    }

    /**
     * @desc 漏桶算法
     * @return bool
     */
    public function leaky()
    {
        // 当前请求时间戳
        $curr_time = time();

        // 计算当前请求的使用水量
        $out_water = ($curr_time - $this->last_request_time) * $this->rate;

        // 计算漏斗内的水量剩余多少
        $this->water = max(0, $this->water - $out_water);

        $this->last_req_time = $curr_time;

        // 若桶内水量还没有满 则往桶内继续加水
        if ($this->water < $this->capacity) {
            $this->water = $this->water + 1;
        }

        // 抱歉水桶已经满了，拒绝加水量
        return false;
    }
}