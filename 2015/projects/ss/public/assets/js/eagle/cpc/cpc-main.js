// CPC変更画面
$(window).load(function () {
	var scroll_x = $('input[name="set_scroll_x"]').val();
	var scroll_y = $('input[name="set_scroll_y"]').val();
	setScrollPosition(scroll_x, scroll_y);
});

$(document).ready(function() {

	currentDoc = document;

	// クライアント選択
	$("#client_id").select2();

	// アカウント選択
	$("#account_id_list").multiSelect({

		selectableHeader: "<div class=\"info label budget-title\">選択可能アカウント</div>",
		selectionHeader: "<div class=\"info label budget-title\">選択済みアカウント</div>",
	});

	// キャンペーン選択
	$("#campaign_id_list").multiSelect({

		selectableHeader: "<div class=\"info label budget-title\">選択可能キャンペーン</div>",
		selectionHeader: "<div class=\"info label budget-title\">選択済みキャンペーン</div>"
	});

	// クライアント選択
	$(document).on("change", "#client_id", function() {

		// 変更前クライアントの設定値をクリア
		$("#all_account_cancel").trigger("click");
		$("#all_campaign_cancel").trigger("click");

		document.form01.action="/sem/new/eagle/cpc/update";
		document.form01.target="";
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

		document.form01.action="/sem/new/eagle/cpc/update/index";
		document.form01.target="";
		document.form01.submit();
	});

	// 最新CPN取得
	$("#new_campaign").on("click", function() {

		// アカウント選択時のみ実行
		if (document.form01.account_id_list.value !== "") {

			document.form01.action="/sem/new/eagle/cpc/update/new_campaign";
			document.form01.target="";
			document.form01.action_type.value="new_campaign";
			document.form01.submit();
		}
	});

	// キャンペーン選択
	$(document).on("change", "#campaign_id_list", function() {

		// キャンペーン選択数を表示
		var count = 0;

		for (var i = 0; i < document.form01.campaign_id_list.length; i++) {

			if (document.form01.campaign_id_list[i].selected === true) {

				count++;
			}
		}

		document.getElementById("select_campaign_count").innerHTML = count;
	});

	// キャンペーン全選択
	$("#all_campaign_select").on("click", function() {

		var objArray = allSelectCommon("campaign_id_list");

		$("#campaign_id_list").multiSelect("select", objArray);
	});

	// キャンペーン全解除
	$("#all_campaign_cancel").on("click", function() {

		var objArray = allSelectCommon("campaign_id_list");

		$("#campaign_id_list").multiSelect("deselect", objArray);
	});

	// キャンペーン検索
	$("#campaign_search").on("click", function() {

		document.form01.action="/sem/new/eagle/cpc/update/index";
		document.form01.target="";

		// スクロール位置を取得
		getScrollPosition();

		document.form01.submit();
	});

	// 対象CPN DL
	$("#campaign_dl").on("click", function() {

		document.form01.action="/sem/new/eagle/cpc/update/campaign_dl";
		document.form01.target="";
		document.form01.submit();
	});

	// 掲載取得
	$("#get_structure").on("click", function() {

		// キャンペーン選択時のみ実行
		if (document.form01.new_campaign_not_disp.value === "checked"
				|| document.form01.campaign_id_list.value !== "") {

			document.form01.action="/sem/new/eagle/cpc/update/get_structure";
			document.form01.target="";
			document.form01.submit();
		}
	});

	// 掲載一覧絞込み
	$("#structure_filter").on("click", function() {

		document.form01.action="/sem/new/eagle/cpc/update/index";
		document.form01.target="";
		document.form01.action_type.value = "structure_filter";

		// スクロール位置を取得
		getScrollPosition();

		document.form01.submit();
	});

	// 掲載一覧DL
	$("#structure_dl").on("click", function() {

		document.form01.action="/sem/new/eagle/cpc/update/structure_dl";
		document.form01.target="";
		document.form01.submit();
	});

	// 掲載再取得
	$("#reget_structure").on("click", function() {

		document.form01.action="/sem/new/eagle/cpc/update/index";
		document.form01.target="";

		// スクロール位置を取得
		getScrollPosition();

		document.form01.submit();
	});

	// 入稿
	$("#edit").on("click", function() {

		document.form01.action_type.value = "edit";
		editCommon();
	});

	// CPC一括変更
	$("#cpc_bulk_change").on("click", function() {

		// 掲載一覧の絞込みが行われている場合、クリアする
		var cpc_bulk_change_exe_flg = true;
		var structure_filter_exe_flg = false;

		if (document.form01.account_filter_text.value !== ""
				|| document.form01.campaign_filter_text.value !== ""
				|| document.form01.adgroup_filter_text.value !== ""
				|| document.form01.matchtype_filter.value !== "") {

			cpc_bulk_change_exe_flg = confirm("CPC一括変更実行前に絞込み条件をクリアします。");
			structure_filter_exe_flg = true;
		}

		if (cpc_bulk_change_exe_flg) {

			// 掲載一覧を全件取得
			if (structure_filter_exe_flg) {

				document.form01.account_filter_text.value = "";
				document.form01.campaign_filter_text.value = "";
				document.form01.adgroup_filter_text.value = "";
				document.form01.matchtype_filter.value = "";

				$("#structure_filter").trigger("click");
			}

			// CPC一括変更を呼び出す
			window.open("about:blank", "cpc_bulk_change", "width=1250, height=700, scrollbars=1, fullscreen=0");

			document.form01.action="/sem/new/eagle/cpc/update/cpc_bulk_change";
			document.form01.target="cpc_bulk_change";
			document.form01.action_type.value="cpc_bulk_change";
			document.form01.submit();
		}
	});

	// CPC一律変更
	$("#cpc_evenness_change").on("click", function() {

		window.open("about:blank", "cpc_evenness_change", "width=900, height=500, scrollbars=1, fullscreen=0");

		document.form01.action="/sem/new/eagle/cpc/update/cpc_evenness_change";
		document.form01.target="cpc_evenness_change";
		document.form01.action_type.value="cpc_evenness_change";
		document.form01.submit();
	});

	// CPC変更内容DL
	$("#cpc_change_dl").on("click", function() {

		document.form01.action_type.value = "cpc_change_dl";
		editCommon();
	});

	// CPC変更内容クリア
	$("#cpc_change_cancel").on("click", function() {

		var structureListCount = document.getElementById("structure_list").rows.length;

		for (i = 2; i < structureListCount; i++) {

			var cpcMaxPcCell = document.getElementById("structure_list").rows[i].cells[ADG_CPC_MAX_PC_CELL_NUM];
			var cpcMaxSpCell = document.getElementById("structure_list").rows[i].cells[ADG_CPC_MAX_SP_CELL_NUM];
			var bidModifierCell = document.getElementById("structure_list").rows[i].cells[ADG_BID_MODIFIER_CELL_NUM];
			var nowCpcPc = document.getElementById("structure_list").rows[i].cells[NOW_ADG_CPC_MAX_PC_CELL_NUM].innerHTML;
			var nowCpcSp = document.getElementById("structure_list").rows[i].cells[NOW_ADG_CPC_MAX_SP_CELL_NUM].innerHTML;
			var nowBidModifier = document.getElementById("structure_list").rows[i].cells[NOW_ADG_BID_MODIFIER_CELL_NUM].innerHTML;

			cpcMaxPcCell.innerHTML = nowCpcPc;
			cpcMaxSpCell.innerHTML = nowCpcSp;
			bidModifier = "";

			if (nowBidModifier !== "") {

				bidModifier = Math.round((nowBidModifier - 1) * 100);
			}

			bidModifierCell.innerHTML = bidModifier + "%";

			cpcMaxPcCell.style.color = "dimgray";
			cpcMaxSpCell.style.color = "dimgray";
			bidModifierCell.style.color = "dimgray";
		}
	});

	// ページ読み込み時に実行
	$("#account_id_list").trigger("change");
	$("#campaign_id_list").trigger("change");
});
