$(function (){

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

    var option_google_other_combination = {

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
            { sWidth: '100px' },    // アカウントID
            { sWidth: '70px' },     // ユーザリストID
            { sWidth: '100px' },    // リスト名
            { sWidth: '60px' },     // ステータス
            { sWidth: '10px' },     // 有効期間
            { sWidth: '50px' },     // リストサイズ
            { sWidth: '50px' },     // タイプ

            { sWidth: '10px' },     // 箱1
            { sWidth: '20px' },     // 項目1
            { sWidth: '30px' },     // 条件1
            { sWidth: '70px' },     // 条件対象1
            { sWidth: '10px' },
            { sWidth: '20px' },
            { sWidth: '30px' },
            { sWidth: '70px' },
            { sWidth: '10px' },
            { sWidth: '20px' },
            { sWidth: '30px' },
            { sWidth: '70px' },
            { sWidth: '10px' },
            { sWidth: '20px' },
            { sWidth: '30px' },
            { sWidth: '70px' },
            { sWidth: '10px' },
            { sWidth: '20px' },
            { sWidth: '30px' },
            { sWidth: '70px' },
            { sWidth: '10px' },
            { sWidth: '20px' },
            { sWidth: '30px' },
            { sWidth: '70px' },
            { sWidth: '10px' },
            { sWidth: '20px' },
            { sWidth: '30px' },
            { sWidth: '70px' },
            { sWidth: '10px' },
            { sWidth: '20px' },
            { sWidth: '30px' },
            { sWidth: '70px' },
            { sWidth: '10px' },
            { sWidth: '20px' },
            { sWidth: '30px' },
            { sWidth: '70px' },
            { sWidth: '10px' },
            { sWidth: '20px' },
            { sWidth: '30px' },
            { sWidth: '70px' },
            { sWidth: '100px' },    // 説明
            { sWidth: '100px' }     // タグID
        ]
    };

    var option_ydn_rule = {

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
            { sWidth: '100px' },    // ターゲットリストID
            { sWidth: '50px' },     // 配信状況
            { sWidth: '60px' },     // リーチ数
            { sWidth: '80px' },     // アカウントID
            { sWidth: '100px' },    // リスト名
            { sWidth: '40px' },     // 訪問者履歴
            { sWidth: '10px' },     // 有効期間
            { sWidth: '40px' },     // 過去の訪問者

            { sWidth: '10px' },     // 箱1
            { sWidth: '20px' },     // 項目1
            { sWidth: '30px' },     // 条件1
            { sWidth: '70px' },     // 条件対象1
            { sWidth: '10px' },
            { sWidth: '20px' },
            { sWidth: '30px' },
            { sWidth: '70px' },
            { sWidth: '10px' },
            { sWidth: '20px' },
            { sWidth: '30px' },
            { sWidth: '70px' },
            { sWidth: '10px' },
            { sWidth: '20px' },
            { sWidth: '30px' },
            { sWidth: '70px' },
            { sWidth: '10px' },
            { sWidth: '20px' },
            { sWidth: '30px' },
            { sWidth: '70px' },
            { sWidth: '10px' },
            { sWidth: '20px' },
            { sWidth: '30px' },
            { sWidth: '70px' },
            { sWidth: '10px' },
            { sWidth: '20px' },
            { sWidth: '30px' },
            { sWidth: '70px' },
            { sWidth: '10px' },
            { sWidth: '20px' },
            { sWidth: '30px' },
            { sWidth: '70px' },
            { sWidth: '10px' },
            { sWidth: '20px' },
            { sWidth: '30px' },
            { sWidth: '70px' },
            { sWidth: '10px' },
            { sWidth: '20px' },
            { sWidth: '30px' },
            { sWidth: '70px' },
            { sWidth: '100px' }     // 説明
        ]
    };

    var option_ydn_combination = {

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
            { sWidth: '60px' },     // ターゲットリストID
            { sWidth: '50px' },     // 配信状況
            { sWidth: '60px' },     // リーチ数
            { sWidth: '100px' },     // アカウントID
            { sWidth: '100px' },    // リスト名

            { sWidth: '10px' },     // 箱1
            { sWidth: '30px' },     // 条件1
            { sWidth: '100px' },    // 組み合わせリスト名1
            { sWidth: '10px' },
            { sWidth: '30px' },
            { sWidth: '100px' },
            { sWidth: '10px' },
            { sWidth: '30px' },
            { sWidth: '100px' },
            { sWidth: '10px' },
            { sWidth: '30px' },
            { sWidth: '100px' },
            { sWidth: '10px' },
            { sWidth: '30px' },
            { sWidth: '100px' },
            { sWidth: '10px' },
            { sWidth: '30px' },
            { sWidth: '100px' },
            { sWidth: '10px' },
            { sWidth: '30px' },
            { sWidth: '100px' },
            { sWidth: '10px' },
            { sWidth: '30px' },
            { sWidth: '100px' },
            { sWidth: '10px' },
            { sWidth: '30px' },
            { sWidth: '100px' },
            { sWidth: '10px' },
            { sWidth: '30px' },
            { sWidth: '100px' },
            { sWidth: '100px' }     // 説明
        ]
    };

    var option_google_combination = option_ydn_combination;

    $(document).ready(function() {
        $('#userlist_ydn_rule').dataTable(option_ydn_rule);
    });

    // タブを切り替えた際に一度だけリロードする
    $(document).one("click", ".tabs .ydn_combination", function() {
        $('#userlist_ydn_combination').dataTable(option_ydn_combination);
    });

    // タブを切り替えた際に一度だけリロードする
    $(document).one("click", ".tabs .google_other_combination", function() {
        $('#userlist_google_other_combination').dataTable(option_google_other_combination);
    });

    // タブを切り替えた際に一度だけリロードする
    $(document).one("click", ".tabs .google_combination", function() {
        $('#userlist_google_combination').dataTable(option_google_combination);
    });


});
