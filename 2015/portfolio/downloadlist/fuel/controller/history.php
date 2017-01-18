<?php
class Controller_DownloadList_history extends Controller_DownloadList_Base {

	/**
	 * レポート履歴を取得
	 * 
	 * @access public
	 * @return array[] $histories
	 */
	public function action_get_history() {
		$limit       = 1000;
		$histories   = [];
		$status_list = [
			DB_STATUS_START => '処理中',
			DB_STATUS_END   => '完了',
			DB_STATUS_PASS  => 'スキップ',
			DB_STATUS_ERROR => 'エラー'
		];

		foreach (Model_Data_History::getAll($limit) as $history) {

			if (array_key_exists($history['service'], $GLOBALS['service_list'])) {
				$history['service'] = $GLOBALS['service_list'][$history['service']];
			}

			if (array_key_exists($history['status'], $status_list)) {
				$history['status'] = $status_list[$history['status']];
			}

			$histories[] = $history;
		}

		return $histories;
	}

	/**
	 * 担当クライアントを取得
	 * 
	 * @access public
	 * @return array $clients
	 */
	public function action_get_myclients() {
		$clients = [
			'my_clients' => [],
			'from'       => Session::get('user_id_sem')
		];

		if ($clients_temp = Model_Mora_Client::get_for_user(Session::get('user_id_sem'))) {
			foreach ($clients_temp as $client) {
				$clients['my_clients'][] = [
					'id'   => $client['id'],
					'name' => Util_Common_Client::get_client_name($client['id'])
				];
			}
		} 
		return $clients;
	}


	/**
	 * 選択したファイルをダウンロードする
	 */
	public function action_download($id) {

		// ダウンロード対象を取得
		$download = Model_Data_History::get($id);
		// 拡張子がcsv(tsv)の場合
		if (pathinfo($download["file_path"], PATHINFO_EXTENSION) === "csv") {
			File::download($download["file_path"], $download["file_name"], "text/tab-separated-values");
		} else {
			File::download($download["file_path"], $download["file_name"]);
		}
	}
}
