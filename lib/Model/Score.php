<?php
class Model_Score extends Model_Table {
	public $table = 'score';
	function init() {
		parent::init();

		$this->addField('team1_score')->type('int');
		$this->addField('team2_score')->type('int');
		$this->addField('created')->defaultValue(date('Y-m-d H:i:s'))->type('datetime')->system(true);
		$this->addField('updated')->type('datetime')->system(true);

		$this->hasMany('Game');
	}
}