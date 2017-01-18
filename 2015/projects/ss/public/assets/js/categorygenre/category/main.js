var clients = location.pathname.split('/');
var c_client_id = clients[clients.length-1];
var service_name = 'categorygenre/category/entrance';
var paths = location.pathname.split(service_name);
var c_path = paths[0];
var wn = '.modal';
$(document).ready(function() {
	$('.close,.modalBK').click(function(){
		$(wn).hide();
	});

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

	$(document).on('click', 'div.js-edit-category', function(){
		$("#loading").show();
		var category_id = $(this).attr("data-item-id");
		var category_elem = $(this).attr("data-item-elem");

		var in_check_flg = viewCheck();
		if (in_check_flg == false) {
		  return;
		}
		if(!category_id){
			var action_type = "new";
		}else{
			var action_type = "view";
		}

		var genre_id = $("select[name='select_genre_id'] option:selected").val();
		var category_elem = $("input[name='select_category_elem']:checked").val();

		$.ajax({
			type: 'post',
			url: c_path+"categorygenre/category/entrance/form/"+c_client_id,
			data: {genre_id: genre_id, category_elem: category_elem, action_type: action_type, category_id: category_id},
			success: function(data) {
				if (data !== false) {
				$('#popup_base').html(data);
				$(wn).addClass('active');
				$("#loading").hide();
				}
			}
		});
	});

	$(document).on('click', 'div.js-del-category', function(){
		$("#loading").show();

		var category_id = $(this).attr("data-item-id");
		var category_elem = $(this).attr("data-item-elem");
		if(!category_id || !category_elem){
			alert(カテゴリ情報が不正です);
			return false;
		}
		var category_name = $(this).attr("data-item-name");
		var delete_check = window.confirm("※注意※\n紐付くカテゴリ設定内容(大・中・小すべて)も削除されます。\n(削除されるカテゴリ自体は選択された種別のみです)\n削除すると元には戻せませんが、よろしいですか？\n\nカテゴリ名："+category_name);
		if(delete_check){
		    $.ajax({
		        url: "/sem/new/categorygenre/category/entrance/del/"+c_client_id,
		        type: "post",
		        data: {category_id: category_id, category_elem: category_elem},
		        cache       : false,
		    });
		    alert('カテゴリを削除しました');
		    loadTable();
		}else{
			$("#loading").hide();
			alert('カテゴリ削除を中止しました');
			return false;
		}
	});

});

//一覧表示時入力チェック
function viewCheck(){
	var check_flg = 0;

	var genre_id = $("select[name='select_genre_id'] option:selected").val();
	if(!genre_id){
		alert("カテゴリカテゴリジャンル名を選択してください");
		check_flg = 1;
		return false;
	}

	var category_elem = $("input[name='select_category_elem']:checked").val();
	if(!category_elem){
		alert("カテゴリ種別を選択してください");
		check_flg = 1;
		return false;
	}
}

function viewCategoryList(){
	// 対象CPN一覧表示ボタン押下時のアクション
	var in_check_flg = viewCheck();
	if (in_check_flg == false) {
	  return;
	}
	$("#loading").show();

	loadTable();
}

function loadTable(){
	var genre_id = $("select[name='select_genre_id'] option:selected").val();
	var category_elem = $("input[name='select_category_elem']:checked").val();

	$(".set_table").load(
		//カテゴリ一覧読み込み
		c_path+"categorygenre/category/entrance/table/"+c_client_id,
		{genre_id: genre_id, category_elem: category_elem},
		function(){
			$("#category_list").dataTable({
				"aaSorting": [ [2,'asc'] ],	// デフォルトソート:並び順
				"bAutoWidth": false,
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

function modBulkCategory(){
	$("#loading").show();

	var in_check_flg = viewCheck();
	if (in_check_flg == false) {
	  return;
	}

	var genre_id = $("select[name='select_genre_id'] option:selected").val();
	var category_elem = $("input[name='select_category_elem']:checked").val();

	$.ajax({
		type: 'post',
		url: c_path+"categorygenre/category/entrance/bulkform/"+c_client_id,
		data: {genre_id: genre_id, category_elem: category_elem},
		success: function(data) {
			if (data !== false) {
			$('#popup_base').html(data);
			$(wn).addClass('active');
			$("#loading").hide();
			}
		}
	});
}

function editCategory(category_id){
	$("#loading").show();

	category_id = category_id || null;

	var name = $('.category_name').val();
	if(!name){
		alert("カテゴリ名を入力してください");
		return;
	}

	var genre_id = $('.genre_id').val();
	var category_elem = $('.category_elem').val();
	var memo = $('.category_memo').val();
	var memo = $('.category_memo').val();
	var sort_order = $("select[name='sort_order'] option:selected").val();

	$.ajax({
		type: 'post',
		url: c_path+"categorygenre/category/check/namecheck/"+c_client_id+".json",
		dataType : 'json',
		data: {genre_id: genre_id, category_id: category_id, category_elem: category_elem, category_name: name},
		success: function(res){
			if(res){
				alert(res);
				return false;
			}else{
				$.ajax({
					type: 'post',
					url: c_path+"categorygenre/category/entrance/edit/"+c_client_id,
					data: {genre_id: genre_id, category_elem: category_elem, category_id: category_id, category_name: name, category_memo: memo, sort_order: sort_order},
				});
				$(wn).removeClass('active');
				loadTable();
				alert("更新完了しました");
			}
		}
	});
}

function editBulkCategory(){
	$("#loading").show();

	var name_list = $('.category_name_list').val();
	if(!name_list){
		alert("カテゴリ名を入力してください");
		return;
	}
	var genre_id = $('.genre_id').val();
	var category_elem = $('.category_elem').val();

	$.ajax({
		type: 'post',
		url: c_path+"categorygenre/category/check/namecheckbulk/"+c_client_id+".json",
		dataType : 'json',
		data: {genre_id: genre_id, category_elem: category_elem, category_name_list: name_list},
		success: function(res){
			if(res){
				alert(res.replace(/<br>/g,'\n'));
				return false;
			}else{
				$.ajax({
					type: 'post',
					url: c_path+"categorygenre/category/entrance/editbulk/"+c_client_id,
					data: {category_name_list: name_list, genre_id: genre_id, category_elem: category_elem},
				});
				loadTable();
				$(wn).removeClass('active');
				alert("更新完了しました");
			}
		}
	});
}