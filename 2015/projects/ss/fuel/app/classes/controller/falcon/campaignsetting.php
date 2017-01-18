<?php

/**
 * falconレポートキャンペーン設定系コントローラ
 **/

class Controller_Falcon_CampaignSetting extends Controller_Falcon_Base {

	// loginユーザ権限チェック用URL
	public $access_url = "/sem/universe_fast/universe.php";

	public function before() {

		parent::before();

		/**
		 * formdataはjsonにシリアライズされたtextが来る
		 * PostData 自体はMultipart-Form-Data で来るので、
		 * upload file があれば $_FILES に格納される
		 */
		$json_input = Input::param("form");
		$input_form = json_decode($json_input, true);

		$this->data["client_id"]              = $input_form["client_id"];
		$this->data["account_id_list"]        = $input_form["account_id_list"];
		$this->data["setting_type"]           = $input_form["setting_type"];
		$this->data["template_id"]            = $input_form["template_id"];
		$this->data["no_setting_flg"]         = $input_form["no_setting_flg"];
		$this->data["export_exclusion_type"]  = $input_form["export_exclusion_type"];

		$this->data["campaign_search_like"]   = $input_form["campaign_search_like"];
		$this->data["except_campaign_search"] = $input_form["except_campaign_search"];
		$this->data["campaign_search_type"]   = $input_form["campaign_search_type"];
		$this->data["campaign_search_text"]   = $input_form["campaign_search_text"];

		if (!empty($input_form["update_list"])) $this->data["update_list"] = $input_form["update_list"];
	}

