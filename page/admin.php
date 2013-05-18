<?php
class page_admin extends Page {
	function initMainPage(){
		parent::init();

		//$is_admin = $this->api->auth->model['is_admin'];
		/*if (!$is_admin) {
			$this->api->redirect('index');
		}*/

		$columns = $this->add('Columns');
		$col1 = $columns->addColumn(9)->add('Frame');

		$tabs = $col1->add('Tabs');

		$tab = $tabs->addTab('Game Admin');
		$adminTabs = $tab->add('Tabs');
		$fields = $this->add('Model_Field');
		foreach ($fields as $f) {
			$tab = $adminTabs->addTab($f['name']);
			$m = $this->add('Model_Game');
			$crud = $tab->add('CRUD',array('allow_edit'=>false));
			$crud->setModel($m)->addCondition('field_id',$f['id'])->addCondition('is_complete',false);
			$games = $this->add('Model_Game')->addCondition('field_id',$f['id']);
			foreach ($games as $g) {
				if ($g['is_complete'] == 0) {
					$crud->add_button->js(true)->hide(true);
				}
			}
			if ($crud->grid) {
				$crud->grid->addColumn('expander','enter_score');
			}
			if ($crud->isEditing('add')) {
				$crud->form->getElement('field_id')->set($f['id'])->js(true);
			}
		}
		$tab = $adminTabs->addTab('Completed Games');
		$m = $this->add('Model_Game');
		$crud = $tab->add('CRUD',array('allow_edit'=>false));
		$crud->setModel($m)->addCondition('is_complete',true);
		$crud->add_button->js(true)->hide(true);
		if ($crud->grid) {
			$crud->grid->addColumn('expander','enter_score');
		}

		$tab = $tabs->addTab('Team Admin');
		$tab->add('CRUD')->setModel('Team');

		$tab = $tabs->addTab('Division Admin');
		$tab->add('CRUD')->setModel('Division');

		$tab = $tabs->addTab('Field Admin');
		$tab->add('CRUD')->setModel('Field');

		$tab = $tabs->addTab('Year Admin');
		$tab->add('CRUD')->setModel('Year');

		$col2 = $columns->addColumn(3)->add('Frame')->setTitle('Field Status');
		$view = $col2->add('View');
		$status = array();
		$i = 0;
		foreach ($fields as $f) {
			$status[$i]["field"] = $f[name];
			$status[$i]["status"] = 'Free';
			$games = $this->add('Model_Game')->addCondition('field_id',$f['id']);
			foreach ($games as $g) {
				if ($g['is_complete'] == 0) {
					$status[$i]["status"] = 'Busy';
					break;
				}
			}
			$i++;
		}
		foreach ($status as $s) {
			$view->add('Html')->set('<p class="field_status"><strong>'.$s['field'].'</strong>'.' : '
				.($s['status']=='Busy'?'<span class="field_busy">'.$s['status'].'</span':'<span class="field_free">'.$s['status'].'</span')
				.'</p>');
		}
		//$view->js(true)->univ()->setInterval($view->js()->reload()->_enclose(),5000);
	}

	function page_enter_score() {
		$this->api->stickyGET('game_id');

		$score_form = $this->add('Form');
		$score_form->addClass('atk-row');
		$score_form->addSeparator('span4');
		$score_form->setModel('Game');
		$score_form->model->load($_GET['game_id']);
		$score_form->getElement('is_complete')->set(true);
		$score_form->addSeparator('span3');
		$score_form->controller->importFields(
			$score_form->model->ref('team1_score_id')
		);
		$score_form->controller->importFields(
			$score_form->model->ref('team2_score_id')
		);
		$score_form->addSubmit();

		if ($score_form->isSubmitted()) {
			$score_form->update();
			$score_form->js()->univ()->location($this->api->url('../..'))->execute();
		}
	}

}
/*
$this->api->stickyGET('id');
$u = $this->add('Model_User')->load($_GET['id']);
$crud = $this->add('CRUD');
$crud->setModel($u->ref('Item'));
if($crud->grid) {
	$crud->grid->addColumn('button','found','Mark as Found');
	if($_GET['found']) {
		$crud->model->load($_GET['found']);
		$crud->model->markAsFound();
		$crud->js(null,$crud->grid->js()->reload())
			->univ()->alert('Success')->execute();
	}
}
*/