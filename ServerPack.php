<?php
class ServerPack
{
	private $_serv;
	public function __construct()
	{
		$this->_serv=new Swoole\Server('127.0.0.1',9501);
		$this->_serv->set([
			'worker_num'=>1,
			'open_length_check'=>true, //开启协议解析
			'package_length_type'=>'N', //长度字段类型
			'package_length_offset'=>0, // 第几个字节是包的长度的值
			'package_body_offset'=>4, //第几个字节开始计算长度
			'package_max_length'=>81920,
		]);

		$this->_serv->on('start',[$this,'onStart']);
		$this->_serv->on('Receive',[$this,'onReceive']);
	}

	public function onStart($serv)
	{
		echo "Server is start on 127.0.0.1:9501\n";
	}

	public function onReceive($serv,$fd,$fromId,$data)
	{
		$info=unpack('N',$data);
		$len=$info[1];
		$body=substr($data,-$len);
		echo "server data is {$body}\n";
	}
	
	public function start()
	{
		$this->_serv->start();
	}
}


$serverPack=new ServerPack;
$serverPack->start();