<?php

class Controller_Knowledge extends Controller_Knowledge_Base
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
		$this->view->set_filename('knowledge/index');	
		//必要テンプレート
		$this->view->set_safe('section_navi', View::forge('knowledge/section'));
	}
}
