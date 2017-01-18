<?php

/**
 * フォームテンプレート画面 コントローラ
 * ※AngularJSから $routeProvider 経由でAJAX呼び出し
 *
 * @return HTML_View
 */
class Controller_Basic_Form extends Controller_Basic_Base
{
	/**
	 * 基本画面のロード
	 *
	 * @access public
	 * @return void
	 */
	public function action_index() {

		$this->view->set_filename('basic/form/index');
		$this->view->set_safe("form_main",
			View::forge('basic/form/main')
				->set_safe("parts_text", View::forge('basic/form/parts/text'))
				->set_safe("parts_select", View::forge('basic/form/parts/select'))
				->set_safe("parts_checkbox", View::forge('basic/form/parts/checkbox'))
				->set_safe("parts_radio", View::forge('basic/form/parts/radio'))
				->set_safe("parts_file", View::forge('basic/form/parts/file'))
		);
		$this->view->set_safe("form_sub1", View::forge('basic/form/sub1'));
		$this->response($this->view);
	}

	/**
	 * 画面の登録
	 *
	 * @access public
	 * @return int insert_id
	 */
	public function post_save() {
		//var_dump($_FILES);
		$basic_id = Input::post("id");

		## formdateはjsonにシリアライズされたtextが来る
		$form_json = Input::post("form");
		$form = json_decode($form_json,true);


		$basic = Model_Data_Basic::find_by_id($basic_id);
		## ファイルのアップロード前準備
		if (!empty($_FILES)){
			$file_name = $this->set_upload_files("file");
		}else{
			$file_name = $basic['file_name'];
		}



		## 登録処理を行う
		if (empty($basic_id)) {
			## サンプルなので送信情報はすべてjsonにして1レコードに入れているが
			## 実際にはそれぞれ処理してください
			$insert = Model_Data_Basic::ins($form_json,$file_name);
			$basic_id = $insert[0];
		}else{
			$update = Model_Data_Basic::upd($basic_id, $form_json, $file_name);
		}

		## ファイルのアップロード開始
		if (!empty($_FILES))
		{
			$this->save_upload_files($basic_id);
		}

		return $this->response( array(
			'basic_id' => $basic_id, ## 登録Id
		));

	}

	/**
	 * 画面の取得
	 *
	 * @param mixed $id
	 * @access public
	 * @return void
	 */
	public function get_data() {
		$basic_id = Input::get("id");

		$basic = null;
		if(!empty($basic_id)){
			//basic詳細
			$basic = Model_Data_Basic::find_by_id($basic_id);
			$basic['form'] = json_decode($basic['text'],true);
		}

		//マスターデータ
		$masterData['subject'] = array(
			array('id' => 1, 'name' => '算数'),
			array('id' => 2, 'name' => '英語'),
			array('id' => 3, 'name' => '国語'),
			array('id' => 4, 'name' => '社会'),
			array('id' => 5, 'name' => '理科'),
		);
		$masterData['sports'] = array(
			array('id' => 1, 'name' => '野球'),
			array('id' => 2, 'name' => 'サッカー'),
			array('id' => 3, 'name' => 'バスケットボール'),
		);

		return $this->response( array(
			'basic' => $basic,
			'master' => $masterData
		));
	}

	/**
	 * set_upload_files
	 *
	 * @param mixed $label_name
	 * @access private
	 * @return void
	 */
	private function set_upload_files($label_name)
	{
		$file_name = "";
		// アップロード基本プロセス実行
		Upload::process(array(
            'path' => UPLOAD_FILES_DIR . "/new/basic/",
            'auto_rename' => false,
			'overwrite' => true,
		));

		if ($files = Upload::get_files())
		{
			foreach ($files as $file)
			{
				if ($file['field'] == $label_name)
				{
					$file_name = $file['name'];
					break;
				}
			}
		}

		return $file_name;
	}


	/**
	 * ファイルアップロードを行う
	 *
	 * @access private
	 * @return void
	 */
	private function save_upload_files($basic_id)
	{
		// 検証
		$this->basic_id = $basic_id;
		if(Upload::is_valid()) {
			//クロージャを利用してコールバックの前に登録する
			Upload::register('before',function($file)
			{
				//もしアップロードファイルにエラーがなければ
				if ($file['error']==Upload::UPLOAD_ERR_OK)
				{
					//ファイル保存はfile_id.拡張子の形式で保存
					$file['filename'] = $this->basic_id . '.' . $file['extension'];
				}
			});

			Upload::save();
			// エラー有り
			foreach (Upload::get_errors() as $file)
			{
				Session::set_flash('errors', array($file['errors'][0]['message']));
				Response::redirect(Input::referrer());
			}
		}


		return true;
	}
}
