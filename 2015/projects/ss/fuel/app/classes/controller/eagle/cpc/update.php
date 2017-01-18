<?php

require_once APPPATH . "/const/main.php";
require_once APPPATH . "/const/eagle/cpc.php";

class Controller_Eagle_Cpc_Update extends Controller_Eagle_Cpc_Base {

	// ログインユーザ権限チェック用URL
	public $access_url = "/sem/new/eagle/cpc/update";

	/**
	 * メイン処理の前処理を実行する
	 *
	 * @param なし
	 * @return なし
	 */
	public function before() {

		parent::before();

		// 入力パラメータを取得
		$this->client_id = Input::get("client_id") ? Input::get("client_id") : Input::post("client_id");
		$this->account_id_list = Input::post("account_id_list");
		$this->account_search_text = Input::post("account_search_text");
		$this->account_search_type = Input::post("account_search_type");
		$this->account_search_id_only = Input::post("account_search_id_only");
		$this->new_campaign_not_disp = Input::post("new_campaign_not_disp");
		$this->campaign_id_list = Input::post("campaign_id_list");
		$this->campaign_search_text = Input::post("campaign_search_text");
		$this->campaign_search_type = Input::post("campaign_search_type");
		$this->campaign_search_id_only = Input::post("campaign_search_id_only");
		$this->get_structure_not_disp = Input::post("get_structure_not_disp");
		$this->account_filter_text = Input::post("account_filter_text");
		$this->account_filter_type = Input::post("account_filter_type");
		$this->account_filter_id_only = Input::post("account_filter_id_only");
		$this->campaign_filter_text = Input::post("campaign_filter_text");
		$this->campaign_filter_type = Input::post("campaign_filter_type");
		$this->campaign_filter_id_only = Input::post("campaign_filter_id_only");
		$this->adgroup_filter_text = Input::post("adgroup_filter_text");
		$this->adgroup_filter_type = Input::post("adgroup_filter_type");
		$this->adgroup_filter_id_only = Input::post("adgroup_filter_id_only");
		$this->matchtype_filter = Input::post("matchtype_filter");
		$this->structure_list = Input::post("structure_list");
		$this->cpc_evenness_change_device = Input::post("cpc_evenness_change_device");
		$this->cpc_evenness_change_method = Input::post("cpc_evenness_change_method");
		$this->cpc_evenness_change_value = Input::post("cpc_evenness_change_value");
		$this->cpc_evenness_change_type = Input::post("cpc_evenness_change_type");
		$this->action_type = Input::post("action_type");

		if (Input::post("scroll_x")) {

			$this->scroll_x = Input::post("scroll_x");

		} else {

			$this->scroll_x = 0;
		}

		if (Input::post("scroll_y")) {

			$this->scroll_y = Input::post("scroll_y");

		} else {

			$this->scroll_y = 0;
		}
	}

