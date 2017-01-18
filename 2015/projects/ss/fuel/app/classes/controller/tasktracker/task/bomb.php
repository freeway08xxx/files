<?php

/**
 * フォームテンプレート画面 コントローラ
 * ※AngularJSから $routeProvider 経由でAJAX呼び出し
 *
 * @return HTML_View
 */
class Controller_Tasktracker_Task_Bomb extends Controller_Tasktracker_Base
{
	/*========================================================================*/
	/* 前提共通処理
	/*========================================================================*/
	public function before() {
		## super
		parent::before();
	}

	/**
	 * action_index
	 * 
	 * @access public
	 * @return void
	 */
	public function action_index() {
		$this->view->set_filename('tasktracker/task/bomb/index');
		$this->response($this->view);
	}

	/**
	 * action_get
	 * 
	 * @param mixed $process_id 
	 * @access public
	 * @return void
	 */
	public function action_get($process_id) {
		$bombs = Model_Data_Tasktracker_Process_Bomb::get_by_process_id($process_id);
		if(empty($bombs)){
			return $bombs;
		}
		$user_ids = Arr::pluck($bombs, 'created_user');
		$users = Model_Mora_User::find_by_user_ids($user_ids);
		$users = Arr::assoc_to_keyval($users, 'id', 'user_name');

		foreach($bombs as $key => $value){

			## 登録者の名前追加
			$value['user_name'] = $users[$value['created_user']];
			$bombs[$key] = $value;
		}
		$response['bombs'] = $bombs;
		return $this->response($response); 
	}

	/**
	 * ボム登録 
	 * 
	 * @param mixed $process_id 
	 * @access public
	 * @return void
	 */
	public function action_save($process_id) {
		$process = Model_Data_Tasktracker_Process::get_one_by_id($process_id);
		// TODO 権限チェック

		$set = array(
			'process_id' => $process_id,
			'bomb_description' => Input::json('bomb_description'),
		);
		$insert = Model_Data_Tasktracker_Process_Bomb::insert($set);
		Model_Data_Tasktracker_Process::update_bomb_flg($process_id, 1);
	}
}
