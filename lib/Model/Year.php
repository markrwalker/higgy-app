<?php
class Model_Year extends Model_Table {
	public $table = 'year';
	function init() {
		parent::init();

		$this->addField('name');
		$this->addField('current')->type('boolean')->defaultValue(false);
		$this->addField('created')->defaultValue(date('Y-m-d H:i:s'))->type('datetime')->system(true);
		$this->addField('updated')->type('datetime')->system(true);

		$this->hasMany('Team');
	}
}