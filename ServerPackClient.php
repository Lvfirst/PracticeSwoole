<?php 
$client=new Swoole\Client(SWOOLE_SOCK_TCP,SWOOLE_SOCK_SYNC);
$client->connect('127.0.0.1',9501) || exit("connect failed {$client->errCode}");

// 向服务端发送pack后的事件
for($i=0;$i<3;$i++)
{
	$data='Just a test.';
	$data=pack('N',strlen($data)).$data;
	$data=$client->send($data);
}

$client->close();