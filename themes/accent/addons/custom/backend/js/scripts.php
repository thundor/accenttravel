<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::addIncludePath(themeFunctions::relativePath(__FILE__), __DIR__ . '/../../common/js/scripts.php'); ?>
<script type="text/javascript" src="<?php echo $this->theme_url; ?>assets/js/excel-jquery.js?v=1.0.1"></script>
<?php themeFunctions::loadAddons(__FILE__); ?>
<script>
var top_offset = 70;
var bottom_offset = 0;
var form_change = false;
;(function($){
  
// $('.side-navbar-wrapper li.current').addClass('active').parents('ul.collapse').addClass('show').prev('a').removeClass('collapsed').attr({'aria-expanded':'true'});
$('#toggle-btn').on('click', function (e) {
  e.preventDefault();
  if ($(window).outerWidth() > 1194) {
    $('nav.side-navbar').toggleClass('shrink');
    // $.cookie("backend-side-navbar-shrink", $('nav.side-navbar').hasClass('shrink') ? 1 : 0);
    $('.page').toggleClass('active');
  } else {
    $('nav.side-navbar').toggleClass('show-sm');
    $('.page').toggleClass('active-sm');
  }
});

$(window).bind('beforeunload', function(event) {
  if(form_change){
    var message = 'Aveti setari nesalvate';
    return message;
  }
} );

})(jQuery);
</script>
<?php themeFunctions::debugFileLine('end'); ?>