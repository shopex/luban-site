<?php
namespace Shopex\LubanSite;
use Shopex\LubanAdmin\Finder;
use Shopex\LubanSite\Finder\Column;
use Illuminate\Http\Request;
use Shopex\LubanAdmin\Finder\Search;
class Datasource extends Finder
{
	private $type;
	private $_offset;
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
	public function setSort($id=0)
	{
		$this->_current_sort_id = $id;
		return $this;
	}
	public function setSearch($filters)
	{
		if (!$filters) {
			return $this;
		}
		if (is_array($filters)) {
			$filters = json_encode($filters);
		}
		
		$this->_filters = Search::parse_filters($this->_searchs, $filters);
		return $this;
	}
	public function setOffset($offset)
	{
		$this->_offset = $offset;
		return $this;
	}
	public function getList(){
		$cols = [];
		$items = [];
		foreach($this->_columns as $col){
			if($col->key){
				$cols[] = $col->key;
			}
		}

		$cols[] = $this->_id_column;

		$query = call_user_func_array([$this->model, 'select'], $cols);
		if(isset($this->_sorts[$this->_current_sort_id])){
			$query = call_user_func_array([$query, 'orderBy'], 
				$this->_sorts[$this->_current_sort_id]->orderBy);
		}
		if(isset($this->_tabs[$this->_current_tab_id])){
			foreach($this->_tabs[$this->_current_tab_id]->filters as $filter){
				$query = call_user_func_array([$query, 'where'], $filter);
			}
		}
		if(isset($this->_filters[0])){
			foreach($this->_filters as $filter){
				$query = call_user_func_array([$query, 'where'], $filter);
			}
		}

		$results = $query->offset($this->_offset)->limit($this->_pagenum)->get();	
		
		return $results;
	}
	public function dataGet(){
		$data = $this->getList()->toArray();
		$item = $items= [];
		foreach($data as $row){
			foreach($this->_columns as $i=>$col){
				$item[$col->mapkey] = $col->key?$row[$col->key]:'';
				if($col->modifier){
					$item[$mapkey] = call_user_func_array($col->modifier, [$item[$mapkey], $row]);
				}
			}
			$items[] = $item;
		}
		return $items;
	}



} // END class Datasource