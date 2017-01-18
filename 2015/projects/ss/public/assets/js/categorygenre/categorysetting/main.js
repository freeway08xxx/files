var clients = location.pathname.split('/');
var c_client_id = clients[clients.length-1];
var service_name = 'categorygenre/categorysetting/entrance';
var paths = location.pathname.split(service_name);
var c_path = paths[0];
var wn = '.modal';
$(document).ready(function() {
	$('.close,.modalBK').click(function(){
		$(wn).hide();
	});

	//履歴タブ読み込み
	loadHistoryTable();

	$("#loading").hide();

	//クライアント選択
	$("#client_id").select2({ width:"650px" });
	$(document).on("change", "#client_id", function() {
		if ($('#client_id').val().length > 0) {
			href=c_path+service_name+"/setting/"+$('#client_id').val();
			location.href=href;
		}
	});

	//サイドメニューのリンクにクライアントID追加
	if ($('#client_id').val().length > 0) {
		$(".sidebar-set-client a").each(function() {
			var obj = $(this);
			var link = obj.attr("href");
			obj.attr("href",link+"/setting/"+$('#client_id').val())
		});
	}

	//カテゴリ一覧
	$("#update_category_big_id").select2({ width:"350px" });
	$("#update_category_middle_id").select2({ width:"350px" });
	$("#update_category_id").select2({ width:"350px" });

	$("#account_id_list").multiSelect({
		//アカウント選択
		selectableHeader: "<div class=\"info label category-title\">選択可能アカウント</div>",
		selectionHeader: "<div class=\"danger label category-title\">選択済みアカウント</div>",
	});

	// クリアボタン押下時
	$("#js-clear").on("click", function() {
		href=c_path+service_name+"/setting/"+$('#client_id').val();
		location.href=href;
	});

	// 対象CPN一覧DLボタン押下時のアクション
	$("#download_category_setting_btn").on("click", function() {
		var in_check_flg = inputCategorySettingCheck();
		if (in_check_flg == false) {
		  return;
		}
		var form01 = $('#form01');
		var formdata = false;
		if (window.FormData){
		  formdata = new FormData(form01[0]);
		}

		//表示件数チェック
		$.ajax({
			url: "/sem/new/categorygenre/categorysetting/check/countcheck/"+c_client_id+".json",
			type: "post",
			data: formdata ? formdata : form01.serialize(),
			cache       : false,
			contentType : false,
			processData : false,
			success: function(res) {
				//最大件数超過時はアラート表示
				if(res !== "false"){
					$("#loading").hide();
					alert(res);
					return;
				}else{
					$.ajax({
						url: "/sem/new/categorygenre/categorysetting/export/download/"+c_client_id,
						type: "post",
						data: formdata ? formdata : form01.serialize(),
						cache       : false,
						contentType : false,
						processData : false,
					});
					alert("ダウンロード開始");
					window.location.reload();
				}
			}
		});
	});

	// 設定シートアップロードボタン押下時のアクション
	$("#upload_category_setting_btn").on("click", function() {
		var in_check_flg = uploadCategorySettingCheck();
		if (in_check_flg == false) {
		  return;
		}

		var genre_name = $("select[name='update_genre_id'] option:selected").text();
		if(!window.confirm("カテゴリジャンル名："+genre_name+"\n\n設定シートをアップロードします。よろしいですか？")){
			return;
		}

		$("#loading").show();

		var form01 = $('#form01');
		var formdata = false;
		if (window.FormData){
			formdata = new FormData(form01[0]);
		}

		//ファイルアップロード
		$.ajax({
			url: "/sem/new/categorygenre/categorysetting/check/filecheck/"+c_client_id+".json",
			type: "post",
			data: formdata ? formdata : form01.serialize(),
			cache       : false,
			contentType : false,
			processData : false,
			success: function(res) {
				$("#loading").hide();

				//履歴IDと保存ファイル名を取得し、formに反映
				var res_data = res.split(",");
				$(':hidden[name="upload_history_id"]').val(res_data[0]);
				if(res_data[1]){
					$(':hidden[name="upload_file_name"]').val(res_data[1]);
				}

				var form01 = $('#form01');
				var formdata = false;
				if (window.FormData){
					formdata = new FormData(form01[0]);
				}

				//カテゴリ設定
				$.ajax({
					url: "/sem/new/categorygenre/categorysetting/export/upload/"+c_client_id,
					type: "post",
					data: formdata ? formdata : form01.serialize(),
					cache       : false,
					contentType : false,
					processData : false,
				});
				alert("アップロード開始");
				window.location.reload();
			}
		});

	});

	//ダウンロード用 設定種別切替
	$(document).on("change", ".select_element_type_id", function() {
		var element_type_id = $("select[name='select_element_type_id'] option:selected").val();
		$(".campaign_search").hide();
		$(".ad_group_search").hide();
		$(".keyword_search").hide();
		$(".url_search").hide();
		if(element_type_id == 2){
			$(".campaign_search").show();
		}else if(element_type_id == 3){
			$(".campaign_search").show();
			$(".ad_group_search").show();
		}else if(element_type_id == 4){
			$(".campaign_search").show();
			$(".ad_group_search").show();
			$(".keyword_search").show();
			$(".url_search").show();
		}
	});

	// アカウント検索ボタン押下時のアクション
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

	// アカウント検索クリアボタン押下時のアクション
	$("#account_search_clear_btn").on("click", function() {
		document.form01.account_search.checked=false;
		document.form01.account_search_type.value="0";
		document.form01.account_search_list.value="";
	});

	// アカウント全選択ボタン押下時のアクション
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

	// アカウント全解除ボタン押下時のアクション
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

});

