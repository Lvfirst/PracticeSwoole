<?php 

class WebSocketServerValid
{
	private $_serv;
	public $key="^manks.top&swoole$";

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

	public function onOpen($serv,$requset)
	{
		// 实现验证权限的操作
		$this->checkAccess($serv,$requset);
	}


	public function onMessage($serv,$frame)
	{

		$serv->push($frame->fd,"server received data:{$frame->data}");
	}

	public function onClose($serv,$fd)
	{
		echo "client {$fd} closed\n";
	}

	public function checkAccess($serv,$requset)
	{

		# 变量写错了 找了半天错误........
		if(!isset($requset->get) || !isset($requset->get['uid']) || !isset($requset->get['token']))
		{
			//如果检测无效关闭连接
		
			$this->_serv->close($requset->fd);
			return false;

		}
		$uid=$requset->get['uid'];
	
		$token=$requset->get['token'];
		if(md5(md5($uid).$this->key)!==$token)
		{
			echo 2;
			$this->_serv->close($requset->fd);
			return false;
		}
	}

	public function start()
	{
		$this->_serv->start();
	}
}

$server=new WebSocketServerValid;
$server->start();