	//設定シートダウンロード
	public function action_campaigndownload() {

		if($this->data["setting_type"] == "exclusion"){
			$setting_type_name = "キャンペーンフィルタ設定";
			//初期化
			$file_contents[] = array("媒体名", "アカウントID", "アカウント名", "キャンペーンID", "キャンペーン名", "キャンペーンステータス", "フィルタ設定(除外/包括/設定解除)");
			$file_contents[] = array("media_name", "account_id", "account_name", "campaign_id", "campaign_name", "campaign_status", "exclusion_flg", "※この行は削除しないでください");
		}else{
			$setting_type_name = "キャンペーン属性設定";
			//初期化
			$file_contents[] = array("媒体名", "アカウントID", "アカウント名", "キャンペーンID", "キャンペーン名", "キャンペーンステータス", "デバイス(PC/MB/SP)", "【Googleのみ設定可】プロダクト(検索/コンテンツ/プレースメント/オンラインキャンペーン/リマーケティング/その他)");
			$file_contents[] = array("media_name", "account_id", "account_name", "campaign_id", "campaign_name", "campaign_status", "device_id", "ad_type_id", "※この行は削除しないでください");
		}

		//指定アカウント毎にループ
		foreach ($this->data["account_id_list"] as $account_id) {

			//media_id, account_id 分割
			list($tmp_media_id, $tmp_account_id) = explode("//",  $account_id);

			//指定アカウント情報取得
			$account = \Model_Mora_Account::get_for_account($tmp_account_id);

			//指定アカウントのキャンペーン情報取得
			$campaign_list = \Model_Structure_Falcon_Campaign::get($tmp_media_id, $tmp_account_id);

			if($this->data["setting_type"] == "exclusion"){
				//指定アカウントのキャンペーン除外情報取得
				$campaign_exclusion_list = \Model_Data_Falcon_CampaignExclusionSetting::get($this->data["template_id"], $tmp_media_id, $tmp_account_id);
			}else{
				//指定アカウントのキャンペーン属性情報取得
				$campaign_attribute_list = \Model_Data_Falcon_CampaignAttributeSetting::get($tmp_media_id, $tmp_account_id);
			}

			//アカウント情報をキャンペーン情報に付加
			foreach ($campaign_list as $campaign) {

				//初期化
				$target_flg = false;

				//キー
				$key = $campaign["media_id"] . "::" .$campaign["account_id"] . "::" . $campaign["campaign_id"];

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

				//ステータスを共通のものに変換
				if (FalconConst::$falcon_status_replace_list[strtolower($campaign["campaign_status"])]) {
					$campaign_status = FalconConst::$falcon_status_replace_list[strtolower($campaign["campaign_status"])];
				}else{
					$campaign_status = null;
				}

				//除外設定
				if($this->data["setting_type"] == "exclusion"){

					if(isset($campaign_exclusion_list[$key]["exclusion_flg"])){
						if($campaign_exclusion_list[$key]["exclusion_flg"] == 0){
							$exclusion_flg = "除外";
							if($this->data["export_exclusion_type"]==1){
								$target_flg = false;
							}
						}else{
							$exclusion_flg = "包括";
							if($this->data["export_exclusion_type"]==0){
								$target_flg = false;
							}
						}
					}else{
						$exclusion_flg = "";
						if($this->data["export_exclusion_type"]!=2){
							$target_flg = false;
						}
					}

					if($target_flg){
						$file_contents[] = array(FalconConst::$falcon_media_list[$tmp_media_id],
												 $tmp_account_id,
												 $account["account_name"],
												 $campaign["campaign_id"],
												 $campaign["campaign_name"],
												 $campaign_status,
												 $exclusion_flg
												 );
					}

				//属性設定
				}else{

					if(isset($campaign_attribute_list[$key])){
						$device_id  = isset($campaign_attribute_list[$key]["device_id"]) ? FalconConst::$falcon_device_list[$campaign_attribute_list[$key]["device_id"]] : "未設定" ;
						$ad_type_id = isset($campaign_attribute_list[$key]["ad_type_id"]) ? FalconConst::$falcon_ad_type_list[$campaign_attribute_list[$key]["ad_type_id"]] : "未設定" ;
						if ($ad_type_id == "未設定" && ($tmp_media_id != MEDIA_ID_GOOGLE || $tmp_media_id != MEDIA_ID_GOOGLE_CONSULTING)) {
							$ad_type_id   = "--";
						}
						if($this->data["no_setting_flg"]){
							$target_flg = false;
						}
					}else{
						$device_id    = "未設定";
						if ($tmp_media_id == MEDIA_ID_GOOGLE || $tmp_media_id == MEDIA_ID_GOOGLE_CONSULTING) {
							$ad_type_id   = "未設定";
						}else{
							$ad_type_id   = "--";
						}
					}

					if($target_flg){
						$file_contents[] = array(FalconConst::$falcon_media_list[$tmp_media_id],
												 $tmp_account_id,
												 $account["account_name"],
												 $campaign["campaign_id"],
												 $campaign["campaign_name"],
												 $campaign_status,
												 $device_id,
												 $ad_type_id
												 );
					}

				}
			}
		}

		//クライアント名取得
		$client_name = \Model_Mora_Client::get_client_name($this->data["client_id"]);
		if($client_name["client_name"]){
			$client_name["company_name"] = $client_name["company_name"]. "(" . $client_name["client_name"] .")";
		}
		//テンプレート名取得
		$template_info = \Model_Data_Falcon_Template::get_info($this->data["template_id"]);
		if($template_info["template_name"]){
			$template_name = $template_info["template_name"] . "_";
		}else{
			$template_name = "_";
		}

		//ファイルダウンロード
		$this->format = "csv";
		$filename = "Falcon_" . $setting_type_name . "【" . $client_name["company_name"] ."】" . $template_name . date("YmdHis") . ".csv";
		$response = $this->response($file_contents);
		$response->set_header("Content-Disposition", "attachment; filename=" . $filename);

		return $response;
	}

