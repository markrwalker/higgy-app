<?php
class page_admin extends Page {
	function initMainPage() {
		parent::init();

		//$is_admin = $this->api->auth->model['is_admin'];
		/*if (!$is_admin) {
			$this->api->redirect('index');
		}*/

		$q = $this->api->db->dsql();
		$q->table('year')->where('current',1)->field('id');
		$year_id = $q->getOne();

		/**** Column 1 ****/

		$columns = $this->add('Columns');
		$col1 = $columns->addColumn(10)->add('Frame');

		$tabs = $col1->add('Tabs');

		/**** Game tab ****/
		$gamesTab = $tabs->addTab('Game Admin');
		$gamesTabs = $gamesTab->add('Tabs');

		/**** Incomplete Games tab ****/
		// $incGamesTab = $gamesTabs->addTab('Incomplete Games');
		// $m = $this->add('Model_Game');
		// $incGamesCrud = $incGamesTab->add('CRUD',array('allow_edit'=>false,'allow_add'=>false));
		// $incGamesCrud->setModel($m,null,array('team1','team2','round'))->addCondition('year_id',$year_id)->addCondition('is_complete',false)->addCondition('field_id',0);
		// $incGamesCrud->js(true)->addClass('refresh_inc_game_crud');
		// $incGamesCrud->js('refresh_inc_game_crud',$incGamesCrud->grid->js()->reload());
		// $incGamesCrud->add('Button')->set('Refresh')->js('click', $incGamesCrud->grid->js()->reload());
		// $incGamesCrud->grid->addColumn('expander','add_field');

		/**** In Progress tabs ****/		
		$progGamesTab = $gamesTabs->addTab('In Progress');
		$m = $this->add('Model_Game');
		$m->setOrder('updated','desc');
		$progGamesCrud = $progGamesTab->add('CRUD',array('allow_edit'=>false,'allow_add'=>false));
		$progGamesCrud->setModel($m,null,array('team1','team1_score','team2','team2_score','field','round','is_complete'))->addCondition('year_id',$year_id)->addCondition('is_complete',false)->addCondition('field_id','>',0);
		$progGamesCrud->js(true)->addClass('refresh_prog_game_crud');
		$progGamesCrud->js('refresh_prog_game_crud',$progGamesCrud->grid->js()->reload());
		$progGamesCrud->add('Button')->set('Refresh')->js('click', $progGamesCrud->grid->js()->reload());
		$progGamesCrud->grid->addColumn('expander','enter_score');

		/**** Completed Games tab ****/
		$compGamesTab = $gamesTabs->addTab('Completed Games');
		$m = $this->add('Model_Game');
		$m->setOrder('updated','desc');
		$compGamesCrud = $compGamesTab->add('CRUD',array('allow_edit'=>false,'allow_add'=>false));
		$compGamesCrud->setModel($m,null,array('team1','team1_score','team2','team2_score','field','round','is_complete'))->addCondition('year_id',$year_id)->addCondition('is_complete',true);
		$compGamesCrud->js(true)->addClass('refresh_comp_game_crud');
		$compGamesCrud->js('refresh_comp_game_crud',$compGamesCrud->grid->js()->reload());
		$compGamesCrud->add('Button')->set('Refresh')->js('click', $compGamesCrud->grid->js()->reload());
		$compGamesCrud->grid->addPaginator(14);
		$compGamesCrud->grid->addQuickSearch(array('team1','team2'));
		$compGamesCrud->grid->addColumn('expander','edit_score');

		/**** Teams tab ****/
		$tab = $tabs->addTab('Team Admin');
		$m = $this->add('Model_Team');
		$m->setOrder('name', 'asc')->setOrder('checked_in', 'asc')->setOrder('dropped_out', 'asc');
		$crud = $tab->add('CRUD', array('allow_edit'=>false));
		$crud->setModel($m,null,array('name','person1','person1_gender','person2','person2_gender','checked_in','dropped_out'))->addCondition('year_id',$year_id)->addCondition('id','!=',999);
		if ($crud->isEditing('add')) {
			$crud->form->getElement('division_id')->set(5)->js(true)->closest('div')->parent('div')->hide();;
			$crud->form->getElement('year_id')->set($year_id)->js(true)->closest('div')->parent('div')->hide();;
			$crud->form->getElement('protected')->js(true)->closest('div')->parent('div')->hide();
		}
		$crud->js(true)->addClass('refresh_team_crud');
		$crud->js('refresh_team_crud')->reload();
		if ($crud->grid) {
			$crud->grid->addButton('Refresh Teams')->js('click',$crud->grid->js()->reload());
			$crud->grid->addColumn('expander','edit_team');
		}

		/**** Users tab ****/
		// $tab = $tabs->addTab('Users Admin');
		// $crud = $tab->add('CRUD');
		// $crud->setModel('Users');
		//$grid->addPaginator(1);
		//$crud->grid->addQuickSearch(array('team'));

		/**** Divisions tab ****/
		// $tab = $tabs->addTab('Division Admin');
		// $tab->add('CRUD')->setModel('Division');

		/**** Fields tab ****/
		$tab = $tabs->addTab('Field Admin');
		$tab->add('CRUD')->setModel('Field');

		/**** Years tab ****/
		$tab = $tabs->addTab('Year Admin');
		$tab->add('CRUD')->setModel('Year');

		/**** Column 2 ****/

		/**** Round Status view ****/
		$col2 = $columns->addColumn(2)->add('Frame')->setTitle('Round Status');
		$crud = $col2->add('CRUD',array('allow_edit'=>false,'allow_add'=>false,'allow_del'=>false));
		$crud->setModel('Settings')->addCondition('setting', 'round');
		if ($crud->grid) {
			$crud->grid->addColumn('button','start_round');
			if($_GET['start_round']) {
				$start = $crud->grid->model->startRound($_GET['start_round']);
				$js[] = $crud->grid->js()->reload();
				$js[] = $crud->js(true)->_selector('.refresh_prog_game_crud')->trigger('refresh_prog_game_crud');
				$js[] = $crud->js(true)->_selector('.refresh_comp_game_crud')->trigger('refresh_comp_game_crud');
				$js[] = $crud->js(true)->_selector('.refresh_field_view')->trigger('refresh_field_view');
				if (is_null($start)) {
					$crud->js(null,$js)->univ()->errorMessage('The swiss pairings are over, now start the knockout tournament')->execute();
				} else if ($start === true) {
					$crud->js(null,$js)->univ()->successMessage('Round Started')->execute();
				} else {
					$crud->js(null,$js)->univ()->errorMessage('There was an error: '.$start)->execute();
				}
			}
		}

		/**** Field Status view ****/
		$view = $col2->add('View');
		$fields = $this->add('Model_Field');
		$view->js(true)->addClass('refresh_field_view');
		$view->js('refresh_field_view', $view->js()->reload());
		$status = array();
		$i = 0;
		foreach ($fields as $f) {
			$status[$i]["field"] = $f['name'];
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
		$view->add('Html')->set('<h2>Field Status</h2>');
		foreach ($status as $s) {
			$view->add('Html')->set('<p class="field_status"><strong>'.$s['field'].'</strong>'.' : '
				.($s['status']=='Busy'?'<span class="field_busy">'.$s['status'].'</span':'<span class="field_free">'.$s['status'].'</span')
				.'</p>');
		}
		$col2->add('Button')->set('Refresh')->js('click', $view->js()->reload());
		//$view->js(true)->univ()->setInterval($view->js()->reload()->_enclose(),15000);
	}

	/**** Add Field expander ****/
	function page_add_field() {
		$this->api->stickyGET('game_id');

		$field_form = $this->add('Form');
		$field_form->addClass('atk-row');
		$field_form->addSeparator('span5');
		$field_form->setModel('Game');
		$field_form->model->load($_GET['game_id']);
		$field_form->addSubmit();
		$q=$this->api->db->dsql();
		$q->table('field')->field('*');
		$q->where('id not in',$q->dsql()->table('game')->field('field_id')->where('is_complete','0'));
		$data = $q->getAll();
		$available_fields[''] = 'Please, select';
		foreach ($data as $x) {
			$available_fields[$x['id']] = $x['name'];
		}
		//$field_form->getElement('team1_id')->js(true)->closest('div')->parent('div')->hide();
		//$field_form->getElement('team2_id')->js(true)->closest('div')->parent('div')->hide();
		//$field_form->getElement('field_id')->destroy();
		//$dropdown = $field_form->getElement('field_id');
		//$dropdown->setValueList($available_fields);
		//$field_form->js()->reload();
		//$field_form->js()->atk4_form('reloadField',$dropdown->short_name,array($this->api->url()));
		//$field_form->addField('dropdown','field_id','Field')->setValueList($available_fields);
		$field_form->getElement('is_complete')->js(true)->closest('div')->hide();

		if ($field_form->isSubmitted()) {
			$field_form->update();
			$field_form->js(null,$this->js()->trigger('refresh_prog_game_crud'))->_selector('.refresh_field_view')->trigger('refresh_field_view')->univ()->successMessage('Game Added to Field')->closeExpander()->execute();
		}
	}

	/**** Enter Score expander ****/
	function page_enter_score() {
		$this->api->stickyGET('game_id');
		$year_id = $this->api->db->dsql()->table('year')->field('id')->where('current',1)->getOne();

		$score_form = $this->add('Form');
		$score_form->addClass('atk-row');
		$score_form->addSeparator('span5');
		$score_form->setModel('Game');
		$score_form->model->load($_GET['game_id']);

		$score_form->getElement('team1_id')->model->addCondition('year_id',$year_id)->setOrder('name','asc');
		$score_form->getElement('team2_id')->model->addCondition('year_id',$year_id)->setOrder('name','asc');

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
			$js[] = $score_form->js(true)->_selector('.refresh_prog_game_crud')->trigger('refresh_prog_game_crud');
			$js[] = $score_form->js(true)->_selector('.refresh_comp_game_crud')->trigger('refresh_comp_game_crud');
			$js[] = $score_form->js(true)->_selector('.refresh_field_view')->trigger('refresh_field_view');
			$score_form->js(null,$js)->univ()->successMessage('Game Updated')->closeExpander()->execute();
		}
	}

