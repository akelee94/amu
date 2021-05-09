<?php
/**
 * Created by 我是阿沐.
 * Date: 2021/05/08
 * Time: 下午13:02
 * QQ: 2511221051@qq.com
 */

/**
 * @desc geohash实现算法原理
 * Class Geohash
 */
class Geohash
{
    /**
     * @desc base32编码对应的字母
     * @var array
     */
    protected $base_32 = [
        '0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
        'b', 'c', 'd', 'e', 'f', 'g', 'h', 'j', 'k', 'm',
        'n', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x',
        'y', 'z'];

    /**
     * @desc 纬度进度的间隔范围
     * @var array
     */
    public $interval = [
        ['min' => -90, 'max' => 90],
        ['min' => -180, 'max' => 180]
    ];


    /**
     * @desc 利用递归思想 找出经纬度的二进制编码
     * @param float $place 经度或纬度
     * @param string $binary_array 每次递归拿到的bit值
     * @param int $max_separate_num递归总次数
     * @param array $section 区间值
     * @param int $num 递归次数
     * @return array
     */
    public function binary($place = 0, $binary_array = [], $max_recursion_num = 20, $section = [], $num = 1)
    {
        if (!$section) return $binary_array;

        // 获取中间值
        $count = ($section['max'] - $section['min']) / 2;

        // 左半边区间
        $left = [
            'min' => $section['min'],
            'max' => $section['min'] + $count
        ];

        // 右半边区间
        $right = [
            'min' => $section['min'] + $count,
            'max' => $section['max']
        ];

        // 假如给点的经纬度值大于右边最小值 则属于右半边区间为1 否则就是左半边区间 0
        array_push($binary_array, $place > $right['min'] ? 1 : 0);

        // 如果递归次数已经达到最大值 则直接返回结果集
        if ($max_recursion_num <= $num) return $binary_array;


        // 下一次递归我们需要传入的经纬度 区间值
        $section = $place > $right['min'] ? $right : $left;

        // 继续针对自身做的递归处理  一直到出现结果
        return $this->binary($place, $binary_array, $max_recursion_num, $section, $num + 1);
    }


    /**
     * @desc 还原二进制对应的经纬度范围
     * @param int $binary
     * @param int $i
     * @param array $section
     * @return array
     */
    public function reductionBinary($binary = 0, $i = 1, $section = [])
    {
        $count = ($section['max'] - $section['min']) / 2;
        $left = [
            'min' => $section['min'],
            'max' => $section['min'] + $count
        ];
        $right = [
            'min' => $section['min'] + $count,
            'max' => $section['max']
        ];


        $section = $binary[$i - 1] == 0? $left : $right;

        if ($i >= strlen($binary)) return $section;

        return $this->reductionBinary($binary, $i + 1, $section);
    }

    /**
     * @desc 编码组合
     * @param $latitude_str 纬度
     * @param $longitude_str 经度
     * @return string
     */
    public function combination($latitude_str, $longitude_str)
    {
        $result = '';
        //循环经度数字 作为偶数位置
        for ($i = 0; $i < strlen($longitude_str); $i++) {
            // 拼接经度作为偶数位置
            $result .= $longitude_str{$i};
            // 维度存在则拼接字符串
            if (isset($latitude_str{$i})) $result .= $latitude_str{$i};
        }
        return $result;
    }

    /**
     * @desc 对组码之后的二进制进行解码
     * @param string $str
     * @return array
     */
    public static function decodeCombination($str = '')
    {
        $latitude_str = $longitude_str = '';
        //根据两位字符串切割 成多维数组
        $str_arr = str_split($str, 2);
        foreach ($str_arr as $value) {
            // 经度数字
            $longitude_str .= $value[0];

            if (isset($value[1])) $latitude_str .= $value[1];
        }
        return [$latitude_str, $longitude_str];
    }

    /**
     * @desc 将二进制字符串转为十进制 再转换为对应的编码
     * @param $str
     * @return string
     */
    public function encode($str = '')
    {
        $string = '';

        // 按照5位分割字符串
        $array = str_split($str, 5);
        if (!$array) return $string;
        foreach ($array as $va) {
            //二进制转换为十进制
            $decimal = bindec($va);
            $string .= $this->base_32[$decimal];
        }
        return $string;
    }

    /**
     * @desc 将编码转为二进制
     * @param string $str
     * @return string
     */
    public function decode($str = '')
    {
        $string = '';
        //根据一位字符串进行切割 成一个数组
        $str_arr = str_split($str);
        // base32编码反转成新的数组
        $base32 = array_flip($this->base_32);
        // 循环还原二进制数据
        foreach ($str_arr as $val) {
            // 十进制转为二进制
            $string .= str_pad(decbin($base32[$val]), 5, '0', STR_PAD_LEFT);
        }
        return (string)$string;
    }

    /**
     * @desc 根据指定编码长度获取经纬度的 二分层数
     * @param int $length 编码精确度
     * @param int $type 类型 0-纬度；1-经度
     * @return mixed
     */
    public function baseLengthGetNums(int $length = 1, int $type = 0)
    {
        // 数组 下标等于0表示纬度  1表示经度
        $list = [
            1 => [2, 3],
            2 => [5, 5],
            3 => [7, 8],
            4 => [10, 10],
            5 => [12, 13],
            6 => [15, 15],
            7 => [17, 18],
            8 => [20, 20],
            9 => [22, 23],
            10 => [25, 25],
            11 => [27, 28],
            12 => [30, 30],
        ];

        /************************上面一种是写死的 下面这个是通过观察规律自动生成配置文件 开始*********************************/
        $cycleNum = 12;
        $list_res = [];
        $lat = $lng = 0;
        for ($i = 1; $i <= $cycleNum; $i++) {
            // 通过规律计算纬度位数
            $lat = $i % 2 == 0 ? $lat + 3 : $lat + 2;
            $lng = $i % 2 == 0 ? $lng + 2 : $lng + 3;
            $list_res[$i] = [$lat, $lng];
        }
        /***********************结束**********************************/

        return isset($list[$length][$type]) ? $list[$length][$type] : 0;
    }
}