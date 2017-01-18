<?php

/**
 * /top/process
 */
class Controller_Tasktracker_Top_Process extends Controller_Tasktracker_Base
{
	/*========================================================================*/
	/* 前提共通処理
	/*========================================================================*/
	public function before() {
		## super
		parent::before();
	}

	/**
	 * ユーザーのプロセス情報を取得する 
	 * 
	 * @access public
	 * @return array 
	 */
	public function get_list() {
		$process_status = Input::param('status');
		$date_from_unix = Input::param('dateFrom');
		$date_to_unix = Input::param('dateTo');

		## unixTimeをdateに変換 (その際、検索ではその日も含めるので1日足す)
		$date_from = !empty($date_from_unix)? date( TSKT_DATE_FORMAT_DATEONLY ,$date_from_unix + 86400 ): null;
		$date_to = !empty($date_to_unix)? date( TSKT_DATE_FORMAT_DATEONLY ,$date_to_unix + 86400 ): null;

		// 自分ののプロセスを取得
		$process = Model_Data_Tasktracker_Process::get_process_by_user_and_status($this->user_id, $process_status, $date_from, $date_to);

		// 担当クライアント取得
		$clients = Util_Common_Client::get_clients_by_owner($this->user_id, Session::get('role_id_sem'));
		foreach($process as $key => $value){
			if(empty($clients[$value['client_id']])){
				continue;
			}

			## 遅延日数
			$value['delay'] = Util_Tasktracker_Common::get_delay_date ($value['process_end_datetime'], $value['task_limit_datetime']);

			## クライアント名
			$value['client_name'] = Util_Common_Client::get_client_name($value['client_id'], $clients[$value['client_id']]);

			$process[$key] = $value;
		}

		$response['process'] = $process;
		return $this->response($response); 
	}

