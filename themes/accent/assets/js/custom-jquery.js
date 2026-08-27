;(function($){
$(document).on('mouseenter mouseleave', '.nav-item.dropdown.clickable', function(){
	$(this).toggleClass('show');
});
$.fn.hasScrollBar = function() {
  return this.get(0).scrollHeight > this.height();
}
$(document).ready(function () {

});

})(jQuery);