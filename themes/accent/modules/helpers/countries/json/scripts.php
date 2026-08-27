<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
require_once(dirname(__DIR__) . '/countries.php'); 
?>
<script type="text/javascript">
var countries_selections = <?php echo json_encode($this->countries_selections); ?>;
var select2_countries_selections = $.map(countries_selections,function(val,i){
  return {
    id: i,
    text: val.text,
    prefix: val.prefix
  };
});
var select2_countries_prefix_selections = $.map(countries_selections,function(val,i){
  return {
    id: i,
    text: val.text + ' (' + val.prefix + ')',
    prefix: val.prefix
  };
});
</script>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>