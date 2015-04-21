<html>
<head>
	<meta http-equiv='content-type' content='text/html;charset=utf-8;'>
	<title></title>
	<link rel="stylesheet" type="text/css" href="../vertu/stylesheets/init.css">
</head>
<body>
	<div class='container'>
		<div class='row' style='margin-top: 10px;'>
			<div class='col-5 panel' id='side-list-box' style=''>
				<a class='item on' href='#overview'>概览</a>
				<a class='item' href='#project'>项目</a>
				<a class='item' href='#config'>配置文件</a>
				<a class='item' href='#parse'>路由解析规则</a>
				<a class='item' href='#controller'>控制器</a>
				<a class='item' href='#database'>模型/数据库</a>
				<a class='item' href='#template'>模版</a>
				<a class='item' href='#components'>组件</a>
				<a class='item' href='#extend'>扩展</a>
			</div>
			<div class='col-18 offset-1'>
				<a name="overview"></a>
				<div class='content'>
					<h1>概览</h1>
					<h4>1、MVC</h4>
					主流的设计模式，分离界面与逻辑，高效的协作开发。
					<h4>2、模块化</h4>
					采用模块形式组织控制器。解决大型项目文件多、难管理的难题，避免后期迭代文件增多导致代码管理混乱的场面。小项目同样可以采用简单的但模块化的架构组织。
					<h4>3、组件式</h4>
					框架所有功能都是以组件形式工作，您可以根据自己的需求对各个组件进行改进升华，以适应自己的开发规则，同时可以添加自己的功能组件到框架中，过程简单易用。
					<h4>4、易使用</h4>
					在控制器以及模型中可以自动的加载所有组件，省去配置、加载等繁琐的操作，一切都是如此简单<span class='tag-def'>$this->组件名</span>
					<h4>5、高效率</h4>
					框架根据php语言的特性，在特定部分采用单例的设计模式以节省内存的使用。采用控制反转(IoC)的设计模式实例化类，以降低模块之间的耦合度。
					<h4>6、数据库</h4>
					数据库提供了三种操作形式。1 直接执行sql，2 通过连贯操作组合sql语句，3 ORM操作数据库，简单快捷。
					<h4>7、安全性</h4>
					1、用户发送的数据全部进行初步检测，并且销毁全局数组，防止一句话脚本的攻击<br>
					2、提供数据过滤，清除非打印字符，文件名不合法，XSS字符串。让网站免受跨站攻击<br>
					3、防止sql注入，数据库在执行前都会对sql字符串进行合法性检测<br>
					4、提供数据格式验证组件，对用户提交的数据类型进行检测，防止数据表字段溢出
					5、提供验证码以及表单CSRF防御机制，以应对互联网的'洪水'攻击
				</div>
				<a name="project"></a>
				<!-- 项目 -->
				<div class='content'>
					<h1>项目</h1>
					<h4>创建项目文件夹</h4>
					我们创建自己的项目文件夹叫做 <span class='tag-def'>App</span>，然后在文件夹里面创建我们项目需要的各个文件<br>
					<ul class='folder-lists'>
						<li><i class='icon-folder-open'></i> App</li>
						<li class='deep-1'><i class='icon-folder-open'></i> controllers<span class='r'>放置控制器类</span></li>
						<li class='deep-1'><i class='icon-folder-open'></i> models <span class='r'>放置模型类</span></li>
						<li class='deep-1'><i class='icon-folder-open'></i> config <span class='r'>放置配置文件</span></li>
						<li class='deep-1'><i class='icon-folder-open'></i> view <span class='r'>放置模版文件</span></li>
						<li class='deep-1'><i class='icon-file'></i> index.php <span class='r'>入口文件</span></li>
					</ul>
					其他文件夹用户可自行创建，建议您将项目文件（控制器、模型类、配置文件）与可访问的文件（入口文件、图片、css、js）分开存放，并且设置不同的读写权限。
					<h4>入口文件</h4>
					入口文件内容仅仅需要下面的三行代码
					<pre class='code'>
&lt;?php
	define('APP_PATH', dirname(__DIR__));	 		// 指定项目文件夹
	include dirname(__DIR__).'/src/App.php'; 			// 加载框架入口文件
	App::run();									// 执行框架
					</pre>
					<h4>配置</h4>
					设置你的配置文件 参照这里 <a href='#config'>配置文件</a>
				</div>
				<a name="config"></a>
				<div class='content'>
					<h1>配置</h1>
					<h4>配置文件</h4>
					下面是配置文件夹的内容：
					<ul class='folder-lists'>
						<li><i class='icon-folder-open'></i> config</li>
						<li class='deep-1'><i class='icon-file'></i> configs.php<span class='r'>核心配置文件</span></li>
						<li class='deep-1'><i class='icon-file'></i> rules.php<span class='r'>表单自动验证定义的规则</span></li>
						<li class='deep-1'><i class='icon-file'></i> hooks.php<span class='r'>定义的钩子程序</span></li>
						<li class='deep-1'><i class='icon-file'></i> datas.php<span class='r'>自定义的配置参数</span></li>
					</ul>
					具体请参阅源代码中configs.php文件的注释
					<h4>主配置文件</h4>
					<pre class='code'>
