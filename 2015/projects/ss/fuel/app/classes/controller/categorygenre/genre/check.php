<?php

require_once APPPATH."/const/categorygenre.php";
/**
 * カテゴリジャンルチェックコントローラ
 */
class Controller_CategoryGenre_Genre_Check extends Controller_Rest
{
	// loginユーザ権限チェック用URL
	public $access_url = "/sem/universe_fast/universe.php";

	//ジャンル名の重複チェック
	public function post_namecheck($client_id) {

		$category_genre_name = Input::post("category_genre_name");
		$genre_id = Input::post("genre_id");
		$check = \Model_Data_CategoryGenre::get_for_category_genre_name($client_id, $category_genre_name);

		if($check && $check["id"] != $genre_id){
			$error_text = 'カテゴリジャンル名「'.$category_genre_name.'」は既に使用されています';
		}else{
			$error_text = null;
		}
		return $this->response($error_text, 200);
	}
}
