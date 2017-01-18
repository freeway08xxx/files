<?php

class Controller_Wabisabid_MultiDevice extends Controller_Wabisabid_Base {

	############################################################################
	## 
	############################################################################}
	public function action_index() {

		$this->view->set_filename("wabisabid/multidevice/index");
		$this->response($this->view);
	}
}
