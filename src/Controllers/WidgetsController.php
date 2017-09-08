<?php

namespace Shopex\LubanSite\Controllers;

use App\Http\Controllers\Controller;
use Shopex\LubanSite\Traits\BigPipe;
use Shopex\LubanSite\Traits\WidgetsStore;
use Illuminate\Http\Request;
use Hprose\Promise;

class WidgetsController extends Controller
{

	use BigPipe;
	use WidgetsStore;

	private $proccedWidgets = [];

	function fetch(Request $request){
		$this->start();		
		$widgets = $this->loadWidgets($request->get('widgets'));
		$a1 = Promise\value($widgets);
		$a1->each([$this, 'runner']);
		$this->end();
	}

	function runner($obj){
		try{
			$data = [
				'id'=>$obj->id,
				'html'=> $obj->render(),
			];

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
				'error' => $e
			];
			$this->flush($data);			
		}
	}

}
