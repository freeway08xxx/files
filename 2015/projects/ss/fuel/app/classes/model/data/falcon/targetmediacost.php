<?php

class Model_Data_Falcon_TargetMediaCost extends \Model
{
	protected static $_table_name = 't_falcon_target_mediacost';

	public static function get($client_id) {
		$redis = new Util_Common_Redis();
		$res = $redis->get_target_media_cost();
		if (!$res) {
			$query = DB::select()
				->from(self::$_table_name)
				->where('client_id', $client_id);
			$res = array();
			$redis_res = array();
			foreach($query->execute() as $item) {
				if ($item['target_type'] === '1') { //メディア別
					$res[$item['client_id'].'//'.$item['media_id']] = (float) $item['media_cost'];
					$redis_res[] = array('key' => $item['client_id'].'//'.$item['media_id'], 'value' => $item['media_cost']);
				} elseif ($item['target_type'] === '2') { //アカウント別
					$res[$item['client_id'].'//'.$item['account_id']] = (float) $item['media_cost'];
					$redis_res[] = array('key' => $item['client_id'].'//'.$item['account_id'], 'value' => $item['media_cost']);
				}
			}
			$redis->set_redis_hash('target_media_cost', $redis_res);
		}
		return $res;
	}

	public static function get_list($client_id) {
		$query = DB::select(self::$_table_name.'.*','mora.user.user_name',
							'CASE WHEN '.self::$_table_name.'".updated_user" IS NULL THEN '.self::$_table_name.'."created_user" ELSE '.self::$_table_name.'."updated_user" END as "user_id"',
							'CASE WHEN '.self::$_table_name.'."updated_at" IS NULL THEN '.self::$_table_name.'."created_at" ELSE '.self::$_table_name.'."updated_at" END as "datetime"')
				->from(self::$_table_name);
		$query->join('mora.user', 'LEFT')->on('mora.user.id', '=', 'case when '.self::$_table_name.'."updated_user" is null then '.self::$_table_name.'."created_user" else '.self::$_table_name.'."updated_user" end');
		$query->where(self::$_table_name . ".client_id", "=", $client_id);

		return $query->execute()->as_array();
	}

	public static function ins($values){
		foreach ($values as $value) {
			if (!isset($query)) {
				$query = DB::insert(self::$_table_name, array_keys($value));
			}
			$query->values($value);
		}
		return $query->execute();
	}

	public static function upd($id, $media_cost){
		DB::start_transaction('administrator');
		$query = DB::update(self::$_table_name);
		$query->value('media_cost', $media_cost);
		$query->where('id', '=', $id);
		$query->execute();
		DB::commit_transaction('administrator');
		return true;
	}

	public static function del($id){
		DB::start_transaction('administrator');
		$query = DB::delete(self::$_table_name);
		$query->where('id', '=', $id);
		$query->execute();
		DB::commit_transaction('administrator');
		return true;
	}

	public static function check($target_type, $client_id, $media_id, $account_id=null){
		$query = DB::select()
			->from(self::$_table_name)
			->where("target_type", $target_type)
			->where("client_id", $client_id)
			->where("media_id", $media_id);
		if($target_type == 2 && $account_id){
			$query->where("account_id", $account_id);
		}

		return $query->execute()->current();
	}

}
