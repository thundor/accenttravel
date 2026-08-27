<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<script type="text/javascript" src="<?php echo $this->theme_url; ?>assets/plugins/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="<?php echo $this->theme_url; ?>assets/plugins/ckeditor/adapters/jquery.js"></script>
<script type="text/javascript">
;(function($){
  CKEDITOR.dtd.$removeEmpty['i'] = false;
  var config_options = {
    filebrowserBrowseUrl: "<?php echo site_url('fileman/index.html'); ?>",
    filebrowserUploadUrl: "<?php echo site_url('backend/file_manager/upload?type=files'); ?>",
    filebrowserImageBrowseUrl: "<?php echo site_url('fileman/index.html?type=image'); ?>",
    filebrowserImageUploadUrl: "<?php echo site_url('backend/file_manager/upload?type=images'); ?>",
    extraPlugins: "jsplusInclude,jsplusBootstrapTools,jsplusBootstrapWidgets,jsplusBootstrapTableTools,jsplusBootstrapEditor"
        // + ",jsplusFileUploader,jsplusFileManager"
        ,
    toolbar: [
      [
        "Source",
        '-',
        // 'Save',
        // 'NewPage',
        // 'Preview',
        // 'Print',
        // '-',
        // 'Templates',
        'Cut',
        'Copy',
        'Paste',
        'PasteText',
        'PasteFromWord',
        '-',
        'Undo',
        'Redo',
        '-',
        'Find',
        'Replace',
        'SelectAll',
      ],
      // [
        // 'Scayt'
      // ],
      [
        'Form',
        'Checkbox',
        'Radio',
        'TextField',
        'Textarea',
        'Select',
        'Button',
        'ImageButton',
        'HiddenField'
      ],
      [
        'Image',
        // 'Flash',
        'Table',
        'HorizontalRule',
        'Smiley',
        'SpecialChar',
        'PageBreak',
        'Iframe'
      ],
      [
        "jsplus_bootstrap_button",
        "jsplus_font_awesome",
        "jsplus_bootstrap_label",
        // "jsplus_bootstrap_badge",
        // "jsplus_bootstrap_breadcrumbs",
        // "jsplus_bootstrap_alert",
        // "jsplus_bootstrap_gallery"
      ],
      [
        "jsplusUpload",
        "-",
        "jsplusUploadUrl",
        "-",
        "jsplusUploadImage",
        "jsplusUploadPreview",
        "jsplusUploadFile",
        "-",
        "jsplusFastUploadImage",
        "jsplusFastUploadPreview",
        "jsplusFastUploadFile"
      ],
      "/",
      [
        "jsplusBootstrapEditor",
        "jsplusBootstrapEditorSelected",
        "jsplusShowBlocks",
      ],
      [
        "jsplusBootstrapToolsContainerEdit",
        "jsplusBootstrapToolsContainerAdd",
        "jsplusBootstrapToolsContainerAddBefore",
        "jsplusBootstrapToolsContainerAddAfter",
        "jsplusBootstrapToolsContainerDelete",
        "jsplusBootstrapToolsContainerMoveUp",
        "jsplusBootstrapToolsContainerMoveDown",
        "-",
        "jsplusBootstrapToolsRowEdit",
        "jsplusBootstrapToolsRowAdd",
        "jsplusBootstrapToolsRowAddBefore",
        "jsplusBootstrapToolsRowAddAfter",
        "jsplusBootstrapToolsRowDelete",
        "jsplusBootstrapToolsRowMoveUp",
        "jsplusBootstrapToolsRowMoveDown",
        "-",
        "jsplusBootstrapToolsColEdit",
        "jsplusBootstrapToolsColAdd",
        "jsplusBootstrapToolsColAddBefore",
        "jsplusBootstrapToolsColAddAfter",
        "jsplusBootstrapToolsColDelete",
        "jsplusBootstrapToolsColMoveLeft",
        "jsplusBootstrapToolsColMoveRight"
      ],
      [
        "jsplus_bootstrap_table_new",
        "jsplus_bootstrap_table_conf",
        "-",
        "jsplusTableRowAddBefore",
        "jsplusTableRowAddAfter",
        "jsplus_bootstrap_table_row_conf",
        "jsplusTableRowMoveUp",
        "jsplusTableRowMoveDown",
        "jsplusTableRowDelete",
        "-",
        "jsplusTableColAddBefore",
        "jsplusTableColAddAfter",
        "jsplus_bootstrap_table_col_conf",
        "jsplusTableColMoveLeft",
        "jsplusTableColMoveRight",
        "jsplusTableColDelete",
        "-",
        "jsplus_bootstrap_table_cell_conf",
        "jsplusTableCellMergeRight",
        "jsplusTableCellMergeDown",
        "jsplusTableCellSplit"
      ],
      "/",
      [
        'Styles',
        'Format',
        'Font',
        'FontSize'
      ],
      [
        'TextColor',
        'BGColor'
      ],
      [
        'Maximize',
        'ShowBlocks'
      ],
      [
        'Bold',
        'Italic',
        'Underline',
        'Strike',
        'Subscript',
        'Superscript',
        '-',
        'CopyFormatting',
        'RemoveFormat'
      ],
      [
        'NumberedList',
        'BulletedList',
        '-',
        'Outdent',
        'Indent',
        '-',
        'Blockquote',
        'CreateDiv',
        '-',
        'JustifyLeft',
        'JustifyCenter',
        'JustifyRight',
        'JustifyBlock',
        '-',
        'BidiLtr',
        'BidiRtl',
        'Language'
      ],
      [
        'Link',
        'Unlink',
        'Anchor'
      ]
    ],
    language: "en",
    skin: "be",
    allowedContent: true,
	extraAllowedContent: 'module[*]',
	autoParagraph: false,
    height: 700,
    asdasd: '<?php echo config_item('theme')['theme']; ?>',
    jsplusInclude: {
      framework: "b4",
      css: [
        "<?php echo $this->theme_url; ?>assets/plugins/jquery-ui/css/jquery-ui.css",
        "<?php echo $this->theme_url; ?>assets/plugins/bootstrap/4.0.0.alpha/css/bootstrap.min.css",
        "<?php echo $this->theme_url; ?>assets/plugins/font-awesome/css/font-awesome.min.css",
        "<?php echo $this->theme_url; ?>assets/css/default.css?v=1.0.1",
        "<?php echo $this->theme_url; ?>assets/css/responsive.css?v=1.0.1",
        "<?php echo $this->theme_url; ?>assets/css/common.css?v=1.0.1",
        "<?php echo $this->theme_url; ?>assets/css/custom.css?v=1.0.1",
        "<?php echo $this->theme_url; ?>assets/css/ckeditor.css?v=1.0.1",
        <?php if('newux' === config_item('theme')['theme']){ ?>
        "<?php echo $this->theme_url; ?>assets/plugins/vuetify/3.8.4/dist/vuetify.min.css",
        "<?php echo $this->theme_url; ?>assets/plugins/material-design-icons/7.4.47/css/materialdesignicons.css",
        "<?php echo $this->theme_url; ?>assets/plugins/quasar/2.17.7/dist/quasar.prod.css",
        "<?php echo $this->theme_url; ?>assets/css/ada.css?newux=1",
        "<?php echo $this->theme_url; ?>assets/css/tudor.css?newux=1",
        <?php } ?>
      ],
      inContainer: false,
      includeJs: false,
      includeCss: false,
      includeJQuery: false,
      useWet: false,
      previewStyles: true,
      includeIcons: false,
      includeTheme: false,
      includeJsToGlobalDoc: false,
      includeCssToGlobalDoc: false
    },
    jsplusBootstrapTools: {
      previewStyles: true
    },
    jsplusFileManager: {
      returnUrlPrefix: "<?php echo site_url('backend'); ?>"
    },
    jsplusFileUploader: {
      urlUploader: "<?php echo site_url('backend/file_manager/upload'); ?>",
      urlFiles: "<?php echo site_url('backend/file_manager/files'); ?>"
    }
  };
  config_options.jsplusBootstrapEditor = {
    HTMLEditorConfig: $.extend(true,{},config_options)
  };
  config_options.jsplusBootstrapEditor.HTMLEditorConfig.jsplusInclude.css = [];
  $.fn.makeCKEditor = function(options){
    var opts = $.extend({},config_options, options);
    // console.log(opts);
    // return false;
    $(this).ckeditor(opts);
    
    return this;
  };
  $(window).on('error','img',function () {
    console.log(this.src);
    $(this).unbind("error").attr("src", "broken.gif");
  });
})(jQuery);
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>