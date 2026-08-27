<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$this->_ci->load->model('Trip/Hotels_model');
$data = $this->_ci->Hotels_model->getSearchData();
$sort_by = $data['sort_by'];
$sort_order = $data['sort_order'];
?>
<div class="sortHotel mb-3" style="display:none;"> 
  <form action="#">
    <div class="row">
      <div class="col-12 col-sm-12 col-md-6"><p>SORTEAZA DUPA:</p></div>
      <div class="form-group col-12 col-sm-12 col-md-3">
        <i class="fa fa-sort-amount-asc"></i>
        <select name="MinPrice" class="form-control hotel-sort-by" id="sortPret" disabled>
          <option value="0" <?php echo $sort_by != 'MinPrice' ? 'selected' : ''; ?>>Tarif</option>
          <option value="1" <?php echo $sort_by == 'MinPrice' && !$sort_order ? 'selected' : ''; ?>>Mic &gt; Mare</option>
          <option value="2" <?php echo $sort_by == 'MinPrice' && $sort_order ? 'selected' : ''; ?>>Mare &gt; Mic</option>
        </select>       
      </div>
<?php /*
      <div class="form-group col-12 col-sm-12 col-md-3">
        <i class="fa fa-sort-alpha-asc float-left"></i>
        <select name="Name" class="form-control hotel-sort-by" id="sortNumeH" disabled>
          <option value="0" <?php echo $sort_by != 'Name' ? 'selected' : ''; ?>>Nume</option>
          <option value="1" <?php echo $sort_by == 'Name' && !$sort_order ? 'selected' : ''; ?>>Alfabetic A > Z</option>
          <option value="2" <?php echo $sort_by == 'Name' && $sort_order ? 'selected' : ''; ?>>Alfabetic Z > A</option>
        </select> 
      </div>
*/ ?>
      <div class="form-group col-12 col-sm-12 col-md-3">
        <i class="fa fa-star"></i>
        <select name="Stars" class="form-control hotel-sort-by" id="sortSteleH" disabled>
          <option value="0" <?php echo $sort_by != 'Stars' ? 'selected' : ''; ?>>Nr. Stele</option>
          <option value="1" <?php echo $sort_by == 'Stars' && !$sort_order ? 'selected' : ''; ?>>1 &gt; 5</option>
          <option value="2" <?php echo $sort_by == 'Stars' && $sort_order ? 'selected' : ''; ?>>5 &gt; 1</option>
        </select> 
      </div>
    </div>
  </form>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>