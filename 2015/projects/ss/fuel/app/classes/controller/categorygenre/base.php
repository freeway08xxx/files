<?php

require_once APPPATH."/const/categorygenre.php";
/**
 * Service Base Controller.
 */
class Controller_CategoryGenre_Base extends Controller_Base
{
	public function before() {
		$this->template = 'template_gb'; // parent::before() の前にオーバーライドが必要

		parent::before();

		if ( ! $this->is_restful()) {
			## ページタイトル
			if (strpos(uri::current(), '/category/') !== false) {
				$this->template->set_global('title', 'カテゴリ管理');
			}
			if (strpos(uri::current(), '/genre/') !== false) {
				$this->template->set_global('title', 'カテゴリジャンル管理');
			}
			if (strpos(uri::current(), '/categorysetting/') !== false) {
				$this->template->set_global('title', 'カテゴリ設定');
			}

			## ページ固有CSS,JS
			$this->css = array(
				'vendor/multi-select.css',
				'vendor/jquery.dataTables.css',
				'categorygenre/main.css'
			);
			$this->js = array(
				'vendor/jquery.multi-select.js',
				'vendor/jquery.quicksearch.js',
				'vendor/jquery.dataTables.min.js'
			);
			if (strpos(uri::current(), '/category/') !== false) {
				$this->js[] = 'categorygenre/category/main.js';
			}
			if (strpos(uri::current(), '/genre/') !== false) {
				$this->js[] = 'categorygenre/genre/main.js';
			}
			if (strpos(uri::current(), '/categorysetting/') !== false) {
				$this->js[] = 'categorygenre/categorysetting/main.js';
			}

			## サイドバーナビ
			$this->template->sidebar = View::forge('common/categorygenre_sidebar',array('admin_flg' => $this->admin_flg));
		}
	}

	## Controller_Hybrid 利用のため、必ず$response を返すこと
	public function after($response) {
		return parent::after($response);
	}
}