	/**
	 * メイン処理を実行する
	 *
	 * @param なし
	 * @return なし
	 */
	public function action_index() {

		// 担当クライアント一覧を取得
		$all_client_list = array();
		$client_list = Model_Mora_Client::get_for_user();

		foreach ($client_list as $client) {

			// クライアント名（表示用）
			if ($client["client_name"]) {

				$client["client_name_disp"] = $client["company_name"]
											. "//"
											. $client["client_name"];

			} else {

				$client["client_name_disp"] = $client["company_name"];
			}

			$all_client_list[] = $client;
		}

		// クライアントのアカウント一覧を取得
		$all_account_list = array();

		if ($this->client_id) {

			$account_list = Model_Mora_Account::get_by_client($this->client_id);

			foreach ($account_list as $account) {

				// アカウントPK
				$account["account_pk"] = $account["media_id"]
									   . "//"
									   . $account["account_id"];

				// アカウント名（表示用）
				$account["account_name_disp"] = "["
											  . $account["account_id"]
											  . "] "
											  . $account["account_name"];

				$all_account_list[] = $account;
			}
		}

		// アカウント一覧を絞込み
		if ($this->account_search_id_only) {

			$all_account_list = Util_Common_Filter::search_filter("account_id",
																  $this->account_search_text,
																  $this->account_search_type,
																  $all_account_list,
																  isset($this->account_id_list) ? $this->account_id_list : array(),
																  "account_pk");

		} else {

			$all_account_list = Util_Common_Filter::search_filter("account_name_disp",
																  $this->account_search_text,
																  $this->account_search_type,
																  $all_account_list,
																  isset($this->account_id_list) ? $this->account_id_list : array(),
																  "account_pk");
		}

		// アカウントのキャンペーン一覧を取得
		$all_campaign_list = array();

		if ($this->account_id_list) {

			foreach ($this->account_id_list as $account_pk) {

				list($media_id, $account_id) = explode("//", $account_pk);

				// キャンペーン情報を取得
				$campaign_list = Model_Data_EagleCampaign::get($media_id, $account_id);

				foreach ($campaign_list as $campaign) {

					// キャンペーンPK
					$campaign["campaign_pk"] = $campaign["media_id"]
											 . "//"
											 . $campaign["account_id"]
											 . "//"
											 . $campaign["campaign_id"];

					// キャンペーン名（表示用）
					$campaign["campaign_name_disp"] = "["
													. $campaign["account_id"]
													. "]["
													. $campaign["campaign_id"]
													. "] "
													. $campaign["campaign_name"];

					$all_campaign_list[] = $campaign;
				}
			}
		}

		// キャンペーン一覧を絞込み
		if ($this->campaign_search_id_only) {

			$all_campaign_list = Util_Common_Filter::search_filter("campaign_id",
																   $this->campaign_search_text,
																   $this->campaign_search_type,
																   $all_campaign_list,
																   isset($this->campaign_id_list) ? $this->campaign_id_list : array(),
																   "campaign_pk");

		} else {

			$all_campaign_list = Util_Common_Filter::search_filter("campaign_name_disp",
																   $this->campaign_search_text,
																   $this->campaign_search_type,
																   $all_campaign_list,
																   isset($this->campaign_id_list) ? $this->campaign_id_list : array(),
																   "campaign_pk");
		}

		// キャンペーン一覧非表示の場合
		if ($this->action_type !== "new_campaign" && isset($this->new_campaign_not_disp)) {

			$this->campaign_id_list = array();

			foreach ($all_campaign_list as $campaign_list) {

				$this->campaign_id_list[] = $campaign_list["campaign_pk"];
			}
		}

		// アカウントの掲載一覧を取得
		$all_structure_list = array();

		if (!isset($this->get_structure_not_disp) && $this->campaign_id_list) {

			// 選択アカウントの掲載取得が１時間以内に完了したか確認
			$get_structure_flg = true;

			foreach ($this->campaign_id_list as $campaign_pk) {

				list($media_id, $account_id, $campaign_id) = explode("//", $campaign_pk);

				$chk_structure_count = Model_Data_EagleTargetAccount::chk_structure(null, $account_id, $media_id);

				if ($chk_structure_count[0]["count"] === "0") {

					$get_structure_flg = false;
				}
			}

			// 選択アカウントの掲載取得が１時間以内に完了している場合のみ実行
			if ($get_structure_flg) {

				foreach ($this->campaign_id_list as $campaign_pk) {

					list($media_id, $account_id, $campaign_id) = explode("//", $campaign_pk);

					// 掲載情報を取得
					$structure_list = Model_Data_EagleAdGroup::get($media_id, $account_id, $campaign_id);

					// アカウント名を取得
					$account_name = "";

					foreach ($all_account_list as $account) {

						if ($account_id === $account["account_id"]) {

							$account_name = $account["account_name"];
							break;
						}
					}

					// キャンペーン名を取得
					$campaign_name = "";

					foreach ($all_campaign_list as $campaign) {

						if ($account_id === $campaign["account_id"]
								&& $campaign_id === $campaign["campaign_id"]) {

							$campaign_name = $campaign["campaign_name"];
							break;
						}
					}

					foreach ($structure_list as $structure) {

						// アカウント名
						$structure["account_name"] = $account_name;

						// アカウント名（表示用）
						$structure["account_name_disp"] = "["
														. $structure["account_id"]
														. "] "
														. $account_name;

						// キャンペーン名
						$structure["campaign_name"] = $campaign_name;

						// キャンペーン名（表示用）
						$structure["campaign_name_disp"] = "["
														 . $structure["campaign_id"]
														 . "] "
														 . $campaign_name;

						// 広告グループ名（表示用）
						$structure["adgroup_name_disp"] = "["
														 . $structure["adgroup_id"]
														 . "] "
														 . $structure["adgroup_name"];

						// マッチタイプ
						if ($structure["adgroup_match_type"]) {

							$structure["adgroup_match_type_disp"] = $GLOBALS["matchtype_replace_list"][$structure["adgroup_match_type"]];

						} else {

							$structure["adgroup_match_type_disp"] = "";
						}

						if (!is_null($structure["adgroup_bid_modifier"]) && $structure["adgroup_bid_modifier"] !== "") {

							// 【SP】設定CPC
							$structure["adgroup_cpc_max_sp"] = round($structure["adgroup_cpc_max"] * $structure["adgroup_bid_modifier"]);

							// 設定MBA
							$structure["adgroup_bid_modifier_disp"] = ($structure["adgroup_bid_modifier"] - 1) * 100;

						} else {

							// 【SP】設定CPC
							$structure["adgroup_cpc_max_sp"] = $structure["adgroup_cpc_max"];

							// 設定MBA
							$structure["adgroup_bid_modifier_disp"] = null;
						}

						$all_structure_list[] = $structure;
					}
				}
			}
		}

		// 掲載一覧の絞込み
		if ($this->action_type === "structure_filter") {

			// アカウント名絞込み
			if ($this->account_filter_id_only) {

				$all_structure_list = Util_Common_Filter::search_filter("account_id", $this->account_filter_text, $this->account_filter_type, $all_structure_list);

			} else {

				$all_structure_list = Util_Common_Filter::search_filter("account_name_disp", $this->account_filter_text, $this->account_filter_type, $all_structure_list);
			}

			// キャンペーン名絞込み
			if ($this->campaign_filter_id_only) {

				$all_structure_list = Util_Common_Filter::search_filter("campaign_id", $this->campaign_filter_text, $this->campaign_filter_type, $all_structure_list);

			} else {

				$all_structure_list = Util_Common_Filter::search_filter("campaign_name_disp", $this->campaign_filter_text, $this->campaign_filter_type, $all_structure_list);
			}

			// 広告グループ名絞込み
			if ($this->adgroup_filter_id_only) {

				$all_structure_list = Util_Common_Filter::search_filter("adgroup_id", $this->adgroup_filter_text, $this->adgroup_filter_type, $all_structure_list);

			} else {

				$all_structure_list = Util_Common_Filter::search_filter("adgroup_name_disp", $this->adgroup_filter_text, $this->adgroup_filter_type, $all_structure_list);
			}

			// マッチタイプ絞込み
			if ($this->matchtype_filter) {

				foreach ($all_structure_list as $index => $structure) {

					if ($structure["adgroup_match_type_disp"] !== $this->matchtype_filter) {
						unset($all_structure_list[$index]);
					}
				}
			}
		}

		// 掲載一覧のインデックス振り直し
		$all_structure_list = array_merge($all_structure_list);

		// キャンペーン一覧非表示の場合
		if (isset($this->new_campaign_not_disp)) {

			$all_campaign_list = array();
		}

		// Viewを実行
		$this->view->set("client_id", $this->client_id);
		$this->view->set("client_list", $all_client_list);
		$this->view->set("account_id_list", $this->account_id_list);
		$this->view->set("account_list", $all_account_list);
		$this->view->set("account_search_text", $this->account_search_text);
		$this->view->set("account_search_type", $this->account_search_type);
		$this->view->set("account_search_id_only", $this->account_search_id_only);
		$this->view->set("new_campaign_not_disp", $this->new_campaign_not_disp);
		$this->view->set("campaign_id_list", $this->campaign_id_list);
		$this->view->set("campaign_list", $all_campaign_list);
		$this->view->set("campaign_search_text", $this->campaign_search_text);
		$this->view->set("campaign_search_type", $this->campaign_search_type);
		$this->view->set("campaign_search_id_only", $this->campaign_search_id_only);
		$this->view->set("get_structure_not_disp", $this->get_structure_not_disp);
		$this->view->set("account_filter_text", $this->account_filter_text);
		$this->view->set("account_filter_type", $this->account_filter_type);
		$this->view->set("account_filter_id_only", $this->account_filter_id_only);
		$this->view->set("campaign_filter_text", $this->campaign_filter_text);
		$this->view->set("campaign_filter_type", $this->campaign_filter_type);
		$this->view->set("campaign_filter_id_only", $this->campaign_filter_id_only);
		$this->view->set("adgroup_filter_text", $this->adgroup_filter_text);
		$this->view->set("adgroup_filter_type", $this->adgroup_filter_type);
		$this->view->set("adgroup_filter_id_only", $this->adgroup_filter_id_only);
		$this->view->set("matchtype_filter", $this->matchtype_filter);
		$this->view->set("structure_list", $all_structure_list);
		$this->view->set("scroll_x", $this->scroll_x);
		$this->view->set("scroll_y", $this->scroll_y);

		$this->view->set_filename("eagle/cpc/index");
	}

	/**
	 * API経由でキャンペーン情報を取得する
	 *
	 * @param なし
	 * @return なし
	 */
	public function action_new_campaign() {

		// アカウントのキャンペーン情報を取得
		if ($this->account_id_list) {

			foreach ($this->account_id_list as $account_pk) {

				list($media_id, $account_id) = explode("//", $account_pk);

				// Yahoo!のキャンペーン情報を取得
				if (intval($media_id) === MEDIA_ID_YAHOO) {

					$result = Util_Eagle_YahooApi::get_campaign($account_id);

				// Google/GDNのキャンペーン情報を取得
				} elseif (intval($media_id) === MEDIA_ID_GOOGLE) {

					$result = Util_Eagle_AdwordsApi::get_campaign($account_id);

				// YDNのキャンペーン情報を取得
				} elseif (intval($media_id) === MEDIA_ID_IM) {

					$result = Util_Eagle_YdnApi::get_campaign($account_id);
				}
			}
		}

		// メイン処理を実行
		$this->action_index();
	}

