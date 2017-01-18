<?php
class Controller_Tasktracker_Common extends Controller_Tasktracker_Base
{
	/*========================================================================*/
	/* 前提共通処理
	/*========================================================================*/
	public function before() {
		## super
		parent::before();
	}


	public function get_const() {
		
	}

	/**
	 * カテゴリマスターの取得 
	 * 
	 * @access public
	 * @return void
	 */
	public function get_category_master() {
		$response['category'] = Util_Tasktracker_Common::get_category();
		return $this->response($response); 
	}

	/**
	 * タスクマスターの取得 
	 *
	 * @access public
	 */
	public function get_taskmaster() {
		$category_id = Input::param('category_id');
		$response['task_masters'] = Model_Data_Tasktracker_Task_Master::get_by_category_id($category_id);
		return $this->response($response); 
	}

	/**
	 * プロセスマスター取得 
	 * 
	 * @access public
	 * @return void
	 */
	public function get_process_master() {
		$task_master_id = Input::param('task_master_id');
		$response['process_masters'] = Model_Data_Tasktracker_Process_Master::get_by_task_master_id($task_master_id);
		return $this->response($response); 
	}

	/**
	 * プロセスユーザー一覧取得 
	 * 
	 * @access public
	 * @return void
	 */
	public function get_process_userlist() {
		//アドテク + 管理者のみを取得
		$bureau_ids = array( 34, 600 , 616, 628, 687, 716 ); // CAAD + Admin
		$caad_work_group_ids = \Arr::pluck(Model_Mora_Workgroup::get_by_bureau_id($bureau_ids), 'id');
		$caad_work_group_ids[] = 269; //Admin追加 (bureauで絞れないもの)

		$user = Model_Mora_User::get_by_work_group_id($caad_work_group_ids);

		$response['user_list'] = array_values($user);
		return $this->response($response); 
	}

	/**
	 * ARUJOマスター取得 
	 * 
	 * @access public
	 * @return void
	 */
	public function get_arujo_master() {
		$task_master_id = Input::param('task_master_id');
		$client_id = Input::param('client_id');

		if(!empty($client_id)){
			$task_master_arujo = Model_Data_Tasktracker_Task_Master_Arujo::get_by_task_master_id_join_client($task_master_id, $client_id);
		}else{
			$task_master_arujo = Model_Data_Tasktracker_Task_Master_Arujo::get_by_task_master_id($task_master_id);
		}
		
		foreach($task_master_arujo as $key => $value){
			$value['media_name'] = Util_Tasktracker_Common::get_media_name($value['media_id']);
			$task_master_arujo[$key] = $value;
		}

		$response['task_master_arujo'] = $task_master_arujo;
		return $this->response($response); 
	}

	/**
	 * action_download
	 * 
	 * @access public
	 * @return void
	 */
	public function action_download() {
		$file_category = Input::param('category');

		## マスター
		if($file_category == TSKT_FILE_CATEGORY_MASTER){
			$id = Input::param('task_master_file_id');
			$file = Model_Data_Tasktracker_Task_Master_File::get_one_by_id($id);
			$file_pash_base = TSKT_MASTER_FILES_PATH;
		}
		
		## Setting
		elseif($file_category == TSKT_FILE_CATEGORY_SETTING){
			$id = Input::param('setting_file_id');
			$file = Model_Data_Tasktracker_Task_Setting_File::get_one_by_id($id);
			$file_pash_base = TSKT_TASK_SETTING_FILES_PATH;
		}

		## Process
		elseif($file_category == TSKT_FILE_CATEGORY_PROCESS){
			$id = Input::param('process_file_id');
			$file = Model_Data_Tasktracker_Process_File::get_one_by_id($id);
			$file_pash_base = TSKT_PROCESS_FILES_PATH;
		}

		$extension = pathinfo($file['file_name'], PATHINFO_EXTENSION);
		$file_path = $file_pash_base . '/' . $id . '.' . $extension;
		if ( !file_exists($file_path) ){
			//TODO ファイルが存在しない
			return false;
		}
		

		$res = Response::forge();
		$res->set_header('Cache-Control', 'public');
		$res->set_header('Pragma', 'public');
		$res->set_header('Content-Type', 'application/octet-stream');
		$res->set_header('Content-Disposition', "attachment; filename=\"" . mb_convert_encoding($file['file_name'], "Shift_JIS") . "\"");
		$res->send(true);

		File::download($file_path, mb_convert_encoding($file['file_name'], "Shift_JIS", "auto"));
	}

	/**
	 * ユーザーリソース画面取得 
	 * 
	 * @access public
	 * @return void
	 */
	public function action_user_resource()
	{
		$this->view->set_filename('tasktracker/common/userresource');
		$this->response($this->view);
	}

	/**
	 * ユーザーリソース取得 
	 * 
	 * @access public
	 * @return void
	 */
	public function get_user_repource_data($user_id){

		//初期値では現在の時間から一週間先までのタスクを取得
		$date_from_unix = Input::param('dateFrom', strtotime("-2 week"));
		$date_to_unix = Input::param('dateTo', strtotime("+2 week"));
		$process_status = null;

		$user = Model_Mora_User::get_user_by_id($user_id);
		$response['user'] = array(
			'id' => $user['id'],			
			'user_name' => $user['user_name'],			
		);

		## unixTimeをdateに変換 (その際、検索ではその日も含めるので1日足す)
		$date_from = !empty($date_from_unix)? date( TSKT_DATE_FORMAT_DATEONLY ,$date_from_unix ): null;
		$date_to = !empty($date_to_unix)? date( TSKT_DATE_FORMAT_DATEONLY ,$date_to_unix + 86400 ): null;

		// 対象のプロセスを取得
		$process = Model_Data_Tasktracker_Process::get_process_by_user_and_status($user_id, $process_status, $date_from, $date_to);
		// 担当クライアント取得
		$clients = Util_Common_Client::get_clients_by_owner($user_id, Session::get('role_id_sem'));

		foreach($process as $key => $value){
			$dt = new DateTime($value['process_start_datetime']);
			if($value['forecast_cost']){
				$dt->add(new DateInterval('PT'.$value['forecast_cost'].'M'));
			}
			$value['process_end_cost_datetime'] = $dt->format(TSKT_DATE_FORMAT);
			$value['client_name'] = Util_Common_Client::get_client_name($value['client_id'], $clients[$value['client_id']]);
			$process[$key] = $value;
		}
		$response['process'] = $process;
		//$response['process'] = Arr::sort($process, 'process_start_datetime');

		return $this->response($response); 
	}

	/**
	 * ルーチン設定画面の取得 
	 * 
	 * @access public
	 * @return void
	 */
	public function action_routine_setting(){
		$this->view->set_filename('tasktracker/common/routine_setting');
		$this->response($this->view);
	}
}
