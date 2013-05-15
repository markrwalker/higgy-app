<?php
class Model_Game extends Model_Table {
	public $table = 'game';
	function init() {
		parent::init();

		$this->hasMany('Team');
		$this->addField('team1_id')->refModel('Model_Team');
		$this->addField('team2_id')->refModel('Model_Team');
		$this->addField('field_id')->refModel('Model_Field');
		$this->addField('score_id')->refModel('Model_Score')->system(true);		
		$this->addField('is_complete')->type('boolean')->defaultValue(false);
		$this->addField('created')->defaultValue(date('Y-m-d H:i:s'))->type('datetime')->system(true);
		$this->addField('updated')->type('datetime')->system(true);
	}

	function finishGame() {
		$this->set('is_complete', true);
		$this->save();
	}
}