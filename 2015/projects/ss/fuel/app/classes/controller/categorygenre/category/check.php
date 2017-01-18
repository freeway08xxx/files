<?php

require_once APPPATH."/const/categorygenre.php";
/**
 * カテゴリジャンルチェックコントローラ
 */
class Controller_CategoryGenre_Category_Check extends Controller_Rest
{
	// loginユーザ権限チェック用URL
	public $access_url = "/sem/universe_fast/universe.php";

	//カテゴリ名の重複チェック
	public function post_namecheck($client_id) {

		$category_name = Input::post("category_name");
		$category_elem = Input::post("category_elem");
		$genre_id = Input::post("genre_id");
		$category_id = Input::post("category_id");
		$check = \Model_Data_Category::get_for_category_name($category_elem, $client_id, $genre_id, $category_name);

		if($check && $check["id"] != $category_id){
			$error_text = 'カテゴリ名「'.$category_name.'」は既に使用されています';
		}else{
			$error_text = null;
		}
		return $this->response($error_text, 200);
	}

	//カテゴリ名の重複チェック(一括登録)
	public function post_namecheckbulk($client_id) {

		$update_flg = Input::post("update_flg");
		$genre_id = Input::post("genre_id");
		$category_elem = Input::post("category_elem");
		$error_text = null;
		$category_name_list_tmp = Input::post("category_name_list");

		//新規登録
		if(!$update_flg){

			//改行コードを統一
			$category_name_list_tmp = str_replace(array("\r\n","\r"), "\n", $category_name_list_tmp);
			//単語毎に分割
			$category_name_list = explode("\n", $category_name_list_tmp);

			$count = 1;
			foreach ($category_name_list as $name){
				$check = \Model_Data_Category::get_for_category_name($category_elem, $client_id, $genre_id, $name);

				if($check){
					$error_text .= $count.'行目：カテゴリ名「'.$name.'」は既に使用されています<br>';
				}
				$count++;
			}

		//一括更新
		}else{

			foreach ($category_name_list_tmp as $key => $value){
				$check = \Model_Data_Category::get_for_category_name($category_elem, $client_id, $genre_id, $value["category_name"]);

				if($check && $check["id"]!=$value["category_id"]){
					$error_text .= 'カテゴリ名「'.$value["category_name"].'」は既に使用されています<br>';
				}
			}
		}

		return $this->response($error_text, 200);
	}
}
