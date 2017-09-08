<?php
namespace Shopex\LubanSite\Traits;

use Illuminate\Support\Facades\Cache;

trait WidgetsStore{

	function storeWidgets($id, $ttl){
		$lazyWidgets = [];
		foreach($this->widgets_loaded as $widget){
			if($widget->lazyLoad){
				$lazyWidgets[$widget->id] = $widget;
			}
		}
		Cache::put('widgets-'.$id, $lazyWidgets, $ttl?$ttl:300);
	}

	function loadWidgets($id){
		return Cache::get('widgets-'.$id);
	}

}