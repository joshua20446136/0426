<?php
require '../common.inc.php';
$table = 'libin_sell_23';


/* 

http://www.385538.com/appgpei/zhuanma1.php?username=13281635960&adddate=快递日期&v2=快递柜id&brand=快递品牌&status=3
备注 v2是快递柜id  n2是签收类型

20250429
接口请求日期 不按照请求的日期显示请求的内容，数据库也是这样的格式http://www.385538.com/appgpei/zhuanma1.php?username=13281635960&adddate=2025-04-27

 */
 
$post['username'] = $_GET['username'];
$post['title'] = $_GET['title'];
$post['brand'] = $_GET['brand'];
$post['v2'] = $_GET['v2'];
$post['status'] = $_GET['status'];
$post['addtime'] = $_GET['addtime'];
$adddate = $_GET['adddate'];

$page = $_GET['page'];        

if($page > 0 ){
	$page = $page-1;
}else{
	$page = 0;
}

$data['page']['count'] = 20; //每页多少条数据



$where = '1=1';
if($post['username']<>''){
	$where.=' and username like "%'. $post['username'].'%"';	
}
if($post['title']<>''){
	$where.=' and title like "%'. $post['title'].'%"';	
}
if($post['brand']<>''){
	$where.=' and brand like "%'. $post['brand'].'%"';	
}
if($post['v2']<>''){
	$where.=' and v2 like "%'. $post['v2'].'%"';	
}
if($post['status']<>''){
	$where.=' and status like "%'. $post['status'].'%"';	
}
if($post['adddate']<>''){
	$where.=' and adddate like "%'. $post['adddate'].'%"';	
}

if($adddate <> '' and strlen($adddate) == 10 ){
	$where.=" and DATE_FORMAT( FROM_UNIXTIME(addtime),'%Y-%m-%d') = '{$adddate}'";
}
$r = $db->get_one("SELECT COUNT(*) AS num  FROM {$table} WHERE " . $where);
$items = $r['num'];
$data['page']['page'] = $page+1;
$data['page']['total'] = $items;
$data['page']['pageCount'] = ceil($data['page']['total']/$data['page']['count']);
if($data['page']['page']>$data['page']['pageCount']){
	$page=$data['page']['pageCount']-1;
}
if($page<0){
	$page = 0;
}

$limit = " LIMIT ".$page*$data['page']['count'] .','.$data['page']['count'];
$db->query("set names utf8");
$orderby = " order by addtime desc ";

$result = $db->query("SELECT username,status,n2,title,brand,adddate,addtime FROM {$table} WHERE " . $where . $orderby . $limit);
$arr = array();
while($row = $db->fetch_array($result)){
	$arr[] = $row;	
}
$json = array('data' => $arr, 'state' => 1);
//收支合计
$result01 = $db->get_one("SELECT 
sum(case when amount >= 0 THEN amount end) as 'income',
sum(case when amount < 0 THEN amount end) as 'expenses' 
from libin_finance_record WHERE " . $where);

if ($data['page']) {
	$newPage['total'] = $data['page']['total'];  //总条数
	$newPage['count'] = $data['page']['count'];  //返回记录的条数
	$newPage['pageCount'] = $data['page']['pageCount']; //总页数
	$newPage['page'] = $data['page']['page'];  //当前页
	//如果下一页大于当前页
	if ($data['page']['pageCount'] > $data['page']['page']) {
		$newPage['hasMore'] = 1;
	} else {
		$newPage['hasMore'] = 0;
	}
	$json['pageInfo'] = $newPage;
}
$json['income'] = $result01['income'];
$json['expenses'] = $result01['expenses'];
exit(json_encode($json));




