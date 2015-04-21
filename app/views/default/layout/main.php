<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html;charset=utf-8;">
	<title>OA1024</title>
	<base href="<?php echo ASSET_PATH;?>">
	<link rel="stylesheet" href="assets/dist/css/bootstrap.css">
	<link rel="stylesheet" href="assets/css/common.css">
	<script type="text/javascript" src="assets/js/jquery.js"></script>
	<script type="text/javascript" src="assets/dist/js/bootstrap.js"></script>
	<link rel="stylesheet" href="assets/dist/css/font-awesome.min.css">
	<!--[if IE 7]>
		<link rel="stylesheet" href="assets/dist/css/font-awesome-ie7.min.css">
	<![endif]-->
	<{tag-assets}>
</head>
<body>
<!-- 导航 -->
<nav class="navbar navbar-default" role="navigation">
	<div class="container-fluid">
		<!-- 响应式导航 -->
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
				<span class="sr-only">导航</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="#">OA1024</a>
		</div>
	<!-- Collect the nav links, forms, and other content for toggling -->
		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
			<ul class="nav navbar-nav">
				<li  class="index active"><a href="#">首页 <span class="sr-only">(current)</span></a></li>
				<li class="report"><a href="#">日报</a></li>
				<li class="project"><a href="#">项目</a></li>
				<li class="flow"><a href="./Flow/">流程</a></li>
				<li class="knowlege"><a href="#">知识库</a></li>
			</ul>
			<form class="navbar-form navbar-left" role="search">
				<div class="form-group">
					<input type="text" class="form-control" placeholder="你想找什么?">
				</div>
				<button type="submit" class="btn btn-default">搜索</button>
			</form>
			<ul class="nav navbar-nav navbar-right">
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">新建 <span class="caret"></span></a>
					<ul class="dropdown-menu" role="menu">
						<li><a href="#">日报</a></li>
						<li><a href="#">项目</a></li>
						<li><a href="#">项目</a></li>
						<li><a href="#">知识库</a></li>
					</ul>
				</li>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">设置 <span class="caret"></span></a>
					<ul class="dropdown-menu" role="menu">
						<li><a href="#">用户设置</a></li>
						<li><a href="#">权限设置</a></li>
						<li><a href="#">流程设置</a></li>
						<li><a href="#">部门设置</a></li>
					</ul>
				</li>
				<li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">fantasy <span class="caret"></span></a>
					<ul class="dropdown-menu" role="menu">
						<li><a href="#">个人中心</a></li>
						<li><a href="#">修改头像</a></li>
						<li><a href="#">消息通知</a></li>
						<li class="divider"></li>
						<li><a href="#">退出</a></li>
					</ul>
				</li>
			</ul>
		</div><!-- /.navbar-collapse -->
	</div><!-- /.container-fluid -->
</nav>
<!-- 内容 -->

<div class='container-fluid'>
	<div class="row">
		<{tag-content}>
	</div>
</div>
</body>
</html>