	/**
	 * 特定プロセスのステータス情報を変更する 
	 * 
	 * @access public
	 * @return void
	 */
	public function action_update_status($process_id) {
		$update_status = Input::json('status');

		// TODO 更新する権限があるか

		// プロセス取得
		$process = Model_Data_Tasktracker_Process::get_one_by_id($process_id);
		$task = Model_Data_Tasktracker_Task::get_one_by_id($process['task_id']);

		## 更新するステータスが完了
		if($update_status == TSKT_TASK_STATUS_COMP){

			## 更新前ステータスが未完了ではない
			if($process['process_status'] != TSKT_TASK_STATUS_INCOMP){
				//TODO エラー,もしくは何もしない
				return false;
			}

			## 他プロセスがすべて終了しているかチェック
			$is_all_comp = true;
			$other_processes = Model_Data_Tasktracker_Process::get_by_task_id($process['task_id']);
			foreach($other_processes as $key => $value) {
				if($value['id'] != $process_id && $value['process_status'] != TSKT_TASK_STATUS_COMP){
					$is_all_comp = false;
					break;
				}
			}


			/**
			 * 他プロセスがすべて完了となるかつにそのタスク設定が定例の場合に
			 * タスクを追加する必要がある。
			 * 追加するタスクは現在登録されているタスクの最終日から更新終了設定日までを登録する
			 * 更新終了日が設定されていない場合は完了した日から最大で１ヶ月分を登録する
	 		 */

			$add_task = null;
			if($is_all_comp){
				$task_setting = Model_Data_Tasktracker_Task_Setting::get_one_by_id($task['task_setting_id']);
				$process_setting = Model_Data_Tasktracker_Process_Setting::get_by_task_setting_id($task['task_setting_id']);
				if($task_setting['job_type'] == TSKT_TASK_JOB_TYPE_ROUTINE){

					## 現在登録されているタスクの最後のタスクを取得 
					$last_task = Model_Data_Tasktracker_Task::get_one_last_by_setting_id_and_status($task['task_setting_id'], TSKT_TASK_STATUS_INCOMP);

					## 最終更新日を超えてないか
					$last_task_limitdate = new DateTime($last_task['task_limit_datetime']);
					//$last_task_limitdate = $last_task_limitdate->setTime(00,00);

					$routine = json_decode($task_setting['routine_setting'],true);
					$setting_last_limitdate =  empty($routine['end_datetime']) ? null : new DateTime($routine['end_datetime']);
					if(empty($setting_last_limitdate) || $setting_last_limitdate->diff($last_task_limitdate)->invert == 1){

						$start_datetime = $last_task_limitdate->modify('+1 days');
						$setting_last_limitdate = empty($setting_last_limitdate) ? null : $setting_last_limitdate->format(TSKT_DATE_FORMAT);

						$add_regular_schedule = Util_Tasktracker_task::create_regular_schedule($routine['frequency'], $routine, $start_datetime->format(TSKT_DATE_FORMAT_DATEONLY), $setting_last_limitdate);

						if(!empty($add_regular_schedule)){

							## 追加するスケジュールは最初の1件のみ
							$routine_setting = json_decode($task_setting['routine_setting'],true);
							$limit_datetime  = new DateTime($add_regular_schedule[0]);
							$limit_datetime->setTime($routine_setting['limit']['hour'], $routine_setting['limit']['minute']); 

							$add_task = array(
								'task_setting_id' 	=> $task['task_setting_id'],
								'task_status' 		=> TSKT_TASK_STATUS_INCOMP,
								'task_limit_datetime' 	=> $limit_datetime->format(TSKT_DATE_FORMAT),
								'owner_user_id' 	=> $task_setting['owner_user_id'], 
							);
						}
					}
				}
			}

			$db = Database_Connection::instance('administrator');
			$db->start_transaction();
			try 
			{
				## プロセスの完了をする
				Model_Data_Tasktracker_Process::update_status($process_id, TSKT_TASK_STATUS_COMP, date(TSKT_DATE_FORMAT));

				## 他プロセスがすべて完了の場合、タスクも完了とする
				if ($is_all_comp) {
					Model_Data_Tasktracker_Task::update_status($process['task_id'], TSKT_TASK_STATUS_COMP, date(TSKT_DATE_FORMAT));
				}

				## 追加タスクがある場合、プロセスと合わせて登録
				if($add_task){
					$insert = Model_Data_Tasktracker_Task::insert($add_task) ;
					$task_id = $insert[0];

					$start_datetime  = new DateTime($add_task['task_limit_datetime']);

					$processes = array();
					foreach($process_setting as $base_process){
						$routine_setting = json_decode($base_process['routine_setting'],true);
						if(!empty($routine_setting['delete_flg'])){
							continue;
						}

						$start_datetime->setTime($routine_setting['start']['hour'], $routine_setting['start']['minute']);

						$process = array(
							'task_id' => $task_id,
							'process_setting_id' => $base_process['id'], 
							'process_status' => TSKT_TASK_STATUS_INCOMP,
							'process_start_datetime' 	=> $start_datetime->format(TSKT_DATE_FORMAT),
							'forecast_cost' => $base_process['forecast_cost'],
							'owner_user_id' => $base_process['owner_user_id'],
						);
						$processes[] = $process;
					}

					## processを登録
					Model_Data_Tasktracker_Process::insert($processes);
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

		//TODO 完了から未完了へ
		elseif($update_status == TSKT_TASK_STATUS_INCOMP){
			if($process['process_status'] != TSKT_TASK_STATUS_COMP || $task['task_status'] ==  TSKT_TASK_STATUS_COMP) {
				//すでにタスクが完了している OR 変更前のタスクが完了ではない
				// TODO
				return false;
			}
			## プロセスの完了をする
			Model_Data_Tasktracker_Process::update_status($process_id, TSKT_TASK_STATUS_INCOMP);
		}
	}


	public function action_fileup($process_id) {
		## プロセスファイル登録
		if(empty($_FILES['file'])){
			return false;
		}

		//TODO ファイル名のチェック (クライアント名が含まれているか)

		//古いファイルの削除
		$file = Model_Data_Tasktracker_Process_File::get_one_by_process_id($process_id);
		if(!empty($file)){
			$extension = pathinfo($file['file_name'], PATHINFO_EXTENSION);
			$path = TSKT_PROCESS_FILES_PATH.'/'.$file['id'].'.'.$extension;
			if(file_exists($path)){
				File::delete($path);
			}
		}

		$set = array(
			'process_id' => $process_id,
			'file_name' => $_FILES['file']['name'],
		);
		$insert = Model_Data_Tasktracker_Process_File::insert_duplicate(array($set));

		## ファイルアップ
		Util_Tasktracker_File::upload_file('file', TSKT_PROCESS_FILES_PATH, $insert[0]);
	}
}
