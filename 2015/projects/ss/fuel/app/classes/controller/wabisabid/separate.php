<?php

class Controller_Wabisabid_Separate extends Controller_Wabisabid_Base {

	############################################################################
	## 
	############################################################################}
	public function action_index() {

		$this->view->set_filename("wabisabid/separate/index");
		$this->response($this->view);
	}
}
