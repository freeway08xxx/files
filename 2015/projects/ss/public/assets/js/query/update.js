// insert用
function doActionMI(){
	document.form01.action = "/sem/new/query/update/mupdate";
	document.form01.keywords_name.value = $('#keywords_name').val();
	document.form01.submit();
}
// delete用
function doActionMD(){
	document.form01.action = "/sem/new/query/update/mdelete";
	document.form01.submit();
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

	var nav_id = 'update';
	$("ul.contents-nav li").removeClass("active");
	$("#query_" + nav_id).addClass("active");
});