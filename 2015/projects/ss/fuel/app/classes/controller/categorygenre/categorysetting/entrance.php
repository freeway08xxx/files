<?php
require_once APPPATH."/const/categorygenre.php";
/**
 * カテゴリ設定コントローラ
 */
class Controller_CategoryGenre_CategorySetting_Entrance extends Controller_CategoryGenre_Base
{
	// loginユーザ権限チェック用URL
	public $access_url = "/sem/universe_fast/universe.php";

	// 前提共通処理
	public function before() {
		// super
		parent::before();

	}

	// カテゴリジャンル管理TOP
	public function action_index() {
		if ($this->admin_flg) {
			$clients = Model_Mora_Client::get_for_user();
		} else {
			$clients = Model_Mora_Client::get_for_user(Session::get('user_id_sem'));
		}
		$this->view->set('table', '');
		$this->view->set('clients', $clients);
		$this->view->set('client_id', '');
		$this->view->set_filename('categorygenre/categorysetting/index');
	}

	public function action_setting($client_id) {
		Util_Common_Websocket::del_info();
		if ($this->admin_flg) {
			$clients = Model_Mora_Client::get_for_user();
		} else {
			$clients = Model_Mora_Client::get_for_user(Session::get('user_id_sem'));
		}

		$top = Request::forge('categorygenre/categorysetting/entrance/top', false)->execute(array($client_id));
		$this->view->set_safe('top', $top);

		$this->view->set('history', '');
		$this->view->set('table', '');
		$this->view->set('clients', $clients);
		$this->view->set('client_id', $client_id);
		$this->view->set_filename('categorygenre/categorysetting/index');
	}

	public function action_top($client_id) {
		if(Request::is_hmvc()) {

			$data = Model_Data_CategoryGenre::get_for_client_id($client_id);

			$genre_list = array();
			foreach ($data as $key => $value){
				$genre_list[] = array('id'                    => $value['id'],
										'client_id'           => $value['client_id'],
										'category_genre_name' => $value['category_genre_name'],
										'user_name'           => $value['user_name'],
										'datetime'            => $value['datetime']);

			}

			//クライアントのアカウント一覧を取得する。
			$media_id_list = array_keys(CategoryGenreConst::$category_genre_media_list);
			$account_list = Model_Mora_Account::get_by_client($client_id, $media_id_list);

			$this->view->set('table', '');
			$this->view->set('genre_list',$genre_list);
			$this->view->set('account_list',$account_list);
			$this->view->set('client_id', $client_id);
			$this->view->set_filename('categorygenre/categorysetting/top');
			return Response::forge($this->view);
		}
	}

	//作業履歴タブ
	public function action_history($client_id) {

		if ($client_id) {
			$data = Model_Data_CategoryHistory::get($client_id);

			//クライアントのアカウント一覧を取得する。
			$media_id_list = array_keys(CategoryGenreConst::$category_genre_media_list);
			$account_list = Model_Mora_Account::get_by_client($client_id, $media_id_list);

			$history_list = array();
			foreach ($data as $key => $value){
				//出力中の場合は出力時間・完了日時を表示しない
				if($value['status_id'] != 0){
					$created_at = $value['updated_at'];
				}else{
					$created_at = '--';
				}

				//作業対象アカウント
				$dsp_account_id_list  = "";
				$account_id_list = !is_null($value['account_id_list']) ? unserialize($value['account_id_list']) : "";
				if(is_array($account_id_list)){
					foreach ($account_id_list as $account) {
						foreach ($account_list as $acc_key => $acc_value){
							if($acc_value["account_id"] == $account){
								$dsp_account_id_list .= "[".$acc_value["media_name"].":".$acc_value["account_id"]."]".$acc_value["account_name"]."\n";
							}
						}
					}
				}else{
					$dsp_account_id_list = "";
				}

				$dsp_element_type_id_list  = "";
				if($value['action_type_id'] != 6){
					$element_type_id_list = !is_null(unserialize($value['element_type_id_list'])) ? unserialize($value['element_type_id_list']) : "";
					if(is_array($element_type_id_list)){
						foreach ($element_type_id_list as $element) {
							if(isset(CategoryGenreConst::$category_genre_element_type_list[$element])){
								$dsp_element_type_id_list .= CategoryGenreConst::$category_genre_element_type_list[$element]."\n";
							}
						}
					}
				}else{
					$dsp_element_type_id_list = $value["delete_category_name"]."\n(".CategoryGenreConst::$category_elem_name_list[$value["delete_category_element"]].")";
				}

				if($value['delete_category_genre_name']){
					$category_genre_name = $value['delete_category_genre_name']."(削除済)";
				}else{
					if(!isset($genre_info_list[$value['category_genre_id']])){
						$genre_info = Model_Data_CategoryGenre::get($value['category_genre_id']);
						$genre_info_list[$value['category_genre_id']] = $genre_info;
					}
					$category_genre_name = isset($genre_info_list[$value['category_genre_id']]) ? $genre_info_list[$value['category_genre_id']]['category_genre_name'] : NULL;
				}

				$history_list[] = array('id'          => $value['id'],
										'client_id'   => $value['client_id'],
										'category_genre_name' => $category_genre_name,
										'action_type' => CategoryGenreConst::$category_edit_action_list[$value['action_type_id']],
										'file_path'   => $value['file_path'],
										'status_id'   => $value['status_id'],
										'account_id_list' => $dsp_account_id_list,
										'element_type' => $dsp_element_type_id_list,
										'user_name'   => $value['user_name'],
										'created_at'  => $value['created_at'],
										'updated_at'  => $created_at);
			}
		}
		$this->view->set('client_id', $client_id);
		$this->view->set('history_list', $history_list);
		$this->view->set_filename('categorygenre/categorysetting/history');
		return Response::forge($this->view);
	}

