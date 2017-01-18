<?php

/**
 * falconレポート 目標設定 カテゴリ別設定CSV コントローラ
 **/

class Controller_Falcon_Aim extends Controller_Falcon_Base {

	// loginユーザ権限チェック用URL
	public $access_url = "/sem/universe_fast/universe.php";

	private $data = array();

	public function before() {
		parent::before();
	}

	/**
	 * 設定用CSVをダウンロード
	 * @return attachment csv
	 */
	public function action_getsheet() {
		/**
		 * formdataはjsonにシリアライズされたtextが来る
		 * PostData 自体はMultipart-Form-Data で来るので、
		 * upload file があれば $_FILES に格納される
		 */
		$json_input = Input::param("form");
		$input_form = json_decode($json_input, true);


		/** CSVヘッダー */
		$row = array_column($input_form["cols"], "name");
		\Arr::insert($row, array("カテゴリ種別", "カテゴリID", "カテゴリ名"), 0);
		$file_contents[] = $row;

		$getkey = function ($val) {
			return (!empty($val["ext_key"])) ? $val["ext_key"] : $val["key"];
		};
		$row = array_map($getkey, $input_form["cols"]);

		$cols = $row; // 登録済数値指定用にコピー
		\Arr::insert($row, array("category_element", "category_id", "category_name"), 0);
		$row[] = "※この行は削除しないでください";

		$file_contents[] = $row;

		$aim_info = Model_Data_Falcon_AimSetting::get_info($input_form["client_id"], $input_form["template_id"]);

		$category_aim = array();
		foreach($aim_info as $aim) {
			if (strpos($aim["target"], "category") !== false) {
				$id = explode(":", $aim["target"]);

				$category_aim[(string)$id[1]] = array(
					"element" => $aim["element"],
					"value"   => $aim["value"]
				);
			}
		}

		/** カテゴリリスト展開、登録済の設定を適用 */
		$category_list = \Model_Data_Category::get_category_list($input_form["category_element"], $input_form["client_id"], $input_form["category_id"]);
		foreach ($category_list as $category) {

			$id = $category["category_id"];

			$row = array();
			foreach($cols as $col) {
				$row[] = (!empty($category_aim[$id]) && $category_aim[$id]["element"] === $col) ?
					$category_aim[$id]["value"] : "";
			}

			\Arr::insert($row, array($input_form["category_element"], $category["category_id"], $category["category_name"]), 0);
			$file_contents[] = $row;
		}

		//クライアント名取得
		$client_name = \Model_Mora_Client::get_client_name($input_form["client_id"]);
		if ($client_name["client_name"]) {
			$client_name["company_name"] = $client_name["company_name"]. "(" . $client_name["client_name"] .")";
		}

		//ファイルダウンロード
		$this->format = "csv";
		$filename = "Falcon_目標設定_カテゴリ別" . "【" . $client_name["company_name"] ."】" . $input_form["template_name"] . "_" . date("YmdHis") . ".csv";
		$response = $this->response($file_contents);
		$response->set_header("Content-Disposition", "attachment; filename=" . $filename);

		return $response;
	}

	/**
	 * アップロードされたCSVの内容をパース
	 * @return array aim_element_list
	 */
	public function action_read($client_id, $category_id_list, $category_element){

		/**
		 * CSVファイル読み込み
		 */
		ini_set('auto_detect_line_endings', 1); // macの改行コードに対応

		Upload::process(
			array(
				'path' => FALCON_CAMPAIGN_SETTING_UPLOAD_DIR,
				'new_name' => 'falcon_category_aim_'.$client_id.'.csv',
				'auto_rename' => false,
				'overwrite' => true,
				'ext_whitelist' => array('csv'),
			)
		);
		Upload::save(0);

		$upload_file      = Upload::get_files(0);
		$upload_file_path = $upload_file["saved_to"].$upload_file["saved_as"];

		$file_content = File::read($upload_file_path, true);

		if(isset($file_content)){
			$aim_list                = array();
			$system_column_name_list = array();

			$upload_list_tmp  = mb_convert_encoding($file_content, "UTF-8", "SJIS");
			$upload_list_tmp  = preg_replace("/\r\n|\r|\n/", "\n", $upload_list_tmp);
			$upload_list_line = explode("\n", $upload_list_tmp);

			foreach ($upload_list_line as $i => $line) {
				$upload_elem_list = array();

				if ($i == 0) {
					// ヘッダを読み込む
					$header_list = explode(",", $line);
					$i++;
					continue;
				} else if ($i == 1) {
					// システムカラムを設定
					$system_column_name_list = explode(",", str_replace('"', '', $line));
					$i++;
					continue;
				} else if (trim($line) == "") {
					break;
				}

				$upload_elem_list = explode(",", $line);

				// 設定対象のカテゴリ種別をバリデート
				if ($upload_elem_list[0] !== $category_element) continue;

				// 設定対象のカテゴリIDとアップロードファイルの内容をつきあわせる
				if (!in_array($upload_elem_list[1], $category_id_list)) continue;

				foreach ($upload_elem_list as $j => $value_tmp) {
					if ($j <= 2 || $system_column_name_list[$j] == "※この行は削除しないでください") continue;

					// 数字のみ抜き出す
					$value = preg_replace('/[^0-9]+/', '', trim($value_tmp));

					if (empty($value)) continue;

					$aim_value = array(
						'client_id' => $client_id,
						'target'    => $category_element.":".$upload_elem_list[1],
						'element'   => $system_column_name_list[$j],
						'value'     => $value,
					);
					$aim_list[$aim_value["target"]."_".$aim_value["element"]] = $aim_value;
				}
			}

			\File::delete($upload_file_path);

			return $this->response($aim_list, 200);
		}
	}
}
