<?php

require_once APPPATH."/const/share.php";
/**
 * Service Base Controller.
 */
class Controller_Competitor_Share_Base extends Controller_Base
{
  public function before() {
    parent::before();

    if ( ! $this->is_restful()) {
      ## AngularJS AppName
      ## 同名を public/assets/js/{yourfunc}/app.js のangular.module に記述してください
      $this->template->ngapp_name = 'dummy';

      ## ページ固有CSS, JS
      $this->css = array(
        'vendor/multi-select.css',
        'vendor/jquery.dataTables.css',
        'vendor/jquery-ui.min.css',
        'vendor/jquery.ui.theme.css',

        'competitor/share.css'
      );

      $this->js = array(
        // jQuery Page Lib
        'vendor/jquery-ui-1.10.4.custom.min.js',
        'vendor/select2.js',

        // dummy Angular App
        'common/ng/dummy.js',
      );

      ## ページタイトル横ナビゲーション 不要の場合は削除
      $this->content_nav = View::forge('competitor/share/nav');
    }
  }

  ## Controller_Hybrid 利用のため、必ず$response を返すこと
  public function after($response) {
    return parent::after($response);
  }
}
