<?php

require_once APPPATH . "/const/main.php";
require_once APPPATH . "/const/segmentbanner/segmentbanner.php";

class Controller_Segmentbanner_Create extends Controller_Segmentbanner_Base {

	// ログインユーザ権限チェック用URL
	public $access_url = "/sem/new/segmentbanner/create";

	/**
	 * メイン処理の前処理を実行する
	 *
	 * @param なし
	 * @return なし
	 */
	public function before() {

		parent::before();

		// 入力パラメータを取得
		$this->action_type = Input::post("action_type");
	}

	/**
	 * メイン処理を実行する
	 *
	 * @param なし
	 * @return なし
	 */
	public function action_index() {

		// Viewを実行
		$this->view->set_filename("segmentbanner/index");
	}

	/**
	 * Bulkフォーマットをダウンロードする
	 *
	 * @param なし
	 * @return なし
	 */
	public function action_bulk_format_dl() {

		// Bulkフォーマットをダウンロード
		$this->format = "csv";
		$response = $this->response($GLOBALS["bulk_format_item_list"]);
		$response->set_header("Content-Disposition", "attachment; filename=" . BULK_FORMAT_FILE_NAME);

		return $response;
	}

	/**
	 * Bulkの設定例をダウンロードする
	 *
	 * @param なし
	 * @return なし
	 */
	public function action_bulk_example_dl() {

		$bulk_example_file = $GLOBALS["bulk_format_item_list"];
		$bulk_example_file[] = $GLOBALS["bulk_example_item_list"];

		// Bulkの設定例をダウンロード
		$this->format = "csv";
		$response = $this->response($bulk_example_file);
		$response->set_header("Content-Disposition", "attachment; filename=" . BULK_FORMAT_FILE_NAME);

		return $response;
	}

	/**
	 * Bulkファイルをアップロードする
	 *
	 * @param なし
	 * @return なし
	 */
	public function action_bulk_up() {

		$alert_message = "";

		if ($this->action_type === "bulk_up") {

			// 処理IDを登録
			$setting_columns = array("exec_code");
			$setting_values[] = array($setting_columns[0] => "0001");

			$setting_result = Model_Data_SegmentbannerSetting::ins($setting_columns, $setting_values);

			// ファイルアップロードの初期設定
			$file_up_config = array("ext_whitelist" => $GLOBALS["bulk_ext_whitelist"],
									"prefix" => $setting_result[0] . "_",
									"path" => UP_BULK_PATH);

			// アップロードを実行
			Upload::process($file_up_config);

			// Bulkファイルのバリデーションチェック
			$check_result = self::_bulk_validation_check();

			if ($check_result === true) {

				if (Upload::is_valid()) {

					// アップロードファイルを保存
					Upload::save();

					// 保存したファイル情報を取得
					$up_file_info = Upload::get_files();
					$up_file_name = $up_file_info[0]["saved_as"];

					// セグメントバナー生成バッチを実行
					$curl = Request::forge("http://" . JENKINS_HOST . "/job/" . urlencode(JENKINS_SEGMENTBANNER_CREATE_JOB) . "/buildWithParameters?token=segmentbanner&bulk_file_name=" . $up_file_name . "&user_id_sem=" . \Session::get("user_id_sem"), "curl");
					$curl->set_option(CURLOPT_USERPWD, JENKINS_USERPWD);
					$curl->execute();

					$this->alert_message = ALERT_MSG_003;

				} else {

					// アップロードエラー
					foreach (Upload::get_errors() as $error_info) {

						foreach ($error_info["errors"] as $error) {

							$alert_message .= $error["message"] . "\n";
						}
					}

					$this->alert_message = $alert_message;
				}

			} else {

				$this->alert_message = $check_result;
			}

		} else {

			if ($this->action_type === "material_up") {

				// ファイルアップロードの初期設定
				$file_up_config = array("ext_whitelist" => $GLOBALS["material_ext_whitelist"],
										"path" => MATERIAL_PATH);

			} elseif ($this->action_type === "font_up") {

				// ファイルアップロードの初期設定
				$file_up_config = array("ext_whitelist" => $GLOBALS["font_ext_whitelist"],
										"path" => FONT_PATH);
			}

			if ($this->action_type) {

				// アップロードを実行
				Upload::process($file_up_config);

				if (Upload::is_valid()) {

					// アップロードファイルを保存
					Upload::save();

					// 保存したファイル情報を取得
					$up_file_info = Upload::get_files();
					$up_file_name = $up_file_info[0]["saved_as"];

					$this->alert_message = ALERT_MSG_001 . $up_file_name . "<br /><br />" . ALERT_MSG_002;

				} else {

					// アップロードエラー
					foreach (Upload::get_errors() as $error_info) {

						foreach ($error_info["errors"] as $error) {

							$alert_message .= $error["message"] . "\n";
						}
					}

					$this->alert_message = $alert_message;
				}
			}
		}

		// メイン処理を実行
		$this->action_index();
	}

