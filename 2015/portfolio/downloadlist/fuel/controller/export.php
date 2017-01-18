<?php

require_once APPPATH . "/const/downloadlist/downloadlist.php";

class Controller_DownloadList_Export extends Controller_DownloadList_Base {


	/**
	 * メイン処理の前処理を実行する
	 */
	public function before() {

		// 入力パラメータを取得
		$this->download_row = Input::post("download_row");
		$this->download_select_row = Input::post("download_select_row");

		parent::before();
	}

	/**
	 * メイン処理を実行する
	 */
	public function action_index() {

		// ダウンロード一覧を取得
		$download_list = Model_Data_DownloadList::get(Session::get("user_id_sem"));

		// Viewを実行
		$this->view->set("download_list", $download_list);
		$this->view->set("download_row", $this->download_row);

		$this->view->set_filename("downloadlist/index");
	}

	/**
	 * 選択したファイルをダウンロードする
	 */
	public function action_dl($id) {

		// ダウンロード対象を取得
		$download = Model_Data_DownloadList::get_by_pk($id);

		// 拡張子がcsv(tsv)の場合
		if (pathinfo($download["out_file_path"], PATHINFO_EXTENSION) === "csv") {
			File::download($download["out_file_path"], $download["file_name"], "text/tab-separated-values");
		} else {
			File::download($download["out_file_path"], $download["file_name"]);
		}
	}

	/**
	 * 選択したファイルを一括ダウンロードする
	 */
	public function action_bulk_dl() {

		// 一括DL対象を取得
		$bulk_dl_list = Model_Data_DownloadList::get_bulk_dl(explode(",", $this->download_select_row));

		try {
			// ファイル圧縮
			$zip = new ZipArchive();
			$zip_file_path = DOWNLOAD_LIST_ZIP_PATH . "/" . mt_rand() . date("YmdHis") . ".zip";
			$zip_res = $zip->open($zip_file_path, ZipArchive::CREATE);

			if ($zip_res === true) {

				// 圧縮するファイルを設定
				$file_exist_flg = false;
				foreach ($bulk_dl_list as $bulk_dl) {
					if (isset($bulk_dl["out_file_path"])) {
						$zip->addFile($bulk_dl["out_file_path"], $bulk_dl["file_name"]);
						$file_exist_flg = true;
					}
				}
				$zip->close();

				if ($file_exist_flg) {
					File::download($zip_file_path);
				} else {
					$this->alert_message = ALERT_MSG_002;
				}
			} else {
				$this->alert_message = ALERT_MSG_001;
			}
		} catch (Exception $e) {
			$this->alert_message = ALERT_MSG_001;
		}

		// メイン処理を実行（エラーもしくは、DLファイルが無い場合に実行）
		$this->action_index();
	}

	/**
	 * 選択したファイルを削除する
	 */
	public function action_delete() {

		// 削除対象を取得
		$download_list = Model_Data_DownloadList::get_bulk_dl(explode(",", $this->download_select_row));

		// 選択したファイルを削除
		Model_Data_DownloadList::del(explode(",", $this->download_select_row));

		foreach ($download_list as $download) {
			unlink($download["out_file_path"]);
		}

		// メイン処理を実行
		$this->action_index();
	}

}
