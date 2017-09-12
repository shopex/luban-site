<?php
namespace Shopex\LubanSite\Widgets\Shared\Block;

use Shopex\LubanSite\Widget;
use Shopex\Luban\Facades\Luban;

class Block extends Widget{

	// var $lazyLoad = false;
	// var $publicCache = true;

	function process($input){
		$this->vars = $input;
	}

}