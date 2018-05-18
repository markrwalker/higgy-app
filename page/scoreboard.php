<?php
class page_scoreboard extends Page {
	function initMainPage() {
		parent::init();

		//$is_admin = $this->api->auth->model['is_admin'];
		/*if (!$is_admin) {
			$this->api->redirect('index');
		}*/

		$q = $this->api->db->dsql();
		$q->table('year')->where('current',1)->field('id');
		$year_id = $q->getOne();

		$columns = $this->add('Columns');

		/**** Column 1 ****/
		$col1 = $columns->addColumn(9)->add('Frame')->setTitle('Scoreboard');;

		$grid = $col1->add('Grid');
		$grid->addColumn('rank');
		$grid->addColumn('name');
		$grid->addColumn('wins');
		$grid->addColumn('losses');
		$grid->addColumn('sos');
		$grid->addColumn('differential');
		$data = array();
		$div_teams = $this->add('Model_Team')->addCondition('year_id',$year_id)->addCondition('checked_in',1);
		foreach ($div_teams as $team) {
			$wins = 0;
			$losses = 0;
			$plus_minus = 0;
			$sos = 0;
			$team2_id = '';
			$div_games = $this->api->db->dsql()
				->table('game_scores')
				->field('team1_id')
				->field('team1_score')
				->field('team2_id')
				->field('team2_score')
				->where(array(array('team1_id',$team['id']),array('team2_id',$team['id'])))
				->get();
			foreach ($div_games as $game) {
				if ($game['team1_id'] == $team['id']) {
					$team2_id = $game['team2_id'];
					$plus_minus += $game['team1_score'];
					$plus_minus -= $game['team2_score'];
					if ($game['team1_score'] > $game['team2_score']) {
						$wins += 1;
					} else {
						$losses += 1;						
					}
				} elseif ($game['team2_id'] == $team['id']) {
					$team2_id = $game['team1_id'];
					$plus_minus += $game['team2_score'];
					$plus_minus -= $game['team1_score'];
					if ($game['team2_score'] > $game['team1_score']) {
						$wins += 1;
					} else {
						$losses += 1;						
					}
				}
				if (empty($team2_id)) {
					continue;
				} else {
					$opponent_games = $this->api->db->dsql()
						->table('game_scores')
						->field('team1_id')
						->field('team1_score')
						->field('team2_id')
						->field('team2_score')
						->where(array(array('team1_id',$team2_id),array('team2_id',$team2_id)))
						->get();
					//$opponent_games->debug();
					//echo '<pre>'.print_r($opponent_games,1).'</pre>'; die();
					foreach ($opponent_games as $game) {
						if ($game['team1_id'] == $team2_id) {
							if ($game['team1_score'] > $game['team2_score']) {
								$sos += 1;
							}
						} elseif ($game['team2_id'] == $team2_id) {
							if ($game['team2_score'] > $game['team1_score']) {
								$sos += 1;
							}
						}
					}
				}
			}
			$data[] = array('id'=>$team['id'],'name'=>$team['name'],'wins'=>"$wins",'losses'=>"$losses",'sos'=>"$sos",'differential'=>"$plus_minus");
		}
		array_sort_higgyball($data,"wins","losses","sos","differential");
		$i = 1;
		foreach ($data as &$row) {
			$row['rank'] = $i;
			$i++;
		}
		$grid->setSource($data);
		// $grid->addColumn('expander','view_matches');

		/**** Column 2 ****/
		$col2 = $columns->addColumn(3)->add('Frame')->setTitle('Knockout Tournament');
		$view = $col2->add('View');

		$q = $this->api->db->dsql();
		$q->table('settings')->where('setting','round')->field('value');
		$round = $q->getOne();

		$incompleteGames = array();
		$q = $this->api->db->dsql();
		$q->table('game')->where('year_id',$year_id)->where('is_complete',0)->field('id');
		$incompleteGames = $q->get();

		if (!empty($incompleteGames)) {
			$view->add('View_Error')->set('Please complete all games before starting the knockout tournament');
		} else if ($round == 5) {
			$html = '
				<table>
					<tr>
						<td>&nbsp;</td>
						<td>1. '.$data[0]['name'].'</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>9. '.$data[8]['name'].'</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>8. '.$data[7]['name'].'</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>4. '.$data[3]['name'].'</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>13. '.$data[12]['name'].'</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>12. '.$data[11]['name'].'</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>5. '.$data[4]['name'].'</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>6. '.$data[5]['name'].'</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>11. '.$data[10]['name'].'</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>14. '.$data[13]['name'].'</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>3. '.$data[2]['name'].'</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>7. '.$data[6]['name'].'</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>10. '.$data[9]['name'].'</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>2. '.$data[1]['name'].'</td>
					</tr>
				</table>
			';
			$view->add('Html')->set($html);
		} else {
			$view->add('View_Error')->set('Please complete all rounds before starting the knockout tournament');
		}
		$js[] = $grid->js()->reload();
		$js[] = $view->js()->reload();
		$col2->add('Button')->set('Refresh')->js('click', $js);
	}

	/**** View Matches expander ****/
	function page_view_matches() {
		$this->api->stickyGET('scoreboard_id');
		$year_id = $this->api->db->dsql()->table('year')->field('id')->where('current',1)->getOne();

		$match_form = $this->add('Form');
		$match_form->addClass('atk-row');
		$match_form->addSeparator('span5');
		$match_form->setModel('Team');
		$match_form->model->load($_GET['scoreboard_id']);
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