	/**
	 * キャンペーン情報をダウンロードする
	 *
	 * @param なし
	 * @return なし
	 */
	public function action_campaign_dl() {

		// アカウントのキャンペーン情報を取得
		if ($this->account_id_list) {

			$campaign_dl_list = array();

			foreach ($this->account_id_list as $account_pk) {

				list($media_id, $account_id) = explode("//", $account_pk);

				// キャンペーン情報を取得
				$campaign_list = Model_Data_EagleCampaign::get($media_id, $account_id);

				foreach ($campaign_list as $campaign) {

					$obj_campaign_list = array();

					$obj_campaign_list["campaign_name_disp"] = "["
															 . $campaign["account_id"]
															 . "]["
															 . $campaign["campaign_id"]
															 . "] "
															 . $campaign["campaign_name"];

					$campaign_dl_list[] = $obj_campaign_list;
				}

				// キャンペーン一覧を絞込み
				if ($this->campaign_search_id_only) {

					$campaign_dl_list = Util_Common_Filter::search_filter("campaign_id", $this->campaign_search_text, $this->campaign_search_type, $campaign_dl_list);

				} else {

					$campaign_dl_list = Util_Common_Filter::search_filter("campaign_name_disp", $this->campaign_search_text, $this->campaign_search_type, $campaign_dl_list);
				}
			}

			if (empty($campaign_dl_list)) {
				$campaign_dl_list[] = array(ALERT_MSG_008);
			}

			// キャンペーン情報をダウンロード
			$this->format = "csv";
			$file_name = CAMPAIGN_DL_FILE_NAME . date("YmdHis") . ".csv";
			$response = $this->response($campaign_dl_list);
			$response->set_header("Content-Disposition", "attachment; filename=$file_name");

			return $response;
		}
	}

	/**
	 * API経由で掲載情報を取得する
	 *
	 * @param なし
	 * @return なし
	 */
	public function action_get_structure() {

		// キャンペーン一覧非表示の場合
		if (isset($this->new_campaign_not_disp)) {

			$all_campaign_list = array();

			if ($this->account_id_list) {

				foreach ($this->account_id_list as $account_pk) {

					list($media_id, $account_id) = explode("//", $account_pk);

					// キャンペーン情報を取得
					$campaign_list = Model_Data_EagleCampaign::get($media_id, $account_id);

					foreach ($campaign_list as $campaign) {

						// キャンペーンPK
						$campaign["campaign_pk"] = $campaign["media_id"]
												 . "//"
												 . $campaign["account_id"]
												 . "//"
												 . $campaign["campaign_id"];

						// キャンペーン名（表示用）
						$campaign["campaign_name_disp"] = "["
														. $campaign["account_id"]
														. "]["
														. $campaign["campaign_id"]
														. "] "
														. $campaign["campaign_name"];

						$all_campaign_list[] = $campaign;
					}
				}

				unset($campaign_list);
			}

			// キャンペーン一覧を絞込み
			if ($this->campaign_search_id_only) {

				$all_campaign_list = Util_Common_Filter::search_filter("campaign_id", $this->campaign_search_text, $this->campaign_search_type, $all_campaign_list);

			} else {

				$all_campaign_list = Util_Common_Filter::search_filter("campaign_name_disp", $this->campaign_search_text, $this->campaign_search_type, $all_campaign_list);
			}

			$this->campaign_id_list = array();

			foreach ($all_campaign_list as $campaign) {

				$this->campaign_id_list[] = $campaign["campaign_pk"];
			}

			unset($all_campaign_list);
		}

		// 選択アカウントの掲載情報を取得
		if ($this->campaign_id_list) {

			// 処理IDを登録
			$setting_columns = array("exec_code");
			$setting_values[] = array($setting_columns[0] => "0001");

			$setting_result = Model_Data_EagleSetting::ins($setting_columns, $setting_values);

			// 掲載取得バッチに必要な情報を登録
			$target_account_columns = array("id", "media_id", "account_id");
			$target_account_values = array();
			$google_account_list = array();
			$account_id_list = array();

			foreach ($this->campaign_id_list as $campaign_pk) {

				list($media_id, $account_id, $campaign_id) = explode("//", $campaign_pk);
				$account_pk = $media_id . "//" . $account_id;

				$account_id_list[$account_pk]["media_id"] = $media_id;
				$account_id_list[$account_pk]["account_id"] = $account_id;
			}

			foreach ($account_id_list as $account_info) {

				$value = array();
				$value[$target_account_columns[0]] = $setting_result[0];
				$value[$target_account_columns[1]] = $account_info["media_id"];
				$value[$target_account_columns[2]] = $account_info["account_id"];

				$target_account_values[] = $value;

				// 掲載取得バッチ用にGoogleのアカウントを設定
				if (intval($account_info["media_id"]) === MEDIA_ID_GOOGLE || intval($account_info["media_id"]) === MEDIA_ID_GOOGLE_CONSULTING) {

					$google_account_list[] = $account_info["account_id"];
				}
			}

			Model_Data_EagleTargetAccount::ins($target_account_columns, $target_account_values);

			// Googleのアカウントを分割
			$div_google_account_list = array();

			if ($google_account_list) {

				$div_google_account_list = array_chunk($google_account_list, 3);
			}

			// 画面の設定値を登録
			$cpc_setting_columns = array("id", "setting_value");

			$cpc_setting_list["client_id"] = $this->client_id;
			$cpc_setting_list["account_id_list"] = $this->account_id_list;
			$cpc_setting_list["campaign_id_list"] = $this->campaign_id_list;
			$cpc_setting_list["account_filter_text"] = $this->account_filter_text;
			$cpc_setting_list["account_filter_type"] = $this->account_filter_type;
			$cpc_setting_list["campaign_filter_text"] = $this->campaign_filter_text;
			$cpc_setting_list["campaign_filter_type"] = $this->campaign_filter_type;
			$cpc_setting_list["adgroup_filter_text"] = $this->adgroup_filter_text;
			$cpc_setting_list["adgroup_filter_type"] = $this->adgroup_filter_type;
			$cpc_setting_list["matchtype_filter"] = $this->matchtype_filter;

			// キャンペーン一覧非表示の場合
			if (isset($this->new_campaign_not_disp)) {

				$cpc_setting_list["new_campaign_not_disp"] = $this->new_campaign_not_disp;
				$cpc_setting_list["campaign_search_text"] = $this->campaign_search_text;
				$cpc_setting_list["campaign_search_type"] = $this->campaign_search_type;
				$cpc_setting_list["campaign_search_id_only"] = $this->campaign_search_id_only;
			}

			// キャンペーン一覧非表示の場合
			if (isset($this->get_structure_not_disp)) {

				$cpc_setting_list["get_structure_not_disp"] = $this->get_structure_not_disp;
				$cpc_setting_list["account_filter_text"] = $this->account_filter_text;
				$cpc_setting_list["account_filter_type"] = $this->account_filter_type;
				$cpc_setting_list["account_filter_id_only"] = $this->account_filter_id_only;
				$cpc_setting_list["campaign_filter_text"] = $this->campaign_filter_text;
				$cpc_setting_list["campaign_filter_type"] = $this->campaign_filter_type;
				$cpc_setting_list["campaign_filter_id_only"] = $this->campaign_filter_id_only;
				$cpc_setting_list["adgroup_filter_text"] = $this->adgroup_filter_text;
				$cpc_setting_list["adgroup_filter_type"] = $this->adgroup_filter_type;
				$cpc_setting_list["adgroup_filter_id_only"] = $this->adgroup_filter_id_only;
				$cpc_setting_list["matchtype_filter"] = $this->matchtype_filter;
			}

			$cpc_setting_value = serialize($cpc_setting_list);
			$cpc_setting_value = base64_encode($cpc_setting_value);

			$cpc_setting_values[] = array($cpc_setting_columns[0] => $setting_result[0],
										  $cpc_setting_columns[1] => $cpc_setting_value);

			Model_Data_EagleCpcSetting::ins($cpc_setting_columns, $cpc_setting_values);

			// 掲載取得バッチを実行
			foreach (array("google","yahoo") as $product) {

				if ($product === "google") {

					foreach ($div_google_account_list as $div_google_account) {

						$account_ids = implode(",", $div_google_account);

						$curl = Request::forge("http://" . JENKINS_HOST . "/job/" . urlencode(JENKINS_STRUCTURE_G_JOB) . "/buildWithParameters?token=eagle&id=" . $setting_result[0] . "&account_ids=" . $account_ids . "&client_id=" . $this->client_id . "&user_id_sem=" . \Session::get("user_id_sem"), "curl");
						$curl->set_option(CURLOPT_USERPWD, JENKINS_USERPWD);
						$curl->execute();
					}

				} else {

					$curl = Request::forge("http://" . JENKINS_HOST . "/job/" . urlencode(JENKINS_STRUCTURE_Y_JOB) . "/buildWithParameters?token=eagle&id=" . $setting_result[0] . "&client_id=" . $this->client_id . "&user_id_sem=" . \Session::get("user_id_sem"), "curl");
					$curl->set_option(CURLOPT_USERPWD, JENKINS_USERPWD);
					$curl->execute();
				}
			}

			$this->alert_message = ALERT_MSG_001;
		}

		// メイン処理を実行
		$this->action_index();
	}

