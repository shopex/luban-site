<?php
namespace Shopex\LubanSite;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\HtmlString;

class Site {

	private $global_widgets_dir = 'Widgets';
	private $custom_widgets_dir = 'site/Widgets';
	private $widgets_loaded = [];
	private $widgets_reslover = [];

	public function routes(){
		Route::any('site-bigpipe', ['uses' => 
			'\Shopex\LubanSite\Controllers\BigPipeController@fetch'])->name('site-bigpipe');
	}

	public function widget($name, $config){
		$obj = $this->load($name);
		$obj->setConfig($config);

		if($obj->lazyLoad){
			$obj->uniqid = uniqid();
			$html = '<'.$obj->tagName.' widget="'.$obj->uniqid.'"></'.$obj->tagName.'>';
		}else{
			$html = $obj->render();
		}
		return new HtmlString($html);
	}

	public function load($name){

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

			$viewFinder = View::getFinder();
			$viewFinder->addNamespace('widgets/'.$name, $widget->dirpath);
			$this->widgets_reslover[$name] = [$className, $widget->dirpath];
		}else{
			$widget = new $this->widgets_reslover[$name][0];
			$widget->dirpath = $this->widgets_reslover[$name][1];
		}

		$widget->name = $name;
		$this->widgets_loaded[] = $widget;

		return $widget;
	}

	public function render($view){
		$output = $this->fetch($view);
		echo $output;
	}

	private function fetch($view){
		ob_start();
		$View = $this;		
		include $view;
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}

} 