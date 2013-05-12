<?php
class Model_Score extends Model_Table {
	public $table = 'score';
	function init() {
		parent::init();

		$this->addField('score')->type('int');
		$this->addField('created')->defaultValue(date('Y-m-d H:i:s'))->type('datetime');
		$this->addField('updated')->type('datetime');

		$this->hasMany('Game');
	}
}