@@
layout main
@@
<div class="col-md-2" >
	<div class='left-menu-lists'>
		<ul>
			<li class='title'><a href="#"><span class='glyphicon glyphicon-th-list'></span> &nbsp;&nbsp;日报</a></li>
			<li class='active'><a href="#">所有日报</a></li>
			<li><a href="#">我的日报</a></li>
			<li><a href="#">未读日报</a></li>
			<li><a href="#">评论我的</a></li>
		</ul>
	</div>
</div>
<div class="col-md-7" style="border-left:1px solid #DDD;min-height:1000px;">
	<div class="panel panel-default">
		<div class="panel-body">
			<form role="form">
				<div class="form-group">
					<textarea class="form-control" rows="3" style="resize:none;"></textarea>
				</div>
				<div class="form-group">
					<button type="submit" class="btn btn-primary" style='float:right;'>提交</button>
				</div>
			</form>
		</div>
	</div>

	<div class="panel panel-info">
		<div class="panel-heading">
			<h3 class="panel-title">日志列表</h3>
		</div>
		<div class="panel-body">
			<div class='blog-lists'>
				<div class='title'>
					<a href="#">fantasy</a>&nbsp;&nbsp;&nbsp; <span>11:30 2015-02-02</span>
				</div>
				<div class="content">
					重新设计前端界面改为bootstrap
				</div>
				<div class='opt'><a href="#">评论</a><a href="#">编辑</a></div>
				<div class='comment'>
					<a href="#">fantiq:</a> 这个还是要慢慢想想的
					<a href="#" style='float:right;'>回复</a>
					<div class='replay'>
						<a href="#">fantasy</a> 回复 <a href="">fantiq：</a> hahahahaha
					</div>
					<a href="#">fantiq:</a> 这个还是要慢慢想想的
					<div class='replay'>
						<a href="#">fantasy</a> 回复 <a href="">fantiq：</a> hahahahaha
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="col-md-3" style="min-height:1000px;">.col-md-1</div>