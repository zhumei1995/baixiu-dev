<?php

	//上传文件的借口
if(empty($_FILES['file']['error'])){
	//php会接收上传的文件保存在一个临时的文件夹
	$temp_file=$_FILES['file']['tmp_name'];
	//把所需的文件放在指定文件夹
	$targa_file='../static/uploads'.$_FILES['file']['tmp_name'];
	
	if (move_uploaded_file($temp_file, $target_file)) {
		$image_file = '/static/uploads/' . $_FILES['file']['name'];
	}
}
	//设置响应类型为json
header('Location:application/json');
if(empty($image_file)){
		echo json_encode(array(
		'success' => false
}else {
		echo json_encode(array(
		'success' => true,
		'data' => $image_file
		));
	}


