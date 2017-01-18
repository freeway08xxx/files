<?php

class Controller_Knowledge_Detail_Edit extends Controller_Knowledge_Base {

	/**
	 * before
	 *
	 * @access public
	 */
	public function before()
	{
		return parent::before();
	}

	/**
	 * action_content
	 *
	 * @access public
	 */
	public function action_index()
	{
		$this->view->set_filename('knowledge/detail/edit/index');
		return Response::forge($this->view);
	}
}
