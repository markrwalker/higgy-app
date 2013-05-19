<?php
class page_scores extends Page {
    function init() {
        parent::init();

        $pts_for = 0;
        $pts_less = 0;
        $wins = 0;
        $losses = 0;

        $frame = $this->add('Frame');

        $me = $this->add('Model_Team')->tryLoad('1');
        $my_teams = $this->add('Model_Team')->addCondition('division_id',$me['division_id']);
        $my_games1 = $this->add('Model_Game')->addCondition('team1_id',$me['id'])->addCondition('is_complete',true);
        $my_games2 = $this->add('Model_Game')->addCondition('team2_id',$me['id'])->addCondition('is_complete',true);

        foreach ($my_games1 as $game) {
            $pts_for += $game['team1_score'];
            $pts_less += $game['team2_score'];
            if ($game['team1_score'] > $game['team2_score']) {
                $wins++;
            } else {
                $losses++;
            }
        }

        foreach ($my_games2 as $game) {
            $pts_for += $game['team2_score'];
            $pts_less += $game['team1_score'];
            if ($game['team2_score'] > $game['team1_score']) {
                $wins++;
            } else {
                $losses++;
            }
        }

        $frame->add('H2')->set($me['name'].' ('.$wins.' - '.$losses.')');
        $frame->add('Html')->set('<p>Wins: '.$wins.'<br />Losses: '.$losses.'<br />Points for: '.$pts_for.'<br />Points against: '.$pts_less.'</p>');

        foreach ($my_teams as $team) {
            if ($team['name'] != $me['name']) {
                $frame->add('P')->set($team['name']);
            }
        }

    }
}
