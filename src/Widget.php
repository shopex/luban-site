<?php
namespace Shopex\LubanSite;

class Widget {

	public $tagName = 'div';
	public $attributes = [];
	public $config = [];
	public $loadMode = 'normal'; // normal | lazy

	public function output(){
		if(isset($this->attributes['class']) && isset($this->attributes['class'][0])){
			$this->attributes['class'] .= ' widget';
		}else{
			$this->attributes['class'] = 'widget';
		}

		$html = $this->tagOpen($this->tagName, $this->attributes);
		
		ob_start();
		$this->render();
		$html .= ob_get_contents();
		ob_end_clean();

		return $html .$this->tagClose($this->tagName);
	}

	public function setConfig($cfg){
		$this->config = $cfg;
	}

	public function render(){
		return 'empty render';
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