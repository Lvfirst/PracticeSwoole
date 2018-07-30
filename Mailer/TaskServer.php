<?php 

class TaskServer
{
	private $_serv;

	private $_run;

	public function __construct()
	{
		$this->_serv=new Swoole\Server('127.0.0.1',9501);
		$this->_serv->set(
			[
				'worker_num'=>2,
				'daemonize'=>false,
				'log_file'=>__DIR__.'/Server.log',
				'task_worker_num'=>2,
				'task_max_request'=>5000,
				'max_request'=>5000,
				'open_eof_check'=>true,
				'package_eof'=>"\r\n",
				'open_eof_split'=>true,
			]
		);
		// on Connect| Receive| WorkerStart |Task| Finish|close
		$this->_serv->on('Connect',[$this,'OnConnect']); 
		$this->_serv->on('Recveive',[$this,'OnReceive']);
		$this->_serv->on('WorkerStart',[$this,'OnWorkerStart']);
		$this->_serv->on('Task',[$this,'OnTask']);
		$this->_serv->on('Finish',[$this,'OnFinish']);
		$this->_serv->on('Close',[$this,'OnClose']);
	}

	public function OnConnect($serv,$fd,$fromId)
	{

	}

	public function OnReceive($serv,$fd,$fromId,$data)
	{
		$data=$this->unpack($data);
		$this->_run->receive($serv,$fd,$fromId,$data);
		// 投递任务
		if(!empty($data['event']))
		{
			$serv->task(array_merge($data,['fd'=>$fd]));
		}
	}

	public function OnWorkerStart($serv,$workerId)
	{
		require_once __DIR__.'/TaskRun.php';
		$this->_run=new TaskRun;
	}

	public function OnTask($serv,$taskId,$workerId,$data)
	{
		$this->_run->task($serv,$taskId,$workerId,$data);
	}

	public function OnFinish($serv,$taskId,$data)
	{
		$this->_run->finish($serv,$taskId,$data);
	}

	public function OnClose($serv,$fd,$fromId)
	{
		
	}
	/**
	 * [upack 要经过json_decode 只能是数组]
	 *
	 * @DateTime 2018-07-30
	 *
	 * @param    [type] $data
	 *
	 * @return   [type]
	 */
	public function upack($data)
	{
		$data=str_replace("\r\n",'',$data);
		if(!$data)
		{
			return false;
		}
		$data=json_decode($data,true);
		if(!$data || !is_array($data,true))
		{
			return false;
		}

		return $data;
	}

	public function start()
	{
		$this->_serv->start();
	}
}

$TaskServer=new TaskServer;
$TaskServer->start();