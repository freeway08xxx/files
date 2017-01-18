<?php
/**
 * falconレポートキャンペーン設定系コントローラ
 **/
require_once APPPATH."/const/main.php";
require_once APPPATH."/const/categorygenre.php";

class Controller_CategoryGenre_CategorySetting_Export extends Controller_CategoryGenre_Base {

	// loginユーザ権限チェック用URL
	public $access_url = "/sem/universe_fast/universe.php";

	public function before() {

		parent::before();

		$this->data["action_target"] = Input::post("action_target");
		$this->data["genre_id"] = Input::post("select_genre_id");
		$this->data["category_elem"] = Input::post("select_category_elem");
		$this->data["no_setting_flg"] = Input::post("no_setting_flg");
		$this->data["element_type_id"] = Input::post("select_element_type_id");
		$this->data["account_id_list"] = Input::post("account_id_list");

		$this->data["campaign_search_like"] = Input::post("campaign_search_like");
		$this->data["except_campaign_search"] = Input::post("except_campaign_search");
		$this->data["campaign_search_type"] = Input::post("campaign_search_type");
		$this->data["campaign_search_text"] = Input::post("campaign_search_text");

		$this->data["ad_group_search_like"] = Input::post("ad_group_search_like");
		$this->data["except_ad_group_search"] = Input::post("except_ad_group_search");
		$this->data["ad_group_search_type"] = Input::post("ad_group_search_type");
		$this->data["ad_group_search_text"] = Input::post("ad_group_search_text");

		$this->data["keyword_search_like"] = Input::post("keyword_search_like");
		$this->data["except_keyword_search"] = Input::post("except_keyword_search");
		$this->data["keyword_search_type"] = Input::post("keyword_search_type");
		$this->data["keyword_search_text"] = Input::post("keyword_search_text");

		$this->data["url_search_like"] = Input::post("url_search_like");
		$this->data["except_url_search"] = Input::post("except_url_search");
		$this->data["url_search_type"] = Input::post("url_search_type");
		$this->data["url_search_text"] = Input::post("url_search_text");

		$this->data["category_search_like"] = Input::post("category_search_like");
		$this->data["except_category_search"] = Input::post("except_category_search");
		$this->data["category_search_type"] = Input::post("category_search_type");
		$this->data["category_search_text"] = Input::post("category_search_text");

	}

