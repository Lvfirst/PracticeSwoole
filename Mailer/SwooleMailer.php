<?php 
require_once __DIR__.'/vendor/autoload.php';

class Mailer
{
	public $transport;

	public $mailer;

	public function send($data)
	{
		// 配置邮箱服务
		$this->transport=(new Swift_SmtpTransport('smtp.qq.com','465'))
			->setEncryption('ssl')
			->setUsername('1655585137@qq.com')
			->setPassword('kbbbqznatpsyebgd');

		$this->mailer=new Swift_Mailer($this->transport);

		//发送邮件内容
		$message=(new Swift_Message($data['subject']))
				->setFrom(['1655585137@qq.com'=>'lvzhiwei'])
				->setTo([$data['to']])
				->setBody($data['content']);

		$res=$this->mailer->send($message);
		$this->destory();

		return $res;
	}

	public function destory()
	{
		$this->transport=null;
		$this->mailer=null;
	}
}