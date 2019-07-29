<?php
class Model_Game_Scores extends Model_Table {
	public $table = 'game_scores';
	function init() {
		parent::init();

		$this->addField('game_id');
		$this->addField('team1');
		$this->addField('team1_score');
		$this->addField('team2');
		$this->addField('team2_score');
		$this->addField('division_id')->refModel('Model_Division')->hidden(true);
		$this->addField('winner_id')->refModel('Model_Team');
	}
}