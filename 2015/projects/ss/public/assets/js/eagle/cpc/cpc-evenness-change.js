// CPC一律変更画面
$(document).ready(function() {

	currentDoc = window.opener.document;

	// 変更方法
	$("input:radio[name='cpc_evenness_change_method']").on("change", function() {

		var method = $("input:radio[name='cpc_evenness_change_method']:checked").val();

		// 金額
		if (method === "amount") {

			document.getElementById("cpc_evenness_change_unit").innerHTML = "円";

		// パーセント
		} else if (method === "percent") {

			document.getElementById("cpc_evenness_change_unit").innerHTML = "パーセント";
		}
	});

	// 変更内容
	$("input:text[name='cpc_evenness_change_value']").on("change", function() {

		var value = document.form01.cpc_evenness_change_value.value;

		// 半角数字チェック
		if (!numberCheck(value, "変更内容", "-")) {

			document.form01.cpc_evenness_change_value.value = "";
		}
	});

	// 入稿
	$("#evenness_edit").on("click", function() {

		window.opener.document.form01.action_type.value="evenness_edit";

		if (evennessEditCommon()) {

			// 入稿ボタンを非表示
			document.getElementById("evenness_edit").style.display="none";
		}
	});

	// CPC変更内容DL
	$("#evenness_cpc_change_dl").on("click", function() {

		window.opener.document.form01.action_type.value="evenness_cpc_change_dl";

		if (evennessEditCommon()) {

			// 入稿ボタンを表示
			document.getElementById("evenness_edit").style.display="inline-block";
		}
	});

	// 設定
	$("#cpc_evenness_change_set").on("click", function() {

		var value = document.form01.cpc_evenness_change_value.value;

		// 変更内容入力時のみ実行
		if (value !== "") {

			// 変更内容をPOST
			var device = $("input:radio[name='cpc_evenness_change_device']:checked").val();
			var method = $("input:radio[name='cpc_evenness_change_method']:checked").val();
			var type = document.form01.cpc_evenness_change_type.value;

			// 掲載一覧を取得
			var structureListCount = window.opener.document.getElementById("structure_list").rows.length;

			for (i = 2; i < structureListCount; i++) {

				var listCpcMaxPc = window.opener.document.getElementById("structure_list").rows[i].cells[NOW_ADG_CPC_MAX_PC_CELL_NUM].innerHTML;
				var listCpcMaxSp = window.opener.document.getElementById("structure_list").rows[i].cells[NOW_ADG_CPC_MAX_SP_CELL_NUM].innerHTML;

				listCpcMaxPc = parseInt(listCpcMaxPc);
				listCpcMaxSp = parseInt(listCpcMaxSp);
				value = parseInt(value);

				// 金額
				if (method === "amount") {

					// 上げる
					if (type === "up") {

						var cpcMaxPcCalc = listCpcMaxPc + value;
						var cpcMaxSpCalc = listCpcMaxSp + value;

					// 下げる
					} else if (type === "down") {

						var cpcMaxPcCalc = listCpcMaxPc - value;
						var cpcMaxSpCalc = listCpcMaxSp - value;
					}

				// パーセント
				} else if (method === "percent") {

					// 上げる
					if (type === "up") {

						var cpcMaxPcCalc = listCpcMaxPc + Math.round(listCpcMaxPc * (value / 100));
						var cpcMaxSpCalc = listCpcMaxSp + Math.round(listCpcMaxSp * (value / 100));

					// 下げる
					} else if (type === "down") {

						var cpcMaxPcCalc = listCpcMaxPc - Math.round(listCpcMaxPc * (value / 100));
						var cpcMaxSpCalc = listCpcMaxSp - Math.round(listCpcMaxSp * (value / 100));
					}
				}

				// PC
				if (device === "pc") {

					var cpcMaxPc = cpcMaxPcCalc;
					var cpcMaxSp = listCpcMaxSp;

					changeCpcMaxPc(String(cpcMaxPc), i, false);

				// SP
				} else if (device === "sp") {

					var cpcMaxPc = listCpcMaxPc;
					var cpcMaxSp = cpcMaxSpCalc;

					changeCpcMaxSp(String(cpcMaxSp), i, false);

				// PC / SP
				} else if (device === "pc_sp") {

					var cpcMaxPc = cpcMaxPcCalc;
					var cpcMaxSp = cpcMaxSpCalc;

					changeCpcMaxPcSp(String(cpcMaxPc), String(cpcMaxSp), i, false);
				}

				var bidModifier = Math.round(((cpcMaxSp / cpcMaxPc) - 1) * 100);

				changeBidModifier(String(bidModifier), i, false);
			}

			alert("CPC一律変更の反映が完了しました。");
		}
	});

	// 閉じる
	$("#cpc_evenness_change_close").on("click", function() {

		window.close();
	});
});

// 入稿 / CPC変更内容DL
function evennessEditCommon() {

	var device = $("input:radio[name='cpc_evenness_change_device']:checked").val();
	var method = $("input:radio[name='cpc_evenness_change_method']:checked").val();
	var value = document.form01.cpc_evenness_change_value.value;
	var type = document.form01.cpc_evenness_change_type.value;

	// 変更内容入力時のみ実行
	if (value !== "") {

		// 変更デバイスを設定
		var inputDevice = window.opener.document.createElement("input");
		inputDevice.type = "hidden";
		inputDevice.name = "cpc_evenness_change_device";
		inputDevice.value = device;
		window.opener.document.form01.appendChild(inputDevice);

		// 変更方法を設定
		var inputMethod = window.opener.document.createElement("input");
		inputMethod.type = "hidden";
		inputMethod.name = "cpc_evenness_change_method";
		inputMethod.value = method;
		window.opener.document.form01.appendChild(inputMethod);

		// 変更内容を設定
		var inputValue = window.opener.document.createElement("input");
		inputValue.type = "hidden";
		inputValue.name = "cpc_evenness_change_value";
		inputValue.value = value;
		window.opener.document.form01.appendChild(inputValue);

		// 変更タイプを設定
		var inputType = window.opener.document.createElement("input");
		inputType.type = "hidden";
		inputType.name = "cpc_evenness_change_type";
		inputType.value = type;
		window.opener.document.form01.appendChild(inputType);

		window.opener.document.form01.action="/sem/new/eagle/cpc/update/edit";
		window.opener.document.form01.target="";
		window.opener.document.form01.submit();

		return true;
	}
}
