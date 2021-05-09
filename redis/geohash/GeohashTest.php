<?php


class GeohashTest
{
    const  LATITUDE = 1;
    const LONGITUDE = 2;
    const BASE32 = array(
        '0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
        'b', 'c', 'd', 'e', 'f', 'g', 'h', 'j', 'k', 'm',
        'n', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x',
        'y', 'z');

    /*******************第一批数据*********************/
    public static function Geohash_test($lon, $lat, $precision = 4 ) {
        $lonA = '';
        $s = -180;$t = 180;
        $totalBits = $precision * 5;
        $bits = ceil($totalBits / 2);
        for ($i = 0; $i < $bits; $i++) {
            $mid = ($s + $t) / 2;
            if ($lon >= $mid) {
                $lonA .= 1;
                $s = $mid;
            } else {
                $t = $mid;
                $lonA .= 0;
            }
        }
        $latA = '';
        $s = -90;$t = 90;
        $bits = floor($totalBits / 2);
        for ($i = 0; $i < $bits; $i++) {
            $mid = ($s + $t) / 2;
            if ($lat >= $mid) {
                $latA .= 1;
                $s = $mid;
            } else {
                $t = $mid;
                $latA .= 0;
            }
        }
//        echo $lonA.'-'.$latA;
        $geoBinary = '';
        for ($i = 0; $i < $bits; $i++) {
            $geoBinary .= $lonA[$i] . $latA[$i];
        }
        echo $geoBinary.'-'.$totalBits;
        die;
//        return self::base32Encode($geoBinary, $totalBits);
    }

    public static function decodeGeoHash(string $geohash) {
        $geoBinary = self::base32Decode($geohash);
        $lonS = -180;$lonT = 180;
        $latS = -90;$latT = 90;
        for ($i = 0; $i < strlen($geoBinary); $i += 2) {
            $lonCode = $geoBinary[$i];
            $lonMid = ($lonS + $lonT) / 2;
            if ($lonCode) {
                $lonS = $lonMid;
            } else {
                $lonT = $lonMid;
            }
            $latCode = $geoBinary[$i + 1];
            $latMid = ($latS + $latT) / 2;
            if ($latCode) {
                $latS = $latMid;
            } else {
                $latT = $latMid;
            }
        }
        $geo = [($lonS + $lonT) / 2, ($latS + $latT) / 2];
        return $geo;
    }

    public static function base32Encode(string $geoBinary, $bits)
    {
        $encodeMap = '0123456789bcdefghjkmnpqrstuvwxyz';
        $encode = '';
        for ($i = 0; $i < $bits; $i += 5) {
            $digit = intval(substr($geoBinary, $i, 5), 2);
            $encode .= $encodeMap[$digit];
        }
        return $encode;
    }

    public static function base32Decode(string $geoHash)
    {
        $encodeMap = '0123456789bcdefghjkmnpqrstuvwxyz';
        $decode = '';
        for ($i = 0; $i < strlen($geoHash); $i++) {
            $digit = strpos($encodeMap, $geoHash[$i]);
            $binary = base_convert($digit, 10, 2);
            $decode .= sprintf('%05d', $binary);
        }
        return $decode;
    }

    public function testGeoHash()
    {
        $geohash = self::Geohash_test(123.15488794512, 39.6584212421, 10);//wxp9d7wehc
        $geo = self::decodeGeoHash($geohash);// (123.15488755703, 39.658420979977)
    }


    /*******************第二批数据*********************/
    /**
     * @param int $latitude 纬度
     * @param int $longitude 经度
     * @param int $level
     * @return string
     */
    public static function encode($latitude = 0, $longitude = 0, $level = 11)
    {
        $latitude_str  = self::separate($latitude, '', 1, self::get_precision_level_num($level, self::LATITUDE), self::get_interval_value(self::LATITUDE));
        $longitude_str = self::separate($longitude, '', 1, self::get_precision_level_num($level, self::LONGITUDE), self::get_interval_value(self::LONGITUDE));
        return self::geohash_encode(self::combination($latitude_str, $longitude_str));
    }

    public static function decode($geohash)
    {
        $data = array();
        $combination_str = self::geohash_decode($geohash);
        $separate_str = self::de_combination($combination_str);
        $data[self::LATITUDE] = self::de_separate($separate_str[self::LATITUDE],1,self::get_interval_value(self::LATITUDE));
        $data[self::LONGITUDE] = self::de_separate($separate_str[self::LONGITUDE],1,self::get_interval_value(self::LONGITUDE));

        return $data;
    }


    /**
     * 编码
     */

