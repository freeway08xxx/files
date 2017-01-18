var modal = '#access_user';
var mode = 'client';
var service = 'competitor/share';
var paths = location.pathname.split(service);
var c_path = paths[0];

/**
 * アクセスユーザーを編集する
 */
function editUser(clnt) {
  $(modal).modal();

  var name = $('#client_' + clnt + '_name').val();
  $.ajax({
    url: c_path+service+'/edituser.php',
    data: {mode: mode, clntn: name, clnt: clnt},
    success: function(data) {
      if (data !== false) {
        $('#popup_base').html(data);
      }
    }
  });
}

/**
 * アクセスユーザーを新規登録する
 */
function insertUser(elm) {

  var name = $('#user_name').val();
  var id = $(elm).closest("#user_base").attr("data-id");

  var client_name = $('#client_' + id + '_name').val();

  if(name === "") {
    alert('メールアドレスを入力してください。');
    return;
  }
  $.ajax({
    url: c_path+service+'/edituser/set',
    data: {id: id, address: name, client_name: client_name, mode: mode},
    success: function(data) {
      if (data !== false) {

console.log(data);
        if (data.indexOf('Duplicate') !== -1) {
          alert('登録済のメールアドレスです。');
        } else {
          $('#user_name').val('');
          $('#popup_base').html(data);
        }
      }
    }
  });
}

/**
 * アクセスユーザー登録を削除する
 */
function deleteUser(elm){
  var client_id = $(elm).closest("#user_base").attr("data-id");
  var item_id   = $(elm).closest(".user_row").find(".mail-address").attr("data-item-id");

  var html = $('#user_' + item_id).html();

  if (html === "") {
    alert('既に削除されたか、値が不正です。');
    return;
  }
  if (confirm("削除します。よろしいですか?")) {
    $.ajax({
      url: '/sem/keyword_monitor/edit_user_del.php',
      data: {del_id: item_id, id: client_id, mode: mode},
      success: function(data) {
        if (data !== false) {
          $('#user_' + item_id).remove();
        }
      }
    });
  }
}

/**
 * クライアントを新規登録する
 */
function insertClient(elm) {
  var client = $(elm).closest("div.register");
  var d_name = client.attr("id");
  var opt = getOptionName(d_name);

  var d_name = 'client' + opt + '_';

  var clnt = $('#client_class').val();
  var sort = $('#' + d_name + '_sort').val();
  var name = $('#' + d_name + '_name').val();

  if(sort === "") {
    alert('並び順を入力してください。');
    return;
  }
  if(name === "") {
    alert('クライアント名を入力してください。');
    return;
  }

  var res = false;
  $.ajax({
    url: c_path+service+'/editclient/set'+opt,
    data: {id: '', sort: sort, name: name, clnt: clnt},
    success: function(data) {
      if (data !== false) {
          $('#' + d_name + '_sort').val('');
          $('#' + d_name + '_name').val('');
          $('#client' + opt + '_base').append(data);
      } else {
        $('#' + d_name + '_sort').val(sort);
        $('#' + d_name + '_name').val(name);
        alert('登録に失敗しました');
      }
    }
  });
}

/**
 * クライアント名を編集する
 */
function editClientName(elm) {
  var client = $(elm).closest("div.client-row");
  var d_name = client.attr("id");

  $('#' + d_name + '_pv').addClass('hide');
  $('#' + d_name + '_pt').removeClass('hide');
}

/**
 * クライアント名を更新する
 */
function updateClientName(elm) {
  var client = $(elm).closest("div.client-row");
  var d_name = client.attr("id");
  var item_id = client.attr("data-item-id");

  var opt = getOptionName(d_name);

  var up_clnt = $('#client_class').val();
  var up_sort = $('#' + d_name + '_sort').val();
  var up_name = $('#' + d_name + '_name').val();

    $.ajax({
      url: c_path+service+'/editclient/set'+opt,
      data: {id: item_id, sort: up_sort, name: up_name, clnt: up_clnt},
      success: function(data) {
        if (data !== false) {
          $('#' + d_name + '_pv .name').html('<span class="label label-success">' + up_sort + "</span> " + up_name);
        }
      }
    });

  $('#' + d_name + '_pv').removeClass('hide');
  $('#' + d_name + '_pt').addClass('hide');
}

/**
 * クライアント登録を削除する
 */
function deleteClient(elm){
  var client = $(elm).closest("div.client-row");
  var d_name = client.attr("id");
  var item_id = client.attr("data-item-id");

  var opt = getOptionName(d_name);

  var html = $('#' + d_name).html();
  if (html === "") {
    alert('既に削除されたか、値が不正です。');
    return;
  }
  if (confirm("削除します。よろしいですか?")) {
    $.ajax({
      url: c_path+service+'/editclient/del'+opt,
      data: {del_id: item_id},
      success: function(data) {
        if (data !== false) {
          $('#' + d_name).remove();
        }
      }
    });
  }
}

/**
 * クライアント or クライアント詳細 を判別
 */
function getOptionName(elm_id){
  if (elm_id.indexOf("class") > 0) {
    return "_class";
  }
  return "";
}

/**
 * ドキュメントロード時に実行する処理は以下にまとめる
 */
$(function(){

  /**
   * DOM Control
   */
  $(document).on('click', 'div.js-insert-btn', function(){
    insertClient(this);
  });

  $(document).on('click', 'div.client-row .name', function(){
    var name = this;
    editClientName(name);

    $('div.js-update-btn').on('click', function(){
      updateClientName(name);
    });
  });

  $(document).on('click', 'div.js-edit-user', function(){
    var client_id = $(this).closest("div.client-row").attr("data-item-id");
    editUser(client_id);
  });

  $(document).on('click', '.js-delete-btn', function(){
    deleteClient(this);
  });

  $(document).on('click', '.js-insert-user-btn', function(){
    insertUser(this);
  });

  $(document).on('click', '.js-delete-user-btn', function(){
    deleteUser(this);
  });


});
