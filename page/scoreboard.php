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

		$d = $this->add('Model_Division')->addCondition('id',5);
		$grid = $col1->add('Grid');
		$grid->addColumn('name');
		$grid->addColumn('wins');
		$grid->addColumn('losses');
		$grid->addColumn('plus_minus');
		$grid->addColumn('sos');
		$data = array();
		$div_teams = $this->add('Model_Team')->addCondition('division_id',$d['id'])->addCondition('year_id',2)->addCondition('checked_in',1);
		foreach ($div_teams as $team) {
			$wins = 0;
			$losses = 0;
			$plus_minus = 0;
			$sos = 0;
			$team2 = '';
			$div_games = $this->api->db->dsql()
				->table('game_scores')
				->field('team1')
				->field('team1_score')
				->field('team2')
				->field('team2_score')
				->where('division_id',$d['id'])
				->where(array(array('team1',$team['name']),array('team2',$team['name'])));
			foreach ($div_games as $game) {
				if ($game['team1'] == $team['name']) {
					$team2 = $game['team2'];
					$plus_minus += $game['team1_score'];
					$plus_minus -= $game['team2_score'];
					if ($game['team1_score'] > $game['team2_score']) {
						$wins += 1;
					} else {
						$losses += 1;						
					}
				} elseif ($game['team2'] == $team['name']) {
					$team2 = $game['team1'];
					$plus_minus += $game['team2_score'];
					$plus_minus -= $game['team1_score'];
					if ($game['team2_score'] > $game['team1_score']) {
						$wins += 1;
					} else {
						$losses += 1;						
					}
				}
				if (empty($team2)) {
					continue;
				} else {
					$opponent_games = $this->api->db->dsql()
						->table('game_scores')
						->field('team1')
						->field('team1_score')
						->field('team2')
						->field('team2_score')
						->where('division_id',$d['id'])
						->where(array(array('team1',$team2),array('team2',$team2)));
					//$opponent_games->debug();
					//echo '<pre>'.print_r($opponent_games,1).'</pre>'; die();
					foreach ($opponent_games as $game) {
						if ($game['team1'] == $team2) {
							if ($game['team1_score'] > $game['team2_score']) {
								$sos += 1;
							}
						} elseif ($game['team2'] == $team2) {
							if ($game['team2_score'] > $game['team1_score']) {
								$sos += 1;
							}
						}
					}
				}
			}
			$data[] = array('name'=>$team['name'],'wins'=>"$wins",'losses'=>"$losses",'plus_minus'=>"$plus_minus",'sos'=>"$sos");
		}
		array_sort_higgyball($data,"wins","losses","plus_minus","sos");
		$grid->setSource($data);
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

	array_multisort($sort[$col1], SORT_DESC, $sort[$col2], SORT_ASC, $sort[$col3], SORT_DESC, $sort[$col4], SORT_DESC, $arr);
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