//設定シートDL・表示時入力チェック
function inputCategorySettingCheck(){
	var check_flg = 0;

	var genre_id = $("select[name='select_genre_id'] option:selected").val();
	if(!genre_id || genre_id == "--"){
		alert("カテゴリジャンル名を選択してください");
		check_flg = 1;
		return false;
	}

	var element_type_id = $("select[name='select_element_type_id'] option:selected").val();
	if(!element_type_id || element_type_id == 0){
		alert("カテゴリ設定単位を選択してください");
		check_flg = 1;
		return false;
	}

	var account_count = 0;
	for (var i = 0; i < document.form01.account_id_list.length; i++) {
		if (document.form01.account_id_list[i].selected == true) {
			account_count++;
		}
	}
	if(account_count < 1){
		alert("アカウントを選択してください");
		check_flg = 1;
		return false;
	}
}

//設定シートアップロード入力チェック
function uploadCategorySettingCheck(){
	var check_flg = 0;

	var genre_id = $("select[name='update_genre_id'] option:selected").val();
	if(!genre_id || genre_id == "--"){
		alert("カテゴリジャンル名を選択してください");
		check_flg = 1;
		return false;
	}

	var file = $('#upload_file')[0].files[0];
	if(file == null) {
		alert("アップロードファイルを選択してください");
		check_flg = 1;
		return false;
	}else{
		var file_size = file.size;
		if(file_size > 104857600){
			alert("ファイルサイズが100MBを超えています。ファイルを分割して実行してください");
			check_flg = 1;
			return false;
		}
	}
	if(!$('#upload_file').val().match(/\.csv$/)){
		alert("拡張子が異なります。CSV形式でアップロードしてください");
		check_flg = 1;
		return false;
	}
}

//カテゴリ解除の対象チェック
function deleteCategorySettingCheck() {
	var check_flg = 0;
	var elem_delete_flg = 0;

	//選択済み項目を取得
	$('.category_elem_list .odd').each(function(){
		elem_delete_flg = $(this).find("input[name='elem_delete_flg']:checked").val();
		if(elem_delete_flg == 1){
			return false;
		}
	});
	if(elem_delete_flg != 1){
		$('.category_elem_list .even').each(function(){
			elem_delete_flg = $(this).find("input[name='elem_delete_flg']:checked").val();
			if(elem_delete_flg == 1){
				return false;
			}
		});
	}
	if(elem_delete_flg != 1){
		alert("解除対象のデータにチェックしてください");
		check_flg = 1;
		return false;
	}
}

