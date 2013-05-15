<?php
class Model_Division extends Model_Table {
	public $table = 'division';
	function init() {
		parent::init();

		$this->addField('name');
		$this->addField('created')->defaultValue(date('Y-m-d H:i:s'))->type('datetime')->system(true);
		$this->addField('updated')->type('datetime')->system(true);
	}
}