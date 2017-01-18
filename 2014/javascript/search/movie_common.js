function refineSearch(formId, kwd) {
    var refineSt = $('input:radio[name=st]:checked').val();
    var refineDr = $('input:radio[name=dr]:checked').val();
    var refine;
    var dr = '';
    var st = '';
    switch (refineSt) {
    case 'all':
        st = '';
        break;
    case 'youtube':
        st = '+fromurl:youtube.com';
        break;
    case 'niconico':
        st = '+fromurl:www.nicovideo.jp';
        break;
    case 'dailymotion':
        st = '+fromurl:www.dailymotion.com';
        break;
    default:
        break;
    };
    switch (refineDr) {
    case '':
        if (st == '') {
            refine = ''
        } else {
            st = st.substr(1);
            refine = '&filter=' + st;
        };
        break;
    case '0':
        dr = '&filter=playlength:<240';
        refine = dr + st;
        break;
    case '1':
        dr = '&filter=playlength:>241+playlength:<1200';
        refine = dr + st;
        break;
    case '2':
        dr = '&filter=playlength:>1201';
        refine = dr + st;
        break;
    default:
        if (st == '') {
            refine = ''
        } else {
            st = st.substr(1);
            refine = '&filter=' + st;
        };
        break;
    };
    $('.category li a').removeClass('on');
    $('.category_mov a').addClass('on');
    document.getElementById('search_form_top').service.value = 'mov';
    document.getElementById('search_form_bottom').service.value = 'mov';
    getIp(kwd, refine);
};

function toSort() {
    if (1 < document.location.search.length) {
        var q = document.location.search.substring(1);
        var parameters = q.split('&');
        var result = new Object();
        var i;
        for (i = 0; i < parameters.length; i++) {
            var element = parameters[i].split('=');
            var paramName = (element[0]);
            var paramValue = (element[1]);
            result[paramName] = paramValue;
        };
    };
    var query = decodeURIComponent(result['q']);
    result['q'] = query;
    jQuery('.classSearch [name=q]').val(query);
    sortSearch(result);
};

function sortSearch(result) {
    var filter = '&filter=' + result['filter'];
    if (result['filter'] === undefined) {
        filter = '';
    };
    var sortIn = $("select[name='theme_no']").val();
    switch (sortIn) {
    case 'label':
        return false;
    case 'new':
        sortIn = '&sort=MOST_RECENT';
        break;
    case 'old':
        sortIn = '&sort=LEAST_RECENT';
        break;
    case 'long':
        sortIn = '&sort=MOST_LONG';
        break;
    case 'short':
        sortIn = '&sort=LEAST_LONG';
        break;
    default:
        break;
    };
    $('.category li a').removeClass('on');
    $('.category_mov a').addClass('on');
    $('#search_form_top [name=service]').val('mov');
    $('#search_form_bottom [name=service]').val('mov');
    getIp(result['q'], filter, sortIn);
};