	/**
	 * 掲載情報をダウンロードする
	 *
	 * @param なし
	 * @return なし
	 */
	public function action_structure_dl() {

		if ($this->account_id_list) {

			$obj_account_list = array();
			$obj_campaign_list = array();

			foreach ($this->account_id_list as $account_pk) {

				list($media_id, $account_id) = explode("//", $account_pk);

				// アカウント情報を取得
				$account_list = Model_Mora_Account::get_account($account_id);

				// アカウント名
				$obj_account_list[$account_pk]["account_name"] = $account_list[0]["account_name"];

				// アカウント名（表示用）
				$obj_account_list[$account_pk]["account_name_disp"] = "["
																	. $account_list[0]["id"]
																	. "] "
																	. $account_list[0]["account_name"];

				// キャンペーン情報を取得
				$campaign_list = Model_Data_EagleCampaign::get($media_id, $account_id);

				foreach ($campaign_list as $campaign) {

					// キャンペーンPK
					$campaign_pk = $campaign["media_id"]
								 . "//"
								 . $campaign["account_id"]
								 . "//"
								 . $campaign["campaign_id"];

					// キャンペーン名（表示用）
					$campaign["campaign_name_disp"] = "["
													. $campaign["account_id"]
													. "]["
													. $campaign["campaign_id"]
													. "] "
													. $campaign["campaign_name"];

					$obj_campaign_list[$campaign_pk] = $campaign;
				}
			}

			// キャンペーン一覧非表示の場合
			if (isset($this->new_campaign_not_disp)) {

				// キャンペーン一覧を絞込み
				if (isset($this->campaign_search_id_only)) {

					$obj_campaign_list = Util_Common_Filter::search_filter("campaign_id", $this->campaign_search_text, $this->campaign_search_type, $obj_campaign_list);

				} else {

					$obj_campaign_list = Util_Common_Filter::search_filter("campaign_name_disp", $this->campaign_search_text, $this->campaign_search_type, $obj_campaign_list);
				}

				$this->campaign_id_list = array();

				foreach ($obj_campaign_list as $campaign_pk => $campaign_list) {

					$this->campaign_id_list[] = $campaign_pk;
				}
			}

			if ($this->campaign_id_list) {

				// 選択アカウントの掲載取得が１時間以内に完了したか確認
				$get_structure_flg = true;

				foreach ($this->campaign_id_list as $campaign_pk) {

					list($media_id, $account_id, $campaign_id) = explode("//", $campaign_pk);

					$chk_structure_count = Model_Data_EagleTargetAccount::chk_structure(null, $account_id, $media_id);

					if ($chk_structure_count[0]["count"] === "0") {

						$get_structure_flg = false;
					}
				}

				$structure_dl_list[] = array("媒体",
											 "アカウントID",
											 "アカウント名",
											 "キャンペーンID",
											 "キャンペーン名",
											 "広告グループID",
											 "広告グループ名",
											 "【PC】設定CPC",
											 "【SP】設定CPC",
											 "設定MBA");

				if ($get_structure_flg) {

					$all_structure_list = array();

					foreach ($this->campaign_id_list as $campaign_pk) {

						list($media_id, $account_id, $campaign_id) = explode("//", $campaign_pk);

						// 広告グループ情報を取得
						$adgroup_list = Model_Data_EagleAdGroup::get($media_id, $account_id, $campaign_id);

						foreach ($adgroup_list as $adgroup) {

							$structure_list = array();

							// 媒体
							$structure_list["media"] = $GLOBALS["media_name_list"][$media_id];

							// アカウントID
							$structure_list["account_id"] = $account_id;

							// アカウント名
							$account_pk = $media_id . "//" . $account_id;
							$structure_list["account_name"] = $obj_account_list[$account_pk]["account_name"];

							// アカウント名（表示用）
							$structure_list["account_name_disp"] = $obj_account_list[$account_pk]["account_name_disp"];

							// キャンペーンID
							$structure_list["campaign_id"] = $campaign_id;

							// キャンペーン名
							$structure_list["campaign_name"] = $obj_campaign_list[$campaign_pk]["campaign_name"];

							// キャンペーン名（表示用）
							$structure_list["campaign_name_disp"] = $obj_campaign_list[$campaign_pk]["campaign_name_disp"];

							// 広告グループID
							$structure_list["adgroup_id"] = $adgroup["adgroup_id"];

							// 広告グループ名
							$structure_list["adgroup_name"] = $adgroup["adgroup_name"];

							// 広告グループ名（表示用）
							$structure_list["adgroup_name_disp"] = "["
																 . $adgroup["adgroup_id"]
																 . "] "
																 . $adgroup["adgroup_name"];

							// 【PC】設定CPC
							$structure_list["adgroup_cpc_max_pc"] = $adgroup["adgroup_cpc_max"];

							if (!is_null($adgroup["adgroup_bid_modifier"]) && $adgroup["adgroup_bid_modifier"] !== "") {

								// 【SP】設定CPC
								$structure_list["adgroup_cpc_max_sp"] = round($adgroup["adgroup_cpc_max"] * $adgroup["adgroup_bid_modifier"]);

								// 設定MBA
								$structure_list["adgroup_bid_modifier"] = ($adgroup["adgroup_bid_modifier"] - 1) * 100 . "%";

							} else {

								// 【SP】設定CPC
								$structure_list["adgroup_cpc_max_sp"] = $adgroup["adgroup_cpc_max"];

								// 設定MBA
								$structure_list["adgroup_bid_modifier"] = null;
							}

							$all_structure_list[] = $structure_list;
						}
					}

					// 掲載情報を絞込み
					// アカウント絞込み
					if ($this->account_filter_id_only) {

						$all_structure_list = Util_Common_Filter::search_filter("account_id", $this->account_filter_text, $this->account_filter_type, $all_structure_list);

					} else {

						$all_structure_list = Util_Common_Filter::search_filter("account_name_disp", $this->account_filter_text, $this->account_filter_type, $all_structure_list);
					}

					// キャンペーン絞込み
					if ($this->campaign_filter_id_only) {

						$all_structure_list = Util_Common_Filter::search_filter("campaign_id", $this->campaign_filter_text, $this->campaign_filter_type, $all_structure_list);

					} else {

						$all_structure_list = Util_Common_Filter::search_filter("campaign_name_disp", $this->campaign_filter_text, $this->campaign_filter_type, $all_structure_list);
					}

					// 広告グループ絞込み
					if ($this->adgroup_filter_id_only) {

						$all_structure_list = Util_Common_Filter::search_filter("adgroup_id", $this->adgroup_filter_text, $this->adgroup_filter_type, $all_structure_list);

					} else {

						$all_structure_list = Util_Common_Filter::search_filter("adgroup_name_disp", $this->adgroup_filter_text, $this->adgroup_filter_type, $all_structure_list);
					}

					// 掲載情報から不要カラムを除去
					foreach ($all_structure_list as $structure_list) {

						$structure_dl_list[] = array($structure_list["media"],
													 $structure_list["account_id"],
													 $structure_list["account_name"],
													 $structure_list["campaign_id"],
													 $structure_list["campaign_name"],
													 $structure_list["adgroup_id"],
													 $structure_list["adgroup_name"],
													 $structure_list["adgroup_cpc_max_pc"],
													 $structure_list["adgroup_cpc_max_sp"],
													 $structure_list["adgroup_bid_modifier"]);
					}
				}

				if (empty($structure_dl_list)) {
					$structure_dl_list[] = array(ALERT_MSG_008);
				}

				// 掲載情報をダウンロード
				$this->format = "csv";
				$file_name = STRUCTURE_DL_FILE_NAME . date("YmdHis") . ".csv";
				$response = $this->response($structure_dl_list);
				$response->set_header("Content-Disposition", "attachment; filename=$file_name");

				return $response;
			}
		}
	}

