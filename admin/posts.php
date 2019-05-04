
<?php

require_once '../functions.php';
baixiu_get_current_user();

//分页数据
$page=empty($_GET['page']) ? 1 :(int)$_GET['page'];
$size=10;
$offset=($page-1)*$size;

if($page<=0){
  header('Location:admin/posts.php?page=1');
  exit;
}

//查询总页数
$total_count=intval(xiu_fetch_one("select count(1) as count from posts
inner join categories on posts.category_id = categories.id
inner join users on posts.user_id = users.id;")['count']);

$total_pages=ceil($total_count/$size);

/*处理分页页码*/
//可展示的数据
$visiables=5;
//最小页与最大页
$begin=$page-($visiables-1)/2;
$end=$begin+$visiables-1;

//考虑合理性问题
$begin=$begin<1 ? 1 : $begin;
$end=$begin+$visiables-1;
$end=$end>$total_pages?$total_pages:$end;
$begin=$end-$visiables+1;
$begin=$begin<1 ? 1 : $begin;



if($page>$total_count){
  header('Location:/admin/posts.php?='.$total_pages);
  exit;
}

//查询所有数据，渲染到页面上
$posts=xiu_fetch_all("select
  posts.id,
  posts.title,
  users.nickname as user_name,
  categories.name as category_name,
  posts.created,
  posts.status
from posts
inner join categories on posts.category_id = categories.id
inner join users on posts.user_id = users.id
order by posts.created asc
limit $offset,$size;");

//数据状态转换
function convert_status($status){
  $dict=array(
    'published' =>'已发布' ,
    'drafted' =>'草稿' ,
    'trashed' =>'已发布' ,
   );
  return isset($dict[$status]) ? $dict[$status] : '未知状态';
}


/*时间转换*/
function convert_created($created){
  $timestamp=strtotime($created);
  return date('Y年m月d日<b\r>H:i:s',$timestamp);
}


?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Posts &laquo; Admin</title>
  <link rel="stylesheet" href="/static/assets/vendors/bootstrap/css/bootstrap.css">
  <link rel="stylesheet" href="/static/assets/vendors/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" href="/static/assets/vendors/nprogress/nprogress.css">
  <link rel="stylesheet" href="/static/assets/css/admin.css">
  <script src="/static/assets/vendors/nprogress/nprogress.js"></script>
</head>
<body>
  <script>NProgress.start()</script>

  <div class="main">

  <?php include 'inc/navbar.php'; ?>
  
    <div class="container-fluid">
      <div class="page-title">
        <h1>所有文章</h1>
        <a href="post-add.html" class="btn btn-primary btn-xs">写文章</a>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="page-action">
        <!-- show when multiple checked -->
        <a class="btn btn-danger btn-sm" href="javascript:;" style="display: none">批量删除</a>
        <form class="form-inline" action="<?php echo $_SERVER['PHP_SELF']; ?>">
         <select name="category" class="form-control input-sm">
            <option value="all">所有分类</option>
            <option value="">
            </option>
            </select>
          <select name="status" class="form-control input-sm">
            <option value="all">所有状态</option>
            <option value="drafted">草稿</option>
            <option value="published">已发布</option>
            <option value="trashed">回收站</option>
          </select>
          <button class="btn btn-default btn-sm">筛选</button>
        </form>
        <ul class="pagination pagination-sm pull-right">
          <?php if( $page-1>0 ):?>
          <li><a href="?page=<?php echo $page-1;?>">上一页</a></li>
          <?php endif;?>

          <?php for ($i = $begin; $i <= $end; $i++): ?>
          <li<?php echo $i === $page ? ' class="active"' : '' ?>>
          <a href="?page=<?php echo $i; ?>">
            <?php echo $i; ?></a>
        </li>
          <?php endfor;  ?>

          <?php if($page+1<=$total_pages):?>
          <li><a href="?page=<?php echo $page+1; ?>">下一页</a></li>
          <?php endif;?>
         </ul>
      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox"></th>
            <th>标题</th>
            <th>作者</th>
            <th>分类</th>
            <th class="text-center">发表时间</th>
            <th class="text-center">状态</th>
            <th class="text-center" width="100">操作</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($posts as $item): ?>
          <tr>
            <td class="text-center"><input type="checkbox"></td>
            <td><?php echo $item['title'];?></td>
            <td><?php echo $item['user_name'];?></td>
            <td><?php echo $item['category_name'];?></td>
            <td class="text-center"><?php echo convert_created($item['created']);?></td>
            <td class="text-center"><?php echo convert_status($item['status']);?></td>
            <td class="text-center">
              <a href="javascript:;" class="btn btn-default btn-xs">编辑</a>
              <a href="javascript:;" class="btn btn-danger btn-xs">删除</a>
            </td>
           </tr>
           <?php endforeach ?>
        </tbody>
      </table>
    </div>
  </div>

  <?php $current_page='posts';?>
  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script>NProgress.done()</script>
</body>
</html>