function check_date(cdate) {
	var y = cdate.substring(0, 4);
	var m = cdate.substring(5, 7);
	var d = cdate.substring(8, 10);
	//入力日付チェック
	var di = new Date(y, m - 1, d);
	if (!(di.getFullYear() == y && di.getMonth() == m - 1 &&
			 di.getDate() == d) || 
				!(cdate.length == 10)) {
		return false;
	}
	return true;
}

function searchReportData(){
	var view_fday = document.form01.from_day.value;
	var view_tday = document.form01.to_day.value;
	if (document.form01.client_id.value == "") {
		alert("クライアントを選択してください。");
		return;
	}

	if ($('#child_' + $("#parent").val()).val() == "") {
		alert("クライアント詳細を選択してください。");
		return;
	}
	document.form01.client_class_id.value = $('#child_' + $("#parent").val()).val();
	if (view_fday == "") {
		alert("集計日を選択してください。");
		return;
	}
	if (check_date(view_fday) == false) {
		alert("集計日は（yyyy/mm/dd）形式で入力してください。");
		return;
	}
	if ($("#day_type").val() == 'fromto') {
		if (view_tday == "") {
			alert("集計日を選択してください。");
			return;
		}
		if (check_date(view_tday) == false) {
			alert("集計日は（yyyy/mm/dd）形式で入力してください。");
			return;
		}
		var target_fday = new Date(view_fday);
		var target_tday = new Date(view_tday);
		var target_span = Math.ceil((target_tday-target_fday)/(60*60*24*1000));
		if (target_span >= 31) {
			alert("集計期間は31日間以内で入力してください。");
			return;
		}
	}
	document.form01.action="/sem/new/competitor/share/report";
	document.form01.action_type.value="search";

	document.form01.submit();
}

function doBackAction(){
	var hide_fday = document.form02.from_day.value;
	var hide_tday = document.form02.to_day.value;
	if (document.form02.client_class_id.value == "") {
		alert("集計対象を選択してください。");
		return;
	}
	if (document.form02.from_day.value == "") {
		alert("集計日を選択してください。");
		return;
	}
	if (check_date(hide_fday) == false) {
		alert("集計日は（yyyy/mm/dd）形式で入力してください。");
		return;
	}
	if (hide_fday == "") {
		alert("集計日を選択してください。");
		return;
	}
	if (hide_tday == false) {
		alert("集計日は（yyyy/mm/dd）形式で入力してください。");
		return;
	}
	var target_fday = new Date(hide_fday);
	var target_tday = new Date(hide_tday);
	var target_span = Math.ceil((target_tday-target_fday)/(60*60*24*1000));
	if (target_span >= 31) {
		alert("集計期間は31日間以内で入力してください。");
		return;
	}
	document.form02.action="/sem/new/competitor/share/report";
	document.form02.action_type.value="search";

	document.form02.submit();
}

function doDetailAction(target_param){
	document.form02.action="/sem/new/competitor/share/report?"+target_param;
	document.form02.action_type.value="detail";

	document.form02.submit();
}

function doExportAction(export_name, link_key){
	document.form02.action="/sem/new/competitor/share/reportexport/"+export_name+link_key;
	document.form02.action_type.value="export";
	document.form02.submit();
	document.form02.action_type.value="";
}

function doSortAction(sort_key, sort_type){
	document.form02.action="/sem/new/competitor/share/report";
	document.form02.action_type.value="search";
	document.form02.sort_key.value=sort_key;
	document.form02.sort_type.value=sort_type;

	document.form02.submit();
}

//表の表示を選択する
function toggle (targetId, imgId) {
	if ( document.getElementById ) {
		target = document.getElementById ( targetId );
		if ( target.style.display == "none" ) {
			$(target).slideToggle(500);
			document.getElementById(imgId).innerHTML ='結果を全画面表示';
		}else{
			$(target).slideToggle(500);
			document.getElementById(imgId).innerHTML ='検索条件を表示する';
		}
	}
}


$(document).ready(function(){
	$.datepicker.setDefaults( $.datepicker.regional[ "ja" ] );

	$("#from_day").datepicker({dateFormat: "yy/mm/dd"});
	$("#to_day").datepicker({dateFormat: "yy/mm/dd"});


	var showChildSelect = function() {
		var target = $('#child_' + $("#parent").val());
		target.removeClass('hide').prop('disabled', false);
	}
	showChildSelect();

	$("#parent").change(function () {
		var childs = $("select.client-class");
		childs.addClass('hide').prop('disabled', true);
		showChildSelect();
	});

	$("#day_type").change(function () {
		var term_input = $('.js-to-day-wrap');
		if ($("#day_type").val() == 'fromto') {
			term_input.removeClass('hide');
		} else {
			term_input.addClass('hide');
		}
	});

	$("#search_btn").on("click", function() {
		searchReportData();
	});


});

