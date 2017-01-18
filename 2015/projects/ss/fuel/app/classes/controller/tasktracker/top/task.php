<?php

/**
 * /top/task
 */
class Controller_Tasktracker_Top_Task extends Controller_Tasktracker_Base
{
	/*========================================================================*/
	/* 前提共通処理
	/*========================================================================*/
	public function before() {
		## super
		parent::before();
	}

	/**
	 * タスク一覧を取得 
	 *
	 * @access public
	 * @return void
	 */
	public function get_list() {
		$status = Input::param('status');
		$date_from_unix = Input::param('dateFrom');
		$date_to_unix = Input::param('dateTo');

		## unixTimeをdateに変換 (その際、検索ではその日も含めるので1日足す)
		$date_from = !empty($date_from_unix)? date( TSKT_DATE_FORMAT_DATEONLY ,$date_from_unix + 86400 ): null;
		$date_to = !empty($date_to_unix)? date( TSKT_DATE_FORMAT_DATEONLY ,$date_to_unix + 86400 ): null;

		## 担当クライアント取得
		$clients = Util_Common_Client::get_clients_by_owner(Session::get('user_id_sem'), Session::get('role_id_sem'));
		$client_ids = array_keys($clients);

		## 担当クライアントのタスクを取得
		$tasks = Model_Data_Tasktracker_Task::get_tasks_by_status($client_ids, $status, $date_from, $date_to);
		foreach($tasks as $key => $value){

			## 担当ではない場合は外す
			if(empty($clients[$value['client_id']])){
				continue;
			}

			## 名前変更
			$value['category'] = $value['task_category_id'];

			## 遅延日数
			$value['delay'] = Util_Tasktracker_Common::get_delay_date ($value['task_end_datetime'], $value['task_limit_datetime']);

			## クライアント名
			$value['client_name'] = Util_Common_Client::get_client_name($value['client_id'], $clients[$value['client_id']]);

			$tasks[$key] = $value;
		}

		$response['tasks'] = $tasks;
		return $this->response($response); 
	}
}
