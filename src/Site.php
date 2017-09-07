<?php
namespace Shopex\LubanSite;

use Illuminate\Support\Facades\Route;

class Site {

	private $global_widgets_dir = 'Widgets';
	private $custom_widgets_dir = 'site/Widgets';
	private $loaded_widgets_paths = [];
	private $loaded_widgets = [];

	public function routes(){
		Route::any('site-datasets/{id}', ['uses' => 
			'\Shopex\LubanSite\Controllers\WidgetController@fetch'])->as('site-datasets');
	}

	public function widget($name, $config){
		$obj = $this->load($name);
		$obj->setConfig($config);
		$this->loaded_widgets[] = $obj;
		echo $obj->output();
	}

	public function load($name){

		if(class_exists($class = $name.'\\main', true)){
			$className = $class;
		}elseif(class_exists($class = __NAMESPACE__.'\\Widgets\\'.$name.'\\main', true)){
			$className = $class;
		}else{
			throw new \Exception('Widget Not Found: "'.$name.'"');
		}

		// if(file_exists($this->custom_widgets_dir.'/'.$name.'/main.php')){
		// 	$path = $this->custom_widgets_dir.'/'.$name.'/main.php';
		// }elseif(file_exists($this->global_widgets_dir.'/'.$name.'/main.php')){
		// 	$path = $this->global_widgets_dir.'/'.$name.'/main.php';
		// }else{
		// 	return false;
		// }

		// include($path);

		// $this->loaded_widgets_paths[$name] = $path;
		// $className = 'Widgets\\'.str_replace('/', '\\', $name);

		return new $className;
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