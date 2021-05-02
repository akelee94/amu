<?php
/**
 * Created by 我是阿沐.
 * Date: 2021/05/02
 * Time: 下午13:45
 * QQ: 2511221051@qq.com
 */

/**
 * @Desc 位图实战系列
 * Class Bitmap
 */
class Bitmap
{
    /**
     * @desc 用户id
     * @var int
     */
    public $user_id = 0;

    /**
     * @desc 当前月签到key
     * @var string
     */
    public $month_key = 'user:sign:%u:%s';

    public $redis = null;

    /**
     * @desc 构造函数
     * bitmap constructor.
     */
    public function __construct($user_id = 0)
    {
        if ($user_id == 0) throw new Exception('user id not null！');

        $this->user_id = $user_id;

        $this->redis = new Redis();

        $this->redis->connect('127.0.0.1', 6379);
    }


    /**
     * @desc 签到
     * @param string $date
     * @return int
     */
    public function signIn($date = '')
    {
        $key = $this->getKey($date);

        return $this->redis->setBit($key, $this->getCurrentDay($date), 1);
    }

    /**
     * @desc 判断用户在某一天是否签到
     * @param string $date
     * @return int
     */
    public function judgeUserSign($date = '')
    {
        $key = $this->getKey($date);

        return $this->redis->getBit($key, $this->getCurrentDay($date));
    }

    /**
     * @desc 获取用户本月签到的记录列表
     * @param string $date
     * @return mixed
     */
    public function getUserAllSign($date = '')
    {
        $key = $this->getKey($date);

        // 很遗憾 本地reddi并没有支持这个函数
        $result = $this->redis->bitField($key); // 正常这里应该返回的是数组

        $list = [];

        // 获取指定月的月数
        $days = $this->getMonthDays($date);

        // 从低位到高位遍历，0表示未签到；1表示已签到
        for ($i = $days; $i > 0; $i--) {
            if ($i < 0) break;

            $local_date = date('Y-m') . '-' . $i;

            $flag = ($result >> 1 << 1) != $result ? true : false;

            $list[$local_date] = $flag ? 1 : 0;

            $result >>= 1;
        }

        return $list;
    }

    /**
     * @Desc 获取用户当月打卡总数
     * @param string $date
     * @return int
     */
    public function getSumSignCount($date = '')
    {
        $key = $this->getKey($date);

        return $this->redis->bitCount($key);
    }

    /**
     * @desc 获取用户连续签到的次数
     * @param string $date
     * @return int
     */
    public function getContinuousSignCount($date = '')
    {
        $key = $this->getKey($date);

        // 获取今天天数
        $days = $this->getCurrentDay($date);

        //// 获取用户从当前日期开始到 1 号的所有签到状态  不过很遗憾 本地reddi并没有支持这个函数
        $result = $this->redis->bitField($key, 'u' . $days, 0); // 正常这里应该返回的是数组
        // 连续签到计数器总数
        $signCount = 0;

        $value = isset($result[0]) ? $result[0] : 0;

        // 通过位移计算连续签到次数
        for ($i = $days; $i > 0; $i--) // i 表示位移操作次数
        {
            if ($i < 0) break; //超出则终止循环

            // 先右移再左移，如果等于自己说明最低位是 0，表示未签到
            if ($value >> 1 << 1 == $value) { //存在用户当天还未签到，所以要排除掉
                // 低位 0 且非当天说明连续签到中断了
                if ($i != $days){
                    break;
                }
            } else {
                // 如果不等于自己说明最低位是1，表示已经签到
                $signCount++;
            }

            // 右移一位并重新赋值，相当于把最低位丢弃一位然后重新计算
            $value >>= 1;
        }
        return $signCount;
    }

    /**
     * @desc 用户补签
     * @param string $date
     * @return bool|int
     */
    public function rebuildSign($date = '')
    {
        $key = $this->getKey($date);

        // 先检测当前用户这一天是否已经签到
        if ($this->judgeUserSign($date)) return false;


        return $this->signIn($date);
    }

    /**
     * @desc 获取本月总天数
     * @return false|int|string
     */
    private function getMonthDays($date = '')
    {
        $days = $date ? date('t', strtotime($date)) : date("t");

        $cur_year = date('Y');

        //假如是闰年
        if (date('m') == 2) {
            if (($cur_year % 4 == 0 and $cur_year % 100 != 0) || $cur_year % 400 == 0) {
                $days = 29;
            } else {
                $days = 28;
            }
        }

        return $days;
    }

    /**
     * @desc 返回当月用户签到key
     * @param string $date
     * @return string
     */
    private function getKey($date = '')
    {
        $date = $date ? $date : date('Ym');

        $date = date('Ym', strtotime($date));

        return sprintf($this->month_key, $this->user_id, $date);
    }

    /**
     * @desc 计算当前是这个月的第几天 返回减一标识位图的开始下标
     * @param string $date
     * @return false|int|string
     */
    private function getCurrentDay($date = '')
    {
        $date = $date ? $date : date('Y-m-d');

        $day = date('d', strtotime($date));

        return $day - 1;
    }
}