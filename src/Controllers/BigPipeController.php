<?php

namespace Shopex\LubanSite\Controllers;

use App\Http\Controllers\Controller;

class BigPipeController extends Controller
{

	private $firstChunkSended = false;

	private function start(){
		header('Transfer-Encoding: chunked');
		header('Content-Type: text/javascript;charset=UTF-8');
		header('Connection: keep-alive');
		header('Cache-Control: nocache');

		for ($i = 0; $i < ob_get_level(); $i++)  ob_end_clean();
	}

	private function flush($data){
		$chunk = json_encode($data);
		if($this->firstChunkSended){
			$chunk = "\n\n".$chunk;
		}else{
			$this->firstChunkSended = true;
		}
		printf("%x\r\n%s\r\n", strlen($chunk), $chunk);
		flush();
	}

	function fetch(){
		$this->start();

		for($i=0;$i<10;$i++){
			$this->flush([str_repeat('c', 1000).$i]);
			// sleep(1);
		}

		$this->end();
	}

	private function end(){
		echo "0\r\n\r\n";
		flush();
	}

}
