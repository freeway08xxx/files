/**
* jQuery Common Functions
*/
$(document).ready(function() {
  var window_active_flg = true;
  $(window).bind("focus", function() {
    window_active_flg = true;
  });
  $(window).bind("blur", function() {
    window_active_flg = false;
  });
	$(document).on("click", "#js-check-param", function() {
    // 始めに、通知の許可を得ているかを確認しましょう
    // 得ていなければ、尋ねましょう
    if (Notification && Notification.permission !== "granted") {
      Notification.requestPermission(function (status) {
        if (Notification.permission !== status) {
          Notification.permission = status;
        }
        if (Notification.permission === "granted") {
          $("#js-check-param").remove();
        }
      });
    }
	});

	$(document).on("click", ".js-href-canceled", function() {
		return false;
	});

	// モーダルウインドウの背景クリックで閉じる
	$(document).on("click", ".modal.active", function(evt) {
		if (evt.target.id === "modal1") {
			$(this).removeClass("active");
		}
	});
  var websocket = websocket_connect();
  info_append();

  if (websocket) {
    //#### Message received from server?
    websocket.onmessage = function(ev) {
      try {
        var msg = JSON.parse(ev.data); //PHP sends Json data
        var uurl = msg.url; //message url
        var umsg = msg.message; //message text
        var uid = msg.user; //user name
        if (uid == $('#header_user_id').val()) {
          var cookie_info = $.cookie("INFO");
          if (cookie_info) {
            var cookie_info_data = JSON.parse(cookie_info);
            cookie_info_data[uurl] = umsg;
          } else {
            var cookie_info_data = {};
            cookie_info_data[uurl] = umsg;
          }
          $.cookie("INFO", JSON.stringify(cookie_info_data), { path: '/sem/new/',expires: 7 });
          // 通知されることにユーザが同意している場合
          if (window_active_flg == false && Notification && Notification.permission === "granted") {
            var n = new Notification('Search Suite',
                {
                  body: '新着のお知らせがあります。'
                  , tag: 'ss_info_sys'
                  , iconUrl: '/sem/new/assets/img/searchsuite_logo_02.jpg'
                  , icon: '/sem/new/assets/img/searchsuite_logo_02.jpg'
                });
            n.addEventListener("click", function() {
              window.focus();
              n.close();
            });
          }
          $('.footer').append('<div class="medium success btn icon-left entypo icon-mail" style="position: fixed; bottom: 50px;"><a href="#">新着のお知らせがあります。</a></div>');

          info_append();
        }
      } catch(e) {
        console.log('websocket display error.');
      }
    };

    websocket.onerror	= function(ev){
      try {
        $('#message_box').append("<div class=\"system_error\">Error Occurred - "+ev.data+"</div>");
      } catch(e) {
        console.log('websocket message error.');
      }
    };
    websocket.onclose	= function(ev){
      try {
        $('#message_box').append("<div class=\"system_msg\">Connection Closed</div>");
      } catch(e) {
        console.log('websocket message error.');
      }
    };
  }
	var alertMessage = function() {
		var alert_message = $('input[name="alert_message"]').val();

		if (alert_message) {
			var msg_bar = '<div class="alert alert-info">' + alert_message + '</div>';
			$("#alert_message_attach").html(msg_bar);

      setTimeout(function () {
        var pos = $(window).height() + 999;
        console.log(pos);
        $(window).scrollTop(pos);
      }, 500);
		}
	};
	alertMessage();
});

function websocket_connect() {
  var conn = null;
  try {
    // connection time out対応
    setTimeout("websocket_connect()",600000);
    var wsServer = $("#websocket_host").val();
    var wsUri = "ws://" + wsServer + ":9000/sem/new/server.php";
    conn = new WebSocket(wsUri);
  } catch(e) {
    console.log('websocket connect error.');
  }
  return conn;
}

function info_append() {
  var cookie_info = $.cookie("INFO");
  var info_count = 0;
  var info_menu = '<li><a href="#">お知らせはありません</a></li>';
  if (cookie_info) {
    var cookie_info_data = JSON.parse(cookie_info);
    var info_menu = '';
    jQuery.each(cookie_info_data, function(key, value) {
      info_menu = info_menu + '<li><a href="'+key+'">'+value+'</a></li>';
      info_count = info_count + 1;
    });
  }
  $('#info_menu_list').empty().append(info_menu);
  $('#info_menu_count').empty().append(info_count.toString());
  if (location.pathname.indexOf('falcon') === -1 && location.pathname.indexOf('segmentbanner') === -1 && location.pathname.indexOf('categorygenre') === -1 && location.pathname.indexOf('impestimate') === -1 && location.pathname.indexOf('relate') === -1) {
    return;
  }
  // if (Notification && Notification.permission !== "granted") {
  //   $('.footer').append('<div class="pretty medium default btn" id="js-check-param" style="position: fixed; bottom: 100px;"><a href="#">デスクトップ通知機能を利用する</a></div>');
  // }
}

