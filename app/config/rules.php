<?php
return [
	'default'=>[
		'username'=>['required','用户名不能为空'],
		'email'=>['email','邮箱格式不正确'],
		'gender'=>['in:1,2','选择正确的'],
		'age'=>['number:10,90','年龄不符合'],
		'verify_code'=>['verify','验证码错误'],
		'reply_content'=>['len:1,500','内容太长','xss'],
		'title'=>['require|len:3,20','内容太长','xss|cut:3,...'],
	],
	'login'=>[
		'username'=>['required|email','用户名不能为空|邮箱格式不正确','xss'],
		'password'=>['required','密码不能为空'],
		're-password'=>['match:password','不匹配'],
	],
];