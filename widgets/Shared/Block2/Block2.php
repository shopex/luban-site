<?php
namespace Shopex\LubanSite\Widgets\Shared\Block2;

use Shopex\LubanSite\Widget;
use Shopex\Luban\Facades\Luban;

class Block2 extends Widget{

	// var $lazyLoad = false;
	// var $publicCache = true;

	function process($input){
		$this->vars = $input;
	}

}