	/**
	 * Bulkファイルのバリデーションチェックを行う
	 *
	 * @param なし
	 * @return なし
	 */
	private function _bulk_validation_check() {

		// Bulkファイルを開く
		$files = Upload::get_files();

		// ファイル名のチェック
		if (strpos($files[0]["name"], " ") !== false
				|| strpos($files[0]["name"], "　") !== false) {

			return ALERT_MSG_005;
		}

		ini_set("auto_detect_line_endings", true);

		foreach (new \Util_Csv($files[0]["file"]) as $row) {

			// 必須チェック
			$row_count = count($row);

			for ($i = 3; $i < 12; $i++) {

				if (!isset($row[$i])) {

					return $GLOBALS["bulk_format_item_list"][0][$i] . ALERT_MSG_006;
				}
			}

			// 数値チェック
			if (!is_numeric($row[9])) return $GLOBALS["bulk_format_item_list"][0][9] . ALERT_MSG_007;
			if (!is_numeric($row[10])) return $GLOBALS["bulk_format_item_list"][0][10] . ALERT_MSG_007;
			if (!is_numeric($row[11])) return $GLOBALS["bulk_format_item_list"][0][11] . ALERT_MSG_007;
			if (!empty($row[15]) && !is_numeric($row[15])) return $GLOBALS["bulk_format_item_list"][0][15] . ALERT_MSG_007;
			if (!empty($row[16]) && !is_numeric($row[16])) return $GLOBALS["bulk_format_item_list"][0][16] . ALERT_MSG_007;
			if (!empty($row[17]) && !is_numeric($row[17])) return $GLOBALS["bulk_format_item_list"][0][17] . ALERT_MSG_007;
			if (!empty($row[21]) && !is_numeric($row[21])) return $GLOBALS["bulk_format_item_list"][0][21] . ALERT_MSG_007;
			if (!empty($row[22]) && !is_numeric($row[22])) return $GLOBALS["bulk_format_item_list"][0][22] . ALERT_MSG_007;
			if (!empty($row[23]) && !is_numeric($row[23])) return $GLOBALS["bulk_format_item_list"][0][23] . ALERT_MSG_007;

			// 素材の存在チェック
			if (!file_exists(MATERIAL_PATH . "/" . $row[3])) {

				return $row[3] . ALERT_MSG_004;
			}

			// フォントの存在チェック
			$font_check = function ($font) {

				if (!file_exists(FONT_PATH . "/" . $font)) {

					return $font . ALERT_MSG_004;
				}

				return true;
			};

			if (($font_check_result = $font_check($row[8])) !== true
					|| ($font_check_result = $font_check($row[14])) !== true
					|| ($font_check_result = $font_check($row[20])) !== true) {

				return $font_check_result;
			}
		}

		return true;
	}

	/**
	 * 結果ファイルをダウンロードする
	 *
	 * @param $result_file_name 結果ファイル名
	 * @return なし
	 */
	public function action_result_dl($result_file_name) {

		Util_Common_Websocket::del_info();

		// 結果ファイルをダウンロード
		File::download(RESULT_PATH . "/zip/" . $result_file_name . ".zip");
	}
}
