<?php

class Controller_Knowledge_Detail_Update extends Controller_Knowledge_Base
{

	/**
	 * 詳細更新 
	 * 
	 * @access public
	 * @return void
	 */
	public function action_index()
	{

		//validate
		$this->set_valid_params(array(
			'title',
			'freetext_for_search',
			'search_active_flg',
			'file_purpose',
			'file_role'
		));
		$val = Validation::forge();
		if (!$this->run_valid_params($val))
		{
			$errors = array();
			foreach($val->error() as $error)
			{
				$errors[] = $error->get_message(VALIDATION_ERROR_ILLEGAL);
			}
			Session::set_flash('errors', $errors);
			Response::redirect(Input::referrer());
		}


		$this->type_section 	= $this->get_request_paramater('type_section');
		$this->file_id 			= $this->get_request_paramater('file_id');
		$this->title 			= $this->get_request_paramater('title');
		$this->description		= $this->get_request_paramater('description');
		$this->freetext_for_search = $this->get_request_paramater('freetext_for_search');
		$this->search_active_flg = $this->get_request_paramater('search_active_flg');
		$this->file_purpose 	= $this->get_request_paramater('file_purpose');
		$this->file_role 		= $this->get_request_paramater('file_role');
		$this->comment 			= $this->get_request_paramater('comment');

		//アクセス権限があるか
		$is_admin_view = Util_Knowledge_Common::is_admin_by_section($this->role_id, $this->type_section, Session::get('bureau_id'),Session::get('work_group_id'),Session::get('user_mail_address'));
		if (!$is_admin_view){
			Session::set_flash('errors', array("権限がありません"));
			Response::redirect(Input::referrer());
		}

		if (!empty($this->file_id))
		{
			//ファイル情報取得
			$file = Model_Mora_Knowledge_File::find_by_id($this->file_id);
			if (empty($file) || !Util_Knowledge_Common::is_file_allow_authority($this->role_id, $this->file_id))
			{
				Session::set_flash('errors', array(KNOWLEDGE_ERROR_FILE_ACCESS_DENIED));
				Response::redirect(Input::referrer());
			}

			//古いカテゴリとのdiffで追加と削除を行う
			$pre_purpose = Arr::pluck(Model_Mora_Knowledge_File_Purpose::find_by_file_id($this->file_id),'purpose_id');
			$add_purpose 		= array_diff($this->file_purpose, $pre_purpose);
			$delete_purpose 	= array_diff($pre_purpose, $this->file_purpose);
			$this->file_purpose = $add_purpose;

			//古い権限と新しい権限取得
			$pre_file_role = Arr::pluck(Model_Mora_Knowledge_File_Role::find_by_id($this->file_id),'role_id');
			$add_file_role 		= array_diff($this->file_role, $pre_file_role);
			$delete_file_role 	= array_diff($pre_file_role, $this->file_role);
			$this->file_role = $add_file_role;
		}

		if (!empty($_FILES)){
			//ファイルのアップロードを行う
			$this->filename = $this->set_upload_files("knowledge_file");
		}else{
			$this->filename = $file['filename'];
		}

		if (empty($this->file_id) and $this->filename == "")
		{
			//新規登録でファイル情報がない場合はエラー
			Session::set_flash('errors', array(KNOWLEDGE_ERROR_VALIDATION_FILE));
			Response::redirect(Input::referrer());
		}

		//freetext_for_searchはカンマで結合
		if(is_array($this->freetext_for_search)) {
			$this->freetext_for_search = implode(",", $this->freetext_for_search);
		}

		/**
		 * トランザクション開始 
		 */

		$db = Database_Connection::instance();
		$db->start_transaction();
		try 
		{
			if (empty($this->file_id))
			{
				//ファイル情報追加
				$insert_result = Model_Mora_Knowledge_File::insert_file($this->user_id, $this->title, $this->filename, $this->description, $this->freetext_for_search, $this->search_active_flg);
				$this->file_id = $insert_result[0];
				if (empty($this->file_id))
				{
					throw new HttpNotFoundException;
				}
			}
			else
			{
				//ファイル情報更新
				Model_Mora_Knowledge_File::update_file($this->file_id, $this->title, $this->filename, $this->description, $this->freetext_for_search, $this->search_active_flg);
			}


			//追加カテゴリ登録
			if ( ! empty($this->file_purpose))
			{
				foreach ($this->file_purpose as $purpose_id)
				{
					Model_Mora_Knowledge_File_Purpose::insert_file_con_purpose($this->file_id, $purpose_id);
				}
			}
			//削除カテゴリ
			if ( ! empty($delete_purpose))
			{
				foreach ($delete_purpose as $purpose_id)
				{
					Model_Mora_Knowledge_File_Purpose::delete_file_con_purpose($this->file_id, $purpose_id);
				}
			}

			//追加権限登録
			if ( ! empty($this->file_role))
			{
				foreach ($this->file_role as $role_id)
				{
					Model_Mora_Knowledge_File_Role::insert_file_con_role($this->file_id, $role_id);
				}
			}

			//削除権限情報
			if ( ! empty($delete_file_role))
			{
				foreach ($delete_file_role as $role_id)
				{
					Model_Mora_Knowledge_File_Role::delete_file_con_role($this->file_id, $role_id);
				}
			}

			//コメント情報追加
			if ( ! empty($this->comment))
			{
				$registered_comment_flg = 0;
				Model_Mora_Knowledge_File_History_Comment::insert_comment($this->file_id, $this->user_id, $this->comment, $registered_comment_flg );
			}

			$db->commit_transaction();
		}
		catch (\Exception $ex)
		{
			$db->rollback_transaction();
			Session::set_flash('errors', array(DB_ERROR_SAVE));
			Response::redirect(Input::referrer());
		}

		if (!empty($_FILES))
		{
			$this->filename = $this->save_upload_files();
		}

		Response::redirect('knowledge#/detail/view?file_id='.$this->file_id . '#type_section=' . $this->type_section);
	}

