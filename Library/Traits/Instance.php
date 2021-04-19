<?php
/**
 * Created by 我是阿沐.
 * Date: 2021/04/18
 * Time: 下午16:51
 * QQ: 2511221051@qq.com
 */

namespace Library\Traits;

/**
 * @desc 单例
 * Trait Instance
 * @package Library\Traits
 */
trait Instance
{
    /**
     * @des 实例对象
     * @var null
     */
    protected static $instance = null;

    /**
     * @param array $config
     * @return static
     */
    public static function instance($config = [])
    {
        if (is_null(self::$instance)) {
            self::$instance = new static($config);
        }
        return self::$instance;
    }

    /**
     * @desc  静态调用
     * @param $method
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public static function __callStatic($method, $params)
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        $call = substr($method, 1);

        if (0 === strpos($method, '_') && is_callable([self::$instance, $call])) {

            return call_user_func_array([self::$instance, $call], $params);
        } else {

            throw new \Exception("method not exists:" . $method);
        }
    }
}