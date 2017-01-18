// セグメントバナー生成画面
$(document).ready(function() {

	// BulkUP
	$("#bulk_up").on("click", function() {

		// Bulkファイル設定時のみ実行
		if (document.form01.bulk_up_file.value !== "") {

			if (confirm("セグメントバナー生成を実行します。")) {

				document.form01.action = "/sem/new/segmentbanner/create/bulk_up";
				document.form01.action_type.value = "bulk_up";
				document.form01.submit();
			}
		}
	});

	// X座標
	$("select[name='x_value'],select[name='y_value']").on("change", function() {

		var x = document.form01.x_value.value;
		var y = document.form01.y_value.value;

		var img = document.createElement("img");
		img.src = "/sem/new/assets/img/segmentbanner/sample_" + x + "_" + y + ".png";

		var imgSpan = document.getElementById("preview_img");

		if (imgSpan.firstChild) {

			imgSpan.removeChild(imgSpan.firstChild);
		}

		imgSpan.appendChild(img);
	});

	// 素材UP
	$("#material_up").on("click", function() {

		// 素材ファイル設定時のみ実行
		if (document.form01.material_up_file.value !== "") {

			document.form01.action = "/sem/new/segmentbanner/create/bulk_up";
			document.form01.action_type.value = "material_up";
			document.form01.submit();
		}
	});

	// フォントUP
	$("#font_up").on("click", function() {

		// フォントファイル設定時のみ実行
		if (document.form01.font_up_file.value !== "") {

			document.form01.action = "/sem/new/segmentbanner/create/bulk_up";
			document.form01.action_type.value = "font_up";
			document.form01.submit();
		}
	});

	// ページ読み込み時に実行
	$("select[name='x_value']").trigger("change");
});
