$(function(){
	$("#date_selector").change(function() {
		var rangeType  = $(this).val();
		var date       = new Date();
		var year       = date.getFullYear();
		var month      = date.getMonth()+1;
		var day        = date.getDate();
		var dayOfWeek  = date.getDay();
		var start_date = "";
		var end_date   = "";

		switch (rangeType) {
			// 昨日
			case "1":
				start_date = calcDate(year, month, day, -1);
				end_date   = start_date;
				break;
			// 今週（日曜日から本日まで）
			case "2":
				start_date = calcDate(year, month, day, (0 - dayOfWeek));
				end_date   = date;
				break;
			// 今週（月曜日から本日まで）
			case "3":
				start_date = calcDate(year, month, day, (1 - dayOfWeek));
				end_date   = date;
				break;
			// 過去 7 日間
			case "4":
				start_date = calcDate(year, month, day, -7);
				end_date   = calcDate(year, month, day, -1);
				break;
			// 先週（日曜日から土曜日まで）
			case "5":
				start_date = calcDate(year, month, day, (-7 - dayOfWeek));
				end_date   = calcDate(year, month, day, (-1 - dayOfWeek));
				break;
			// 先週 (月～日)
			case "6":
				start_date = calcDate(year, month, day, (-6 - dayOfWeek));
				end_date   = calcDate(year, month, day, (0 - dayOfWeek));
				break;
			// 先週の営業日（月～金）
			case "7":
				start_date = calcDate(year, month, day, (-6 - dayOfWeek));
				end_date   = calcDate(year, month, day, (-2 - dayOfWeek));
				break;
			// 過去 14 日間
			case "8":
				start_date = calcDate(year, month, day, -14);
				end_date   = calcDate(year, month, day, -1);
				break;
			// 過去 30 日間
			case "9":
				start_date = calcDate(year, month, day, -30);
				end_date   = calcDate(year, month, day, -1);
				break;
			// 今月
			case "10":
				start_date = new Date(year, (month - 1), 1);
				end_date   = date;
				break;
			// 先月
			case "11":
				start_date = new Date(year, (month - 2), 1);
				end_date   = new Date(year, (month - 1), 0);
				break;
			// 月初～昨日
			case "12":
				start_date = new Date(year, (month - 1), 1);
				end_date   = calcDate(year, month, day, -1);
				break;
			default:
				break;
		}

		if (start_date) {
			$("#start_year").val(start_date.getFullYear());
			$("#start_month").val(formatDate(start_date.getMonth()+1));
			$("#start_day").val(formatDate(start_date.getDate()));
			$("#end_year").val(end_date.getFullYear());
			$("#end_month").val(formatDate(end_date.getMonth()+1));
			$("#end_day").val(formatDate(end_date.getDate()));
		}
	});

	$("select[name^='start_']").change(function() {
			$("#date_selector").val("");
	});
	$("select[name^='end_']").change(function() {
			$("#date_selector").val("");
	});
});

function calcDate(year, month, day, addDays) {
	var date      = new Date(year, (month - 1), day);
	var baseSec   = date.getTime();
	var addSec    = addDays * 86400000;
	var targetSec = baseSec + addSec;
	date.setTime(targetSec);
	return date;
}

function formatDate(num) {
	if (String(num).length == 1) return "0"+String(num);
	return num;
}
