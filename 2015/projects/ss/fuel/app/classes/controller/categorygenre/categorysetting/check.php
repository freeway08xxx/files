<?php

require_once APPPATH."/const/categorygenre.php";
/**
 * カテゴリジャンルチェックコントローラ
 */
class Controller_CategoryGenre_CategorySetting_Check extends Controller_Rest
{
	// loginユーザ権限チェック用URL
	public $access_url = "/sem/universe_fast/universe.php";

	//設定シートアップロードファイルチェック
	public function post_filecheck($client_id){

		$res_text = null;
		$genre_id = Input::post("update_genre_id");

		// 変更履歴登録
		$values = array("client_id"   => $client_id,
						"category_genre_id" => $genre_id,
						"action_type_id"  => 2,
						"status_id" => 0
						);
		$ret = \Model_Data_CategoryHistory::ins($values);
		$res_text .= $ret[0].",";

		$file_name = 'category_setting_'.$client_id.'_'.$genre_id.'_'.date("YmdHis").'.csv';

		Upload::process(
			array(
				'path' => CATEGORY_GENRE_SETTING_UPLOAD_DIR,
				'new_name' => $file_name,
				'auto_rename' => false,
				'overwrite' => true,
				'ext_whitelist' => array('csv'),
			)
		);

		if(Upload::is_valid()){
			Upload::save(0);
			$res_text .= $file_name.",";
		}else{
			return false;
		}

		return $this->response($res_text, 200);
	}

	//設定画面の表示件数チェック
	public function post_countcheck($client_id){

		$element_type_id = Input::post("select_element_type_id");
		$account_id_list = Input::post("account_id_list");
		$total_count = 0;

		//large_companyかどうか
		$company_id = \Model_Mora_Client::get_large_company($client_id);

		if($element_type_id != 1){
			foreach ($account_id_list as $account_id) {
				list($tmp_media_id, $tmp_account_id) = explode("//",  $account_id);

				$value_list[$tmp_media_id]["account_id_list"][$tmp_account_id] = $tmp_account_id;
				$value_list[$tmp_media_id]["table_name"] = Util_Common_Table::get_structure_table_name($tmp_media_id, CategoryGenreConst::$category_genre_element_list[$element_type_id], $company_id);
			}

			foreach ($value_list as $media_id => $values){
				if($element_type_id == 2){
					$count = Model_Structure_Campaign::get_count($values["table_name"], $media_id, $values["account_id_list"]);
				}elseif($element_type_id == 3){
					$count = Model_Structure_AdGroup::get_count($values["table_name"], $media_id, $values["account_id_list"]);
				}elseif($element_type_id == 4){
					$count = Model_Structure_Keyword::get_count($values["table_name"], $media_id, $values["account_id_list"]);
				}
				$total_count += $count["count"];
				if($total_count > CATEGORY_GENRE_STRUCTURE_MAX_COUNT){
					break;
				}
			}
		}

		if($total_count > CATEGORY_GENRE_STRUCTURE_MAX_COUNT){
			$error_text = "掲載内容が最大件数(".CATEGORY_GENRE_STRUCTURE_MAX_COUNT."件)を超えています。条件を絞り込んで再度お試しください";
			return $this->response($error_text, 200);
		}else{
			$fal = "false";
			return $this->response($fal, 200);
		}
	}

}
