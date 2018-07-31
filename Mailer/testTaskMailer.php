<?php 
$t1=microtime(true);
require_once __DIR__.'/SwooleMailer.php';

$data=[
	[
		'to'=>'534148647@qq.com',
		'subject'=>'One test email',
		'content'=>'This is just a test'
	],
	[
		'to'=>'534148647@qq.com',
		'subject'=>'Two test email',
		'content'=>'This is just a test'
	],	
	[
		'to'=>'534148647@qq.com',
		'subject'=>'Three test email',
		'content'=>'This is just a test'
	]	
];

$mailer=new Mailer;
foreach ($data as $value) {
	$res=$mailer->send($value);
}
$t2=microtime(true);
$time=$t2-$t1;
echo "time:".$time;
echo PHP_EOL;