&lt;?php
return [
	// 全局的应用程序配置项
	'application'=>[
		'id'=>'app',												// 应用程序的id，项目的命名空间会用到
		'timezone'=>'RPC', 											// 设置时区
		'hooks'=>['file'=>'config/hooks.php','class'=>'Hooks'], 	// 指定钩子程序的位置
	],
	// 路由配置
	// 'router'=>[
	// 0 自动识别url
	// 1 ?m=Admin&c=Access&a=login&arg1=1....
	// 2 Admin/Access/login/arg1/arg2...
	// 3 ?r=Admin/Access/login/arg1/arg2...
	// 4 
		'urlmode'=>0,
		'defaultController'=>'Index',		// 默认控制器
		'defaultAction'=>'index',			// 默认方法
		'modules'=>['Api/WeiXin','Admin'],	// 存在的模块名
		'regex'=>[							// 正则匹配url规则
			'pattern'=>'Index',
		],
	],
	// 数据库配置
	'database'=>[
		// 'dsn'=>'pdo-mysql://root@127.0.0.1/test',
		'dsn'=>'mysql://root@127.0.0.1/test', 	// dsn形式
		'scheme'=>'pdo-mysql',					// 数据库类型（pdo类型的要以 pdo-模型 的形式指定）
		'host'=>'127.0.0.1',					// 地址
		'port'=>3306,							// 端口
		'dbname'=>'test',						// 数据库名称
		'user'=>'root',							// 帐号
		'passwd'=>'321321',						// 密码
		'charset'=>'utf8',						// 数据表编码
		'prefix'=>''							// 数据表前缀
	],
	// 内存缓存
	'cache'=>[
		'dsn'=>'memcache://127.0.0.1:11211', 	// dsn字符串形式定义
		'scheme'=>'memcache',					// 缓存类型
		'host'=>'127.0.0.1',					// 地址
		'port'=>11211,							// 端口
	],
	// session
	'session'=>[
		'auto_start'=>false,	// 自动加载
		'passwd'=>'321321',	// 连接store的密码
		// 'dsn'=>'pdo-mysql://root:@127.0.0.1:3306/session/sess_tab',
		'dsn'=>'memcache://127.0.0.1:11211', 	// 字符串形式
		'scheme'=>'pdo-mysql', 					// 存储session数据库的模型
		'host'=>'127.0.0.1',					// 地址
		'port'=>3306,							// 端口号
		'user'=>'root',							// 用户名
		'dbname'=>'session',					// 存储session的数据库名称
		'tbname'=>'sess_tab',					// 存储session的数据表名称
		'charset'=>'utf8',						// 编码方式
		'prefix'=>'',							// 表前缀
		'sess_name'=>'__SESSIONID__',			
		'sess_expire'=>3600*24,		// 默认session过期时间
		'alive_time'=>3600,			// 用户活跃时间间隔 这个时间内没有任何操作视为下线
		'cookie_path'=>'/',			// cookie 路径
		'cookie_domain'=>'',		// cookie 域名
	],
	// 模版配置
	'view'=>[
		'drive'=>'template',			// 模板引擎
		'skin'=>'default',				// 默认皮肤
		'tpl_ext'=>'php',				// 模版文件后缀
		'form_hash_name'=>'__hash__',	// 表单hash字段名
		'form_hash_keys'=>'fantasy',	// 表单hash的key
	],
	/////////////
	// 加载用户数据 //
	/////////////	
	'datas'=>include 'datas.php',
	'alias'=>[],
];
					</pre>
					<h4>钩子程序</h4>
					默认的钩子程序是在 项目文件夹/config/hooks.php，你也可以在配置文件中指定其他的文件作为钩子程序。
					<table class='table'>
						<tr class='title'>
							<th>#</th>
							<th>方法名</th>
							<th>作用</th>
						</tr>
						<tr>
							<td>1</td>
							<td>preSystem</td>
							<td>框架初始化之后调用执行</td>
						</tr>
						<tr>
							<td>2</td>
							<td>preRoute</td>
							<td>在路由解析url之前调用</td>
						</tr>
						<tr>
							<td>3</td>
							<td>preController</td>
							<td>在url解析之后，调用执行指定的控制器之前调用</td>
						</tr>
						<tr>
							<td>4</td>
							<td>preResponse</td>
							<td>在发送内容到用户客户端之前调用</td>
						</tr>
						<tr>
							<td>5</td>
							<td>endSystem</td>
							<td>整个交互过程完成之后执行</td>
						</tr>
					</table>
					钩子程序中的类继承了Base.class 你在钩子程序中是可以调用所有绑定过的组件的，但是你不能修改钩子程序类中的方法名。
					<pre class='code'>