	/**** Edit Score expander ****/
	function page_edit_score() {
		$this->api->stickyGET('game_id');
		$year_id = $this->api->db->dsql()->table('year')->field('id')->where('current',1)->getOne();

		$score_form = $this->add('Form');
		$score_form->addClass('atk-row');
		$score_form->addSeparator('span5');
		$score_form->setModel('Game');
		$score_form->model->load($_GET['game_id']);

		$score_form->getElement('team1_id')->model->addCondition('year_id',$year_id)->setOrder('name','asc');
		$score_form->getElement('team2_id')->model->addCondition('year_id',$year_id)->setOrder('name','asc');

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
			$score_form->js(null,$this->js()->trigger('refresh_comp_game_crud'))->univ()->successMessage('Game Updated')->closeExpander()->execute();
		}
	}

	/**** Edit Team expander ****/
	function page_edit_team() {
		$this->api->stickyGET('team_id');

		$team_form = $this->add('Form');
		$team_form->addClass('atk-row');
		$team_form->addSeparator('span5');
		$team_form->setModel('Team');
		$team_form->model->load($_GET['team_id']);
		$team_form->getElement('division_id')->js(true)->closest('div')->parent('div')->hide();
		$team_form->getElement('year_id')->js(true)->closest('div')->parent('div')->hide();
		$team_form->getElement('protected')->js(true)->closest('div')->parent('div')->hide();
		$team_form->addSubmit();

		if ($team_form->isSubmitted()) {
			$team_form->update();
			$this->js()->_selector('.refresh_team_crud')->trigger('refresh_team_crud')->univ()->successMessage('Team Saved')->closeExpander()->execute();
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