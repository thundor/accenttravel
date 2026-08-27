<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<script type="text/javascript" src="<?php echo $this->theme_url; ?>assets/plugins/inputmask/min/jquery.inputmask.bundle.min.js?v=1.0.2"></script>
<script type="text/javascript">
(function($){
  var input_mask_date_options = { 
    mask: "1.2.y", 
    alias: 'dd.mm.yyyy', 
    placeholder: "zz.ll.aaaa",
    separator: '.',
    ignorables: [ 9, 13, 19, 27, 33, 34, 35, 36, 37, 38, 39, 40, 45, 93, 112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122, 123, 0, 229 ],
  };
  $.fn.makeInputmaskDate = function(options){
    var opts = $.extend({},input_mask_date_options, options);
    $(this).inputmask(opts);
    return this;
  };
  var input_mask_date_options2 = { 
    mask: "1/2/y", 
    alias: 'dd/mm/yyyy', 
    placeholder: "zz/ll/aaaa",
    separator: '/',
    ignorables: [ 9, 13, 19, 27, 33, 34, 35, 36, 37, 38, 39, 40, 45, 93, 112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122, 123, 0, 229 ],
  };
  $.fn.makeInputmaskDate2 = function(options){
    var opts = $.extend({},input_mask_date_options2, options);
    $(this).inputmask(opts);
    return this;
  };
  var input_mask_date_options3 = { 
    alias: 'yyyy-mm-dd', 
    placeholder: "aaaa-ll-zz",
    separator: '-',
    ignorables: [ 9, 13, 19, 27, 33, 34, 35, 36, 37, 38, 39, 40, 45, 93, 112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122, 123, 0, 229 ],
  };
  $.fn.makeInputmaskDate3 = function(options){
    var opts = $.extend({},input_mask_date_options3, options);
    $(this).inputmask(opts);
    return this;
  };
})(jQuery);
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>