<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<script type="text/javascript" src="<?php echo $this->theme_url; ?>assets/plugins/calendar/build/js/moment.min.js"></script>
<script type="text/javascript" src="<?php echo $this->theme_url; ?>assets/plugins/calendar/build/js/moment-with-locales.min.js"></script>
<script type="text/javascript" src="<?php echo $this->theme_url; ?>assets/plugins/calendar/js/caleran.js?v=1.0.49"></script>
<script type="text/javascript" src="<?php echo $this->theme_url; ?>assets/plugins/calendar/js/jquery.hammer.js"></script>
<script type="text/javascript">
(function($){
  var caleran_options = {
    startEmpty: true,
    singleDate: true,
    locale: 'ro',
    enableKeyboard: true,
    minDate: moment().startOf( 'day' ),
    autoCloseOnSelect: true,
    startOnMonday: true,
    format: 'DD.MM.Y',
    onbeforeselect: function(elem, start_date, end_date){
      $(elem.$elem).focus();
      return true;
    }
  };
  $.fn.makeCaleranDatepicker = function(options){
    var opts = $.extend({},caleran_options, options);
    $(this).caleran(opts)
    .on('blur',function(e){
      var caleran = $(this).data("caleran");
      if(this.value.length){
        caleran.globals.firstValueSelected = true;
      } else {
        caleran.globals.firstValueSelected = false;
      }
      caleran.globals.keyboardHoverDate = null;
      return true;
    });
    return this;
  };
})(jQuery);
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>