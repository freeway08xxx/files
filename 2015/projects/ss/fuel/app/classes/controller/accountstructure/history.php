<?php
class Controller_AccountStructure_History extends Controller_AccountStructure_Base {

	/*========================================================================*/
	/* 前提共通処理
	/*========================================================================*/
	public function before() {

		## super
		parent::before();

	}

	/*========================================================================*/
	/* 履歴画面出力
	/*========================================================================*/
	public function action_index() {
		
		$this->view->set_filename("accountstructure/history/index");
		
		## angular $routeProvider 経由で出力
		$this->response($this->view);
	}
}
