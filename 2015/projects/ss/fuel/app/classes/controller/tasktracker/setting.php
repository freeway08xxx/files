<?php

/**
 * フォームテンプレート画面 コントローラ
 * ※AngularJSから $routeProvider 経由でAJAX呼び出し
 *
 * @return HTML_View
 */
class Controller_Tasktracker_Setting extends Controller_Tasktracker_Base
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
		$this->view->set_filename('tasktracker/setting/index');
		$this->response($this->view);
	}

	/**
	 * 基本画面のロード
	 *
	 * @access public
	 */
	public function action_task() {
		## レポート作成画面
		$this->view->set_filename('tasktracker/setting/task/index');
		$this->response($this->view);
	}

	/**
	 * 基本画面のロード
	 *
	 * @access public
	 */
	public function action_add_task() {
		## レポート作成画面
		$this->view->set_filename('tasktracker/setting/task/add');
		$this->response($this->view);
	}

	/**
	 * 基本画面のロード
	 *
	 * @access public
	 */
	public function action_task_detail() {
		## レポート作成画面
		$this->view->set_filename('tasktracker/setting/task/detail');
		$this->response($this->view);
	}

	/**
	 * 基本画面のロード
	 *
	 * @access public
	 */
	public function action_add_process() {
		## レポート作成画面
		$this->view->set_filename('tasktracker/setting/process/add');
		$this->response($this->view);
	}

	/**
	 * 基本画面のロード
	 *
	 * @access public
	 */
	public function action_arujo() {
		## レポート作成画面
		$this->view->set_filename('tasktracker/setting/arujo/index');
		$this->response($this->view);
	}

	/**
	 * ARUJO追加画面の取得 
	 * 
	 * @access public
	 * @return void
	 */
	public function action_add_arujo(){
		$this->view->set_filename('tasktracker/setting/arujo/add');
		$this->response($this->view);
	}

	/**
	 * タスクの追加 
	 * 
	 * @access public
	 * @return void
	 */
	public function action_add_task_save() {
		$category_id 	= Input::json('category_id');
		$name 			= Input::json('name');

		$set = array(
			'task_name' => $name,
			'task_category_id' => $category_id
		);
		Model_Data_Tasktracker_Task_Master::insert($set);
	}

	/**
	 * action_remove_task
	 * 
	 * @param mixed $task_id 
	 * @access public
	 * @return void
	 */
	public function action_remove_task($task_id) {
		if(empty($task_id)){
			// TODO エラー
			return false;
		}
		Model_Data_Tasktracker_Task_Master::delete_by_id($task_id);
	}

	/**
	 * プロセスの追加 
	 * 
	 * @access public
	 * @return void
	 */
	public function action_add_process_save($task_master_id) {
		$id				= Input::json('id');
		$name 			= Input::json('name');
		$main_process 	= Input::json('main_process');

		if(empty($id)){
			// 新規登録
			$set = array(
				'task_master_id' => $task_master_id,
				'process_name' => $name,
			);
			// まだ一件も登録されていない場合は最初をメインプロセスとする
			$process_master = Model_Data_Tasktracker_Process_Master::get_by_task_master_id($task_master_id);
			if(empty($process_master)){
				$set['main_process'] = true;
			}
			Model_Data_Tasktracker_Process_Master::insert($set);
		} else {
			$set = array(
				'process_name' => $name,
			);
			if(!empty($set)){
				Model_Data_Tasktracker_Process_Master::update($id, $set);
			}
		}
	}

	/**
	 * メインプロセスにする 
	 * 
	 * @param mixed $id 
	 * @access public
	 * @return void
	 */
	public function action_select_main_process($id) {
		$select_process = Model_Data_Tasktracker_Process_Master::get_one_by_id($id);
		Model_Data_Tasktracker_Process_Master::update_by_task_master_id($select_process['task_master_id'], array('main_process'=> false) );
		Model_Data_Tasktracker_Process_Master::update($id, array('main_process'=> true) );
	}

	/**
	 * action_remove_process
	 * 
	 * @param mixed $process_master_id 
	 * @access public
	 * @return void
	 */
	public function action_remove_process($id) {
		if(empty($id)){
			// TODO エラー
			return false;
		}
		$select_process = Model_Data_Tasktracker_Process_Master::get_one_by_id($id);
		if(empty($select_process) || $select_process['main_process']){
			//TODO エラー
			return false;
		}
		
		Model_Data_Tasktracker_Process_Master::delete_by_id($id);
	}

	/**
	 * post_add_arujo_save
	 * 
	 * @param mixed $task_master_id 
	 * @access public
	 * @return void
	 */
	public function action_add_arujo_save($task_master_id) {
		$id = Input::json('id');
		$client_id 	= Input::json('client_id');
		$media_id 	= Input::json('media_id');
		$name 		= Input::json('name');
		$value 		= Input::json('value');
		$description = Input::json('arujo_description');

		$set = array(
			'task_master_id' => $task_master_id,
			'media_id' => $media_id,
			'name' => $name,
			'value' => $value,
			'arujo_description' => $description
		);

		if(empty($client_id)) {
			// ARUJO マスターが対象
			if(!empty($id)) {
				// 更新
				Model_Data_Tasktracker_Task_Master_Arujo::update($id, $set);
			}else{
				// 追加
				Model_Data_Tasktracker_Task_Master_Arujo::insert($set);
			}
		}else{
			// ARUJO クライアントマスターが対象
			$set['client_id'] = $client_id;
			$set['task_master_arujo_id'] = !empty($id)? $id : null;

			$task_master_arujo_client_id = Input::json('task_master_arujo_client_id');
			if(!empty($task_master_arujo_client_id)){
				// 更新
				Model_Data_Tasktracker_Task_Master_Arujo_Client::update($task_master_arujo_client_id, $set);
			}else{
				// 追加
				Model_Data_Tasktracker_Task_Master_Arujo_Client::insert($set);
			}
		}
	}

	/**
	 * action_remove_arujo
	 * 
	 * @param mixed $arujo_id 
	 * @access public
	 * @return void
	 */
	public function action_remove_arujo() {
		$id = Input::json('id');
		if(!empty($id)){
			Model_Data_Tasktracker_Task_Master_Arujo::delete_by_id($id);
		}
		
		$task_master_arujo_client_id = Input::json('task_master_arujo_client_id');
		if (!empty($task_master_arujo_client_id)) {
			Model_Data_Tasktracker_Task_Master_Arujo_Client::delete_by_id($task_master_arujo_client_id);
		}
	}


	/**
	 * action_add_arujo_client
	 * 
	 * @access public
	 * @return void
	 */
	public function action_add_arujo_client(){
		$this->view->set_filename('tasktracker/setting/arujo/addclient');
		$this->response($this->view);
	}
}
