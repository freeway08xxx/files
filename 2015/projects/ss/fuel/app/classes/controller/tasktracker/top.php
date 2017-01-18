<?php

/**
 * フォームテンプレート画面 コントローラ
 * ※AngularJSから $routeProvider 経由でAJAX呼び出し
 *
 * @return HTML_View
 */
class Controller_Tasktracker_Top extends Controller_Tasktracker_Base
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
		$this->view->set_safe("view_task", View::forge("tasktracker/top/task/index"));
		$this->view->set_safe("view_process", View::forge("tasktracker/top/process/index"));
		$this->view->set_safe("view_detail", View::forge("tasktracker/top/detail/index"));
		$this->view->set_filename('tasktracker/top/index');
		$this->response($this->view);
	}
}
