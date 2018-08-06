<?php 

class WebSocketServer
{
	private $_serv;


	public function __construct()
	{
		$this->_serv=new swoole_websocket_server('0.0.0.0',9501);	
		$this->_serv->set([
			'worker_num'=>1,
			'heartbeat_check_interval'=>10,
			'heartbeat_idle_time'=>30,
		]);

		$this->_serv->on('open',[$this,'onOpen']);
		$this->_serv->on('message',[$this,'onMessage']);
		$this->_serv->on('close',[$this,'onClose']);
	}

	public function onOpen($serv,$request)
	{
		echo "server: handshake success with fd{$request->fd}.\n";
	}


	public function onMessage($serv,$frame)
	{

		// push 的时候必须有  fd ...
		
		$serv->push($frame->fd,"server received data:{$frame->data}");
		// foreach ($serv->connections as $fd) {
		// 	$serv->push($fd,$frame->data);
		// }
	}

	public function onClose($serv,$fd)
	{
		echo "client {$fd} closed\n";
	}

	public function start()
	{
		$this->_serv->start();
	}
}

$server=new WebSocketServer;
$server->start();