	//設定シートダウンロード
	public function action_download($client_id) {

		$checker = true;
		if(!Input::is_ajax()) {
			$checker = false;
		} elseif (!$this->admin_flg) {
			$checker = Model_Mora_Client::check_client_user($client_id, Session::get('user_id_sem'));
		}
		if (!$checker) {
			return new Response(false, 404);
		}

		try {

			//履歴用
			$history_account_id_list = array();

			// 変更履歴登録
			$values = array("client_id"   => $client_id,
							"category_genre_id" => $this->data["genre_id"],
							"action_type_id"  => 1,
							"status_id" => 0
							);
			$ret = \Model_Data_CategoryHistory::ins($values);
			$history_id = $ret[0];

			//large_companyかどうか
			$company_id = \Model_Mora_Client::get_large_company($client_id);

			//指定アカウント毎にループ
			foreach ($this->data["account_id_list"] as $account_id) {

				//media_id, account_id 分割
				list($tmp_media_id, $tmp_account_id) = explode("//",  $account_id);

				$history_account_id_list[$tmp_account_id] = $tmp_account_id;

				//指定アカウント情報取得
				$account = \Model_Mora_Account::get_for_account($tmp_account_id);

				//出力コンポーネント単位×element_type_idごとのelement_type_id数
				$element_type_id_list_tmp = \Model_Data_CategoryElement::get_elementtypeid_list($client_id, $this->data["genre_id"], $tmp_account_id, $this->data["element_type_id"]);
				//出力コンポーネント単位ごとのelement_type_id数
				$get_element_count_list = \Model_Data_CategoryElement::get_element_count_list($client_id, $this->data["genre_id"], $tmp_account_id, $this->data["element_type_id"]);

				$element_type_id_list = array();
				foreach ($element_type_id_list_tmp as $key => $value){
					$element_type_id_list[$value["unique_key"]][$value["element_type_id"]]["element_type_id"] = $value["element_type_id"];
					$element_type_id_list[$value["unique_key"]][$value["element_type_id"]]["count_big"] = $value["count_big"];
					$element_type_id_list[$value["unique_key"]][$value["element_type_id"]]["count_middle"] = $value["count_middle"];
					$element_type_id_list[$value["unique_key"]][$value["element_type_id"]]["count_min"] = $value["count_min"];
					$element_type_id_list[$value["unique_key"]][$value["element_type_id"]]["big_flg"] = $get_element_count_list[$value["unique_key"]]["total_big"] == $value["count_big"] && $get_element_count_list[$value["unique_key"]]["total_big"] == 1 ? true : false;
					$element_type_id_list[$value["unique_key"]][$value["element_type_id"]]["middle_flg"] = $get_element_count_list[$value["unique_key"]]["total_middle"] == $value["count_middle"] && $get_element_count_list[$value["unique_key"]]["total_middle"] == 1 ? true : false;
					$element_type_id_list[$value["unique_key"]][$value["element_type_id"]]["min_flg"] = $get_element_count_list[$value["unique_key"]]["total_min"] == $value["count_min"] && $get_element_count_list[$value["unique_key"]]["total_min"] == 1 ? true : false;
				}

				// 結果キーリスト
				if(!isset($file_contents)){
					$file_contents[] = CategoryGenreConst::$category_genre_header_list[$this->data["element_type_id"]];
					$file_contents[] = array_flip(CategoryGenreConst::$category_genre_header_list[$this->data["element_type_id"]]);
				}

				//カテゴリ設定情報を取得
				$category_element_list = \Model_Data_CategoryElement::get_category_list($client_id, $this->data["genre_id"], $tmp_account_id, $this->data["element_type_id"]);

				// アカウント別
				if ($this->data["element_type_id"] == 1) {
					//初期化
					$unique_key =array();
					$target_flg = false;

					$unique_key[1] = $tmp_media_id . ":" .$tmp_account_id;

					//カテゴリ情報を紐付け
					list($element_type_name, $category_big_name, $category_middle_name, $category_name) = \Util_CategoryGenre_CategorySetting::get_element_info($this->data["element_type_id"], $unique_key, $element_type_id_list, $category_element_list);

					//カテゴリ名でフィルタリング
					if (!empty($this->data["category_search_text"])) {
						$ret = \Util_Common_Filter::filterElements(array($category_big_name, $category_middle_name, $category_name),
																   $this->data["category_search_text"],
																   $this->data["category_search_like"],
																   $this->data["category_search_type"],
																   $this->data["except_category_search"]);
						if ($ret) {
							$target_flg = true;
						}
					}else{
						$target_flg = true;
					}

					//未設定のみの場合
					if($this->data["no_setting_flg"] && $element_type_name != ""){
						$target_flg = false;
					}

					if($target_flg){
						$file_contents[] = array(CategoryGenreConst::$category_genre_media_list[$tmp_media_id],
												 $tmp_account_id,
												 $account["account_name"],
												 $element_type_name ? $element_type_name : "未設定",
												 $category_big_name,
												 $category_middle_name,
												 $category_name,
												 CategoryGenreConst::$category_genre_element_type_list[$this->data["element_type_id"]],
												 "",
												 "",
												 ""
												 );
					}

				}else{

					// キャンペーン・広告グループ・キーワードの場合は事前にテーブル名を取得
					$table_name = Util_Common_Table::get_structure_table_name($tmp_media_id, CategoryGenreConst::$category_genre_element_list[$this->data["element_type_id"]], $company_id);

					//テーブルの存在確認
					if (!$table_name) {
						logger(ERROR, "No Table Error:" . $account_id);
						continue;
					}

					// キャンペーン
					if ($this->data["element_type_id"] == 2) {
						//指定アカウントのキャンペーン情報取得
						$campaign_list = \Model_Structure_Campaign::get($table_name, $tmp_media_id, $tmp_account_id);

						foreach ($campaign_list as $campaign) {
							//初期化
							$unique_key =array();
							$target_flg = false;

							//キャンペーン名でフィルタリング
							if (!empty($this->data["campaign_search_text"])) {
								$ret = \Util_Common_Filter::filterElements(array($campaign["campaign_id"], $campaign["campaign_name"]),
																		   $this->data["campaign_search_text"],
																		   $this->data["campaign_search_like"],
																		   $this->data["campaign_search_type"],
																		   $this->data["except_campaign_search"]);
								if ($ret) {
									$target_flg = true;
								}
							}else{
								$target_flg = true;
							}

							if($target_flg){
								$unique_key[1] = $campaign["media_id"] . ":" .$campaign["account_id"];
								$unique_key[2] = $campaign["media_id"] . ":" .$campaign["account_id"] . ":" . $campaign["campaign_id"];

								//ステータスを共通のものに変換
								if (CategoryGenreConst::$category_genre_status_replace_list[strtolower($campaign["campaign_status"])]) {
									$campaign_status = CategoryGenreConst::$category_genre_status_replace_list[strtolower($campaign["campaign_status"])];
								}else{
									$campaign_status = null;
								}

								//カテゴリ情報を紐付け
								list($element_type_name, $category_big_name, $category_middle_name, $category_name) = \Util_CategoryGenre_CategorySetting::get_element_info($this->data["element_type_id"], $unique_key, $element_type_id_list, $category_element_list);

								//カテゴリ名でフィルタリング
								if (!empty($this->data["category_search_text"])) {
									$ret = \Util_Common_Filter::filterElements(array($category_big_name, $category_middle_name, $category_name),
																			   $this->data["category_search_text"],
																			   $this->data["category_search_like"],
																			   $this->data["category_search_type"],
																			   $this->data["except_category_search"]);
									if (!$ret) {
										$target_flg = false;
									}
								}
							}

							//未設定のみの場合
							if($this->data["no_setting_flg"] && $element_type_name != ""){
								$target_flg = false;
							}

							if($target_flg){
								$file_contents[] = array(CategoryGenreConst::$category_genre_media_list[$tmp_media_id],
														 $tmp_account_id,
														 $account["account_name"],
														 $campaign["campaign_id"],
														 $campaign["campaign_name"],
														 $campaign_status,
														 $element_type_name ? $element_type_name : "未設定",
														 $category_big_name,
														 $category_middle_name,
														 $category_name,
														 CategoryGenreConst::$category_genre_element_type_list[$this->data["element_type_id"]],
														 "",
														 "",
														 ""
														 );
							}
						}
					// 広告グループ
					}elseif ($this->data["element_type_id"] == 3) {
						//指定アカウントのキャンペーン情報取得
						$cpn_media_name = \Util_Common_Table::get_structure_media_name($tmp_media_id);
						$cpn_table_name = \Util_Common_Table::get_structure_table_name($tmp_media_id, 'campaign', $company_id);
						$campaign_list = \Model_Structure_Campaign::get($cpn_table_name, $tmp_media_id, $tmp_account_id);

						//指定アカウントの広告グループ情報取得
						$ad_group_list = \Model_Structure_AdGroup::get($table_name, $tmp_media_id, $tmp_account_id);

						foreach ($ad_group_list as $ad_group) {
							//初期化
							$unique_key =array();
							$target_flg = false;

							$cpn_key = $ad_group["media_id"] . "::" .$ad_group["account_id"] . "::" . $ad_group["campaign_id"];
							$unique_key[1] = $ad_group["media_id"] . ":" .$ad_group["account_id"];
							$unique_key[2] = $ad_group["media_id"] . ":" .$ad_group["account_id"] . ":" . $ad_group["campaign_id"];
							$unique_key[3] = $ad_group["media_id"] . ":" .$ad_group["account_id"] . ":" . $ad_group["campaign_id"] . ":" . $ad_group["ad_group_id"];

							if(isset($campaign_list[$cpn_key]["campaign_name"])){
								$campaign_name = $campaign_list[$cpn_key]["campaign_name"];
							}else{
								$campaign_name = null;
							}

							//キャンペーン名でフィルタリング
							if (!empty($this->data["campaign_search_text"])) {
								$ret = \Util_Common_Filter::filterElements(array($ad_group["campaign_id"], $campaign_name),
																		   $this->data["campaign_search_text"],
																		   $this->data["campaign_search_like"],
																		   $this->data["campaign_search_type"],
																		   $this->data["except_campaign_search"]);
								if ($ret) {
									$target_flg = true;
								}
							}else{
								$target_flg = true;
							}

							if($target_flg){
								//広告グループ名でフィルタリング
								if (!empty($this->data["ad_group_search_text"])) {
									$ret = \Util_Common_Filter::filterElements(array($ad_group["ad_group_id"], $ad_group["ad_group_name"]),
																			   $this->data["ad_group_search_text"],
																			   $this->data["ad_group_search_like"],
																			   $this->data["ad_group_search_type"],
																			   $this->data["except_ad_group_search"]);
									if (!$ret) {
										$target_flg = false;
									}
								}
							}

							if($target_flg){
								//ステータスを共通のものに変換
								if (isset($campaign_list[$cpn_key]["campaign_status"]) && CategoryGenreConst::$category_genre_status_replace_list[strtolower($campaign_list[$cpn_key]["campaign_status"])]) {
									$camapign_status = CategoryGenreConst::$category_genre_status_replace_list[strtolower($campaign_list[$cpn_key]["campaign_status"])];
								}else{
									$camapign_status = null;
								}

								//カテゴリ情報を紐付け
								list($element_type_name, $category_big_name, $category_middle_name, $category_name) = \Util_CategoryGenre_CategorySetting::get_element_info($this->data["element_type_id"], $unique_key, $element_type_id_list, $category_element_list);

								//カテゴリ名でフィルタリング
								if (!empty($this->data["category_search_text"])) {
									$ret = \Util_Common_Filter::filterElements(array($category_big_name, $category_middle_name, $category_name),
																			   $this->data["category_search_text"],
																			   $this->data["category_search_like"],
																			   $this->data["category_search_type"],
																			   $this->data["except_category_search"]);
									if (!$ret) {
										$target_flg = false;
									}
								}

							}

							//未設定のみの場合
							if($this->data["no_setting_flg"] && $element_type_name != ""){
								$target_flg = false;
							}

							if($target_flg){
								$file_contents[] = array(CategoryGenreConst::$category_genre_media_list[$tmp_media_id],
														 $tmp_account_id,
														 $account["account_name"],
														 $ad_group["campaign_id"],
														 $campaign_name,
														 $camapign_status,
														 $ad_group["ad_group_id"],
														 $ad_group["ad_group_name"],
														 $element_type_name ? $element_type_name : "未設定",
														 $category_big_name,
														 $category_middle_name,
														 $category_name,
														 CategoryGenreConst::$category_genre_element_type_list[$this->data["element_type_id"]],
														 "",
														 "",
														 ""
														 );
							}
						}
					// キーワード
					}elseif ($this->data["element_type_id"] == 4) {
						//指定アカウントのキャンペーン情報取得
						$cpn_media_name = \Util_Common_Table::get_structure_media_name($tmp_media_id);
						$cpn_table_name = \Util_Common_Table::get_structure_table_name($tmp_media_id, 'campaign', $company_id);
						$campaign_list = \Model_Structure_Campaign::get($cpn_table_name, $tmp_media_id, $tmp_account_id);

						//指定アカウントの広告グループ情報取得
						$adg_media_name = \Util_Common_Table::get_structure_media_name($tmp_media_id);
						$adg_table_name = \Util_Common_Table::get_structure_table_name($tmp_media_id, 'ad_group', $company_id);
						$ad_group_list = \Model_Structure_AdGroup::get($adg_table_name, $tmp_media_id, $tmp_account_id);
						$ad_group_list_key = \Model_Structure_AdGroup::get_adg_key($adg_table_name, $tmp_media_id, $tmp_account_id);

						//指定アカウントのキーワード情報取得
						$keyword_list = \Model_Structure_Keyword::get($table_name, $tmp_media_id, $tmp_account_id);

						foreach ($keyword_list as $keyword) {
							//初期化
							$unique_key = array();
							$target_flg = false;

							$cpn_key = $keyword["media_id"] . "::" .$keyword["account_id"] . "::" . $ad_group_list_key[$keyword['ad_group_id']]["campaign_id"];

							$unique_key[1] = $keyword["media_id"] . ":" .$keyword["account_id"];
							$unique_key[2] = $keyword["media_id"] . ":" .$keyword["account_id"] . ":" . $ad_group_list_key[$keyword['ad_group_id']]["campaign_id"];
							$unique_key[3] = $keyword["media_id"] . ":" .$keyword["account_id"] . ":" . $ad_group_list_key[$keyword['ad_group_id']]["campaign_id"] . ":" . $keyword["ad_group_id"];
							$unique_key[4] = $keyword["media_id"] . ":" .$keyword["account_id"] . ":" . $ad_group_list_key[$keyword['ad_group_id']]["campaign_id"] . ":" . $keyword["ad_group_id"] . ":" . $keyword["keyword_id"];

							if(isset($campaign_list[$cpn_key]["campaign_id"]) && isset($campaign_list[$cpn_key]["campaign_name"])){
								$campaign_id = $campaign_list[$cpn_key]["campaign_id"];
								$campaign_name = $campaign_list[$cpn_key]["campaign_name"];
							}else{
								$campaign_id = null;
								$campaign_name = null;
							}

							//キャンペーン名でフィルタリング
							if (!empty($this->data["campaign_search_text"])) {
								$ret = \Util_Common_Filter::filterElements(array($campaign_id, $campaign_name),
																		   $this->data["campaign_search_text"],
																		   $this->data["campaign_search_like"],
																		   $this->data["campaign_search_type"],
																		   $this->data["except_campaign_search"]);
								if ($ret) {
									$target_flg = true;
								}
							}else{
								$target_flg = true;
							}

							if($target_flg){
								if(isset($ad_group_list_key[$keyword['ad_group_id']]["ad_group_name"])){
									$ad_group_name = $ad_group_list_key[$keyword['ad_group_id']]["ad_group_name"];
								}else{
									$ad_group_name = null;
								}

								//広告グループ名でフィルタリング
								if (!empty($this->data["ad_group_search_text"])) {
									$ret = \Util_Common_Filter::filterElements(array($keyword['ad_group_id'], $ad_group_name),
																			   $this->data["ad_group_search_text"],
																			   $this->data["ad_group_search_like"],
																			   $this->data["ad_group_search_type"],
																			   $this->data["except_ad_group_search"]);
									if (!$ret) {
										$target_flg = false;
									}
								}
							}

							if($target_flg){
								//キーワードでフィルタリング
								if (!empty($this->data["keyword_search_text"])) {
									$ret = \Util_Common_Filter::filterElements(array($keyword["keyword_id"], $keyword["keyword"]),
																			   $this->data["keyword_search_text"],
																			   $this->data["keyword_search_like"],
																			   $this->data["keyword_search_type"],
																			   $this->data["except_keyword_search"]);
									if (!$ret) {
										$target_flg = false;
									}
								}
							}

							if($target_flg){
								//リンク先URLでフィルタリング
								if (!empty($this->data["url_search_text"])) {
									$ret = \Util_Common_Filter::filterElements(array($keyword["link_url"], $keyword["link_url"]),
																			   $this->data["url_search_text"],
																			   $this->data["url_search_like"],
																			   $this->data["url_search_type"],
																			   $this->data["except_url_search"]);
									if (!$ret) {
										$target_flg = false;
									}
								}
							}

							if($target_flg){
								//ステータスを共通のものに変換
								if (isset($campaign_list[$cpn_key]["campaign_status"]) && CategoryGenreConst::$category_genre_status_replace_list[strtolower($campaign_list[$cpn_key]["campaign_status"])]) {
									$camapign_status = CategoryGenreConst::$category_genre_status_replace_list[strtolower($campaign_list[$cpn_key]["campaign_status"])];
								}else{
									$camapign_status = null;
								}

								//カテゴリ情報を紐付け
								list($element_type_name, $category_big_name, $category_middle_name, $category_name) = \Util_CategoryGenre_CategorySetting::get_element_info($this->data["element_type_id"], $unique_key, $element_type_id_list, $category_element_list);

								//カテゴリ名でフィルタリング
								if (!empty($this->data["category_search_text"])) {
									$ret = \Util_Common_Filter::filterElements(array($category_big_name, $category_middle_name, $category_name),
																			   $this->data["category_search_text"],
																			   $this->data["category_search_like"],
																			   $this->data["category_search_type"],
																			   $this->data["except_category_search"]);
									if (!$ret) {
										$target_flg = false;
									}
								}
							}

							//未設定のみの場合
							if($this->data["no_setting_flg"] && $element_type_name != ""){
								$target_flg = false;
							}

							if($target_flg){
								$file_contents[] = array(CategoryGenreConst::$category_genre_media_list[$tmp_media_id],
														 $tmp_account_id,
														 $account["account_name"],
														 $campaign_id,
														 $campaign_name,
														 $camapign_status,
														 $keyword["ad_group_id"],
														 $ad_group_name,
														 $keyword["keyword_id"],
														 $keyword["keyword"],
														 $keyword["link_url"],
														 $element_type_name ? $element_type_name : "未設定",
														 $category_big_name,
														 $category_middle_name,
														 $category_name,
														 CategoryGenreConst::$category_genre_element_type_list[$this->data["element_type_id"]],
														 "",
														 "",
														 ""
														 );
							}
						}
					}
				}
			}

		} catch (\Exception $e) {
			logger(ERROR, 'category setting error. message:'.$e, 'CategorySetting');
			// 変更履歴更新：エラー時
			$values = array("file_path" => "",
							"status_id" => 3,
							"account_id_list" => serialize($history_account_id_list),
							"element_type_id_list" => serialize(array($this->data["element_type_id"]))
							);
		}

		//クライアント名取得
		$client_name = \Model_Mora_Client::get_client_name($client_id);
		if($client_name["client_name"]){
			$client_name["company_name"] = $client_name["company_name"]. "(" . $client_name["client_name"] .")";
		}
		//カテゴリジャンル名取得
		$genre_info = \Model_Data_CategoryGenre::get($this->data["genre_id"]);

		$dsp_filename = "カテゴリ設定【" . $client_name["company_name"] ."】" . $genre_info["category_genre_name"] . "(" . CategoryGenreConst::$category_genre_element_type_list[$this->data["element_type_id"]] . "単位)_" . date("YmdHis") . ".csv";

		//結果ファイル作成
		$filename = "category_setting_" . $client_id ."_" . $this->data["genre_id"] . "_" . date("YmdHis") . ".csv";
		$fp = fopen(CATEGORY_GENRE_SETTING_DIR.$filename, 'w');

		$csv_data = "";
		foreach($file_contents as $key_row => $value_row ){
			foreach($value_row as $key => $value){
				$csv_data .= $value. ",";
			}
			$csv_data .= "\n";
		}
		$csv_data = mb_convert_encoding($csv_data, "SJIS-win","UTF-8");
		fwrite($fp, $csv_data);
		fclose($fp);

		$values = array("file_path"  => CATEGORY_GENRE_SETTING_DIR.$filename,
						"file_name"  => $dsp_filename,
						"status_id"  => 1,
						"account_id_list" => serialize($history_account_id_list),
						"element_type_id_list" => serialize(array($this->data["element_type_id"]))
						);
		\Model_Data_CategoryHistory::upd($history_id, $values);

		$message = $client_name["company_name"]." のカテゴリ設定シートダウンロード処理が完了しています。";
		$send_url = '/sem/new/categorygenre/categorysetting/entrance/setting/'.$client_id;
		\Util_Common_Websocket::send_info(Session::get("user_id_sem"), $send_url, $message);

		return new Response();
	}


