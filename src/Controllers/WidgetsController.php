<?php

namespace Shopex\LubanSite\Controllers;

use App\Http\Controllers\Controller;
use Shopex\LubanSite\Traits\BigPipe;
use Shopex\LubanSite\Traits\WidgetsStore;
use Illuminate\Http\Request;
use Shopex\LubanSite\Coroutine\Scheduler;
// use Hprose\Promise;

class WidgetsController extends Controller
{

	use BigPipe;
	use WidgetsStore;

	private $proccedWidgets = [];

	function fetch(Request $request){
		$this->start();
		$widgets = $this->loadWidgets($request->get('widgets'));

		$Scheduler = new Scheduler;
		foreach($widgets as $obj){
			$Scheduler->createTask( $this->runner($obj) );
		}
		$Scheduler->run();
		$this->end();
	}

	function runner($obj){
		try{

			ob_start();
			$process = $obj->process($obj->input);

			if($process instanceof \Generator){
				while($process->valid()){
					yield $process->current();
					$process->next();
				}
			}

			$html = $obj->html();

			$data = [
				'id'=>$obj->id,
				'html'=> $html,
				'error'=>ob_get_contents(),
			];
			ob_end_clean();

			if(!isset($this->proccedWidgets[$obj->name])){
				$data['css'] = $obj->css();
				$data['js'] = $obj->js();
				$this->proccedWidgets[$obj->name] = true;
			}else{
				$id = '.widget-id-'.$obj->id;
				$type = '.widget-type-'.$obj->type_id;
				$data['js'] = "\$.widgets.apply('{$id}','{$type}')";
			}

			$this->flush($data);
		}catch(\Exception $e){
			$data = [
				'error' => $e->getMessage()
			];
			$this->flush($data);	
		}
	}

}
