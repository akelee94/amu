<?php
/**
 * Created by 我是阿沐.
 * Date: 2021/05/09
 * Time: 下午13:18
 * QQ: 2511221051@qq.com
 */

/**
 * @desc redis实现附近人
 * Class GeoRedis
 */
class GeoRedis
{
    /**
     * @dec 附近人搜索key
     * @var string
     */
    private $key = 'user:nearby';

    /**
     * @desc redis实例
     * @var null
     */
    public $redis = null;

    /**
     * @desc 构造函数
     * listCase constructor.
     */
    public function __construct($config = [])
    {
        try {

            $this->redis = new Redis();

            $this->redis->connect('127.0.0.1', 6379);

        } catch (Exception $exception) {

            throw new Exception($exception->getMessage());
        }
    }

    /**
     * @desc 添加用户地理位置 可以添加多个 大家可以测试下 修改下方法
     * @param int $user_id
     * @param int $latitude
     * @param int $longitude
     * @return int
     */
    public function insert($user_id = 0, $latitude = 0, $longitude = 0)
    {
        $result = $this->redis->geoAdd($this->key, $longitude, $latitude, $user_id);

        return $result;
    }


    /**
     * @desc 搜索附近人结果集
     * @param int $distance
     * @param int $latitude
     * @param int $longitude
     * @return mixed
     */
    public function searchNearby($distance = 300, $latitude = 0, $longitude = 0)
    {
        $result = $this->redis->georadius(
            $this->key,
            $longitude,
            $latitude,
            $distance,
            'mi',
            ['WITHDIST','count' => 10,'DESC']
        );
        return $result;
    }

    /**
     * @desc 根据储存在位置集合里面的某个地点获取指定范围内的地理位置集合
     * @param int $user_id
     * @param int $distance
     * @return array
     */
    public function georadiusbymember($user_id = 0,$distance = 300)
    {
        $result = $this->redis->georadiusbymember(
            $this->key,
            $user_id,
            $distance,
            'mi',
            ['WITHDIST','count' => 10,'DESC']
        );
        return $result;
    }
}