//カテゴリ一覧表示・更新・解除
function viewCategorySettingList(action_target){
	// 対象CPN一覧表示ボタン押下時のアクション
	var genre_id = $("select[name='select_genre_id'] option:selected").val();
	var element_type_id = $("select[name='select_element_type_id'] option:selected").val();
	var element_type_name = $("select[name='select_element_type_id'] option:selected").text();

	var in_check_flg = inputCategorySettingCheck();
	if (in_check_flg == false) {
	  return;
	}
	if(action_target == "delete"){
		var in_check_flg = deleteCategorySettingCheck();
		if (in_check_flg == false) {
		  return;
		}
	}
	if(action_target=="update" && !window.confirm(element_type_name+"単位でカテゴリ設定を行います。\n既に設定されているカテゴリ設定は上書きされます。")){
		return;
	}
	if(action_target=="delete" && !window.confirm("カテゴリ設定を解除します。")){
		return;
	}

	$("#loading").show();

	var form01 = $('#form01');
	var formdata = false;
	if (window.FormData){
	  formdata = new FormData(form01[0]);
	}

	if(action_target=="update" || action_target=="delete"){
		//設定単位で取得列が異なるためそれぞれセット
		var b_elem_no = 4; //アカウント
		var elem_no = 5;

		if(element_type_id > 1){ //キャンペーン
			var cpn_no = 4;
			var b_elem_no = 7;
			var elem_no = 8;

			if(element_type_id > 2){ //広告グループ
				var adg_no = 7;
				var b_elem_no = 9;
				var elem_no = 10;

				if(element_type_id > 3){ //キーワード
					var kw_no = 9;
					var b_elem_no = 12;
					var elem_no = 13;
				}
			}
		}

		var category_data_list = new Array();

		//選択済み項目を取得
		$('.category_elem_list .odd').each(function(){
			if(action_target == "update"
			&& ($(this).find("select[name='update_category_big_id'] option:selected").text() == "[複数設定あり]"
			|| $(this).find("select[name='update_category_middle_id'] option:selected").text() == "[複数設定あり]"
			|| $(this).find("select[name='update_category_id'] option:selected").text() == "[複数設定あり]")){
				return true;
			}

			category_data_list.push({
				no: $(this).closest('tr').children('td:eq(0)').text(),
				media_name: $(this).closest('tr').children('td:eq(1)').text(),
				account_id: $(this).closest('tr').children('td:eq(2)').text(),
				campaign_id: $(this).closest('tr').children('td:eq('+cpn_no+')').text(),
				ad_group_id: $(this).closest('tr').children('td:eq('+adg_no+')').text(),
				keyword_id: $(this).closest('tr').children('td:eq('+kw_no+')').text(),
				before_category_type_name: $(this).closest('tr').children('td:eq('+b_elem_no+')').text(),
				category_type_name: $(this).closest('tr').children('td:eq('+elem_no+')').text(),
				category_big_id: $(this).find("select[name='update_category_big_id'] option:selected").val(),
				category_big_name: $(this).find("select[name='update_category_big_id'] option:selected").text(),
				category_middle_id: $(this).find("select[name='update_category_middle_id'] option:selected").val(),
				category_middle_name: $(this).find("select[name='update_category_middle_id'] option:selected").text(),
				category_id: $(this).find("select[name='update_category_id'] option:selected").val(),
				category_name: $(this).find("select[name='update_category_id'] option:selected").text(),
				elem_delete_flg: $(this).find("input[name='elem_delete_flg']:checked").val()
			});
		});
		$('.category_elem_list .even').each(function(){
			if(action_target == "update"
				&& ($(this).find("select[name='update_category_big_id'] option:selected").text() == "[複数設定あり]"
				|| $(this).find("select[name='update_category_middle_id'] option:selected").text() == "[複数設定あり]"
				|| $(this).find("select[name='update_category_id'] option:selected").text() == "[複数設定あり]")){
					return true;
			}

			category_data_list.push({
				no: $(this).closest('tr').children('td:eq(0)').text(),
				media_name: $(this).closest('tr').children('td:eq(1)').text(),
				account_id: $(this).closest('tr').children('td:eq(2)').text(),
				campaign_id: $(this).closest('tr').children('td:eq('+cpn_no+')').text(),
				ad_group_id: $(this).closest('tr').children('td:eq('+adg_no+')').text(),
				keyword_id: $(this).closest('tr').children('td:eq('+kw_no+')').text(),
				before_category_type_name: $(this).closest('tr').children('td:eq('+b_elem_no+')').text(),
				category_type_name: $(this).closest('tr').children('td:eq('+elem_no+')').text(),
				category_big_id: $(this).find("select[name='update_category_big_id'] option:selected").val(),
				category_big_name: $(this).find("select[name='update_category_big_id'] option:selected").text(),
				category_middle_id: $(this).find("select[name='update_category_middle_id'] option:selected").val(),
				category_middle_name: $(this).find("select[name='update_category_middle_id'] option:selected").text(),
				category_id: $(this).find("select[name='update_category_id'] option:selected").val(),
				category_name: $(this).find("select[name='update_category_id'] option:selected").text(),
				elem_delete_flg: $(this).find("input[name='elem_delete_flg']:checked").val()
			});
		});
	}

	//表示件数チェック
	$.ajax({
		url: "/sem/new/categorygenre/categorysetting/check/countcheck/"+c_client_id+".json",
		type: "post",
		data: formdata ? formdata : form01.serialize(),
		cache       : true,
		contentType : false,
		processData : false,
		success: function(res) {
			//最大件数超過時はアラート表示
			if(res !== "false"){
				$("#loading").hide();
				alert(res);
				return;
			}else{
				//更新ボタン押下時
				if(action_target=="update"){
					$.ajax({
						url: "/sem/new/categorygenre/categorysetting/update/update/"+c_client_id+".json",
						type: "post",
						data: { category_data_list: category_data_list, genre_id: genre_id },
						success: function(res) {
							//更新内容にエラーがあればアラート表示
							if(res){
								$("#loading").hide();
								alert(res.replace(/<br>/g,'\n'));
								return;
							}else{
								loadCategoryTable(); //画面更新
							}
						}
					});
				//解除ボタン押下時
				}else if(action_target=="delete"){
					$.ajax({
						url: "/sem/new/categorygenre/categorysetting/update/delete/"+c_client_id+".json",
						type: "post",
						data: { category_data_list: category_data_list, genre_id: genre_id },
						success: function(res) {
							//更新内容にエラーがあればアラート表示
							if(res){
								$("#loading").hide();
								alert(res.replace(/<br>/g,'\n'));
								return;
							}else{
								loadCategoryTable(); //画面更新
							}
						}
					});
				}else{
					loadCategoryTable(); //画面更新
				}
			}
		}
	});
}

