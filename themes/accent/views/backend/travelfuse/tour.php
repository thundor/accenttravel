<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadLang('travelfuse_' . basename(__FILE__, '.php') . ''); ?>
<?php themeFunctions::includeAddon('select2'); ?>
<?php themeFunctions::includeAddon('twigjs'); ?>
<?php themeFunctions::includeAddon('inputmask'); ?>
<?php themeFunctions::includeAddon('datepicker'); ?>
<?php themeFunctions::includeAddon('pagination'); ?>
<?php themeFunctions::includeAddon('sweetalert'); ?>
<?php themeFunctions::includeAddon('editors'); ?>
<?php themeFunctions::includeAddon('forms-validation'); ?>
<?php themeFunctions::addIncludePath('includes/body/scripts.php', __DIR__ . '/' . basename(__FILE__, '.php') . '/scripts.php'); ?>
<?php themeFunctions::addIncludePath('includes/head/stylesheets.php', __DIR__ . '/' . basename(__FILE__, '.php') . '/stylesheets.php'); ?>
<?php themeFunctions::addIncludePath('layouts/backend/default/headbar/page_actions.php', __DIR__ . '/' . basename(__FILE__, '.php') . '/page_actions.php'); ?>
<?php themeFunctions::addIncludePath('modules/backend/headbar/page_title.php', __DIR__ . '/' . basename(__FILE__, '.php') . '/page_title.php'); ?>
<?php themeFunctions::addIncludePath('modules/head/meta.php', __DIR__ . '/' . basename(__FILE__, '.php') . '/meta.php'); ?>
<?php 
$label_size = array();
$label_size['xl'] = 3;
$label_size['md'] = 3;
$label_size['lg'] = 4;
$label_size['sm'] = 3;
$label_size[''] = 12;
$label_class = '';
$value_class = '';
foreach($label_size as $k=>$v){
  $label_class .= ' col-' . ($k ? $k . '-' : '') . $v;
  $value_class .= ' col-' . ($k ? $k . '-' : '') . ($v < 12 ? 12-$v : 12);
}
$tour = $this->view_data['tour'];
$editing = $tour->id != 0;
$can_write = $this->_method !='view';
?>
<section class="forms">
  <div class="col-12">
	<div id="result_toursForm"></div>
    <form id="toursForm" name="toursForm" action="<?php echo site_url('backend/travelfuse/travelfuse_tours/save'); ?>" method="POST" enctype="multipart/form-data">
      <?php if($this->_ci->config->item('csrf_protection') === TRUE){ ?>
      <input type="hidden" name="<?php echo htmlspecialchars($this->_ci->security->get_csrf_token_name()); ?>" value="<?php echo htmlspecialchars($this->_ci->security->get_csrf_hash()); ?>" />
      <?php } ?>
      <?php if($can_write){ ?>
      <input type="hidden" id="task" name="task" value="" />
      <?php if($editing){ ?>
      <input type="hidden" name="id" value="<?php echo $tour->id; ?>" />
      <?php } ?>
      <?php } ?>
      <div id="tour-details" class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-header d-flex align-items-center">
              <h2 class="h5 display"><?php echo lang('tour_info_section_title/html'); ?></h2>
            </div>
            <div class="card-block">
              <div class="form-group row">
                <label for="tour_country" class="<?php echo $label_class; ?> text-center"><?php echo lang('country_field_label/html'); ?></label>
                <div class="<?php echo $value_class; ?>">
                  <div class="form-control" readonly><?php echo htmlspecialchars($tour->country); ?>&nbsp;</div>
                </div>
              </div>
			  <div class="form-group row">
                <label class="<?php echo $label_class; ?> text-center"><?php echo lang('cities_field_label/html'); ?></label>
                <div class="<?php echo $value_class; ?>">
                  <div class="form-control" readonly>
					<?php if($tour->cities){ ?>
					<ul class="list-group">
					<?php foreach($tour->cities as $city){ ?>
					<li class="list-group-item">
						<a href="<?php echo site_url('backend/travelfuse/travelfuse_cities/edit'); ?>?id=<?php echo $city['Id']; ?>" target="_blank"><?php echo $city['namefinal']; ?> (<?php echo $city['type']; ?>)</a>
					</li>
					<?php } ?>
					</ul>
					<?php } ?>
				  &nbsp;</div>
                </div>
              </div>
			  <div class="form-group row">
                <label class="<?php echo $label_class; ?> text-center">Nume</label>
                <div class="<?php echo $value_class; ?>">
                  <div class="form-control" readonly><?php echo htmlspecialchars($tour->namefinal); ?>&nbsp;</div>
                </div>
              </div>
			  <div class="form-group row">
                <label for="tour_name-ro" class="<?php echo $label_class; ?> text-center">Nume RO</label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <input id="tour_name-ro" type="text" maxlength="255" name="_name_ro" placeholder="Nume" class="form-control" value="<?php echo htmlspecialchars($tour->_name_ro); ?>" />
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo htmlspecialchars($tour->_name_ro); ?>&nbsp;</div>
                  <?php } ?>
                </div>
              </div>
			  <div class="form-group row">
                <label for="tour_name-en" class="<?php echo $label_class; ?> text-center">Nume EN</label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <input id="tour_name-en" type="text" maxlength="255" name="_name_en" placeholder="Nume" class="form-control" value="<?php echo htmlspecialchars($tour->_name_en); ?>" />
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo htmlspecialchars($tour->_name_en); ?>&nbsp;</div>
                  <?php } ?>
                </div>
              </div>
			  <div class="form-group row">
                <label class="<?php echo $label_class; ?> text-center">ShortContent</label>
                <div class="<?php echo $value_class; ?>">
                  <div class="form-control text-pre" readonly><?php echo htmlspecialchars($tour->ShortContent); ?>&nbsp;</div>
                </div>
              </div>
			  <div class="form-group row">
                <label for="tour_short_content-ro" class="<?php echo $label_class; ?> text-center">ShortContent RO</label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
				  <textarea id="tour_short_content-ro" name="_short_content_ro" class="form-control"><?php echo htmlentities($tour->_short_content_ro); ?></textarea>
                  <?php } else { ?>
                  <div class="form-control text-pre" readonly><?php echo htmlentities($tour->_short_content_ro); ?>&nbsp;</div>
                  <?php } ?>
                </div>
              </div>
			  <div class="form-group row">
                <label for="tour_short_content-en" class="<?php echo $label_class; ?> text-center">ShortContent EN</label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
				  <textarea id="tour_short_content-en" name="_short_content_en" class="form-control"><?php echo htmlentities($tour->_short_content_en); ?></textarea>
                  <?php } else { ?>
                  <div class="form-control text-pre" readonly><?php echo htmlentities($tour->_short_content_en); ?>&nbsp;</div>
                  <?php } ?>
                </div>
              </div>
			  <div class="form-group row">
                <label for="tour_stars" class="<?php echo $label_class; ?> text-center">Stele</label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <input id="tour_stars" type="number" step="1" min="0" max="7" placeholder="" name="_stars" placeholder="Stele" class="form-control" value="<?php echo htmlspecialchars($tour->_stars); ?>" />
                  <?php } else { ?>
                  <?php } ?>
                  <div class="form-control" readonly>Travelfuse: <?php echo htmlspecialchars($tour->Stars); ?>&nbsp;</div>
                </div>
              </div>
			  <div class="form-group row">
                <label for="tour_web_address" class="<?php echo $label_class; ?> text-center">Web Address</label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <input id="tour_web_address" type="text" maxlength="255" placeholder="<?php echo htmlspecialchars($tour->WebAddress); ?>" name="_web_address" placeholder="Web Address" class="form-control" value="<?php echo htmlspecialchars($tour->_web_address); ?>" />
                  <?php } else { ?>
                  <?php } ?>
                  <div class="form-control" readonly>Travelfuse: <?php echo htmlspecialchars($tour->WebAddress); ?>&nbsp;</div>
                </div>
              </div>
			  <div class="form-group row">
                <label for="tour_latitude" class="<?php echo $label_class; ?> text-center">Latitude</label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <input id="tour_latitude" type="text" maxlength="255" placeholder="<?php echo htmlspecialchars($tour->Latitude); ?>" name="_latitude" placeholder="Latitude" class="form-control" value="<?php echo htmlspecialchars($tour->_latitude); ?>" />
                  <?php } else { ?>
                  <?php } ?>
                  <div class="form-control" readonly>Travelfuse: <?php echo htmlspecialchars($tour->Latitude); ?>&nbsp;</div>
                </div>
              </div>
			  <div class="form-group row">
                <label for="tour_longitude" class="<?php echo $label_class; ?> text-center">Longitude</label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <input id="tour_longitude" type="text" maxlength="255" placeholder="<?php echo htmlspecialchars($tour->Longitude); ?>" name="_longitude" placeholder="Longitude" class="form-control" value="<?php echo htmlspecialchars($tour->_longitude); ?>" />
                  <?php } else { ?>
                  <?php } ?>
                  <div class="form-control" readonly>Travelfuse: <?php echo htmlspecialchars($tour->Longitude); ?>&nbsp;</div>
                </div>
              </div>
              <div class="form-group row">
                <label for="tour_status" class="<?php echo $label_class; ?> text-center"><?php echo lang('status_field_label/html'); ?></label>
                <div class="<?php echo $value_class; ?>">
                  <?php if($can_write){ ?>
                  <div class="i-checks">
                    <input id="tour_status_active" type="radio" value="1" name="status" <?php echo $tour->status ==1 ? 'checked' : ''; ?> class="form-control-custom radio-custom">
                    <label for="tour_status_active"><?php echo lang('option_active'); ?></label>
                  </div>
                  <div class="i-checks">
                    <input id="tour_status_inactive" type="radio" value="0" name="status" <?php echo !$tour->status ? 'checked' : ''; ?> class="form-control-custom radio-custom">
                    <label for="tour_status_inactive"><?php echo lang('option_inactive'); ?></label>
                  </div>
                  <?php } else { ?>
                  <div class="form-control" readonly><?php echo $tour->status == 1 ? lang('option_active') : lang('option_inactive'); ?>&nbsp;</div>
                  <?php } ?>
                </div>
              </div>
			  <div class="form-group row">
                <label class="col-12 text-center">Content</label>
                <div class="col-12">
					<textarea id="tour_content" disabled class="form-control make-htmleditor"><?php echo htmlentities($tour->Content); ?></textarea>
                </div>
              </div>
			  <div class="form-group row">
                <label for="tour_content-ro" class="col-12 text-center">Content RO</label>
                <div class="col-12">
                  <?php if($can_write){ ?>
				  <textarea id="tour_content-ro" name="_content_ro" class="form-control make-htmleditor"><?php echo htmlentities($tour->_content_ro); ?></textarea>
                  <?php } else { ?>
                  <div class="form-control text-pre" readonly><?php echo htmlentities($tour->_content_ro); ?>&nbsp;</div>
                  <?php } ?>
                </div>
              </div>
			  <div class="form-group row">
                <label for="tour_content-en" class="col-12 text-center">Content EN</label>
                <div class="col-12">
                  <?php if($can_write){ ?>
				  <textarea id="tour_content-en" name="_content_en" class="form-control make-htmleditor"><?php echo htmlentities($tour->_content_en); ?></textarea>
                  <?php } else { ?>
                  <div class="form-control text-pre" readonly><?php echo htmlentities($tour->_content_en); ?>&nbsp;</div>
                  <?php } ?>
                </div>
              </div>
			  <div class="form-group row">
                <label for="tour_images" class="col-12 text-center">Imagini</label>
                <div class="col-12">
					<button type="button" class="btn btn-success add_image" data-pos="start">Adauga imagine</button>
				  <table class="table table-bordered table-hover crt">
					<thead>
						<tr>
							<th>#</th>
							<th>Image</th>
							<th>Type</th>
							<th>Link</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody id="tour-images-tbody">
						<?php 
						$image_counter = -1;
						foreach($tour->images as $image => $image_detail){
						$image_counter++;
						?>
						<tr>
							<td>
								<span class="crt"></span>
								<input type="hidden" name="_images[<?php echo $image_counter; ?>][name]" value="<?php echo htmlspecialchars($image, ENT_QUOTES); ?>">
							<?php if(!empty($image_detail['custom']) ){ ?>
								<input type="hidden" name="_images[<?php echo $image_counter; ?>][custom]" value="1">
							<?php } ?>
							</td>
							<td><a href="<?php echo htmlspecialchars($image, ENT_QUOTES); ?>" target="_blank"><img src="<?php echo htmlspecialchars($image, ENT_QUOTES); ?>" loading="lazy" class="tour-image" /></a></td>
							<td><?php echo !empty($image_detail['custom']) ? 'Custom' : 'Travelfuse'; ?>
							<?php if(!empty($image_detail['missing'])){ ?>
							<i class="fa fa-warning" title="Missing"></i>
							<?php } ?>
							</td>
							<td><?php echo htmlspecialchars($image); ?></td>
							<td>
								<label class="i-checks on-off-show">
								  <input type="checkbox" value="1" name="_images[<?php echo $image_counter; ?>][hide]" <?php echo !empty($image_detail['hide']) ? 'checked=""' : ''; ?> class="form-control-custom radio-custom">
								  <i class="fa fa-eye is-off text-success" title="Se afiseaza"></i>
								  <i class="fa fa-eye-slash is-on" title="Ascuns"></i>
								</label>
								<?php if(!empty($image_detail['missing']) || !empty($image_detail['custom']) ){ ?>
								<label class="i-checks on-off-show">
								  <input type="checkbox" value="1" <?php echo !empty($image_detail['hide']) ? 'checked=""' : ''; ?> class="form-control-custom radio-custom" onchange="$('input[name]', $(this).closest('tr').toggleClass('todelete', $(this).is(':checked'))).prop('disabled', $(this).is(':checked'))">
								  <i class="fa fa-check is-on text-success" title="Anuleaza stergerea"></i>
								  <i class="fa fa-trash is-off" title="Marcheaza pentru stergere"></i>
								</label>
								<?php } ?>
							</td>
						</tr>
						<?php } ?>
					</tbody>
				  </table>
				  <button type="button" class="btn btn-success add_image" data-pos="end">Adauga imagine</button>
                  <?php if($can_write){ ?>
                  <?php } else { ?>
                  
                  <?php } ?>
                </div>
              </div>
			  <div class="form-group row">
                <label for="tour_facilities" class="col-12 text-center">Facilitati</label>
				<div id="facilitati_modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
				<div class="d-flex flex-column justify-content-center" style="height:100%;pointer-events:none;">
                    <div class="modal-dialog modal-xl" role="document" style="max-height: 90%;pointer-events:auto;">
                      <div class="modal-content">
                        <div class="modal-body">
                          <button type="button" class="close custom-close" data-dismiss="modal" aria-label="Inchide">
                            <span aria-hidden="true">&times;</span>
                          </button>
						  <div class="tour-facilities-list">
					<?php
					$this->_ci->load->model('Travelfuse/TravelFuseFacilities_model');
					$cached_facilities = $this->_ci->TravelFuseFacilities_model->getCachedFacilities();
					foreach($cached_facilities as $name => $other_name){ 
						if(!isset($other_name)) continue;
						if(isset($tour->facilities[$name])) continue; ?>
						<label class="input-group"><span class="input-group-addon"><input type="checkbox" class="tour-facility-checkbox" value="<?php echo htmlspecialchars($name, ENT_QUOTES); ?>" data-other-name="<?php echo htmlspecialchars($other_name, ENT_QUOTES); ?>"></span><span class="form-control"><?php echo $other_name; ?></span></label>
					<?php
					}
					?>
					</div>
						</div>
                      </div>
                    </div>
				</div>
				</div>
                <div class="col-12">
					<button type="button" class="btn btn-success add_facility" data-toggle="modal" data-target="#facilitati_modal">Adauga facilitati</button>
				  <table class="table table-bordered table-hover crt">
					<thead>
						<tr>
							<th>#</th>
							<th>Tip</th>
							<th>Nume</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody id="tour-facilities-tbody">
						<?php 
						$facilities_counter = -1;
						foreach($tour->facilities as $facility => $facility_detail){ 
						$facilities_counter++;
						?>
						<tr>
							<td>
								<span class="crt"></span>
								<input type="hidden" name="_facilities[<?php echo $facilities_counter; ?>][name]" value="<?php echo htmlspecialchars($facility, ENT_QUOTES); ?>">
							<?php if(!empty($facility_detail['custom']) ){ ?>
								<input type="hidden" name="_facilities[<?php echo $facilities_counter; ?>][custom]" value="1">
							<?php } ?>
							</td>
							<td><?php echo !empty($facility_detail['custom']) ? 'Custom' : ($facility_detail['type']); ?>
							<?php if(!empty($facility_detail['missing'])){ ?>
							<i class="fa fa-warning" title="Missing"></i>
							<?php } ?>
							</td>
							<td><?php echo htmlspecialchars(isset($cached_facilities[$facility]) ? $cached_facilities[$facility] : $facility); ?></td>
							<td>
								<label class="i-checks on-off-show">
								  <input type="checkbox" value="1" name="_facilities[<?php echo $facilities_counter; ?>][hide]" <?php echo !empty($facility_detail['hide']) ? 'checked=""' : ''; ?> class="form-control-custom radio-custom">
								  <i class="fa fa-eye is-off text-success" title="Se afiseaza"></i>
								  <i class="fa fa-eye-slash is-on" title="Ascuns"></i>
								</label>
								<?php if(!empty($facility_detail['missing']) || !empty($facility_detail['custom']) ){ ?>
								<label class="i-checks on-off-show">
								  <input type="checkbox" value="1" <?php echo !empty($facility_detail['hide']) ? 'checked=""' : ''; ?> class="form-control-custom radio-custom" onchange="$('input[name]', $(this).closest('tr').toggleClass('todelete', $(this).is(':checked'))).prop('disabled', $(this).is(':checked'))">
								  <i class="fa fa-check is-on text-success" title="Anuleaza stergerea"></i>
								  <i class="fa fa-trash is-off" title="Marcheaza pentru stergere"></i>
								</label>
								<?php } ?>
							</td>
						</tr>
						<?php } ?>
					</tbody>
				  </table>
                  <?php if($can_write){ ?>
                  <?php } else { ?>
                  
                  <?php } ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</section>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>