<?php
class page_admin extends Page {
	function init(){
		parent::init();

		//$is_admin = $this->api->auth->model['is_admin'];
		/*if (!$is_admin) {
			$this->api->redirect('index');
		}*/

		$tabs = $this->add('Tabs');

		$tab = $tabs->addTab('Game Admin');
		$tab->add('CRUD')->setModel('Game');

		$tab = $tabs->addTab('Team Admin');
		$tab->add('CRUD')->setModel('Team');

		$tab = $tabs->addTab('Division Admin');
		$tab->add('CRUD')->setModel('Division');

		$tab = $tabs->addTab('Field Admin');
		$tab->add('CRUD')->setModel('Field');

		$tab = $tabs->addTab('Year Admin');
		$tab->add('CRUD')->setModel('Year');
	}
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