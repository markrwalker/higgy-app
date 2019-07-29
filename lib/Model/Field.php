<?php
class Model_Field extends Model_Table {
	public $table = 'field';
	function init() {
		parent::init();

		$this->addField('name');
		$this->addField('active')->type('boolean')->defaultValue(true);
		$this->addField('created')->defaultValue(date('Y-m-d H:i:s'))->type('datetime')->system(true);
		$this->addField('updated')->type('datetime')->system(true);
		$this->addExpression('inuse', $this->api->db->dsql()->table('field', 'f')
			->field('count(*)')
			->where('id', $this->api->db->dsql()->table('game')->field('field_id')->where($this->dsql()->andExpr()->where('is_complete', '0')->where('field_id = field.id')))
		);

		$this->hasMany('Game');
	}
}