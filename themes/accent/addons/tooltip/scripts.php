<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<script type="text/javascript">
$('body').uitooltip({
  items: '[data-toggle=tooltip],.hasTooltip',
  position: {
    my: "center bottom-20",
    at: "center top",
    using: function (position, feedback) {
      $(this).css(position);
      $("<div>")
          .addClass("arrow")
          .addClass(feedback.vertical)
          .addClass(feedback.horizontal)
          .appendTo(this);
    }
  }
});
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>