// アカウント同期実行画面
$(window).load(function () {
	var scroll_x = $('input[name="set_scroll_x"]').val();
	var scroll_y = $('input[name="set_scroll_y"]').val();
	setScrollPosition(scroll_x, scroll_y);
});

$(document).ready(function() {

	// クライアント選択
	$("#client_id").select2();

	// アカウント選択
	$("#account_id_list").multiSelect({

		selectableHeader: "<span class=\"label label-default\">選択可能アカウント</span>",
		selectionHeader: "<span class=\"label label-success\">選択済みアカウント</span>",
	});

	// 予約日時
	$.datepicker.setDefaults($.datepicker.regional["ja"]);

	// 予約日時From
	$("#account_sync_date_from").datepicker({

		dateFormat: "yy/mm/dd"
	});

	// 予約日時To
	$("#account_sync_date_to").datepicker({

		dateFormat: "yy/mm/dd"
	});

	// アカウント同期一覧
	$("#account_sync_list").dataTable({

		"aaSorting": [[3, "desc"]],
		"bDeferRender": true,
		"bDestroy": true,
		"iDisplayLength": 10,
		"oLanguage": {
			"sLengthMenu": "表示件数_MENU_",
			"sZeroRecords": "該当データが存在しません",
			"sInfo": "_START_ ～ _END_件 / 全_TOTAL_件",
			"sInfoEmpty": "該当データが存在しません",
			"sInfoFiltered": "（全_MAX_件より抽出）",
			"sInfoPostFix": "",
			"sSearch": '<i class="glyphicon glyphicon-search"></i>',
			"sUrl":	"",
			"oPaginate": {
				"sFirst": "先頭",
				"sPrevious": "前",
				"sNext": "次",
				"sLast": "最終"
			},
			"sLengthMenu": '表示件数 <select>'
						 + '<option value="10">10</option>'
						 + '<option value="-1">All</option>'
						 + '</select>',
		}
	});

	// クライアント選択
	$(document).on("change", "#client_id", function() {

		// 変更前クライアントの設定値をクリア
		$("#all_account_cancel").trigger("click");

		document.form01.action="/sem/new/accountsync/execute";
		document.form01.submit();
	});

	// アカウント選択
	$(document).on("change", "#account_id_list", function() {

		// アカウント選択数を表示
		var count = 0;

		for (var i = 0; i < document.form01.account_id_list.length; i++) {

			if (document.form01.account_id_list[i].selected === true) {

				count++;
			}
		}

		document.getElementById("select_account_count").innerHTML = count;
	});

	// アカウント全選択
	$("#all_account_select").on("click", function() {

		var objArray = allSelectCommon("account_id_list");

		$("#account_id_list").multiSelect("select", objArray);
	});

	// アカウント全解除
	$("#all_account_cancel").on("click", function() {

		var objArray = allSelectCommon("account_id_list");

		$("#account_id_list").multiSelect("deselect", objArray);
	});

	// アカウント検索
	$("#account_search").on("click", function() {

		searchComponent("account", "account_id_list");
	});

	// アカウント同期実行 実行種別
	$("input:radio[name='account_sync_type']").on("change", function() {

		var type = $("input:radio[name='account_sync_type']:checked").val();

		// 即時実行
		if (type === "0") {

			$(".account_sync_datetime").css("display", "none");

		// 予約
		} else if (type === "1") {

			$(".account_sync_datetime").css("display", "block");
		}
	});

	// アカウント同期実行 実行内容
	$("input:radio[name='account_sync_content']").on("change", function() {

		var content = $("input:radio[name='account_sync_content']:checked").val();

		// アカウント同期
		if (content === "0") {

			$(".account_sync_out_format").css("display", "block");

		// 審査状況取得
		} else if (content === "1") {

			$(".account_sync_out_format").css("display", "none");
		}
	});

	// アカウント同期実行 実行
	$("#sync_execute").on("click", function() {

		var type = $("input:radio[name='account_sync_type']:checked").val();

		// 予約時のみ
		if (type === "reserve") {

			var dateFrom = document.form01.account_sync_date_from.value;
			var dateTo = document.form01.account_sync_date_to.value;

			// 日付の必須チェック
			if (dateFrom === "" || dateTo === "") {

				alert("予約日は必須です。");
				return;
			}

			// 日付の妥当性チェック
			if (dateCheck(dateFrom) === false) {

				alert("予約日Fromの形式が間違ってます。");
				return;
			}

			if (dateCheck(dateTo) === false) {

				alert("予約日Toの形式が間違ってます。");
				return;
			}

			// 日付From-Toの妥当性チェック
			var chkDateFrom = new Date(dateFrom);
			var chkDateTo = new Date(dateTo);

			if (chkDateFrom > chkDateTo) {

				alert("予約日ToはFromより後にしてください。");
				return;
			}

			// 予約日チェック
			var nowDate = new Date();

			if (nowDate.setMonth(nowDate.getMonth() + 1) < chkDateFrom) {

				alert("予約日Fromは直近1ヶ月以内にしてください。");
				return;
			}

			// 予約期間チェック
			if (chkDateFrom.setMonth(chkDateFrom.getMonth() + 1) < chkDateTo) {

				alert("予約期間は1ヶ月以内にしてください。");
				return;
			}
		}

		// メールアドレスチェック
		var mailAddress = document.form01.account_sync_mail_address.value;

		if (mailAddress !== "") {

			var mailAddressList = mailAddress.split(",");

			for (i in mailAddressList) {

				if (!mailAddressList[i].match(/.+@.+\..+/)) {

					alert("メールアドレスの形式が正しくありません。\n[" + mailAddressList[i] + "]");
					return;
				}
			}
		}

		if (confirm("アカウント同期を実行します。")) {

			document.form01.action="/sem/new/accountsync/execute/sync_execute";
			document.form01.submit();
		}
	});

	// アカウント同期結果 Reload
	$("#reload").on("click", function() {

		document.form01.action="/sem/new/accountsync/execute";

		// スクロール位置を取得
		getScrollPosition();

		document.form01.submit();
	});

	// 予約一覧 全選択
	$("#all_account_sync_select").on("click", function() {

		$(".account_sync_row").prop("checked", true);
	});

	// 予約一覧 全解除
	$("#all_account_sync_cancel").on("click", function() {

		$(".account_sync_row").prop("checked", false);
	});

	// 予約一覧 一括DL
	$("#all_account_sync_bulk_dl").on("click", function() {

		var index = 0;
		var accountSyncList = new Array;

		$("input[name=account_sync_row]:checked").map(function() {
			accountSyncList[index] = $(this).val();
			index++;
		});

		if (accountSyncList.length > 0) {
			document.form01.action="/sem/new/accountsync/execute/sync_bulk_dl";
			document.form01.account_sync_select_row.value=accountSyncList;
			document.form01.submit();
		}
	});

	// 予約一覧 全削除
	$("#all_account_sync_delete").on("click", function() {

		if (confirm("アカウント同期予約を削除します。")) {

			var index = 0;
			var accountSyncList = new Array;

			$("input[name=account_sync_row]:checked").map(function() {

				accountSyncList[index] = $(this).val();
				index++;
			});

			if (accountSyncList.length > 0) {

				document.form01.action="/sem/new/accountsync/execute/sync_delete";
				document.form01.account_sync_select_row.value=accountSyncList;
				document.form01.submit();
			}
		}
	});

	// ページ読み込み時に実行
	$("input:radio[name='account_sync_type']").trigger("change");
	$("input:radio[name='account_sync_content']").trigger("change");
});

// 日付チェック
function dateCheck(vDate) {

	// YYYY/MM/DD形式チェック
	if (!vDate.match(/^\d{4}\/\d{2}\/\d{2}$/)) {

		return false;
	}

	var vYear = vDate.substr(0, 4) - 0;
	var vMonth = vDate.substr(5, 2) - 1;
	var vDay = vDate.substr(8, 2) - 0;

	// 月日の妥当性チェック
	if (vMonth >= 0 && vMonth <= 11 && vDay >= 1 && vDay <= 31) {

		var vDt = new Date(vYear, vMonth, vDay);

		if (isNaN(vDt)) {

			return false;

		} else if (vDt.getFullYear() == vYear && vDt.getMonth() == vMonth && vDt.getDate() == vDay) {

			return true;

		} else {

			return false;
		}

	} else {

		return false;
	}
}