    /**
     * @param float $num经度或纬度
     * @param string $str递归字符串
     * @param int $i 递归次数
     * @param int $max_separate_num递归总次数
     * @param array $data 区间值
     * @return string
     */
    public static function separate($num, $str = '', $i = 1, $max_separate_num = 20, $data = array('min' => -90, 'max' => 90))
    {
        $count   = ($data['max'] - $data['min']) / 2;
        $limit_0 = array(
            'min' => $data['min'],
            'max' => $data['min'] + $count
        );
        $limit_1 = array(
            'min' => $data['min'] + $count,
            'max' => $data['max']
        );
        $str     .= $num > $limit_1['min'] ? 1 : 0;
        if ($i >= $max_separate_num) {
            return $str;
        } else {
            return self::separate($num, $str, $i + 1, $max_separate_num, $num > $limit_1['min'] ? $limit_1 : $limit_0);
        }
    }

    /**
     * @param $latitude_str 纬度
     * @param $longitude_str 经度
     */
    public static function combination($latitude_str, $longitude_str)
    {
        $str = '';
        for ($i = 0; $i < strlen($longitude_str); $i++) {//根据精度表，可发现维度>=精度
            $str .= $longitude_str{$i};
            if(isset($latitude_str{$i})){
                $str .=  $latitude_str{$i};
            }
        }
        return $str;
    }

    public static function geohash_encode($str)
    {
        $str_arr    = str_split($str, 5);//按5位分割字符串
        $encode_str = '';
        foreach ($str_arr as $va) {
            $decimal    = bindec($va);
            $encode_str .= self::BASE32[$decimal];
        }
        return $encode_str;
    }
    /**
     * 编码
     */

    /**
     * 解码
     */
    public static  function geohash_decode($str)
    {
        //根据一位字符串进行切割
        $str_arr    = str_split($str, 1);
        $decode_str = '';
        $base32     = array_flip(self::BASE32);
        foreach ($str_arr as $va) {
            $decode_str .= str_pad(decbin($base32[$va]),5,'0',STR_PAD_LEFT);
        }
        return (string)$decode_str;

    }

    /**
     * 解码二进制组合
     * @param $str
     * @return array
     */
    public static function de_combination($str)
    {
        $latitude_str  = '';
        $longitude_str = '';
        //根据两位字符串切割
        $str_arr = str_split($str, 2);
        foreach ($str_arr as $va) {
            $longitude_str .= $va[0];
            if(isset($va[1])){//根据精度表，可发现维度>=精度
                $latitude_str  .= $va[1];
            }
        }
        return array(
            self::LATITUDE=>$latitude_str,
            self::LONGITUDE=>$longitude_str,
        );
    }

    /**
     * 解码二分区间
     * @param $str
     * @param string $i//执行次数
     * @param array $data、、区间
     */
    public static function de_separate($str,$i=1,$data = array('min' => -90, 'max' => 90)){
        $count   = ($data['max'] - $data['min']) / 2;
        $limit_0 = array(
            'min' => $data['min'],
            'max' => $data['min'] + $count
        );
        $limit_1 = array(
            'min' => $data['min'] + $count,
            'max' => $data['max']
        );
        if($str[$i-1]==0){
            $data = $limit_0;
        }else{
            $data = $limit_1;
        }

        if ($i >= strlen($str)) {
            return $data;
        } else {
            return self::de_separate($str, $i + 1, $data);
        }
    }

    /**
     * 解码
     */

    /**
     * 根据精度获取二分次数
     * @param $level
     * @param $type
     */
    public static function get_precision_level_num($level, $type = self::LATITUDE)
    {
        $precision = array(
            1  => array(
                self::LATITUDE  => 2,
                self::LONGITUDE => 3,
            ),
            2  => array(
                self::LATITUDE  => 5,
                self::LONGITUDE => 5,
            ),
            3  => array(
                self::LATITUDE  => 7,
                self::LONGITUDE => 8,
            ),
            4  => array(
                self::LATITUDE  => 10,
                self::LONGITUDE => 10,
            ),
            5  => array(
                self::LATITUDE  => 12,
                self::LONGITUDE => 13,
            ),
            6  => array(
                self::LATITUDE  => 15,
                self::LONGITUDE => 15,
            ),
            7  => array(
                self::LATITUDE  => 17,
                self::LONGITUDE => 18,
            ),
            8  => array(
                self::LATITUDE  => 20,
                self::LONGITUDE => 20,
            ),
            9  => array(
                self::LATITUDE  => 22,
                self::LONGITUDE => 23,
            ),
            10 => array(
                self::LATITUDE  => 25,
                self::LONGITUDE => 25,
            ),
            11 => array(
                self::LATITUDE  => 27,
                self::LONGITUDE => 28,
            ),
            12 => array(
                self::LATITUDE  => 30,
                self::LONGITUDE => 30,
            ),
        );
        return $precision[$level][$type];

    }

    /**
     * 获取区间
     * @param $type
     * @return mixed
     */
    public static function get_interval_value($type = self::LATITUDE)
    {
        $interval = array(
            self::LATITUDE  => array(
                'min' => -90,
                'max' => 90
            ),
            self::LONGITUDE => array(
                'min' => -180,
                'max' => 180
            ),
        );
        return $interval[$type];
    }

}