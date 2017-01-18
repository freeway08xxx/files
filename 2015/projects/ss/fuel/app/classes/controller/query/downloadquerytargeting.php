<?php

class Controller_Query_DownloadQuerytargeting extends Controller_Query_Base {

	// loginユーザ権限チェック用URL
	public $access_url = "/sem/share_monitor/entry_action_schedule.php";

	// 前提共通処理
	public function before() {
		parent::before(); // super
		$this->file_name = Input::get("file"); // ダウンロードファイル名
	}

	// CSVダウンロード
	public function action_index() {
		File::download(DL_QUERYTARGET_DIR . '/' . DL_QUERYTARGET . $this->file_name . '.csv');
	}
}
