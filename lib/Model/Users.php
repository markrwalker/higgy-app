<?php
class Model_Users extends Model_Table {
	public $table = 'users';
	function init() {
		parent::init();

		$this->addField('team_id')->refModel('Model_Team');
		$this->addField('password')->type('int');
		$this->addField('created')->defaultValue(date('Y-m-d H:i:s'))->type('datetime')->system(true);
		$this->addField('updated')->type('datetime')->system(true);
	}
}