	//設定シートアップロード
	public function action_campaignupload(){

		$upload_setting_type = $this->data["setting_type"];
		$upload_template_id = $this->data["template_id"];
		// $upload_campaign_exclusion_memo = Input::post("upload_campaign_exclusion_memo_".$upload_template_id);
		$upload_campaign_exclusion_memo = ''; // CP設定でのメモ追加はいったんオフる


		ini_set('auto_detect_line_endings', 1); // macの改行コードに対応

		if(!empty($_FILES)) {
			$file_name = 'falcon_campaign_setting_'.$this->data["client_id"].'_'.$upload_setting_type;
			if($upload_setting_type == 'exclusion'){
				if(!isset($upload_template_id)){
					return FALSE;
				}
				$file_name .= "_" . $upload_template_id;
			}
			$file_name .= '.csv';

			Upload::process(
				array(
					'path' => FALCON_CAMPAIGN_SETTING_UPLOAD_DIR,
					'new_name' => $file_name,
					'auto_rename' => false,
					'overwrite' => true,
					'ext_whitelist' => array('csv'),
				)
			);
			Upload::save(0);

			$upload_file      = Upload::get_files(0);
			$upload_file_path = $upload_file["saved_to"].$upload_file["saved_as"];

			$file_content     = File::read($upload_file_path, true);

			if(isset($file_content)){

				$update_list = array();
				$del_list = array();
				$header_list = array();
				$contents_list = array();
				$account_id_list = array();
				$campaign_id_list = array();
				$exclusion_check_list = array();

				$upload_list_tmp  = mb_convert_encoding($file_content, "UTF-8", "SJIS");
				$upload_list_tmp  = preg_replace("/\r\n|\r|\n/", "\n", $upload_list_tmp);
				$upload_list_line = explode("\n", $upload_list_tmp);

				$line_no = 1;
				foreach ($upload_list_line as $line) {
					$upload_elem_list = array();

					if ($line_no == 1) {
						// ヘッダを読み込む
						$header_list = explode(",", $line);
						$line_no++;
						continue;
					} else if ($line_no == 2) {
						// システムカラムを設定
						$system_column_name_list = explode(",", str_replace('"', '', $line));
						$line_no++;
						if($upload_setting_type == 'exclusion'){
							$setting_type_name = "キャンペーンフィルタ設定";
							if($system_column_name_list[6]!="exclusion_flg"){
								$file_contents[] = array("エラー：設定シートにフィルタ設定(exclusion_flg)が存在しません");
								break;
							}
						}else{
							$setting_type_name = "キャンペーン属性設定";
							if($system_column_name_list[6]!="device_id" || $system_column_name_list[7]!="ad_type_id"){
								$file_contents[] = array("エラー：設定シートにデバイス(device_id)またはプロダクト(ad_type_id)が存在しません");
								break;
							}
						}
						continue;
					} else if (trim($line) == "") {
						break;
					} else {
						$target_flg = true;
					}

					$upload_elem_list = explode(",", $line);

					foreach ($upload_elem_list as $no => $value_tmp) {
						if ($system_column_name_list[$no] == "※この行は削除しないでください") {
							break;
						}

						$value = preg_replace('/^"|"$/', '', trim($value_tmp));
						$contents_list["client_id"] = $this->data["client_id"];

						if($system_column_name_list[$no] == "media_name"){
								foreach (FalconConst::$falcon_media_list as $media_tmp => $media_value){
									if($media_value == $value){
										$contents_list["media_id"] = $media_tmp;
										$out_contents_list["media_id"] = $media_tmp;
										$media_id = $media_tmp;
									}
								}

						}elseif ($system_column_name_list[$no] == "account_id" || $system_column_name_list[$no] == "campaign_id") {
							$value = str_replace("'", "", $value);
							$contents_list[$system_column_name_list[$no]] = $value;
							$out_contents_list[$system_column_name_list[$no]] = $value;
							if($system_column_name_list[$no] == "account_id"){
								$account_id_list[$value] = $value;
								$account_id = $value;
							}else{
								$campaign_id_list[] = $value;
							}
						}elseif ($system_column_name_list[$no] == "account_name" || $system_column_name_list[$no] == "campaign_name" || $system_column_name_list[$no] == "campaign_status") {
							$out_contents_list[$system_column_name_list[$no]] = $value;
							continue;
						}else{
							if($upload_setting_type == 'exclusion'){
								$contents_list["template_id"] = $upload_template_id;

								if($system_column_name_list[$no] == "exclusion_flg"){
									if($value == "除外"){
										$contents_list[$system_column_name_list[$no]] = 0;
										$exclusion_check_list[$account_id]["exclusion"] = TRUE;
									}elseif($value == "包括"){
										$contents_list[$system_column_name_list[$no]] = 1;
										$exclusion_check_list[$account_id]["inclusion"] = TRUE;
									}else{
										$target_flg = false;
									}
								}

							}elseif($upload_setting_type == 'attribute'){

								if($system_column_name_list[$no] == "device_id"){
									$contents_list[$system_column_name_list[$no]] = NULL;

									foreach (FalconConst::$falcon_device_list as $tmp_key => $tmp_value){
										if($tmp_value == $value){
											$contents_list[$system_column_name_list[$no]] = $tmp_key;
										}
									}
								}
								if($system_column_name_list[$no] == "ad_type_id"){
									$contents_list[$system_column_name_list[$no]] = NULL;

									if ($media_id == MEDIA_ID_GOOGLE || $media_id == MEDIA_ID_GOOGLE_CONSULTING) {
										foreach (FalconConst::$falcon_ad_type_list as $tmp_key => $tmp_value){
											if($tmp_value == $value && $value!="YDN"){
												$contents_list[$system_column_name_list[$no]] = $tmp_key;
											}
										}
									}
								}
							}
						}
					}
					if($upload_setting_type == 'attribute' && $contents_list["device_id"] == NULL && $contents_list["ad_type_id"] == NULL){
						$target_flg = false;
					}

					if($target_flg){
						$update_list[] = $contents_list;
						$line_no++;
					}else{
						unset($contents_list["exclusion_flg"]);
						unset($contents_list["device_id"]);
						unset($contents_list["ad_type_id"]);

						$del_list[] = $contents_list;
						$line_no++;
					}
					$file_contents_tmp[] = $out_contents_list;
				}

				if (!isset($update_list[0]) && !isset($del_list[0])) {
					$file_contents[] = array("更新用ファイル値が存在しません");
				}else{
					if($upload_setting_type == "exclusion"){
						//同一アカウント内で除外・包括が設定されている場合、包括設定は無視
						if(isset($exclusion_check_list)){
							foreach ($exclusion_check_list as $check_account_id => $check_flg){
								foreach ($update_list as $tmp => $tmp_val){
									if((isset($check_flg["exclusion"]) && $check_flg["exclusion"] == TRUE) && (isset($check_flg["inclusion"]) && $check_flg["inclusion"] == TRUE) && ($tmp_val["account_id"] == $check_account_id && $tmp_val["exclusion_flg"] == 1)){
										//包括設定を削除リストに追加
										$del_list[] = array(
													'template_id' => $tmp_val["template_id"],
													'client_id' => $tmp_val["client_id"],
													'account_id' => $tmp_val["account_id"],
													'media_id' => $tmp_val["media_id"],
													'campaign_id' => $tmp_val["campaign_id"]
										);

										//登録リストから包括設定を削除
										unset($update_list[$tmp]);
									}
								}
							}
						}

						if(isset($del_list[0])){
							\Model_Data_Falcon_CampaignExclusionSetting::del($del_list);
						}
						if(isset($update_list[0])){
							\Model_Data_Falcon_CampaignExclusionSetting::ins($upload_template_id, $update_list, $upload_campaign_exclusion_memo);
						}

						//更新結果を取得
						$campaign_exclusion_list = \Model_Data_Falcon_CampaignExclusionSetting::get_for_campaign_id($upload_template_id, $account_id_list, $campaign_id_list);
						$file_contents[] = array("媒体名", "アカウントID", "アカウント名", "キャンペーンID", "キャンペーン名", "キャンペーンステータス", "フィルタ設定(除外/包括/設定解除)");
						$file_contents[] = array("media_name", "account_id", "account_name", "campaign_id", "campaign_name", "campaign_status", "exclusion_flg", "※この行は削除しないでください");

						foreach ($campaign_exclusion_list as $key => $value){
							foreach ($file_contents_tmp as $contents_key => $contents_value){
								if($contents_value["media_id"]==$value["media_id"] && $contents_value["account_id"]==$value["account_id"] && $contents_value["campaign_id"]==$value["campaign_id"]){
									if($value["exclusion_flg"] == 0){
										$exclusion_flg = "除外";
									}elseif($value["exclusion_flg"] == 1){
										$exclusion_flg = "包括";
									}else{
										$exclusion_flg = "";
									}

									$file_contents[] = array(FalconConst::$falcon_media_list[$value["media_id"]],
															 $value["account_id"],
															 $contents_value["account_name"],
															 $value["campaign_id"],
															 $contents_value["campaign_name"],
															 $contents_value["campaign_status"],
															 $exclusion_flg
															 );

								}
							}
						}
					}else{
						if(isset($del_list[0])){
							\Model_Data_Falcon_CampaignAttributeSetting::del($del_list);
						}
						if(isset($update_list[0])){
							\Model_Data_Falcon_CampaignAttributeSetting::ins($this->data["client_id"], $update_list);
						}

						//更新結果を取得
						$campaign_attribute_list = \Model_Data_Falcon_CampaignAttributeSetting::get_for_campaign_id($this->data["client_id"], $account_id_list, $campaign_id_list);
						$file_contents[] = array("媒体名", "アカウントID", "アカウント名", "キャンペーンID", "キャンペーン名", "キャンペーンステータス", "デバイス(PC/MB/SP)", "【Googleのみ設定可】プロダクト(検索/コンテンツ/プレースメント/オンラインキャンペーン/リマーケティング/その他)");
						$file_contents[] = array("media_name", "account_id", "account_name", "campaign_id", "campaign_name", "campaign_status", "device_id", "ad_type_id", "※この行は削除しないでください");

						foreach ($campaign_attribute_list as $key => $value){
							foreach ($file_contents_tmp as $contents_key => $contents_value){
								if($contents_value["media_id"]==$value["media_id"] && $contents_value["account_id"]==$value["account_id"] && $contents_value["campaign_id"]==$value["campaign_id"]){
									$device_id  = isset($value["device_id"]) ? FalconConst::$falcon_device_list[$value["device_id"]] : "未設定" ;
									$ad_type_id = isset($value["ad_type_id"]) ? FalconConst::$falcon_ad_type_list[$value["ad_type_id"]] : "未設定" ;
									if ($ad_type_id == "未設定" && ($value["media_id"] != MEDIA_ID_GOOGLE || $value["media_id"] != MEDIA_ID_GOOGLE_CONSULTING)) {
										$ad_type_id   = "--";
									}

									$file_contents[] = array(FalconConst::$falcon_media_list[$value["media_id"]],
															 $value["account_id"],
															 $contents_value["account_name"],
															 $value["campaign_id"],
															 $contents_value["campaign_name"],
															 $contents_value["campaign_status"],
															 $device_id,
															 $ad_type_id
															 );

								}
							}
						}
					}
				}

				\File::delete($upload_file_path);

				//クライアント名取得
				$client_name = \Model_Mora_Client::get_client_name($this->data["client_id"]);
				if($client_name["client_name"]){
					$client_name["company_name"] = $client_name["company_name"]. "(" . $client_name["client_name"] .")";
				}
				//テンプレート名取得
				$template_info = \Model_Data_Falcon_Template::get_info($upload_template_id);
				if($template_info["template_name"]){
					$template_name = $template_info["template_name"] . "_";
				}else{
					$template_name = "_";
				}

				$filename = "【アップロード結果】Falcon_" . $setting_type_name . "【" . $client_name["company_name"] ."】" . $template_name . date("YmdHis") . ".csv";

				// 結果ファイルダウンロード用データをRedisに保存
				$redis = new Util_Common_Redis();

				$resdata_id = $this->data["client_id"].Session::get('user_id_sem').date("hms");

				$expire   = 60; // 1 min
				$register = array(
					$resdata_id => array(
						"filename" => $filename,
						"contents" => $file_contents,
					)
				);
				$res = $redis->set_redis_hash(FalconConst::$redis_key_list['cp'], $register, true, $expire);

				return $this->response(array($resdata_id), 200);
			}
		}
	}

