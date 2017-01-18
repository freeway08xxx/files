<?php

class Controller_AccountStructure extends Controller_AccountStructure_Base
{
	
	/**
	 * action_index
	 * 
	 * @access public
	 * @return void
	 */
	public function action_index() {
		//view へ出力
		$this->view->set_filename('accountstructure/index');
	}
}
