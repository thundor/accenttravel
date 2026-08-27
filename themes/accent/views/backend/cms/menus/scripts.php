<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$data = $this->view_data;
$can_write = $this->_method !='view';
if($can_write){
themeFunctions::jsLang('warning_form_not_ready'); ?>
<script>
(function($){
  var $action_buttons = $('button[type=submit][form=menuForm]');
  var $message_container = $('#result_menuForm');
  var form_ready = true;
  var page_select2 = {
    previous_params : null,
    previous_data : [],
    default_params : null,
    default_data : null,
  };
  $('#page_selector').select2_4({
    theme:'bootstrap',
    placeholder:'Alegere pagina pentru precompletare automata', 
    width: '100%',
    allowClear:true,
    minimumResultsForSearch:1,
    ajax: {
      url: '<?php echo site_url('backend/cms/pages/getlist'); ?>',
      dataType: 'json',
      type: 'POST',
      delay: 400,
      data: function (params) {
        var limit = 10;
        var page = params.page > 1 ? params.page : 1;
        var term = params.term;
        if (typeof params.term === 'undefined'){
          term = "";
          if(page_select2.previous_params && page_select2.previous_params.data){
            if (typeof page_select2.previous_params.data.search === 'string'){
              term = page_select2.previous_params.data.search;
            }
          }
        }
        var form_data = {
          search: term,
          page: page,
          simple: true,
          <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
          '<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>': '<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>',
          <?php } ?>
          select: [
            '`p.page_id` AS "id"',
            'CONCAT(pc.title, " (", pc.slug, ")") AS "text"',
            'pc.title',
            'pc.slug',
            'pc.route',
            'pc.params',
            'IF(p.status<>1,true,false) AS "disabled"',
          ],
          limit: limit
        };
        return form_data;
      },
      transport: function (params, success, failure) {
        if(typeof params.data.search === 'undefined'){
          if(page_select2.previous_params){
            params.data.search = page_select2.previous_params.data.search;
          }
        }
        if(page_select2.previous_params && params.data.search === page_select2.previous_params.data.search && params.data.page === 1){
          success(page_select2.previous_data);
          return false;
        } else {
          page_select2.previous_params = params;
        }
        if(page_select2.default_data && (typeof params.data.search === 'undefined' || params.data.search === '') && params.data.search === page_select2.previous_params.data.search){
          page_select2.default_params = page_select2.previous_params;
          if(params.data.page === 1){
            page_select2.previous_data = page_select2.default_data;
            success(page_select2.default_data);
            return false;
          }
        }
        var $request = $.ajax(params);
        $request.then(function(response){
          var results = [];
          if(response.status != 'success'){
            alert(response.message);
            
          }
          var pages = response.data.pages;
          var results = $.map(response.data.pages, function(group) {
            return {
              id: group.id,
              text: group.text,
              title: group.title,
              slug: group.slug,
              route: group.route,
              params: group.params,
              disabled: parseInt(group.disabled) ? true : false
            };
          });
          success_data = {
            results: results,
            pagination: {
              more: (page_select2.previous_params.data.page < response.data.max_pages)
            }
          };
          if(params.data.page===1){
            page_select2.previous_data = success_data;
            if(typeof params.data.search === 'undefined' || params.data.search === ''){
              page_select2.default_data = page_select2.previous_data;
            }
          }
          success(success_data);
        });
        $request.fail(function(){
          page_select2.previous_params=page_select2.default_params;
          page_select2.previous_data=page_select2.default_data;
          failure();
        });
        return $request;
      },
      processResults: function (data, params) {
        return data;
      }
    }
  }).on("select2_4:opening", function() {
    if(page_select2.previous_params){
      if(typeof page_select2.previous_params.data.search === 'string'){
        var $el = $(this);
        var $search = $el.data('select2_4').dropdown.$search || $el.data('select2_4').selection.$search;
        $search.val(page_select2.previous_params.data.search);
      }
    }
  }).on("select2_4:selecting", function() { 
    if(page_select2.previous_params && (typeof page_select2.previous_params.data.search !== 'undefined' && page_select2.previous_params.data.search !== '')){
      page_select2.previous_params=page_select2.default_params;
      page_select2.previous_data=page_select2.default_data;
    }
  }).on('change',function(event){
    var selection = $(this).select2_4('data');
    if(!selection || !selection.length) return;
    var selected_item = selection[0];
    $(this).val(null).trigger('change.select2_4');
    
    $('#page_title').val(selected_item.title);
    $('#page_url').val(selected_item.slug);
  });
  var $menu_structure = $('#menu_structure');
  
  var updateOutput = function(e){
    var list   = e.length ? e : $(e.target),
      output = list.data('output');
    if (window.JSON) {
      output.val(window.JSON.stringify(list.nestable('serialize')));//, null, 2));
    } else {
      alert('JSON browser support required for this demo.');
    }
  };
  var menu_structure_opts = {
    expandBtnHTML: '<button data-action="expand" type="button"><i class="fa fa-plus"></i></button>',
    collapseBtnHTML: '<button data-action="collapse" type="button"><i class="fa fa-minus"></i></button>',
  };
  $menu_structure.nestable(menu_structure_opts).on('change', updateOutput);
  function addMenuItem(data,$parent){
    var $menu_element = $('<li class="dd-item dd3-item">\
                            <button type="button" class="edit-menu-item btn btn-primary btn-sm"><i class="fa fa-pencil"></i></button>\
                            <button type="button" class="delete-menu-item btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>\
                            <div class="dd-handle dd3-handle"><i class="fa fa-arrows"></i></div>\
                            <div class="dd3-content">Title</div>\
                          </li>');
    $menu_element.data(data);
    $('.dd3-content',$menu_element).text(data.title);
    $menu_element.appendTo($('>ol',$parent));
    return $menu_element;
  }
  function buildMenu(menu_data, $menu_container){
    $.each(menu_data,function(i){
      $elem = addMenuItem(this, $menu_container);
      if(this.children){
        var $submenu = $('<ol class="dd-list" />');
        var $collapse = $(menu_structure_opts.collapseBtnHTML);
        var $expand = $(menu_structure_opts.expandBtnHTML).hide();
        $expand.prependTo($elem);
        $collapse.prependTo($elem);
        $submenu.appendTo($elem);
        
        buildMenu(this.children, $elem);
      }
    });
  }
  $('#add_page_form').on('submit', function(){
    var data = {
      title : $.trim($('#page_title').val()),
      url : $.trim($('#page_url').val()),
      target : $.trim($('#page_target').val())
    };
    var $li = $('.editing', $menu_structure);
    if(this.task.value == 'edit'){
      var data = {
        title : $.trim($('#page_title').val()),
        url : $.trim($('#page_url').val()),
        target : $.trim($('#page_target').val())
      };
      $li.data(data);
      $('> .dd3-content',$li).text(data.title);
    } else {
      addMenuItem(data, $menu_structure);
    }
    $menu_structure.trigger('change');
    if($li.length){
      $('> button.edit-menu-item',$li)[0].click();
    }
    $('#edit_page_button').hide();
    $('#cancel_edit_page_button').hide();
    this.task.value = '';
    this.reset();
  });
  $('#edit_page_button').click(function(){
    this.form.task.value = 'edit';
  });
  var menu_data = <?php echo json_encode($data['menu']); ?>;
  buildMenu(menu_data, $menu_structure);
  updateOutput($menu_structure.data('output', $('#menu_structure-output')));
  
  $menu_structure.on('click','button.edit-menu-item', function(){
    var $li = $(this).parent();
    if($li.hasClass('editing')){
      $li.removeClass('editing');
      $('#edit_page_button').hide();
      $('#cancel_edit_page_button').hide();
      return;
    }
    $('#page_title').val($li.data('title'));
    $('#page_url').val($li.data('url'));
    $('#page_target').val($li.data('target'));
    $('.editing', $menu_structure).removeClass('editing');
    $li.addClass('editing');
    $('#cancel_edit_page_button').show();
    $('#edit_page_button').show();
  }).on('click','button.delete-menu-item', function(){
    var $li = $(this).parent();
    if($li.hasClass('editing')){
      $('> button.edit-menu-item',$li)[0].click();
    }
    if($('.editing',$li).length){
      $('.editing > button.edit-menu-item',$li)[0].click();
    }
    var $ul = $li.parent();
    $li.remove();
    if(!$ul.children().length && !$ul.parent().is($menu_structure)){
      $('button[data-action]',$ul.parent()).remove();
      $ul.remove();
    }
    $menu_structure.trigger('change');
  });
  $('#cancel_edit_page_button').on('click', function(){
    $('.editing > button.edit-menu-item', $menu_structure)[0].click();
    $('#add_page_form')[0].reset();
  });
  $('#menu_selector').on('change', function(){
    var name = this.value;
    window.location.href = `<?php echo site_url('backend/cms/menus'); ?>/${name}`;
  });
  $('#create_menu').on('click', function(){
    swal({
      text: 'Introduceti numele meniului',
      content: 'input',
      button: {
        text: "Creaza",
      },
    }).then(name => {
      if (!name || !$.trim(name)) return;
      name = name.replace(/\W+/g, " ");
      name = $.trim(name);
      name = name.replace(/\W+/g, "-");
      window.location.href = `<?php echo site_url('backend/cms/menus'); ?>/${name}`;
    });
  });
  $action_buttons.prop('disabled',false);
  
  function submitFormSubmitCallback($form,resp,$error_container){
    form_ready = true;
    if(resp.status !== 'success'){
      return true;
    }
    form_change = false;
    $form[0].submit();
    $form[0].task.value = '';
    return true;
  }
  $('#menuForm').on('submit',function(e){
    $message_container.empty();
    if(!form_ready){
      showMessage($message_container, js_lang.warning_form_not_ready, 'warning');
      return false;
    }
    if(this.item.value=='new'){
      if(this.structure.value=='[]'){
        showMessage($message_container, 'Meniul este gol. Adaugati cel putin un element pentru a putea crea meniul', 'warning');
        return false;
      }
      swal({
        text: 'Introduceti numele meniului',
        content: 'input',
        button: {
          text: "Creaza",
        },
      }).then(name => {
        if (!name || !$.trim(name)) return;
        name = name.replace(/\W+/g, " ");
        name = $.trim(name);
        name = name.replace(/\W+/g, "-");
        $('#menuForm input[name=item]').val(name);
        $('#menuForm').submit();
      });
      return false;
    }
    form_ready = false;
    e.preventDefault();basicFormPostSubmit(this,this.action,submitFormSubmitCallback,true);
  });
  
})(jQuery);
</script><?php
} ?>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
  