	//設定シートアップロード
	public function action_upload($client_id){

		$checker = true;
		if(!Input::is_ajax()) {
			$checker = false;
		} elseif (!$this->admin_flg) {
			$checker = Model_Mora_Client::check_client_user($client_id, Session::get('user_id_sem'));
		}
		if (!$checker) {
			return new Response(false, 404);
		}

		$genre_id = Input::post("update_genre_id");
		$file_name = Input::post("upload_file_name");
		$history_id = Input::post("upload_history_id");

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

			if($file_name){

				list($category_data_list, $check_flg) = \Util_CategoryGenre_CategorySetting::check_upload_file($client_id, $genre_id, $file_name);

				//ファイルチェックがエラーの場合
				if (!$check_flg) {
					$error_list = array_merge($error_list, $category_data_list);

				}else{
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

					foreach ($category_data_list as $row_no => &$category_data) {
						$category_big_name = $category_data["category_big_name"] ? $category_data["category_big_name"] : null;
						$category_middle_name = $category_data["category_middle_name"] ? $category_data["category_middle_name"] : null;
						$category_name = $category_data["category_name"] ? $category_data["category_name"] : null;
						$category_type_name = $category_data["category_type_name"] ? $category_data["category_type_name"] : null;
						$before_category_type_name = array_search($category_data["before_category_type_name"], CategoryGenreConst::$category_genre_element_type_list) || $category_data["before_category_type_name"] == "未設定" ? $category_data["before_category_type_name"] : null;

						if(!array_keys(CategoryGenreConst::$category_genre_media_list, $category_data["media_name"])){
							// エラー
							$error_data_list[$row_no][] = "媒体名が不正です[" . $row_no . "行目]";
						}else{
							$media_id = array_keys(CategoryGenreConst::$category_genre_media_list, $category_data["media_name"]);
							$media_id = $media_id[0];
						}
						$account_id = ltrim($category_data["account_id"], "'");
						$campaign_id = isset($category_data["campaign_id"]) ? ltrim($category_data["campaign_id"], "'") : "";
						$ad_group_id = isset($category_data["ad_group_id"]) ? ltrim($category_data["ad_group_id"], "'") : "";
						$keyword_id = isset($category_data["keyword_id"]) ? ltrim($category_data["keyword_id"], "'") : "";

						// 【現】カテゴリ設定単位が存在しない場合はエラー
						if (!$before_category_type_name) {
							$error_data_list[$row_no][] = "【現】カテゴリ設定単位が空白です。現在の設定単位を入力するか、設定シートをダウンロードし直してください[" . $row_no . "行目]";
						}

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
							$error_data_list[$row_no][] = "中カテゴリまたは小カテゴリのみの設定はNGです[" . $row_no . "行目]";
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
								$error_data_list[$row_no][] = "同一の小カテゴリに別の中カテゴリが設定されています[" . $row_no . "行目]";
							}
							$check_category_list[$category_name] = $category_middle_name;

							// 同一の中カテゴリに別の大カテゴリが設定されていたらエラー
							if (isset($check_category_middle_list[$category_middle_name]) && $check_category_middle_list[$category_middle_name] != $category_big_name) {
								$error_data_list[$row_no][] = "同一の中カテゴリに別の大カテゴリが設定されています[" . $row_no . "行目]";
							}
							$check_category_middle_list[$category_middle_name] = $category_big_name;
						}

						// カテゴリ設定単位が記載されていないものはエラー
						if ($set_category_flg && !$category_type_name) {
						 	 // エラー
							$error_data_list[$row_no][] = "カテゴリ設定単位が指定されていません[" . $row_no . "行目]";
						} elseif (!$set_category_flg && $category_type_name) {
							// エラー
							$error_data_list[$row_no][] = "大・中・小カテゴリが空白の場合、設定単位は指定できません[" . $row_no . "行目]";
						}

						// コンポーネントIDの入力チェック
						if (isset($account_id) && !$account_id) {
							$error_data_list[$row_no][] = "アカウントIDを指定してください[" . $row_no . "行目]";
						} elseif (!array_key_exists($account_id, $account_list)) {
							$error_data_list[$row_no][] = "アカウントIDがクライアントに存在しません[" . $row_no . "行目]";
						}
						if ($category_type_name == "キャンペーン" || $category_type_name == "広告グループ" || $category_type_name == "キーワード") {
							if (!$campaign_id) {
								$error_data_list[$row_no][] = "キャンペーンIDを指定してください[" . $row_no . "行目]";
							}
						}
						if ($category_type_name == "広告グループ" || $category_type_name == "キーワード") {
							if (!$ad_group_id) {
								$error_data_list[$row_no][] = "広告グループIDを指定してください[" . $row_no . "行目]";
							}
						}

						if ($category_type_name == "キーワード") {
							if (!$keyword_id) {
								$error_data_list[$row_no][] = "キーワードIDを指定してください[" . $row_no . "行目]";
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
									$error_data_list[$row_no][] = "カテゴリ設定単位が指定が不正です[" . $row_no . "行目]";
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

									$error_data_list[$row_no][] = "同一コンポーネントに別カテゴリが設定されています[" . $row_no . "行目]";
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
							$media_id = isset($media_id[0]) ? $media_id[0] : NULL;
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
											$error_data_list[$row_no][] = "キャンペーン単位のカテゴリ設定に、アカウント単位の設定が存在します[" . $row_no . "行目]";
										}
										break;

									case "広告グループ" :
										if (isset($input_category_list[$account_key]) && $input_category_list[$account_key]) {
											$error_data_list[$row_no][] = "広告グループ単位のカテゴリ設定に、アカウント単位の設定が存在します[" . $row_no . "行目]";
										} elseif (isset($input_category_list[$campaign_key]) && $input_category_list[$campaign_key]) {
											$error_data_list[$row_no][] = "広告グループ単位のカテゴリ設定に、キャンペーン単位の設定が存在します[" . $row_no . "行目]";
										}
										break;

									case "キーワード" :
										if (isset($input_category_list[$account_key]) && $input_category_list[$account_key]) {
											$error_data_list[$row_no][] = "キーワード単位のカテゴリ設定に、アカウント単位の設定が存在します[" . $row_no . "行目]";
										} elseif (isset($input_category_list[$campaign_key]) && $input_category_list[$campaign_key]) {
											$error_data_list[$row_no][] = "キーワード単位のカテゴリ設定に、キャンペーン単位の設定が存在します[" . $row_no . "行目]";
										} elseif (isset($input_category_list[$ad_group_key]) && $input_category_list[$ad_group_key]) {
											$error_data_list[$row_no][] = "キーワード単位のカテゴリ設定に、広告グループ単位の設定が存在します[" . $row_no . "行目]";
										}
										break;

									default :
										$error_data_list[$row_no][] = "カテゴリ設定単位の指定が不正です[" . $row_no . "行目]";
										break;
								}
							}
						}
					}
				}

				//エラーが無い場合は登録
				if(!$error_list && !isset($error_data_list)){
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
						}elseif($category_data["before_category_type_name"] != "未設定"){
							$before_element_type_id = array_search($category_data["before_category_type_name"], CategoryGenreConst::$category_genre_element_type_list);
						}else{
							$before_element_type_id = 0;
						}

						$media_id = array_keys(CategoryGenreConst::$category_genre_media_list, $category_data["media_name"]);
						$media_id = $media_id[0];
						$account_id = ltrim($category_data["account_id"], "'");
						$campaign_id = isset($category_data["campaign_id"]) ? ltrim($category_data["campaign_id"], "'") : "";
						$ad_group_id = isset($category_data["ad_group_id"]) ? ltrim($category_data["ad_group_id"], "'") : "";
						$keyword_id = isset($category_data["keyword_id"]) ? ltrim($category_data["keyword_id"], "'") : "";

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

					if (!isset($update_list) && !isset($delete_list[0])) {
						$error_list[] = array("エラー：更新失敗しました。アップロードファイルの修正後、再度お試しください");
					}else{
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
					}
				}

			}else{
				$error_list[] = array("エラー：アップロードファイルが存在しません");
			}

		} catch (\Exception $e) {
			logger(ERROR, 'category setting error. message:'.$e, 'CategorySetting');
			// 変更履歴更新：エラー時
			$values = array("file_path" => "",
							"status_id" => 3,
							"account_id_list" => serialize($history_account_id_list),
							"element_type_id_list" => serialize($history_element_type_id_list)
							);
		}

		//クライアント名取得
		$client_name = \Model_Mora_Client::get_client_name($client_id);
		if($client_name["client_name"]){
			$client_name["company_name"] = $client_name["company_name"]. "(" . $client_name["client_name"] .")";
		}
		//カテゴリジャンル名取得
		$genre_info = \Model_Data_CategoryGenre::get($genre_id);

		$dsp_filename = "【アップロードエラー】カテゴリ設定【" . $client_name["company_name"] ."】" . $genre_info["category_genre_name"] . "_" . date("YmdHis") . ".csv";

		// 変更履歴更新：正常終了時
		if(!isset($error_list[0]) && !isset($error_data_list)){
			$values = array("file_path"  => "",
							"file_name" => "",
							"status_id"  => 1,
							"account_id_list" => serialize($history_account_id_list),
							"element_type_id_list" => serialize($history_element_type_id_list)
							);
		// 変更履歴更新：入力内容エラー時
		}else{
			//結果ファイル作成
			$feedback_file_name = "feedback_category_setting_" . $client_id ."_" . $genre_id . "_" . date("YmdHis") . ".csv";
			$fb_fp = fopen(CATEGORY_GENRE_SETTING_FEEDBACK_DIR.$feedback_file_name, 'w');

			//アップロードファイルの項目不備などのエラーの場合
			if(isset($error_list[0])){
				$csv_data = "";
				foreach($error_list as $key => $value ){
					for ($i = 0; $i < count($error_list[$key]); $i++) {
						$csv_data .= $value[$i]. ",";
					}
					$csv_data .= "\n";
				}
				$csv_data = mb_convert_encoding($csv_data, "SJIS-win","UTF-8");
				fwrite($fb_fp, $csv_data);

			//アップロードファイルのカテゴリ設定データのエラーの場合
			}elseif(isset($error_data_list)){
				//アップロードファイルの内容を取得
				$file_content = \File::read(CATEGORY_GENRE_SETTING_UPLOAD_DIR. $file_name, true);

				if(isset($file_content)){
					$file_content = mb_convert_encoding($file_content, "UTF-8", "SJIS-win");
					$up_data_list = explode("\n", $file_content);

					$csv_data = "";
					foreach ($up_data_list as $up_key => $up_val){
						//Excel行
						$row = $up_key + 1;

						$up_val = str_replace(array("\r\n","\r","\n"), '', $up_val);
						$csv_data .= $up_val;
						if(isset($error_data_list[$row])){
							$error_text = "";
							$value_list = array();
							foreach ($error_data_list[$row] as $key => $value){
								//重複したエラーメッセージは省く
								if(!isset($value_list[$value])){
									$error_text .= $value . ",";
								}
								$value_list[$value] = $value;
							}
							$csv_data .= $error_text;
						}
						$csv_data .= "\n";
					}

					$csv_data = mb_convert_encoding($csv_data, "SJIS-win","UTF-8");
					fwrite($fb_fp, $csv_data);
				}

				\File::delete(CATEGORY_GENRE_SETTING_UPLOAD_DIR. $file_name);
			}

			fclose($fb_fp);

			$values = array("file_path"  => CATEGORY_GENRE_SETTING_FEEDBACK_DIR.$feedback_file_name,
							"file_name" => $dsp_filename,
							"status_id"  => 2,
							"account_id_list" => serialize($history_account_id_list),
							"element_type_id_list" => serialize($history_element_type_id_list)
							);
		}
		\Model_Data_CategoryHistory::upd($history_id, $values);

		$message = $client_name["company_name"]." のカテゴリ設定アップロード処理が完了しています。";
		$send_url = '/sem/new/categorygenre/categorysetting/entrance/setting/'.$client_id;
		\Util_Common_Websocket::send_info(Session::get("user_id_sem"), $send_url, $message);

		return new Response();
	}
}
