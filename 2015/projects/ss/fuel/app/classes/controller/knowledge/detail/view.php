<?php

class Controller_Knowledge_Detail_View extends Controller_Knowledge_Base {

	public function before()
	{
		parent::before();
	}

	/**
	 * action_content
	 *
	 * @access public
	 * @return void
	 */
	public function action_index()
	{
		$this->view->set_filename('knowledge/detail/view/index');
		return Response::forge($this->view);
	}
}
