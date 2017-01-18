<?php
class Model_Data_History extends \Model {

	## テーブル名定義
	protected static $_table_name = "t_history";

	############################################################################
	## 取得
	############################################################################
	public static function get($limit = null) {
		$SQL = DB::select(
			self::$_table_name.'.client_id',
			self::$_table_name.".account_id",
			self::$_table_name.".media_id",
			self::$_table_name.".content",
			self::$_table_name.".status",
			self::$_table_name.".reason",
			self::$_table_name.".created_user",
			self::$_table_name.".created_at",
			self::$_table_name.".updated_at",
			'mora.account.account_name',
			'mora.user.user_name'
		);
		$SQL->from(self::$_table_name)
			->join('mora.account', 'LEFT OUTER')->on(self::$_table_name.'.account_id', '=', 'mora.account.id')
			->join('mora.user', 'LEFT OUTER')->on(self::$_table_name.'.created_user', '=', 'mora.user.id');
			if (!is_null($limit)) $SQL->limit($limit);
		return $SQL->execute()->as_array();
	}


	############################################################################
	## 挿入
	############################################################################
	public static function ins($values) {

		$SQL = DB::insert(self::$_table_name, array_keys($values));
		$SQL->values(array_values($values));

		return $SQL->execute("searchsuite_report_admin");
	}

}
