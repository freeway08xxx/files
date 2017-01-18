<?php

require_once APPPATH."/const/share.php";
/**
 * Service Base Controller.
 */
class Controller_Customer_Base extends Controller_Base
{
  public function before() {
    parent::before();

    if ( ! $this->is_restful()) {
    }
  }

  ## Controller_Hybrid 利用のため、必ず$response を返すこと
  public function after($response) {
    return parent::after($response);
  }
}