&lt;?php
use framework\base\Base;
class Hooks extends Base{
	public function __construct(){
		$this->preSystem();
	}
	// 框架开始执行之前 初始化之前
	public function preSystem(){}
	// 路由开始解析之前
	public function preRoute(){
		// echo 'hello';
	}
	// 路由解析之后 调用用户控制器之前
	public function preController(){
		// echo "你请求的控制器是：".$this->dispatch->getControllerName()."<br>";
		// echo "你请求的控制器方法是：".$this->dispatch->getActionName()."<br>";
	}
	//发送内容到用户浏览器之前
	public function preResponse(){}
	框架结束
	public function endSystem(){}
}
					</pre>
				</div>
				<a name="parse"></a>
				<div class='content'>
					<h1>路由解析规则</h1>
					框架支持多种的url风格
					<ul>
						<li><i class='icon-check'></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ?m=模块名&c=控制器名&a=方法&args....</li>
						<li><i class='icon-check'></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; /模块名/控制器名/方法/args....</li>
						<li><i class='icon-check'></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ?r=模块名/控制器名/方法/args....</li>
					</ul><br>
					模块名是为了指明你的控制器所在的文件夹目录，未指定则指定是 <span class='tag-def'>项目/controllers/</span> 这个目录，指定的话需要你事先在配置文件中 <span class='tag-def'>['router']['modules'] => [模块1,模块2,模块3]</span> 声明。指定模块名的话，框架所调用的控制器文件路径就是 <span class='tag-def'>项目/controllers/模块名/</span> 这个目录了。<br>
					比如我们需要访问一个控制器，这个控制器文件所在目录路径 App/controllers/Pay/Taobao/CashController.php 如下图：
					<ul class='folder-lists'>
						<li><i class='icon-folder-open'></i> App</li>
						<li class='deep-1'><i class='icon-folder-open'></i> controllers</li>
						<li class='deep-2'><i class='icon-folder-open'></i> Pay <span class='r'>嵌套的模块</span></li>
						<li class='deep-3'><i class='icon-folder-open'></i> Taobao <span class='r'>嵌套的模块</span></li>
						<li class='deep-4'><i class='icon-file'></i> CashController.php <span class='r'>控制器类文件</span></li>
						<li class='deep-1'><i class='icon-folder-close'></i> models</li>
						<li class='deep-1'><i class='icon-folder-close'></i> config</li>
					</ul>
					我们可以知道，要访问的控制器 CashController.php 在文件夹 controllers/Pay/Taobao 下，而 Pay/Taobao 则是这个控制器的模块名。URL中的参数部分应该是这样的 /Pay_Taobao/Cash/index
					Pay_Taobao 会被框架自动转义成 Pay/Taobao ，URL中的模块名 控制器名都会被自动转义，规则如下
					<table class='table'>
						<tr class='title'>
							<td style='width:60px;'>URL部分</td>
							<td>原来的</td>
							<td>转义后</td>
							<td>转义规则</td>
						</tr>
						<tr>
							<td>模块名</td>
							<td>foo</td>
							<td>Foo</td>
							<td style='text-align:left;'>在没有 '-' '_' 字符串时，将模块名字符串首字母大写</td>
						</tr>
						<tr>
							<td>模块名</td>
							<td>foo_bar</td>
							<td>Foo/Bar</td>
							<td style='text-align:left;'>对于中间有下划线的，会依据下划线分割字符串，再将每个字符串首字母大写然后用 '/' 将字符串拼接</td>
						</tr>
						<tr>
							<td>模块名</td>
							<td>foo-bar</td>
							<td>FooBar</td>
							<td style='text-align:left;'>对于中间有横线的，会依据横线分割字符串，再将每个字符串首字母大写然后直接将字符串拼接</td>
						</tr>
						<tr>
							<td>控制器</td>
							<td>bar</td>
							<td>Bar</td>
							<td style='text-align:left;'>在没有 '-' 字符串时，将控制器的首字母大写。(所以在类以及类文件的命名规则都要首字母大写)</td>
						</tr>
						<tr>
							<td>控制器</td>
							<td>foo-bar</td>
							<td>FooBar</td>
							<td style='text-align:left;'>对于中间有横线的，会依据横线分割字符串，再将每个字符串首字母大写然后直接将字符串拼接</td>
						</tr>
					</table>
				</div>
				<a name="controller"></a>
				<div class='content'>
					<h1>控制器</h1>
					<h4>命名规则</h4>
					我们采用流行的驼峰命名法对文件名、类名进行命名规范<br>
					控制器文件名首字母要大写且要连接上字符串Controller，后缀为php。比如我们的控制器名称为Index，则文件名为 IndexController.php。若我们要定义的控制器名称是两个单词(customer 、analysis)组成的 则文件名应该是 CustomerAnalysisController.php 。值得注意的是 <span class='tag-warn'>文件名要和类名一致，包括大小写</span>。
					<h4>命名空间</h4>
					我们采用命名空间对用户的类进行管理，这样您就不必担心类命名冲突的问题啦！如何使用命名空间？<br>
					1、在文件的最开始定义命名空间 <span class='tag-def'>namespace 自定义的名字</span><br>
					2、需要使用一个类的时候 <span class='tag-def'>use 自定义的命名空间\类名</span>
					更多关于命名空间的用法可以到这里查看 <a href="http://blog.csdn.net/fanyilong_v5/article/details/24383235" target='__blank'>php5增加的命名空间以及异常</a><br>
					3、命名空间的名称是以 foo\bar\class 这样的形式来命名的，一般这个字符串要反映出所属模块以及文件路径的 模块\路径\路径..<br>
					<h5>创建我们的第一个控制器 IndexController.php</h5>
					在controllers目录创建一个文件名称为 <span class='tag-def'>IndexController.php</span>
					文件内容如下：
					<pre class='code'>
