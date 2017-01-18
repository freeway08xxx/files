<?php
require_once(APPPATH."/const/tasktracker.php");

/**
 * タスク編集
 */
class Controller_Tasktracker_Task_Save extends Controller_Tasktracker_Base
{
	/*========================================================================*/
	/* 前提共通処理
	/*========================================================================*/
	public function before() {
		## super
		parent::before();
	}

	public function action_index() {
		$this->view->set_filename('tasktracker/task/save/index');
		$this->response($this->view);
	}

	/**
	 * action_save
	 * 
	 * @access public
	 * @return void
	 */
	public function action_save() {
		foreach(Input::post() as $key => $value){
			$post[$key] = json_decode($value,true);
		}

		// TODO 登録する権限があるか
		// TODO 送信情報に不足はないか

		## task_setting登録
		$routine_setting = '';
		if($post['jobType'] == TSKT_TASK_JOB_TYPE_ROUTINE){
			$routine_setting = $post['routine'];
			$routine_setting['limit'] = array(
				'hour' => $post['limit']['hour'],
				'minute' => $post['limit']['minute']
			);
		}
		$setting = array(
			'task_master_id' 	=> $post['taskMaster'],
			'task_name' 		=> $post['taskName'],
			'task_description' 	=> $post['description'],
			'client_id' 		=> $post['client']['id'],
			'job_type'  		=> $post['jobType'],
			'routine_setting'	=> json_encode($routine_setting),
			'owner_user_id' 	=> $this->user_id,
		);
		$insert = Model_Data_Tasktracker_Task_Setting::insert($setting);
		$setting_id = $insert[0];


		## task_setting_account登録
		foreach($post['accounts'] as $key => $value){
			$value = array(
				'task_setting_id' 	=> $setting_id,
				'media_id' 			=> $value['media_id'],
				'account_id' 		=> $value['account_id'],
			);
			$values[] = $value;
		}
		Model_Data_Tasktracker_Task_Setting_Account::insert($values);


		## task_setting_file登録
		if(!empty($_FILES['file'])){
			$file = array(
				'task_setting_id' => $setting_id,
				'file_name' => $_FILES['file']['name'],
			);

			$insert = Model_Data_Tasktracker_Task_Setting_File::insert($file);
			## ファイルアップ
			Util_Tasktracker_File::upload_file('file', TSKT_TASK_SETTING_FILES_PATH, $insert[0]);
		}

		## t_tasktracker_task_setting_arujo登録
		$sets= array();
		foreach($post['arujoMaster'] as $key => $value){
			$set = array(
				'task_setting_id' 		=> $setting_id,
				'task_master_arujo_id' 	=> !empty($value['id'])? $value['id']: null,
				'task_master_arujo_client_id' => !empty($value['task_master_arujo_client_id'])? $value['task_master_arujo_client_id']: null,
				'name' 					=> $value['name'],
				'value' 				=> $value['value'],
				'arujo_description' 	=> $value['arujo_description'],
				'media_id' 				=> $value['media_id'],
			);
			$sets[] = $set;
		}
		if(!empty($post['arujoAdd'])){
			foreach($post['arujoAdd'] as $key => $value){
				$set = array(
					'task_setting_id' 		=> $setting_id,
					'task_master_arujo_id' 	=> null,
					'task_master_arujo_client_id' => null,
					'name' 					=> $value['name'],
					'value' 				=> $value['value'],
					'arujo_description' 	=> $value['arujo_description'],
					'media_id' 				=> $value['media_id'],
				);
			}
			$sets[] = $set;
		}
		if(!empty($sets)){
			Model_Data_Tasktracker_Task_Setting_Arujo::insert($sets);
		}


		## task_process_setting登録
		foreach($post['processList'] as $key => $value){
			$routine_setting = '';
			if($post['jobType'] == TSKT_TASK_JOB_TYPE_ROUTINE){
				$routine_setting['start'] = array(
					'hour' => $value['process_start_datetime']['hour'],
					'minute' => $value['process_start_datetime']['minute']
				);
			}

			$process = array(
				'task_setting_id' 		=> $setting_id,
				'process_name' 			=> $value['process_name'],
				'process_description' 	=> '', // TODO プロセス詳細っているのか？
				'routine_setting'		=> json_encode($routine_setting),
				'priority' 				=> 0, // $value['priority'], //TODO いらない可能性あり
				'main_process'			=> $value['main_process'],
				'forecast_cost' 		=> $value['forecast_cost'],
				'owner_user_id' 		=> $value['user_id'],
			);
			$insert = Model_Data_Tasktracker_Process_Setting::insert($process);
			$post['processList'][$key]['process_setting_id'] = $insert[0];
		}


		## taskを登録
		$tasks = array();
		if($post['jobType'] == TSKT_TASK_JOB_TYPE_ROUTINE){
			//対象日時
			$regular_schedule = Util_Tasktracker_task::create_regular_schedule($post['routine']['frequency'], $post['routine']);
			foreach ($regular_schedule as $key => $value) {
				$limit_datetime  = new DateTime($value);
				$limit_datetime->setTime($post['limit']['hour'], $post['limit']['minute']);
				$limit_datetime = $limit_datetime->format(TSKT_DATE_FORMAT);

				$task = array(
					'task_setting_id' 	=> $setting_id,
					'task_status' 		=> TSKT_TASK_STATUS_INCOMP,
					'task_limit_datetime' 	=> $limit_datetime,
					'owner_user_id' 	=> $this->user_id, 
				);
				$tasks[] = $task;
			}
		}else{
			$limit_datetime  = new DateTime($post['limit']['date']);
			$limit_datetime->setTime($post['limit']['hour'], $post['limit']['minute']);
			$limit_datetime = $limit_datetime->format(TSKT_DATE_FORMAT);

			$task = array(
				'task_setting_id' 	=> $setting_id,
				'task_status' 		=> TSKT_TASK_STATUS_INCOMP,
				'task_limit_datetime' 	=> $limit_datetime,
				'owner_user_id' 	=> $this->user_id, 
			);
			$tasks[] = $task;
		}
		// ※ bulkinsertは最初のauto_increment値しか戻らないので
		// 通常のinsertを行う
		foreach($tasks as $key => $value){
			$insert = Model_Data_Tasktracker_Task::insert($value);
			$task_id = $insert[0];

			$processes = array();
			foreach($post['processList'] as $process){
				if($post['jobType'] == TSKT_TASK_JOB_TYPE_ROUTINE){
					$start_datetime  = new DateTime($value['task_limit_datetime']);
				}else{
					$start_datetime  = new DateTime($process['process_start_datetime']['date']);
				}

				$start_datetime->setTime($process['process_start_datetime']['hour'], $process['process_start_datetime']['minute']);

				$process = array(
					'task_id' => $task_id,
					'process_setting_id' => $process['process_setting_id'], //TODO テーブルがいらない可能性あり
					'process_status' => 1, //TODO 定数化,
					'process_start_datetime' 	=> $start_datetime->format(TSKT_DATE_FORMAT),
					'forecast_cost' => $process['forecast_cost'],
					'owner_user_id' => $process['user_id'],
				);
				$processes[] = $process;
			}

			## processを登録
			Model_Data_Tasktracker_Process::insert($processes);
		}
	}


