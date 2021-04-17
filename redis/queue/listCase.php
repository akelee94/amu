<?php
/**
 * Created by 我是阿沐.
 * Date: 2021/04/17
 * Time: 上午11:58
 * QQ: 2511221051@qq.com
 */

/***************可命名空间*********************/

/**
 * @desc Redis 列表使用场景案例
 * Class listCase
 */
class listCase
{
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
     * @desc 延迟队列的名称
     * @var string
     */
    protected $write_delay_queue = 'write:delay:queue:%u';

    /**
     * @desc 定义延迟类型事件 对应的延迟时长
     * @var array
     */
    protected $delay_queues = [
        'amumu' => 10,
        'amumu1' => 15,
        'amumu2' => 60,
    ];

    /**
     * @desc 最大运行时间
     * @var int
     */
    protected $max_runtime = 30;

    /**
     * @desc 构造函数
     * listCase constructor.
     */
    public function __construct($config = [])
    {
        $this->host = isset($config['host']) ? $config['host'] : $this->host;

        $this->port = isset($config['port']) ? $config['port'] : $this->port;

        $this->password = isset($config['password']) ? $config['password'] : $this->password;

        try {

            $this->redis = new Redis();

            $this->redis->connect($this->host, $this->port);

            $this->redis->auth($this->password);

        } catch (Exception $exception) {

            throw new Exception($exception->getMessage());
        }
    }

    /**
     * @desc 列表案例一  热点新闻 固定列表长度 维护列表长度
     * @param array $data 需要加入队列的数据
     * @return bool|int
     */
    public function hotNewsRank($data = array())
    {
        if (!$data || !is_array($data)) return false;

        $data = json_encode($data);

        $key = sprintf('every:month:hot:news:%s', date('%Y%m'));

        // 每月热点新闻只展示前10条
        $length = $this->redis->lLen($key);

        if ($length >= 10) return false;

        $result = $this->redis->lPush($key, $data);

        return $result;
    }

    /**
     * @desc 抽奖排行榜 每次最新的排在最前面 只轮播前五名
     * @param array $data
     * @return bool|int
     */
    public function drawRank($data = array())
    {
        if (!$data || !is_array($data)) return false;

        $data = json_encode($data);

        $key = sprintf('every:day:draw:rank:%s', date('%Y%m%d'));

        $result = $this->redis->lPush($key, $data);

        $this->redis->lTrim($key, 0, 20); //这里截图存储20个为了防止数据不够 增加容错

        return $result;
    }

    /**
     * @desc 队列生产者
     * @param string $qname
     * @param array $data
     * @return bool
     */
    public function push($qname = '', $data = [])
    {
        if (!$qname || !$data) return false;

        if (is_array($data)) $data = json_encode($data);

        // 进入队列
        $result = $this->redis->rPush($qname, $data);

        if ($result) return true;

        // 此处可以增加一条失败队列 处理 或者记录日志 方便后续处理 或者增加告警

        return false;
    }

    /**
     * @desc 延迟队列处理
     * @param string $event
     * @param array $data
     * @return bool
     */
    public function pushDelay($event = '', $data = [])
    {
        if (!$event || !$data || !is_array($data)) return false;

        $delay = $this->delay_queues[$event] ?: 5;

        $data['delay'] = time() + $delay;

        return $this->push(sprintf($this->write_delay_queue, $delay), json_encode($data));
    }

    /**
     * @desc 消息队列实现
     * @param string $qname
     * @param null $handler
     * @param int $max_runtime
     * @return bool
     */
    public function handle($qname = '', $handler = [], $max_runtime = 0)
    {
        if (!$qname || !$handler) return false;

        // 开始运行时间
        $start_time = time();

        // 最大运行时间
        $max_runtime = $max_runtime ? $max_runtime : $this->max_runtime;

        //每次执行脚本处理了多少条数据
        $run_count = 0;

        while (true) {
            $info = $this->redis->lPop($qname);

            if (!$info) break;

            $data = json_decode($info, true);

            if (!$data) break;

            $run_count = $run_count + 1;

            $retval = call_user_func_array($handler, [$data, $qname]);

            if (!$retval) break;

            if (time() - $start_time > $max_runtime) break;
        }

        if ($run_count > 0) {
            // todo 记录日志
        }
        // 输出日志查看下
        echo $run_count;

        return true;
    }

    /**
     * @desc 延迟消费队列
     * @param string $event
     * @param array $handler
     * @param int $max_runtime
     * @return bool
     */
    public function handleDelay($event = '', $handler = [], $max_runtime = 0)
    {
        if (!$event || !$handler) return false;

        // 开始运行时间
        $start_time = time();

        // 最大运行时间
        $max_runtime = $max_runtime ? $max_runtime : $this->max_runtime;

        //每次执行脚本处理了多少条数据
        $run_count = 0;

        // 获取延迟时间
        $delay = $this->delay_queues[$event] ?: 5;

        // 队列名称
        $qname = sprintf($this->write_delay_queue, $delay);

        while (true) {
            $info = $this->redis->lPop($qname);

            if (!$info) break;

            $data = json_decode($info, true);

            if (!$data) break;

            $delay_time = isset($data['delay']) ? $data['delay'] : 0;
            echo $delay_time.'-';
            if ($delay_time <= 0) break;

            if ($delay_time > time()) {
                $res = $this->redis->lPush($qname, $info);
                if ($res) {
                    $length = $this->redis->lLen($qname);
                    if ($length > 10000) { // 队列积压严重
                        // todo 短信告警开发人员  处理
                        echo "队列已堆积";
                    } else {
                        // todo 失败落入日志文件 人为介入 或者定时检测日志文件数据 重新消费
                    }
                }
                break;
            }

            // 处理逻辑

            $run_count = $run_count + 1;

            $retval = call_user_func_array($handler, [$data, $qname]);

            if (!$retval) break;

            if (time() - $start_time > $max_runtime) break;
        }

        return true;
    }
}