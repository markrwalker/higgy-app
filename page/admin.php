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

		/**** Add Games tab ****/
		// $addGamesTab = $gamesTabs->addTab('Add Games');
		// $addGamesGrid = $addGamesTab->add('MyGrid');
		// $addGamesGrid->addColumn('rank');
		// $addGamesGrid->addColumn('playing', 'name');
		// $addGamesGrid->addColumn('wins');
		// $addGamesGrid->addColumn('losses');
		// $addGamesGrid->addColumn('sos');
		// $addGamesGrid->addColumn('plus_minus');
		// $data = array();
		// $div_teams = $this->add('Model_Team')->addCondition('year_id',$year_id)->addCondition('checked_in',1)->addCondition('dropped_out',0);
		// foreach ($div_teams as $team) {
		// 	$wins = 0;
		// 	$losses = 0;
		// 	$plus_minus = 0;
		// 	$sos = 0;
		// 	$team2_id = '';
		// 	$div_games = $this->api->db->dsql()
		// 		->table('game_scores')
		// 		->field('team1_id')
		// 		->field('team1_score')
		// 		->field('team2_id')
		// 		->field('team2_score')
		// 		->where(array(array('team1_id',$team['id']),array('team2_id',$team['id'])))
		// 		->get();
		// 	foreach ($div_games as $game) {
		// 		if ($game['team1_id'] == $team['id']) {
		// 			$team2_id = $game['team2_id'];
		// 			$plus_minus += $game['team1_score'];
		// 			$plus_minus -= $game['team2_score'];
		// 			if ($game['team1_score'] > $game['team2_score']) {
		// 				$wins += 1;
		// 			} else {
		// 				$losses += 1;						
		// 			}
		// 		} elseif ($game['team2_id'] == $team['id']) {
		// 			$team2_id = $game['team1_id'];
		// 			$plus_minus += $game['team2_score'];
		// 			$plus_minus -= $game['team1_score'];
		// 			if ($game['team2_score'] > $game['team1_score']) {
		// 				$wins += 1;
		// 			} else {
		// 				$losses += 1;						
		// 			}
		// 		}
		// 		if (empty($team2_id)) {
		// 			continue;
		// 		} else {
		// 			$opponent_games = $this->api->db->dsql()
		// 				->table('game_scores')
		// 				->field('team1_id')
		// 				->field('team1_score')
		// 				->field('team2_id')
		// 				->field('team2_score')
		// 				->where(array(array('team1_id',$team2_id),array('team2_id',$team2_id)))
		// 				->get();
		// 			//$opponent_games->debug();
		// 			//echo '<pre>'.print_r($opponent_games,1).'</pre>'; die();
		// 			foreach ($opponent_games as $game) {
		// 				if ($game['team1_id'] == $team2_id) {
		// 					if ($game['team1_score'] > $game['team2_score']) {
		// 						$sos += 1;
		// 					}
		// 				} elseif ($game['team2_id'] == $team2_id) {
		// 					if ($game['team2_score'] > $game['team1_score']) {
		// 						$sos += 1;
		// 					}
		// 				}
		// 			}
		// 		}
		// 	}
		// 	$team_status = $this->api->db->dsql()->table('team')->field('*')->where('id', $team['id'])->where($this->api->db->dsql()->orExpr()->where('id', $this->api->db->dsql()->table('game')->field('team1_id')->where($this->api->db->dsql()->andExpr()->where('is_complete', '0')->where('team1_id = team.id')))->where('id', $this->api->db->dsql()->table('game')->field('team2_id')->where($this->api->db->dsql()->andExpr()->where('is_complete', '0')->where('team2_id = team.id'))))->fetch();
		// 	$playing = !empty($team_status) ? true: false;
		// 	$data[] = array('id'=>$team['id'],'name'=>$team['name'],'wins'=>"$wins",'losses'=>"$losses",'sos'=>"$sos",'plus_minus'=>"$plus_minus",'playing'=>$playing);
		// }
		// array_sort_higgyball($data,"wins","losses","sos","plus_minus");
		// $i = 1;
		// foreach ($data as &$row) {
		// 	$row['rank'] = $i;
		// 	$i++;
		// }
		// $addGamesGrid->setSource($data);
		// $addGamesGrid->addColumn('expander','create_game');
		// $addGamesGrid->js(true)->addClass('refresh_add_game_grid');
		// $addGamesGrid->js('refresh_add_game_grid',$addGamesGrid->js()->reload());
		// $addGamesTab->add('Button')->set('Refresh')->js('click', $addGamesGrid->js()->reload());

		/**** Teams tab ****/
		$tab = $tabs->addTab('Team Admin');
		$m = $this->add('Model_Team');
		$m->setOrder('name', 'asc')->setOrder('checked_in', 'asc')->setOrder('dropped_out', 'asc');
		$crud = $tab->add('CRUD', array('allow_edit'=>false));
		$crud->setModel($m,null,array('name','person1','person1_gender','person2','person2_gender','checked_in'))->addCondition('year_id',$year_id)->addCondition('id','!=',999);
		if ($crud->isEditing('add')) {
			$crud->form->getElement('division_id')->set(5)->js(true)->closest('div')->parent('div')->hide();;
			$crud->form->getElement('year_id')->set($year_id)->js(true)->closest('div')->parent('div')->hide();;
			$crud->form->getElement('protected')->js(true)->closest('div')->parent('div')->hide();
			$crud->form->getElement('dropped_out')->js(true)->closest('div')->parent('div')->hide();
		}
		$crud->js(true)->addClass('refresh_team_crud');
		$crud->js('refresh_team_crud')->reload();
		if ($crud->grid) {
			$crud->grid->addButton('Refresh Teams')->js('click',$crud->grid->js()->reload());
			$crud->grid->addColumn('button','checkin', 'Check In/Out');
			if($_GET['checkin']) {
				$crud->grid->model->checkinTeam($_GET['checkin']);
				$js[] = $crud->grid->js()->reload();
				$js[] = $crud->js(true)->_selector('.refresh_team_crud')->trigger('refresh_team_crud');
				$crud->js(null,$js)->execute();
			}
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
		$yearCrud = $tab->add('CRUD');
		$yearCrud->setModel('Year');
		$yearCrud->js(true)->addClass('refresh_year_crud');
		$yearCrud->js('refresh_year_crud',$yearCrud->grid->js()->reload());
		if ($yearCrud->grid) {
			$yearCrud->grid->addColumn('button', 'setcurrent', 'Set Current');
			if ($_GET['setcurrent']) {
				$yearCrud->grid->model->setCurrentYear($_GET['setcurrent']);
				$js[] = $yearCrud->grid->js()->reload();
				$js[] = $yearCrud->js(true)->_selector('.refresh_year_crud')->trigger('refresh_year_crud');
				$js[] = $yearCrud->js(true)->_selector('.refresh_prog_game_crud')->trigger('refresh_prog_game_crud');
				$js[] = $yearCrud->js(true)->_selector('.refresh_comp_game_crud')->trigger('refresh_comp_game_crud');
				$js[] = $yearCrud->js(true)->_selector('.refresh_team_crud')->trigger('refresh_team_crud');
				$js[] = $yearCrud->js(true)->_selector('.refresh_field_view')->trigger('refresh_field_view');
				$yearCrud->js(null,$js)->execute();
			}
		}

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
		$fields = $this->add('Model_Field')->addCondition('active', 1);
		$view->js(true)->addClass('refresh_field_view');
		$view->js('refresh_field_view', $view->js()->reload());
		$status = array();
		$i = 0;
		foreach ($fields as $f) {
			$status[$i]["field"] = $f['name'];
			$status[$i]["status"] = !empty($f['inuse']) ? 'Busy' : 'Free';
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

		$score_form->getElement('team1_id')->disable()->model->addCondition('year_id',$year_id)->setOrder('name','asc');
		$score_form->getElement('team2_id')->disable()->model->addCondition('year_id',$year_id)->setOrder('name','asc');

		$score_form->getElement('is_complete')->set(true);
		$score_form->addSeparator('span3');
		$score_form->controller->importFields(
			$score_form->model->ref('team1_score_id')
		);
		$score_form->controller->importFields(
			$score_form->model->ref('team2_score_id')
		);

		$score_form->js(true, '$("#higgy_app_admin_enter_score_form_name").focus();'); // 'name' is the element for team1_score_id for some reason

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
			$js[] = $score_form->js(true)->_selector('.refresh_prog_game_crud')->trigger('refresh_prog_game_crud');
			$js[] = $score_form->js(true)->_selector('.refresh_comp_game_crud')->trigger('refresh_comp_game_crud');
			$js[] = $score_form->js(true)->_selector('.refresh_field_view')->trigger('refresh_field_view');
			$score_form->js(null,$js)->univ()->successMessage('Game Updated')->closeExpander()->execute();
		}
	}

	/**** Create Game expander ****/
	function page_create_game() {
		$this->api->stickyGET('admin_id');
		$year_id = $this->api->db->dsql()->table('year')->field('id')->where('current',1)->getOne();
		$round = $this->api->db->dsql()->table('settings')->field('value')->where('setting', 'round')->getOne();
		$team = $this->api->db->dsql()->table('team')->field('*')->where('id', $_GET['admin_id'])->where($this->api->db->dsql()->orExpr()->where('id', $this->api->db->dsql()->table('game')->field('team1_id')->where($this->api->db->dsql()->andExpr()->where('is_complete', '0')->where('team1_id = team.id')))->where('id', $this->api->db->dsql()->table('game')->field('team2_id')->where($this->api->db->dsql()->andExpr()->where('is_complete', '0')->where('team2_id = team.id'))))->fetch();
		if (!empty($team)) {
			$message = $this->add('View');
			$message->add('Html')->set('<h2>This team is currently playing.</h2>');
		} else {
			$game_form = $this->add('Form');
			$game_form->addClass('atk-row');
			$game_form->addSeparator('span5');
			$game_form->setModel('Game');

			$game_form->getElement('team1_id')->setEmptyText(null)->model->addCondition('id', $_GET['admin_id']);
			$game_form->getElement('team2_id')->model->addCondition('year_id', $year_id)->addCondition('id', '!=', $_GET['admin_id'])->addCondition('inplay1', 0)->addCondition('inplay2', 0)->setOrder('name','asc');
			$game_form->getElement('is_complete')->js(true)->closest('div')->hide();
			$game_form->getElement('field_id')->model->addCondition('inuse', 0);
			$game_form->getElement('round')->set(++$round);

			$game_form->addSeparator('span3');
			$game_grid = $game_form->add('MyGrid');
			$game_grid->addColumn('round');
			$game_grid->addColumn('field');
			$game_grid->addColumn('oppteam', 'opponent');
			$q = $this->api->db->dsql();
			$q->table('game_scores')
				->field(array(
					'round'=>'round',
					'team1'=>'t1.name',
					'team2'=>'t2.name',
					'field'=>'field_id',
					'winner'=>'w.name'
				)
			);
			$q->field(
				$q->expr('CASE WHEN team1_id = '.$_GET['admin_id'].' THEN t2.name ELSE t1.name END'
			), 'opponent')
				->join(array('t1'=>'team'), 'team1_id', 'inner')
				->join(array('t2'=>'team'), 'team2_id', 'inner')
				->join(array('w'=>'team'), 'winner_id', 'inner')
				->where(
					$q->orExpr()
					->where('team1_id', $_GET['admin_id'])
					->where('team2_id', $_GET['admin_id'])
			);

			$team_game_data = $q->get();
			$game_grid->setSource($team_game_data);

			$game_form->addSubmit();

			if ($game_form->isSubmitted()) {
				$game_form->update();
				$js[] = $game_form->js(true)->_selector('.refresh_add_game_grid')->trigger('refresh_add_game_grid');
				$js[] = $game_form->js(true)->_selector('.refresh_prog_game_crud')->trigger('refresh_prog_game_crud');
				$js[] = $game_form->js(true)->_selector('.refresh_field_view')->trigger('refresh_field_view');
				$game_form->js(null,$js)->univ()->successMessage('Game Added')->closeExpander()->execute();
			}
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
//		$team_form->getElement('protected')->js(true)->closest('div')->parent('div')->hide();
		$team_form->addSubmit();

		if ($team_form->isSubmitted()) {
			$team_form->update();
			$this->js()->_selector('.refresh_team_crud')->trigger('refresh_team_crud')->univ()->successMessage('Team Saved')->closeExpander()->execute();
		}
	}

}

function array_sort_higgyball(&$arr, $col1, $col2, $col3, $col4) {
	$sort = array();
	foreach ($arr as $key=>$val) {
		$sort[$col1][$key] = $val[$col1];
		$sort[$col2][$key] = $val[$col2];
		$sort[$col3][$key] = $val[$col3];
		$sort[$col4][$key] = $val[$col4];
	}

	array_multisort($sort[$col1], SORT_DESC, $sort[$col2], SORT_ASC, $sort[$col3], SORT_DESC, $sort[$col4], SORT_DESC, $arr);
}