	public function action_table($client_id) {
		$checker = true;
		if(!Input::is_ajax()) {
			$checker = false;
		} elseif (!$this->admin_flg) {
			$checker = Model_Mora_Client::check_client_user($client_id, Session::get('user_id_sem'));
		}
		if (!$checker) {
			return new Response(false, 404);
		}

		$genre_id = Input::post("genre_id");
		$category_elem = Input::post("category_elem");
		$no_setting_flg = Input::post("no_setting_flg");
		$element_type_id = Input::post("element_type_id");
		$account_id_list = Input::post("account_id_list");

		$campaign_search_like = Input::post("campaign_search_like");
		$except_campaign_search = Input::post("except_campaign_search");
		$campaign_search_type = Input::post("campaign_search_type");
		$campaign_search_text = Input::post("campaign_search_text");

		$ad_group_search_like = Input::post("ad_group_search_like");
		$except_ad_group_search = Input::post("except_ad_group_search");
		$ad_group_search_type = Input::post("ad_group_search_type");
		$ad_group_search_text = Input::post("ad_group_search_text");

		$keyword_search_like = Input::post("keyword_search_like");
		$except_keyword_search = Input::post("except_keyword_search");
		$keyword_search_type = Input::post("keyword_search_type");
		$keyword_search_text = Input::post("keyword_search_text");

		$url_search_like = Input::post("url_search_like");
		$except_url_search = Input::post("except_url_search");
		$url_search_type = Input::post("url_search_type");
		$url_search_text = Input::post("url_search_text");

		$category_search_like = Input::post("category_search_like");
		$except_category_search = Input::post("except_category_search");
		$category_search_type = Input::post("category_search_type");
		$category_search_text = Input::post("category_search_text");

		//large_companyかどうか
		$company_id = \Model_Mora_Client::get_large_company($client_id);

		//登録済みカテゴリ一覧
		$category_big_list = \Model_Data_Category::get_for_category_genre_id('category_big_id', $client_id, $genre_id);
		$category_middle_list = \Model_Data_Category::get_for_category_genre_id('category_middle_id', $client_id, $genre_id);
		$category_list = \Model_Data_Category::get_for_category_genre_id('category_id', $client_id, $genre_id);

		//カテゴリ名一覧
		$category_big_id_list = array();
		foreach($category_big_list as $category_big) {
			$category_big_id_list[$category_big["id"]] = $category_big["category_name"];
		}
		$category_middle_id_list = array();
		foreach($category_middle_list as $category_middle) {
			$category_middle_id_list[$category_middle["id"]] = $category_middle["category_name"];
		}
		$category_id_list = array();
		foreach($category_list as $category) {
			$category_id_list[$category["id"]] = $category["category_name"];
		}

		//結果リスト
		$view_category_elem_list = array();
		$max_count_flg = false;

		//指定アカウント毎にループ
		foreach ($account_id_list as $account_id) {

			//media_id, account_id 分割
			list($tmp_media_id, $tmp_account_id) = explode("//",  $account_id);

			$history_account_id_list[$tmp_account_id] = $tmp_account_id;

			//指定アカウント情報取得
			$account = \Model_Mora_Account::get_for_account($tmp_account_id);

			//出力コンポーネント単位×element_type_idごとのelement_type_id数
			$element_type_id_list_tmp = \Model_Data_CategoryElement::get_elementtypeid_list($client_id, $genre_id, $tmp_account_id, $element_type_id);
			//出力コンポーネント単位ごとのelement_type_id数
			$get_element_count_list = \Model_Data_CategoryElement::get_element_count_list($client_id, $genre_id, $tmp_account_id, $element_type_id);

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

			//カテゴリ設定情報を取得
			$category_element_list = \Model_Data_CategoryElement::get_category_list($client_id, $genre_id, $tmp_account_id, $element_type_id);

			// アカウント別
			if ($element_type_id == 1) {
				//初期化
				$unique_key =array();
				$target_flg = false;

				$unique_key[1] = $tmp_media_id . ":" .$tmp_account_id;

				//カテゴリ情報を紐付け
				list($element_type_name, $category_big_name, $category_middle_name, $category_name, $before_element_type_id, $category_big_id, $category_middle_id, $category_id)
				= \Util_CategoryGenre_CategorySetting::get_element_info($element_type_id, $unique_key, $element_type_id_list, $category_element_list);

				//カテゴリ名でフィルタリング
				if (!empty($category_search_text)) {
					$ret = \Util_Common_Filter::filterElements(array($category_big_name, $category_middle_name, $category_name),
															   $category_search_text,
															   $category_search_like,
															   $category_search_type,
															   $except_category_search);
					if ($ret) {
						$target_flg = true;
					}
				}else{
					$target_flg = true;
				}

				//未設定のみの場合
				if($no_setting_flg && $element_type_name != ""){
					$target_flg = false;
				}

				if($target_flg){
					$view_category_elem_list[] = array("no" => count($view_category_elem_list)+1,
											"media_id" => $tmp_media_id,
											 "media_name" => CategoryGenreConst::$category_genre_media_list[$tmp_media_id],
											 "account_id" => $tmp_account_id,
											 "account_name" => $account["account_name"],
											 "before_element_type_id" => $before_element_type_id,
											 "before_element_type_name" => $element_type_name,
											 "before_category_big_id" => $category_big_id,
											 "before_category_big_name" => $category_big_name,
											 "before_category_middle_id" => $category_middle_id,
											 "before_category_middle_name" => $category_middle_name,
											 "before_category_id" => $category_id,
											 "before_category_name" => $category_name,
											 "element_type_id" => $element_type_id,
											 "element_type_name" => CategoryGenreConst::$category_genre_element_type_list[$element_type_id]
											 );

					if(count($view_category_elem_list) >= CATEGORY_GENRE_TABLE_VIEW_MAX_COUNT){
						$max_count_flg = true;
						break;
					}
				}

			}else{

				// キャンペーン・広告グループ・キーワードの場合は事前にテーブル名を取得
				$table_name = Util_Common_Table::get_structure_table_name($tmp_media_id, CategoryGenreConst::$category_genre_element_list[$element_type_id], $company_id);

				//テーブルの存在確認
				if (!$table_name) {
					logger(ERROR, "No Table Error:" . $account_id);
					continue;
				}

				// キャンペーン
				if ($element_type_id == 2) {
					//指定アカウントのキャンペーン情報取得
					$campaign_list = \Model_Structure_Campaign::get($table_name, $tmp_media_id, $tmp_account_id);

					foreach ($campaign_list as $campaign) {
						//初期化
						$unique_key =array();
						$target_flg = false;

						//キャンペーン名でフィルタリング
						if (!empty($campaign_search_text)) {
							$ret = \Util_Common_Filter::filterElements(array($campaign["campaign_id"], $campaign["campaign_name"]),
																	   $campaign_search_text,
																	   $campaign_search_like,
																	   $campaign_search_type,
																	   $except_campaign_search);
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
							list($element_type_name, $category_big_name, $category_middle_name, $category_name, $before_element_type_id, $category_big_id, $category_middle_id, $category_id)
							= \Util_CategoryGenre_CategorySetting::get_element_info($element_type_id, $unique_key, $element_type_id_list, $category_element_list);

							//カテゴリ名でフィルタリング
							if (!empty($category_search_text)) {
								$ret = \Util_Common_Filter::filterElements(array($category_big_name, $category_middle_name, $category_name),
																		   $category_search_text,
																		   $category_search_like,
																		   $category_search_type,
																		   $except_category_search);
								if (!$ret) {
									$target_flg = false;
								}
							}
						}

						//未設定のみの場合
						if($no_setting_flg && $element_type_name != ""){
							$target_flg = false;
						}

						if($target_flg){
							$view_category_elem_list[] = array("no" => count($view_category_elem_list)+1,
													 "media_id" => $tmp_media_id,
													 "media_name" => CategoryGenreConst::$category_genre_media_list[$tmp_media_id],
													 "account_id" => $tmp_account_id,
													 "account_name" => $account["account_name"],
													 "campaign_id" => $campaign["campaign_id"],
													 "campaign_name" => $campaign["campaign_name"],
													 "campaign_status" => $campaign_status,
													 "before_element_type_id" => $before_element_type_id,
													 "before_element_type_name" => $element_type_name,
													 "before_category_big_id" => $category_big_id,
													 "before_category_big_name" => $category_big_name,
													 "before_category_middle_id" => $category_middle_id,
													 "before_category_middle_name" => $category_middle_name,
													 "before_category_id" => $category_id,
													 "before_category_name" => $category_name,
													 "element_type_id" => $element_type_id,
													 "element_type_name" => CategoryGenreConst::$category_genre_element_type_list[$element_type_id]
													 );

							if(count($view_category_elem_list) >= CATEGORY_GENRE_TABLE_VIEW_MAX_COUNT){
								$max_count_flg = true;
								break 2;
							}
						}
					}
				// 広告グループ
				}elseif ($element_type_id == 3) {
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
						if (!empty($campaign_search_text)) {
							$ret = \Util_Common_Filter::filterElements(array($ad_group["campaign_id"], $campaign_name),
																	   $campaign_search_text,
																	   $campaign_search_like,
																	   $campaign_search_type,
																	   $except_campaign_search);
							if ($ret) {
								$target_flg = true;
							}
						}else{
							$target_flg = true;
						}

						if($target_flg){
							//広告グループ名でフィルタリング
							if (!empty($ad_group_search_text)) {
								$ret = \Util_Common_Filter::filterElements(array($ad_group["ad_group_id"], $ad_group["ad_group_name"]),
																		   $ad_group_search_text,
																		   $ad_group_search_like,
																		   $ad_group_search_type,
																		   $except_ad_group_search);
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
							list($element_type_name, $category_big_name, $category_middle_name, $category_name, $before_element_type_id, $category_big_id, $category_middle_id, $category_id)
							= \Util_CategoryGenre_CategorySetting::get_element_info($element_type_id, $unique_key, $element_type_id_list, $category_element_list);

							//カテゴリ名でフィルタリング
							if (!empty($category_search_text)) {
								$ret = \Util_Common_Filter::filterElements(array($category_big_name, $category_middle_name, $category_name),
																		   $category_search_text,
																		   $category_search_like,
																		   $category_search_type,
																		   $except_category_search);
								if (!$ret) {
									$target_flg = false;
								}
							}

						}

						//未設定のみの場合
						if($no_setting_flg && $element_type_name != ""){
							$target_flg = false;
						}

						if($target_flg){
							$view_category_elem_list[] = array("no" => count($view_category_elem_list)+1,
													 "media_id" => $tmp_media_id,
													 "media_name" => CategoryGenreConst::$category_genre_media_list[$tmp_media_id],
													 "account_id" => $tmp_account_id,
													 "account_name" => $account["account_name"],
													 "campaign_id" => $ad_group["campaign_id"],
													 "campaign_name" => $campaign_name,
													 "campaign_status" => $camapign_status,
													 "ad_group_id" => $ad_group["ad_group_id"],
													 "ad_group_name" => $ad_group["ad_group_name"],
													 "before_element_type_id" => $before_element_type_id,
													 "before_element_type_name" => $element_type_name,
													 "before_category_big_id" => $category_big_id,
													 "before_category_big_name" => $category_big_name,
													 "before_category_middle_id" => $category_middle_id,
													 "before_category_middle_name" => $category_middle_name,
													 "before_category_id" => $category_id,
													 "before_category_name" => $category_name,
													 "element_type_id" => $element_type_id,
													 "element_type_name" => CategoryGenreConst::$category_genre_element_type_list[$element_type_id]
													 );

							if(count($view_category_elem_list) >= CATEGORY_GENRE_TABLE_VIEW_MAX_COUNT){
								$max_count_flg = true;
								break 2;
							}
						}
					}
				// キーワード
				}elseif ($element_type_id == 4) {
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
						if (!empty($campaign_search_text)) {
							$ret = \Util_Common_Filter::filterElements(array($campaign_id, $campaign_name),
																	   $campaign_search_text,
																	   $campaign_search_like,
																	   $campaign_search_type,
																	   $except_campaign_search);
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
							if (!empty($ad_group_search_text)) {
								$ret = \Util_Common_Filter::filterElements(array($keyword['ad_group_id'], $ad_group_name),
																		   $ad_group_search_text,
																		   $ad_group_search_like,
																		   $ad_group_search_type,
																		   $except_ad_group_search);
								if (!$ret) {
									$target_flg = false;
								}
							}
						}

						if($target_flg){
							//キーワードでフィルタリング
							if (!empty($keyword_search_text)) {
								$ret = \Util_Common_Filter::filterElements(array($keyword["keyword_id"], $keyword["keyword"]),
																		   $keyword_search_text,
																		   $keyword_search_like,
																		   $keyword_search_type,
																		   $except_keyword_search);
								if (!$ret) {
									$target_flg = false;
								}
							}
						}

						if($target_flg){
							//リンク先URLでフィルタリング
							if (!empty($url_search_text)) {
								$ret = \Util_Common_Filter::filterElements(array($keyword["link_url"], $keyword["link_url"]),
																		   $url_search_text,
																		   $url_search_like,
																		   $url_search_type,
																		   $except_url_search);
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
							list($element_type_name, $category_big_name, $category_middle_name, $category_name, $before_element_type_id, $category_big_id, $category_middle_id, $category_id)
							= \Util_CategoryGenre_CategorySetting::get_element_info($element_type_id, $unique_key, $element_type_id_list, $category_element_list);

							//カテゴリ名でフィルタリング
							if (!empty($category_search_text)) {
								$ret = \Util_Common_Filter::filterElements(array($category_big_name, $category_middle_name, $category_name),
																		   $category_search_text,
																		   $category_search_like,
																		   $category_search_type,
																		   $except_category_search);
								if (!$ret) {
									$target_flg = false;
								}
							}
						}

						//未設定のみの場合
						if($no_setting_flg && $element_type_name != ""){
							$target_flg = false;
						}

						if($target_flg){
							$view_category_elem_list[] = array("no" => count($view_category_elem_list)+1,
													 "media_id" => $tmp_media_id,
													 "media_name" => CategoryGenreConst::$category_genre_media_list[$tmp_media_id],
													 "account_id" => $tmp_account_id,
													 "account_name" => $account["account_name"],
													 "campaign_id" => $campaign_id,
													 "campaign_name" => $campaign_name,
													 "campaign_status" => $camapign_status,
													 "ad_group_id" => $keyword["ad_group_id"],
													 "ad_group_name" => $ad_group_name,
													 "keyword_id" => $keyword["keyword_id"],
													 "keyword" => $keyword["keyword"],
													 "link_url" => $keyword["link_url"],
													 "before_element_type_id" => $before_element_type_id,
													 "before_element_type_name" => $element_type_name,
													 "before_category_big_id" => $category_big_id,
													 "before_category_big_name" => $category_big_name,
													 "before_category_middle_id" => $category_middle_id,
													 "before_category_middle_name" => $category_middle_name,
													 "before_category_id" => $category_id,
													 "before_category_name" => $category_name,
													 "element_type_id" => $element_type_id,
													 "element_type_name" => CategoryGenreConst::$category_genre_element_type_list[$element_type_id]
													 );

							if(count($view_category_elem_list) >= CATEGORY_GENRE_TABLE_VIEW_MAX_COUNT){
								$max_count_flg = true;
								break 2;
							}
						}
					}
				}
			}
		}

		//カテゴリジャンル名取得
		$genre_info = Model_Data_CategoryGenre::get($genre_id);

		$this->view->set('view_category_elem_list', $view_category_elem_list);
		$this->view->set('max_count_flg', $max_count_flg);
		$this->view->set('category_big_id_list', $category_big_id_list);
		$this->view->set('category_middle_id_list', $category_middle_id_list);
		$this->view->set('category_id_list', $category_id_list);
		$this->view->set('genre_info', $genre_info);
		$this->view->set('element_type_id', $element_type_id);
		$this->view->set('client_id', $client_id);
		$this->view->set_filename('categorygenre/categorysetting/table');
		return Response::forge($this->view);
	}

	// ファイルダウンロード
	public function action_download($history_id) {
		$export_info = Model_Data_CategoryHistory::get_export_info($history_id);
		if ($export_info) {
			$export_file_path = $export_info['file_path'];
			$output_file_name = ($export_info['file_name']) ? $export_info['file_name'] : NULL;
			File::download($export_file_path, $output_file_name);
		} else {
			return new Response(false, 404);
		}
	}

}
