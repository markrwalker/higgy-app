<?php
class Model_Team extends Model_Table {
	public $table = 'team';
	function init() {
		parent::init();

		$this->addField('name')->sortable(true)->searchable(true);
		$this->addField('person1');
		$this->addField('person2');
		$this->addField('division_id')->refModel('Model_Division');
		$this->addField('year_id')->refModel('Model_Year');
		$this->addField('checked_in')->type('boolean')->defaultValue(false);
		$this->addField('created')->defaultValue(date('Y-m-d H:i:s'))->type('datetime')->system(true);
		$this->addField('updated')->type('datetime')->system(true);
	}
}