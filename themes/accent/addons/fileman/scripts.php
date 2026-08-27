<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<div class="modal fade" id="modal_fileman" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="d-flex flex-column justify-content-center" style="height:100%;pointer-events:none;">
    <div class="modal-dialog modal-lg" role="document" style="max-height: 90%;pointer-events:auto;">
      <div class="modal-content">
        <div class="modal-body p-0">
          <button type="button" class="close custom-close" data-dismiss="modal" aria-label="Inchide">
            <span aria-hidden="true">&times;</span>
          </button>
          <iframe id="fileman_iframe" src="about:blank" style="width:800px;height:500px;max-width:100%;max-height:100%" frameborder="0"></iframe>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
;(function($){
  var $target_input;
  var urls = {
    file: '<?php echo site_url('fileman/index.html?integration=input&env=iframe'); ?>',
    image: '<?php echo site_url('fileman/index.html?type=image&integration=input&env=iframe'); ?>'
  };
  $('#modal_fileman').on('hidden.bs.modal', function(){
    $target_input = null;
    // $('#fileman_iframe').attr('src','about:blank');
  });
  $(document).on('click', '.fileman-input-group .fileman-browse', function(){
    var $fileman_input_group = $(this).closest('.fileman-input-group');
    var $fileman_input = $fileman_input_group.children('.fileman-input');
    var type = $(this).data('fileman_type');
    type = type && typeof urls[type] !== undefined ? type : 'image';
    var url = urls[type];
    $target_input = $fileman_input;
    if(url !== $('#fileman_iframe').attr('src')){
      $('#fileman_iframe').attr('src',url);
    }
    $('#modal_fileman').modal('show');
  });
  window.filemanUpdate = function(file){
    if(!$target_input) return;
    $target_input.val(file.fullPath);
    $('#modal_fileman').modal('hide');
  };
})(jQuery);
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>