	/**
	 * action_update
	 * 
	 * @param mixed $task_id 
	 * @access public
	 * @return void
	 */
	public function action_update($task_id) {
		$processList = json_decode(Input::post('processList'),true) ;
		$accounts = json_decode(Input::post('accounts'),true);
		$post_task = json_decode(Input::post('task'),true);
		$arujoList = json_decode(Input::post('arujoList'),true);

		//TODO 権限チェック
		//TODO 存在チェック プロセス、アカウント両方あるか

		$task = Model_Data_Tasktracker_Task::get_one_by_id($task_id);
		$task_setting_id = $task['task_setting_id'];
		$task_setting = Model_Data_Tasktracker_Task_Setting::get_one_by_id($task_setting_id);

		$is_routine = false;
		if($task_setting['job_type'] == TSKT_TASK_JOB_TYPE_ROUTINE){
			$is_routine = true;
		}

		$db = Database_Connection::instance('administrator');
		$db->start_transaction();
		try 
		{

			/**
			 * タスク設定更新 
		 	 */
			$routine_setting = json_decode($task_setting['routine_setting'],true);
			if($is_routine){
				$routine_setting['limit'] = array(
					'hour' => $post_task['task_limit_datetime']['hour'],
					'minute' => $post_task['task_limit_datetime']['minute']
				);
			}

			$set = array(
				'task_name' => $post_task['task_name'],
				'routine_setting'	=> json_encode($routine_setting),
				'task_description' => $post_task['description']
			);
			Model_Data_Tasktracker_Task_Setting::update($task_setting_id, $set);

			/**
			 * アカウント更新 
		 	 */
			Model_Data_Tasktracker_Task_Setting_Account::delete_by_setting_id($task_setting_id);
			foreach($accounts as $key => $value){
				$value = array(
					'task_setting_id' 	=> $task['task_setting_id'],
					'media_id' 			=> $value['media_id'],
					'account_id' 		=> $value['account_id'],
				);
				$values[] = $value;
			}
			// 新しいアカウント登録
			Model_Data_Tasktracker_Task_Setting_Account::insert($values);

			/**
			 * ARUJO更新 
		 	 */
			if(!empty($arujoList)){
				foreach($arujoList as $key => $value) {
					$set = array(
						'id'					=> !empty($value['id']) ?  $value['id']: null,
						'task_setting_id' 		=> $task_setting_id,
						'task_master_arujo_id' 	=> !empty($value['task_master_arujo_id']) ? $value['task_master_arujo_id'] : null,
						'task_master_arujo_client_id' => !empty($value['task_master_arujo_client_id']) ? $value['task_master_arujo_client_id'] : null,
						'name' 					=> $value['name'],
						'value' 				=> $value['value'],
						'arujo_description' 	=> $value['arujo_description'],
						'media_id' 				=> $value['media_id'],
					);
					$arujoList[$key] = $set;
				}
				Model_Data_Tasktracker_Task_Setting_Arujo::insert_duplicate($arujoList);
			}

			/**
			 * プロセスの更新は追加はあるものの削除はない。 
			 */
			foreach($processList as $key => $value){

				$routine_setting = json_decode($task_setting['routine_setting'],true);
				if($is_routine){
					$routine_setting['start'] = array(
						'hour' => $value['process_start_datetime']['hour'],
						'minute' => $value['process_start_datetime']['minute']
					);
				}

				$set = array(
					'id'					=> !empty($value['process_setting_id']) ? $value['process_setting_id'] : null,
					'task_setting_id'		=> $task_setting_id,
					'process_name' 			=> $value['process_name'],
					'process_description' 	=> !empty($value['process_description'])? $value['process_description']:'',
					'routine_setting'		=> json_encode($routine_setting),
					'priority' 				=> !empty($value['priority'])? $value['priority']:0,
					'main_process'			=> $value['main_process'],
					'forecast_cost' 		=> $value['forecast_cost'],
					'owner_user_id' 		=> $value['owner_user_id'],
				);

				if(!empty($value['process_setting_id'])) {
					## 更新処理
					Model_Data_Tasktracker_Process_Setting::update($value['process_setting_id'], $set);
				}
				else{
					## 追加処理
					$insert = Model_Data_Tasktracker_Process_Setting::insert($set);
					$processList[$key]['process_setting_id'] = $insert[0];
				}
			}

			$tasks = array();
			$processes = array();
			// 対象タスクを元にループ
			$targetTasks = Model_Data_Tasktracker_Task::get_by_setting_id($task_setting_id, $task['task_limit_datetime'], TSKT_TASK_STATUS_INCOMP);
			foreach($targetTasks as $key => $value){
				if($is_routine){
					//定例の場合はタスクの日付を
					$limit_datetime  = new DateTime($value['task_limit_datetime']);
				}else{
					//通常の場合は設定した日を
					$limit_datetime  = new DateTime($post_task['task_limit_datetime']['date']);
				}
				$limit_datetime->setTime($post_task['task_limit_datetime']['hour'], $post_task['task_limit_datetime']['minute']);
				$value['task_limit_datetime'] = $limit_datetime->format(TSKT_DATE_FORMAT);
				$tasks[] = $value; 

				foreach($processList as $process){
					if($is_routine){
						//定例の場合はタスクの日付を
						$start_datetime  = new DateTime($value['task_limit_datetime']);
					}else{
						//プロセスの場合は設定した日付を
						$start_datetime  = new DateTime($process['process_start_datetime']['date']);
					}
					$start_datetime->setTime($process['process_start_datetime']['hour'], $process['process_start_datetime']['minute']);

					$process = array(
						'task_id' => $value['id'],
						'process_setting_id' => $process['process_setting_id'], 
						'process_status' 			=> TSKT_TASK_STATUS_INCOMP,
						'process_start_datetime' 	=> $start_datetime->format(TSKT_DATE_FORMAT),
						'forecast_cost' => $process['forecast_cost'],
						'owner_user_id' => $process['owner_user_id'],
					);
					$processes[] = $process;
				}
			}

			## processを登録
			Model_Data_Tasktracker_Task::insert_duplicate($tasks);
			Model_Data_Tasktracker_Process::insert_duplicate($processes);

			## task_setting_file登録
			if(!empty($_FILES['file'])){
				$file = array(
					'task_setting_id' => $task_setting_id,
					'file_name' => $_FILES['file']['name'],
				);

				$insert = Model_Data_Tasktracker_Task_Setting_File::insert($file);
				## ファイルアップ
				Util_Tasktracker_File::upload_file('file', TSKT_TASK_SETTING_FILES_PATH, $insert[0]);
			}

			$db->commit_transaction();
		}
		catch (\Exception $ex)
		{
			$db->rollback_transaction();
			Log::debug(__METHOD__."(".__LINE__.") ----> ".print_r( $ex ,true));
			//TODO エラー
		}
	}


