// insert用
function doActionMI(){
	document.form01.action = "/sem/new/query/querytargeting/minsert";
	document.form01.category_name.value = $('#category_name').val();
	document.form01.keywords_name.value = $('#keywords_name').val();
	document.form01.submit();
}

$(document).ready(function(){
	document.form01.keywords_name.focus();

	var nav_id = 'targeting';
	$("ul.contents-nav li").removeClass("active");
	$("#query_" + nav_id).addClass("active");
});