&lt;?php
namespace app\controllers;					// 命名空间
use framework\base\Controller;				// 继承框架的控制器
class IndexController extends Controller{		// 定义类名
	public function show(){ 				// 创建一个方法
		echo "Hello World!";
	}
}
					</pre>
					对于命名空间 <span class='tag-def'>app\controllers</span> app指明模块是我们的项目 ，controllers是我们文件的存放路径。注意，这里的 app 是你在配置文件中定义的 ['application']['id'] 项。在你的项目中的所有类定义命名空间都要以这个id为开头，来表示文件所属的模块。
					框架的命名空间是以 framework 为开始的。并且控制器要继承框架的控制器，这样你就能方便的调用各个组件啦！
					<pre class='code'>
// 常用的页面显示函数
$this->assign('key','val')   为模版赋值
$this->display('tpl_name') 显示模版</pre>
				</div>
				<!-- 数据库 -->
				<a name="database"></a>
				<div class='content'>
					<h1>数据库</h1>
					<h4>连贯操作</h4>
					无论是在控制器还是在模型中进行编码，我们都可以随时使用数据库，只需要你简单的直接使用 <span class='tag-def'>$this->db</span> 。
					<ul>
						<li><i class='icon-info-sign'></i> $this->db->select(查询的字段)->where(查询条件)->limit(条数限制)->all()</li>
						<li><i class='icon-info-sign'></i> $this->db->insert(表名,存储的数据)</li>
						<li><i class='icon-info-sign'></i> $this->db->where(条件)->update(表名,修改的数据)</li>
						<li><i class='icon-info-sign'></i> $this->db->update(表名,修改的数据,条件)</li>
						<li><i class='icon-info-sign'></i> $this->db->where(条件)->delete(表名)</li>
						<li><i class='icon-info-sign'></i> $this->db->delete(表名,条件)</li>
					</ul>
					<h6>1、查询</h6>
					一个查询 <pre class='code'>
$this->db->select()->from('tests')->where('level',2)->order('name','DESC')->limit(10,15)->all();</pre>最终执行的sql语句是这样的 <pre class='code'>
select * from `tests` where level=2 order by name desc limit 10,15</pre>
					对于where条件语句值得注意，你可以用三种形式构造你个查询条件(或简单或复杂)
					<ul class='folder-lists'>
						<li>1、where(<b>field,value</b>) <span class='r'>简单的就是两个值，field=value</span></li>
						<li>2、where(field=>value,比较符,连接符) <span class='r'>多个条件的情况</span></li>
						<li>3、where('field1=? and field2>?',['name',3]) <span class='r'>以statement的形式传递参数</span> </li>
					</ul>
					举例如下:<pre class='code'>
