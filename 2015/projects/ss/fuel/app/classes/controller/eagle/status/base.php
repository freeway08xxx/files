<?php

require_once APPPATH . "/const/adwords.php";
require_once APPPATH . "/const/yahoo.php";
require_once APPPATH . "/const/ydn.php";

################################################################################
#
# Title : ステータス変更用コントローラ基底クラス
#
#  2014/06/01  First Version
#
################################################################################

class Controller_Eagle_Status_Base extends Controller_Base {

	public function before() {
		$this->template = 'template_gb'; // parent::before() の前にオーバーライドが必要

		parent::before();

		if ( ! $this->is_restful()) {
			## ページタイトル
			$this->template->set_global('title', 'ステータス変更');

			## ページ固有CSS,JS
			$this->css = array(
				'vendor/multi-select.css',
				'eagle/status.css'
			);
			$this->js = array(
				'vendor/jquery.multi-select.js',
				'vendor/jquery.quicksearch.js',
				'eagle/status/main.js'
			);

			## サイドバーナビ
			$this->template->sidebar = View::forge('common/eagle_sidebar',array('admin_flg' => $this->admin_flg));
		}
	}

	## Controller_Hybrid 利用のため、必ず$response を返すこと
	public function after($response) {
		return parent::after($response);
	}
}
