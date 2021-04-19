<?php
/**
 * Created by 我是阿沐.
 * Date: 2021/04/17
 * Time: 下午19:01
 * QQ: 2511221051@qq.com
 */

namespace amu\redis\currentlimiting;

/**
 * @desc 滑动窗口算法
 * Class Slide
 * @package amu\redis\currentlimiting
 */
class Slide
{

    /**
     * @desc 指定时间内 最大的请求次数
     * @var int
     */
    protected $request_limit = 100;

    /**
     * @desc 规定时间值
     * @var int
     */
    protected $fix_time = 60;

    /**
     * @des 构造器
     * Slide constructor.
     */
    public function __construct()
    {

    }

    /**
     * @desc 平滑移动请求 目前网上大部分都是在描述 滑动窗口的使用 并没有用代码实现
     *  此代码是依据掌阅老钱的 python代码 转化给php
     * @return bool
     */
    public function slide()
    {
        $key = 'slide:test:key:user';

        $redis = new \Redis();

        $now_time = time();

        // 事务处理 按照先后顺序把命令放进一个队列当中
        $redis->multi();

        // value + score 都使用毫秒时间戳
        $redis->zAdd($key, $now_time, $now_time);

        // 移除时间窗口之前的请求记录，剩下的就全部都是时间窗口内的
        $redis->zRemRangeByScore($key, 0, $now_time - $this->fix_time);

        // 获取窗口内的记录数量
        $redis->zCard($key);

        // 设置 移动窗口的过期时间 避免占用过多的内存 过期时间等于最大窗口长度 额外补加1s
        $redis->expire($key, $this->fix_time + 1);

        // 批量执行 此操作是原子性的
        $result = $redis->exec();

        $current_count = isset($result[3]) ?$result[3]:0;

        return $current_count < $this->request_limit;
    }
}