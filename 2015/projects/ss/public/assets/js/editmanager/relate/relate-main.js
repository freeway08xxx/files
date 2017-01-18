// 関連KW取得
$(document).ready(function() {

    var page_id = 'editmanager';
    var nav_id  = 'relate';
    $("ul.contents-nav li").removeClass("active");
    $("#" + page_id + "_" + nav_id).addClass("active");

	$("#client_id").select2();
	$("#account_id").select2();

	// クライアント選択
	$(document).on("change", "#client_id", function() {

		document.form01.action="/sem/new/editmanager/relate/execute";
		document.form01.submit();
	});

	// 取得
	$("#relate_get").on("click", function() {

		if (document.form01.account_id.value === "") {
			alert("アカウントを入力してください。");
			return;
		}

		if (document.form01.relate_keyword.value === "") {
			alert("キーワードを入力してください。");
			return;
		}

		// キーワード数をチェック
		var keywordList = document.form01.relate_keyword.value.split("\n");
		if (keywordList.length > 50) {
			alert("指定できるキーワード数は最大50です。");
			return;
		}

		document.form01.action = "/sem/new/editmanager/relate/execute/get";
		document.form01.action_type.value = "get";
		document.form01.submit();
	});
});
