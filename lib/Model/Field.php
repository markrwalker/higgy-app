<?php
class Model_Field extends Model_Table {
	public $table = 'field';
	function init() {
		parent::init();

		$this->addField('name');
		$this->addField('created')->defaultValue(date('Y-m-d H:i:s'))->type('datetime');
		$this->addField('updated')->type('datetime');

		$this->hasMany('Game');
	}
}