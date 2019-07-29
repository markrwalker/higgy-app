<?php
class Model_Team extends Model_Table {
	public $table = 'team';
	function init() {
		parent::init();

		$year_id = $this->api->db->dsql()->table('year')->where('name',date('Y'))->field('id')->getOne();

		$this->addField('name')->sortable(true)->searchable(true);
		$this->addField('person1');
		$this->addField('person1_gender')->enum(array('M','F'));
		$this->addField('person2');
		$this->addField('person2_gender')->enum(array('M','F'));
		$this->addField('division_id')->refModel('Model_Division');
		$this->addField('year_id')->refModel('Model_Year')->defaultValue($year_id);
		$this->addField('protected')->type('boolean')->defaultValue(false);
		$this->addField('winner')->type('int')->defaultValue(0);
		$this->addField('checked_in')->type('boolean')->defaultValue(false);
		$this->addField('dropped_out')->type('boolean')->defaultValue(false);
		$this->addField('created')->defaultValue(date('Y-m-d H:i:s'))->type('datetime')->system(true);
		$this->addField('updated')->type('datetime')->system(true);
		// $this->addExpression('inplay1', $this->api->db->dsql()->table('team', 't')
		// 	->field('count(*)')
		// 	->where('id', $this->api->db->dsql()->table('game')->field('team1_id')->where($this->dsql()->andExpr()->where('is_complete', '0')->where('team1_id = team.id')))
		// );
		// $this->addExpression('inplay2', $this->api->db->dsql()->table('team', 't')
		// 	->field('count(*)')
		// 	->where('id', $this->api->db->dsql()->table('game')->field('team2_id')->where($this->dsql()->andExpr()->where('is_complete', '0')->where('team2_id = team.id')))
		// );
	}

	function checkinTeam($team_id) {
		$val = $this->load($team_id)->get('checked_in');
		$opp = !$val;
		$this->load($team_id)->set('checked_in', $opp)->save();
	}
}
