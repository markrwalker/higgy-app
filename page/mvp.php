<?php
class page_mvp extends Page {
	function initMainPage() {
		parent::init();

		//$is_admin = $this->api->auth->model['is_admin'];
		/*if (!$is_admin) {
			$this->api->redirect('index');
		}*/

		/**** Column 1 ****/
		$columns = $this->add('Columns');
		$col1 = $columns->addColumn(5)->add('Frame');
		$m = $this->add('Model_Vote');
		$m->setOrder('votes','desc');
		$female_grid = $col1->add('Grid');
		$female_grid->setModel($m)->addCondition('gender','F');
		$female_grid->dq->group('name');

		$col2 = $columns->addColumn(5)->add('Frame');
		$m = $this->add('Model_Vote');
		$m->setOrder('votes','desc');
		$male_grid = $col2->add('Grid');
		$male_grid->setModel($m)->addCondition('gender','M');
		$male_grid->dq->group('name');

	}
}