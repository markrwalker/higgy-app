<?php
	ini_set('memory_limit', '256M');
	ini_set('max_execution_time', 300);
	//ini_set('display_errors',1);
	//error_reporting(E_ALL);
	require_once('mobile/config.php');
	global $team_data, $year_id, $round;
	$year_id = 3;
	$round = 0;
	if (!empty($_GET['round'])) $round = $_GET['round'];

	if ($round < 5) {
		echo '<h1><a href="tournament.php?round='.($round+1).'">Start Round '.($round+1).'</a></h1>';
	} else {
		echo '<h1>No more rounds after these games</h1>';
	}

	if ($round > 0) {
		$teams = array();
		$checked_in = '';
		if ($round > 1) $checked_in = "AND checked_in = 1";
		$sql1 = "SELECT * FROM `team` WHERE `year_id` = $year_id $checked_in AND `dropped_out` = 0";
		$result1 = mysql_query($sql1);
		while ($row = mysql_fetch_assoc($result1)) {
			$teams[] = $row;
		}
		
		$team1_data = array();
		foreach ($teams as $team) {
			$team_plus_minus = 0;
			$team_wins = 0;
			$team_losses = 0;
			$team_sos = 0;
			$team_game_data = array();
			$sql = "SELECT * FROM game_scores WHERE (team1_id = '".$team['id']."' OR team2_id = '".$team['id']."')";
			$result = mysql_query($sql);
			while ($row = mysql_fetch_assoc($result)) {
				$team_game_data[] = $row;
			}
			$history = array();
			foreach ($team_game_data as $game) {
				$team2_id = '';
				if ($game['team1_id'] == $team['id']) {
					$team2_id = $game['team2_id'];
					$history[] = $team2_id;
					$team_plus_minus += $game['team1_score'];
					$team_plus_minus -= $game['team2_score'];
					if ($game['team1_score'] > $game['team2_score']) {
						$team_wins += 1;
					} else {
						$team_losses += 1;
					}
				} elseif ($game['team2_id'] == $team['id']) {
					$team2_id = $game['team1_id'];
					$history[] = $team2_id;
					$team_plus_minus += $game['team2_score'];
					$team_plus_minus -= $game['team1_score'];
					if ($game['team2_score'] > $game['team1_score']) {
						$team_wins += 1;
					} else {
						$team_losses += 1;
					}
				}
				$sql2 = "SELECT * FROM game_scores WHERE (team1_id = $team2_id OR team2_id = $team2_id)";
				$result2 = mysql_query($sql2);
				$opponent_game_data = array();
				while ($row = mysql_fetch_assoc($result2)) {
					$opponent_game_data[] = $row;
				}
				foreach ($opponent_game_data as $game) {
					if ($game['team1_id'] == $team2_id) {
						if ($game['team1_score'] > $game['team2_score']) {
							$team_sos += 1;
						}
					} elseif ($game['team2_id'] == $team2_id) {
						if ($game['team2_score'] > $game['team1_score']) {
							$team_sos += 1;
						}
					}
				}
			}
			$team1_data[$team['id']] = array('id'=>$team['id'],'name'=>$team['name'],'wins'=>"$team_wins",'losses'=>"$team_losses",'sos'=>"$team_sos",'plus_minus'=>"$team_plus_minus",'protected'=>$team['protected'],'history'=>$history);
			unset($sql);
			unset($result);
		}
		array_sort_higgyball($team1_data,"wins","losses","sos","plus_minus");
		//echo '<pre>'.print_r($team1_data,1).'</pre>';
		$team_data = array();
		foreach ($team1_data as $data) {
			$team_data[$data['id']] = array(
				'name' => $data['name'],
				'wins' => $data['wins'],
				'losses' => $data['losses'],
				'plus_minus' => $data['plus_minus'],
				'sos' => $data['sos'],
				'protected' => $data['protected'],
				'history' => $data['history']
			);
		}
		//echo '<pre>'.print_r($team_data,1).'</pre>';
		$team_round_data = generate_round($team_data);
	}


	function generate_round($team_data) {
		global $round;
		$segments = array();
		$x = 0;
		$wl = '';
		if (count($team_data) % 2 != 0 && $round == 1) {
			$team_data = array(999=>array('wins'=>0,'losses'=>0,'plus_minus'=>0,'sos'=>0,'protected'=>0)) + $team_data;
		} else if (count($team_data) % 2 != 0 && $round > 1) {
			$team_data = array(999=>array('wins'=>0,'losses'=>99,'plus_minus'=>-999,'sos'=>0,'protected'=>0)) + $team_data;
		}
		foreach ($team_data as $id=>$team) {
			if ($wl != $team['wins'].'-'.$team['losses']) {
				$wl = $team['wins'].'-'.$team['losses'];
				$x++;
			}
			$segments[$x][] = $id;//.' '.$team['wins'].'-'.$team['losses'].' '.$team['sos'].' '.$team['plus_minus'];
		}
		$segments = array_reverse($segments, true);
		$n = count($segments);
		for ($i=$n; $i>0; $i--) {
			$segments[$i] = array_reverse($segments[$i]);
		}
		for ($i=$n; $i>0; $i--) {
			if (count($segments[$i]) % 2 != 0) {
				$move = $segments[$i][count($segments[$i])-1];
				unset($segments[$i][count($segments[$i])-1]);
				array_unshift($segments[$i-1], $move);
			}
		}
		//echo '<pre>'.print_r($segments,1).'</pre>'; die();

		$games = array();
		foreach ($segments as $record=>$ids) {
			if (!empty($ids)) {
				$matchups = generate_matchups($ids);
				$games = generate_games($matchups);
			}
		}
	}

	function generate_matchups($ids) {
		global $team_data, $round;
		$check = false;
		$matchups = array();
		do {
			shuffle($ids);
			$n = sizeof($ids);
			for ($i=0; $i<$n; $i+=2) {
				echo $ids[$i].' opponent\'s history '.implode(',', $team_data[$ids[$i+1]]['history']).'<br>';
				echo $ids[$i+1].' opponent\'s history '.implode(',', $team_data[$ids[$i]]['history']).'<br><br>';
				if ($ids[$i] != 999 && $ids[$i+1] != 999 && (in_array($ids[$i], $team_data[$ids[$i+1]]['history']) || in_array($ids[$i+1], $team_data[$ids[$i]]['history']))) {
					// the opponents have played each other already
					echo 'restarting...<br>';
					$matchups = array();
					break;
				}
				if ($round == 1) {
					if ($team_data[$ids[$i]]['protected'] == 1 && $team_data[$ids[$i+1]]['protected'] == 1) {
						$matchups = array();
						break;
					} else {
						$matchups[] = array('team1_id'=>$ids[$i], 'team2_id'=>$ids[$i+1]);
					}
				} else {
					$matchups[] = array('team1_id'=>$ids[$i], 'team2_id'=>$ids[$i+1]);
				}
			}
			if ($i == $n && !empty($matchups)) $check = true;
		} while ($check == false);
		//echo '<pre>'.print_r($matchups,1).'</pre>';
		return $matchups;
	}

	function generate_games($matchups) {
		global $year_id, $round, $team_data;
		foreach ($matchups as $matchup) {
			$team1_id = $matchup['team1_id'];
			$team2_id = $matchup['team2_id'];
			$sql3 = "INSERT INTO score (score, created, updated) VALUES ('0', now(), now())";
			$result3 = mysql_query($sql3);
			$team1_score_id = mysql_insert_id();
			$sql4 = "INSERT INTO score (score, created, updated) VALUES ('0', now(), now())";
			$result4 = mysql_query($sql4);
			$team2_score_id = mysql_insert_id();
			$sql5 = "INSERT INTO game (team1_id, team1_score_id, team2_id, team2_score_id, year_id, round, is_complete, created, updated) 
				VALUES ($team1_id, $team1_score_id, $team2_id, $team2_score_id, $year_id, $round, 0, now(), now())";
			$result5 = mysql_query($sql5);
			$game_id = mysql_insert_id();
			echo 'Game '.$game_id.': ';
			echo $team_data[$team1_id]['name'].' ('.$team_data[$team1_id]['wins'].'-'.$team_data[$team1_id]['losses'].' sos:'.$team_data[$team1_id]['sos'].' diff:'.$team_data[$team1_id]['plus_minus'].')';
			echo ' vs ';
			echo $team_data[$team2_id]['name'].' ('.$team_data[$team2_id]['wins'].'-'.$team_data[$team2_id]['losses'].' sos:'.$team_data[$team2_id]['sos'].' diff:'.$team_data[$team2_id]['plus_minus'].')';
			echo '<br>';
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

		array_multisort($sort[$col1], SORT_ASC, $sort[$col2], SORT_DESC, $sort[$col3], SORT_ASC, $sort[$col4], SORT_ASC, $arr);
	}