	/**
	 * CPC変更とCPC変更内容DLを行う
	 *
	 * @param なし
	 * @return なし
	 */
	public function action_edit() {

		// キャンペーン一覧非表示の場合
		if (isset($this->new_campaign_not_disp)) {

			// アカウントのキャンペーン情報を取得
			if ($this->account_id_list) {

				$obj_campaign_list = array();

				foreach ($this->account_id_list as $account_pk) {

					list($media_id, $account_id) = explode("//", $account_pk);

					// キャンペーン情報を取得
					$campaign_list = Model_Data_EagleCampaign::get($media_id, $account_id);

					foreach ($campaign_list as $campaign) {

						// キャンペーンPK
						$campaign["campaign_pk"] = $campaign["media_id"]
												 . "//"
												 . $campaign["account_id"]
												 . "//"
												 . $campaign["campaign_id"];

						// キャンペーン名（表示用）
						$campaign["campaign_name_disp"] = "["
														. $campaign["account_id"]
														. "]["
														. $campaign["campaign_id"]
														. "] "
														. $campaign["campaign_name"];

						$obj_campaign_list[] = $campaign;
					}
				}

				// キャンペーン一覧を絞込み
				if (isset($this->campaign_search_id_only)) {

					$obj_campaign_list = Util_Common_Filter::search_filter("campaign_id", $this->campaign_search_text, $this->campaign_search_type, $obj_campaign_list);

				} else {

					$obj_campaign_list = Util_Common_Filter::search_filter("campaign_name_disp", $this->campaign_search_text, $this->campaign_search_type, $obj_campaign_list);
				}

				$this->campaign_id_list = array();

				foreach ($obj_campaign_list as $campaign_list) {

					$this->campaign_id_list[] = $campaign_list["campaign_pk"];
				}

				unset($obj_campaign_list);
			}
		}

		// アカウントの掲載一覧を取得
		if ($this->campaign_id_list) {

			$edit_exe_flg = true;

			// 掲載一覧非表示の場合、入稿に必要なデータを取得
			if ($this->action_type === "bulk_edit"
					|| $this->action_type === "bulk_cpc_change_dl"
					|| $this->action_type === "evenness_edit"
					|| $this->action_type === "evenness_cpc_change_dl") {

				// 選択アカウントの掲載取得が１時間以内に完了したか確認
				$get_structure_flg = true;

				foreach ($this->campaign_id_list as $campaign_pk) {

					list($media_id, $account_id, $campaign_id) = explode("//", $campaign_pk);

					$chk_structure_count = Model_Data_EagleTargetAccount::chk_structure(null, $account_id, $media_id);

					if ($chk_structure_count[0]["count"] === "0") {

						$get_structure_flg = false;
					}
				}

				// 選択アカウントの掲載取得が１時間以内に完了している場合のみ実行
				if ($get_structure_flg) {

					if ($this->action_type === "bulk_edit"
							|| $this->action_type === "bulk_cpc_change_dl") {

						self::_get_edit_data_bulk();

					} elseif ($this->action_type === "evenness_edit"
							|| $this->action_type === "evenness_cpc_change_dl") {

						self::_get_edit_data_evenness();
					}

				} else {

					$edit_exe_flg = false;
				}
			}

			if ($edit_exe_flg) {

				// CPC変更内容DL
				$cpc_target_values = array();
				$all_dl_structure_list = array();

				if ($this->action_type === "cpc_change_dl") {

					// ダウンロードデータを設定
					$dl_cpc_target_list[] = array("媒体",
												  "アカウントID",
												  "アカウント名",
												  "キャンペーンID",
												  "キャンペーン名",
												  "広告グループID",
												  "広告グループ名",
												  "【PC】変更前CPC",
												  "【PC】変更後CPC",
												  "【SP】変更前CPC",
												  "【SP】変更後CPC",
												  "変更前MBA",
												  "変更後MBA",
												  "エラーメッセージ");

				// CPC変更
				} else {

					// 処理IDを登録
					$setting_columns = array("exec_code");
					$setting_values[] = array($setting_columns[0] => "0003");

					$setting_result = Model_Data_EagleSetting::ins($setting_columns, $setting_values);

					// CPC変更バッチに必要な情報を登録
					$cpc_target_columns = array("id",
												"media_id",
												"account_id",
												"campaign_id",
												"adgroup_id",
												"adgroup_cpc_max",
												"adgroup_bid_modifier",
												"adgroup_bid_modifier_operator");

					// 画面の設定値を登録
					$cpc_setting_columns = array("id", "setting_value");

					$cpc_setting_list["client_id"] = $this->client_id;
					$cpc_setting_list["account_id_list"] = $this->account_id_list;
					$cpc_setting_list["campaign_id_list"] = $this->campaign_id_list;
					$cpc_setting_list["account_filter_text"] = $this->account_filter_text;
					$cpc_setting_list["account_filter_type"] = $this->account_filter_type;
					$cpc_setting_list["campaign_filter_text"] = $this->campaign_filter_text;
					$cpc_setting_list["campaign_filter_type"] = $this->campaign_filter_type;
					$cpc_setting_list["adgroup_filter_text"] = $this->adgroup_filter_text;
					$cpc_setting_list["adgroup_filter_type"] = $this->adgroup_filter_type;
					$cpc_setting_list["matchtype_filter"] = $this->matchtype_filter;

					// キャンペーン一覧非表示の場合
					if (isset($this->new_campaign_not_disp)) {

						$cpc_setting_list["new_campaign_not_disp"] = $this->new_campaign_not_disp;
						$cpc_setting_list["campaign_search_text"] = $this->campaign_search_text;
						$cpc_setting_list["campaign_search_type"] = $this->campaign_search_type;
						$cpc_setting_list["campaign_search_id_only"] = $this->campaign_search_id_only;
					}

					// 掲載一覧非表示の場合
					if (isset($this->get_structure_not_disp)) {

						$cpc_setting_list["get_structure_not_disp"] = $this->get_structure_not_disp;
						$cpc_setting_list["account_filter_text"] = $this->account_filter_text;
						$cpc_setting_list["account_filter_type"] = $this->account_filter_type;
						$cpc_setting_list["account_filter_id_only"] = $this->account_filter_id_only;
						$cpc_setting_list["campaign_filter_text"] = $this->campaign_filter_text;
						$cpc_setting_list["campaign_filter_type"] = $this->campaign_filter_type;
						$cpc_setting_list["campaign_filter_id_only"] = $this->campaign_filter_id_only;
						$cpc_setting_list["adgroup_filter_text"] = $this->adgroup_filter_text;
						$cpc_setting_list["adgroup_filter_type"] = $this->adgroup_filter_type;
						$cpc_setting_list["adgroup_filter_id_only"] = $this->adgroup_filter_id_only;
						$cpc_setting_list["matchtype_filter"] = $this->matchtype_filter;
					}

					$cpc_setting_value = serialize($cpc_setting_list);
					$cpc_setting_value = base64_encode($cpc_setting_value);

					$cpc_setting_values[] = array($cpc_setting_columns[0] => $setting_result[0],
												  $cpc_setting_columns[1] => $cpc_setting_value);

					Model_Data_EagleCpcSetting::ins($cpc_setting_columns, $cpc_setting_values);
				}

				$alert_message = "";

				foreach ($this->campaign_id_list as $campaign_pk) {

					list($media_id, $account_id, $campaign_id) = explode("//", $campaign_pk);

					// 掲載情報を取得
					$db_structure_list = Model_Data_EagleAdGroup::get($media_id, $account_id, $campaign_id);

					foreach ($this->structure_list as $structure) {

						$adgroup_pk = $structure["media_id"] . "//"
									. $structure["account_id"] . "//"
									. $structure["campaign_id"] . "//"
									. $structure["adgroup_id"];

						foreach ($db_structure_list as $db_structure) {

							$db_adgroup_pk = $campaign_pk . "//" . $db_structure["adgroup_id"];

							// 画面の掲載一覧のレコードとDBの掲載内容のレコードが一致した場合
							if ($adgroup_pk === $db_adgroup_pk) {

								// 【PC】設定CPCが変更されている場合
								$cpc_max_pc = trim($structure["adgroup_cpc_max_pc"]);

								if (intval($cpc_max_pc) !== intval($db_structure["adgroup_cpc_max"])) {

									$adgroup_cpc_max_pc = $cpc_max_pc;

								} else {

									$adgroup_cpc_max_pc = null;
								}

								// 【SP】設定CPCが変更されている場合
								$cpc_max_sp = trim($structure["adgroup_cpc_max_sp"]);

								if (!is_null($db_structure["adgroup_bid_modifier"]) && $db_structure["adgroup_bid_modifier"] !== "") {

									$db_cpc_max_sp = round($db_structure["adgroup_cpc_max"] * $db_structure["adgroup_bid_modifier"]);

								} else {

									$db_cpc_max_sp = $db_structure["adgroup_cpc_max"];
								}

								if (intval($cpc_max_sp) !== intval($db_cpc_max_sp)) {

									$adgroup_cpc_max_sp = $cpc_max_sp;

								} else {

									$adgroup_cpc_max_sp = null;
								}

								// 設定MBAが変更されている場合
								$bid_modifier = "";

								if (!is_null($structure["adgroup_bid_modifier"]) && $structure["adgroup_bid_modifier"] !== "") {

									$bid_modifier = round(($structure["adgroup_bid_modifier"] / 100) + 1, 3);
								}

								if (intval($media_id) !== MEDIA_ID_IM
										&& $bid_modifier !== ""
										&& floatval($bid_modifier) !== floatval($db_structure["adgroup_bid_modifier"])) {

									$adgroup_bid_modifier = $bid_modifier;

								} else {

									$adgroup_bid_modifier = null;
								}

								// AdWordsAPIのOperatorを設定
								$adwords_mba_operator = null;

								if (intval($media_id) === MEDIA_ID_GOOGLE) {

									if (is_null($db_structure["adgroup_bid_modifier"])) {

										$adwords_mba_operator = "ADD";

									} else {

										$adwords_mba_operator = "SET";
									}
								}

								// 全掲載DL用に保持
								$bef_adgroup_bid_modifier = "";

								if ($db_structure["adgroup_bid_modifier"]) {

									$bef_adgroup_bid_modifier = ($db_structure["adgroup_bid_modifier"] - 1) * 100 . "%";
								}

								$all_dl_structure_list[] = array($GLOBALS["media_name_list"][$media_id],
																 $structure["account_id"],
																 $structure["account_name"],
																 $structure["campaign_id"],
																 $structure["campaign_name"],
																 $structure["adgroup_id"],
																 $structure["adgroup_name"],
																 $db_structure["adgroup_cpc_max"],
																 "",
																 $db_cpc_max_sp,
																 "",
																 $bef_adgroup_bid_modifier,
																 "");

								if (!is_null($adgroup_cpc_max_pc) || !is_null($adgroup_bid_modifier)) {

									$alert_message = "";

									// モバイル調整率をチェック
									if ($structure["adgroup_bid_modifier"] < -100
											|| $structure["adgroup_bid_modifier"] > 300
											|| ($structure["adgroup_bid_modifier"] < -90 && $structure["adgroup_bid_modifier"] > -100)) {

										$alert_message = ALERT_MSG_003;
									}

									// CPC変更内容DL
									if ($this->action_type === "cpc_change_dl") {

										$bef_adgroup_bid_modifier = "";

										if ($db_structure["adgroup_bid_modifier"]) {

											$bef_adgroup_bid_modifier = ($db_structure["adgroup_bid_modifier"] - 1) * 100 . "%";
										}

										if (!is_null($adgroup_bid_modifier) && $adgroup_bid_modifier !== "") {

											$adgroup_bid_modifier = ($adgroup_bid_modifier - 1) * 100 . "%";
										}

										$dl_cpc_target_list[] = array($GLOBALS["media_name_list"][$media_id],
																	  $structure["account_id"],
																	  $structure["account_name"],
																	  $structure["campaign_id"],
																	  $structure["campaign_name"],
																	  $structure["adgroup_id"],
																	  $structure["adgroup_name"],
																	  $db_structure["adgroup_cpc_max"],
																	  $adgroup_cpc_max_pc,
																	  $db_cpc_max_sp,
																	  $adgroup_cpc_max_sp,
																	  $bef_adgroup_bid_modifier,
																	  $adgroup_bid_modifier,
																	  $alert_message);

									// CPC変更
									} else {

										// 入稿内容にエラーがある場合は、入稿しない
										if ($alert_message) {

											unset($cpc_target_values);
											break 3;
										}

										$value = array();
										$value[$cpc_target_columns[0]] = $setting_result[0];
										$value[$cpc_target_columns[1]] = $media_id;
										$value[$cpc_target_columns[2]] = $account_id;
										$value[$cpc_target_columns[3]] = $campaign_id;
										$value[$cpc_target_columns[4]] = $structure["adgroup_id"];
										$value[$cpc_target_columns[5]] = $adgroup_cpc_max_pc;
										$value[$cpc_target_columns[6]] = $adgroup_bid_modifier;
										$value[$cpc_target_columns[7]] = $adwords_mba_operator;

										$cpc_target_values[] = $value;
									}
								}

								break;
							}
						}
					}
				}

				if ($this->action_type === "cpc_change_dl") {

					// CPC変更内容が存在しない場合、全掲載をダウンロード
					if (count($dl_cpc_target_list) === 1) {

						$dl_cpc_target_list = array_merge($dl_cpc_target_list, $all_dl_structure_list);
					}

					// CPC変更内容をダウンロード
					$this->format = "csv";
					$file_name = CPC_CHANGE_DL_FILE_NAME . date("YmdHis") . ".csv";
					$response = $this->response($dl_cpc_target_list);
					$response->set_header("Content-Disposition", "attachment; filename=$file_name");

					return $response;

				} else {

					if (isset($cpc_target_values)) {

						Model_Data_EagleCpcTarget::ins($cpc_target_columns, $cpc_target_values);

						// CPC変更バッチを実行
						$curl = Request::forge("http://" . JENKINS_HOST . "/job/" . urlencode(JENKINS_EAGLE_CPC_JOB) . "/buildWithParameters?token=eagle&id=" . $setting_result[0] . "&client_id=" . $this->client_id . "&user_id_sem=" . \Session::get("user_id_sem"), "curl");
						$curl->set_option(CURLOPT_USERPWD, JENKINS_USERPWD);
						$curl->execute();

					} elseif (isset($alert_message)) {

						$this->alert_message = ALERT_MSG_004;
					}
				}

			} else {

				$this->alert_message = ALERT_MSG_007;
			}
		}

		// メイン処理を実行
		$this->action_index();
	}

