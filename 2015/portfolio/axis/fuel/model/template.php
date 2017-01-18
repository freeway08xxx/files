<?php
class Model_Data_Axis_Template extends \Model {

	## テーブル名定義
	protected static $_table_name = "t_axis_template_setting";

	############################################################################
	## 取得
	############################################################################
	public static function get($id) {

		$SQL = DB::select();
		$SQL->from(self::$_table_name);
		$SQL->where("id", $id);

		return $SQL->execute("readonly")->current();
	}

	############################################################################
	## 一覧取得
	############################################################################
	public static function get_list($client_id, $limit = null) {

		$user_table    = "mora.user";

		$SQL = DB::select(self::$_table_name . ".*",
						  $user_table . ".user_name",
						  "case when " . self::$_table_name . ".updated_at is null then " . self::$_table_name . ".created_at else " . self::$_table_name . ".updated_at END as datetime");
		$SQL->from(self::$_table_name);
		$SQL->join($user_table, "LEFT")
			->on($user_table . ".id", "=", "case when " . self::$_table_name . ".updated_user is null then " . self::$_table_name . ".created_user else " . self::$_table_name . ".updated_user end");
		if (!empty($client_id)) $SQL->where(self::$_table_name . ".client_id", $client_id);
		$SQL->order_by(self::$_table_name . ".created_at", "desc");
		if (!is_null($limit)) $SQL->limit($limit);

		return $SQL->execute("readonly")->as_array();
	}

	############################################################################
	## 挿入
	############################################################################
	public static function ins($values) {

		$SQL = DB::insert(self::$_table_name, array_keys($values));
		$SQL->values(array_values($values));

		return $SQL->execute("administrator");
	}

	############################################################################
	## 更新
	############################################################################
	public static function upd($id, $values) {

		$SQL = DB::update(self::$_table_name);
		$SQL->set($values);
		$SQL->where("id", $id);

		return $SQL->execute("administrator");
	}

	############################################################################
	## 削除
	############################################################################
	public static function del($id) {

		$SQL = DB::delete(self::$_table_name);
		$SQL->where("id", $id);

		return $SQL->execute("administrator");
	}

	############################################################################
	## テンプレート登録・更新
	############################################################################
	public static function setting_template($id, $values, $account_values_list) {

		DB::start_transaction("administrator");
		## 更新
		if (!empty($id)) {
			self::upd($id, $values);
		## 挿入
		} else {
			$ret = self::ins($values);
			if (!isset($ret[0])) {
				DB::rollback_transaction("administrator");
				return FALSE;
			}
			$id = $ret[0];
		}
		\Model_Data_Axis_AccountSetting::del($id);
		\Model_Data_Axis_AccountSetting::ins_list($id, $account_values_list);
		DB::commit_transaction("administrator");

		return $id;
	}

	############################################################################
	## テンプレート削除
	############################################################################
	public static function delete_template($id) {

		DB::start_transaction("administrator");
		self::del($id);
		\Model_Data_Axis_AccountSetting::del($id);
		DB::commit_transaction("administrator");

		return TRUE;
	}
}
