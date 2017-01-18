var wn = '.modal';
var mode = 'industry';

/**
 * アクセスユーザーを編集する
 */
function editUser(industry_id) {
  var name = $('#industry_' + industry_id + '_name').val();
  $.ajax({
    url: '/sem/keyword_monitor/edit_user.php',
    data: {mode: mode, idstn: name, idst: industry_id},
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

  if(name === "") {
    alert('メールアドレスを入力してください。');
    return;
  }
  var res = false;
  $.ajax({
    url: '/sem/keyword_monitor/edit_user_set.php',
    data: {id: id, address: name, mode: mode},
    success: function(data) {
      if (data !== false) {
        if (data != 'D') {
          $('#user_name').val('');
          $('#user_base').append(data);
        } else {
          alert('登録済のメールアドレスです。');
        }
      }
    }
  });
}

/**
 * アクセスユーザー登録を削除する
 */
function deleteUser(elm){
  var industry_id = $(elm).closest("#user_base").attr("data-id");
  var item_id   = $(elm).closest(".user_row").attr("data-item-id");

  var html = $('#user_' + item_id).html();

  if (html === "") {
    alert('既に削除されたか、値が不正です。');
    return;
  }
  if (confirm("削除します。よろしいですか?")) {
    $.ajax({
      url: '/sem/keyword_monitor/edit_user_del.php',
      data: {del_id: item_id, id: industry_id, mode: mode},
      success: function(data) {
        if (data !== false) {
          $('#user_' + item_id).remove();
        }
      }
    });
  }
}

/**
 * 業種を新規登録する
 */
function insertIndustry(elm) {
  var industry = $(elm).closest("div.register");
  var d_name = industry.attr("id");
  var opt = getOptionName(d_name);

  var d_name = 'industry' + opt + '_';

  var idst = $('#industry_class').val();
  var sort = $('#' + d_name + '_sort').val();
  var name = $('#' + d_name + '_name').val();

  if(sort === "") {
    alert('並び順を入力してください。');
    return;
  }
  if(name === "") {
    alert('業種名を入力してください。');
    return;
  }

  var res = false;
  $.ajax({
    url: '/sem/keyword_monitor/edit_industry' + opt + '_set.php',
    data: {id: '', sort: sort, name: name, idst: idst},
    success: function(data) {
      if (data !== false) {
          $('#' + d_name + '_sort').val('');
          $('#' + d_name + '_name').val('');
          $('#industry' + opt + '_base').append(data);
      } else {
        $('#' + d_name + '_sort').val(sort);
        $('#' + d_name + '_name').val(name);
        alert('登録に失敗しました');
      }
    }
  });
}

/**
 * 業種名を編集する
 */
function editIndustryName(elm) {
  var industry = $(elm).closest("div.industry-row");
  var d_name = industry.attr("id");

  $('#' + d_name + '_pv').hide();
  $('#' + d_name + '_pvb').hide();
  $('#' + d_name + '_pt').show();
}

/**
 * 業種名を更新する
 */
function updateIndustryName(elm) {
  var industry = $(elm).closest("div.industry-row");
  var d_name = industry.attr("id");
  var item_id = industry.attr("data-item-id");

  var opt = getOptionName(d_name);

  var up_idst = $('#industry_class').val();
  var up_sort = $('#' + d_name + '_sort').val();
  var up_name = $('#' + d_name + '_name').val();

    $.ajax({
      url: '/sem/keyword_monitor/edit_industry' + opt + '_set.php',
      data: {id: item_id, sort: up_sort, name: up_name, idst: up_idst},
      success: function(data) {
        if (data !== false) {
          $('#' + d_name + '_pv').html('<span class="light badge">' + up_sort + "</span> " + up_name);
        }
      }
    });

  $('#' + d_name + '_pv').show();
  $('#' + d_name + '_pvb').show();
  $('#' + d_name + '_pt').hide();
}

/**
 * 業種登録を削除する
 */
function deleteIndustry(elm){
  var industry = $(elm).closest("div.industry-row");
  var d_name = industry.attr("id");
  var item_id = industry.attr("data-item-id");

  var opt = getOptionName(d_name);

  var html = $('#' + d_name).html();
  if (html === "") {
    alert('既に削除されたか、値が不正です。');
    return;
  }
  if (confirm("削除します。よろしいですか?")) {
    $.ajax({
      url: '/sem/keyword_monitor/edit_industry' + opt + '_del.php',
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
 * 業種 or 業種詳細 を判別
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
  $('.close,.modalBK').click(function(){
    $(wn).hide();
  });


  /**
   * DOM Control
   */
  $(document).on('click', 'div.js-insert-btn', function(){
    insertIndustry(this);
  });

  $(document).on('click', 'div.name', function(){
    var name = this;
    editIndustryName(name);

    $('div.js-update-btn').on('click', function(){
      updateIndustryName(name);
    });
  });

  $(document).on('click', 'div.js-edit-user', function(){
    var industry_id = $(this).closest("div.industry-row").attr("data-item-id");
    editUser(industry_id);
  });

  $(document).on('click', '.js-delete-btn', function(){
    deleteIndustry(this);
  });

  $(document).on('click', '.js-insert-user-btn', function(){
    insertUser(this);
  });

  $(document).on('click', '.js-delete-user-btn', function(){
    deleteUser(this);
  });


});