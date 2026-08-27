<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/404/meta.php'); ?>
<div class="container">
  <?php include '404/breadcrumbs.php'; ?>
  <br />
  <br />
  <br />
  <section class="text-center">
    <div class="intro_title error">
      <h1 class="animated fadeInDown">404</h1>
      <p class="animated fadeInDown">Vacanța nu a fost găsită</p>
      <a href="<?php echo base_url(); ?>" class="animated fadeInUp btn iosbtn">Înapoi la prima pagină</a>
    </div>
  </section>
  <br />
  <br />
  <br />
  <br />
  <br />
</div>
<?php themeFunctions::debugFileLine('end'); ?>