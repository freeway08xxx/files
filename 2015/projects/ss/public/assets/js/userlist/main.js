$(function (){

    var nav_id = $("#nav_id").val();
    $("ul.contents-nav li").removeClass("active");
    $("#userlist_" + nav_id).addClass("active");

    $('.searchable').multiSelect({
      selectableHeader: "<input type='text' class='search-input' autocomplete='off' placeholder='try \"12\"'>",
      selectionHeader: "<input type='text' class='search-input' autocomplete='off' placeholder='try \"4\"'>",
      afterInit: function(ms){
        var that = this,
            $selectableSearch = that.$selectableUl.prev(),
            $selectionSearch = that.$selectionUl.prev(),
            selectableSearchString = '#'+that.$container.attr('id')+' .ms-elem-selectable:not(.ms-selected)',
            selectionSearchString = '#'+that.$container.attr('id')+' .ms-elem-selection.ms-selected';

        that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
        .on('keydown', function(e){
          if (e.which === 40){
            that.$selectableUl.focus();
            return false;
          }
        });

        that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
        .on('keydown', function(e){
          if (e.which == 40){
            that.$selectionUl.focus();
            return false;
          }
        });
      },
      afterSelect: function(){
        this.qs1.cache();
        this.qs2.cache();
      },
      afterDeselect: function(){
        this.qs1.cache();
        this.qs2.cache();
      }
    });

    // 外部ツール
    $(document).ready(function() {
        $("#client_id").select2();
        $("#account_id_list").multiSelect({
              selectableHeader: "<span class=\"label label-default\">選択可能アカウント</span> <i class='glyphicon glyphicon-search'></i><input type='text' class='search-input form-control input-sm' autocomplete='off'>",
              selectionHeader: "<span class=\"label label-success\">選択済みアカウント</span> <i class='glyphicon glyphicon-search'></i><input type='text' class='search-input form-control input-sm' autocomplete='off'>",
              afterInit: function(ms){
                var that = this,
                    $selectableSearch = that.$selectableUl.prev(),
                    $selectionSearch = that.$selectionUl.prev(),
                    selectableSearchString = '#'+that.$container.attr('id')+' .ms-elem-selectable:not(.ms-selected)',
                    selectionSearchString = '#'+that.$container.attr('id')+' .ms-elem-selection.ms-selected';

                that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
                .on('keydown', function(e){
                  if (e.which === 40){
                    that.$selectableUl.focus();
                    return false;
                  }
                });

                that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
                .on('keydown', function(e){
                  if (e.which == 40){
                    that.$selectionUl.focus();
                    return false;
                  }
                });
              },
              afterSelect: function(){
                this.qs1.cache();
                this.qs2.cache();
              },
              afterDeselect: function(){
                this.qs1.cache();
                this.qs2.cache();
              }
        });
    });

    // クライアント選択時のアクション
    $(document).on("change", "#client_id", function() {
        // クライアント未選択時はクライアント選択
        if (document.form01.client_id.value == "") {
            document.form01.action="/sem/new/userlist/get/client";
        // クライアント選択時はアカウント選択
        } else {
            document.form01.action="/sem/new/userlist/get/account";
        }
        document.form01.submit();
    });

    // アカウント全選択ボタン押下時のアクション
    $(document).ready(function() {
        $("#account_all_select_btn").on("click", function() {
            $("#account_id_list").multiSelect("select_all");
            $("#account_all_select_btn").attr("disabled", "disabled");
            $("#account_all_cancel_btn").attr("disabled", false);
        });
    });

    // アカウント全解除ボタン押下時のアクション
    $(document).ready(function() {
        $("#account_all_cancel_btn").on("click", function() {
            $("#account_id_list").multiSelect("deselect_all");
            $("#account_all_select_btn").attr("disabled", false);
            $("#account_all_cancel_btn").attr("disabled", "disabled");
        });
    });

    // 選択しているアカウント数を表示
    $(document).on("change", "#account_id_list", function() {
        var count = 0;

        for (var i = 0; i < document.form01.account_id_list.length; i++) {
            if (document.form01.account_id_list[i].selected == true) {
                count++;
            }
        }
        document.getElementById("select_account_count").innerHTML = count;
    });

    // リマーケティングリストデータ取得ボタン押下時のアクション
    $(document).ready(function() {
        $("#userlist_get_btn").on("click", function() {
            // アカウント選択時のみ
            if (document.form01.account_id_list.value != "") {
                document.form01.action="/sem/new/userlist/get/result";
                document.form01.submit();
            }
        });
    });


});
