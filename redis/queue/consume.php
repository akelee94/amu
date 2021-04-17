<?php
/**
 * Created by 我是阿沐.
 * Date: 2021/04/17
 * Time: 上午11:58
 * QQ: 2511221051@qq.com
 */

/**
 * @desc 模拟一个消费类数据
 * Class consume
 */
class consume
{
    /**
     * @Desc 队列接受者 打印接收到的数据
     * @return bool
     */
    public function receive($info, $qname)
    {
        echo $qname;
        return true;
    }

    /**
     * @Desc  延迟队列消费数据展示
     * @param $info
     * @param $qname
     * @return bool
     */
    public function delay($info, $qname)
    {
        echo $qname;
        echo "<br/>";
        return true;
    }
}