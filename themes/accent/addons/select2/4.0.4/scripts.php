<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<script type="text/javascript" src="<?php echo $this->theme_url; ?>assets/plugins/select2/4.0.4/js/select2_4.full.min.js"></script>
<script type="text/javascript" src="<?php echo $this->theme_url; ?>assets/plugins/select2/4.0.4/js/i18n/ro.js"></script>
<script type="text/javascript">
(function($) {
  var Defaults = $.fn.select2_4.amd.require('select2_4/defaults');
  $.extend(Defaults.defaults, {
    searchInputPlaceholder: ''
  });
  var SearchDropdown = $.fn.select2_4.amd.require('select2_4/dropdown/search');
  var _renderSearchDropdown = SearchDropdown.prototype.render;
  SearchDropdown.prototype.render = function(decorated) {
    // invoke parent method
    var $rendered = _renderSearchDropdown.apply(this, Array.prototype.slice.apply(arguments));
    this.$search.attr('placeholder', this.options.get('searchInputPlaceholder'));
    return $rendered;
  };
})(window.jQuery);
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>