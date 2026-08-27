<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$this->_ci->load->model('Trip/Flights_model');
$data = $this->_ci->Flights_model->getSearchData();
$sort_by = isset($data['sort_by']) ? $data['sort_by'] : '';
$sort_order = isset($data['sort_order']) ? $data['sort_order'] : '';
?>
<div class="sortTicket mb-3">
  <form action="#">
    <div class="row">
      <div class="col-6 col-sm-12 col-md-3"><p><strong>SORTEAZA DUPA</strong>:</p></div>
      <div class="form-group col-6 col-sm-4 col-md-3">
        <i class="fa fa-sort-amount-asc"></i>
        <select name="sortPret" class="form-control" id="sortPret" title="Ordonare dupa pret">
          <option value="0">Pret</option>
          <option value="1">Mic &gt; Mare</option>
          <option value="2">Mare &gt; Mic</option>
        </select>
      </div>
      <div class="form-group col-6 col-sm-4 col-md-3">
        <i class="fa fa-sort-alpha-asc"></i>
        <select name="sortNumeH" class="form-control" id="sortNumeH" title="Ordonare dupa companie">
          <option value="0">Companie</option>
          <option value="1">Alfabetic A &gt; Z</option>
          <option value="2">Alfabetic Z &gt; A</option>
        </select>
      </div>
      <div class="form-group col-6 col-sm-4 col-md-3">
        <i class="fa fa-history"></i>
        <select name="sortSteleH" class="form-control" id="sortSteleH" title="Ordonare dupa durata zbor">
          <option value="0">Durata zbor</option>
          <option value="1">Scurta &gt; Lunga</option>
          <option value="2">Lunga &gt; Scurta</option>
        </select>
      </div>
      <?php /*
      <div class="form-group col-6 col-sm-4 col-md-3">
        <i class="fa fa-star"></i>
        <select name="sortSteleH" class="form-control" id="sortSteleH">
          <option value="0">Clasa de zbor</option>
          <option value="1">Economic</option>
          <option value="2">First</option>
          <option value="3">Business</option>
        </select>
      </div>
      */ ?>
    </div>
  </form>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>