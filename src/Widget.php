<?php
namespace Shopex\LubanSite;
use Illuminate\Support\Facades\View;
use Leafo\ScssPhp\Compiler as SassCompiler;

class Widget {

	public $tagName = 'div';
	public $attributes = [];
	public $input = [];
	public $lazyLoad = true;
	public $dirpath = '';
	public $uniqid = '';
	public $id;
	public $type_id;
	public $name;
	public $vars = [];

	public function run(){
		$ret = $this->process($this->input);
		if($ret instanceof \Generator){
			while($ret->valid()){
				$ret->current();
				$ret->next();
			}
		}
	}

	public function async_run(){
		return $this->process($this->input);
	}

	public function html(){
		$viewFinder = View::getFinder();
		$viewFinder->addNamespace('widgets/'.$this->name, $this->dirpath);
		$html = $this->tagOpen($this->tagName);
		$html .= View::make('widgets/'.$this->name.'::widget', $this->vars)->render();
		$html .= $this->tagClose($this->tagName);
		return $html;
	}

	public function js(){
		if(!file_exists($this->dirpath.'/widget.js')){
			return '';
		}
		return '$.widgets(".widget-type-'.$this->type_id.'",'.file_get_contents($this->dirpath.'/widget.js').')';
	}

	public function css(){
		if(!file_exists($this->dirpath.'/widget.scss')){
			return '';
		}
		$scss_compiler = new SassCompiler;
		$scss_compiler->setImportPaths([base_path().'/resources/assets/sass', $this->dirpath]);
		$string_sass = '@import "variables";';
		$string_sass .= '.widget-type-'.$this->type_id.'{';
		$string_sass .= file_get_contents($this->dirpath.'/widget.scss');
		$string_sass .= '}';
		$string_css = $scss_compiler->compile($string_sass);
		return $string_css;
	}

	public function setInput($input){
		$this->input = $input;
	}

	public function process($input){
		return [];
	}

	public function tagOpen($tag, $attributes = []){
		$html = '<'.$tag;
		$domClass = 'widget-type-'.$this->type_id.' widget-id-'.$this->id;
		$attributes['class'] = isset($attributes['class']) ? ($attributes['class'].' '.$domClass) : $domClass;

		foreach($attributes as $k=>$v){
			$html .= ' '.$k.'="'.str_replace('"', '\\"', $v).'"';
		}
		return $html.'>';
	}

	public function tagClose($tag){
		return '</'.$tag.'>';
	}
}