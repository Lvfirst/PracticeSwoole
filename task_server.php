<?php 
 $serv=new swoole_server('127.0.0.1',9501);

 // 设置开启多少个task进程
 $serv->set([
 	'task_worker_num'=>2,
 ]);

 // 有新连接监听
 $serv->on('Connect',function($serv,$fd){
 	echo 'new client connected'.PHP_EOL;
 });
// 监听接收的数据
$serv->on('Receive',function($serv,$fd,$fromId,$data){
	echo "worker received data:{$data}".PHP_EOL; 
	// 将任务投递到task中
	$serv->task($data);

	//通知客户端server收到数据了
	$serv->send($fd,'this is a message from server'.PHP_EOL);
	// 验证task 是否异步执行的，都输出点东西看看效果
	echo 'worker continue run. '.PHP_EOL;

});

/**
 * $serv swoole_server object
 * $taskId task进程是由worker进程发起的，worker过多可能值会相同
 * $fromId  来字那个workerId
 * $data  投递的数据
 */
$serv->on('Task',function($serv,$taskId,$fromId,$data){
	echo "task start. ---from worker id :{$fromId}".PHP_EOL;
	 for ($i=0; $i < 5; $i++) { 
        sleep(1);
        echo "task runing. --- {$i}" . PHP_EOL;
    }
    // echo "task end." . PHP_EOL;
    // $serv->finish("response"); // 只有再task任务下 调用才会调用finish
    // 也可以通过return的方式调用finsih
    return "task end." . PHP_EOL;

    # 只要出现return 或者 finish 就代表直接结束了
});


// 只有task进程中调用finish或者return 了结果，才会触发finish
$serv->on('Finish',function($serv,$taskId,$data){

	echo "finish received data '{$data}'".PHP_EOL;
});

$serv->start();