var clients = location.pathname.split('/');
var c_client_id = clients[clients.length-1];
var service_name = 'categorygenre/genre/entrance';
var paths = location.pathname.split(service_name);
var c_path = paths[0];
var wn = '.modal';
$(document).ready(function() {
	$('.close,.modalBK').click(function(){
		$(wn).hide();
	});

	$("#loading").hide();

	loadTable();

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

	$(document).on('click', 'div.js-del-genre', function(){
		$("#loading").show();

		var genre_id = $(this).attr("data-item-id");
		if(!genre_id){
			alert(カテゴリジャンル情報が不正です)
			return false;
		}
		var genre_name = $(this).attr("data-item-name");
		var delete_check = window.confirm("※注意※\n紐付くカテゴリ・カテゴリ設定内容(大・中・小すべて)も削除されます。\n削除すると元には戻せませんが、よろしいですか？\n\nカテゴリジャンル名："+genre_name);
		if(delete_check){
			$.ajax({
				url: "/sem/new/categorygenre/genre/entrance/del/"+c_client_id,
				type: "post",
				data: {genre_id: genre_id},
				cache       : false,
			});
			alert('カテゴリジャンルを削除しました');
			loadTable();
		}else{
			alert('カテゴリジャンル削除を中止しました');
			$("#loading").hide();
			return false;
		}
	});

});

function modGenre(genre_id){
	$("#loading").show();

	genre_id = genre_id || null;
	if(!genre_id){
		var action_type = "new";
	}else{
		var action_type = "view";
	}

	$.ajax({
		type: 'post',
		url: c_path+"categorygenre/genre/entrance/form/"+c_client_id,
		data: {genre_id: genre_id, action_type: action_type},
		success: function(data) {
			if (data !== false) {
			$('#popup_base').html(data);
			$(wn).addClass('active');
			$("#loading").hide();
			}
		}
	});
}

function editGenre(genre_id){
	$("#loading").show();

	var in_check_flg = editCheck();
	if (in_check_flg == false) {
	  return;
	}

	genre_id = genre_id || null;

	var name = $('.category_genre_name').val();
	var memo = $('.category_genre_memo').val();

	$.ajax({
		type: 'post',
		url: c_path+"categorygenre/genre/check/namecheck/"+c_client_id+".json",
		dataType : 'json',
		data: {genre_id: genre_id, category_genre_name: name},
		success: function(res){
			if(res){
				alert(res);
				return false;
			}else{
				$.ajax({
					type: 'post',
					url: c_path+"categorygenre/genre/entrance/edit/"+c_client_id,
					data: {genre_id: genre_id, category_genre_name: name, category_genre_memo: memo, client_id: c_client_id},
				});
				$(wn).removeClass('active');
				loadTable();
				alert("更新完了しました")
			}
		}
	});
}

//一覧表示時入力チェック
function editCheck(){
	var check_flg = 0;

	var name = $('.category_genre_name').val();
	if(!name){
		alert("カテゴリジャンル名を入力してください");
		check_flg = 1;
		return false;
	}
}

function loadTable(){
	$(".set_table").load(
		//カテゴリ一覧読み込み
		c_path+"categorygenre/genre/entrance/table/"+c_client_id,
		{},
		function(){
			$("#categorygenre_list").dataTable({
				"aaSorting": [ [4,'desc'] ],	// デフォルトソート:最終更新日時
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