$(function() {

    var nav_id = $("#nav_id").val();
    $("ul.contents-nav li").removeClass("active");
    $("#userlist_" + nav_id).addClass("active");

    var lang = {
        "sProcessing":   "処理中...",
        "sLengthMenu":   "表示件数 _MENU_",
        "sZeroRecords":  "データはありません",
        "sInfo":         " _START_ - _END_ / _TOTAL_",
        "sInfoEmpty":    " 0 件中 0 から 0 まで表示",
        "sInfoFiltered": "（全 _MAX_ 件より抽出）",
        "sInfoPostFix":  "",
        "sSearch":       '<i class="glyphicon glyphicon-search"></i>',
        "sUrl":          "",
        "oPaginate": {
            "sFirst":    "先頭",
            "sPrevious": "前",
            "sNext":     "次",
            "sLast":     "最終"
        },
        "sLengthMenu": '表示件数 <select>'+
            '<option value="10">10</option>'+
            '<option value="30">50</option>'+
            '<option value="50">100</option>'+
            '<option value="-1">All</option>'+
            '</select>  ',
    };

    var option_rule = {

        "dom": 'fT<"clear">Clrtpi',
        "scrollX": true,
        "bAutoWidth": false,
        "bProcessing":  true,
        "bDeferRender": true,
        "bDestroy": true,
        "bStateSave":   true,
        "iDisplayLength" : 10,
        "sPaginationType": "full_numbers",
        "oTableTools": {
            "sSwfPath": "../../assets/js/vendor/swf/copy_csv_xls_pdf.swf",
            "aButtons": [
                {
                    "sExtends": "xls",
                    "sButtonText": "Download",
                    "oSelectorOpts": {
                        page: 'current'
                    }
                }
            ],
        },
        "oLanguage": lang,
        "aoColumnDefs": [
            { "bSortable": false, "aTargets": [ ] },
        ],
        "aoColumns" : [
            { sWidth: '80px' },     // アカウントID
            { sWidth: '100px' },    // リスト名
            { sWidth: '40px' },     // 訪問者履歴
            { sWidth: '10px' },     // 有効期間
            { sWidth: '40px' },     // 過去の訪問者
            { sWidth: '40px' },     // 条件

            { sWidth: '10px' },     // 箱1
            { sWidth: '20px' },     // 項目1
            { sWidth: '40px' },     // 条件1
            { sWidth: '70px' },     // 条件対象1
            { sWidth: '10px' },
            { sWidth: '20px' },
            { sWidth: '40px' },
            { sWidth: '70px' },
            { sWidth: '10px' },
            { sWidth: '20px' },
            { sWidth: '40px' },
            { sWidth: '70px' },
            { sWidth: '10px' },
            { sWidth: '20px' },
            { sWidth: '40px' },
            { sWidth: '70px' },
            { sWidth: '10px' },
            { sWidth: '20px' },
            { sWidth: '40px' },
            { sWidth: '70px' },
            { sWidth: '10px' },
            { sWidth: '20px' },
            { sWidth: '40px' },
            { sWidth: '70px' },
            { sWidth: '10px' },
            { sWidth: '20px' },
            { sWidth: '40px' },
            { sWidth: '70px' },
            { sWidth: '10px' },
            { sWidth: '20px' },
            { sWidth: '40px' },
            { sWidth: '70px' },
            { sWidth: '10px' },
            { sWidth: '20px' },
            { sWidth: '40px' },
            { sWidth: '70px' },
            { sWidth: '10px' },
            { sWidth: '20px' },
            { sWidth: '40px' },
            { sWidth: '70px' },
            { sWidth: '100px' },    // 説明
            { sWidth: '50px' }      // タグID
        ]
    };

    var option_combination = {

        "dom": 'fT<"clear">Clrtpi',
        "scrollX": true,
        "bAutoWidth": false,
        "bProcessing":  true,
        "bDeferRender": true,
        "bDestroy": true,
        "bStateSave":   true,
        "iDisplayLength" : 10,
        "sPaginationType": "full_numbers",
        "oTableTools": {
            "sSwfPath": "../../assets/js/vendor/swf/copy_csv_xls_pdf.swf",
            "aButtons": [
                {
                    "sExtends": "xls",
                    "sButtonText": "Download",
                    "oSelectorOpts": {
                        page: 'current'
                    }
                }
            ],
        },
        "oLanguage": lang,
        "aoColumnDefs": [
            { "bSortable": false, "aTargets": [ ] },
        ],
        "aoColumns" : [
            { sWidth: '80px' },     // アカウントID
            { sWidth: '100px' },    // リスト名

            { sWidth: '10px' },     // 箱1
            { sWidth: '10px' },     // 条件1
            { sWidth: '100px' },    // 組み合わせリスト名1
            { sWidth: '10px' },
            { sWidth: '10px' },
            { sWidth: '100px' },
            { sWidth: '10px' },
            { sWidth: '10px' },
            { sWidth: '100px' },
            { sWidth: '10px' },
            { sWidth: '10px' },
            { sWidth: '100px' },
            { sWidth: '10px' },
            { sWidth: '10px' },
            { sWidth: '100px' },
            { sWidth: '10px' },
            { sWidth: '10px' },
            { sWidth: '100px' },
            { sWidth: '10px' },
            { sWidth: '10px' },
            { sWidth: '100px' },
            { sWidth: '10px' },
            { sWidth: '10px' },
            { sWidth: '100px' },
            { sWidth: '10px' },
            { sWidth: '10px' },
            { sWidth: '100px' },
            { sWidth: '10px' },
            { sWidth: '10px' },
            { sWidth: '100px' },
            { sWidth: '100px' }     // 説明
        ]
    };

    var option_gdn_rule = {

        "dom": 'fT<"clear">Clrtpi',
        "scrollX": true,
        "bAutoWidth": false,
        "bProcessing":  true,
        "bDeferRender": true,
        "bDestroy": true,
        "bStateSave":   true,
        "iDisplayLength" : 10,
        "sPaginationType": "full_numbers",
        "oTableTools": {
            "sSwfPath": "../../assets/js/vendor/swf/copy_csv_xls_pdf.swf",
            "aButtons": [
                {
                    "sExtends": "xls",
                    "sButtonText": "Download",
                    "oSelectorOpts": {
                        page: 'current'
                    }
                }
            ],
        },
        "oLanguage": lang,
        "aoColumnDefs": [
            { "bSortable": false, "aTargets": [ ] },
        ],
        "aoColumns" : [
            { sWidth: '80px' },     // アカウントID
            { sWidth: '100px' },    // リスト名
            { sWidth: '50px' },     // 有効期間

            { sWidth: '10px' },     // 箱1
            { sWidth: '20px' },     // 項目1
            { sWidth: '40px' },     // 条件1
            { sWidth: '70px' },     // 条件対象1
            { sWidth: '10px' },
            { sWidth: '20px' },
            { sWidth: '40px' },
            { sWidth: '70px' },
            { sWidth: '10px' },
            { sWidth: '20px' },
            { sWidth: '40px' },
            { sWidth: '70px' },
            { sWidth: '10px' },
            { sWidth: '20px' },
            { sWidth: '40px' },
            { sWidth: '70px' },
            { sWidth: '10px' },
            { sWidth: '20px' },
            { sWidth: '40px' },
            { sWidth: '70px' },
            { sWidth: '10px' },
            { sWidth: '20px' },
            { sWidth: '40px' },
            { sWidth: '70px' },
            { sWidth: '10px' },
            { sWidth: '20px' },
            { sWidth: '40px' },
            { sWidth: '70px' },
            { sWidth: '10px' },
            { sWidth: '20px' },
            { sWidth: '40px' },
            { sWidth: '70px' },
            { sWidth: '10px' },
            { sWidth: '20px' },
            { sWidth: '40px' },
            { sWidth: '70px' },
            { sWidth: '10px' },
            { sWidth: '20px' },
            { sWidth: '40px' },
            { sWidth: '70px' },
            { sWidth: '100px' }    // 説明
        ]
    };

    $(document).ready(function() {
        $('#userlist_table_rule').removeClass("hide");
        $('#userlist_table_combination').removeClass("hide");
        $('#userlist_table_gdn_rule').removeClass("hide");
        $('#userlist_table_rule').dataTable(option_rule);
        $('#userlist_table_combination').dataTable(option_combination);
        $('#userlist_table_gdn_rule').dataTable(option_gdn_rule);
    });

    $("#user_list_file").on("change", function() {
        document.form_confirm.action = "/sem/new/userlist/edit/confirm";
        document.form_confirm.submit();
    });

    $("#api_regist").on("click", function() {

        if (confirm("画面上の内容で媒体に登録を行います。\n よろしいですか？？")) {
            $("#loading_api").fadeIn();
            if (document.form_edit.uniqid.value !== "") {
                document.form_edit.action = "/sem/new/userlist/edit/api";
                document.form_edit.submit();
            }
        }
    });
});