	/**
	 * タスクの削除を行う 
	 * 
	 * @param mixed $task_id 
	 * @access public
	 * @return void
	 */
	public function action_remove($task_id) {

		$task = Model_Data_Tasktracker_Task::get_one_by_id($task_id);
		//TODO 権限チェック

		## タスクの削除実行
		Model_Data_Tasktracker_Task::delete_by_id($task_id);

		## プロセスの削除実行
		Model_Data_Tasktracker_Process::delete_by_task_ids(array($task_id));
	}

	/**
	 * プロセスの削除を行う 
	 * 
	 * @param mixed $process_id 
	 * @access public
	 * @return void
	 */
	public function action_remove_process($process_id) {
		$process = Model_Data_Tasktracker_Process::get_one_by_id($process_id);
		//TODO 権限チェック

		$process_setting = Model_Data_Tasktracker_Process_Setting::get_one_by_id($process['process_setting_id']);
		if($process_setting['main_process'] == 1){
			//TODO メインプロセスは削除出来ない
			return false;
		}

		//ルーチン設定にdelete_flgを立てる
		$routine_setting = json_decode($process_setting['routine_setting'], true);
		$routine_setting['delete_flg'] = 1;
		$set = array(
			'routine_setting' => json_encode($routine_setting)
		);
		Model_Data_Tasktracker_Process_Setting::update($process['process_setting_id'], $set);

		//このプロセス以降の同じ
		Model_Data_Tasktracker_Process::delete_by_process_setting_id($process['process_setting_id'], $process['process_start_datetime'], TSKT_TASK_STATUS_INCOMP);
	}


