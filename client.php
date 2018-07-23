<?php 

/**
 * SWOOLE_SOCK_TCP
 * SWOOLE_SOCK_TCP6
 * SWOOLE_SOCK_UDP
 * SWOOLE_SOCK_UDP6
 * 同步|异步
 * SWOOLE_SOCK_SYNC
 * SWOOLE_SOCK_ASYNC
 */

# 异步非阻塞客户端
$client=new swoole_client(SWOOLE_SOCK_TCP,SWOOLE_SOCK_SYNC);
// 创建连接
$client->connect('127.0.0.1',9501) || exit("connect failed Error:{$client->errCode}");
// 向服务端发送数据
$client->send("hello server.");

//服务端接受数据
$response=$client->recv();
echo $response;

//关闭客户端连接
$client->close();

