<?php
require_once '../functions.php';
baixiu_get_current_user();


?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="utf-8">
  <title>Comments &laquo; Admin</title>
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
        <h1>所有评论</h1>
      </div>
      <!-- 有错误信息时展示 -->
      <!-- <div class="alert alert-danger">
        <strong>错误！</strong>发生XXX错误
      </div> -->
      <div class="page-action">
        <!-- show when multiple checked -->
        <div class="btn-batch" style="display: none">
          <button class="btn btn-info btn-sm">批量批准</button>
          <button class="btn btn-warning btn-sm">批量拒绝</button>
          <button class="btn btn-danger btn-sm">批量删除</button>
        </div>
        <ul class="pagination pagination-sm pull-right"></ul>
      </div>
      <table class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th class="text-center" width="40"><input type="checkbox"></th>
            <th>作者</th>
            <th width="500">评论</th>
            <th>评论在</th>
            <th>提交于</th>
            <th>状态</th>
            <th class="text-center" width="140">操作</th>
          </tr>
        </thead>
        <tbody>
        </tbody> 
      </table>
    </div>
  </div>

  <?php $current_page='comments';?>
  <?php include 'inc/sidebar.php'; ?>

  <script src="/static/assets/vendors/jquery/jquery.js"></script>
  <script src="/static/assets/vendors/bootstrap/js/bootstrap.js"></script>
  <script src="/static/assets/vendors/twbs-pagination/jquery.twbsPagination.js"></script>
  <script src="/static/assets/vendors/jsrender/jsrender.js"></script>
  <!-- 模板渲染数据 -->
  <script type="text/x-jsrender" id="comments-tmpl">
    {{for comments}}
    <tr {{if status=='held'}} class="warning" {{else status == "rejected"}} class="danger" {{/if}} data-id="{{:id}}">
      <td class="text-center"><input type="checkbox"></td>
      <td>{{:author}}</td>
      <td>{{:content}}</td>
      <td>{{:post_title}}</td>
      <td>{{:created}}</td>
      <td>{{: status === 'held' ? '待审' : status === 'rejected' ? '拒绝' : '准许' }}</td>
      <td class="text-center">
        {{if status === 'held'}}
        <a class="btn btn-info btn-xs btn-edit" href="javascript:;" data-status="approved">批准</a>
        <a class="btn btn-warning btn-xs btn-edit" href="javascript:;" data-status="rejected">拒绝</a>
        {{/if}}
        <a class="btn btn-danger btn-xs btn-delete href="javascript:;">删除</a>
      </td>
    </tr>
    {{/for}}
  </script>
  <!-- 发送ajax请求，获取服务端数据，渲染到页面上 -->
  <script>
    function loadPageDate(page){
      $.get('/admin/api/comments.php',{page:page},function(res){
        $('.pagination').twbsPagination({
          first:'&laquo;',
          last:'&raquo;',
          prev:'&lt;',
          next:'&gt;',
          totalPages: res.total_pages,
          visiablePages: 5,
          initiateStartPageClick:false,
          onPageClick:function(e,page){
            loadPageDate(page);
          }
      });
        
    //回调，请求得到响应之后自动返回
    var html=$('#comments-tmpl').render({comments:res.comments});
        $('tbody').html(html)
      })
    }

      loadPageDate(1);

  //删除功能
  //按钮是动态添加的，动态添加代码是在此之后执行的，采用委托事件方式

    $('tbody').on('click','.btn-delete',function(){
    //先拿到需要删除数据的id
      var $tr=$(this).parent().parent()
      var id=$tr.data('id')

  //发送ajax请求，告诉服务端删除的数据
  $.get('/admin/api/comments-delete.php',{id:id},function(res){
      if(!res) return;
      $tr.remove();
    });

  //根据服务端返回的删除判断是否在界面移除这个数据
  });
  </script>
  <script>NProgress.done()</script>
</body>
</html>