	/**
	 * 入稿に必要なデータを取得する
	 *
	 * @param なし
	 * @return なし
	 */
	private function _get_edit_data_bulk() {

		$obj_structure_list = array();

		foreach ($this->structure_list as $structure) {

			// アカウント一覧を取得
			$account_list = Model_Mora_Account::get_account($structure["account_id"]);

			// キャンペーン一覧を取得
			$campaign_list = Model_Data_EagleCampaign::get_by_pk($account_list[0]["media_id"],
																 $structure["account_id"],
																 $structure["campaign_id"]);

			// 広告グループ一覧を取得
			$adgroup_list = Model_Data_EagleAdGroup::get_by_pk($account_list[0]["media_id"],
															   $structure["account_id"],
															   $structure["campaign_id"],
															   $structure["adgroup_id"]);

			// CPC PCを設定
			$cpc_max_pc = 0;

			if (isset($structure["adgroup_cpc_max_pc"]) && $structure["adgroup_cpc_max_pc"] !== "") {

				$cpc_max_pc = $structure["adgroup_cpc_max_pc"];

			} else {

				$cpc_max_pc = $adgroup_list[0]["adgroup_cpc_max"];
			}

			// CPC SPを設定
			$cpc_max_sp = 0;

			if (isset($structure["adgroup_cpc_max_sp"]) && $structure["adgroup_cpc_max_sp"] !== "") {

				$cpc_max_sp = $structure["adgroup_cpc_max_sp"];

			} else {

				if ($structure["method"] === "cpc") {

					// CPC
					$cpc_max_sp = round($adgroup_list[0]["adgroup_cpc_max"] * $adgroup_list[0]["adgroup_bid_modifier"]);

				} elseif ($structure["method"] === "mba") {

					// MBA
					$bid_modifier = round(($structure["adgroup_bid_modifier"] / 100) + 1, 3);
					$cpc_max_sp = round($adgroup_list[0]["adgroup_cpc_max"] * $bid_modifier);

				} elseif ($structure["method"] === "default_cpc") {

					// Default CPC
					$cpc_max_sp = round($structure["adgroup_cpc_max_pc"] * $adgroup_list[0]["adgroup_bid_modifier"]);
				}
			}

			// MBAを設定
			$mba = 0;

			if ($structure["method"] === "cpc") {

				$mba = round(($cpc_max_sp / $cpc_max_pc) * 100) / 100;

				// MBAが上限を超えた場合
				if ($mba > MAX_BID_MODIFIER) {

					$mba = MAX_BID_MODIFIER;
					$cpc_max_sp = $cpc_max_pc * $mba;
				}

				$mba = ($mba - 1) * 100;

			} elseif ($structure["method"] === "mba") {

				$mba = $structure["adgroup_bid_modifier"];

			} elseif ($structure["method"] === "default_cpc") {

				$mba = ($adgroup_list[0]["adgroup_bid_modifier"] - 1) * 100;
			}

			// 掲載一覧に設定
			$structure["media_id"] = $account_list[0]["media_id"];
			$structure["account_name"] = $account_list[0]["account_name"];
			$structure["campaign_name"] = $campaign_list[0]["campaign_name"];
			$structure["adgroup_name"] = $adgroup_list[0]["adgroup_name"];
			$structure["adgroup_cpc_max_pc"] = $cpc_max_pc;
			$structure["adgroup_cpc_max_sp"] = $cpc_max_sp;
			$structure["adgroup_bid_modifier"] = $mba;

			$obj_structure_list[] = $structure;
		}

		$this->structure_list = $obj_structure_list;

		if ($this->action_type === "bulk_edit") {

			$this->action_type = "edit";

		} elseif ($this->action_type === "bulk_cpc_change_dl") {

			$this->action_type = "cpc_change_dl";
		}
	}

