<?php

class Controller_Tasktracker_Top_Detail extends Controller_Tasktracker_Base
{


	CONST DOWNLOAD_URL_BASE = '/sem/new/tasktracker/common/download/';

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
	public function action_content($task_id) {
		// $task_idが存在するか、もしくは正しいか
		if(empty($task_id)){
			return false;
		}

		$task 					= Model_Data_Tasktracker_Task::get_one_by_id($task_id);
		$task_setting 			= Model_Data_Tasktracker_Task_Setting::get_one_by_id($task['task_setting_id']);
		$task_setting_account 	= Model_Data_Tasktracker_Task_Setting_Account::get_by_task_setting_id($task['task_setting_id']);
		$task_master 			= Model_Data_Tasktracker_Task_Master::get_one_by_id($task_setting['task_master_id']);

		foreach($task_setting_account as $key => $value){
			$task_setting_account[$key]['media_name'] = Util_Tasktracker_Common::get_media_name($value['media_id']);
		}
		$arujo_list				= $this->_get_arujo_list($task['task_setting_id'], $task_setting['task_master_id'], $task_setting['client_id']);
		$process_list			= $this->_get_process_list($task_id, $task['owner_user_id']);
		$file_list 				= $this->_get_file_list($task['task_setting_id'], $task_setting['task_master_id']);

		$task['category'] 			= $task_master['task_category_id'];
		$task['task_name'] 			= $task_setting['task_name'];
		$task['task_description'] 	= $task_setting['task_description'];
		$task['job_type'] 			= $task_setting['job_type'];
		$task['routine'] 			= $task_setting['routine_setting'];
		$task['client_id'] 			= $task_setting['client_id'];
		$task['client_name'] 		= Util_Common_Client::get_client_name($task_setting['client_id']);
		$task['file_name'] 			= !empty($task_setting_file)? $task_setting_file['file_name'] : '';
		$task['file_path'] 			= !empty($task_setting_file)? $task_setting_file['file_name'] : ''; //TODO 修正
		$task['owner_user_name'] 	= $this->user_list[$task['owner_user_id']]['user_name'];

		$response['task'] = $task;
		$response['account_list'] 	= $task_setting_account;
		$response['process_list'] 	= $process_list;
		$response['file_list'] 		= $file_list;
		$response['arujo_list'] 	= $arujo_list;

		return $this->response($response); 
	}


	/**
	 * ARUJO一覧取得 
	 *
	 *
	 * 設定にあるARUJOとマスターのみにあるARUJOを組み合わせて取得
	 * 
	 * @param mixed $task_setting_id 
	 * @param mixed $task_master_id 
	 * @param mixed $client_id 
	 * @access private
	 * @return void
	 */
	private function _get_arujo_list($task_setting_id, $task_master_id, $client_id) {
		$task_setting_arujo 	= Model_Data_Tasktracker_Task_Setting_Arujo::get_by_task_setting_id($task_setting_id);
		$task_master_arujo		= Model_Data_Tasktracker_Task_Master_Arujo::get_by_task_master_id_join_client($task_master_id, $client_id);
		//$task_master_arujo		= Util_Common_Array::attach_key ($task_master_arujo, 'id');

		foreach($task_setting_arujo as $key => $value){
			foreach($task_master_arujo as $master_arujo_key => $msater_arujo){

				## 設定にARUJOマスターのIDがあり、設定とマスターIDが同じの場合はマスターから削除
				if(!empty($value['task_master_arujo_id']) && !empty($msater_arujo['id']) && $value['task_master_arujo_id'] == $msater_arujo['id'] ) {
					unset($task_master_arujo[$master_arujo_key]);
					break;
				}

				## 設定にARUJOクライアントマスターIDがあり、設定とクライアントマスターIDが同じ場合にマスターから削除
				if(!empty($value['task_master_arujo_client_id']) && !empty($msater_arujo['task_master_arujo_client_id']) && $value['task_master_arujo_client_id'] == $msater_arujo['task_master_arujo_client_id']) {
					unset($task_master_arujo[$master_arujo_key]);
					break;
				}
			}

			$task_setting_arujo[$key] = $value;
		}

		if(!empty($task_master_arujo)){
			foreach($task_master_arujo as $key => $value) {
				$value['task_master_arujo_id'] = $value['id']; 
				unset($value['id']); //IDは別の意味になるので削除
				$task_setting_arujo[] = $value;
			}
		}

		return $task_setting_arujo;
	}


