<?php

class Controller_Eagle_Cpc_Base extends Controller_Base {

	public function before() {
		$this->template = 'template_gb'; // parent::before() の前にオーバーライドが必要

		parent::before();

		if ( ! $this->is_restful()) {
			## ページタイトル
			$this->template->set_global('title', 'CPC変更');

			## ページ固有CSS,JS
			$this->css = array(
				'vendor/multi-select.css',
				'eagle/cpc.css'
			);
			$this->js = array(
				'vendor/jquery.multi-select.js',
				'vendor/jquery.quicksearch.js',
				'eagle/cpc/cpc-main.js',
				'eagle/cpc/cpc-validation.js',
				'eagle/cpc/cpc-common.js',
				'eagle/eagle-common.js'
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