	/**
	 * 設定結果CSVファイルをダウンロード
	 */
	public function action_downloadresult($resdata_id) {
		$redis = new Util_Common_Redis();

		$res = $redis->get_redis_hash(FalconConst::$redis_key_list['cp'], $resdata_id);
		if (empty($res[0])) return false;

		$this->format = "csv";
		$response = $this->response($res[0]["contents"]);
		$response->set_header("Content-Disposition", "attachment; filename=" . $res[0]["filename"]);

		return $response;
	}

	/**
	 * 画面表示用データを取得
	 */
	public function post_data() {
		$campaign_list = array();

		//指定アカウント毎にループ
		foreach ($this->data["account_id_list"] as $account_id) {

			//media_id, account_id 分割
			list($tmp_media_id, $tmp_account_id) = explode("//",  $account_id);

			//指定アカウント情報取得
			$account = \Model_Mora_Account::get_for_account($tmp_account_id);

			if($this->data["setting_type"] == "exclusion"){
				//指定アカウントのキャンペーン除外情報取得
				$campaign_exclusion_list = \Model_Data_Falcon_CampaignExclusionSetting::get($this->data["template_id"], $tmp_media_id, $tmp_account_id);
				$template_info = \Model_Data_Falcon_Template::get_info($this->data["template_id"]);
				$this->view->set('template_info', $template_info);
				$setting_type_name = "キャンペーンフィルタ設定";

			}else{
				//指定アカウントのキャンペーン属性情報取得
				$campaign_attribute_list = \Model_Data_Falcon_CampaignAttributeSetting::get($tmp_media_id, $tmp_account_id);
				$setting_type_name = "キャンペーン属性設定";
			}

			//指定アカウントのキャンペーン情報取得
			$campaign_info = \Model_Structure_Falcon_Campaign::get($tmp_media_id, $tmp_account_id);

			//アカウント情報をキャンペーン情報に付加
			foreach ($campaign_info as $campaign) {

				//初期化
				$target_flg = false;

				//キー
				$key = $campaign["media_id"] . "::" .$campaign["account_id"] . "::" . $campaign["campaign_id"];

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

				//ステータスを共通のものに変換
				if (FalconConst::$falcon_status_replace_list[strtolower($campaign["campaign_status"])]) {
					$campaign_status = FalconConst::$falcon_status_replace_list[strtolower($campaign["campaign_status"])];
				}else{
					$campaign_status = null;
				}

				//除外設定
				if($this->data["setting_type"] == "exclusion"){

					if(isset($campaign_exclusion_list[$key]["exclusion_flg"])){
						if($campaign_exclusion_list[$key]["exclusion_flg"] == 0){
							$exclusion_flg = "0";
							if($this->data["export_exclusion_type"]==1){
								$target_flg = false;
							}
						}else{
							$exclusion_flg = "1";
							if($this->data["export_exclusion_type"]==0){
								$target_flg = false;
							}
						}
						$add_user     = $campaign_exclusion_list[$key]["user_name"];
						$add_at       = $campaign_exclusion_list[$key]["datetime"];

						$add_at = str_replace('-', '.', $add_at);
					}else{
						$exclusion_flg = "";
						$add_user     = "--";
						$add_at       = "--";
						if($this->data["export_exclusion_type"]!=2){
							$target_flg = false;
						}
					}

					if($target_flg){
						$campaign_list[] = array('media_id' => $tmp_media_id,
												 'media_name' => FalconConst::$falcon_media_list[$tmp_media_id],
												 'account_id' => $tmp_account_id,
												 'account_name' => $account["account_name"],
												 'campaign_id' => $campaign["campaign_id"],
												 'campaign_name' => $campaign["campaign_name"],
												 'status' => $campaign_status,
												 'exclusion_flg' => $exclusion_flg,
												 'add_user' => $add_user,
												 'add_at' => $add_at
												 );
					}

				//属性設定
				}else{

					if(isset($campaign_attribute_list[$key])){
						$device_id    = $campaign_attribute_list[$key]["device_id"];
						$ad_type_id   = $campaign_attribute_list[$key]["ad_type_id"];
						$add_user     = $campaign_attribute_list[$key]["user_name"];
						$add_at       = $campaign_attribute_list[$key]["datetime"];

						$add_at = str_replace('-', '.', $add_at);

						if($this->data["no_setting_flg"]){
							$target_flg = false;
						}
					}else{
						$device_id    = "";
						$ad_type_id   = "";
						$add_user     = "--";
						$add_at       = "--";
					}

					if($target_flg){
						$campaign_list[] = array('media_id' => $tmp_media_id,
												 'media_name' => FalconConst::$falcon_media_list[$tmp_media_id],
												 'account_id' => $tmp_account_id,
												 'account_name' => $account["account_name"],
												 'campaign_id' => $campaign["campaign_id"],
												 'campaign_name' => $campaign["campaign_name"],
												 'status' => $campaign_status,
												 'device_id' => $device_id,
												 'ad_type_id' => $ad_type_id,
												 'add_user' => $add_user,
												 'add_at' => $add_at
												 );
					}
				}
			}
		}

		$res_arr = (count($campaign_list) > FALCON_CPSETTING_DISPLAY_LIMIT) ? array(false, FALCON_CPSETTING_DISPLAY_LIMIT) : $campaign_list;

		return $this->response($res_arr);
	}

	/**
	 * 画面から更新処理
	 */
	public function post_update() {
		$update_campaign_exclusion_memo = '';  // CP設定でのメモ追加はいったんオフる

		$res = false;
		if(isset($this->data["update_list"])){
			$res = \Util_Falcon_CampaignSetting::update($this->data["client_id"], $this->data["setting_type"], $this->data["update_list"], $this->data["template_id"], $update_campaign_exclusion_memo);
		}
		return $this->response(array($res), 200);
	}
}
