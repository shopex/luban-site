<?php
namespace Shopex\LubanSite;
use Illuminate\Support\Facades\View;

class Widget {

	public $tagName = 'div';
	public $attributes = [];
	public $config = [];
	public $lazyLoad = true;
	public $dirpath = '';
	public $uniqid = '';

	public function render(){
		$vars = $this->vars();
		$html = $this->tagOpen($this->tagName);
		$html .= View::make('widgets/'.$this->name.'::main', $vars)->render();
		return $html.$this->tagClose($this->tagName);
	}

	public function setConfig($cfg){
		$this->config = $cfg;
	}

	public function vars(){
		return [];
	}

	public function tagOpen($tag, $attributes = []){
		$html = '<'.$tag;
		foreach($attributes as $k=>$v){
			$html .= ' '.$k.'="'.str_replace('"', '\\"', $v).'"';
		}
		return $html.'>';
	}

	public function tagClose($tag){
		return '</'.$tag.'>';
	}
}