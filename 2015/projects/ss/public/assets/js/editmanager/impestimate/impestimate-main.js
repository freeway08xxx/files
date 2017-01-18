// 検索予測数取得
$(document).ready(function() {

    var page_id = 'editmanager';
    var nav_id  = 'impestimate';
    $("ul.contents-nav li").removeClass("active");
    $("#" + page_id + "_" + nav_id).addClass("active");

	// 取得条件設定 媒体
	$("input:radio[name='imp_estimate_media']").on("change", function() {

		var media = $("input:radio[name='imp_estimate_media']:checked").val();

		// Yahoo!
		if (media === "1") {

			document.form01.imp_estimate_get[0].checked = "checked";
			document.form01.imp_estimate_get[1].disabled = "disabled";
			document.form01.imp_estimate_search[1].disabled = "";
			document.form01.imp_estimate_search[2].disabled = "";
			$(".imp_estimate_y_only").show();

		// Google
		} else if (media === "2") {

			document.form01.imp_estimate_get[1].disabled = "";
			document.form01.imp_estimate_search[0].checked = "checked";
			document.form01.imp_estimate_search[1].disabled = "disabled";
			document.form01.imp_estimate_search[2].disabled = "disabled";
			$(".imp_estimate_y_only").hide();
		}
	});

	// 取得条件設定 取得
	$("#imp_estimate_get").on("click", function() {

		if (document.form01.imp_estimate_keyword.value !== "") {

			var cpc = document.form01.imp_estimate_cpc.value;
			if (cpc === "" || cpc.match(/[^0-9]+/)) {
				alert("入札価格に金額を設定してください。");
				return;
			}

			// Yahoo!の場合は、キーワード数をチェック
			var media = $("input:radio[name='imp_estimate_media']:checked").val();
			if (media === "1") {

				var keywordList = document.form01.imp_estimate_keyword.value.split("\n");
				if (keywordList.length > 10000) {

					alert("指定できるキーワード数は最大10000です。");
					return;
				}
			}

			document.form01.action = "/sem/new/editmanager/impestimate/execute/get";
			document.form01.action_type.value = "get";
			document.form01.submit();

		} else {
			alert("キーワードを入力してください。");
		}
	});

	// ページ読み込み時に実行
	$("input:radio[name='imp_estimate_media']").trigger("change");
});
