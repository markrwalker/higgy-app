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

	function setCurrentYear($year_id) {
		if (!empty($year_id)) {
			$q = $this->api->db->dsql();
			$q->table('year')->where('current', 1)->field('id');
			$old_year_id = $q->getOne();

			$this->load($old_year_id)->set('current', 0)->save();
			$this->load($year_id)->set('current', 1)->save();
		}
	}
}