<?php 
class Test
{
	public function run($data)
	{
		// echo $data;
		$data=json_decode($data,true);
		if(!is_array($data))
		{
			echo "server receive \$data fromat error!";
			return ;
		}
		var_dump($data);
	}
}