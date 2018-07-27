<?php

/*swoole_timer_tick('1000',function(){
	echo "this is a tick";
	echo PHP_EOL;
});*/

/*$i=0;
swoole_timer_tick('1000',function($timeId,$params) use (&$i){
	$i++;
	echo "hello,{$params},i:{$i},timeId{$timeId}";
	echo PHP_EOL;
	if($i>5)
	{
		swoole_timer_clear($timeId);
	}
},'world');*/

# swoole_timer_tick 的第三个参数是传递到 他第二个回调函数的第二个参数
# world==$params
# 
/*$serv=new Swoole\Server('127.0.0.1','9501');
$serv->set(
	[
		'worker_num'=>2,
	]
);
$serv->on('Receive',function($serv,$fd,$fromId,$data){

});
$serv->on('WorkerStart',function($serv,$workerId){
	if($workerId==0)
	{
		$i=0;
		$params='world';
		$serv->tick(1000,function($timeId) use($serv,&$i,$params,$workerId){
			$i++;
			echo "hello,{$params}--{$i}";
			echo PHP_EOL;
			if($i>3)
			{
				// 清除定时器
				$serv->clearTimer($timeId);
			}
		});
	}
	// $serv->close();
});
$serv->start();*/

####分割线，上面永久定时器，下面一次性的

// swoole_timer_after(3000,function(){
// 	echo "TimeOut";
// 	echo PHP_EOL;
// });
$serv=new Swoole\Server('127.0.0.1','9501');
$serv->set(
	[
		'worker_num'=>2,
	]
);
$serv->on('Receive',function($serv,$fd,$fromId,$data){
	$serv->after(3000,function(){
		echo "only once";
	});
});

$serv->on('Close', function ($serv, $fd) {
});

$serv->start();