	/**
	 * コメントのみの更新 
	 * 
	 * @access public
	 * @return void
	 */
	public function action_comment()
	{
		//validate
		$this->set_valid_params(array(
			'file_id',
			'comment'
		));
		$val = Validation::forge();
		if ( ! $this->run_valid_params($val))
		{
			foreach($val->error() as $error)
			{
				$errors[] = $error->get_message(VALIDATION_ERROR_ILLEGAL);
			}
			Session::set_flash('errors', $errors);
			Response::redirect(Input::referrer());
		}

		$this->file_id 			= $this->get_request_paramater('file_id');
		$this->comment 			= $this->get_request_paramater('comment');
		$this->type_section		= $this->get_request_paramater('type_section');

		$registered_comment_flg = 0;
		Model_Mora_Knowledge_File_History_Comment::insert_comment($this->file_id, $this->user_id, $this->comment, $registered_comment_flg );

		Response::redirect('knowledge#/detail/view?file_id='.$this->file_id. '#type_section=' . $this->type_section);
	}

	/**
	 * 削除を行う 
	 * 
	 * @access public
	 * @return void
	 */
	public function action_delete()
	{
		//ファイル情報取得
		$this->file_id 	= $this->get_request_paramater('file_id');
		$this->type_section		= $this->get_request_paramater('type_section');

		$file = Model_Mora_Knowledge_File::find_by_id($this->file_id);
		if (empty($file) || !Util_Knowledge_Common::is_file_allow_authority($this->role_id, $this->file_id)) 
		{
			Session::set_flash('errors', array(KNOWLEDGE_ERROR_FILE_ACCESS_DENIED));
			Response::redirect(Input::referrer());
		}

		/**
		 * トランザクション開始 
		 */

		$db = Database_Connection::instance();
		$db->start_transaction();
		try 
		{

			//file情報の削除
			$delete_id = Model_Mora_Knowledge_File::delete_by_file_id($this->file_id);
			//purpose情報の削除
			Model_Mora_Knowledge_File_Purpose::delete_file_con_purpose_fll($this->file_id);
			//role情報の削除
			Model_Mora_Knowledge_File_Role::delete_file_con_role_all($this->file_id);

			$db->commit_transaction();
		}
		catch (\Exception $ex)
		{
			$db->rollback_transaction();
			Session::set_flash('errors', array(DB_ERROR_SAVE));
			Response::redirect(Input::referrer());
		}	

		Response::redirect('knowledge#/list?#type_section=' . $this->type_section);
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
            'path' => UPLOAD_KNOWLEDGE_FILES_PATH, 
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
	private function save_upload_files()
	{

		// 検証
		if(Upload::is_valid()) {
			//クロージャを利用してコールバックの前に登録する
			Upload::register('before',function($file)
			{ 
				//もしアップロードファイルにエラーがなければ
				if ($file['error']==Upload::UPLOAD_ERR_OK)
				{
					//ファイル保存はfile_id.拡張子の形式で保存
					$file['filename'] = $this->file_id . '.' . $file['extension'];
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
