<?php 
$t1 = microtime(true);
include_once "TaskClient.php";
/*$data=[
	'event'=>TaskClient::EVENT_TYPE_SEND_MAIL,
	'to'=>'534148647@qq.com',
	'subject'=>'this is a message',
	'content'=>'this is a content',
];*/

$data=[
	[
		'event'=>TaskClient::EVENT_TYPE_SEND_MAIL,
		'to'=>'534148647@qq.com',
		'subject'=>'One test email',
		'content'=>'This is just a test'
	],
	[
		'event'=>TaskClient::EVENT_TYPE_SEND_MAIL,
		'to'=>'534148647@qq.com',
		'subject'=>'Two test email',
		'content'=>'This is just a test'
	],	
	[
		'event'=>TaskClient::EVENT_TYPE_SEND_MAIL,
		'to'=>'534148647@qq.com',
		'subject'=>'Three test email',
		'content'=>'This is just a test'
	]	
];


$client=new TaskClient();
foreach ($data as $value) {
	$res=$client->sendData($value);
}

$t2=microtime(true);
$time=$t2-$t1;
echo "time:".$time;
echo PHP_EOL;