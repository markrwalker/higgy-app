<?php
class Model_Field extends Model_Table {
	public $table = 'field';
	function init() {
		parent::init();

		$this->addField('name');
		$this->addField('active')->type('boolean')->defaultValue(true);
		$this->addField('created')->defaultValue(date('Y-m-d H:i:s'))->type('datetime')->system(true);
		$this->addField('updated')->type('datetime')->system(true);

		$this->hasMany('Game');
	}
}