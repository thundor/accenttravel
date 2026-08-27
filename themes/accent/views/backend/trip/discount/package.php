<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/package_scripts.php'); ?>
<div class="form-group row">
  <label for="discount_name" class="<?php echo $label_class; ?> text-center">Vacanta</label>
  <div class="<?php echo $value_class; ?>">
    <?php if($can_write){ ?>
    <select id="discount_type_id_selector" class="form-control" ><?php
    if($discount->type_id) { ?>
      <option value="<?php echo htmlspecialchars($discount->type_id); ?>"><?php echo htmlspecialchars($discount->name); ?></option><?php
    }
    ?>
    </select>
    <?php } else { ?>
    <div class="form-control" readonly><?php echo htmlspecialchars($discount->name); ?>&nbsp;</div>
    <?php } ?>
  </div>
</div>
<div class="form-group row">
  <label for="discount_type_id" class="<?php echo $label_class; ?> text-center">ID vacanta <small>(autocompletat)</small></label>
  <div class="<?php echo $value_class; ?>">
    <?php if($can_write){ ?>
    <input id="discount_type_id" type="text" maxlength="255" name="type_id" placeholder="ID vacanta" class="form-control" value="<?php echo htmlspecialchars($discount->type_id); ?>" required />
    <?php } else { ?>
    <div class="form-control" readonly><?php echo htmlspecialchars($discount->type_id); ?>&nbsp;</div>
    <?php } ?>
  </div>
</div>
<div class="form-group row">
  <label for="discount_name" class="<?php echo $label_class; ?> text-center">Nume vacanta <small>(autocompletat)</small></label>
  <div class="<?php echo $value_class; ?>">
    <?php if($can_write){ ?>
    <input id="discount_name" type="text" maxlength="255" name="name" placeholder="Nume vacanta" class="form-control" value="<?php echo htmlspecialchars($discount->name); ?>" required />
    <?php } else { ?>
    <div class="form-control" readonly><?php echo htmlspecialchars($discount->name); ?>&nbsp;</div>
    <?php } ?>
  </div>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>