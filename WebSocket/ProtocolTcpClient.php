<?php 
// 绑定另一个端口的文件
class Client
{
	private $client;

	public function __construct()
	{
		$this->client=new Swoole\Client(SWOOLE_SOCK_TCP);

		if(!$this->client->connect('127.0.0.1',9502))
		{
			$msg = 'swoole client connect failed.';
            throw new \Exception("Error: {$msg}.");
		}


	}


	public function sendData ($data)
    {
        $data = $this->togetherDataByEof($data);
        $this->client->send($data);
    }

    /**
     * 数据末尾拼接EOF标记
     * @param Array $data 要处理的数据
     * @return String json_encode($data) . EOF
     */
    public function togetherDataByEof($data)
    {
        if (!is_array($data)) {
            return false;
        }

        return json_encode($data) . "\r\n";
    }
}

$client=new Client;
$client->sendData([
	'event'=>'alertTip',
	'toUid'=>100,
]);