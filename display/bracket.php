<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

require_once('inc/config.php');

$result = '';
if ((int)$current_round > 5) {
	include('tournament.php');
} else {
	// $result = 'data: <table class="table table-striped table-condensed" id="bracket" border="1" cellpadding="0" cellspacing="0"><thead><tr><th colspan="2" class="teams">Teams</th><th>Record</th><th>SOS</th><th>+/-</th><th class="rotate"><div class="vertical">Round 1</div></th><th class="rotate"><div class="vertical">Round 2</div></th><th class="rotate"><div class="vertical">Round 3</div></th><th class="rotate"><div class="vertical">Round 4</div></th><th class="rotate"><div class="vertical">Round 5</div></th></tr></thead>';
	$result = 'data: <table class="table table-striped table-condensed" id="bracket" border="1" cellpadding="0" cellspacing="0"><thead><tr><th colspan="2" class="teams">Teams</th><th>Record</th><th class="rotate"><div class="vertical">Round 1</div></th><th class="rotate"><div class="vertical">Round 2</div></th><th class="rotate"><div class="vertical">Round 3</div></th><th class="rotate"><div class="vertical">Round 4</div></th><th class="rotate"><div class="vertical">Round 5</div></th></tr></thead>';

	$team_data = get_team_data();

	if (!empty($team_data)) {
		$result .= '<tbody>';
		$count = count($team_data);
		foreach ($team_data as $team) {
			$trophies = '';
			if ($team['winner']) {
					$trophies .= '<img class="trophy" src="img/trophy'.$team['winner'].'.png">';
			}
			$field = '';
			$field_sql = "SELECT field_id FROM game WHERE (team1_id = {$team['id']} OR team2_id = {$team['id']}) AND year_id = $year_id AND is_complete = 0";
			$field_query = mysqli_query($db, $field_sql);
			$field_result = mysqli_fetch_assoc($field_query);
			if (!empty($field_result['field_id'])) {
				$field = '<span class="field_icon field' . $field_result['field_id'] . '">' . $field_result['field_id'] . '</span>';
			}
			$result .= '<tr>';
			$result .= '<td class="team">'.$trophies.$team['name'].$field.'</td>';
			$result .= '<td class="names">'.$team['person1'].', '.$team['person2'].'</td>';
			$result .= '<td class="record">'.$team['wins'].' - '.$team['losses'].'</td>';
			// $result .= '<td class="round">'.$team['sos'].'</td>';
			// $result .= '<td class="round">'.$team['plus_minus'].'</td>';
			$result .= '<td class="round">'.$team['rounds'][1].'</td>';
			$result .= '<td class="round">'.$team['rounds'][2].'</td>';
			$result .= '<td class="round">'.$team['rounds'][3].'</td>';
			$result .= '<td class="round">'.$team['rounds'][4].'</td>';
			$result .= '<td class="round">'.$team['rounds'][5].'</td>';
			$result .= '</tr>';
		}
		if ($count < 28) {
			for ($i=0; $i<28-$count; $i++) {
				$result .= '<tr><td colspan="8">&nbsp;</td></tr>';
			}
		}
		$result .= '</tbody></table>';
	} else {
		// $result = '<tbody><tr><td colspan="10" style="text-align: center;"<h2><em>No teams checked in</em></h2></tr></tbody></table>';
		$result .= '<tbody><tr><td colspan="8" style="text-align: center;"<h2><em>No teams have checked in yet.</em></h2></tr>';
		for ($i=0; $i<27; $i++) {
			$result .= '<tr><td colspan="8">&nbsp;</td></tr>';
		}
		$result .= '</tbody></table>';
	}
}

echo $result."\n\n";
flush();


?>