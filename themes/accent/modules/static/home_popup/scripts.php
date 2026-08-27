<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<script type="text/javascript">
;(function($){
  $('#imagine_home_popup').lazy({
    afterLoad: function(element) {
      var imageSrc = element.data('src');
      $('#modal_home_popup').modal('show');
    },
  });
  $('#imagine_home_popup_link').on('click', function(e){
    $.ajax({url: '<?php echo site_url('/contorizare'); ?>'});
  });
  $('#imagine_home_popup_dont_show').on('click', function(e){
    $.ajax({url: '<?php echo site_url('/contorizare/dontshow'); ?>', data: {status: $(this).is(':checked')}});
  });
})(jQuery);
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>