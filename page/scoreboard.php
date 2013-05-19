<?php
class page_scoreboard extends Page {
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

		$divisions = $this->add('Model_Division');
		foreach ($divisions as $d) {
			$tab = $tabs->addTabURL($this->api->url('./division',array('divid'=>$d['id'])),$d['name']);
		}

		/**** Column 2 ****/
		$col2 = $columns->addColumn(3)->add('Frame')->setTitle('Leaderboard');

		$view = $col2->add('View');
		$divisions = $this->add('Model_Division');
		foreach ($divisions as $d) {
			$view->add('H4')->set($d['name']);
			$teams = $this->add('Model_Team')->addCondition('division_id',$d['id']);
			$top_teams = array();
			foreach ($teams as $team) {
				$pts_for = 0;
				$pts_less = 0;
				$wins = 0;
				$losses = 0;
				$my_games1 = $this->add('Model_Game')->addCondition('team1_id',$team['id'])->addCondition('is_complete',true);
				$my_games2 = $this->add('Model_Game')->addCondition('team2_id',$team['id'])->addCondition('is_complete',true);

		        foreach ($my_games1 as $game) {
		            $pts_for += $game['team1_score'];
		            $pts_less += $game['team2_score'];
		            if ($game['team1_score'] > $game['team2_score']) {
		                $wins += 1;
		            } else {
		                $losses += 1;
		            }
		        }

		        foreach ($my_games2 as $game) {
		            $pts_for += $game['team2_score'];
		            $pts_less += $game['team1_score'];
		            if ($game['team2_score'] > $game['team1_score']) {
		                $wins += 1;
		            } else {
		                $losses += 1;
		            }
		        }

				$top_teams[] = array('name'=>$team['name'],'wins'=>"$wins",'losses'=>"$losses");
			}
			array_sort_by_column($top_teams,"wins",SORT_DESC);
			//echo '<pre>'.print_r($top_teams,1).'<pre>';
			for ($i=0;$i<4;$i++) {
				$view->add('Html')->set($top_teams[$i]['name'].' ('.$top_teams[$i]['wins'].' - '.$top_teams[$i]['losses'].')<br />');
			}
		}

	}

	function page_division() {
		$divid = $_GET['divid'];
		$grid = $this->add('Grid');
		$grid->addColumn('name');
		$grid->addColumn('wins');
		$grid->addColumn('losses');
		$grid->addColumn('points_for');
		$grid->addColumn('points_against');
		$grid->addColumn('expander','details');
		$data = array();
		$div_teams = $this->add('Model_Team')->addCondition('division_id',$divid);
		foreach ($div_teams as $team) {
			$pts_for = 0;
			$pts_less = 0;
			$wins = 0;
			$losses = 0;
			$my_games1 = $this->add('Model_Game')->addCondition('team1_id',$team['id'])->addCondition('is_complete',true);
			$my_games2 = $this->add('Model_Game')->addCondition('team2_id',$team['id'])->addCondition('is_complete',true);

	        foreach ($my_games1 as $game) {
	            $pts_for += $game['team1_score'];
	            $pts_less += $game['team2_score'];
	            if ($game['team1_score'] > $game['team2_score']) {
	                $wins += 1;
	            } else {
	                $losses += 1;
	            }
	        }

	        foreach ($my_games2 as $game) {
	            $pts_for += $game['team2_score'];
	            $pts_less += $game['team1_score'];
	            if ($game['team2_score'] > $game['team1_score']) {
	                $wins += 1;
	            } else {
	                $losses += 1;
	            }
	        }

			$data[] = array('name'=>$team['name'],'wins'=>"$wins",'losses'=>"$losses",'points_for'=>"$pts_for",'points_against'=>"$pts_less");
		}
		array_sort_by_column($data,"wins",SORT_DESC);
		$grid->setSource($data);
	}

	/**** Team Details expander ****/
	function page_division_details() {
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

function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
	$sort_col = array();
	foreach ($arr as $key=> $row) {
		$sort_col[$key] = $row[$col];
	}

	array_multisort($sort_col, $dir, $arr);
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