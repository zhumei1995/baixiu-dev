<?php 

require_once 'config.php';
 session_start();

 /*用户登录信息保存*/
 function baixiu_get_current_user(){
 	if(empty($_SESSION['current_login_users'])){
 		header('Location:/admin/login.php');
 	}
 	return $_SESSION['current_login_users'];
 }



/*通过一个数据库查询多条数据*/
function xiu_fetch_all($sql){
$conn=mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
if(!$conn){
	exit('连接失败');
}
$query=mysqli_query($conn,$sql);
	if(!$query){
		return false;
	}
while($row=mysqli_fetch_assoc($query)){
	$result[]=$row;
}
mysqli_free_result($query);
mysqli_close($conn);
return $result;

}






/*查询单条语句*/

function xiu_fetch_one($sql){
	$res=xiu_fetch_all($sql);
	return isset($res[0]) ? $res[0] : null;
}



/*增删语句*/
function xiu_execute($sql){
$conn=mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
	if(!$conn){
		exit('连接失败');
	}
$query=mysqli_query($conn,$sql);
	if(!$query){
		return false;
	}
/*获取受影响行数*/
$_affected_rows=mysqli_affected_rows($conn);

mysqli_close($conn);
return $_affected_rows;


}
?>