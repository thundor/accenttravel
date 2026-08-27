<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php extract($this->view_data); ?>
<center>
    <table width="100%" align="center" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff">
      <tbody>
        <tr>
          <td style="border-collapse: collapse;" valign="top" align="center">
            <table style="border-top-style:solid; border-top-color:#ffffff; border-top-width:1px;" width="100%" align="center" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff">
              <tbody>
                <tr>
                  <td style="font-size: 0;line-height: 0;border-collapse: collapse;" height="21" valign="top" align="center" bgcolor="#ffffff">&nbsp;</td>
                </tr>
              </tbody>
            </table>
            <?php //include 'content/subheader.php'; ?>
            <table class="table600" width="600" align="center" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff">
              <tbody>
                <tr>
                  <td class="sectionRegularInfoTextTD" style="border-collapse: collapse;color: #0096ff;font-family: Arial, Tahoma, Verdana, sans-serif;font-size: 13px;font-weight: lighter;padding: 0;margin: 0;text-align: left;line-height: 165%;letter-spacing: 0;" height="10" valign="middle" align="center" bgcolor="#ffffff">
                    <?php echo $this->content(); ?>
                    <br />
                    Cu stima, <br />
                    Echipa Accent Travel&amp;Events
                  </td>
                </tr>
              </tbody>
            </table>
          </td>
        </tr>
      </tbody>
    </table>
</center>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>