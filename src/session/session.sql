CREATE DATABASE IF NOT EXISTS `phpframework`;
USE `phpframework`;
CREATE TABLE IF NOT EXISTS `session`(
	`id` 		  int 		auto_increment,
	`sid` 		  char(255) not null 	default '' comment 'session id',
	`user_id` 	  int 		not null 	default 0  comment '用户id',
	`expire_time` int 		not null 	default 0  comment '记录过期时间',
	`last_active` int 		not null 	default 0  comment '最后活跃时间 用来判断用户是否在线',
	`data` 		  text  comment 'session数据',
	primary key(`id`),
	index(`sid`),
	index(`user_id`)
	)auto_increment=1,charset=utf8,engine=innodb;