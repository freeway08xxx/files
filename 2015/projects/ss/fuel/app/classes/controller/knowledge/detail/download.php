<?php

class Controller_Knowledge_Detail_Download extends Controller_Knowledge_Base
{

	/**
	 * 詳細更新 
	 * 
	 * @access public
	 * @return void
	 */
	public function action_index()
	{

		$this->file_id 	= $this->get_request_paramater('file_id');
		$file = Model_Mora_Knowledge_File::find_by_id($this->file_id);

		//ファイルとロールのチェック
		if (empty($file) || !Util_Knowledge_Common::is_file_allow_authority($this->role_id, $this->file_id)) 
		{
			//ファイルがない、もしくは権限がない
			Session::set_flash('errors', array(KNOWLEDGE_ERROR_FILE_ACCESS_DENIED));
			Response::redirect(Input::referrer());
		}
		
		$extension = pathinfo($file['filename'], PATHINFO_EXTENSION);
		$file_path = UPLOAD_KNOWLEDGE_FILES_PATH . '/' . $file['id'] . '.' . $extension;
		if ( !file_exists($file_path) )
		{
			//ファイルが存在しない
			Session::set_flash('errors', array(KNOWLEDGE_ERROR_FILE_ACCESS_DENIED));
			Response::redirect(Input::referrer());
		}

		//ダウンロード情報を登録する
		Model_Mora_Knowledge_File_History_Download::insert_download_history($file['id'], $this->user_id);

		$res = Response::forge();
		$res->set_header('Cache-Control', 'public');
		$res->set_header('Pragma', 'public');
		$res->set_header('Content-Type', 'application/octet-stream');
		$res->set_header('Content-Disposition', "attachment; filename=\"" . mb_convert_encoding($file['filename'], "Shift_JIS") . "\"");
		$res->send(true);

		File::download($file_path, mb_convert_encoding($file['filename'], "Shift_JIS", "auto"));
	}

}
