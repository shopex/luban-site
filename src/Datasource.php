<?php
namespace Shopex\LubanSite;
use Shopex\LubanAdmin\Finder;
use Shopex\LubanSite\Finder\Column;
class Datasource extends Finder
{
	private $type;
	public function setTitle($title)
	{
		$this->_title = $title;
		return $this;
	}
	public function setType($type){
		$this->type = $type;
		return $this;
	}
	public function getType(){
		return $this->type;
	}
	public function addColumn($label, $key, $mapkey=null){
		$col = new Column($this);
		$col->label = $label;
		$col->key = $key;
		$col->mapkey = $mapkey;
		$this->_columns[] = $col;
		return $col;
	}
	
} // END class Datasource