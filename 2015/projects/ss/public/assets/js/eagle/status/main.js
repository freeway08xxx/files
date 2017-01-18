// 外部ツール
$(document).ready(function() {
	$("#client_id").select2();
	$("#account_id_list").multiSelect({
		selectableHeader: "<div class=\"info label eagle-title\">選択可能アカウント</div>",
		selectionHeader: "<div class=\"danger label eagle-title\">選択済みアカウント</div>",
	});
});

// クライアント選択時のアクション
$(document).on("change", "#client_id", function() {
	// クライアント未選択時はクライアント選択
	if (document.form01.client_id.value == "") {
		document.form01.action="/sem/new/eagle/status/update/client";
	// クライアント選択時はアカウント選択
	} else {
		document.form01.action="/sem/new/eagle/status/update/account";
	}
	document.form01.submit();
});

// アカウント検索ボタン押下時のアクション
$(document).ready(function() {
	$("#account_search_btn").on("click", function() {

		var objList    = document.getElementById("account_id_list");
		var searchList = document.form01.account_search_list.value;
		var searchType = $("input:radio[name='account_search_type']:checked").val();
		var search     = document.form01.account_search.checked;
		var searchLike = true;

		// 検索文字列入力時のみ実行
		if (searchList !== "") {
			var searchLists = searchList.split("\n");
			for (i = 0; i < objList.length; i++) {
				var display_none_flg = false;
				$("#account_id_list_" + i).css("display", "block");

				for (j in searchLists) {
					// 空文字は無視
					if (searchLists[j] ==="") break;
	                // AND
					if (searchType === "1") {
						// 除外 ON / 部分一致 ON
						if (search && searchLike) {
							if (objList[i].text.match(searchLists[j])) {
								display_none_flg = true;
							} else {
								display_none_flg = false;
								break;
							}
						// 除外 OFF / 部分一致 ON
						} else if (!search && searchLike) {
							if (!objList[i].text.match(searchLists[j])) {
								display_none_flg = true;
								break;
							} else {
								display_none_flg = false;
							}
						}
					// OR
					} else if (searchType === "0") {
						// 除外 ON / 部分一致 ON
						if (search && searchLike) {
							if (objList[i].text.match(searchLists[j])) {
								display_none_flg = true;
								break
							} else {
								display_none_flg = false;
							}
						// 除外 OFF / 部分一致 ON
						} else if (!search && searchLike) {
							if (!objList[i].text.match(searchLists[j])) {
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
					$("#account_id_list_" + i).css("display", "none");
				}
			}
		} else {
			// 全表示
			for (i = 0; i < objList.length; i++) {
				$("#account_id_list_" + i).css("display", "block");
			}
		}
		$("#account_id_list").multiSelect("refresh");
	});
});

// アカウント検索クリアボタン押下時のアクション
$(document).ready(function() {
	$("#account_search_clear_btn").on("click", function() {
		document.form01.account_search.checked=false;
		document.form01.account_search_type.value="0";
		document.form01.account_search_list.value="";
	});
});

// アカウント全選択ボタン押下時のアクション
$(document).ready(function() {
	$("#account_all_select_btn").on("click", function() {
		var objList  = document.getElementById("account_id_list");
		var searchList = document.form01.account_search_list.value;
		var objArray = new Array();

		// 検索文字列入力時
		if (searchList !== "") {
			// 表示されているコンポーネントのみ対象
			for (i = 0; i < objList.length; i++) {
				if ($("#account_id_list_" + i).css("display") === "block") {
					objArray.push(objList[i].value);
				}
			}
			$("#account_id_list").multiSelect("select", objArray);
		} else {
			$("#account_id_list").multiSelect("select_all");
		}
	});
});

// アカウント全解除ボタン押下時のアクション
$(document).ready(function() {
	$("#account_all_cancel_btn").on("click", function() {
		var objList  = document.getElementById("account_id_list");
		var searchList = document.form01.account_search_list.value;
		var objArray = new Array();

		// 検索文字列入力時
		if (searchList !== "") {
			// 表示されているコンポーネントのみ対象
			for (i = 0; i < objList.length; i++) {
				if ($("#account_id_list_" + i).css("display") === "block") {
					objArray.push(objList[i].value);
				}
			}
			$("#account_id_list").multiSelect("deselect", objArray);
		} else {
			$("#account_id_list").multiSelect("deselect_all");
		}
	});
});

// 選択しているアカウント数を表示
$(document).on("change", "#account_id_list", function() {
	var count = 0;

	for (var i = 0; i < document.form01.account_id_list.length; i++) {
		if (document.form01.account_id_list[i].selected == true) {
			count++;
		}
	}
	document.getElementById("select_account_count").innerHTML = count;
});

// 対象CPN一覧DLボタン押下時のアクション
$(document).ready(function() {
	$("#download_campaign_btn").on("click", function() {
		// アカウント選択時のみ
		if (document.form01.account_id_list.value != "") {
			document.form01.action="/sem/new/eagle/status/update/campaigndl";
			document.form01.submit();
		}
	});
});

// 最新CPN取得ボタン押下時のアクション
$(document).ready(function() {
	$("#refresh_campaign_btn").on("click", function() {
		// アカウント選択時のみ
		if (document.form01.account_id_list.value != "") {
			document.form01.action="/sem/new/eagle/status/update/refreshcpn";
			document.form01.submit();
		}
	});
});

// 掲載取得
$(document).ready(function() {
	$("#get_campaign_structure_btn").on("click", function() {
		// アカウント選択時のみ
		if (document.form01.account_id_list.value != "") {
			document.form01.action="/sem/new/eagle/status/update/getstructure";
			document.form01.submit();
		}
	});
});

// 掲載取得せず進む
$(document).ready(function() {
	$("#unget_campaign_structure_btn").on("click", function() {
		// アカウント選択時のみ
		if (document.form01.account_id_list.value != "") {
			if (confirm("最新の掲載取得をしませんが、よろしいですか？")) {
				document.form01.action="/sem/new/eagle/status/update/ungetstructure";
				document.form01.submit();
			}
		}
	});
});

// 処理対象選択変更時のアクション
$(document).on("change", "#component", function() {

	var objList = document.getElementById("component");

	// 広告グループ選択時
	if (objList.selectedIndex == "1") {
		$("#campaign_search").css("display", "block");
		$("#adgroup_search").css("display", "block");
		$("#keyword_search").css("display", "none");
		$("#ad_search").css("display", "none");
		$("#ad_search_pattern").css("display", "none");
	// キーワード選択時
	} else if (objList.selectedIndex == "2") {
		$("#campaign_search").css("display", "block");
		$("#adgroup_search").css("display", "block");
		$("#keyword_search").css("display", "block");
		$("#ad_search").css("display", "none");
		$("#ad_search_pattern").css("display", "none");
	// 広告選択時
	} else if (objList.selectedIndex == "3") {
		$("#campaign_search").css("display", "block");
		$("#adgroup_search").css("display", "block");
		$("#keyword_search").css("display", "none");
		$("#ad_search").css("display", "block");
		$("#ad_search_pattern").css("display", "block");
	// キャンペーン選択時
	} else {
		$("#campaign_search").css("display", "block");
		$("#adgroup_search").css("display", "none");
		$("#keyword_search").css("display", "none");
		$("#ad_search").css("display", "none");
		$("#ad_search_pattern").css("display", "none");
	}
});

// 処理対象選択読込時のアクション
$(document).ready(function() {
	var objList = document.getElementById("component");
	if (objList) {
		$("#component").trigger("change");
	}
});

// 処理対象一覧DLボタン押下時のアクション
$(document).ready(function() {
	$("#download_update_status_btn").on("click", function() {
		// アカウント選択時のみ
		if (document.form01.account_id_list.value != "") {
			document.form01.action="/sem/new/eagle/status/update/targetdl";
			document.form01.submit();
		}
	});
});

// 確認ボタン押下時のアクション
$(document).ready(function() {
	$("#check_btn").on("click", function() {
		// アカウント選択時のみ
		if (document.form01.account_id_list.value != "") {
			document.form01.action="/sem/new/eagle/status/update/check";
			document.form01.submit();
		}
	});
});

// 掲載取得せず進むへ戻る
$(document).ready(function() {
	$("#return_unget_campaign_structure_btn").on("click", function() {
		// アカウント選択時のみ
		if (document.form01.account_id_list.value != "") {
			document.form01.action="/sem/new/eagle/status/update/ungetstructure";
			document.form01.submit();
		}
	});
});

// 絞り込み検索ボタン押下時のアクション
$(document).ready(function() {
	$("#search_btn").on("click", function() {
		// アカウント選択時のみ
		if (document.form01.account_id_list.value != "") {
			document.form01.search_flg.value="1";
			document.form01.action="/sem/new/eagle/status/update/check";
			document.form01.submit();
		}
	});
});

// 表示件数選択時のアクション
$(document).on("change", "#per_page", function() {
	document.form01.page.value = "1";
	document.form01.action="/sem/new/eagle/status/update/check";
	document.form01.submit();
});

// ページ押下時のアクション
function send(param) {
	orgPage = $(param).children("a").attr("page");
	newPage = orgPage.replace(/\?p=/,"");
	document.form01.page.value = newPage;
	action="/sem/new/eagle/status/update/check";
	action += orgPage;
	document.form01.action=action;
	document.form01.submit();
}
$(function(){
  $(document).on("click", "#pagination span", function(){
    send(this);
  });
});

// 全ONボタン押下時のアクション
$(document).ready(function() {
	$("#update_status_on_btn").on("click", function() {
		// アカウント選択時のみ
		if (document.form01.account_id_list.value != "") {
			if (confirm("処理対象のステータスを「全て再開」しますが、\nよろしいですか？")) {
				document.form01.update_status_flg.value="1";
				document.form01.action="/sem/new/eagle/status/update/updstatus";
				document.form01.submit();
			}
		}
	});
});

// 全OFFボタン押下時のアクション
$(document).ready(function() {
	$("#update_status_off_btn").on("click", function() {
		// アカウント選択時のみ
		if (document.form01.account_id_list.value != "") {
			if (confirm("処理対象のステータスを「全て停止」しますが、\nよろしいですか？")) {
				document.form01.update_status_flg.value="0";
				document.form01.action="/sem/new/eagle/status/update/updstatus";
				document.form01.submit();
			}
		}
	});
});

// 戻るボタン押下時のアクション
$(document).ready(function() {
	$("#return_btn").on("click", function() {
		// クライアント未選択時はクライアント選択
		if (document.form01.client_id.value == "") {
			document.form01.action="/sem/new/eagle/status/update/client";
		// クライアント選択時はアカウント選択
		} else {
			document.form01.action="/sem/new/eagle/status/update/account";
		}
		document.form01.submit();
	});
});
