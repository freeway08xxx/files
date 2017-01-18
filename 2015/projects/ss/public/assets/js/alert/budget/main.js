var alert_list = ['1','2','3','4','9'];
var display_obj = [];
var ary_display_obj = [];
var obj_total_count;
var account_table = $('#account_table').html();
var alert_table = $('#alert_table').html();
var $alert_data;
var clients = location.pathname.split('/');
var c_client_id = clients[clients.length-1];
var paths = location.pathname.split('alert/budget');
var c_path = paths[0];
var modal = '#mediabudget';

$(document).ready(function() {

  $.getJSON(c_path+"alert/budget/table/"+c_client_id, function(data){
    display_obj = [];
    $alert_data = data;
    $(data).each(function(){
      display_view(this);
    })
    obj_total_count = display_obj.length;
    ary_display_obj = $('#paging').wPaginate({total: obj_total_count,index: 1}, display_obj);
    for_paging(1);
  })
	$("#for_stop_search").on("click", function() {
    $('#media_search').val('');
    $('#accountname_search').val('');
    $('#accountid_search').val('');
    $('#stoplimit_search').trigger('gumby.uncheck');
    $('#budget_search').trigger('gumby.uncheck');
    display_obj = [];
    $('#account_table').html(account_table);
    $('#alert_table').html(alert_table);
    $($alert_data).each(function(){
        if ($('#stop_search').is(':checked')) {
          if (this.stop_flg == $('#stop_search').val()) {
            display_view(this);
          }
        } else {
          display_view(this);
        }
    })
    obj_total_count = display_obj.length;
    ary_display_obj = $('#paging').wPaginate({total: obj_total_count,index: 1}, display_obj);
    for_paging(1);
	});
	$("#for_stoplimit_search").on("click", function() {
    $('#media_search').val('');
    $('#accountname_search').val('');
    $('#accountid_search').val('');
    $('#stop_search').trigger('gumby.uncheck');
    $('#budget_search').trigger('gumby.uncheck');
    display_obj = [];
    $('#account_table').html(account_table);
    $('#alert_table').html(alert_table);
    $($alert_data).each(function(){
        if ($('#stoplimit_search').is(':checked')) {
          if (this.remainder_day <= 3 && this.alert_code != '0') {
            display_view(this);
          }
        } else {
          display_view(this);
        }
    })
    obj_total_count = display_obj.length;
    ary_display_obj = $('#paging').wPaginate({total: obj_total_count,index: 1}, display_obj);
    for_paging(1);
	});
	$("#media_search").on("change", function() {
    $('#accountname_search').val('');
    $('#accountid_search').val('');
    $('#stop_search').trigger('gumby.uncheck');
    $('#stoplimit_search').trigger('gumby.uncheck');
    $('#budget_search').trigger('gumby.uncheck');
    display_obj = [];
    $('#account_table').html(account_table);
    $('#alert_table').html(alert_table);
    $($alert_data).each(function(){
        if (this.media_id == $('#media_search').val() || $('#media_search').val() == '') {
          display_view(this);
        }
    })
    obj_total_count = display_obj.length;
    ary_display_obj = $('#paging').wPaginate({total: obj_total_count,index: 1}, display_obj);
    for_paging(1);
	});
	$("#accountid_search").on("keyup", function() {
    $('#media_search').val('');
    $('#accountname_search').val('');
    $('#stop_search').trigger('gumby.uncheck');
    $('#stoplimit_search').trigger('gumby.uncheck');
    $('#budget_search').trigger('gumby.uncheck');
    display_obj = [];
    $('#account_table').html(account_table);
    $('#alert_table').html(alert_table);
    $($alert_data).each(function(){
        if ((this.account_id).indexOf($('#accountid_search').val()) >= 0) {
          display_view(this);
        }
    })
    obj_total_count = display_obj.length;
    ary_display_obj = $('#paging').wPaginate({total: obj_total_count,index: 1}, display_obj);
    for_paging(1);
	});
	$("#accountname_search").on("keyup", function() {
    $('#media_search').val('');
    $('#accountid_search').val('');
    $('#stop_search').trigger('gumby.uncheck');
    $('#stoplimit_search').trigger('gumby.uncheck');
    $('#budget_search').trigger('gumby.uncheck');
    display_obj = [];
    $('#account_table').html(account_table);
    $('#alert_table').html(alert_table);
    $($alert_data).each(function(){
        if ((this.account_name).indexOf($('#accountname_search').val()) >= 0) {
          display_view(this);
        }
    })
    obj_total_count = display_obj.length;
    ary_display_obj = $('#paging').wPaginate({total: obj_total_count,index: 1}, display_obj);
    for_paging(1);
	});
	$("#for_budget_search").on("click", function() {
    tmp_display_obj = display_obj;
    display_obj = [];
    $('#account_table').html(account_table);
    $('#alert_table').html(alert_table);
    if ($('#budget_search').is(':checked')) {
      $(tmp_display_obj).each(function(){
          if (this.consumption_cost > 0) {
            display_view(this);
          }
      })
    } else {
      if($('#media_search').val().length > 0){
	      $("#media_search").trigger("change");
      } else if ($('#accountid_search').val().length > 0) {
	      $("#accountid_search").trigger("keyup");
      } else if ($('#accountname_search').val().length > 0) {
	      $("#accountname_search").trigger("keyup");
      } else if ($('#stop_search').is(':checked')) {
	      $("#stop_search").trigger('click').trigger("click");
      } else if ($('#stoplimit_search').is(':checked')) {
	      $("#stoplimit_search").trigger('click').trigger("click");
      } else {
	      $("#media_search").trigger("change");
      }
    }
    obj_total_count = display_obj.length;
    ary_display_obj = $('#paging').wPaginate({total: obj_total_count,index: 1}, display_obj);
    for_paging(1);
	});

  $("#client_id").select2();
  $("#client_id").on("change", function() {
    if ($('#client_id').val().length > 0) {
      var href=c_path+"alert/budget/list/"+$('#client_id').val();
      location.href=href;
    }
	});

  $(document).on('click', 'button.js-mod-budget', function(){
    var account_id = $(this).attr("data-item-id");
    modBudget(account_id);
  });
  $(document).on('click', 'button.js-send-budget', function(){
    var account_id = $('#item_id').val();
    sendBudget(account_id);
  });

});
function modBudget(account_id) {
  $(modal).modal();
  $.ajax({
    type: 'post',
    url: c_path+"alert/budget/form/"+c_client_id,
    data: {account_id: account_id},
    success: function(data) {
      if (data !== false) {
        $('#popup_base').html(data);
      }
    }
  });
}
function sendBudget(account_id) {
  var before_budget = $('#before_budget').val();
  var budget = $('#after_budget').val();
  var before_budget_type = $('#before_budget_type').val();
  var budget_type_id = $('#after_budget_type').val();

  budget = budget.split(",").join("");
  if (isNaN(budget)) {
    alert('数値で入力してください。');
    return;
  }
  if(budget < 3000) {
    alert('金額を3,000円以上で入力してください。');
    return;
  }
  if((budget % 1000) > 0) {
    alert('1,000円以下の単位で金額変更はできません。');
    return;
  }
  if (before_budget_type == budget_type_id && before_budget == budget) {
    alert('変更がありません。');
    return;
  }
  var conf_message = "以下の内容で予算変更依頼メールを送信します。よろしいですか?\n\n";
  if (before_budget_type == budget_type_id) {
    conf_message += "予算タイプ　変更なし\n\n";
  } else {
    var before_type = "月額";
    if (before_budget_type != 1) {
      before_type = "総額";
    }
    var after_type = "月額";
    if (budget_type_id != 1) {
      after_type = "総額";
    }
    conf_message += "予算タイプ　変更前："+before_type+" >> 変更後："+after_type+"\n\n";
  }
  if (before_budget == budget) {
    conf_message += "媒体予算　変更なし\n";
  } else {
    conf_message += "媒体予算　変更前：\\"+before_budget.replace( /(\d)(?=(\d\d\d)+(?!\d))/g, '$1,' )+" >> 変更後：\\"+budget.replace( /(\d)(?=(\d\d\d)+(?!\d))/g, '$1,' );
  }
  if (confirm(conf_message)) {
    var res = false;
    $.ajax({
      type: 'post',
      url: c_path+"alert/budget/send/"+c_client_id,
      data: {account_id: account_id, budget_type_id: budget_type_id, budget: budget},
      success: function(data) {
        if (data !== false) {
          alert("依頼メールを送信しました。");

          $(modal).modal('hide');
        }
      }
    });
  }
}
function for_paging(i){
  obj = ary_display_obj[i];
  $('#account_table').html(account_table);
  $(obj).each(function(){
    $(this.view).appendTo('#account_table');
  })
  $('#paging').wPaginate({total: obj_total_count,index: i},[]);
}
function display_view(data) {
  if ($.inArray(data.alert_code, alert_list) >= 0) {
    $(data.alert_view).appendTo('#alert_table');
  }
  display_obj.push(data);
}
function editLimit(account_id, alert_val) {
  if ($('#limit_'+alert_val+account_id).val()) {
    var limit_cost = $('#limit_'+alert_val+account_id).val().split(",").join("");
    if (isNaN(limit_cost)) {
      alert('数値で入力してください。');
      return;
    }
    var upd_url = c_path+"alert/budget/upd/"+c_client_id;
    $.ajax({
      type: 'post',
      url: upd_url,
      data: {account_id: account_id, limit: limit_cost},
      success: function(data) {
        if (data !== false) {
          var $input = $('#limit_'+alert_val+account_id);
          $input.val('');
          $input.attr('placeholder', number_format(limit_cost));

          $('#limit_budget_'+account_id).empty();
          $('#limit_budget_'+account_id).append(number_format(limit_cost));
          $('#limit_budget_alert_'+account_id).empty();
          $('#limit_budget_alert_'+account_id).append(number_format(limit_cost));

          account_reload();
          alert('予算リミットを更新しました。');
          return;
        }
      },
      error: function(data) {
        $('#limit_'+alert_val+account_id).val('');
        alert('予算リミットの更新に失敗しました。お手数ですがしばらくしてから再度実行してください。');
        return;
      }
    });
  }
}

function startAccount(account_id){
  var urls = location.pathname.split('/');
  var start_url = c_path+"alert/budget/start/"+c_client_id;
  if (confirm("アカウントID："+account_id+"の強制停止解除処理を行います。よろしいですか?")) {
    $.ajax({
      type: 'post',
      url: start_url,
      data: {account_id: account_id},
      success: function(data) {
        if (data !== false) {
          $('#stop_account_'+account_id).empty();
          $('#stop_account_'+account_id).append('<div class="no_btn"></div>');
          $('#stop_account_alert_'+account_id).empty();
          $('#stop_account_alert_'+account_id).append('<div class="no_btn"></div>');
          account_reload();
          alert('アカウントの再開処理を行いました。各媒体画面にてご確認お願いいたします。');
          return;
        }
      },
      error: function(data) {
        alert('アカウントの再開処理に失敗しました。お手数ですがしばらくし画面をリロードしてから再度実行してください。');
        return;
      }
    });
  }
}
function account_reload(){
  $.getJSON(c_path+"alert/budget/table/"+c_client_id, function(data){
    $alert_data = data;
  })
}

