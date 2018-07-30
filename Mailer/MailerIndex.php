<?php 
require_once('TaskClinet.php');

$data=[
	'event'=>TaskClient::EVENT_TYPE_SEND_MAIL,
	'to'=>'534148647@qq.com',
	'subject'=>'this is a message',
	'content'=>'this is a content',
];

$client=new TaskClient();

$client->sendData($data);