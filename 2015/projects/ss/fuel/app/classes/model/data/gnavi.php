<?php

/**
 * Model_Data_Gnav
 *
 * @package
 * @version $id$
 * @copyright 2006-2009 FanIQ
 * @author kevin olson <acidjazz@gmail.com>
 * @license PHP Version 5.2 {@link http://www.php.net/license/}
 */
class Model_Data_Gnavi extends \Model
{
	protected static $_table_name = 'm_gnavi';

	public static function get($role=null){
		$navi   = array();
		$result = array();

		## Redisがあるならそっちを利用
		$redis = new Util_Common_Redis();
		$navi   = $redis->get_redis_hash(REDIS_KEY_GNAVI.'_'.$role);

		## Redisがない場合はDBから
		if (!$navi) {
			## DBから取得してRedisに保存して返す
			$query = DB::select()->from(self::$_table_name)
				->where('delete_flg',0)
				->order_by('category_id');
			$nav_master = $query->execute()->as_array();

			foreach($nav_master as $i => $arr){
				## roleチェック
				if(!empty($role) && !empty($arr['role'])){
					if(!in_array($role, explode(",", $arr['role']))){
						continue;
					}
				}
				$navi[$i] = !empty($navi[$arr['id']]) ? array_merge($navi[$arr['id']],$arr) : $arr;
			}
			$redis->set_redis_hash(REDIS_KEY_GNAVI.'_'.$role, $navi);
		}
		foreach ($navi as $i => $value) {
			if(!empty($GLOBALS["gnavi"]["category"][$value['category_id']])){
				$category_name = $GLOBALS["gnavi"]["category"][$value['category_id']];
				$group_name    = !empty($GLOBALS["gnavi"]["sub"][$category_name]) ? $GLOBALS["gnavi"]["sub"][$category_name][$value['group_id']] : "sub";
				$result["gnavi"][$category_name][$group_name][$value["priority"]]["name"]  = $value["name"];
				$result["gnavi"][$category_name][$group_name][$value["priority"]]["path"]  = $value["path"];
				$result["gnavi"][$category_name][$group_name][$value["priority"]]["label"] = $value["label"];
				ksort($result["gnavi"][$category_name][$group_name]);
			}
		}
		return $result;
	}
}




