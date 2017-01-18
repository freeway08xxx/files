<?php

require_once APPPATH."/const/categorygenre.php";
/**
 * カテゴリジャンルチェックコントローラ
 */
class Controller_CategoryGenre_CategorySetting_Update extends Controller_Rest
{
	// loginユーザ権限チェック用URL
	public $access_url = "/sem/universe_fast/universe.php";

	//カテゴリ設定一覧での更新処理
	public function post_update($client_id) {

		$genre_id = Input::post("genre_id");
		$category_data_list = Input::post("category_data_list");
		$error_text = null;

		// 変更履歴登録
		$values = array("client_id"   => $client_id,
						"category_genre_id" => $genre_id,
						"action_type_id"  => 3,
						"status_id" => 0
						);
		$ret = \Model_Data_CategoryHistory::ins($values);
		$history_id = $ret[0];

		try {

			//クライアントのアカウント一覧を取得する。
			$media_id_list = array_keys(CategoryGenreConst::$category_genre_media_list);
			$account_list_tmp = Model_Mora_Account::get_by_client($client_id, $media_id_list);
			foreach ($account_list_tmp as $account){
				$account_list[$account["account_id"]] = $account;
			}

			//処理結果用
			$error_list = array();
			//履歴用
			$history_account_id_list = array();
			$history_element_type_id_list = array();

			if(isset($category_data_list)){

				//登録済みカテゴリ一覧
				$category_big_list = \Model_Data_Category::get_for_category_genre_id('category_big_id', $client_id, $genre_id);
				$category_middle_list = \Model_Data_Category::get_for_category_genre_id('category_middle_id', $client_id, $genre_id);
				$category_list = \Model_Data_Category::get_for_category_genre_id('category_id', $client_id, $genre_id);

				//カテゴリ名一覧
				$category_big_name_list = array();
				foreach($category_big_list as $category_big) {
					$category_big_name_list[$category_big["category_name"]] = $category_big["id"];
				}
				$category_middle_name_list = array();
				foreach($category_middle_list as $category_middle) {
					$category_middle_name_list[$category_middle["category_name"]] = $category_middle["id"];
				}
				$category_name_list = array();
				foreach($category_list as $category) {
					$category_name_list[$category["category_name"]] = $category["id"];
				}

				// 新規登録対象カテゴリ
				$insert_category_big_list = array();
				$insert_category_middle_list = array();
				$insert_category_list = array();

				// 同一コンポーネントの設定矛盾をチェック
				$input_category_list = array();
				$category_setting_check_list = array();

				// 上位カテゴリのチェック用
				$check_category_list = array();
				$check_category_middle_list = array();

				$entry_account_list = array();

				//表示順でソート
				foreach ($category_data_list as $key => $value){
					$key_id[$key] = $value["no"];
				}
				array_multisort($key_id, SORT_ASC, $category_data_list);

				foreach ($category_data_list as $row_no => &$category_data) {
					$category_big_name = isset($category_big_name_list[$category_data["category_big_name"]]) && $category_data["category_big_name"] != "未設定" ? $category_data["category_big_name"] : null;
					$category_middle_name = isset($category_middle_name_list[$category_data["category_middle_name"]]) && $category_data["category_middle_name"] != "未設定" ? $category_data["category_middle_name"] : null;
					$category_name = isset($category_name_list[$category_data["category_name"]]) && $category_data["category_name"] != "未設定" ? $category_data["category_name"] : null;
					$category_type_name = array_search($category_data["category_type_name"], CategoryGenreConst::$category_genre_element_type_list) ? $category_data["category_type_name"] : null;
					$before_category_type_name = array_search($category_data["before_category_type_name"], CategoryGenreConst::$category_genre_element_type_list) ? $category_data["before_category_type_name"] : null;

					$media_id = array_keys(CategoryGenreConst::$category_genre_media_list, $category_data["media_name"]);
					$media_id = $media_id[0];
					$account_id = $category_data["account_id"];
					$campaign_id = $category_data["campaign_id"] ? $category_data["campaign_id"] : "";
					$ad_group_id = $category_data["ad_group_id"] ? $category_data["ad_group_id"] : "";
					$keyword_id = $category_data["keyword_id"] ? $category_data["keyword_id"] : "";

					// 大カテゴリが存在して、中カテゴリが存在しないものは、中カテゴリに大カテゴリを設定する。
					if ($category_big_name) {
						if (!$category_middle_name) {
							$category_middle_name = $category_big_name;
							$category_data_list[$row_no]["category_middle_name"] = $category_middle_name;
						}
					}

					// 中カテゴリが存在して、小カテゴリが存在しないものは、小カテゴリに中カテゴリを設定する。
					if ($category_middle_name) {
						if (!$category_name) {
							$category_name = $category_middle_name;
							$category_data_list[$row_no]["category_name"] = $category_name;
						}
					}

					// 中カテゴリまたは小カテゴリのみが存在する場合はエラー
					if (!$category_big_name && ($category_middle_name || $category_name)) {
						$error_text .= "中カテゴリまたは小カテゴリのみの設定はNGです[" . $category_data["no"] . "行目]";
						break;
					}

					if ($category_big_name || $category_middle_name || $category_name) {
						$set_category_flg = TRUE;
					} else {
						$set_category_flg = FALSE;
					}

					// チェック用のカテゴリを設定
					if($set_category_flg) {
						// 同一の中カテゴリに別の大カテゴリが設定されていたらエラー
						if (isset($check_category_list[$category_name]) && $check_category_list[$category_name] != $category_middle_name) {
							$error_text .= "同一の小カテゴリに別の中カテゴリが設定されています[" . $category_data["no"] . "行目]<br>";
							break;
						}
						$check_category_list[$category_name] = $category_middle_name;

						// 同一の中カテゴリに別の大カテゴリが設定されていたらエラー
						if (isset($check_category_middle_list[$category_middle_name]) && $check_category_middle_list[$category_middle_name] != $category_big_name) {
							$error_text .= "同一の中カテゴリに別の大カテゴリが設定されています[" . $category_data["no"] . "行目]<br>";
							break;
						}
						$check_category_middle_list[$category_middle_name] = $category_big_name;
					}

					// カテゴリ設定単位が記載されていないものはエラー
					if ($set_category_flg && !$category_type_name) {
					 	 // エラー
						$error_text .= "カテゴリ設定単位が指定されていません[" . $category_data["no"] . "行目]<br>";
						break;
					} elseif (!$set_category_flg && $category_type_name) {
						// エラー
						$error_text .= "大・中・小カテゴリが空白の場合、設定単位は指定できません[" . $category_data["no"] . "行目]<br>";
						break;
					}

					// コンポーネントIDの入力チェック
					if (isset($account_id) && !$account_id) {
						$error_text .= "アカウントIDを指定してください[" . $category_data["no"] . "行目]<br>";
						break;
					} elseif (!array_key_exists($account_id, $account_list)) {
						$error_text .= "アカウントIDがクライアントに存在しません[" . $category_data["no"] . "行目]<br>";
						break;
					}
					if ($category_type_name == "キャンペーン" || $category_type_name == "広告グループ" || $category_type_name == "キーワード") {
						if (!$campaign_id) {
							$error_text .= "キャンペーンIDを指定してください[" . $category_data["no"] . "行目]<br>";
							break;
						}
					}
					if ($category_type_name == "広告グループ" || $category_type_name == "キーワード") {
						if (!$ad_group_id) {
							$error_text .= "広告グループIDを指定してください[" . $category_data["no"] . "行目]<br>";
							break;
						}
					}

					if ($category_type_name == "キーワード") {
						if (!$keyword_id) {
							$error_text .= "キーワードIDを指定してください[" . $category_data["no"] . "行目]<br>";
							break;
						}
					}

					// カテゴリ設定を行うコンポーネントを登録
					if ($set_category_flg) {
						switch ($category_type_name) {
							case "アカウント" :

								$category_key = $account_id;
								break;
							case "キャンペーン" :
								$category_key = $account_id . ":" . $campaign_id;
								break;

							case "広告グループ" :
								$category_key = $account_id . ":" . $campaign_id . ":" . $ad_group_id;
								break;

							case "キーワード" :
								$category_key = $account_id . ":" . $campaign_id . ":" . $ad_group_id . ":" . $keyword_id;
								break;

							default :
								$error_text .= "カテゴリ設定単位が指定が不正です[" . $category_data["no"] . "行目]<br>";
								break;
						}

						if (!isset($input_category_list[$category_key])) {
							// コンポーネントのカテゴリを設定
							$input_category_list[$category_key]["category_big_name"] = $category_big_name;
							$input_category_list[$category_key]["category_middle_name"] = $category_middle_name;
							$input_category_list[$category_key]["category_name"] = $category_name;
						} else {
							// 同一コンポーネントに別カテゴリが設定されていないかチェック
							if ((isset($input_category_list[$category_key]["category_big_name"]) && isset($category_data[$account_key]["category_big_name"]) && $input_category_list[$category_key]["category_big_name"] !== $category_data[$account_key]["category_big_name"]) ||
							    (isset($input_category_list[$category_key]["category_middle_name"]) && isset($category_data[$account_key]["category_middle_name"]) && $input_category_list[$category_key]["category_middle_name"] !== $category_data[$account_key]["category_middle_name"]) ||
							    (isset($input_category_list[$category_key]["category_name"]) && isset($category_data[$account_key]["category_name"]) && $input_category_list[$category_key]["category_name"] !== $category_data[$account_key]["category_name"])) {

							    $error_text .= "同一コンポーネントに別カテゴリが設定されています[" . $category_data["no"] . "行目]<br>";
								break;
							}
						}

						// 未登録の大カテゴリ
						if ($category_big_name && !isset($category_big_name_list[$category_big_name])) {
							$insert_category_big_list[$category_big_name] = $category_big_name;
						}

						// 未登録の中カテゴリ
						if ($category_middle_name && !isset($category_middle_name_list[$category_middle_name])) {
							$insert_category_middle_list[$category_middle_name] = $category_middle_name;
						}

						// 未登録の小カテゴリ
						if ($category_name && !isset($category_name_list[$category_name])) {
							$insert_category_list[$category_name] = $category_name;
						}
					}

					$entry_account_list[$account_id] = $account_id;

					// 上位コンポーネントの設定矛盾をチェック
					foreach($category_data_list as $row_no => &$category_data) {
						if ((isset($category_data["category_big_name"]) && $category_data["category_big_name"]) || (isset($category_data["category_middle_name"]) && $category_data["category_middle_name"]) || (isset($category_data["category_name"]) && $category_data["category_name"])) {
							$set_category_flg = TRUE;
						} else {
							$set_category_flg = FALSE;
						}

						$media_id = array_keys(CategoryGenreConst::$category_genre_media_list, $category_data["media_name"]);
						$media_id = $media_id[0];
						$account_id = ltrim($category_data["account_id"], "'");
						$campaign_id = isset($category_data["campaign_id"]) ? ltrim($category_data["campaign_id"], "'") : "";
						$ad_group_id = isset($category_data["ad_group_id"]) ? ltrim($category_data["ad_group_id"], "'") : "";
						$keyword_id = isset($category_data["keyword_id"]) ? ltrim($category_data["keyword_id"], "'") : "";

						// カテゴリ設定を行うコンポーネントを登録
						if ($set_category_flg) {
							$account_key = $account_id;
							$campaign_key = $account_id . ":" . $campaign_id;
							$ad_group_key = $account_id . ":" . $campaign_id . ":" . $ad_group_id;
							$category_type_name = $category_data["category_type_name"];

							switch ($category_type_name) {
								case "アカウント" :
									break;
								case "キャンペーン" :
									if (isset($input_category_list[$account_key]) && $input_category_list[$account_key]) {
										$error_text .= "キャンペーン単位のカテゴリ設定に、アカウント単位の設定が存在します[" . $category_data["no"] . "行目]<br>";
									}
									break;

								case "広告グループ" :
									if (isset($input_category_list[$account_key]) && $input_category_list[$account_key]) {
										$error_text .= "広告グループ単位のカテゴリ設定に、アカウント単位の設定が存在します[" . $category_data["no"] . "行目]<br>";
									} elseif (isset($input_category_list[$campaign_key]) && $input_category_list[$campaign_key]) {
										$error_text .= "広告グループ単位のカテゴリ設定に、キャンペーン単位の設定が存在します[" . $category_data["no"] . "行目]<br>";
									}
									break;

								case "キーワード" :
									if (isset($input_category_list[$account_key]) && $input_category_list[$account_key]) {
										$error_text .= "キーワード単位のカテゴリ設定に、アカウント単位の設定が存在します[" . $category_data["no"] . "行目]<br>";
									} elseif (isset($input_category_list[$campaign_key]) && $input_category_list[$campaign_key]) {
										$error_text .= "キーワード単位のカテゴリ設定に、キャンペーン単位の設定が存在します[" . $category_data["no"] . "行目]<br>";
									} elseif (isset($input_category_list[$ad_group_key]) && $input_category_list[$ad_group_key]) {
										$error_text .= "キーワード単位のカテゴリ設定に、広告グループ単位の設定が存在します[" . $category_data["no"] . "行目]<br>";
									}
									break;

								default :
									$error_text .= "カテゴリ設定単位の指定が不正です。<br>";
									break;
							}
						}
					}
				}

				//エラーが無い場合は登録
				if(!$error_text){

					//未登録のカテゴリ登録
					$category_values = array();
					$category_values["client_id"] = $client_id;
					$category_values["category_genre_id"] = $genre_id;

					// 未登録の大カテゴリを登録
					if (isset($insert_category_big_list) && $insert_category_big_list) {
						foreach($insert_category_big_list as $insert_category_big) {
							$category_values["category_name"] = $insert_category_big;
							$insert_id = \Model_Data_Category::ins("category_big_id", null, $category_values);
							$category_big_name_list[$insert_category_big] = $insert_id;
						}
					}
					// 未登録の中カテゴリを登録
					if (isset($insert_category_middle_list) && $insert_category_middle_list) {
						foreach($insert_category_middle_list as $insert_category_middle) {
							$category_values["category_name"] = $insert_category_middle;
							$insert_id = \Model_Data_Category::ins("category_middle_id", null, $category_values);
							$category_middle_name_list[$insert_category_middle] = $insert_id;
						}
					}
					// 未登録の小カテゴリを登録
					if (isset($insert_category_list) && $insert_category_list) {
						foreach($insert_category_list as $insert_category) {
							$category_values["category_name"] = $insert_category;
							$insert_id = \Model_Data_Category::ins("category_id", null, $category_values);
							$category_name_list[$insert_category] = $insert_id;
						}
					}

					//更新・削除
					foreach($category_data_list as $row_no => &$category_data) {
						$insert_values = array();
						$delete_values = array();

						$category_big_name = $category_data["category_big_name"];
						$category_middle_name = $category_data["category_middle_name"];
						$category_name = $category_data["category_name"];

						$category_big_id = $category_big_name_list[$category_big_name];
						$category_middle_id = $category_middle_name_list[$category_middle_name];
						$category_id = $category_name_list[$category_name];

						$category_type_name = $category_data["category_type_name"];
						$before_category_type_name = $category_data["before_category_type_name"];

						$element_type_id = array_search($category_data["category_type_name"], CategoryGenreConst::$category_genre_element_type_list);
						//複数コンポーネントで設定されている場合、一番大きい階層を取る
						if(preg_match('/\//', $category_data["before_category_type_name"])){
							$before_element_type_name_list = explode('/', $category_data["before_category_type_name"]);
							$before_element_type_name = min($before_element_type_name_list);
							$before_element_type_id = array_search($before_element_type_name, CategoryGenreConst::$category_genre_element_type_list);
						}else{
							$before_element_type_id = array_search($category_data["before_category_type_name"], CategoryGenreConst::$category_genre_element_type_list);
						}

						$media_id = array_keys(CategoryGenreConst::$category_genre_media_list, $category_data["media_name"]);
						$media_id = $media_id[0];
						$account_id = $category_data["account_id"];
						$campaign_id = $category_data["campaign_id"] ? $category_data["campaign_id"] : "";
						$ad_group_id = $category_data["ad_group_id"] ? $category_data["ad_group_id"] : "";
						$keyword_id = $category_data["keyword_id"] ? $category_data["keyword_id"] : "";

						//履歴用にリストに保持
						$history_account_id_list[$account_id] = $account_id;
						$history_element_type_id_list[$element_type_id] = $element_type_id;

						//更新リスト
						$insert_values = array("client_id" => $client_id,
												 "category_genre_id" => $genre_id,
												 "category_big_id" => $category_big_id,
												 "category_middle_id" => $category_middle_id,
												 "category_id" => $category_id,
												 "media_id" => $media_id,
												 "account_id" => $account_id,
												 "campaign_id" => $campaign_id,
												 "ad_group_id" => $ad_group_id,
												 "keyword_id" => $keyword_id,
												 "element_type_id" => $element_type_id
												);

						//設定単位以下のコンポーネントにIDが存在する場合は、削除
						if($element_type_id < 4){
							$elem = $element_type_id + 1;
							for($i=$elem; $i<5; $i++){
								if(isset($insert_values[CategoryGenreConst::$category_genre_element_list[$i]."_id"])){
									unset($insert_values[CategoryGenreConst::$category_genre_element_list[$i]."_id"]);
								}
							}
						}

						$update_list[$element_type_id][] = $insert_values;

						//削除リスト
						if($before_element_type_id > 0){
							$delete_values = array("client_id" => $client_id,
													 "category_genre_id" => $genre_id,
													 "account_id" => $account_id,
													 "campaign_id" => $campaign_id,
													 "ad_group_id" => $ad_group_id,
													 "keyword_id" => $keyword_id,
													);

							$diff = $element_type_id - $before_element_type_id;
							if($diff < 0){
								$elem = $element_type_id + 1;
								for($i=$elem; $i<5; $i++){
									if(isset($delete_values[CategoryGenreConst::$category_genre_element_list[$i]."_id"])){
										unset($delete_values[CategoryGenreConst::$category_genre_element_list[$i]."_id"]);
									}
								}
							}elseif($diff > 0){
								$elem = $element_type_id;
								for($i=$elem; $i<5; $i++){
									if(isset($delete_values[CategoryGenreConst::$category_genre_element_list[$i]."_id"])){
										unset($delete_values[CategoryGenreConst::$category_genre_element_list[$i]."_id"]);
									}
								}
							}
							//設定単位以下のコンポーネントにIDが存在する場合は、削除
							if($before_element_type_id < 4){
								$elem = $before_element_type_id + 1;
								for($i=$elem; $i<5; $i++){
									if(isset($delete_values[CategoryGenreConst::$category_genre_element_list[$i]."_id"])){
										unset($delete_values[CategoryGenreConst::$category_genre_element_list[$i]."_id"]);
									}
								}
							}
							$delete_key = implode($delete_values);
							$delete_list[$delete_key] = $delete_values;
						}
					}
				}
			}

			if(isset($update_list) || isset($delete_list)){
				//更新
				if(isset($delete_list)){
					\Model_Data_CategoryElement::del($delete_list);
				}
				if(isset($update_list)){
					//分割して実行
					for($i=1; $i<5; $i++){
						if(isset($update_list[$i])){
							$update_all_list = array_chunk($update_list[$i], 1000);
							foreach($update_all_list as $update) {
								\Model_Data_CategoryElement::ins($update);
							}
						}
					}
				}

				// 変更履歴更新：正常終了時
				$values = array("file_path"  => "",
								"file_name" => "",
								"status_id"  => 1,
								"account_id_list" => serialize($history_account_id_list),
								"element_type_id_list" => serialize($history_element_type_id_list)
								);
				\Model_Data_CategoryHistory::upd($history_id, $values);
				return false;
			}else{
				if(!$error_text){
					$error_text = "エラー：更新対象データが存在しません";
				}
			}

			if($error_text){
				// エラー時は履歴を削除して、エラー内容をアラート表示
				\Model_Data_CategoryHistory::del($history_id);
				return $this->response($error_text, 200);
			}

		} catch (\Exception $e) {
			logger(ERROR, 'category setting error. message:'.$e, 'CategorySetting');
			// 変更履歴更新：エラー時
			$values = array("file_path" => "",
							"status_id" => 3,
							"account_id_list" => serialize($history_account_id_list),
							"element_type_id_list" => serialize($history_element_type_id_list)
							);
			\Model_Data_CategoryHistory::upd($history_id, $values);

			$error_text = "エラー：更新失敗しました";
			return $this->response($error_text, 200);
		}
	}

