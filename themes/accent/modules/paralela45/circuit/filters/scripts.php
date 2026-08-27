<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<script type="text/javascript">
var browserWidth = $(window).width();
/* ----------- 	pentru afisare filtre din paginile de categorie la rezolutie < 768px 	------------------ */
if (browserWidth < 769) {
  $(".filterTitle").on("click", function (e) {
    $("#allFilters").toggleClass("hiddenFilt");
    if ($("#allFilters").hasClass("hiddenFilt") == false) {
      $(".filterTitle i:last-child").removeClass("fa-plus-square-o").addClass("fa-minus-square-o");
    } else {
      $(".filterTitle i:last-child").removeClass("fa-minuss-square-o").addClass("fa-plus-square-o");
    }
  });
}
if ($(".filterTitle i:last-child").hasClass("fa-minus-square-o")) {
  $(".filterTitle i:last-child").removeClass("fa-minus-square-o").addClass("fa-plus-square-o");
}
/* ---------------- 	buton de aplica & resetare filtre 	------------------ */
$(window).scroll(function () {
  if ($(this).scrollTop() > 250) {
    $('#applyFilters').fadeIn();
  }
});
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>