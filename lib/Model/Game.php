<?php
class Model_Game extends Model_Table {
	public $table = 'game';
	function init() {
		parent::init();

		$this->addField('team1_id')->refModel('Model_Team', 'id');
		$this->addField('team1_score_id')->refModel('Model_Score','id')->hidden(true);
		$this->addField('team2_id')->refModel('Model_Team', 'id');
		$this->addField('team2_score_id')->refModel('Model_Score','id')->hidden(true);
		$this->addField('field_id')->refModel('Model_Field');
		$this->addField('is_complete')->type('boolean')->defaultValue(false)->visible(false);
		$this->addField('created')->defaultValue(date('Y-m-d H:i:s'))->type('datetime')->system(true);
		$this->addField('updated')->type('datetime')->system(true);

		$this->addHook('beforeInsert',$this);
	}

	function beforeInsert($m,$q) {
		$team1_score_id = $this->api->db->dsql()
			->table('score')
			->set('score','0')
			->do_insert()
		;

		$team2_score_id = $this->api->db->dsql()
			->table('score')
			->set('score','0')
			->do_insert()
		;

		$q->set('team1_score_id',$team1_score_id);
		$q->set('team2_score_id',$team2_score_id);		
	}
}