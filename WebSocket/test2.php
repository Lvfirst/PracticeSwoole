<?php 

class CommentServer
{
	private $_serv;
	public $key = '^manks.top&swoole$';

	public $user2fd=[];

	public function __construct()
	{
		// 初始化服务对象
		$this->_serv=new swoole_websocket_server('0.0.0.0',9501);
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
		// 权限验证
		$accessResult=$this->checkAccess($serv,$request);
		if(!$accessResult)
		{
			return false;
		}

		if(array_key_exists($request->get['uid'],$this->user2fd))
		{
			// 存在则更新最新的连接
			$existFd=$this->user2fd[$request->get['uid']];
			$this->close($existFd,'uid exists');
			$this->user2fd[$request->get['uid']]=$request->fd;
			return false;
		}
		else
		{
			// 不存在则压入进去
			$this->user2fd[$request->get['uid']]=$request->fd;
		}

	}


	public function onMessage($serv,$frame)
	{
		// 接收数据
		$data=$frame->data;
		$data=json_decode($data,true);
		// 判断数据格式不对就直接关闭
		if(!$data || !is_array($data) || empty($data['event']))
		{
			$this->close($frame->fd,'data fromat invalidate!');
			return false;
		}

		// 获取传递过来的method
		$method=$data['event'];
		// 判断method是否存在
		if(!method_exists($this,$method))
		{
			$this->close($frame->fd,'event is not exists');
			return false;
		}
		#存在调用方法
		$this->$method($frame->fd,$data);
	}

	public function onClose($serv,$fd)
	{
		echo "client {$fd} closed \n";
	}

	//用于关闭连接 顺路删除这个用户
	public function close($fd,$msg='')
	{
		$this->_serv->close($fd);
		if($uid=array_search($fd,$this->user2fd))
		{
			unset($this->user2fd[$uid]);
		}
	}

	//给用户弹出消息显示
	public function alertTip($fd,$data)
	{
		// 判断是否存在uid  因为拿uid做的键
		if(empty($data['toUid']) || !array_key_exists($data['toUid'], $this->user2fd))
		{
			$this->close($fd);
			return false;
		}
		$this->push($this->user2fd[$data['toUid']],['event'=>$data['event'],'msg'=>'new message']);
	}

	//消息最后返回客户端
	public function push($fd,$message)
	{
		if(!is_array($message))
		{
			$message=[$message];

		}

		// 转换成json
		$msg=json_encode($message);
		// 如果传送失败了
		if($this->_serv->push($fd,$msg)==false)
		{
			$this->close($fd);
		}
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