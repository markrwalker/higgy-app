<?php
ini_set('memory_limit', '512M');
ini_set('max_execution_time', 300);

class Model_Settings extends Model_Table {
	public $table = 'settings';
	function init() {
		parent::init();

		$this->addField('setting');
		$this->addField('value');

		$q = $this->api->db->dsql();
		$q->table('year')->where('current',1)->field('id');
		$this->year_id = $q->getOne();
		$this->round = null;
		$this->team_data = array();
		$q = $this->api->db->dsql();
		$q->table('field')->field('id')->where('active', 1);
		$fields = $q->get();
		$this->available_fields = array();
		foreach ($fields as $field) {
			$this->available_fields[$field['id']] = $field['id'];
		}
	}

	function toggleSetting($settings_id) {
		$val = $this->load($settings_id)->get('value');
		$opp = $val == 'On' ? 'Off' : 'On';
		$this->load($settings_id)->set('value', $opp)->save();
	}

	function startRound($settings_id) {
		global $which, $save_segments, $error, $success;

		$error = false;
		$success = false;
		$round = $this->load($settings_id)->get('value');
		$this->round = ++$round;

		$q = $this->api->db->dsql();
		$q->table('game')->where('year_id',$this->year_id)->where('is_complete',0)->field('id');
		$incompleteGames = $q->get();
		if (!empty($incompleteGames)) {
			return "Games are still ongoing. Please add scores to complete all games";
		}

		// if ($round > 5) {
		// 	return null;
		// } else if ($round <= 5) {
		if ($round) {
			$q = $this->api->db->dsql();
			$q->table('team')->field('*')->where('year_id', $this->year_id)->where('dropped_out', 0);
			if ($round > 1) $q->where('checked_in', 1);
			$teams = $q->get();

			$team1_data = array();
			foreach ($teams as $team) {
				$team_plus_minus = 0;
				$team_wins = 0;
				$team_losses = 0;
				$team_sos = 0;
				$team_game_data = array();

				$q = $this->api->db->dsql();
				$q->table('game_scores')->field('*')->where($q->orExpr()->where('team1_id', $team['id'])->where('team2_id', $team['id']))->where('round<', 6);
				$team_game_data = $q->get();

				$history = array();
				$fields = array();
				foreach ($team_game_data as $game) {
					$team2_id = '';
					if ($game['team1_id'] == $team['id']) {
						$team2_id = $game['team2_id'];
						$history[] = $team2_id;
						$fields[] = $game['field_id'];
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
						$fields[] = $game['field_id'];
						$team_plus_minus += $game['team2_score'];
						$team_plus_minus -= $game['team1_score'];
						if ($game['team2_score'] > $game['team1_score']) {
							$team_wins += 1;
						} else {
							$team_losses += 1;
						}
					}

					$q = $this->api->db->dsql();			
					$q->table('game_scores')->field('*')->where($q->orExpr()->where('team1_id', $team2_id)->where('team2_id', $team2_id))->where('round<', 6);
					$opponent_game_data = $q->get();

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
				$team1_data[$team['id']] = array('id'=>$team['id'],'name'=>$team['name'],'wins'=>"$team_wins",'losses'=>"$team_losses",'sos'=>"$team_sos",'plus_minus'=>"$team_plus_minus",'protected'=>$team['protected'],'history'=>$history,'fields'=>$fields);
			}
			$this->array_sort_higgyball($team1_data,"wins","losses","sos","plus_minus");

			foreach ($team1_data as $data) {
				$this->team_data[$data['id']] = array(
					'id' => $data['id'],
					'name' => $data['name'],
					'wins' => $data['wins'],
					'losses' => $data['losses'],
					'plus_minus' => $data['plus_minus'],
					'sos' => $data['sos'],
					'protected' => $data['protected'],
					'history' => $data['history'],
					'fields' => $data['fields']
				);
			}

			$this->generate_round($this->team_data);
			if ($error === true) {
				$i = 0;
				do {
					$i++;
					$this->generate_round($this->team_data, true);
				} while ($success === false);
			}

			$this->load($settings_id)->set('value', $this->round)->save();
			return true;
		} else {
			return 'There was a problem, please try again';
		}
	}

	private function generate_round($team_data, $retry = false) {
		global $which, $save_segments, $error, $success;
		if ($retry) {
			$error = false;
		}
		if ($this->round == 6) {
			$team_data= array_values($team_data);
			$matchups = array(
				array(
					'team1_id' => $team_data[8]['id'],
					'team2_id' => $team_data[7]['id'],
				),
				array(
					'team1_id' => $team_data[3]['id'],
					'team2_id' => $team_data[12]['id'],
				),
				array(
					'team1_id' => $team_data[11]['id'],
					'team2_id' => $team_data[4]['id'],
				),
				array(
					'team1_id' => $team_data[5]['id'],
					'team2_id' => $team_data[10]['id'],
				),
				array(
					'team1_id' => $team_data[13]['id'],
					'team2_id' => $team_data[2]['id'],
				),
				array(
					'team1_id' => $team_data[6]['id'],
					'team2_id' => $team_data[9]['id'],
				),
			);
			$games = $this->generate_games($matchups);
		} else if ($this->round == 7) {
			$team_data= array_values($team_data);
			$q = $this->api->db->dsql();
			$q->table('game_scores')->field('winner_id')->where('round', 6)->where('year_id', $this->year_id)->order('game_id ASC');
			$winners = $q->get();
			if ($winners) {
				$matchups = array(
					array(
						'team1_id' => $team_data[0]['id'],
						'team2_id' => $winners[0]['winner_id'],
					),
					array(
						'team1_id' => $winners[1]['winner_id'],
						'team2_id' => $winners[2]['winner_id'],
					),
					array(
						'team1_id' => $winners[3]['winner_id'],
						'team2_id' => $winners[4]['winner_id'],
					),
					array(
						'team1_id' => $winners[5]['winner_id'],
						'team2_id' => $team_data[1]['id'],
					),
				);
				$games = $this->generate_games($matchups);
			}
		} else if ($this->round == 8) {
			$q = $this->api->db->dsql();
			$q->table('game_scores')->field('winner_id')->where('round', 7)->where('year_id', $this->year_id)->order('game_id ASC');
			$winners = $q->get();
			if ($winners) {
				$matchups = array(
					array(
						'team1_id' => $winners[0]['winner_id'],
						'team2_id' => $winners[1]['winner_id'],
					),
					array(
						'team1_id' => $winners[2]['winner_id'],
						'team2_id' => $winners[3]['winner_id'],
					),
				);
				$games = $this->generate_games($matchups);
			}
		} else if ($this->round == 9) {
			$q = $this->api->db->dsql();
			$q->table('game_scores')->field('*')->where('round', 8)->where('year_id', $this->year_id)->order('game_id ASC');
			$games = $q->get();
			if ($games) {
				$losers = array();
				foreach ($games as $game) {
					if ($game['winner_id'] == $game['team1_id']) {
						$losers[] = $game['team2_id'];
					} else {
						$losers[] = $game['team1_id'];
					}
				}
				if (!empty($losers)) {
					$matchups = array(
						array(
							'team1_id' => $losers[0],
							'team2_id' => $losers[1],
						)
					);
					$games = $this->generate_games($matchups);
				}
			}
		} else if ($this->round == 10) {
			$q = $this->api->db->dsql();
			$q->table('game_scores')->field('winner_id')->where('round', 8)->where('year_id', $this->year_id)->order('game_id ASC');
			$winners = $q->get();
			if ($winners) {
				$matchups = array(
					array(
						'team1_id' => $winners[0]['winner_id'],
						'team2_id' => $winners[1]['winner_id'],
					)
				);
				$games = $this->generate_games($matchups);
			}
		} else {
			if (!$retry) {
				$segments = array();
				$x = 0;
				$wl = '';
				if (count($team_data) % 2 != 0 && $this->round == 1) {
					$team_data = array(999=>array('wins'=>0,'losses'=>0,'plus_minus'=>0,'sos'=>0,'protected'=>0)) + $team_data;
				} else if (count($team_data) % 2 != 0 && $this->round > 1) {
					$team_data = array(999=>array('wins'=>0,'losses'=>99,'plus_minus'=>-999,'sos'=>0,'protected'=>0)) + $team_data;
				}
				foreach ($team_data as $team) {
					if ($wl != $team['wins'].'-'.$team['losses']) {
						$wl = $team['wins'].'-'.$team['losses'];
						$x++;
					}
					$segments[$x][] = $team['id'];//.' '.$team['wins'].'-'.$team['losses'].' '.$team['sos'].' '.$team['plus_minus'];
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
			} else if ($retry && !empty($save_segments)) {
				$segments = $save_segments;
				if (!empty($which)) {
					$move1 = $segments[$which+1][count($segments[$which+1])-1];
					unset($segments[$which+1][count($segments[$which+1])-1]);
					array_unshift($segments[$which], $move1);
					$move2 = $segments[$which+1][count($segments[$which+1])-1];
					unset($segments[$which+1][count($segments[$which+1])-1]);
					array_unshift($segments[$which], $move2);
					$which = '';
					$save_segments = $segments;
				}
			}

			// $output = '';
			// foreach ($segments as $x => $ids) {
			// 	$output .= 'segment '.$x."\n";
			// 	foreach ($ids as $id) {
			// 		$output .= print_r($this->team_data[$id],1);
			// 	}
			// 	$output .= "\n\n";
			// }

			$games = array();
			$matchups = array();
			foreach ($segments as $segment => $ids) {
				if (!empty($ids)) {
					$seg_matchups = $this->generate_matchups($ids);
					if ($error) {
						$which = $segment;
						$save_segments = $segments;
						$seg_matchups = '';
					}
					if (!empty($seg_matchups)) {
						$matchups = array_merge($matchups, $seg_matchups);
					}
				}
			}
			if (!empty($matchups) && !$error) {
				$success = true;
				$games = $this->generate_games($matchups);
			}
		}
	}

	private function generate_matchups($ids) {
		global $error;

		$check = false;
		$matchups = array();
		do {
			shuffle($ids);
			$n = sizeof($ids);
			for ($i=0; $i<$n; $i+=2) {
				if ($ids[$i] != 999 && $ids[$i+1] != 999 && (in_array($ids[$i], $this->team_data[$ids[$i+1]]['history']) || in_array($ids[$i+1], $this->team_data[$ids[$i]]['history']))) {
					// the opponents have played each other already
					$matchups = array();
					if ($n == 2) {
						$error = true;
						$check = true;
					}
					break;
				}
				if ($this->round == 1) {
					if ($this->team_data[$ids[$i]]['protected'] == 1 && $this->team_data[$ids[$i+1]]['protected'] == 1) {
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

		return $matchups;
	}

	private function generate_games($matchups) {

		foreach ($matchups as $matchup) {
			$team1_id = $matchup['team1_id'];
			$team2_id = $matchup['team2_id'];
			$field = $this->get_random_field($team1_id, $team2_id);

			$q = $this->api->db->dsql();
			$team1_score_id = $q->table('score')->set('score',null)->set('created',$q->expr('NOW()'))->set('updated',$q->expr('NOW()'))->do_insert();

			$q = $this->api->db->dsql();
			$team2_score_id = $q->table('score')->set('score',null)->set('created',$q->expr('NOW()'))->set('updated',$q->expr('NOW()'))->do_insert();

			$q = $this->api->db->dsql();
			$game_id = $q
				->table('game')
				->set('team1_id',$team1_id)
				->set('team1_score_id',$team1_score_id)
				->set('team2_id',$team2_id)
				->set('team2_score_id',$team2_score_id)
				->set('field_id',$field)
				->set('year_id',$this->year_id)
				->set('round',$this->round)
				->set('is_complete',0)
				->set('created',$q->expr('NOW()'))
				->set('updated',$q->expr('NOW()'))
				->do_insert()
			;
			unset($this->available_fields[$field]);
		}
	}

	private function get_random_field($team1_id, $team2_id) {
		$possible_fields = $this->available_fields;
		foreach ($this->available_fields as $field) {
			if (in_array($field, $this->team_data[$team1_id]['fields'])) {
				unset($possible_fields[$field]);
			}
			if (in_array($field, $this->team_data[$team2_id]['fields'])) {
				unset($possible_fields[$field]);
			}
		}
		if (empty($possible_fields)) {
			$possible_fields = $this->available_fields;
		}
		$this->ashuffle($possible_fields);
		return array_shift($possible_fields);
	}

	private function array_sort_higgyball(&$arr, $col1, $col2, $col3, $col4) {
		$sort = array();
		foreach ($arr as $key=>$val) {
			$sort[$col1][$key] = $val[$col1];
			$sort[$col2][$key] = $val[$col2];
			$sort[$col3][$key] = $val[$col3];
			$sort[$col4][$key] = $val[$col4];
		}

		array_multisort($sort[$col1], SORT_DESC, $sort[$col2], SORT_ASC, $sort[$col3], SORT_DESC, $sort[$col4], SORT_DESC, $arr);
	}

	private function ashuffle (&$arr) {
		uasort($arr, function ($a, $b) {
			return rand(-1, 1);
		});
	}
}