	//カテゴリ設定一覧での解除処理
	public function post_delete($client_id) {

		$genre_id = Input::post("genre_id");
		$category_data_list = Input::post("category_data_list");

		// 変更履歴登録
		$values = array("client_id"   => $client_id,
						"category_genre_id" => $genre_id,
						"action_type_id"  => 4,
						"status_id" => 0
						);
		$ret = \Model_Data_CategoryHistory::ins($values);
		$history_id = $ret[0];

		try {

			//クライアントのアカウント一覧を取得する。
			$media_id_list = array_keys(CategoryGenreConst::$category_genre_media_list);
			$account_list_tmp = Model_Mora_Account::get_by_client($client_id, $media_id_list);
			foreach ($account_list_tmp as $account){
				$account_list[$account["account_id"]] = $account;
			}

			//処理結果用
			$error_list = array();
			//履歴用
			$history_account_id_list = array();
			$history_element_type_id_list = array();

			if(isset($category_data_list)){

				//表示順でソート
				foreach ($category_data_list as $key => $value){
					//解除フラグが無いものは更新対象外
					if(!isset($value["elem_delete_flg"])){
						unset($category_data_list[$key]);
					}else{
						$key_id[$key] = $value["no"];
					}
				}
				array_multisort($key_id, SORT_ASC, $category_data_list);

				//登録済みカテゴリ一覧
				$category_big_list = \Model_Data_Category::get_for_category_genre_id('category_big_id', $client_id, $genre_id);
				$category_middle_list = \Model_Data_Category::get_for_category_genre_id('category_middle_id', $client_id, $genre_id);
				$category_list = \Model_Data_Category::get_for_category_genre_id('category_id', $client_id, $genre_id);

				//カテゴリ名一覧
				$category_big_name_list = array();
				foreach($category_big_list as $category_big) {
					$category_big_name_list[$category_big["category_name"]] = $category_big["id"];
				}
				$category_middle_name_list = array();
				foreach($category_middle_list as $category_middle) {
					$category_middle_name_list[$category_middle["category_name"]] = $category_middle["id"];
				}
				$category_name_list = array();
				foreach($category_list as $category) {
					$category_name_list[$category["category_name"]] = $category["id"];
				}

				// 新規登録対象カテゴリ
				$insert_category_big_list = array();
				$insert_category_middle_list = array();
				$insert_category_list = array();

				// 同一コンポーネントの設定矛盾をチェック
				$input_category_list = array();
				$category_setting_check_list = array();

				// 上位カテゴリのチェック用
				$check_category_list = array();
				$check_category_middle_list = array();

				$entry_account_list = array();

				//削除
				foreach($category_data_list as $row_no => &$category_data) {
					$delete_values = array();

					$element_type_id = array_search($category_data["category_type_name"], CategoryGenreConst::$category_genre_element_type_list);
					//複数コンポーネントで設定されている場合、一番大きい階層を取る
					if(preg_match('/\//', $category_data["before_category_type_name"])){
						$before_element_type_name_list = explode('/', $category_data["before_category_type_name"]);
						$before_element_type_name = min($before_element_type_name_list);
						$before_element_type_id = array_search($before_element_type_name, CategoryGenreConst::$category_genre_element_type_list);
					}else{
						$before_element_type_id = array_search($category_data["before_category_type_name"], CategoryGenreConst::$category_genre_element_type_list);
					}

					$media_id = array_keys(CategoryGenreConst::$category_genre_media_list, $category_data["media_name"]);
					$media_id = $media_id[0];
					$account_id = $category_data["account_id"];
					$campaign_id = $category_data["campaign_id"] ? $category_data["campaign_id"] : "";
					$ad_group_id = $category_data["ad_group_id"] ? $category_data["ad_group_id"] : "";
					$keyword_id = $category_data["keyword_id"] ? $category_data["keyword_id"] : "";

					//履歴用にリストに保持
					$history_account_id_list[$account_id] = $account_id;
					$history_element_type_id_list[$before_element_type_id] = $before_element_type_id;

					//削除リスト
					if($before_element_type_id > 0){
						$delete_values = array("client_id" => $client_id,
												 "category_genre_id" => $genre_id,
												 "account_id" => $account_id,
												 "campaign_id" => $campaign_id,
												 "ad_group_id" => $ad_group_id,
												 "keyword_id" => $keyword_id,
												);

						$diff = $element_type_id - $before_element_type_id;
						if($diff < 0){
							$elem = $element_type_id + 1;
							for($i=$elem; $i<5; $i++){
								if(isset($delete_values[CategoryGenreConst::$category_genre_element_list[$i]."_id"])){
									unset($delete_values[CategoryGenreConst::$category_genre_element_list[$i]."_id"]);
								}
							}
						}elseif($diff > 0){
							$elem = $element_type_id;
							for($i=$elem; $i<5; $i++){
								if(isset($delete_values[CategoryGenreConst::$category_genre_element_list[$i]."_id"])){
									unset($delete_values[CategoryGenreConst::$category_genre_element_list[$i]."_id"]);
								}
							}
						}
						//設定単位以下のコンポーネントにIDが存在する場合は、削除
						if($before_element_type_id < 4){
							$elem = $before_element_type_id + 1;
							for($i=$elem; $i<5; $i++){
								if(isset($delete_values[CategoryGenreConst::$category_genre_element_list[$i]."_id"])){
									unset($delete_values[CategoryGenreConst::$category_genre_element_list[$i]."_id"]);
								}
							}
						}
						$delete_key = implode($delete_values);
						$delete_list[$delete_key] = $delete_values;
					}
				}

				//設定解除更新
				if(isset($delete_list)){
					\Model_Data_CategoryElement::del($delete_list);

					$values = array("file_path"  => "",
									"file_name" => "",
									"status_id"  => 1,
									"account_id_list" => serialize($history_account_id_list),
									"element_type_id_list" => serialize($history_element_type_id_list)
									);

					\Model_Data_CategoryHistory::upd($history_id, $values);
					return false;
				}else{
					\Model_Data_CategoryHistory::del($history_id);
					$error_text = "カテゴリが設定されていません";
					return $this->response($error_text, 200);
				}
			}

		} catch (\Exception $e) {
			logger(ERROR, 'category setting error. message:'.$e, 'CategorySetting');
			// 変更履歴更新：エラー時
			$values = array("file_path" => "",
							"status_id" => 3,
							"account_id_list" => serialize($history_account_id_list),
							"element_type_id_list" => serialize($history_element_type_id_list)
							);
			\Model_Data_CategoryHistory::upd($history_id, $values);

			$error_text = "エラー：設定解除の更新が失敗しました";
			return $this->response($error_text, 200);
		}
	}

}
