<?php
/**
 * Created by 我是阿沐.
 * Date: 2021/04/18
 * Time: 下午17:45
 * QQ: 2511221051@qq.com
 */


/**
 * @desc 计数器
 * Class counter
 */
class Counter
{
    /**
     * @desc 第一次请求时间
     * @var int
     */
    protected $first_request_time = 0;

    /**
     * @desc 已产生的请求总量
     * @var int
     */
    protected $request_count = 0;

    /**
     * @desc 指定时间内 最大的请求次数
     * @var int
     */
    protected $request_limit = 100;

    /**
     * @desc 规定时间值
     * @var int
     */
    public $fix_time = 60;

    /**
     * @esc 构造函数
     * counter constructor.
     */
    public function __construct()
    {
        $this->first_request_time = time();
    }

    /**
     * @desc 计数器限流
     * @return bool
     */
    public function counter()
    {
        $curr_time = time();

        // 如果当前请求时间大于第一次请求时间+最大限制时间
        if ($this->first_request_time + $this->fix_time > $curr_time) {

            //若当前的请求数量 大于等于 限制的总数量
            if ($this->request_count >= $this->request_limit) return false;

            $this->request_count++;

            return true;
        } else {
            // 重置第一次请求时间 和 请求总次数
            $this->first_request_time = $curr_time;

            $this->request_count = 1;

            return true;
        }
    }
}