$this->db->where([
	['name'=>'fantasy','=','and'],
	['date'=>'12-01','>','or'],
	['date'=>'12-31','<'],
])
<b>对应的SQL语句：</b> where name='fantasy' and date>'12-01' or date<'12-31'</pre>
					<h6>2、添加</h6>
					添加数据就是 <span class='tag-def'>$this->db->insert(tablename,data)</span>
					tablename 就是表名成，对于data是添加的数据，形如 <span class='tag-def'>[field1=>val1,field2=>val2,field3=>val3.....]</span>。若要增加多条记录data需要是多维数组的形式<pre class='code'>
[
	[field1=>val1-1,field2=>val1-2,field3=>val1-3.....], 	// 第一条数据
	[field1=>val2-1,field2=>val2-2,field3=>val2-3.....],	// 第二条数据
	[field1=>val3-1,field2=>val3-2,field3=>val3-3.....], 	// 第三条数据
	[field1=>val4-1,field2=>val4-2,field3=>val4-3.....], 	// 第四条数据
	........ 											// 更多
]</pre>
					<h6>3、修改</h6>
					更新数据 $this->update(tablename,data[,condition])<br>
					tablename 要更新的表名称，data 更新的数据，更新的条件（这里可以不填，在$this->db->where() 处指明条件）。
					数据部分的形式多为这样 <pre class='code'>data = [field1=>newValue1,field2=>newValue2,field3=>newValue3,.......]</pre>
					<h6>4、删除</h6>
					$this->delete(表名称,条件) or $this->where(条件)->delete(表名称)
					<h4>ORM</h4>
					值得注意的是目前的ORM仅限于在model层使用，ORM的操作会让你的开发效率更上一层楼。当我们想要映射一张表的时候只需要在模型(model)的方法里这样写 <span class='tag-def'>$表的实例化对象 = $this->orm(表名称)</span> 。<br>
					 我们创建这样一张表
					 <table class='table'>
					 	<tr class='title'><td>字段名称</td><td>类型</td><td>注释</td></tr>
					 	<tr><td>id</td><td>unsigned int</td><td>记录id</td></tr>
					 	<tr><td>name</td><td>varchar(255)</td><td>名称</td></tr>
					 	<tr><td>email</td><td>varchar(255)</td><td>邮箱</td></tr>
					 	<tr><td>addr</td><td>text</td><td>地址</td></tr>
					 	<tr><td>add_time</td><td>unsigned int</td><td>添加记录的时间</td></tr>
					 	<tr><td>ip</td><td>unsigned int</td><td>用户的ip</td></tr>
					 </table>
					<h6>1、添加数据</h6> <pre class='code'>public function testOrm(){
	$tests = $this->orm('tests'); // 声明一个表的映射
	$tests->name='orm'; 		// 赋值
	$tests->email='orm@oa1024.com';
	$tests->addr='jinhua';
	$tests->add_time=time();
	$tests->ip = sprintf('%u',ip2long('192.168.0.12'));
	var_dump($tests->add()); /// 调用add方法添加数据 成功返回true 失败返回false
}</pre>
				<h6>2、修改数据</h6>
				$orm->save(条件) 这个条件跟非orm模式的条件格式一样。不同的是，为了安全起见，如果在更新的时候不想设置条件，需要在条件的部分填写false，否则方法返回false<pre class='code'>$tests = $this->orm('tests');
$tests->name = 'fantiq';
$tests->email = 'fantiq@163.com';
var_dump($tests->save(['id',1]));</pre>
				<h6>3、删除</h6>
				$orm->delete(条件) 条件跟save方法的用法一样。
				<h6>4、查询数据</h6>
				ORM模块提供了方法的查询方法，满足您各种的查询需求。查询结果都是以对象的形式返回，默认是只去结果集的前20条的，你可以通过<span class='tag-def'>setLimit(...)</span>来修改这个参数；查询条件中如果只传递一个只的话，框架会将这个值使用在表的主键字段上，默认的主键字段是 id ，你可以通过 <span class='tag-def'>setPrimaryKey</span> 来修改这个参数。下面是ORM查询方面的方法：<ul class='folder-lists'>
					<li><i class='icon-wrench'></i> get([fields...]) <span class='r'>获取指定字段的所有数据</span></li>
					<li><i class='icon-wrench'></i> getOne([fields...]) <span class='r'>获取指定字段的一条数据</span></li>
					<li><i class='icon-wrench'></i> getWhere(条件) <span class='r'>根据条件查询数据</span></li>
					<li><i class='icon-wrench'></i> getBy(排序规则[order/group],排序字段) <span class='r'>对查询结果进行排序</span></li>
					<li><i class='icon-wrench'></i> getWhereBy(条件，排序规则，排序字段) <span class='r'>根据条件查询数据并根据排序规则将结果排序</span></li>
					<li><i class='icon-wrench'></i> setLimit() <span class='r'>修改limit的参数</span></li>
					<li><i class='icon-wrench'></i> setPrimaryKey() <span class='r'>设置主键</span></li>
				</ul>
				</div>
				<a name="template"></a>
				<div class='content'>
					<h1>模版</h1>
					<h4>控制器调用模版</h4>
					在控制器里面我们常用的是assign(key,val) 方法，对模版进行赋值，display([path]) 方法进行显示页面信息，若display指定参数则会调用指定路径的页面，若未指定这调用默认页面 项目/views/皮肤/控制器名称/方法名.模版后缀。
					cache(时间min) 方法可以设置页面静态缓存，参数单位为分钟，表示缓存过期时间。<pre class='code'>
