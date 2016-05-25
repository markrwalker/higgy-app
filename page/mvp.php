<?php
class page_mvp extends Page {
	function initMainPage() {
		parent::init();

		$q = $this->api->db->dsql();
		$q->table('year')->where('current',1)->field('id');
		$year_id = $q->getOne();

		$this->year_id = $year_id;

		$q = $this->api->db->dsql();
		$q->table('settings')->where('setting','mvp_voting')->field('value');
		$voting_enabled = $q->getOne();

		/**** Column 1 ****/
		$columns = $this->add('Columns');
		$col1 = $columns->addColumn(5)->add('Frame')->setTitle('Female Votes');
		$m = $this->add('Model_Vote');
		$m->setOrder('votes','desc');
		$female_grid = $col1->add('Grid');
		$female_grid->setModel($m)->addCondition('gender','F')->addCondition('year_id', $year_id);
		$female_grid->dq->group('name');
		$female_grid->js(true)->addClass('refresh_mvp_female_grid');
		$female_grid->js('refresh_mvp_female_grid', $female_grid->js()->reload());

		/**** Column 2 ****/
		$col2 = $columns->addColumn(5)->add('Frame')->setTitle('Male Votes');;
		$m = $this->add('Model_Vote');
		$m->setOrder('votes','desc');
		$male_grid = $col2->add('Grid');
		$male_grid->setModel($m)->addCondition('gender','M')->addCondition('year_id', $year_id);
		$male_grid->dq->group('name');
		$male_grid->js(true)->addClass('refresh_mvp_male_grid');
		$male_grid->js('refresh_mvp_male_grid', $male_grid->js()->reload());

		/**** Column 3 ****/
		$col3 = $columns->addColumn(2)->add('Frame');
		$crud = $col3->add('CRUD',array('allow_edit'=>false,'allow_add'=>false,'allow_del'=>false));
		$crud->setModel('Settings')->addCondition('setting', 'mvp_voting');
		if ($crud->grid) {
			$crud->grid->addColumn('button','toggle','Toggle');
			if($_GET['toggle']) {
				$crud->grid->model->toggleSetting($_GET['toggle']);
				$js[] = $crud->grid->js()->reload();
				$js[] = $crud->js(true)->_selector('.refresh_mvp_vote_form')->trigger('refresh_mvp_vote_form');
				$crud->js(null,$js)->execute();
			}
		}

		$vote_view = $col3->add('View');
		if ($voting_enabled) {
			$vote_form = $vote_view->add('Form');
			$vote_form->setModel('Vote');
			$vote_form->add('Html')->set('<h2>MVP Voting</h2>');
			$vote_form->getElement('player')->destroy();

			$allPlayers = array();
			$q = $this->api->db->dsql();
			$q->table('team')
				->where('year_id',$year_id)
				->where('checked_in',1)
				->where('dropped_out',0)
				->field('person1')
				->field('person1_gender')
				->field('person2')
				->field('person2_gender')
				->field('name')
			;
			$allPlayers = $q->get();
			$femalePlayers = array();
			$malePlayers = array();
			foreach ($allPlayers as $row) {
				if ($row['person1_gender'] == 'F') $femalePlayers[$row['person1'].' ('.$row['name'].')'] = $row['person1'].' ('.$row['name'].')';
				if ($row['person2_gender'] == 'F') $femalePlayers[$row['person2'].' ('.$row['name'].')'] = $row['person2'].' ('.$row['name'].')';
				if ($row['person1_gender'] == 'M') $malePlayers[$row['person1'].' ('.$row['name'].')'] = $row['person1'].' ('.$row['name'].')';
				if ($row['person2_gender'] == 'M') $malePlayers[$row['person2'].' ('.$row['name'].')'] = $row['person2'].' ('.$row['name'].')';
			}
			asort($femalePlayers);
			asort($malePlayers);
			$vote_form->addField('dropdown','vote_female')->setEmptyText('Choose one')->setValueList($femalePlayers);
			$vote_form->addField('dropdown','vote_male')->setEmptyText('Choose one')->setValueList($malePlayers);
			$vote_form->addSubmit();

			$vote_form->onSubmit(function($vote_form) {
				if(!empty($vote_form->data['vote_female'])) {
					$q = $this->api->db->dsql();
					$q->table('vote')
						->set('name',$vote_form->data['vote_female'])
						->set('gender','F')
						->set('year_id',$this->year_id)
						->set('ip',$_SERVER['REMOTE_ADDR'])
						->set('created',$q->expr('NOW()'))
						->do_insert()
					;
				}
				if(!empty($vote_form->data['vote_male'])) {
					$q = $this->api->db->dsql();
					$q->table('vote')
						->set('name',$vote_form->data['vote_male'])
						->set('gender','M')
						->set('year_id',$this->year_id)
						->set('ip',$_SERVER['REMOTE_ADDR'])
						->set('created',$q->expr('NOW()'))
						->do_insert()
					;
				}

				$js[] = $vote_form->js(true)->_selector('.refresh_mvp_female_grid')->trigger('refresh_mvp_female_grid');
				$js[] = $vote_form->js(true)->_selector('.refresh_mvp_male_grid')->trigger('refresh_mvp_male_grid');
				$js[] = $vote_form->js(true)->_selector('.refresh_mvp_vote_form')->trigger('refresh_mvp_vote_form');
				$vote_form->js(null,$js)->execute();

				return;
			});

		} else {
			$vote_view->add('Html')->set('&nbsp;');
		}
		$vote_view->js(true)->addClass('refresh_mvp_vote_form');
		$vote_view->js('refresh_mvp_vote_form', $vote_view->js()->reload());
	}
}