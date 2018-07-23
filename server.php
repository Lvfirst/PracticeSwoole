<?php
$serv=new swoole_server('127.0.0.1',9501);

//设置进程数量 
$serv->set([
    'worker_num'=>2,
]);
// $serv swoole 对象
// $fd 用于区分不同的客户端, 参数1-1600 范围内 
// 监听连接的  如果有新的连接就被监听到
$serv->on('Connect',function($serv,$fd){
    echo 'new Client connected'.PHP_EOL;
});
// 接受数据
// $fromId  线程的Id
// $data  收到的数据(字符串|二进制)
$serv->on('Receive',function($serv,$fd,$fromId,$data){
   $serv->send($fd,'Server_'.$data);  
});
//关闭 断开连接
$serv->on('Close',function($serv,$fd){
    echo 'Client close'.PHP_EOL;
});

$serv->start();
