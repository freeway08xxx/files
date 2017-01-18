<?php

class Controller_Knowledge_List extends Controller_Knowledge_Base
{

	/**
	 * コンテンツ情報のロード
	 *
	 * @access public
	 * @return void
	 */
	public function action_index()
	{
		## View
		$this->view->set("view_form", View::forge('knowledge/list/form'));
		$this->view->set("view_list", View::forge('knowledge/list/file'));
		$this->view->set_filename('knowledge/list/index');
		return Response::forge($this->view);
	}
}