	/**
	 * プロセス一覧取得 
	 * 
	 * @param mixed $task_id 
	 * @param mixed $task_owner_user_id 
	 * @access private
	 * @return void
	 */
	private function _get_process_list($task_id, $task_owner_user_id) {

		## プロセス取得
		$processes 				= Model_Data_Tasktracker_Process::get_by_task_id($task_id);
		## 検索用ユーザーId配列作成
		$query_user_ids[$task_owner_user_id] = $task_owner_user_id;
		$query_user_ids = array_merge($query_user_ids, array_column($processes,'owner_user_id','owner_user_id'));
		## ユーザー取得
		$users = Model_Mora_User::find_by_user_ids($query_user_ids);
		$this->_set_user_list($users);

		## プロセス情報追加
		foreach($processes as $key => $value){

			## プロセスファイル情報追加
			$file = Model_Data_Tasktracker_Process_File::get_one_by_process_id($value['id']);
			if(!empty($file)){
				$file['download_link'] = self::DOWNLOAD_URL_BASE.'?category='.TSKT_FILE_CATEGORY_PROCESS.'&process_file_id='.$file['id'];
				$value['file'] = $file;
			}

			## BOBM情報取得
			if($value['process_bomb_flg']){
				$process_bombs = Model_Data_Tasktracker_Process_Bomb::get_by_process_id($value['id']);
				$value['process_bomb_count'] = count($process_bombs);
			}

			## プロセス名取得
			$process_setting 	= Model_Data_Tasktracker_Process_Setting::get_one_by_id($value['process_setting_id']);

			$value['process_name'] = $process_setting['process_name'];
			$value['owner_user_name'] = $this->user_list[$value['owner_user_id']]['user_name'];
			$value['main_process'] = $process_setting['main_process'];
			## 遅延日数
			//$value['delay'] = Util_Tasktracker_Common::get_delay_date ($value['process_end_datetime'], $value['task_limit_datetime']);

			$processes[$key] = $value;
		}

		return $processes;
	}

	/**
	 * ファイル一覧取得 
	 * 
	 * @param mixed $task_setting_id 
	 * @param mixed $task_master_id 
	 * @access private
	 * @return void
	 */
	private function _get_file_list($task_setting_id, $task_master_id) {
		$task_setting_files 	= Model_Data_Tasktracker_Task_Setting_File::get_by_task_setting_id($task_setting_id);
		$task_master_files 		= Model_Data_Tasktracker_Task_Master_File::get_by_task_master_id($task_master_id);
		## ダウンロード一覧作成
		$file_list = array();
		foreach($task_master_files as $file){
			$file['download_link'] = self::DOWNLOAD_URL_BASE.'?category='.TSKT_FILE_CATEGORY_MASTER.'&task_master_file_id='.$file['id'];
			$file['owner_user_name'] = 'マスター';
			$file_list[] = $file;
		}
		foreach($task_setting_files as $file){
			$file['download_link'] = self::DOWNLOAD_URL_BASE.'?category='.TSKT_FILE_CATEGORY_SETTING.'&setting_file_id='.$file['id'];
			$file['owner_user_name'] = !empty($this->user_list[$file['created_user']])? $this->user_list[$file['created_user']]['user_name']:'';
			$file_list[] = $file;
		}

		return $file_list;
	}

	/**
	 * _set_user_list
	 * 
	 * @param mixed $user_list 
	 * @access private
	 * @return void
	 */
	private function _set_user_list($user_list) {
		$this->user_list = $user_list;
	}
}
