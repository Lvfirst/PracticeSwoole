<?php 

class CommentServer
{
	private $_serv;
	private $_tcp;
	public $key = '^manks.top&swoole$';

	public $user2fd=[];

	public function __construct()
	{
		// 初始化服务对象
		$this->_serv=new swoole_websocket_server('0.0.0.0',9501);
		// 设置监听另一个端口  9502
		$this->_tcp=$this->_serv->listen('127.0.0.1',9502,SWOOLE_SOCK_TCP);
		$this->_tcp->set([
		    'open_eof_check' => true, //打开EOF检测
		    'package_eof' => "\r\n", //设置EOF
		    'open_eof_split' => true, // 自动分包
		]);
		// 设定要监听的回调函数
		$this->_tcp->on('Receive',[$this,'OnReceive']);
		// 设定参数
		$this->_serv->set([
            'worker_num' => 1,
            'heartbeat_check_interval' => 60,
            'heartbeat_idle_time' => 125,
        ]);

		 //监听事件 
        $this->_serv->on('open', [$this, 'onOpen']);
        $this->_serv->on('message', [$this, 'onMessage']);
        $this->_serv->on('close', [$this, 'onClose']);
	}


	public function onOpen($serv,$request)
	{
		$accessResult=$this->checkAccess($serv,$request);
		if(!$accessResult)
		{
			return false;
		}

		// 判断fd是否存在 存在则更新了 把旧的fd释放掉
		if(array_key_exists($request->get['uid'], $this->user2fd))
		{
			// 获取旧的 fd ,取出存储在数组中的fd
			$existsFd=$this->user2fd[$request->get['uid']];
			// 释放掉fd  与此同时关闭 uid=>fd  的关联
			$this->close($existsFd,'uid exist');
			// 更新新的fd 
			$this->user2fd[$request->get['uid']]=$request->fd;
			return false;
		}
		else
		{
			// 没有则压入 这个
			$this->user2fd[$request->get['uid']]=$request->fd;
		}
	}



	public function onMessage($srev,$frame)
	{
		$data=$frame->data;
		// 处理json数据
		$data=json_decode($data,true);

		// 判断是否存在  数据  以及传递过来的事件标识
		if(!$data || !is_array($data) || empty($data['event']))
		{
			// 关闭标识
			$this->close($frame->fd,'data format invalidate');
			return false;
		}
		
		// 如果传递过来事件 event
		$method=$data['event'];

		// 查看他是否存在
		if(!method_exists($this, $method))
		{
			$this->close($frame->fd,'method not exists');
			return false;
		}

		$this->$method($frame->fd,$data);

		// 只要判断不存在的  全部返回false,中断这个事件
	}

	public function alertTip($fd,$data)
	{
		// 这里实现发送到谁那里
		if(empty($data['toUid']) || !array_key_exists($data['toUid'], $this->user2fd))
		{
			$this->close($fd,'user not exists');
			return false;

		}
		// 存在就是推送给那个 客服端  通过fd
		$this->push($this->user2fd[$data['toUid']],['event'=>$data['event'],'msg'=>"this is a message"]);
	}
	//消息推送到客户端
	public function push($fd,$message)
	{
		if(!is_array($message))
		{
			$message=[$message];
		}
		$msg=json_encode($message);

		if($this->_serv->push($fd,$msg)==false)
		{
			$this->close($fd);
		}
	}

	public function close($fd,$msg='')
	{
		$this->_serv->close($fd);
		// 解除数组的关联
		if($uid=array_search($fd,$this->user2fd))
		{
			unset($this->user2fd[$uid]);
		}
	}
	// 实现监听的另一个端口的回调方法
	public function OnReceive($serv,$fd,$fromId,$data)
	{
		try {
				
			$data=json_decode($data,true);
			// 这里还是判断event是否存在
			if(!isset($data['event']))
			{
				throw new \Exception('params error ,needs',1);
			}

			$method=$data['event'];
			if(!method_exists($this, $method)) {
				throw new \Exception("params error, not support method.", 1);
			}
			$this->$method($fd, $data);
			return true;	

		} catch (\Exception $e) {
			$msge=$e->getMessage();			
			throw new \Exception("{$msg}",1);
		}
	}

	public function onClose($serv,$fd)
	{
		echo "client {$fd} closed\n";
	}


	public function checkAccess($serv, $request)
    {
        // get不存在或者uid和token有一项不存在，关闭当前连接
        if (!isset($request->get) || !isset($request->get['uid']) || !isset($request->get['token'])) {
            $this->close($request->fd, 'access faild.');
            return false;
        }
        $uid = $request->get['uid'];
        $token = $request->get['token'];

        // 校验token是否正确,无效关闭连接
        if (md5(md5($uid) . $this->key) != $token) {
            $this->close($request->fd, 'token invalidate.');
            return false;
        }

        return true;
    }

    public function start()
    {
    	$this->_serv->start();
    }
}

$server=new CommentServer;
$server->start();