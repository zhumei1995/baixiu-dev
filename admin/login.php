<!-- 载入配置文件 -->
<?php 
/*载入配置数据库的配置文件*/
  require_once('../config.php');
  session_start();

  //校验方式
  function login(){
    //邮箱验证
    if(empty($_POST['email'])){
      $GLOBALS['message']='请输入邮箱！';
      return;
    }

    if(empty($_POST['password'])){
      $GLOBALS['message']='请输入密码！';
      return;
    }

    //接收数据
    $email=$_POST['email'];
    $password=$_POST['password'];

    //在数据库中校验数据
    //连接数据库
    $conn=mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);

    //判断连接是否成功
    if(!$conn){
      exit('<h1>请连接数据库</h1>');
    }

    //查询数据
    $query=mysqli_query($conn,"select * from users where email='{$email}' limit 1;");

    //判断查询
    if(!$query){
      $GLOBALS['message']='查询失败，请重试';
      return;
    }

    //获取登录信息并判断
    $users=mysqli_fetch_assoc($query);

    if(!$users){
      $GLOBALS['message']='密码与邮箱不匹配';
      return;
    }
    if($users['password']!==$password){
      $GLOBALS['message'] ='密码与邮箱不匹配';
      return;
    }

    $_SESSION['current_login_users']=$users;
    header('Location: /admin/index.php');

  }

  //判断请求方式
  if($_SERVER['REQUEST_METHOD']==='POST'){
    login();
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
        <input id="email" name="email" type="email" class="form-control" placeholder="邮箱" autofocus  value="<?php echo empty($_POST['email']) ? '' : $_POST['email']; ?>">
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
    $(function(){
      var emailFormat=/^[a-zA-Z0-9]+[@][a-zA-Z0-9]+\.[a-zA-Z0-9]+$/
      $('#email').on('blur',function(){
        var value=$(this).val();
        if(!value || !emailFormat.test(value)) return;
        //通过ajax拿服务端数据
        $.get('/admin/api/avatar.php',{email:value},function(res){
          if(!res) return;
          $('.avatar').fadeOut(function(){
            $(this).on('load',function(){
              $(this).fadeIn()
            }).attr('src',res)
          })
        })
      })
    })
  </script>

</body>
</html>
