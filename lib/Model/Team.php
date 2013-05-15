<?php
class Model_Team extends Model_Table {
	public $table = 'team';
	function init() {
		parent::init();

		$this->addField('name');
		$this->addField('person1');
		$this->addField('person2');
		$this->hasOne('Division');
		$this->hasOne('Year');
		$this->addField('created')->defaultValue(date('Y-m-d H:i:s'))->type('datetime')->system(true);
		$this->addField('updated')->type('datetime')->system(true);
	}
}