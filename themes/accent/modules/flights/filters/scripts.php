<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<script type="text/javascript">
/* display escale */
$(".filterTitleT").on("click", function (e) {
  $("#allFiltersT").toggleClass("hiddenFiltT");
  if ($("#allFiltersT").hasClass("hiddenFiltT") == false) {
    $(".filterTitleT i:last-child").removeClass("fa-plus-square-o").addClass("fa-minus-square-o").css("color", "#F00");
  } else {
    $(".filterTitleT i:last-child").removeClass("fa-minuss-square-o").addClass("fa-plus-square-o").css("color", "#0275d8");
  }
});
if ($(".filterTitleT i:last-child").hasClass("fa-minus-square-o")) {
  $(".filterTitleT i:last-child").removeClass("fa-minus-square-o").addClass("fa-plus-square-o");
};
$(".calendarT").on("click", function (e) {
  $("#calendarFlights").toggleClass("hiddenFiltT");
  if ($("#calendarFlights").hasClass("hiddenFiltT") == false) {
    $(".calendarT i:last-child").removeClass("fa-plus-square-o").addClass("fa-minus-square-o").css("color", "#F00");
  } else {
    $(".calendarT i:last-child").removeClass("fa-minuss-square-o").addClass("fa-plus-square-o").css("color", "#0275d8");
  }
});
if ($(".calendarT i:last-child").hasClass("fa-minus-square-o")) {
  $(".calendarT i:last-child").removeClass("fa-minus-square-o").addClass("fa-plus-square-o");
};
/*---------- JQUERY FOR +/- # Days table from FLIGHTS SEARCH RESULTS ---------- */
$(document).on("mouseenter",".table3Days td", function (e) {
  e.preventDefault();
  var index = $(this).index();
  jQuery(".table3Days tr:first-child th").eq(index).addClass("hoverBGT");
  $(this).parents(".table3Days tr").find("th").addClass("hoverBGL");
  jQuery(this).addClass("hoverTD");
  jQuery(this).children(".toolTipPrice").show();
  jQuery(this).css("position", "relative");
}).on("mouseleave",".table3Days td", function (e) {
  e.preventDefault();
  var index = $(this).index();
  jQuery(".table3Days tr:first-child th").eq(index).removeClass("hoverBGT");
  $(this).parents(".table3Days tr").find("th").removeClass("hoverBGL");
  jQuery(this).removeClass("hoverTD");
  jQuery(this).children(".toolTipPrice").hide();
  jQuery(this).css("position", "static");
});
jQuery(".plus3Days").on("click", function (e) {
  jQuery(".table3Days").toggle("slow");
});
$(function () {
  var $price_slider = $("#slider-range").slider({
    range: true,
    min: 0,
    max: 0,
    values: [0, 0],
    slide: function (event, ui) {
      $(this).trigger('updatePrice',ui);
    }
  }).on('updatePrice', function(e, ui){
    if(typeof flights_result !== 'undefined' && flights_result.price_range){
      if(ui){
        var slider_values = ui.values;
      } else {
        var $price_slider = $(this).slider();
        var slider_values = $price_slider.slider('values');
      }
      $("#amount").val(parseFloat(flights_result.price_range[slider_values[ 0 ]]).toLocaleString('ro') + " <?php echo $this->_ci->currency_symbol; ?> - " + parseFloat(flights_result.price_range[slider_values[ 1 ]]).toLocaleString('ro') + ' <?php echo $this->_ci->currency_symbol; ?>');
    }
  });
  $price_slider.trigger('updatePrice');
});
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>