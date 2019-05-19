<?php

$game1 = 'game1';
$game1_t1 = array('name' => '&nbsp;');
$game1_t2 = array('name' => '&nbsp;');
$game2 = 'game2';
$game2_t1 = array('name' => '&nbsp;');
$game2_t2 = array('name' => '&nbsp;');
$game3 = 'game3';
$game3_t1 = array('name' => '&nbsp;');
$game3_t2 = array('name' => '&nbsp;');
$game4 = 'game4';
$game4_t1 = array('name' => '&nbsp;');
$game4_t2 = array('name' => '&nbsp;');
$game5 = 'game5';
$game5_t1 = array('name' => '&nbsp;');
$game5_t2 = array('name' => '&nbsp;');
$game6 = 'game6';
$game6_t1 = array('name' => '&nbsp;');
$game6_t2 = array('name' => '&nbsp;');
$game7 = 'game7';
$game7_t1 = array('name' => '&nbsp;');
$game7_t2 = array('name' => '&nbsp;');
$game8 = 'game8';
$game8_t1 = array('name' => '&nbsp;');
$game8_t2 = array('name' => '&nbsp;');
$game9 = 'game9';
$game9_t1 = array('name' => '&nbsp;');
$game9_t2 = array('name' => '&nbsp;');
$game10 = 'game10';
$game10_t1 = array('name' => '&nbsp;');
$game10_t2 = array('name' => '&nbsp;');
$game11 = 'game11';
$game11_t1 = array('name' => '&nbsp;');
$game11_t2 = array('name' => '&nbsp;');
$game12 = 'game12';
$game12_t1 = array('name' => '&nbsp;');
$game12_t2 = array('name' => '&nbsp;');
$game13 = 'game13';
$game13_t1 = array('name' => '&nbsp;');
$game13_t2 = array('name' => '&nbsp;');
$game14 = 'game14';
$game14_t1 = array('name' => '&nbsp;');
$game14_t2 = array('name' => '&nbsp;');
$winner = array('name' => '&nbsp;');

$team_data = get_team_data();
if (!empty($team_data)) {
	$game1_t1 = $team_data[8]; // Team 9
	$game1_t2 = $team_data[7]; // Team 8
	$game2_t1 = $team_data[3]; // Team 4
	$game2_t2 = $team_data[12]; // Team 13
	$game3_t1 = $team_data[11]; // Team 12
	$game3_t2 = $team_data[4]; // Team 5
	$game4_t1 = $team_data[5]; // Team 6
	$game4_t2 = $team_data[10]; // Team 11
	$game5_t1 = $team_data[13]; // Team 14
	$game5_t2 = $team_data[2]; // Team 3
	$game6_t1 = $team_data[6]; // Team 7
	$game6_t2 = $team_data[9]; // Team 10
	$game7_t1 = $team_data[0]; // Team 1
	$game7_t2 = get_winner($game1_t1['id'], 6);
	$game8_t1 = get_winner($game2_t1['id'], 6);
	$game8_t2 = get_winner($game3_t1['id'], 6);
	$game9_t1 = get_winner($game4_t1['id'], 6);
	$game9_t2 = get_winner($game5_t1['id'], 6);
	$game10_t1 = get_winner($game6_t1['id'], 6);
	$game10_t2 = $team_data[1]; // Team 2
	$game11_t1 = get_winner($game7_t1['id'], 7);
	$game11_t2 = get_winner($game8_t1['id'], 7);
	$game12_t1 = get_winner($game9_t1['id'], 7);
	$game12_t2 = get_winner($game10_t1['id'], 7);
	$game13_t1 = get_winner($game11_t1['id'], 8, true);
	$game13_t2 = get_winner($game12_t1['id'], 8, true);
	$game14_t1 = get_winner($game11_t1['id'], 8);
	$game14_t2 = get_winner($game12_t1['id'], 8);
	$winner = get_winner($game14_t1['id'], 10);

	$game1 = get_field($game1_t1['id'], 6);
	$game2 = get_field($game2_t1['id'], 6);
	$game3 = get_field($game3_t1['id'], 6);
	$game4 = get_field($game4_t1['id'], 6);
	$game5 = get_field($game5_t1['id'], 6);
	$game6 = get_field($game6_t1['id'], 6);
	$game7 = get_field($game7_t1['id'], 7);
	$game8 = get_field($game8_t1['id'], 7);
	$game9 = get_field($game9_t1['id'], 7);
	$game10 = get_field($game10_t1['id'], 7);
	$game11 = get_field($game11_t1['id'], 8);
	$game12 = get_field($game12_t1['id'], 8);
	$game13 = get_field($game13_t1['id'], 9);
	$game14 = get_field($game14_t1['id'], 10);

}

