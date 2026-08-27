<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
require_once(dirname(__DIR__) . '/titles.php'); 
?>
<script type="text/javascript">
var titles_selections = <?php echo json_encode($this->titles_selections); ?>;
var select2_adult_titles_prefix_selections = [];
var select2_children_titles_prefix_selections = [];
var select2_titles_prefix_selections = $.map(titles_selections,function(val,i){
  var obj = {
    id: i,
    text: val
  };
  if(i !== 'chd'){
    select2_adult_titles_prefix_selections.push(obj);
    if(i !== 'mrs'){
      select2_children_titles_prefix_selections.push(obj);
    }
  }
  return obj;
});
</script>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>