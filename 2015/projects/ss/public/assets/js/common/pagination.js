Paginate = function(settings, objData) {
  this.init(settings, objData);
}
$.extend(Paginate.prototype ,{
  init: function(settings, objData) {
    this.paginate = null;
    this.aryObjData = [];
    this.settings = settings;
    this.objData = objData;
  },
  displayView: function() {
    for(var i=1; i<=this.totalPages; i++) {
      begin = (i-1)*this.settings.limit;
      last = i*this.settings.limit;
      this.aryObjData[i] = this.objData.slice(begin,last);
    }
  },
  generate: function() {
    this.paginate = $('<ul class="pagination pagination-sm"></ul>');
    this.generateLinks();
    return this.paginate;
  },
  generateLinks: function() {
    this.totalPages = Math.ceil(this.settings.total/this.settings.limit);
    var visiblePages = this.settings.spread * 2 + 1;
    var currentPage = this.settings.index;
    var start = 0, end = 0;
    // get start and end page
    if(this.totalPages <= visiblePages) { start = 0; end = this.totalPages; }
    else if(currentPage < this.settings.spread){ start = 0; end = visiblePages; }
    else if(currentPage > this.totalPages - this.settings.spread-1){ start = this.totalPages-visiblePages; end=this.totalPages; }
    else{ start = currentPage-this.settings.spread; end=currentPage+this.settings.spread+1; }
    this.paginate.html('');
    // generate links
    this.paginate.append(this.getLink(0, 'first'));
    if (currentPage != 1) this.paginate.append(this.getLink(currentPage-2, 'prev'));
    for(var i=start; i<end; i++) {
      this.paginate.append(this.getLink(i, (i+1) === this.settings.index ? 'active' : null));
    }
    if (currentPage != this.totalPages) this.paginate.append(this.getLink(currentPage, 'next'));
    this.paginate.append(this.getLink(this.totalPages-1, 'last'));
  },
  getLink: function(i, key) {
    class_name = '';
    page_name = (i+1);
    if (key == 'first') {
      class_name = 'class="first"';
      page_name = "&laquo;&laquo;";
    }
    if (key == 'prev') {
      class_name = 'class="previous"';
      page_name = "&laquo;";
    }
    if (key == 'next') {
      class_name = 'class="next"';
      page_name = "&raquo;";
    }
    if (key == 'last') {
      class_name = 'class="last"';
      page_name = "&raquo;&raquo;";
    }
    if (key == 'active') {
      class_name = 'class="active"';
    }
    return $('<li '+class_name+'><a href="#" onclick="for_paging('+(i+1)+');" class="js-href-canceled">'+page_name+'</a></li>');
  },
});
(function($) {
	$.fn.wPaginate = function(settings, objData) {
		var _settings = $.extend({}, $.fn.wPaginate.defaultSettings, settings || {});
		var paginate = new Paginate(_settings, objData);
		var $el = paginate.generate();
		paginate.displayView();
		$(this).empty();
		$(this).append($el);
    return paginate.aryObjData;
	}
	$.fn.wPaginate.defaultSettings = {
		spread		: 5,			// number of links to display on each side (total 11)
		total			: 400,			// total number of results
		index			: 1,			// current index (0, 20, 40, etc)
		limit			: 10			// increment for index (limit)
	};
})(jQuery);
