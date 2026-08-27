<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php extract($this->view_data); ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/unsubscribe/meta.php'); ?>
<?php themeFunctions::addIncludePath('subheader_text', __DIR__ . '/unsubscribe/subheader_text.php'); ?>
<div style='text-align: left;'>
  <p>Ne pare rau ca ati optat sa va dezabonati de la newsletterul nostru. Nu va vom mai trimite informari cu privire la vacantele si ofertele noastre turistice, la stiri si noutati din lumea calatoriilor in cele mai frumoase si dorite destinatii.</p>
  <p>Daca v-ati dezabonat din greseala va puteti reabona oricand prin site-ul nostru <a href="https://accenttravel.ro/">https://accenttravel.ro/</a>.</p>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>