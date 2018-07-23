<?php 
$serv=new swoole_server('127.0.0.1',9501);

$serv->set([
	'worker_num'=>1,
	'task_worker_num'=>1,
]);

$serv->on('Connect',function($serv,$fd){
});
$serv->on('Receive', function ($serv, $fd, $fromId, $data) {
});
$serv->on('Close', function ($serv, $fd) {
});
$serv->on('Task', function ($serv, $taskId, $fromId, $data) {
});
$serv->on('Finish', function ($serv, $taskId, $data) {
});



# 设置进程的名字  便于区分Master || Manager || Worker
#Master
$serv->on('start',function($serv){
	swoole_set_process_name('server-process: master');
});
#ManagerStart
$serv->on('ManagerStart',function($serv){
	swoole_set_process_name('server-process: manager');
});

# worker || task
$serv->on('WorkerStart',function($serv,$workerId){
	if($workerId >= $serv->setting['worker_num'])
	{
		swoole_set_process_name('server-process: task');
	}
	else
	{
		swoole_set_process_name('server-process: worker');
	}
});

$serv->start();

# ps aux | grep server-process
# 会出现一个master进程 一个Manager进程  剩下的根据set() 方法里定义的worker_num | task_num 来算 定义多少出现多少进程