<?php

class Model_Data_CampaignGoogleStops extends \Model
{
	protected static $_table_name = 't_campaign_googlestops';

  public static function get($client_id, $account_id){
    $query = DB::select()->from(self::$_table_name)
            ->where('client_id', $client_id)
            ->where('account_id', $account_id);
    return $query->execute()->as_array();
  }

  public static function ins($client_id, $account_id, $campaign_id, $name) {
    $query = DB::insert(self::$_table_name, array('client_id', 'account_id', 'campaign_id', 'campaign_name'));
    $query->values(array($client_id, $account_id, $campaign_id, $name));
    return $query->execute();
  }

  public static function del($client_id, $account_id){
    $query = DB::delete(self::$_table_name);
    $query->where('client_id', $client_id)
          ->where('account_id', $account_id);
    return $query->execute();
  }
}
