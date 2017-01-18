<?php

class Controller_AccountStructure_Export extends Controller_AccountStructure_Base {

	/*========================================================================*/
	/* 前提共通処理
	/*========================================================================*/
	public function before() {
		## super
		parent::before();
	}
	
	/*========================================================================*/
	/* レポート出力
	/*========================================================================*/
	public function action_index() {

		$account_info_list = array();

		// クライアント情報取得
		$client_id	= Input::param('ssClient');

		// 出力条件およびフィルタリング条件取得
		$conditions	= Format::forge(Input::param('conditions'), 'json')->to_array();

		// 選択されたアカウント情報を取得
		foreach (Input::param('ssAccount') as $account) {
			$temp = Format::forge($account, 'json')->to_array();
			$account_info["media_id"] 	= $temp["media_id"];
			$account_info["account_id"] = $temp["account_id"];
			$account_info_list[] = $account_info;
		}
		
		$values = array(
					"client_id"  => $client_id,
					"account_id" => base64_encode(serialize($account_info_list)),
					"conditions" => base64_encode(serialize($conditions)),
				);
	
		try{
			// アカウント情報および出力条件をまとめてDBに保存
			$ret = Model_Data_Structure_UserExportHistory::ins($values);

			$history_id = $ret[0];
			$result 	= $ret[1];

			if($result){
				$curl = Request::forge("http://" . JENKINS_HOST . "/job/" . urlencode(JENKINS_ACCOUNT_STRUCTURE_CREATE_JOB) . "/buildWithParameters?token=accountstructure&history_id=" . $history_id."&user_id_sem=".\Session::get("user_id_sem"), "curl");
				$curl->set_option(CURLOPT_USERPWD, JENKINS_USERPWD);
				$curl->execute();
			}
		} catch (\Exception $e) {
			logger(ERROR, "FAILURE id:[".$history_id."],message:[" . $e->getMessage() . "]", __METHOD__);
		}

		// 完了画面
		$this->view->set_filename("accountstructure/condition/accept");

	}

	/*========================================================================*/
	/* バッチの処理結果をダウンロードする
	/*========================================================================*/
    public function action_download() {

        $history_id = Input::get("id");
        $history_id = \Crypt::decode($history_id);

        $info = \Model_Data_Structure_UserExportHistory::get_by_id($history_id);
		$path_parts = pathinfo($info["export_file_path"]);
		File::download($info["export_file_path"], mb_convert_encoding($path_parts["basename"], "Shift_JIS", "auto"));
    }
    
}
