<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<script type="text/javascript">
// varste copii rezervare Bilete Avion
  $("#copiiZbor").on("change", function () {
    $(this).find("option:selected").each(function () {
      var optionValue = $(this).attr("value");
      if (optionValue == 2) {
        $("#copiiZborArea .varsteCopii").show();
        $("#aniCop1Zbor").show();
        $("#aniCop2Zbor").hide();
        $("#copiiZborArea .varsteCopii p#v1Z").show();
        $("#copiiZborArea .varsteCopii p#v2Z").hide();
      }
      if (optionValue == 3) {
        $("#copiiZborArea .varsteCopii").show();
        $("#aniCop2Zbor").show();
        $("#aniCop1Zbor").show();
        $("#copiiZborArea .varsteCopii p#v1Z").show();
        $("#copiiZborArea .varsteCopii p#v2Z").show();
      }
      if (optionValue == 1) {
        $("#copiiZborArea .varsteCopii").hide();
        $("#aniCop2Zbor").hide();
        $("#aniCop1Zbor").hide();
        $("#copiiZborArea .varsteCopii p#v1Z").hide();
        $("#copiiZborArea .varsteCopii p#v2Z").hide();
      }
    });
  });
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>