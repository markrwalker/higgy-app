<?php
class Model_Vote extends Model_Table {
	public $table = 'vote';
	function init() {
		parent::init();

		$this->addField('player')->actual('name');
		$this->addField('gender')->enum(array('M','F'))->hidden(true);
		$this->addField('ip')->defaultValue($_SERVER['REMOTE_ADDR'])->hidden(true);
		$this->addField('created')->type('datetime')->system(true);
		$this->addExpression("votes", $this->dsql()
			->field($this->dsql()->expr("count(*)"), "id")
			->where("player", $this->getField("player"))
        );
	}
}