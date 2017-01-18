<?php
class Controller_QuickManage_Discount extends Controller_QuickManage_Base {

	/*========================================================================*/
	/* 前提共通処理
	/*========================================================================*/
	public function before() {

		## super
		parent::before();
	}

	/*========================================================================*/
	/* トップ画面出力
	/*========================================================================*/
	public function action_index() {

		$HMVC_list = Request::forge("quickmanage/discount/list", false)->execute();
		$HMVC_form = Request::forge("quickmanage/discount/form", false)->execute();

		## View
		$this->view->set_safe("HMVC_list", $HMVC_list);
		$this->view->set_safe("HMVC_form", $HMVC_form);
		$this->view->set_filename("quickmanage/discount/index");

		## angular $routeProvider 経由で出力
		$this->response($this->view);
	}

	/*========================================================================*/
	/* 一覧出力
	/*========================================================================*/
	public function action_list() {

		if (Request::is_hmvc()) {

			## 値引設定一覧取得
			$DB_discount_list = \Model_Data_QuickManage_Discount::get_list(date("Ym"), date("Ym"));

			## 出力パラメータ
			$this->view->set("DB_discount_list", $DB_discount_list);

			## View
			$this->view->set_filename("quickmanage/discount/list");
			return Response::forge($this->view);
		}
	}

	/*========================================================================*/
	/* フォーム画面出力
	/*========================================================================*/
	public function action_form() {

		## View
		$this->view->set_filename("quickmanage/discount/form");
		return Response::forge($this->view);
	}

	/*========================================================================*/
	/* 設定シートダウンロード
	/*========================================================================*/
	public function action_download() {

		$DL_contents[] = array("媒体", "アカウントID", "アカウント", "値引種別", "値引額");

		## 値引設定一覧取得
		$DB_discount_list = \Model_Data_QuickManage_Discount::get_list(date("Ym"), date("Ym"));

		foreach ($DB_discount_list as $DB_discount) {
			$DL_contents[] = array($GLOBALS["all_media_id_list"][$DB_discount["media_id"]],
								   $DB_discount["account_id"],
								   $DB_discount["account_name"],
								   $GLOBALS["discount_type_list"][$DB_discount["discount_type"]],
								   $DB_discount["discount_rate"]);
		}

		## ファイルダウンロード
		$this->format = "csv";
		$DL_file_name = "quickmanage_discount_" . date("YmdHis") . ".csv";
		$response = $this->response($DL_contents);
		$response->set_header("Content-Disposition", "attachment; filename=" . $DL_file_name);

		return $response;
	}

	/*========================================================================*/
	/* 設定シートアップロード
	/*========================================================================*/
	public function action_upload() {
		$post_arr = Input::post();

		/**
		 * URLエンコードでPOSTされたCSVデータを読み込み、配列に変換
		 */
		$pos = strpos($post_arr["file_dataurl"], ';base64,');
		$buf = substr($post_arr["file_dataurl"], $pos + 8);
		$buf = base64_decode($buf);
		$buf = mb_convert_encoding($buf, "UTF-8", "SJIS");

		// 改行コードをすべてLFに変換
		$buf = preg_replace("/\r\n|\r|\n/", "\n", $buf);
		$fp  = tmpfile();
		fwrite($fp, $buf);
		rewind($fp);

		$csv_arr = array();
		while (($data = fgetcsv($fp, 0, ",")) !== FALSE) {
			$csv_arr[] = $data;
		}
		fclose($fp);

		## 対象期間算出
		$target_ym_list = array();
		$start_date = str_replace('/', '-', $post_arr["start_date"][0]);
		$end_date   = str_replace('/', '-', $post_arr["end_date"][0]);

		$target_ym_totime = strtotime($start_date);
		while ($target_ym_totime <= strtotime($end_date)) {
			$target_ym_list[] = date("Ym", $target_ym_totime);
			$target_ym_totime = strtotime("+1 month", $target_ym_totime);
		}

		## アップロード失敗した場合
		if (empty($csv_arr)) {
			$DL_contents[] = ERRMSG_001;

		} else {
			$values = array();
			$UL_line_list = $csv_arr;

			## 行毎ループ
			for ($line=0; $line<count($UL_line_list); $line++) {

				## 空行はスキップ
				$array_content = array_filter($UL_line_list[$line]);
				if (empty($array_content)) {
					continue;
				}

				$UL_column_list = $UL_line_list[$line];
				$DL_contents[$line] = $UL_column_list;

				## 先頭行はスキップ
				if ($line === 0) {
					$DL_contents[$line][] = "結果";
					continue;

				} else {
					## 必須チェック
					if (count($UL_column_list) !== 5) {
						$DL_contents[$line][] = ERRMSG_002;
						continue;
					}
					for ($i=0; $i<count($UL_column_list); $i++) {
						$UL_column_list[$i] = trim($UL_column_list[$i]);
						## 未入力チェック
						if (strlen($UL_column_list[$i]) === 0) {
							$DL_contents[$line][] = ERRMSG_003;
							continue 2;
						}
						## 入力文字チェック
						// 媒体
						if ($i === 0) {
							if (!in_array($UL_column_list[$i], $GLOBALS["all_media_id_list"])) {
								$DL_contents[$line][] = ERRMSG_005;
								continue 2;
							}
						// アカウントID
						} elseif ($i === 1) {
							if (!preg_match("/^[a-zA-Z0-9-]+$/", $UL_column_list[$i])) {
								$DL_contents[$line][] = ERRMSG_007;
								continue 2;
							}
						// 割引種別
						} elseif ($i === 3) {
							if (!in_array($UL_column_list[$i], $GLOBALS["discount_type_list"])) {
								$DL_contents[$line][] = ERRMSG_006;
								continue 2;
							}
						// 割引額
						} elseif ($i === 4) {
							if (!preg_match("/^[0-9.-]+$/", $UL_column_list[$i])) {
								$DL_contents[$line][] = ERRMSG_004;
								continue 2;
							}
						}
					}

					## 対象期間分、登録内容を配列格納
					foreach ($target_ym_list as $target_ym) {
						$values[] = array("media_id"      => array_keys($GLOBALS["all_media_id_list"], trim($UL_column_list[0])),
										  "account_id"    => trim($UL_column_list[1]),
										  "target_ym"     => $target_ym,
										  "discount_type" => array_keys($GLOBALS["discount_type_list"], trim($UL_column_list[3])),
										  "discount_rate" => trim($UL_column_list[4]));
					}
					$DL_contents[$line][] = "OK";
				}
			}
			## 値引登録
			if (!empty($values)) {
				\Model_Data_QuickManage_Discount::ins($values);
			}
		}
		## 結果ファイルダウンロード
		$this->format = "csv";
		$DL_file_name = "【処理結果】quickmanage_discount_" . date("YmdHis") . "_" . SEM_PORTAL_PROCESS_ID . ".csv";
		$response = $this->response($DL_contents);
		$response->set_header("Content-Disposition", "attachment; filename=" . $DL_file_name);

		return $response;
	}
}
