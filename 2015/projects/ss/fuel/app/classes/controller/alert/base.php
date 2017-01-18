<?php

require_once APPPATH."/const/share.php";
/**
 * Service Base Controller.
 */
class Controller_Alert_Base extends Controller_Base
{
  public function before() {
    parent::before();

    if ( ! $this->is_restful()) {
      ## ページタイトル
      $this->template->set_global('title', '予算アラート');
      ## AngularJS AppName
      ## 同名を public/assets/js/{yourfunc}/app.js のangular.module に記述してください
      $this->template->ngapp_name = 'dummy';

      ## ページ固有CSS,JS
      $this->css = array(
        'vendor/multi-select.css',
        'alert/budget.css'
      );
      $this->js = array(
        'vendor/jquery.multi-select.js',

        // jQuery Page Lib
        'vendor/jquery-ui-1.10.4.custom.min.js',
        'vendor/select2.js',

        'common/pagination.js',

        // dummy Angular App
        'common/ng/dummy.js',

        'alert/budget/main.js'
      );

      ## ページタイトル横ナビゲーション 不要の場合は削除
      // $this->content_nav = View::forge('alert/nav');
    }
  }

  ## Controller_Hybrid 利用のため、必ず$response を返すこと
  public function after($response) {
    return parent::after($response);
  }
}