	/**
	 * ルーチンの設定 
	 * 
	 * @param mixed $task_id 
	 * @access public
	 * @return void
	 */
	public function action_routine_setting($task_id) {
		$routine_end_date = Input::json('routineEndDate');
		$routine_end_date = date(TSKT_DATE_FORMAT, $routine_end_date);

		$task = Model_Data_Tasktracker_Task::get_one_by_id($task_id);

		$task_setting_id = $task['task_setting_id'];
		$task_setting = Model_Data_Tasktracker_Task_Setting::get_one_by_id($task_setting_id);

		// 定例の終了日登録
		$routine_setting = json_decode($task_setting['routine_setting'],true);
		$routine_setting['end_datetime'] = $routine_end_date;
		$set = array(
			'routine_setting' => json_encode($routine_setting)
		);
		Model_Data_Tasktracker_Task_Setting::update($task_setting_id, $set);

		// それ以降のタスクとプロセスを削除
		$targetTasks = Model_Data_Tasktracker_Task::get_by_setting_id($task_setting_id, $routine_end_date, TSKT_TASK_STATUS_INCOMP);
		if(!empty($targetTasks)){
			$task_ids = array_column($targetTasks, 'id');
			Model_Data_Tasktracker_Task::delete_by_id($task_ids);
			Model_Data_Tasktracker_Process::delete_by_task_ids($task_ids);
		}
	}
}
