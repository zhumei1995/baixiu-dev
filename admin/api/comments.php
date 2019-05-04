<?php
/*接收服务端的ajax请求 返回评论数据*/
//载入封装的所有的函数
require '../../functions.php';

$page=empty($_GET['page']) ? 1 : intval($_GET['page']);

$length=20;
$offset=($page-1)*$length;


$sql=sprintf('select
 comments.*, 
 posts.title as post_title
from comments
inner join posts on comments.post_id = posts.id
order by comments.created desc
limit %d, %d', $offset, $length);

$total_count=xiu_fetch_one('select count(1) as count
	from comments
	inner join posts on comments.post_id = posts.id')['count'];

$total_pages=ceil($total_count/$length);

//返回类型是float，但是数字一定是整数

//查询所有数据
$comments=xiu_fetch_all($sql);


//将数据转换成字符串
$json=json_encode(array(
	'total_pages'=>$total_pages,
	'comments'=>$comments
));

//设置响应的响应体类型为json
header('Content-Type:application/json');

echo $json;

