<?php
class Model_Data_QuickManage_Aim extends \Model {

	## テーブル名定義
	protected static $_table_name = "t_quickmanage_aim_setting";

	/*========================================================================*/
	/* 一覧取得
	/*========================================================================*/
	public static function get_list($target_ym = null, $limit = null) {

		$client_table  = "mora.client";
		$company_table = "mora.company";

		$SQL = DB::select("Y_S.client_id",
						  "Y_S.target_ym",
						  array("Y_S.aim_budget","Y_S_aim_budget"),
						  array("Y_D.aim_budget","Y_D_aim_budget"),
						  array("G_S.aim_budget","G_S_aim_budget"),
						  array("G_D.aim_budget","G_D_aim_budget"),
						  array("D2C.aim_budget","D2C_aim_budget"),
						  DB::expr("if( LENGTH( client_name ) > 0, concat( company_name, '//', client_name ) , company_name ) AS client_name"));
		$SQL->from(array(self::$_table_name, "Y_S"));
		$SQL->join($client_table, "INNER")
			->on("Y_S.client_id", "=", $client_table . ".id");
		$SQL->join($company_table, "INNER")
			->on($client_table . ".company_id", "=", $company_table . ".id");
		$SQL->join(array(self::$_table_name, "Y_D"), "INNER")
			->on("Y_S.client_id", "=", "Y_D.client_id")
			->on("Y_S.target_ym", "=", "Y_D.target_ym");
		$SQL->join(array(self::$_table_name, "G_S"), "INNER")
			->on("Y_S.client_id", "=", "G_S.client_id")
			->on("Y_S.target_ym", "=", "G_S.target_ym");
		$SQL->join(array(self::$_table_name, "G_D"), "INNER")
			->on("Y_S.client_id", "=", "G_D.client_id")
			->on("Y_S.target_ym", "=", "G_D.target_ym");
		$SQL->join(array(self::$_table_name, "D2C"), "INNER")
			->on("Y_S.client_id", "=", "D2C.client_id")
			->on("Y_S.target_ym", "=", "D2C.target_ym");
		if (!is_null($target_ym)) {
			 $SQL->where("Y_S.target_ym", $target_ym);
		}
		$SQL->where("Y_S.media_id", MEDIA_ID_YAHOO)
			->where("Y_S.product", "Search Network");
		$SQL->where("Y_D.media_id", MEDIA_ID_IM)
			->where("Y_D.product", "Display Network");
		$SQL->where("G_S.media_id", MEDIA_ID_GOOGLE)
			->where("G_S.product", "Search Network");
		$SQL->where("G_D.media_id", MEDIA_ID_GOOGLE)
			->where("G_D.product", "Display Network");
		$SQL->where("D2C.media_id", MEDIA_ID_D2C)
			->where("D2C.product", "--");
		$SQL->group_by("Y_S.client_id", "Y_S.target_ym", "Y_S_aim_budget", "Y_D_aim_budget", "G_S_aim_budget", "G_D_aim_budget", "D2C_aim_budget");
		$SQL->order_by("Y_S.client_id");
		if (!is_null($limit)) $SQL->limit($limit);

		return $SQL->execute("readonly")->as_array();
	}

	/*========================================================================*/
	/* 合計一覧取得
	/*========================================================================*/
	public static function get_sum_list($target_ym = null, $limit = null) {

		$SQL = DB::select("client_id", DB::expr("sum(aim_budget) as aim_budget"));
		$SQL->from(self::$_table_name);
		if (!is_null($target_ym)) {
			 $SQL->where("target_ym", $target_ym);
		}
		$SQL->group_by("client_id");
		if (!is_null($limit)) $SQL->limit($limit);

		return $SQL->execute("readonly")->as_array("client_id");
	}

	/*========================================================================*/
	/* 挿入
	/*========================================================================*/
	public static function ins($values) {

		$SQL = DB::insert(self::$_table_name, array_keys($values[0]));

		foreach ($values as $value) {
			$SQL->values(array_values($value));
		}

		$SQL->set_duplicate(array("aim_budget = VALUES(aim_budget)"));

		return $SQL->execute("administrator");
	}


	/*========================================================================*/
	/* 合計一覧取得（サマリオプション対応）
	/*========================================================================*/
	public static function get_sum_sumopt($client_id_list = array(), $media_id = null, $product = null, $target_ym = null, $limit = null) {

		$SQL = DB::select(DB::expr("case when sum(aim_budget) is null then 0 else sum(aim_budget) end as aim_budget"));
		$SQL->from(self::$_table_name);
		if (!is_null($target_ym)) $SQL->where("target_ym", $target_ym);
		$SQL->where("client_id", "in", $client_id_list);
		if (!is_null($media_id)) $SQL->where("media_id", $media_id);
		if (!is_null($product)) $SQL->where("product", $product);
		if (!is_null($limit)) $SQL->limit($limit);

		return $SQL->execute("readonly")->current();
	}
}
