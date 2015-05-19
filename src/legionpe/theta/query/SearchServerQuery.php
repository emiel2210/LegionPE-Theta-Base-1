<?php

namespace legionpe\theta\query;

use legionpe\theta\BasePlugin;

class SearchServerQuery extends AsyncQuery{
	/** @var int */
	public $class;
	public function __construct(BasePlugin $plugin, $class){
		parent::__construct($plugin);
		$this->class = $class;
	}
	public function getQuery(){
		return "SELECT ip,port FROM server_status WHERE unix_timestamp()-last_online < 5 AND class=$this->class ORDER BY online_players ASC LIMIT 1";
	}
	public function getResultType(){
		return self::TYPE_ASSOC;
	}
	public function getExpectedColumns(){
		return [
			"ip" => self::COL_STRING,
			"port" => self::COL_INT
		];
	}
}
