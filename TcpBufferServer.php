<?php 

class TcpBufferServer
{
	private $serv;

	public function __construct()
	{
		$this->serv=new Swoole\Server("127.0.0.1",9501);
		$this->serv->set([
			'worker_num'=>1,
		]);
		$this->serv->on('Receive',[$this,'onReceive']);
	}

	public function onReceive($sevr,$fd,$fromId,$data)
	{
		echo "Server received data: {$data}".PHP_EOL;
	}

	public function start()
	{
		$this->serv->start();
	}
}

$reload=new TcpBufferServer;
$reload->start();