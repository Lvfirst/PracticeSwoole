<?php 
require_once("Test.php");

class NoReload
{
	//swoole 对象容器
	private $_serv;
	// test 对象容器
	private $_test;

	public function __construct()
	{
		// swoole_server 等同于 Swoole\Server
		$this->_serv=new Swoole\Server('127.0.0.1','9501');
		// daemonize 在后台作为守护进程运行
		$this->_serv->set([
			'woker_num'=>1,
			
		]);
		$this->_serv->on('Receive',[$this,'onReceive']);
		$this->_test=new Test;
		// $this->_serv->on('WorkerStart',[$this,'onWorkerStart']);
	}

	public function start()
	{
		$this->_serv->start();
	}

	public function onReceive($serv,$fd,$fromId,$data)
	{
		$this->_test->run($data);
	}

	#解决之前加载test文件 通过 SIGUSR1 信号重启
	#预先加载的文件不产生效果的
	// public function onWorkerStart($serv,$fd,$fromId,$data)
	// {
	// 	require_once('Test.php');
	// 	$this->_test=new Test;
	// }
}

$noReload=new NoReload;
$noReload->start();