//カテゴリ設定一覧読み込み
function loadCategoryTable(){

	var genre_id = $("select[name='select_genre_id'] option:selected").val();
	var no_setting_flg = $("input[name='no_setting_flg']:checked").val();
	var element_type_id = $("select[name='select_element_type_id'] option:selected").val();

	var campaign_search_like = $("input[name='campaign_search_like']:checked").val();
	var except_campaign_search = $("input[name='except_campaign_search']:checked").val();
	var campaign_search_type = $("input[name='campaign_search_type']:checked").val();
	var campaign_search_text = $(".campaign_search_text").val();

	var ad_group_search_like = $("input[name='ad_group_search_like']:checked").val();
	var except_ad_group_search = $("input[name='except_ad_group_search']:checked").val();
	var ad_group_search_type = $("input[name='ad_group_search_type']:checked").val();
	var ad_group_search_text = $(".ad_group_search_text").val();

	var keyword_search_like = $("input[name='keyword_search_like']:checked").val();
	var except_keyword_search = $("input[name='except_keyword_search']:checked").val();
	var keyword_search_type = $("input[name='keyword_search_type']:checked").val();
	var keyword_search_text = $(".keyword_search_text").val();

	var url_search_like = $("input[name='url_search_like']:checked").val();
	var except_url_search = $("input[name='except_url_search']:checked").val();
	var url_search_type = $("input[name='url_search_type']:checked").val();
	var url_search_text = $(".url_search_text").val();

	var category_search_like = $("input[name='category_search_like']:checked").val();
	var except_category_search = $("input[name='except_category_search']:checked").val();
	var category_search_type = $("input[name='category_search_type']:checked").val();
	var category_search_text = $(".category_search_text").val();

	var add_account_id_list = new Array();
	for (var i = 0; i < document.form01.account_id_list.length; i++) {
		if (document.form01.account_id_list[i].selected == true) {
			add_account_id_list.push(document.form01.account_id_list[i].value);
		}
	}

	//テーブル読み込み
	$(".set_table").load(
			c_path+"categorygenre/categorysetting/entrance/table/"+c_client_id,
			{
			  client_id: c_client_id,
			  genre_id: genre_id,
			  no_setting_flg: no_setting_flg,
			  element_type_id: element_type_id,
			  campaign_search_like: campaign_search_like,
			  except_campaign_search: except_campaign_search,
			  campaign_search_type: campaign_search_type,
			  campaign_search_text: campaign_search_text,
			  ad_group_search_like: ad_group_search_like,
			  except_ad_group_search: except_ad_group_search,
			  ad_group_search_type: ad_group_search_type,
			  ad_group_search_text: ad_group_search_text,
			  keyword_search_like: keyword_search_like,
			  except_keyword_search: except_keyword_search,
			  keyword_search_type: keyword_search_type,
			  keyword_search_text: keyword_search_text,
			  url_search_like: url_search_like,
			  except_url_search: except_url_search,
			  url_search_type: url_search_type,
			  url_search_text: url_search_text,
			  category_search_like: category_search_like,
			  except_category_search: except_category_search,
			  category_search_type: category_search_type,
			  category_search_text: category_search_text,
			  account_id_list: add_account_id_list,
			},
		function(){

			//dataTablesのソート・検索の除外列があるため、設定単位ごとに分岐
			if(element_type_id == 1){ //アカウント
				$("#category_elem_combination").dataTable({
					"bDeferRender": true,
					"bDestroy": true,
					"bStateSave": true,
					"iDisplayLength" : 25,
					"oLanguage": {
						"sLengthMenu": "表示件数 _MENU_",
						"sZeroRecords":  "該当データが存在しません",
						"sInfo": "_START_ ～ _END_件 / 全_TOTAL_件",
						"sInfoEmpty":    " 該当データが存在しません",
						"sInfoFiltered": "（全 _MAX_ 件より抽出）",
						"sInfoPostFix":  "",
						"sSearch":       '※大・中・小カテゴリは検索出来ません<br><i class="icon icon-search"></i>',
						"sUrl":  "",
						"oPaginate": {
						    "sFirst":    "先頭",
						    "sPrevious": "前",
						    "sNext":     "次",
						    "sLast":     "最終"
					},
					"sLengthMenu": '表示件数 <select>'+
					    '<option value="25">25</option>'+
					    '<option value="50">50</option>'+
					    '<option value="200">200</option>'+
					    '<option value="-1">All</option>'+
					    '</select>  ',
					},
					//一括切替プルダウンがある列はソート無し
					"aoColumnDefs": [
					    { "bSortable": false, "aTargets": [ 6, 7, 8, 9 ] }
					],
					//大・中・小カテゴリ、解除チェックボックスは検索対象列から除外
					"aoColumns": [
					    {"bSearchable": true },
					    {"bSearchable": true },
					    {"bSearchable": true },
					    {"bSearchable": true },
					    {"bSearchable": true },
					    {"bSearchable": true },
					    {"bSearchable": false},
					    {"bSearchable": false},
					    {"bSearchable": false},
					    {"bSearchable": false},
					]
				});
			}else if(element_type_id == 2){ //キャンペーン
				$("#category_elem_combination").dataTable({
					"bDeferRender": true,
					"bDestroy": true,
					"bStateSave": true,
					"iDisplayLength" : 25,
					"oLanguage": {
						"sLengthMenu": "表示件数 _MENU_",
						"sZeroRecords":  "該当データが存在しません",
						"sInfo": "_START_ ～ _END_件 / 全_TOTAL_件",
						"sInfoEmpty":    " 該当データが存在しません",
						"sInfoFiltered": "（全 _MAX_ 件より抽出）",
						"sInfoPostFix":  "",
						"sSearch":       '※大・中・小カテゴリは検索出来ません<br><i class="icon icon-search"></i>',
						"sUrl":  "",
						"oPaginate": {
						    "sFirst":    "先頭",
						    "sPrevious": "前",
						    "sNext":     "次",
						    "sLast":     "最終"
					},
					"sLengthMenu": '表示件数 <select>'+
					    '<option value="25">25</option>'+
					    '<option value="50">50</option>'+
					    '<option value="200">200</option>'+
					    '<option value="-1">All</option>'+
					    '</select>  ',
					},
					//一括切替プルダウンがある列はソート無し
					"aoColumnDefs": [
					    { "bSortable": false, "aTargets": [ 9, 10, 11, 12 ] }
					],
					//大・中・小カテゴリ、解除チェックボックスは検索対象列から除外
					"aoColumns": [
					    {"bSearchable": true },
					    {"bSearchable": true },
					    {"bSearchable": true },
					    {"bSearchable": true },
					    {"bSearchable": true },
					    {"bSearchable": true },
					    {"bSearchable": true },
					    {"bSearchable": true },
					    {"bSearchable": true },
					    {"bSearchable": false},
					    {"bSearchable": false},
					    {"bSearchable": false},
					    {"bSearchable": false},
					]
				});
			}else if(element_type_id == 3){ //広告グループ
				$("#category_elem_combination").dataTable({
					"bDeferRender": true,
					"bDestroy": true,
					"bStateSave": true,
					"iDisplayLength" : 25,
					"oLanguage": {
						"sLengthMenu": "表示件数 _MENU_",
						"sZeroRecords":  "該当データが存在しません",
						"sInfo": "_START_ ～ _END_件 / 全_TOTAL_件",
						"sInfoEmpty":    " 該当データが存在しません",
						"sInfoFiltered": "（全 _MAX_ 件より抽出）",
						"sInfoPostFix":  "",
						"sSearch":       '※大・中・小カテゴリは検索出来ません<br><i class="icon icon-search"></i>',
						"sUrl":  "",
						"oPaginate": {
						    "sFirst":    "先頭",
						    "sPrevious": "前",
						    "sNext":     "次",
						    "sLast":     "最終"
					},
					"sLengthMenu": '表示件数 <select>'+
					    '<option value="25">25</option>'+
					    '<option value="50">50</option>'+
					    '<option value="200">200</option>'+
					    '<option value="-1">All</option>'+
					    '</select>  ',
					},
					//一括切替プルダウンがある列はソート無し
					"aoColumnDefs": [
					    { "bSortable": false, "aTargets": [ 11, 12, 13, 14 ] }
					],
					//大・中・小カテゴリ、解除チェックボックスは検索対象列から除外
					"aoColumns": [
					    {"bSearchable": true },
					    {"bSearchable": true },
					    {"bSearchable": true },
					    {"bSearchable": true },
					    {"bSearchable": true },
					    {"bSearchable": true },
					    {"bSearchable": true },
					    {"bSearchable": true },
					    {"bSearchable": true },
					    {"bSearchable": true },
					    {"bSearchable": true },
					    {"bSearchable": false},
					    {"bSearchable": false},
					    {"bSearchable": false},
					    {"bSearchable": false},
					]
				});
			}else if(element_type_id == 4){ //キーワード
				$("#category_elem_combination").dataTable({
					"bDeferRender": true,
					"bDestroy": true,
					"bStateSave": true,
					"iDisplayLength" : 25,
					"oLanguage": {
						"sLengthMenu": "表示件数 _MENU_",
						"sZeroRecords":  "該当データが存在しません",
						"sInfo": "_START_ ～ _END_件 / 全_TOTAL_件",
						"sInfoEmpty":    " 該当データが存在しません",
						"sInfoFiltered": "（全 _MAX_ 件より抽出）",
						"sInfoPostFix":  "",
						"sSearch":       '※大・中・小カテゴリは検索出来ません<br><i class="icon icon-search"></i>',
						"sUrl":  "",
						"oPaginate": {
						    "sFirst":    "先頭",
						    "sPrevious": "前",
						    "sNext":     "次",
						    "sLast":     "最終"
					},
					"sLengthMenu": '表示件数 <select>'+
					    '<option value="25">25</option>'+
					    '<option value="50">50</option>'+
					    '<option value="200">200</option>'+
					    '<option value="-1">All</option>'+
					    '</select>  ',
					},
					//一括切替プルダウンがある列はソート無し
					"aoColumnDefs": [
					    { "bSortable": false, "aTargets": [ 14, 15, 16, 17 ] }
					],
					//大・中・小カテゴリ、解除チェックボックスは検索対象列から除外
					"aoColumns": [
					    {"bSearchable": true },
					    {"bSearchable": true },
					    {"bSearchable": true },
					    {"bSearchable": true },
					    {"bSearchable": true },
					    {"bSearchable": true },
					    {"bSearchable": true },
					    {"bSearchable": true },
					    {"bSearchable": true },
					    {"bSearchable": true },
					    {"bSearchable": true },
					    {"bSearchable": true },
					    {"bSearchable": true },
					    {"bSearchable": true },
					    {"bSearchable": false},
					    {"bSearchable": false},
					    {"bSearchable": false},
					    {"bSearchable": false},
					]
				});
			}

			$(".set_top").hide();
			$(".set_table").show();
			$("#loading").hide();

			//戻るボタン押下時
			$(".back_category_setting").on("click", function() {
				if ($('#client_id').val().length > 0) {
					$(".set_table").hide();
					$(".set_top").show();
				}
			});

			//大カテゴリ一括切替
			$('select[name="js_category_big_id"]').on("change", function() {
				$("#loading").show();
				var category_big_id = $('select[name="js_category_big_id"] option:selected').val();
				$('.category_elem_list').each(function(){
					$(".update_category_big_id").val([category_big_id]);
				});
				$("#loading").hide();
			});

			//中カテゴリ一括切替
			$('select[name="js_category_middle_id"]').on("change", function() {
				$("#loading").show();
				var category_middle_id = $('select[name="js_category_middle_id"] option:selected').val();
				$('.category_elem_list').each(function(){
					$(".update_category_middle_id").val([category_middle_id]);
				});
				$("#loading").hide();
			});

			//小カテゴリ一括切替
			$('select[name="js_category_id"]').on("change", function() {
				$("#loading").show();
				var category_id = $('select[name="js_category_id"] option:selected').val();
				$('.category_elem_list').each(function(){
					$(".update_category_id").val([category_id]);
				});
				$("#loading").hide();
			});

			//設定解除一括チェック
			$('#elem_all_delete_flg').on('change', function() {
				$('input[name=elem_delete_flg]').prop('checked', this.checked);
			});
		}
	);
}

