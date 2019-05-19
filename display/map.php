<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

require_once('inc/config.php');

$result = '';
if ((int)$current_round == 5 && $round_complete) {
	include('tournament.php');
} else {
	$result = 'data: <div id="fields">';
	$fields = array();
	$fields_query = mysqli_query($db, "SELECT * FROM field WHERE active = 1 ORDER BY id ASC");
	while ($row = mysqli_fetch_assoc($fields_query)) {
		$fields[] = $row;
	}
	if (!empty($fields)) {
		foreach ($fields as $field) {
			$matchup = '<em>Empty</em>';
			$game_query = "SELECT t1.name AS 'team1', t2.name AS 'team2' FROM game g
				INNER JOIN team t1 ON t1.id = g.team1_id
				INNER JOIN team t2 ON t2.id = g.team2_id
				WHERE g.year_id = $year_id
				AND g.round = $current_round
				AND g.is_complete = 0
				AND g.field_id = {$field['id']}
			";
			$game_result = mysqli_query($db, $game_query);
			$game = mysqli_fetch_assoc($game_result);
			if (!empty($game)) {
				$matchup = '<div class="matchup">'.$game['team1'] . '<br>' . $game['team2'] . '</div>';
			}
			$result .= '<div class="field'.$field['id'].'"><div>Field '.$field['id'].'</div>'.$matchup.'</div>';
		}
	} else {
		$result .= '<div class="field1"><div>Field 1</div><em>Empty</em></div>';
		$result .= '<div class="field2"><div>Field 2</div><em>Empty</em></div>';
		$result .= '<div class="field3"><div>Field 3</div><em>Empty</em></div>';
		$result .= '<div class="field4"><div>Field 4</div><em>Empty</em></div>';
		$result .= '<div class="field5"><div>Field 5</div><em>Empty</em></div>';
		$result .= '<div class="field6"><div>Field 6</div><em>Empty</em></div>';
		$result .= '<div class="field7"><div>Field 7</div><em>Empty</em></div>';
		$result .= '<div class="field8"><div>Field 8</div><em>Empty</em></div>';
		$result .= '<div class="field9"><div>Field 9</div><em>Empty</em></div>';
		$result .= '<div class="field10"><div>Field 10</div><em>Empty</em></div>';
		$result .= '<div class="field11"><div>Field 11</div><em>Empty</em></div>';
		$result .= '<div class="field12"><div>Field 12</div><em>Empty</em></div>';
		$result .= '<div class="field13"><div>Field 13</div><em>Empty</em></div>';
		$result .= '<div class="field14"><div>Field 14</div><em>Empty</em></div>';
	}

	$result .= '</div><img id="map" src="img/map2019.jpg"><img src="img/h16-header.jpg" id="logo">';
}

echo $result."\n\n";
flush();
?>