public function index(){
	$this->assign('name','fantasy');
	$this->assign('lists',['fantasy','addr','time']);
	// $this->cache(1); // 一分钟的缓存
	$this->display();
	// $this->display('ad/main');
}</pre>
					<h4>模版输出</h4>
					你可以通过 <span class='tag-def'> setLeftTag() </span> 、<span class='tag-def'> setRightTag() </span>这些方法定义模版标签的界定符，默认是 <span class='tag-def'>"<{" </span>和<span class='tag-def'> "}>"</span> 。模版支持皮肤功能功能，你可以通过 <span class='tag-def'> setSkin() </span> 方法切换皮肤，默认的皮肤是 'default' 。
					<h6>输出</h6><pre class='code'>
<{变量名}> 									&lt;!-- 这样会直接输出变量 --&gt;

<{foreach $data->$val}> 						&lt;!-- 遍历数字索引的数组 --&gt;
<{val}>
<{/foreach}>
<{foreach $data=$key->$val}> 					&lt;!-- 遍历字符串索引的数组 --&gt;
<{key}>---><{val}>
<{/foreach}>

<{if 判断条件}> 								&lt;!-- 分支判断 --&gt;
....
<{else}>
....
<{/if}>
					</pre>
					<h6>模版功能</h6>
					页面可被分为多个模块，可以通过模版提供的命令加载模版<pre class='code'>
