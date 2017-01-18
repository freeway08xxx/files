/**
 * 自動実行設定の内容を取得する
 */
function searchSchedule(){
	if ($("#parent").val() == "1") {
		document.form01.action="editschedule.php?clnt="+$("#child_client select").val();
	}
	if ($("#parent").val() == "2") {
		document.form01.action="editschedule.php?idst="+$("#child_dist select").val();
	}
	document.form01.action_type.value="search";
	document.form01.submit();
}

/**
 * 自動実行設定を登録する
 */
function entrySchedule(word){
	if ($("#parent").val() == "1") {
		document.form01.action="editschedule.php?clnt="+$("#child_client select").val();
	}
	if ($("#parent").val() == "2") {
		document.form01.action="editschedule.php?idst="+$("#child_dist select").val();
	}
	document.form01.action_type.value="upsert";
	document.form01.submit();
}


/**
 * ドキュメントロード時に実行する処理は以下にまとめる
 */
$(document).ready(function(){
	/**
	 * プルダウン切り替え処理
	 */
	var toggleChild = function(val) {
		var client = $("#child_client");
		var dist   = $("#child_dist");

		if (val === "1") {
			client.css('display', 'inline-block').find("select").attr("disabled", false);
			dist.hide().find("select").attr("disabled", "disabled");
		} else {
			dist.css('display', 'inline-block').find("select").attr("disabled", false);
			client.hide().find("select").attr("disabled", "disabled");
		}
	};

	var val = $("#parent").val();
	toggleChild(val);

	$("#parent").change(function () {
		var val = $(this).val();
		toggleChild(val);
	});


	/**
	 * DOM Control
	 */
	$("#show_btn").on("click", function () {
		searchSchedule();
	});

	$("#submit_btn").on("click", function () {
		entrySchedule();
	});
});
