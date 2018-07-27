<?php 
$client=new swoole_client(SWOOLE_SOCK_TCP,SWOOLE_SOCK_SYNC);

$client->connect('127.0.0.1',9501) || exit("connected failed ecode:{$client->errCode}");
for ($i = 0; $i < 200; $i++) { 

    $client->send("Just a test.\r\n"); 
}
$client->close();
# 发送的时候定义eof \r\n  不能使用单引号 ，单引号不转义