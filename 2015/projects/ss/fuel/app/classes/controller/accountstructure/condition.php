<?php
class Controller_AccountStructure_Condition extends Controller_AccountStructure_Base {

	/*========================================================================*/
	/* 前提共通処理　　
	/*========================================================================*/
	public function before() {

		## super
		parent::before();

	}

	/*========================================================================*/
	/* 条件設定画面出力
	/*========================================================================*/
	public function action_index() {
			
		// アカウント条件画面
		$HMVC_account 		= Request::forge("accountstructure/condition/account", false)->execute();
		// 検索条件入力画面
		$HMVC_filter 		= Request::forge("accountstructure/condition/filter", false)->execute();
		
		## View
		$this->view->set_safe("HMVC_account",		$HMVC_account);
		$this->view->set_safe("HMVC_filter",		$HMVC_filter);
		$this->view->set_filename("accountstructure/condition/index");
		
		## angular $routeProvider 経由で出力
		$this->response($this->view);
	}
	
	/*========================================================================*/
	/* アカウント条件設定画面
	/*========================================================================*/
	public function action_account() {
		if (Request::is_hmvc()) {
			
	        $DB_client_list = Model_Mora_Client::get_for_user();
	        $this->view->set("DB_client_list", $DB_client_list);

	        // 取得可能項目
			$campaign 			= View::forge("accountstructure/list/campaign");
			$adgroup 			= View::forge("accountstructure/list/adgroup");
			$kw 				= View::forge("accountstructure/list/kw");
			$negative_kw 		= View::forge("accountstructure/list/negativekw");
			$ad 				= View::forge("accountstructure/list/ad");
			$target_area 		= View::forge("accountstructure/list/target/area");
			$target_schedule 	= View::forge("accountstructure/list/target/schedule");
			$target_gender 		= View::forge("accountstructure/list/target/gender");	
			$target_age 		= View::forge("accountstructure/list/target/age");
			$target_userlist 	= View::forge("accountstructure/list/target/userlist");
			$target_placement 	= View::forge("accountstructure/list/target/placement");
        	
			## View
			$this->view->set_safe("campaign",			$campaign);
			$this->view->set_safe("adgroup",			$adgroup);
			$this->view->set_safe("kw",					$kw);
			$this->view->set_safe("negative_kw",		$negative_kw);
			$this->view->set_safe("ad",					$ad);
			$this->view->set_safe("target_area",		$target_area);
			$this->view->set_safe("target_schedule",	$target_schedule);
			$this->view->set_safe("target_gender",		$target_gender);
			$this->view->set_safe("target_age",			$target_age);
			$this->view->set_safe("target_userlist",	$target_userlist);
			$this->view->set_safe("target_placement",	$target_placement);
			$this->view->set_filename("accountstructure/condition/account");
			$this->response($this->view);
		}
	}

	/*========================================================================*/
	/* 検索条件入力画面
	/*========================================================================*/
	public function action_filter() {
		if (Request::is_hmvc()) {
			
			$DB_client_list = Model_Mora_Client::get_for_user();
			$this->view->set("DB_client_list", $DB_client_list);
			
			## View
			$this->view->set_filename("accountstructure/condition/filter");
			$this->response($this->view);
		}
	}
}