@@
layout 模版路径(不要写文件后缀)
css css资源
js js资源
@@</pre>
					layout 可以让你指定模版文件，然后当前模版的内容会填充到指定模版的位置( 指定模版的标签 <{tag-content}>)处。css 、js 会将指定的资源引用一并注入到模版中(模版中对应的标签是 <{tag-assets}>)。
				</div>
				<!-- 组件 -->
				<a name="components"></a>
				<div class='content'>
					<h1>组件</h1>
					<h4>Session</h4>
					框架中session的使用非常简单，你只需要 <span class='tg-def'>$this->session->start()</span> session数据就加载成功并且能够提供给你使用了，并且框架会在最后更新数据并持久化存储，常用的方法有如下几种，使用非常简单：
					<ul class='folder-lists'>
						<li>$this->session->set(key,val) <span class='r'>设置或修改session数据</span></li>
						<li>$this->session->get(key) <span class='r'>获取session数据，若设置key值，则返回所有的session数据</span></li>
						<li>$this->session->del(key) <span class='r'>删除一项session数据</span></li>
						<li>$this->session->isOnline(user_id) <span class='r'>查询某个uid是否在线</span></li>
						<li>$this->session->countOnline() <span class='r'>返回总在线用户数</span></li>
					</ul>
					值得注意的是你需要在配置文件中指明你需要存储session的数据库(mysql memcache redis)，建议您使用memcache redis的内存型缓存数据库效率会高，mysql或者php文件型的存储在大访问量下过多的IO使项目效率下降。如若你使用php原生的session机制，可以直接使用 <span class='tag-def'>$_SESSION</span> 数据进行操作。
					<h4>数据验证</h4>
					框架提供了web应用中最常用的几个数据格式验证 ，你可以通过 <span class='tag-def'>$this->verify->验证函数(参数)</span> 直接使用，下面列出这些方法以及用法
					<h6>1、字段不能为空</h6>
					<span class='tag-info'>$this->verify->required(string $str);</span>
					方法会先过滤用户提交的不可打印的字符，其次过滤掉空格，最后检测是否是空字符串。
					<h6>2、邮箱格式验证</h6>
					<span class='tag-info'>$this->verify->email(string $email)</span>
					内部采用的是php的过滤函数进行的验证，字符串太长(超过1000字符)也会返回false
					<h6>3、url链接格式验证</h6>
					<span class='tag-info'>$this->verify->url(string $url)</span>
					由于php自带的验证不太灵活(不带协议会返回false)，采取新的验证格式。
					<h6>4、ip地址验证</h6>
					<span class='tag-info'>$this->verify->ip(string $ip)</span>
					采用php内部过滤函数的验证方法。
					<h6>5、身份证号码验证</h6>
					<span class='tag-info'>$this->verify->id(string/int $id)</span>
					采用身份证的数据验证算法对身份证数字格式进行验证。
					<h6>6、手机号(中国)格式验证</h6>
					<span class='tag-info'>$this->verify->phone(string/int $phone)</span>
					手机号码验证，目前只支持中国的手机号。
					<h6>7、数组验证</h6>
					<span class='tag-info'>$this->verify->number(int/string number,int min,int max)</span>
					检测给定的数字是否在 min max的范围内，如果仅需要检测数字是否小于某个数只需要，<span class='tag-def'>$num=10;$this->verify->number($num,null,100)</span> 这个是检测数字$num是否小于 100。
					<h6>8、检测字符串格式</h6>
					<span class='tag-info'>$this->verify->string(string $str,string $rule)</span>
					其中$rule 可以是 alpha、num、zh 以及其他的。alpha表示仅允许字符串，num仅允许数字，zh仅允许汉字。若我们需要字符串允许数字级字母就要这样写 $rule = "alpha,num"
					<span class='tag-def'>$this->verify->string($str,"alpha,num")</span>。若我们需要允许其他的字符只需要在$rule字符串后面跟上需要允许的字符并用 ',' 隔开即可。比如 <span class='tag-def'>$this->verify->string('fanyilong@sina.com','alpha,num,@,.')</span> 这个将返回<span class='tag-prim'>true</span>
					<h6>9、检测字符串长度</h6>
					<span class='tag-info'>$this->verify->len(string $str,int min,int max)</span>
					<h6>10、检测是否存在一个列表中</h6>
					<span class='tag-info'>$this->verify->in(mixed $var,string $lists)</span>
					变量 $lists 是一列数据的字符串，这些数据用 ',' 隔开。如同这样的数据 $lists = "android,iOS,linux,centos,windows"; 方法就是检测给定的数值是否存在于这个列中。
					<h6>11、匹配</h6>
					<span class='tag-info'>$this->verify->match(mixed $var,string $field)</span>
					这个功能最常用在在注册的时候检测两次密码是否一致。$var 是值，$field是需要检测值是否一致的字段名称。
					<h4>配置</h4>
					配置的用法最简单 当我我们想要获取某项值的时候 <span class='tag-def'>$this->config->get(key); </span>同样在需要设置修改某项值的时候 <span class='tag-def'>$this->config->set(key,val);</span>
					<h4>HTTP数据</h4>
					在web交互方面这个用的很频繁，但是又十分容易遭到攻击(XSS)，框架最这些数据进行了初步的过滤并删除全局变量，防止在不正当的使用中导致攻击。
					<ul class='folder-lists'>
			<li><i class='icon-wrench'></i> $this->request->get(key) <span class='r'>获取GET形式的数据</span></li>
			<li><i class='icon-wrench'></i> $this->request->post(key) <span class='r'>获取POST形式的数据</span></li>
			<li><i class='icon-wrench'></i> $this->request->cookie(key) <span class='r'>获取COOKIE数据</span></li>
			<li><i class='icon-wrench'></i> $this->request->files(key) <span class='r'>获取上传文件 $_FILES 数据</span></li>
			<li><i class='icon-wrench'></i> $this->setGet(key,val) <span class='r'>设置修改GET的某项数据</span></li>
			<li><i class='icon-wrench'></i> $this->setPost(key,val) <span class='r'>设置修改POST的某项数据</span></li>
			<li><i class='icon-wrench'></i> $this->setCookie(key,val) <span class='r'>设置修改COOKIE的某项数据</span></li>
					</ul>
					<h4>工具</h4>
					工具类组件中提供了丰富的类供您使用，您只需要 <span class='tag-def'>$upload = $this->utils->getUpload(); </span> 就可以使用上传类了。其他的有图片，验证码，文件上传，分页，文件系统 等等...
					<h4>工具-上传文件</h4>
					下面一段代码是文件上传，非常简单：<pre class='code'>
