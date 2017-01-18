// ダウンロード一覧
$(document).ready(function() {

	// ダウンロード一覧
	$("#download_list").dataTable({

		"aaSorting": [[2, "desc"]],
		"bDeferRender": true,
		"bDestroy": true,
		"iDisplayLength": 10,
		"oLanguage": {
			"sLengthMenu": "表示件数_MENU_",
			"sZeroRecords": "該当データが存在しません",
			"sInfo": "_START_ ～ _END_件 / 全_TOTAL_件",
			"sInfoEmpty": "該当データが存在しません",
			"sInfoFiltered": "（全_MAX_件より抽出）",
			"sInfoPostFix": "",
			"sSearch": '<i class="glyphicon glyphicon-search"></i>',
			"sUrl":	"",
			"oPaginate": {
				"sFirst": "先頭",
				"sPrevious": "前",
				"sNext": "次",
				"sLast": "最終"
			},
			"sLengthMenu": '表示件数 <select>'
						 + '<option value="10">10</option>'
						 + '<option value="-1">All</option>'
						 + '</select>',
		}
	});

	// Reload
	$("#reload").on("click", function() {

		document.form01.action="/sem/new/downloadlist/export";
		document.form01.submit();
	});

	// 全選択
	$("#all_download_select").on("click", function() {

		$(".download_row").prop("checked", true);
	});

	// 全解除
	$("#all_download_cancel").on("click", function() {

		$(".download_row").prop("checked", false);
	});

	// 一括DL
	$("#download_bulk_dl").on("click", function() {

		var index = 0;
		var downloadList = new Array;

		$("input[name=download_row]:checked").map(function() {
			downloadList[index] = $(this).val();
			index++;
		});

		if (downloadList.length > 0) {
			document.form01.action="/sem/new/downloadlist/export/bulk_dl";
			document.form01.download_select_row.value=downloadList;
			document.form01.submit();
		}
	});

	// 削除
	$("#download_delete").on("click", function() {

		if (confirm("選択したファイルを削除します。")) {

			var index = 0;
			var downloadList = new Array;

			$("input[name=download_row]:checked").map(function() {
				downloadList[index] = $(this).val();
				index++;
			});

			if (downloadList.length > 0) {
				document.form01.action="/sem/new/downloadlist/export/delete";
				document.form01.download_select_row.value=downloadList;
				document.form01.submit();
			}
		}
	});
});
