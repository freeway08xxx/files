<?php

/**
 * @return HTML_View
 */
class Controller_Basic_Table extends Controller_Basic_Base
{
	/**
	 * 基本画面のロード
	 *
	 * @access public
	 * @return void
	 */
	public function action_index()
	{
		$this->view->set_filename('basic/table/index');

		$this->response($this->view);
	}

	/**
	 * テーブル内情報を取得
	 *
	 * @access public
	 * @return void
	 */
	public function get_datas()
	{
		$basics = Model_Data_Basic::get();
		return $this->response( array(
			'basics' => $basics,
		));
	}

	public function get_file()
	{
		$basic_id = Input::get("id");
		if(empty($basic_id)){
			return false;
		}
		$basic = Model_Data_Basic::find_by_id($basic_id);

		$extension = pathinfo($basic['file_name'], PATHINFO_EXTENSION);
		$file_path = UPLOAD_FILES_DIR . '/new/basic/' . $basic_id . '.' . $extension;
		if ( !file_exists($file_path) )
		{
			//ファイルが存在しない
			return false;
		}

		$res = Response::forge();
		$res->set_header('Cache-Control', 'public');
		$res->set_header('Pragma', 'public');
		$res->set_header('Content-Type', 'application/octet-stream');
		$res->set_header('Content-Disposition', "attachment; filename=\"" . mb_convert_encoding($basic['file_name'], "Shift_JIS") . "\"");
		$res->send(true);

		File::download($file_path, mb_convert_encoding($basic['file_name'], "Shift_JIS", "auto"));
	}
}
