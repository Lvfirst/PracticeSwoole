<?php 
require_once('./TaskClient.php');
require_once('./SwooleMailer.php');

class TaskRun
{
	public function receive($serv,$fd,$fromId,$data)
	{

	}

	public function task($serv,$taskId,$fromId,$data)
	{
		try {

			switch ($data['event']) {
				case TaskClient::EVENT_TYPE_SEND_MAIL:
					$mailer=new Mailer;
					$res=$mailer->send($data);
					# code...
					break;
				default:
					# code...
					break;
			}
			
			return $res;
			
		} catch (\Exception $e) {
			throw new \Exception("[{$e->getCode()}] Task Exception: {$e->getMessage()}")
		}
	}

	public function finish($serv, $taskId, $data)
    {
        return true;
    }
}