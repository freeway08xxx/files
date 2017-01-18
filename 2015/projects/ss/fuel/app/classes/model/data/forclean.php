<?php

class Model_Data_ForClean extends \Model
{
	private $_table_name = '';
	private $_column = '';
	private $_sign = '';
	private $_value = '';
	private $_limit = 10000;
	private $_offset = 0;

  public function set_table($table){
    $this->_table_name = $table;
    return DBUtil::table_exists($this->_table_name);
  }

  public function set_column($column){
    $this->_column = $column;
    return DBUtil::field_exists($this->_table_name, array($this->_column));
  }

  public function set_sign($sign){
    $this->_sign = $sign;
  }

  public function set_value($value){
    $this->_value = $value;
  }

  public function set_limit($limit){
    $this->_limit = $limit;
  }

  public function set_offset($offset){
    $this->_offset = $offset;
  }

  public function cnt(){
    $query = DB::select(array("count(*)","count"))->from($this->_table_name);
    $query = $this->set_where($query);
    return $query->execute()->current();
  }

  public function get(){
    $query = DB::select()->from($this->_table_name);
    $query = $this->set_where($query);
    $query = $this->set_limiter($query);
    return $query->execute();
  }

  public function del(){
    $query = DB::delete($this->_table_name);
    $query = $this->set_where($query);
    return $query->execute();
  }

  private function set_where($query){
    if ($this->_column == 'updated_at') {
      $query->where($this->_column, $this->_sign, $this->_value);
      $query->or_where_open();
      $query->where('created_at', $this->_sign, $this->_value);
      $query->and_where($this->_column, '=', NULL);
      $query->or_where_close();
    } else {
      $query->where($this->_column, $this->_sign, $this->_value);
    }
    return $query;
  }

  private function set_limiter($query){
    $query->limit($this->_limit);
    $query->offset($this->_offset);
    return $query;
  }

}