public function upload(){
	$upload = $this->utils->getUpload(); 	// 实例化上传组件
	$config = [								// 设置配置项
		'type'=>['txt','jpg','png','gif'],
		'size'=>2048,
		'path'=>'./uploads',
		'rename'=>true
	];
	if($upload->run(field)){				// run(field,config) 开始上传
		// 上传成功 返回成功相关信息(上传后的文件路径)
		print_r($upload->getInfo());
	}else{
		// 上传失败 获取失败信息
		print_r($upload->getError());
	}
}</pre>配置部分: type =>[...] 指定允许的文件后缀；size=>2048 指定文件最大尺寸 单位是KB；path=>'./....' 指定上传文件的存储位置，注意这个文件夹要可写(chmod 777)；rename=>true/false 是否将文件名重命名
					<h4>工具-图片 / 验证码</h4>
					同样通过 <span class='tag-def'>$img = $this->utils->getImage();</span> 实例化图片组件，这个组件主要有三个功能： <span class='tag-def'>captcha()</span>生成验证码 ；<span class='tag-def'>thumb(缩放比例,保存路径,缩放参照)</span>生成图片缩略图；<span class='tag-def'>crop()</span>截取图片；
					<h6>验证码： $image->captcha(string $h,number $n,array $bg,array $color) </h6>
					你可以参考源代码里面的代码示例 <a target='__blank' href="http://127.0.0.1/php-framework/public/component/image/verify">验证码示例</a>
					<p>参数列表：</p>
					<ul class='folder-lists'>
<li>$h <span class='r'>验证码的图片高度</span></li>
<li>$n <span class='r'>验证码字符数量</span></li>
<li>$bg <span class='r'>验证码图片背景色(rgb)</span></li>
<li>$color <span class='r'>验证码字符颜色(rgb)</span></li>
					</ul>
					<h6>图片缩放：$image->thumb(int $refer,string $savePath,int $order) </h6>
					<p>参数说明：</p>
					<ul class='folder-lists'>
						<li>$refer<span class='r'>缩放比例</span></li>
						<li>$savePath<span class='r'>处理后的图片保存路径</span></li>
						<li>$order<span class='r'>参照标准，0 参照宽度缩放，1 参照高度缩放</span></li>
					</ul>
					<h6>图片截取：$image->crop(int $start_x,int $start_y,int $x_len,int $y_len,int $dst_w,int $dst_h,string $savePath)</h6>
					<ul class='folder-lists'>
						<li>$start_x<span class='r'>x轴开始截取的位置</span></li>
						<li>$start_y<span class='r'>y轴开始截取的位置</span></li>
						<li>$x_len<span class='r'>x轴截取长度</span></li>
						<li>$y_len<span class='r'>y轴截取长度</span></li>
						<li>$dst_w<span class='r'>粘贴到的图片宽度</span></li>
						<li>$dst_h<span class='r'>粘贴到的图片高度</span></li>
						<li>$savePath<span class='r'>图片保存地址</span></li>
					</ul>
					<h4>工具-分页</h4>
					使用分页工具的时候我们需要这样 <span class='tag-def'>$page = $this->utils->getPagination(int count,int listNum,int page)</span>
					解释下方法 getPagination的三个参数：<ul class='folder-lists'>
						<li>count <span class='r'>要分页的数据的总条数</span></li>
						<li>listNum <span class='r'>每页显示的数据的条数</span></li>
						<li>page <span class='r'>要显示哪页的数据</span></li>
					</ul>每页显示的条数我们可以通过 <span class='tag-def'>$page->setListNum(int n)</span> 来动态设置，也可以在配置文件中设置 <pre class='code'>
['application'=>[
	.....
	'page_lists'=> 每页显示的条数,
	....
	]
]</pre>分页中的数据在技术上是反映在sql语句的limit部分的参数的写法，这个分页类通过计算为你提供的这些参数，<span class='tag-def'>$page->getStart()</span> 返回limit要开始的位置，<span class='tag-def'>$page->getLimitNum()</span> 返回取出显示的条数，在使用中就是下面这样：<pre class='code'>
public function list($p=1){
	$page = $this->utils->getPagination($count,$listNum,$p);
	sql..... limit .$page->getStart.','.$page->getListNum();
	// 或者 用getLimit() 方法，这个方法直接返回的是limit参数的组合
	sql..... limit $page->getLimit();
}
</pre>下面是前端框架的分页css的一个示例：
					<ul class='pagination'>
						<li><a href="javascript:;">上一页</a></li>
						<li><a href="javascript:;">1</a></li>
						<li class='on'><a href="javascript:;">2</a></li>
						<li><a href="javascript:;">3</a></li>
						<li><a href="javascript:;">4</a></li>
						<li><a href="javascript:;">5</a></li>
						<li><a href="javascript:;">下一页</a></li>
					</ul>
					<h4>工具-文件</h4>
				</div>
			</div>
		</div>
	</div>
</body>
</html>