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
			$tab = $tabs->addTab($d['name']);
			$grid = $tab->add('Grid');
			$grid->addColumn('name');
			$grid->addColumn('wins');
			$grid->addColumn('losses');
			$grid->addColumn('points_for');
			$grid->addColumn('points_against');
			$data = array();
			$div_teams = $this->add('Model_Team')->addCondition('division_id',$d['id']);
			$div_games = $this->add('Model_Game_Scores')->addCondition('division_id',$d['id']);
			foreach ($div_teams as $team) {
				$pts_for = 0;
				$pts_less = 0;
				$wins = 0;
				$losses = 0;
				foreach ($div_games as $game) {
					if ($game['team1'] == $team['name']) {
						$pts_for += $game['team1_score'];
						$pts_less += $game['team2_score'];
						if ($game['team1_score'] > $game['team2_score']) {
							$wins += 1;
						} else {
							$losses += 1;						
						}
					} elseif ($game['team2'] == $team['name']) {
						$pts_for += $game['team2_score'];
						$pts_less += $game['team1_score'];
						if ($game['team2_score'] > $game['team1_score']) {
							$wins += 1;
						} else {
							$losses += 1;						
						}
					}
				}
				$data[] = array('name'=>$team['name'],'wins'=>"$wins",'losses'=>"$losses",'points_for'=>"$pts_for",'points_against'=>"$pts_less");
			}
			array_sort_higgyball($data,"wins","losses","points_for","points_against");
			$grid->setSource($data);
		}

		/**** Column 2 ****/
		$col2 = $columns->addColumn(3)->add('Frame')->setTitle('Leaderboard');

		$view = $col2->add('View');
		$divisions = $this->add('Model_Division');
		foreach ($divisions as $d) {
			$view->add('H5')->set($d['name']);
			$div_teams = $this->add('Model_Team')->addCondition('division_id',$d['id']);
			$div_games = $this->add('Model_Game_Scores')->addCondition('division_id',$d['id']);
			$top_teams = array();
			foreach ($div_teams as $team) {
				$pts_for = 0;
				$pts_less = 0;
				$wins = 0;
				$losses = 0;
				foreach ($div_games as $game) {
					if ($game['team1'] == $team['name']) {
						if ($game['team1_score'] > $game['team2_score']) {
							$pts_for += $game['team1_score'];
							$pts_less += $game['team2_score'];
							$wins += 1;
						} else {
							$pts_for += $game['team1_score'];
							$pts_less += $game['team2_score'];
							$losses += 1;						
						}
					} elseif ($game['team2'] == $team['name']) {
						if ($game['team2_score'] > $game['team1_score']) {
							$pts_for += $game['team2_score'];
							$pts_less += $game['team1_score'];
							$wins += 1;
						} else {
							$pts_for += $game['team2_score'];
							$pts_less += $game['team1_score'];
							$losses += 1;						
						}
					}
				}

				$top_teams[] = array('name'=>$team['name'],'wins'=>"$wins",'losses'=>"$losses");
			}
			array_sort_higgyball($top_teams,"wins","losses","points_for","points_against");
			//echo '<pre>'.print_r($top_teams,1).'<pre>';
			$view->add('Html')->set('<ol class="leaderboard">');
			for ($i=0;$i<4;$i++) {
				$view->add('Html')->set('<li>'.$top_teams[$i]['name'].' ('.$top_teams[$i]['wins'].' - '.$top_teams[$i]['losses'].')</li>');
			}
			$view->add('Html')->set('</ol>');
		}

	}

	/**** Team Details expander ****/
	function page_details() {
		$my_team = $_GET['name'];
		echo '<pre>'.print_r($_GET,1).'</pre>';

		$grid = $this->add('Grid');
		$grid->debug();
		$grid->addColumn('opponent');
		$grid->addColumn('points_for');
		$grid->addColumn('points_against');
		$grid->addColumn('result');

		$data = array();
		$div_teams = $this->add('Model_Team')->addCondition('division_id',$d['id']);
		$div_games = $this->add('Model_Game_Scores')->addCondition('division_id',$d['id']);
		foreach ($div_teams as $team) {
			if ($team['name'] == $my_team) {
				continue;
			}
			$opponent = $team['name'];
			$pts_for = 0;
			$pts_less = 0;
			$result = '';
			foreach ($div_games as $game) {
				if ($game['team1'] == $my_team && $game['team2'] == $team['name']) {
					$pts_for += $game['team1_score'];
					$pts_less += $game['team2_score'];
					if ($game['team1_score'] > $game['team2_score']) {
						$result = 'W';
					} else {
						$result = 'L';						
					}
				} elseif ($game['team2'] == $my_team && $game['team1'] == $team['name']) {
					$pts_for += $game['team2_score'];
					$pts_less += $game['team1_score'];
					if ($game['team2_score'] > $game['team1_score']) {
						$result = 'W';
					} else {
						$result = 'L';					
					}
				}
			}
			$data[] = array('opponent'=>"$opponent",'points_for'=>"$pts_for",'points_against'=>"$pts_less",'result'=>"$result");
		}
		$grid->setSource($data);
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

	array_multisort($sort[$col1], SORT_DESC, $sort[$col2], SORT_ASC, $sort[$col3], SORT_DESC, $sort[$col4], SORT_ASC, $arr);
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