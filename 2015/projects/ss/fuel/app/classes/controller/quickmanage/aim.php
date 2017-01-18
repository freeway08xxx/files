<?php
class Controller_QuickManage_Aim extends Controller_QuickManage_Base {

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

		$HMVC_list = Request::forge("quickmanage/aim/list", false)->execute();
		$HMVC_form = Request::forge("quickmanage/aim/form", false)->execute();

		## View
		$this->view->set_safe("HMVC_list", $HMVC_list);
		$this->view->set_safe("HMVC_form", $HMVC_form);
		$this->view->set_filename("quickmanage/aim/index");

		## angular $routeProvider 経由で出力
		$this->response($this->view);
	}

	/*========================================================================*/
	/* 一覧出力
	/*========================================================================*/
	public function action_list() {

		if (Request::is_hmvc()) {

			## 目標設定一覧取得
			$DB_aim_list = \Model_Data_QuickManage_Aim::get_list(date("Ym"));

			## 出力パラメータ
			$this->view->set("DB_aim_list", $DB_aim_list);

			## View
			$this->view->set_filename("quickmanage/aim/list");
			return Response::forge($this->view);
		}
	}

	/*========================================================================*/
	/* フォーム画面出力
	/*========================================================================*/
	public function action_form() {

		## View
		$this->view->set_filename("quickmanage/aim/form");
		return Response::forge($this->view);
	}

	/*========================================================================*/
	/* 設定シートダウンロード
	/*========================================================================*/
	public function action_download() {

		$DL_contents[] = array("クライアントID", "クライアント", "Yahoo!目標予算額", "YDN目標予算額", "Google目標予算額", "GDN目標予算額", "D2C目標予算額");

		## 目標設定一覧取得
		$DB_aim_list = \Model_Data_QuickManage_Aim::get_list(date("Ym"));

		foreach ($DB_aim_list as $DB_aim) {
			$DL_contents[] = array($DB_aim["client_id"],
								   $DB_aim["client_name"],
								   $DB_aim["Y_S_aim_budget"],
								   $DB_aim["Y_D_aim_budget"],
								   $DB_aim["G_S_aim_budget"],
								   $DB_aim["G_D_aim_budget"],
								   $DB_aim["D2C_aim_budget"]);
		}

		## ファイルダウンロード
		$this->format = "csv";
		$DL_file_name = "quickmanage_aim_" . date("YmdHis") . ".csv";
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
					if (count($UL_column_list) !== 7) {
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
						if (!preg_match("/^[0-9]+$/", $UL_column_list[$i])) {
							## クライアント名以外
							if ($i !== 1) {
								$DL_contents[$line][] = ERRMSG_004;
								continue 2;
							}
						}
					}
					## 対象期間分、登録内容を配列格納
					foreach ($target_ym_list as $target_ym) {
						$values[] = array("client_id"  => trim($UL_column_list[0]),
										  "media_id"   => MEDIA_ID_YAHOO,
										  "product"    => "Search Network",
										  "target_ym"  => $target_ym,
										  "aim_budget" => trim($UL_column_list[2]));
						$values[] = array("client_id"  => trim($UL_column_list[0]),
										  "media_id"   => MEDIA_ID_IM,
										  "product"    => "Display Network",
										  "target_ym"  => $target_ym,
										  "aim_budget" => trim($UL_column_list[3]));
						$values[] = array("client_id"  => trim($UL_column_list[0]),
										  "media_id"   => MEDIA_ID_GOOGLE,
										  "product"    => "Search Network",
										  "target_ym"  => $target_ym,
										  "aim_budget" => trim($UL_column_list[4]));
						$values[] = array("client_id"  => trim($UL_column_list[0]),
										  "media_id"   => MEDIA_ID_GOOGLE,
										  "product"    => "Display Network",
										  "target_ym"  => $target_ym,
										  "aim_budget" => trim($UL_column_list[5]));
						$values[] = array("client_id"  => trim($UL_column_list[0]),
										  "media_id"   => MEDIA_ID_D2C,
										  "product"    => "--",
										  "target_ym"  => $target_ym,
										  "aim_budget" => trim($UL_column_list[6]));
					}
					$DL_contents[$line][] = "OK";
				}
			}
			## 目標登録
			if (!empty($values)) {
				\Model_Data_QuickManage_Aim::ins($values);
			}
		}

		## 結果ファイルダウンロード
		$this->format = "csv";
		$DL_file_name = "【処理結果】quickmanage_aim_" . date("YmdHis") . "_" . SEM_PORTAL_PROCESS_ID . ".csv";
		$response = $this->response($DL_contents);
		$response->set_header("Content-Disposition", "attachment; filename=" . $DL_file_name);

		return $response;
	}
}