	/**
	 * 入稿に必要なデータを取得する
	 *
	 * @param なし
	 * @return なし
	 */
	private function _get_edit_data_evenness() {

		// アカウントの掲載一覧を取得
		if ($this->campaign_id_list) {

			$all_structure_list = array();

			foreach ($this->campaign_id_list as $campaign_pk) {

				list($media_id, $account_id, $campaign_id) = explode("//", $campaign_pk);

				// アカウント一覧を取得
				$account_list = Model_Mora_Account::get_account($account_id);

				// キャンペーン一覧を取得
				$campaign_list = Model_Data_EagleCampaign::get_by_pk($media_id, $account_id, $campaign_id);

				// 広告グループ一覧を取得
				$adgroup_list = Model_Data_EagleAdGroup::get($media_id, $account_id, $campaign_id);

				foreach ($adgroup_list as $adgroup) {

					// アカウント名
					$adgroup["account_name"] = $account_list[0]["account_name"];

					// アカウント名（表示用）
					$adgroup["account_name_disp"] = "["
												  . $account_list[0]["id"]
												  . "] "
												  . $account_list[0]["account_name"];

					// キャンペーン名
					$adgroup["campaign_name"] = $campaign_list[0]["campaign_name"];

					// キャンペーン名（表示用）
					$adgroup["campaign_name_disp"] = "["
												   . $campaign_list[0]["campaign_id"]
												   . "] "
												   . $campaign_list[0]["campaign_name"];

					// 広告グループ名（表示用）
					$adgroup["adgroup_name_disp"] = "["
												  . $adgroup["adgroup_id"]
												  . "] "
												  . $adgroup["adgroup_name"];

					$all_structure_list[] = $adgroup;
				}
			}

			// アカウント絞込み
			if ($this->account_filter_id_only) {

				$all_structure_list = Util_Common_Filter::search_filter("account_id", $this->account_filter_text, $this->account_filter_type, $all_structure_list);

			} else {

				$all_structure_list = Util_Common_Filter::search_filter("account_name_disp", $this->account_filter_text, $this->account_filter_type, $all_structure_list);
			}

			// キャンペーン絞込み
			if ($this->campaign_filter_id_only) {

				$all_structure_list = Util_Common_Filter::search_filter("campaign_id", $this->campaign_filter_text, $this->campaign_filter_type, $all_structure_list);

			} else {

				$all_structure_list = Util_Common_Filter::search_filter("campaign_name_disp", $this->campaign_filter_text, $this->campaign_filter_type, $all_structure_list);
			}

			// 広告グループ絞込み
			if ($this->adgroup_filter_id_only) {

				$all_structure_list = Util_Common_Filter::search_filter("adgroup_id", $this->adgroup_filter_text, $this->adgroup_filter_type, $all_structure_list);

			} else {

				$all_structure_list = Util_Common_Filter::search_filter("adgroup_name_disp", $this->adgroup_filter_text, $this->adgroup_filter_type, $all_structure_list);
			}

			// 掲載一覧を設定
			$obj_structure_list = array();

			foreach ($all_structure_list as $structure_list) {

				$cpc_max_pc = $structure_list["adgroup_cpc_max"];
				$cpc_max_sp = round($structure_list["adgroup_cpc_max"] * $structure_list["adgroup_bid_modifier"]);
				$value = intval($this->cpc_evenness_change_value);
				$cpc_max_pc_calc = 0;
				$cpc_max_sp_calc = 0;

				// 金額
				if ($this->cpc_evenness_change_method === "amount") {

					// 上げる
					if ($this->cpc_evenness_change_type === "up") {

						$cpc_max_pc_calc = $cpc_max_pc + $value;
						$cpc_max_sp_calc = $cpc_max_sp + $value;

					// 下げる
					} elseif ($this->cpc_evenness_change_type === "down") {

						$cpc_max_pc_calc = $cpc_max_pc - $value;
						$cpc_max_sp_calc = $cpc_max_sp - $value;
					}

				// パーセント
				} elseif ($this->cpc_evenness_change_method === "percent") {

					// 上げる
					if ($this->cpc_evenness_change_type === "up") {

						$cpc_max_pc_calc = $cpc_max_pc + round($cpc_max_pc * ($value / 100));
						$cpc_max_sp_calc = $cpc_max_sp + round($cpc_max_sp * ($value / 100));

					// 下げる
					} elseif ($this->cpc_evenness_change_type === "down") {

						$cpc_max_pc_calc = $cpc_max_pc - round($cpc_max_pc * ($value / 100));
						$cpc_max_sp_calc = $cpc_max_sp - round($cpc_max_sp * ($value / 100));
					}
				}

				$obj_cpc_max_pc = 0;
				$obj_cpc_max_sp = 0;

				// PC
				if ($this->cpc_evenness_change_device === "pc") {

					$obj_cpc_max_pc = $cpc_max_pc_calc;
					$obj_cpc_max_sp = $cpc_max_sp;

				// SP
				} elseif ($this->cpc_evenness_change_device === "sp") {

					$obj_cpc_max_pc = $cpc_max_pc;
					$obj_cpc_max_sp = $cpc_max_sp_calc;

				// PC / SP
				} elseif ($this->cpc_evenness_change_device === "pc_sp") {

					$obj_cpc_max_pc = $cpc_max_pc_calc;
					$obj_cpc_max_sp = $cpc_max_sp_calc;
				}

				$mba = round(($obj_cpc_max_sp / $obj_cpc_max_pc) * 100) / 100;

				// MBAが上限を超えた場合
				if ($mba > MAX_BID_MODIFIER) {

					$mba = MAX_BID_MODIFIER;
					$obj_cpc_max_sp = $obj_cpc_max_pc * $mba;
				}

				$mba = ($mba - 1) * 100;

				// 掲載一覧に設定
				$structure_list["adgroup_cpc_max_pc"] = $obj_cpc_max_pc;
				$structure_list["adgroup_cpc_max_sp"] = $obj_cpc_max_sp;
				$structure_list["adgroup_bid_modifier"] = $mba;

				$obj_structure_list[] = $structure_list;
			}
		}

		$this->structure_list = $obj_structure_list;

		if ($this->action_type === "evenness_edit") {

			$this->action_type = "edit";

		} elseif ($this->action_type === "evenness_cpc_change_dl") {

			$this->action_type = "cpc_change_dl";
		}
	}