$result = 'data: <table class="table table-condensed" id="tournament" border="0" cellpadding="0" cellspacing="0"><thead><tr><th>First Rd</th><th>Quarters</th><th>Semis</th><th>Finals</th><th>Winner</th></tr></thead>';
$result .= '<tbody>';
$result .= '<tr><td>&nbsp;</td><td class="underline">1. <span class="'.get_css($game7_t1['id'],7).'">'.$game7_t1['name'].'</span></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>';
$result .= '<tr><td>&nbsp;</td><td class="lineright">&nbsp;</td><td class="underline"><span class="'.get_css($game11_t1['id'],8).'">'.$game11_t1['name'].'</span></td><td>&nbsp;</td><td>&nbsp;</td></tr>';
$result .= '<tr><td class="underline">9. <span class="'.get_css($game1_t1['id'],6).'">'.$game1_t1['name'].'</span></td><td class="lineright field">'.$game7.'</td><td class="lineright">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>';
$result .= '<tr><td class="lineright field">'.$game1.'</td><td class="underline lineright"><span class="'.get_css($game7_t2['id'],7).'">'.$game7_t2['name'].'</span></td><td class="lineright">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>';
$result .= '<tr><td class="underline lineright">8. <span class="'.get_css($game1_t2['id'],6).'">'.$game1_t2['name'].'</span></td><td>&nbsp;</td><td class="lineright">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>';
$result .= '<tr><td>&nbsp;</td><td>&nbsp;</td><td class="lineright field">'.$game11.'</td><td class="underline"><span class="'.get_css($game14_t1['id'],10).'">'.$game14_t1['name'].'</span></td><td>&nbsp;</td></tr>';
$result .= '<tr><td class="underline">4. <span class="'.get_css($game2_t1['id'],6).'">'.$game2_t1['name'].'</span></td><td>&nbsp;</td><td class="lineright">&nbsp;</td><td class="lineright">&nbsp;</td><td>&nbsp;</td></tr>';
$result .= '<tr><td class="lineright field">'.$game2.'</td><td class="underline"><span class="'.get_css($game8_t1['id'],7).'">'.$game8_t1['name'].'</span></td><td class="lineright">&nbsp;</td><td class="lineright">&nbsp;</td><td>&nbsp;</td></tr>';
$result .= '<tr><td class="underline lineright">13. <span class="'.get_css($game2_t2['id'],6).'">'.$game2_t2['name'].'</span></td><td class="lineright">&nbsp;</td><td class="lineright">&nbsp;</td><td class="lineright">&nbsp;</td><td>&nbsp;</td></tr>';
$result .= '<tr><td>&nbsp;</td><td class="lineright field">'.$game8.'</td><td class="underline lineright"><span class="'.get_css($game11_t2['id'],8).'">'.$game11_t2['name'].'</span></td><td class="lineright">&nbsp;</td><td>&nbsp;</td></tr>';
$result .= '<tr><td class="underline">12. <span class="'.get_css($game3_t1['id'],6).'">'.$game3_t1['name'].'</span></td><td class="lineright">&nbsp;</td><td>&nbsp;</td><td class="lineright">&nbsp;</td><td>&nbsp;</td></tr>';
$result .= '<tr><td class="lineright field">'.$game3.'</td><td class="underline lineright"><span class="'.get_css($game8_t2['id'],7).'">'.$game8_t2['name'].'</span></td><td>&nbsp;</td><td class="lineright">&nbsp;</td><td>&nbsp;</td></tr>';
$result .= '<tr><td class="underline lineright">5. <span class="'.get_css($game3_t2['id'],6).'">'.$game3_t2['name'].'</span></td><td>&nbsp;</td><td>&nbsp;</td><td class="lineright">&nbsp;</td><td>&nbsp;</td></tr>';
$result .= '<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td class="lineright field">'.$game14.'</td><td class="underline">'.$winner['name'].'</td></tr>';
$result .= '<tr><td class="underline">6. <span class="'.get_css($game4_t1['id'],6).'">'.$game4_t1['name'].'</span></td><td>&nbsp;</td><td>&nbsp;</td><td class="lineright">&nbsp;</td><td>&nbsp;</td></tr>';
$result .= '<tr><td class="lineright field">'.$game4.'</td><td class="underline"><span class="'.get_css($game9_t1['id'],7).'">'.$game9_t1['name'].'</span></td><td>&nbsp;</td><td class="lineright">&nbsp;</td><td>&nbsp;</td></tr>';
$result .= '<tr><td class="underline lineright">11. <span class="'.get_css($game4_t2['id'],6).'">'.$game4_t2['name'].'</span></td><td class="lineright">&nbsp;</td><td>&nbsp;</td><td class="lineright">&nbsp;</td><td>&nbsp;</td></tr>';
$result .= '<tr><td>&nbsp;</td><td class="lineright">&nbsp;</td><td class="underline"><span class="'.get_css($game12_t1['id'],8).'">'.$game12_t1['name'].'</span></td><td class="lineright">&nbsp;</td><td>&nbsp;</td></tr>';
$result .= '<tr><td class="underline">14. <span class="'.get_css($game5_t1['id'],6).'">'.$game5_t1['name'].'</span></td><td class="lineright field">'.$game9.'</td><td class="lineright">&nbsp;</td><td class="lineright">&nbsp;</td><td>&nbsp;</td></tr>';
$result .= '<tr><td class="lineright field">'.$game5.'</td><td class="underline lineright"><span class="'.get_css($game9_t2['id'],7).'">'.$game9_t2['name'].'</span></td><td class="lineright">&nbsp;</td><td class="lineright">&nbsp;</td><td>&nbsp;</td></tr>';
$result .= '<tr><td class="underline lineright">3. <span class="'.get_css($game5_t2['id'],6).'">'.$game5_t2['name'].'</span></td><td>&nbsp;</td><td class="lineright">&nbsp;</td><td class="lineright">&nbsp;</td><td>&nbsp;</td></tr>';
$result .= '<tr><td>&nbsp;</td><td>&nbsp;</td><td class="lineright field">'.$game12.'</td><td class="underline lineright"><span class="'.get_css($game14_t2['id'],10).'">'.$game14_t2['name'].'</span></td><td>&nbsp;</td></tr>';
$result .= '<tr><td class="underline">7. <span class="'.get_css($game6_t1['id'],6).'">'.$game6_t1['name'].'</span></td><td>&nbsp;</td><td class="lineright">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>';
$result .= '<tr><td class="lineright field">'.$game6.'</td><td class="underline"><span class="'.get_css($game10_t1['id'],7).'">'.$game10_t1['name'].'</span></td><td class="lineright">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>';
$result .= '<tr><td class="underline lineright">10. <span class="'.get_css($game6_t2['id'],6).'">'.$game6_t2['name'].'</span></td><td class="lineright">&nbsp;</td><td class="lineright">&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>';
$result .= '<tr><td>&nbsp;</td><td class="lineright field">'.$game10.'</td><td class="underline lineright"><span class="'.get_css($game12_t2['id'],8).'">'.$game12_t2['name'].'</span></td><td>&nbsp;</td><td>&nbsp;</td></tr>';
$result .= '<tr><td>&nbsp;</td><td class="underline lineright">2. <span class="'.get_css($game10_t2['id'],7).'">'.$game10_t2['name'].'</span></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>';
$result .= '<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>';
$result .= '<tr><td colspan="2" style="text-align: right;">3rd Place</td><td class="underline"><span class="'.get_css($game13_t1['id'],9).'">'.$game13_t1['name'].'</span></td><td class="field">'.$game13.'</td><td class="underline"><span class="'.get_css($game13_t2['id'],9).'">'.$game13_t2['name'].'</span></td></tr>';
$result .= '</tbody></table>';

?>