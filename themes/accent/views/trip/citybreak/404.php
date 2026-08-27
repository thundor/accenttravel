<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/404/meta.php'); ?>
<?php
$this->_ci->load->model('Trip/Citybreaks_model');
$citybreak_search_data = $this->_ci->Citybreaks_model->getSearchData();
$this->citybreak_search_data = &$citybreak_search_data;
?>
<div class="container">
  <?php include '404/breadcrumbs.php'; ?>
  <br />
  <br />
  <br />
  <section class="text-center">
    <div class="intro_title error">
      <h1 class="animated fadeInDown">404</h1>
      <p class="animated fadeInDown">Oferta de City Break nu a fost găsit</p>
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