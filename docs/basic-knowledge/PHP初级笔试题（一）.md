![【面试之道】PHP初级笔试题（一）](http://img.phpassn.com/upload/images/20191216/4f/47195a7f53e1317af4a89e60e3bac1.jpg)

> 学而不思则罔，思而不学则殆
学习是一种进步，看书阅读是一种习惯
本文章整理一线二线公司面试题，已放在 **GitHub** [github.com/phpInterview](https://github.com/82nlxj/phpInterview) 上，初级中级高级PHP开发工程师都可以参考阅读复习，欢迎Star和指教。

### 前言

PHP开发工程师面试已经越来越难了，从最开始在郑州发展☞上海（魔都）☞广州（过冬）这些年的漂泊，尝试过一天内找到工作，也尝试过一个月内找到工作；也遇见过一个人面试一个PHP岗位，也遇见过40多人同时面试一个PHP岗位；也经历过多次面试失败，也经历过一次面试成功；最后也很迅速的进入到一线大厂，也带了不少小弟新人，才发现面试变得这么难。事实上，是越来越多的PHP开发者基础太过薄弱，要么是直接培训机构出来的，要么就是小白自学转行过来的，太多面试碰壁的，也有太多的无奈。

大大小小的博客做了很多，并没有一个能坚持超过五年的。所以这次想认认真真的做下去，分享是一种热爱，学习是一种被逼无奈（说多了，是一种兴趣_φ(❐_❐✧ **人丑就要多读书**）

本文章并不能完全的让你面试成功，但是一定能让你在面试官面前避免尴尬。

### 步入主题 面试之道

> 1、抓取远程图片到本地,你会用什么函数?

- 第一种方案：
```php
<?php
//需要抓取的远程图片链接
$image_url = 'http://img.phpassn.com/upload/images/20191213/19/3a716eccda6f4a1d4b30e98dcdc36f.jpg';

$image_url = file_get_contents($image_url); //把整个文件读入一个字符串中 快速链接 [file_get_contents导向](https://www.php.net/manual/zh/function.file-get-contents.php)

$result = file_put_contents('phpassn.jpg',$image_url); //将一个字符串写入文件 快速链接 [file_put_contents导向](https://www.php.net/manual/zh/function.file-put-contents.php)

var_dump($result); // int(15094)
```

- 第二种方案：
```php
<?php
function downImage($image_url = '', $filename = '')
{
    if (!$image_url) return false;
    if (!$filename) {
        $ext_name = strrchr($image_url, '.'); //获取图片的扩展名
        if (!in_array($ext_name, ['.gif', '.jpg', '.bmp', '.png'])) return false;
        $filename = time() . $ext_name;
    }
    // 开始捕获
    ob_start();
    //readfile() 函数输出一个文件 写入到输出缓冲
    readfile($image_url);
    //得到缓冲区的数据
    $img_date = ob_get_contents();
    //清除输出缓冲 关闭缓存区
    ob_end_clean();
    //获取文件大小
    $size = strlen($img_date);
    $local_file = fopen($filename, 'a'); //写入方式打开
    fwrite($local_file, $img_date);
    fclose($local_file);
    return $filename;
}

//需要抓取的远程图片链接
$image_url = 'http://img.phpassn.com/upload/images/20191213/19/3a716eccda6f4a1d4b30e98dcdc36f.jpg';
echo downImage($image_url);
```
- 第三种使用PHP的GD库
```php
<?php
//需要抓取的远程图片链接
$image_url = 'http://img.phpassn.com/upload/images/20191213/19/3a716eccda6f4a1d4b30e98dcdc36f.jpg';
$src_im = imagecreatefromjpeg($image_url);
$srcW = ImageSX($src_im); //获得图像的宽
$srcH = ImageSY($src_im); //获得图像的高
$dst_im = ImageCreateTrueColor($srcW, $srcH); //创建新的图像对象
imagecopy($dst_im, $src_im, 0, 0, 0, 0, $srcW, $srcH);
imagejpeg($dst_im, "phpassn.jpg"); //创建缩略图文件
```
 
> 2、用PHP打印出前一天的时间，打印格式是2019年12月12日12:12:12

```php
<?php
echo date('Y-m-d H:i:s',strtotime('-1 day'));
```

> 3、假设a.html和b.html在同一个文件夹下面，用javascript实现当打开a.html五秒钟后，自动跳转到b.html

```php
<script>
function go2b(){
         window.location = “b.html”;
         window.close();
}
setTimeout( “go2b()”,5000 ); //5秒钟后自动执行go2b()
</script>
```

> 4、在HTTP 1.0中，状态码 401 的含义是未授权____；如果返回“找不到文件”的提示，则可用 header 函数，其语句为_____
     
401表示未授权;header(“HTTP/1.0404 Not Found”);
 
> 5、把 John 新增到 users 阵列？

```php
<?php
$users[] = 'john';   
array_push($users,'john');
```

> 6、在PHP中error_reporting这个函数有什么作用？

error_reporting() 设置 PHP 的**报错级别并返回当前级别**。
 
7、如何修改SESSION的生存时间(1分钟)

```php
<?php
**方法1**:将php.ini中的session.gc_maxlifetime设置为9999重启apache
**方法2**:$savePath = "./session_save_dir/";
$lifeTime = 小时 * 秒;
session_save_path($savePath);
session_set_cookie_params($lifeTime);
session_start();
**方法3**:
setcookie() 
session_set_cookie_params($lifeTime);
````

> 7、有一个网页地址, 比如PHP开发资源网主页:http://www.baidu.com/index.html,如何得到它的内容?

```php
<?php
**方法1**:
$readcontents = fopen("http://www.baidu.com/index.html", "rb");
$contents = stream_get_contents($readcontents);
fclose($readcontents);
echo $contents;
**方法2**:
echo file_get_contents("http://www.baidu.com/index.html");
```
 
> 8、写一个函数，尽可能高效的，从一个标准 url 里取出文件的扩展名
例如:http://www.sina.com.cn/abc/de/fg.php?id=1 需要取出 php 或 .php

```php
<?php
//答案1:
function getExt($url)
{
    $arr = parse_url($url);
    $file = basename($arr['path']);
    $ext = explode(".", $file);
    return $ext[1];
}

//答案2:
function getExt1($url)
{
    $url = basename($url);
    $pos1 = strpos($url, " . ");
    $pos2 = strpos($url, " ? ");
    if (strstr($url, " ? ")) {
        return substr($url, $pos1 + 1, $pos2 - $pos1 - 1);
    }
    return substr($url, $pos1);
}

```

> 9、使用五种以上方式获取一个文件的扩展名要求：dir/upload.image.jpg，找出 .jpg 或者 jpg ，
必须使用PHP自带的处理函数进行处理，方法不能明显重复，可以封装成函数 get_ext1($file_name), get_ext2($file_name)

```php
function get_ext1($file_name)
{
    return strrchr($file_name, '.');
}

function get_ext2($file_name)
{
    return substr($file_name, strrpos($file_name, '.'));
}

function get_ext3($file_name)
{
    return array_pop(explode('.', $file_name));
}

function get_ext4($file_name)
{
    $p = pathinfo($file_name);
    return $p['extension'];
}

function get_ext5($file_name)
{
    return strrev(substr(strrev($file_name), 0, strpos(strrev($file_name), ‘ . ’)));
}

```
 
> 10、MySQL数据库中的字段类型varchar和char的主要区别是什么？那种字段的查找效率要高，为什么?

**Varchar是变长**，节省存储空间，**char是固定长度**。查找效率要varchar型快，因为varchar是非定长，必须先查找长度，然后进行数据的提取，比char定长类型多了一个步骤，所以效率低一些
 
> 11、请使用JavaScript写出三种产生一个Image 标签的方法（提示：从方法、对象、HTML角度考虑）

```javascript
(1)var img = new Image();
(2)var img = document.createElement("image")
(3)img.innerHTML = "<img src="xxx.jpg" />"
```
 
> 12、请描述出两点以上XHTML和HTML最显著的区别

(1)XHTML必须强制指定文档类型DocType，HTML不需要
(2)XHTML所有标签必须闭合，HTML比较随意
 
> 13、写出三种以上MySQL数据库存储引擎的名称（提示：不区分大小写）

MyISAM、InnoDB、BDB（Berkeley DB）、Merge、Memory（Heap）、Example、Federated、Archive、CSV、Blackhole、MaxDB 等等十几个引擎
 
> 14、求两个日期的差数，例如2007-2-5 ~ 2007-3-6 的日期差数

```php
function diffTime($date1, $date2)
{
    $time1 = strtotime($date1);
    $time2 = strtotime($date2);
    return ($time2 - $time1) / 86400;
}

echo diffTime('2007-02-05', '2007-03-06');
echo "<br>";
//方法二：
$temp = explode('-', '2007-02-05');
$time1 = mktime(0, 0, 0, $temp[1], $temp[2], $temp[0]);
$temp = explode('-', '2007-03-06');
$time2 = mktime(0, 0, 0, $temp[1], $temp[2], $temp[0]);
echo ($time2 - $time1) / 86400;
echo "<br>";
//方法三：
echo abs(strtotime("2007-02-01") - strtotime("2007-03-01")) / 60 / 60 / 24; //计算时间差
```
 
> 15、请写一个函数，实现以下功能：字符串"open_door"转换成"OpenDoor"、"make_by_id"转换成"MakeById"

```php
<?php
//方法1：
$string1 = "open_door";
$string2 = "make_by_id";
function strExplode($str)
{
    $str_arr = explode("_", $str);
    $str_implode = implode(" ", $str_arr);
    $str_implode = implode("", explode(" ", ucwords($str_implode)));
    return $str_implode;
}

echo strExplode($string1);
echo "<br>";
echo strExplode($string2);
echo "<br>";
//方法二：
$expStr = explode("_", $string1);
for ($i = 0; $i < count($expStr); $i++) {
    echo ucwords($expStr[$i]);
}
echo "<br>";
//方法三：
echo str_replace(' ', '', ucwords(str_replace('_', ' ', $string2)));
```
 
> 16、js中网页前进和后退的代码

```javascript
前进: history.forward();=history.go(1);
后退: history.back();=history.go(-1);
```
 
> 17、echo count("abc"); 输出什么？


count —**计算数组中的单元数目或对象中的属性个数**
int count ( mixed$var[, int $mode ] ), 如果 var 不是数组类型或者实现了 Countable 接口的对象，将返回1，有一个例外，如果 var 是 NULL 则结果是 0。
对于对象，如果安装了 SPL，可以通过实现 Countable 接口来调用 count()。该接口只有一个方法 count()，此方法返回 count() 函数的返回值。
 
> 18、有一个一维数组，里面存储整形数据，请写一个函数，将他们按从大到小的顺序排列。要求执行效率高。并说明如何改善执行效率。（该函数必须自己实现，不能使用php函数）

```php
<?php
$array = [1, 3, 6, 8, 2, 7, 0, 56, 4,45];
function BubbleSort(&$arr)
{
    $count = count($arr);
    for ($i = 0; $i < $count; $i++) {
        for ($j = 0; $j < $count - $i - 1; $j++) {
            if ($arr[$j] > $arr[$j + 1]) {
                $tmp = $arr[$j];
                $arr[$j] = $arr[$j + 1];
                $arr[$j + 1] = $tmp;
            }
        }
    }
}
BubbleSort($array);
var_dump($array);
```

> 19、请举例说明在你的开发过程中用什么方法来加快页面的加载速度

要用到服务器资源时才打开，及时关闭服务器资源，数据库添加索引，页面可生成静态，图片等大文件单独服务器。
 
> 20、以下的代码会产生什么？为什么？

```php
$num =10;
function multiply(){
   $num =$num *10;
}
multiply();
echo $num;
```
由于函式 multiply() 没有指定 $num 为全域变量（例如 global $num 或者 $_GLOBALS['num']），所以 $num 的值是 10。
 
> 21、HTTP协议中GET、POST和HEAD的区别？

HEAD：只请求页面的首部。
GET：请求指定的页面信息，并返回实体主体。
POST：请求服务器接受所指定的文档作为对所标识的URI的新的从属实体。
（1）HTTP 定义了与服务器交互的不同方法，最基本的方法是 GET 和 POST。事实上 GET 适用于多数请求，而保留 POST 仅用于更新站点。
（2）在FORM提交的时候，如果不指定Method，则默认为GET请求，Form中提交的数据将会附加在url之后，以?分开与url分开。字母数字字符原样发送，但空格转换为“+“号，其它符号转换为%XX,其中XX为该符号以16进制表示的ASCII（或ISO Latin-1）值。GET请求请提交的数据放置在HTTP请求协议头中，而POST提交的数据则放在实体数据中；
GET方式提交的数据最多只能有1024字节，而POST则没有此限制。
GET这个是浏览器用语向服务器请求最常用的方法。POST这个方法也是用来传送数据的，但是与GET不同的是，使用POST的时候，数据不是附在URI后面传递的，而是要做为独立的行来传递，此时还必须要发送一个Content_length标题，以标明数据长度，随后一个空白行，然后就是实际传送的数据。网页的表单通常是用POST来传送的。

（完结篇一）