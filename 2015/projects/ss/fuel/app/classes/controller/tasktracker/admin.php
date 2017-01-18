<?php
class Controller_Tasktracker_Admin extends Controller_Tasktracker_Base
{
	/*========================================================================*/
	/* 前提共通処理
	/*========================================================================*/
	public function before() {
		## super
		parent::before();
	}

	/**
	 * 基本画面のロード
	 *
	 * @access public
	 */
	public function action_index() {
		## レポート作成画面
		$this->view->set_filename('tasktracker/admin/index');
		$this->response($this->view);
	}
}
