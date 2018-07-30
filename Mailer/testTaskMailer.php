<?php 
require_once __DIR__.'/SwooleMailer.php';

$data=[
	'to'=>'534148647@qq.com',
	'subject'=>'just a test email',
	'content'=>'This is just a test'
];

$mailer=new Mailer;
$res=$mailer->send($data);
var_dump($res);