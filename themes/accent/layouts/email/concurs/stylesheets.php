<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<style type="text/css">
.ReadMsgBody {
  width: 100%;
}

.ExternalClass {
  width: 100%;
}

body {
  background-color: #f8f8f8;
  -webkit-text-size-adjust: 100%;
  -ms-text-size-adjust: 100%;
  -webkit-font-smoothing: antialiased;
  margin: 0 !important;
  padding: 0 !important;
  width: 100% !important;
}

@media only screen and (max-width: 599px) {
  body {
    min-width: 100% !important;
  }
  table[class=eraseForMobile] {
    width: 0;
    display: none !important;
  }
  table[class=table600] {
    width: 420px !important;
  }
  table[class=table200] {
    width: 420px !important;
  }
  table[class=table200B] {
    width: 420px !important;
    margin: 20px 0 0 0 !important;
  }
  table[class=youreceive] {
    width: 160px !important;
  }
  table[class=contactJpg] {
    width: 230px !important;
  }
  td[class=companySloganTD] {
    font-size: 19px !important;
  }
  td[class=sectionsHeaderTD] {
    font-size: 22px !important;
  }
  td[class=subHeaderTD] {
    font-size: 17px !important;
  }
  td[class=sectionRegularInfoTextTD] {
    font-size: 14px !important;
  }
  img[class=image600] {
    width: 420px !important;
    height: 203px !important;
  }
  img[class=image280] {
    width: 260px !important;
    height: 260px !important;
  }
}

@media only screen and (max-width: 479px) {
  body {
    min-width: 100% !important;
  }
  table[class=eraseForMobile] {
    width: 0;
    display: none !important;
  }
  table[class=table600] {
    width: 280px !important;
  }
  table[class=table200] {
    width: 280px !important;
  }
  table[class=table200B] {
    width: 280px !important;
    margin: 20px 0 0 0 !important;
  }
  table[class=youreceive] {
    width: 280px !important;
    margin: 20px 0 0 0 !important;
  }
  table[class=contactJpg] {
    width: 280px !important;
  }
  td[class=companySloganTD] {
    font-size: 16px !important;
  }
  td[class=sectionsHeaderTD] {
    font-size: 20px !important;
  }
  td[class=subHeaderTD] {
    font-size: 16px !important;
  }
  td[class=sectionRegularInfoTextTD] {
    font-size: 14px !important;
  }
  td[class=footerInfoTDSupport] {
    text-align: center !important;
  }
  td[class=footerInfoTDSupport2] {
    text-align: center !important;
  }
  img[class=image600] {
    width: 280px !important;
    height: 135px !important;
  }
  img[class=image280] {
    width: 280px !important;
    height: 280px !important;
  }
}
</style>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>