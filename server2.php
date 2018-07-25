<?php 
$serv=new swoole_server('127.0.0.1','9501');

/**
 * max_request 和 task_max_request 
 * 限制进程最大数量，如果超出指定数量 swoole会自动释放
 * max_request 解决PHP进程内存溢出问题。
 * 
 */
$serv->set(
	[
		'worker_num'=>1,
		'task_worker_num'=>1,
		'max_request'=>3,
		'task_max_request'=>4,
	]
);

$serv->on('Connect',function($serv,$fd){

});

$serv->on('Receive',function($serv,$fd,$fromId,$data){
	$serv->task($data);
});

$serv->on('Task',function($serv,$taskId,$fromId,$data){
	// echo 'this message is '.$data;
});

$serv->on('Finish',function($serv,$taskId,$data){

});

$serv->on('Close',function($serv,$fd){

});

$serv->start();

# max_request 只能用同步、无状态请求的响应式服务
# 减少常驻内存的开销,解决内存溢出