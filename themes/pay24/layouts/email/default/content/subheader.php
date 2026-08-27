<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php extract($this->view_data); ?>
<table class="table600" width="600" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff">
  <tbody>
    <tr>
      <td class="subHeaderTD" style="border-collapse: collapse;font-family: Arial, Tahoma, Verdana, sans-serif;font-size: 14px;font-weight: normal;padding: 0;margin: 0;line-height: 155%;letter-spacing: 0;" height="10" align="left" bgcolor="#ffffff">
      <?php themeFunctions::loadAddons($this->theme_path . 'subheader_text'); ?>
      </td>
    </tr>
    <tr>

    </tr>
  </tbody>
</table>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>