        //都道府県、市区町村リストを取得
        var monitor = []; //データ取得終了を監視
        var get;
        var q = [
            {grpby : 'ken'}
            ,{grpby : 'city'}
        ]
        $.each(q, function(i, query){
            get = service.getData('count', query)
                .then(function(data, type){
                    var d = $.Deferred();
                    serviceModel[type].catchError(data)
                        .then(function(count){
                            serviceModel.count.storage[query.grpby] = count;
                            d.resolve();
                        }
                        ,function(errorStatus, errorCode){
                            view.errorAppend(type, errorStatus, errorCode, model.$wrapper);
                            model.$loading.hide();
                            model.$wrapper.show();
                            d.reject();
                        });
                    return d.promise();
                });
            monitor.push(get);
        })
        //データ取得終了後に実行
        $.when.apply($, monitor).done(function() {
            view.createPrefList();
            view.createCityList();
            view.setCondListAction();
            model.$loading.hide();
            model.$wrapper.show();
        })