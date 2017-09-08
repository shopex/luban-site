<?php
namespace Shopex\LubanSite;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\HtmlString;
use Shopex\LubanSite\Traits\WidgetsStore;

class Site {

	use WidgetsStore;

	const HEADER_PLACER = '<!---PAGE-HEADER-PLACER--->';

	private $global_widgets_dir = 'Widgets';
	private $custom_widgets_dir = 'site/Widgets';
	private $widgets_loaded = [];
	private $widgets_reslover = [];
	private $async_fetch_id = '';
	private $widgets_loaded_cnt = 0;
	private $current_widget_id = 0;
	private $expired = 0;

	public function publicCacheTTL($ttl){
		$this->expired = time() + $ttl;
	}

	public function routes(){
		Route::any('site-widgets-fetch', ['uses' => 
			'\Shopex\LubanSite\Controllers\WidgetsController@fetch'])->name('site-widgets-fetch');
	}

	public function widget($name, $input){
		$obj = $this->loadWidget($name);
		$obj->setInput($input);

		if($obj->lazyLoad){
			$html = '<'.$obj->tagName.' widget-placeholder="'.$obj->id.'"></'.$obj->tagName.'>';
		}else{
			$html = $obj->render();
		}
		return new HtmlString($html);
	}

	public function header(){
		return new HtmlString(self::HEADER_PLACER);
	}

	public function footer(){
		$this->session = uniqid();
		$data = '<script id="widgets-js" data-widgets="'.$this->session.'">';
		$data .= "\n\$(function(){";
		foreach($this->widgets_reslover as $loadedType){
			$obj = $loadedType[3];
			if(!$obj->lazyLoad){
				$data .= $obj->js()."\n";
			}
		}
		$data .='})</script>';

		$this->storeWidgets($this->session, $this->expired);

		return new HtmlString($data);
	}

	public function widgetsResource($content){
		$data = '<style id="widgets-css">';
		foreach($this->widgets_reslover as $loadedType){
			$obj = $loadedType[3];
			if(!$obj->lazyLoad){
				$data .= $obj->css();
			}
		}
		$data .='</style>';
		return str_replace(self::HEADER_PLACER, $data, $content);
	}

	public function loadWidget($name){

		if(!isset($this->widgets_reslover[$name])){
			$namefixed = str_replace('/', '\\', $name);
			if(class_exists($class = $namefixed.'\\main', true)){
				$className = $class;
			}elseif(class_exists($class = __NAMESPACE__.'\\Widgets\\'.$namefixed.'\\main', true)){
				$className = $class;
			}else{
				throw new \Exception('Widget Not Found: "'.$name.'"');
			}

			$widget = new $className;
			$reflector = new \ReflectionClass($className);
			$fn = $reflector->getFileName();
			$widget->dirpath = dirname($fn);

			$this->widgets_reslover[$name] = [$className, $widget->dirpath, $this->widgets_loaded_cnt, &$widget];
			$this->widgets_loaded_cnt++;
		}else{
			$widget = new $this->widgets_reslover[$name][0];
			$widget->dirpath = $this->widgets_reslover[$name][1];
		}

		$widget->name = $name;
		$widget->id = $this->current_widget_id;
		$widget->type_id = $this->widgets_reslover[$name][2];

		$this->widgets_loaded[] = $widget;
		$this->current_widget_id++;
		return $widget;
	}

} 