function number_format(num) {
  return num.toString().replace(/([0-9]+?)(?=(?:[0-9]{3})+$)/g , '$1,')
}

/**
 * Search Common Functions
 */

// アカウント検索 / キャンペーン検索
function searchComponent(component, id) {

  // アカウント検索
  if (component === "account") {

    var objList = document.getElementById("account_id_list");
    var searchText = document.form01.account_search_text.value;
    var searchType = $("input:radio[name='account_search_type']:checked").val();
    var searchExcept = document.form01.account_search_except.checked;
    var searchBroad = document.form01.account_search_broad.checked;
    var searchIdOnly = document.form01.account_search_id_only.checked;

  // キャンペーン検索
  } else if (component === "campaign") {

    var objList = document.getElementById("campaign_id_list");
    var searchText = document.form01.campaign_search_text.value;
    var searchType = $("input:radio[name='campaign_search_type']:checked").val();
    var searchExcept = document.form01.campaign_search_except.checked;
    var searchBroad = document.form01.campaign_search_broad.checked;
    var searchIdOnly = document.form01.campaign_search_id_only.checked;
  }

  // 検索文字列入力時のみ実行
  if (searchText !== "") {

    var searchTextList = searchText.split("\n");

    for (i = 0; i < objList.length; i++) {

      var objText = objList[i].text;
      var display_none_flg = false;
      $("." + id + i).css("display", "block");

      for (j in searchTextList) {

        if (searchTextList[j] === "") {
          continue;
        }

        // ID検索 ON
        if (searchIdOnly) {
          if (component === "account") {
            var componentId = objText.match(/\[([a-zA-Z0-9-]*)\]/);
          } else if (component === "campaign") {
            var componentId = objText.match(/\[[a-zA-Z0-9-]*\]\[([a-zA-Z0-9-]*)\]/);
          }
          if (componentId) {
            objText = componentId[1];
          }
        }

        // AND
        if (searchType === "and") {

          // 除外 ON / 部分一致 ON
          if (searchExcept && searchBroad) {
            if (objText.match(searchTextList[j])) {
              display_none_flg = true;
            } else {
              display_none_flg = false;
              break;
            }

          // 除外 ON / 部分一致 OFF
          } else if (searchExcept && !searchBroad) {
            if (objText === searchTextList[j]) {
              display_none_flg = true;
            } else {
              display_none_flg = false;
              break;
            }

          // 除外 OFF / 部分一致 ON
          } else if (!searchExcept && searchBroad) {
            if (!objText.match(searchTextList[j])) {
              display_none_flg = true;
              break;
            } else {
              display_none_flg = false;
            }

          // 除外 OFF / 部分一致 OFF
          } else {
            if (objText !== searchTextList[j]) {
              display_none_flg = true;
              break;
            } else {
              display_none_flg = false;
            }
          }

        // OR
        } else if (searchType === "or") {

          // 除外 ON / 部分一致 ON
          if (searchExcept && searchBroad) {
            if (objText.match(searchTextList[j])) {
              display_none_flg = true;
              break
            } else {
              display_none_flg = false;
            }

          // 除外 ON / 部分一致 OFF
          } else if (searchExcept && !searchBroad) {
            if (objText === searchTextList[j]) {
              display_none_flg = true;
              break;
            } else {
              display_none_flg = false;
            }

          // 除外 OFF / 部分一致 ON
          } else if (!searchExcept && searchBroad) {
            if (!objText.match(searchTextList[j])) {
              display_none_flg = true;
            } else {
              display_none_flg = false;
              break;
            }

          // 除外 OFF / 部分一致 OFF
          } else {
            if (objText !== searchTextList[j]) {
              display_none_flg = true;
            } else {
              display_none_flg = false;
              break;
            }
          }
        }
      }

      // コンポーネントを非表示
      if (display_none_flg) {
        $("." + id + i).css("display", "none");
      }
    }
  } else {

    // 全表示
    for (i = 0; i < objList.length; i++) {
      $("." + id + i).css("display", "block");
    }
  }

  $("#" + id).multiSelect("refresh");
}

// 全選択 / 全解除
function allSelectCommon(id) {

  var objList = document.getElementById(id);
  var objArray = new Array();

  // 表示されているコンポーネントのみ対象
  for (i = 0; i < objList.length; i++) {

    if ($("." + id + i).css("display") === "inline"
        || $("." + id + i).css("display") === "block") {

      objArray.push(objList[i].value);
    }
  }

  return objArray;
}

// 現在のスクロール位置を取得
function getScrollPosition() {

  document.form01.scroll_x.value = document.documentElement.scrollLeft || document.body.scrollLeft;
  document.form01.scroll_y.value = document.documentElement.scrollTop || document.body.scrollTop;
}

// submit時のスクロール位置を設定
function setScrollPosition(scroll_x, scroll_y) {

  window.scroll(scroll_x, scroll_y);
}
