//写真一覧画面のモック
//データ取得→表示→写真クリックで詳細表示

var APIURL = PARAMS['domain'] + '/' + PARAMS['version'] + PARAMS['path'];
axios.defaults.headers =  {
    'Authorization': 'Bearer '+ PARAMS['access_token'],
    'Content-Type': 'application/x-www-form-urlencoded'
};


var store = {
    state : {
        viewStatus : {
            hide : {
                detailWindow : true
            }
        },
    },
    setViewStatus : function(elm,newValue) {
        this.state.viewStatus.hide[elm] = newValue
    }
};


new Vue({
    el         : '#photo_list',
    delimiters : ['{[[',']]}'],//Twigとのバッティングを回避
    data : {
        photos           : [],
        currentPhotoData : {
            photo_url : {
                w2 : ''
            }
        },
        sharedState      : store.state
    },
    created : function(){
        this.fetch();
    },
    methods: {
        fetch : function() {
            var $data = this.$data;
　　　　　　　//Ajaxは推奨ライブラリのaxiosを使用
            axios.get(APIURL).then(function (res) {
                $data.photos = res.data.photos;
            });
        },
         showDetailWindow : function(index){
             var $data = this.$data;
             $data.currentPhotoData = $data.photos[index];
             store.setViewStatus('detailWindow',false);
            return false;
        }
    },
});


// Template sample
// <div id="photo_list" class="photo_list">
//     <ul>
//         <li v-for="(photo, index) in photos" v-show="sharedState.viewStatus.hide.detailWindow">
//             <p v-on:click="showDetailWindow(index)">
//                 <span><img v-bind:src="photo.photo_url.w2"></span>
//             </p>
//             <span class="photo_number">No.{[[ photo.photo_number ]]}</span>
//         </li>
//     </ul>

//     <!-- 詳細拡大 -->
//     <div v-show="!sharedState.viewStatus.hide.detailWindow">
//         <img v-bind:src="currentPhotoData.photo_url.w2">
//     </div>
// </div>