//作業履歴一覧読み込み
function loadHistoryTable(){
	$("#loading").show();

	//テーブル読み込み
	$(".set_history").load(
			c_path+"categorygenre/categorysetting/entrance/history/"+c_client_id,
			{},
		function(){
			$("#history_combination").dataTable({
			"aaSorting": [ [7,'desc'] ],	// デフォルトソート
			"bDeferRender": true,
			"bDestroy": true,
			"bStateSave": true,
			"iDisplayLength" : 25,
				"oLanguage": {
					"sLengthMenu": "表示件数 _MENU_",
					"sZeroRecords":  "該当データが存在しません",
					"sInfo": "_START_ ～ _END_件 / 全_TOTAL_件",
					"sInfoEmpty":    " 該当データが存在しません",
					"sInfoFiltered": "（全 _MAX_ 件より抽出）",
					"sInfoPostFix":  "",
					"sSearch":       '<i class="icon icon-search"></i>',
					"sUrl":	  "",
					"oPaginate": {
					    "sFirst":    "先頭",
					    "sPrevious": "前",
					    "sNext":     "次",
					    "sLast":     "最終"
				},
				"sLengthMenu": '表示件数 <select>'+
				    '<option value="25">25</option>'+
				    '<option value="50">50</option>'+
				    '<option value="100">100</option>'+
				    '<option value="-1">All</option>'+
				    '</select>  ',
				}
			});
			$("#loading").hide();
		}
	);
}

//対象アカウントを子ウィンドウで表示
function selectAccountIdCopy(items){
	// 子ウィンドウオープン
	var obj = window.open("","","width=600, height=300");
	obj.document.open();
	obj.document.writeln(items);
	obj.document.close();
}
