<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php extract($this->view_data); ?>
<table width="100%" align="center" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff">
  <tbody>
    <tr>
      <td style="border-collapse: collapse;" valign="top" align="center">

        <table style="border-top-style:solid; border-top-color:#eeeeee; border-top-width:1px;" width="100%" align="center" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff">
          <tbody>
            <tr>
              <td style="font-size: 0;line-height: 0;border-collapse: collapse;" height="50" valign="top" align="center" bgcolor="#ffffff">&nbsp;</td>
            </tr>
          </tbody>
        </table>

        <table class="table600" width="600" align="center" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff">
          <tbody>
            <tr>
              <td style="border-collapse: collapse;" valign="top" align="center" bgcolor="#ffffff">
                <table align="center" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff" style="width:100%">
                  <tbody>
                    <tr>

                      <td class="pictureAlternativeTextTD" style="border-collapse: collapse;color: #bbbbbb;text-align: center;line-height: 1px;" width="250" valign="top" align="center" bgcolor="#ffffff">
                        <a href="#" target="_blank" class="buttonsAndImagesLink" style="color: #bbbbbb;text-decoration: none;outline: none;"><img src="<?php echo $this->theme_url; ?>assets/emails/images/emailheader.png" style="width:100%; height:auto; margin:auto; margin-bottom:15px;" alt="" hspace="0" height="76" width="180" vspace="0" align="top" border="0"></a>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </td>
            </tr>
          </tbody>
        </table>

        <table class="table600" width="600" align="center" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff">
          <tbody>
            <tr>

              <td class="companySloganTD" style="border-collapse: collapse;color: #000000;font-family: Arial, Tahoma, Verdana, sans-serif;font-size: 16px;font-weight: bold;padding: 0;margin: 0;text-align: center;line-height: 150%;letter-spacing: 0;" height="15" valign="middle" align="center" bgcolor="#ffffff">Suntem bucuroși că ai ales să zburăm împreună!</td>
            </tr>
          </tbody>
        </table>

        <table style="border-bottom-style:solid; border-bottom-color:#eeeeee; border-bottom-width:1px;" width="100%" align="center" cellspacing="0" cellpadding="0" border="0" bgcolor="#ffffff">
          <tbody>
            <tr>
              <td style="font-size: 0;line-height: 0;border-collapse: collapse;" height="21" valign="top" align="center" bgcolor="#ffffff">&nbsp;</td>
            </tr>
          </tbody>
        </table>
      </td>
    </tr>
  </tbody>
</table>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>