	/**
	 * CPC変更画面を呼び出す（メールからの呼出し）
	 *
	 * @param $id 処理ID
	 * @return なし
	 */
	public function action_from_mail($id) {

		// 画面の設定値を取得
		$eagle_cpc_setting = Model_Data_EagleCpcSetting::get($id);

		if ($eagle_cpc_setting) {

			// CPC変更からの遷移の場合
			$cpc_setting_value = $eagle_cpc_setting["setting_value"];
			$cpc_setting_value = base64_decode($cpc_setting_value);
			$cpc_setting_list = unserialize($cpc_setting_value);

			$this->client_id = $cpc_setting_list["client_id"];
			$this->account_id_list = $cpc_setting_list["account_id_list"];
			$this->campaign_id_list = $cpc_setting_list["campaign_id_list"];
			$this->account_filter_text = $cpc_setting_list["account_filter_text"];
			$this->account_filter_type = $cpc_setting_list["account_filter_type"];
			$this->campaign_filter_text = $cpc_setting_list["campaign_filter_text"];
			$this->campaign_filter_type = $cpc_setting_list["campaign_filter_type"];
			$this->adgroup_filter_text = $cpc_setting_list["adgroup_filter_text"];
			$this->adgroup_filter_type = $cpc_setting_list["adgroup_filter_type"];
			$this->matchtype_filter = $cpc_setting_list["matchtype_filter"];

			// キャンペーン一覧非表示の場合
			if (isset($cpc_setting_list["new_campaign_not_disp"])) {

				$this->new_campaign_not_disp = $cpc_setting_list["new_campaign_not_disp"];
				$this->campaign_search_text = $cpc_setting_list["campaign_search_text"];
				$this->campaign_search_type = $cpc_setting_list["campaign_search_type"];
				$this->campaign_search_id_only = $cpc_setting_list["campaign_search_id_only"];
			}

			// 掲載一覧非表示の場合
			if (isset($cpc_setting_list["get_structure_not_disp"])) {

				$this->get_structure_not_disp = $cpc_setting_list["get_structure_not_disp"];
				$this->account_filter_text = $cpc_setting_list["account_filter_text"];
				$this->account_filter_type = $cpc_setting_list["account_filter_type"];
				$this->account_filter_id_only = $cpc_setting_list["account_filter_id_only"];
				$this->campaign_filter_text = $cpc_setting_list["campaign_filter_text"];
				$this->campaign_filter_type = $cpc_setting_list["campaign_filter_type"];
				$this->campaign_filter_id_only = $cpc_setting_list["campaign_filter_id_only"];
				$this->adgroup_filter_text = $cpc_setting_list["adgroup_filter_text"];
				$this->adgroup_filter_type = $cpc_setting_list["adgroup_filter_type"];
				$this->adgroup_filter_id_only = $cpc_setting_list["adgroup_filter_id_only"];
				$this->matchtype_filter = $cpc_setting_list["matchtype_filter"];
			}
		} else {

			// ステータス変更及び、アカウント同期予約からの遷移の場合
			$eagle_target_account = Model_Data_EagleTargetAccount::get($id);

			foreach ($eagle_target_account as $value) {
				$this->account_id_list[] = $value["media_id"] . "//" . $value["account_id"];
			}
			$this->new_campaign_not_disp = "checked";
			$this->get_structure_not_disp = "checked";
		}

		// メイン処理を実行
		$this->action_index();
	}

	/**
	 * CPC一括変更画面を呼び出す
	 *
	 * @param なし
	 * @return なし
	 */
	public function action_cpc_bulk_change() {

		// Viewを実行
		$this->view->set("get_structure_not_disp", $this->get_structure_not_disp);

		$this->view->set_filename("eagle/cpc/cpcbulkchange");
		return Response::forge($this->view);
	}

	/**
	 * CPC一律変更画面を呼び出す
	 *
	 * @param なし
	 * @return なし
	 */
	public function action_cpc_evenness_change() {

		// Viewを実行
		$this->view->set("get_structure_not_disp", $this->get_structure_not_disp);

		$this->view->set_filename("eagle/cpc/cpcevennesschange");
		return Response::forge($this->view);
	}
}
