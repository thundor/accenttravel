<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php extract($this->view_data); ?>
<table class="table600" width="600" align="center" cellspacing="0" cellpadding="0" border="0">
  <tbody>
    <tr>
      <td class="subHeaderTD" style="border-collapse: collapse;color: #0096ff;font-family: Arial, Tahoma, Verdana, sans-serif;font-size: 16px;font-weight: lighter;padding: 0;margin: 0;text-align: center;line-height: 155%;letter-spacing: 0;" height="10" valign="middle" align="center">
      <?php themeFunctions::loadAddons($this->theme_path . 'subheader_text'); ?>
      </td>
    </tr>
    <tr>

    </tr>
  </tbody>
</table>
<table class="table600" style="border-bottom-style:solid; border-bottom-color:#e8e8e8; border-bottom-width:1px;" width="600" align="center" cellspacing="0" cellpadding="0" border="0" bgcolor="#f8f8f8">
  <tbody>
    <tr>
    </tr>
  </tbody>
</table>
<table class="table600" style="border-top-style:solid; border-top-color:#ffffff; border-top-width:1px;" width="600" align="center" cellspacing="0" cellpadding="0" border="0" bgcolor="#f8f8f8">
  <tbody>
    <tr>
      <td style="font-size: 0;line-height: 0;border-collapse: collapse;" height="15" valign="top" align="center" bgcolor="#f8f8f8">&nbsp;</td>
    </tr>
  </tbody>
</table>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>