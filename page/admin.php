<?php
class page_admin extends Page {
	function initMainPage() {
		parent::init();

		//$is_admin = $this->api->auth->model['is_admin'];
		/*if (!$is_admin) {
			$this->api->redirect('index');
		}*/

		/**** Column 1 ****/
		$columns = $this->add('Columns');
		$col1 = $columns->addColumn(9)->add('Frame');

		$tabs = $col1->add('Tabs');

		/**** Game tab ****/
		$tab = $tabs->addTab('Game Admin');

		/**** Game Fields tabs ****/		
		$gamesTabs = $tab->add('Tabs');
		$fields = $this->add('Model_Field');
		foreach ($fields as $f) {
			$tab = $gamesTabs->addTab($f['name']);
			$m = $this->add('Model_Game');
			$crud = $tab->add('CRUD',array('allow_edit'=>false));
			$crud->setModel($m)->addCondition('field_id',$f['id'])->addCondition('is_complete',false);
			if ($crud->grid)
				$crud->add('Button')->set('Refresh')->js('click', $crud->grid->js()->reload());
			/*$games = $this->add('Model_Game')->addCondition('field_id',$f['id']);
			$remove_button = false;
			foreach ($games as $g) {
				if ($g['is_complete'] == 0) {
					$remove_button = true;
					break;
				}
			}
			///////////////// FIX //////////////////
			if ($remove_button) $crud->add_button->js(true)->hide(true);*/
			if ($crud->grid) {
				$crud->grid->addColumn('expander','enter_score');
			}
			if ($crud->isEditing('add')) {
				$q=$this->api->db->dsql();
				$q->table('team')->field('*');
				$q
					->where('id not in',$q->dsql()->table('game')->field('team1_id')->where('is_complete','0'))
					->where('id not in',$q->dsql()->table('game')->field('team2_id')->where('is_complete','0'));
				$data = $q->getAll();
				foreach ($data as $x) {
					$available_teams[$x['id']] = "'".$x['name']."'";
				}
				$crud->form->getElement('team1_id')->setValueList($available_teams);
				$crud->form->getElement('field_id')->set($f['id'])->js(true);
			}
		}

		/**** Completed Games tab ****/
		$tab = $gamesTabs->addTab('Completed Games');
		$m = $this->add('Model_Game');
		$crud = $tab->add('CRUD',array('allow_edit'=>false,'allow_add'=>false));
		$crud->setModel($m)->addCondition('is_complete',true);
		$crud->add('Button')->set('Refresh')->js('click', $crud->grid->js()->reload());
		if ($crud->grid) {
			$crud->grid->addPaginator(7);
			$crud->grid->addQuickSearch(array('team1','team2'));
			$crud->grid->addColumn('expander','edit_score');
		}

		/**** Teams tab ****/
		$tab = $tabs->addTab('Team Admin');

		/**** Team Divisions tabs ****/
		$teamsTabs = $tab->add('Tabs');
		$divisions = $this->add('Model_Division');
		$year_id = $this->api->db->dsql()->table('year')->where('name',date('Y'))->field('id');
		foreach ($divisions as $d) {
			$tab = $teamsTabs->addTab($d['name']);
			$m = $this->add('Model_Team');
			$crud = $tab->add('CRUD');
			$crud->setModel($m)->addCondition('division_id',$d['id']);
			if ($crud->isEditing('add')) {
				$crud->form->getElement('division_id')->set($d['id'])->js(true);
				$crud->form->getElement('year_id')->set($year_id)->js(true);
			}
		}

		/**** Divisions tab ****/
		$tab = $tabs->addTab('Division Admin');
		$tab->add('CRUD')->setModel('Division');

		/**** Fields tab ****/
		$tab = $tabs->addTab('Field Admin');
		$tab->add('CRUD')->setModel('Field');

		/**** Years tab ****/
		$tab = $tabs->addTab('Year Admin');
		$tab->add('CRUD')->setModel('Year');

		/**** Users tab ****/
		$tab = $tabs->addTab('Users Admin');
		$grid = $tab->add('Grid');
		$grid->setModel('Users');
		$grid->addPaginator(7);
		$grid->addQuickSearch(array('team'));

		/**** Column 2 ****/
		$col2 = $columns->addColumn(3)->add('Frame')->setTitle('Field Status');
		
		/**** Game Status view ****/
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
		$col2->add('Button')->set('Refresh')->js('click', $view->js()->reload());
		//$view->js(true)->univ()->setInterval($view->js()->reload()->_enclose(),15000);
	}

	/**** Enter Score expander ****/
	function page_enter_score() {
		$this->api->stickyGET('game_id');

		$score_form = $this->add('Form');
		$score_form->addClass('atk-row');
		$score_form->addSeparator('span4');
		$score_form->setModel('Game');
		$score_form->model->load($_GET['game_id']);
		//$score_form->controller->importField('is_complete')->set(true);
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