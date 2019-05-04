<!-- 载入配置文件 -->
<?php 
/*载入配置数据库的配置文件*/
require_once('../config.php');

/*配置一个箱子*/
  session_start();

function login(){

/*接收并校验*/

  if(empty($_POST['email'])){
  $GLOBALS['message']='请输入邮箱';
  return;
  }
  if(empty($_POST['password'])){
  $GLOBALS['message']='请输入密码';
  return;
  }

/*接收数据*/

  $email=$_POST['email'];
  $password=$_POST['password'];
  /*校验数据在数据库中*/
  $conn=mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
  /*判断连接是否成功*/
  if(!$conn){
  exit('<h1>请链接数据库</h1>');
  }
  /*查询数据*/
  $query=mysqli_query($conn,"select * from users where email='{$email}' limit 1;");

  /*判断是否正确*/
  if(!$query){
  $GLOBALS['message']='登录失败，请重试！';
  return;
  }

/*获取用户登录信息*/

  $users=mysqli_fetch_assoc($query);
  /*判断用户名是否正确*/
  if(!$users){
    $GLOBALS['message']='邮箱与密码不匹配';
    return;
  }
  if($users['password']!==$password){
    $GLOBALS['message']='邮箱与密码不匹配';
    return;
  }


  /*保存用户登录状态*/
  $_SESSION['current_login_users']=$users;
  /*登录成功 跳转*/
  header('Location: /admin/index.php');
  }


  /*判断请求方式*/
  if($_SERVER['REQUEST_METHOD']==='POST'){
    login();
  }


  /*退出登录页信息*/
  if($_SERVER['REQUEST_METHOD']==='GET'&& isset($_GET['action'])&&$_GET['action']==='logout'){
    //删除session信息
    unset($_SESSION['current_login_users']);
    
  }
?>









<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Sign in &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
</head>
<body>
  <div class="login">
    <!-- 为form表单添加相关信息action method autocomplete关闭客户端自动完成功能 novaladate是关闭表单自动检验功能 -->
    <form class="login-wrap" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" autocomplete="off" novalidate>
      <img class="avatar" src="/static/assets/img/default.png">
      <!-- 有错误信息时展示 -->
      <?php if (isset($message)): ?>
      <div class="alert alert-danger">
        <strong>错误！</strong> <?php echo $message; ?>
      </div>
      <?php endif ?>
      <div class="form-group">
        <label for="email" class="sr-only">邮箱</label>
        <input id="email" name="email" type="email" class="form-control" placeholder="邮箱" autofocus action="<?php echo $_SERVER['PHP_SELF']; ?>"  value="<?php echo empty($_POST['email']) ? '' : $_POST['email']; ?>">
      </div>
      <div class="form-group">
        <label for="password" class="sr-only">密码</label>
        <input id="password" name="password" type="password" class="form-control" placeholder="密码">
      </div>
      <button class="btn btn-primary btn-block" href="index.php">登 录</button>
    </form>
  </div>
  <script src="/static/assets/vendors/jquery/jquery-1.12.1.js"></script>

  <script >
    /*客户端通过邮件名直接获取服务端数据，无法直接获取，只能通过js操作ajax拿到服务端数据*/
    $(function($){
      /*目标：当用户输入邮箱后，获取相应的头像
      时机：当输入框失去光标后，并且能拿到输入的邮箱
      事件：获取邮箱对应的头像地址，并把它输入到img文本框中
      */
      var emailFormat=/^[a-zA-Z0-9]+[@][a-zA-Z0-9]+\.[a-zA-Z0-9]+$/
      $('#email').on('blur',function(){
        /*获取value值，value值就是email值*/
        var value=$(this).val();
        /*判断vaule*/
        if(!value || !emailFormat.test(value))return;
        /*客户端获取正确的邮箱和头像地址，但是无法从服务端拿到数据，所以通过ajax借口获取数据*/
           $.get('/admin/api/avatar.php', { email: value }, function (res) {
          // 希望 res => 这个邮箱对应的头像地址
          if (!res) return
          // 展示到上面的 img 元素上
          // $('.avatar').fadeOut().attr('src', res).fadeIn()
          $('.avatar').fadeOut(function () {
            // 等到 淡出完成
            $(this).on('load', function () {
              // 图片完全加载成功过后
              $(this).fadeIn()
            }).attr('src', res)         
           })
        })
    })
 })
  </script>
</body>
</html>
