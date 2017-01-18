// 実行用
function doAction(){
	// document.form01.action = "/sem/new/query/execute/start";
	// document.form01.submit();

	$.ajax({
		type: 'POST',
		url: "/sem/new/query/execute/start",
		data: {category_name:document.form01.category_name.value},
	}).done(function(data){
		alert(data);
	}).fail(function(data){
		alert(data);
	});
}

// ドロップダウン非同期処理
$("#category_name").change(function(){
	$.ajax({
		type: 'POST',
		url: "/sem/new/query/update/keywordList",
		data: {category_name:document.form01.category_name.value},
		dataType: 'html'
	}).done(function(data){
		$("#keyword_map").html(data);
	}).fail(function(data){
		$("#keyword_map").html('communication error');
	});
});

$(document).ready(function(){
	document.form01.category_name.focus();

	var nav_id = 'execute';
	$("ul.contents-nav li").removeClass("active");
	$("#query_" + nav_id).addClass("active");
});