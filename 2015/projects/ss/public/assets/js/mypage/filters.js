/* Filters */
var mypageFilter = angular.module( 'mypage.filters', []);

mypageFilter.filter('format', function(){
    return function(data,format) {
        var yensign   =  (format == 'no_yen' || format == 'diff_no_yen' ) ?  '':'¥';
        var plus_sign = ((format == 'diff' || format == 'diff_no_yen') && data != "0" && data.indexOf('-')) ? '+': '';
        if(data === '-1' && format != 'diff' && format != 'diff_no_yen'){
            data = '--';
            yensign  = '';
        }else if(data == ''){
            data = "0";
        }
        return yensign + plus_sign + data;
    };
});

mypageFilter.filter('label_class', function(){
    return function(data,place) {
        var className = "";
        if(data >= 0){
            className = "label label-success";
        }else if(data < 0){
            className = "label label-danger";
        }else{
            className = "hide";
        }
        //月初めのkeyword diffは非表示
        if(moment().format("DD") == 2 && place == "keyword"){className = "hide"}
        return className;
    };
});

mypageFilter.filter('to_japanese', function(){
    return function(data) {
        switch (data.toLowerCase()){
          case "exact":
            data = "完全一致";
            break;
          case "broad":
            data = "部分一致";
            break;
          case "phrase":
            data = "フレーズ一致";
            break;
        }
        return data;
    };
});

