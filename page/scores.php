<?php
class page_scores extends Page {
    function init(){
        parent::init();

        $view = $this->add('View');

        $all_teams = $this->add('Model_Team');
        $me = $all_teams->addCondition('name','Wedding Jeans');
        //$my_teams = $all_teams->addCondition('division_id',$me[division_id]);

        $view->add('H2')->set($me['name']);

    }
}
