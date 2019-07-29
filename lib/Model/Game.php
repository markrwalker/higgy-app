<?php
class Model_Game extends Model_Table {
	public $table = 'game';
	function init() {
		parent::init();

		$year_id = $this->api->db->dsql()->table('year')->where('current', '1')->field('id')->getOne();
		$team_list = $this->api->db->dsql()->table('team')->where('year_id', $year_id)->field('id')->get();

		$this->addField('team1_id')->refModel('Model_Team', 'id');
		$this->addField('team1_score_id')->refModel('Model_Score','id')->hidden(true);
		$this->addField('team2_id')->refModel('Model_Team', 'id');
		$this->addField('team2_score_id')->refModel('Model_Score','id')->hidden(true);
		$this->addField('field_id')->refModel('Model_Field');
		$this->addField('year_id')->refModel('Model_Year')->defaultValue($year_id)->hidden(true);
		$this->addField('round')->type('int');
		$this->addField('is_complete')->type('boolean')->defaultValue(false);
		$this->addField('created')->defaultValue(date('Y-m-d H:i:s'))->type('datetime')->system(true);
		$this->addField('updated')->type('datetime')->system(true);

		$this->addHook('beforeInsert',$this);
	}

	function beforeInsert($m,$q) {
		$team1_score_id = $this->api->db->dsql()
			->table('score')
			->set('score', null)
			->do_insert()
		;

		$team2_score_id = $this->api->db->dsql()
			->table('score')
			->set('score', null)
			->do_insert()
		;

		$q->set('team1_score_id',$team1_score_id);
		$q->set('team2_score_id',$team2_score_id);		
	}
}