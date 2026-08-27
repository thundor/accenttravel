<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = $this->view_data;
$zones = 0;
if(isset($data['status']) && is_array($data['status'])){
  $zones = count($data['status']);
}
?>
<script>
;(function($){
  var zone_index = <?php echo $zones; ?>;
  var $static_advantages_form = $('#static_advantages_form');
  function applyZoneEffects($container){
  }
  $('.static-sortable').sortable({
    revert: true,
    items: ">.static-sortable-item",
    handle: ".move-offer",
    start: function(e, ui){
      ui.placeholder.width(ui.item.width());
      ui.placeholder.height(ui.item.height());
    }
  });
  applyZoneEffects($static_advantages_form);
  $static_advantages_form.on('click', '#advantages_add_zone', function(e){
    e.preventDefault();
    console.log('click', this);
    var $new_tr = $('#static_advantages_form_models > div').clone();
    $('>.card', $new_tr).addClass('active-zone');
    $('>.card>.card-header>h2>strong', $new_tr).text(zone_index+1);
    $('[id*="_0_"]', $new_tr).each(function(){
      $(this).attr('id',$(this).attr('id').replace('_0_','_' + (zone_index + 1) + '_'));
    });
    $('[for*="_0_"]', $new_tr).each(function(){
      $(this).attr('for',$(this).attr('for').replace('_0_','_' + (zone_index + 1) + '_'));
    });
    $('[name$="[-1]"]', $new_tr).each(function(){
      $(this).attr('name',$(this).attr('name').replace('[-1]','[' + zone_index + ']'));
    });
    zone_index++;
    $new_tr.insertBefore($(this).parent());
    applyZoneEffects($new_tr);
    return false;
  }).on('click', '.advantages-remove-zone', function(e){
    e.preventDefault();
    console.log('click', this);
    $(this).closest('.card').parent().remove();
    return false;
  });
})(jQuery);
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>