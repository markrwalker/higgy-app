<?php
class Model_Game extends Model_Table {
	public $table = 'game';
	function init() {
		parent::init();

		$this->hasMany('Team');
		$this->addField('team_id')->refModel('Model_Team');
		$this->hasMany('Score');
		$this->addField('score_id')->refModel('Model_Score');
		$this->hasOne('Field');
		$this->addField('is_complete')->type('boolean')->defaultValue(false);
		$this->addField('created')->defaultValue(date('Y-m-d H:i:s'))->type('datetime');
		$this->addField('updated')->type('datetime');
	}

	function finishGame() {
		$this->set('is_complete', true);
		$this->save();
	}
}