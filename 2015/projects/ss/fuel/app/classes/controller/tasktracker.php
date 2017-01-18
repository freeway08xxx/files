<?php

class Controller_Tasktracker extends Controller_Tasktracker_Base
{
	
	/**
	 * action_index
	 * 
	 * @access public
	 * @return void
	 */
	public function action_index()
	{
		//view へ出力
		$this->view->set_filename('tasktracker/index');	
	}
}
