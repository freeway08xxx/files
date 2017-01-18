<?php

require_once APPPATH . "/const/editmanager/queryconnector/queryconnector.php";

class Controller_EditManager_QueryConnector_Create extends Controller_EditManager_QueryConnector_Base
{
	// ログインユーザ権限チェック用URL
	public $access_url = '/sem/new/editmanager/queryconnector/create';

	// 前提共通処理
	public function before() {
		parent::before();

		// 入力パラメータを取得
		$this->action_type = Input::post('action_type');
		$this->replace_words = Input::post('replace_words');
	}

	// 業種管理画面出力
	public function action_index() {

		$this->view->set('action_type', 'analysis');
    	$this->view->set_filename('editmanager/queryconnector/index');
  }

	/**
	 * クエリコネクタ生成
	 *
	 * @param なし
	 * @return なし
	 */
	public function action_exec() {

		$alert_message = '';

		// ファイルアップロードの初期設定
		$file_up_config = array('ext_whitelist' => $GLOBALS['file_ext_whitelist'],
								'path' => UP_FILE_PATH);

		// アップロードを実行
		Upload::process($file_up_config);

		// アップロードチェック
		if (Upload::is_valid()) {

			// アップロードファイルを保存
			Upload::save();

			// 保存したファイル情報を取得
			$up_file_info = Upload::get_files();
			$up_file_name = $up_file_info[0]['saved_as'];

			// セグメントバナー生成バッチを実行
			$curl = Request::forge("http://" . JENKINS_HOST . "/job/" . urlencode(JENKINS_QUERYCONNECTOR_CREATE_JOB)
			. "/buildWithParameters?token=queryconnector&file_name="
			. $up_file_name . "&action_type=" . $this->action_type
			. "&user_id_sem=" . \Session::get("user_id_sem")
			. "&replace_words=" . str_replace(array("\r\n","\r","\n"), ':', $this->replace_words), 'curl');
			$curl->set_option(CURLOPT_USERPWD, JENKINS_USERPWD);
			$curl->execute();

			// 受付完了メッセージ
			$this->alert_message = ALERT_MSG_001;

		} else {

			// アップロードエラー
			foreach (Upload::get_errors() as $error_info) {

				foreach ($error_info['errors'] as $error) {

					$alert_message .= $error['message'] . "\n";
				}
			}
			$this->alert_message = $alert_message;
		}

		// 再帰処理
		$this->view->set('action_type', $this->action_type);
		$this->view->set_filename('editmanager/queryconnector/index');
	}

	/**
	 * 結果ファイルをダウンロードする
	 *
	 * @param $result_file_name 結果ファイル名
	 * @return なし
	 */
	public function action_result_dl($result_file_name) {

		// お知らせ削除
		Util_Common_Websocket::del_info();

		// 結果ファイルをダウンロード
		File::download(RESULT_PATH . $result_file_name . ".csv");
	}

	/**
	 * エラー文言表示
	 */
	public function action_error_result() {

		// お知らせ削除
		Util_Common_Websocket::del_info();
		$this->action_index();
	}
}