<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php $data = $this->view_data; ?>
<div class="modal fade" id="modal-termeni-companie" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="<?php /*d-flex flex-column justify-content-center */ ?>" style="height:100%;">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header pt-2 pl-3 pb-2 pr-3">
          <h4 class="mb-0 mr-3">Termeni si conditii companii aeriene</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Inchide">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body p-0"><?php
          $has_uncommon = false;
          $common_fare_rules = array();
          $first_passenger = true;
          foreach($data['flight_details']->FareRules as $passenger_type => $rules){
            $first_airline = true;
            foreach($rules as $rule){
              if(!$first_passenger){
                $rule_categories = array();
              }
              foreach($rule->Category as $rule_category){
                if(!$first_passenger){
                  $rule_categories[$rule_category->Name] = true;
                }
                if(!isset($common_fare_rules[$rule_category->Name])){
                  $common_fare_rules[$rule_category->Name] = $rule_category->_;
                } elseif(!$first_passenger && isset($common_fare_rules[$rule_category->Name])) {
                  if($common_fare_rules[$rule_category->Name] !== $rule_category->_){
                    $has_uncommon = true;
                    unset($common_fare_rules[$rule_category->Name]);
                  }
                }
              }
              if(!$first_passenger){
                $common_fare_rules = array_intersect_key($common_fare_rules, $rule_categories);
              }
              $first_airline = false;
            }
            $first_passenger = false;
          }
          if($common_fare_rules){ ?>
            <ul class="list-group"><?php
              foreach($common_fare_rules as $category_name => $category_description){ ?>
              <li class="list-group-item d-block">
                <strong><?php echo $category_name; ?></strong>
                <p style="white-space:pre-wrap;"><?php echo $category_description; ?></p>
              </li><?php
              } ?>
            </ul><?php
          }
          if($has_uncommon){ 
            $fare_rules = array();
            foreach($data['flight_details']->FareRules as $passenger_type => $rules){
              foreach($rules as $rk => $rule){
                foreach($rule->Category as $rule_category){
                  if(!isset($common_fare_rules[$rule_category->Name])){
                    if(!isset($fare_rules[$passenger_type])){
                      $fare_rules[$passenger_type] = array();
                    }
                    if(!isset($fare_rules[$passenger_type][$rk])){
                      $new_rule = new stdClass;
                      $new_rule->Airline = $rule->Airline;
                      $new_rule->Origin = $rule->Origin;
                      $new_rule->Destination = $rule->Destination;
                      $new_rule->Category = array();
                      $fare_rules[$passenger_type][$rk] = $new_rule;
                    }
                    $fare_rules[$passenger_type][$rk]->Category[] = $rule_category;
                  }
                }
              }
            }
          ?>
          <div class="card">
            <div class="card-header pt-1 pr-3 pl-3">
              <ul class="nav nav-tabs card-header-tabs nav-justified"><?php 
                $first_passenger = null;
                foreach($fare_rules as $passenger_type => $rules){ ?>
                <li class="nav-item">
                  <a class="nav-link<?php echo isset($first_passenger) ? '' : ' active'; $first_passenger=true; ?>" data-toggle="tab" href="#fare_rules_<?php echo $passenger_type; ?>_tab" role="tab" aria-controls="fare_rules_<?php echo $passenger_type; ?>_tab">
                    <strong><i class="fa fa-user"></i> <?php echo $passenger_type; ?></strong>
                  </a>
                </li>
                <?php } ?>
              </ul>
            </div>
            <div class="tab-content card-block p-1">
              <?php 
              $first_passenger = null;
              foreach($fare_rules as $passenger_type => $rules){ ?>
              <div class="tab-pane<?php echo isset($first_passenger) ? '' : ' active'; $first_passenger=true; ?>" id="fare_rules_<?php echo $passenger_type; ?>_tab" role="tabpanel">
                <div class="card">
                  <div class="card-header pt-1 pr-3 pl-3">
                    <ul class="nav nav-tabs card-header-tabs nav-justified"><?php 
                      $first_airline = null;
                      foreach($rules as $rule){ ?>
                      <li class="nav-item">
                        <a class="nav-link<?php echo isset($first_airline) ? '' : ' active'; $first_airline = true; ?>" data-toggle="tab" href="#fare_rules_<?php echo $passenger_type; ?>_tab" role="tab" aria-controls="fare_rules_<?php echo $passenger_type; ?>_airline_<?php echo $rule->Airline->Code; ?>_<?php echo $rule->Origin->Code; ?>_<?php echo $rule->Destination->Code; ?>_tab">
                          <strong><i class="fa fa-plane"></i> <?php echo $rule->Airline->_; ?></strong><br />
                          <span><?php echo $rule->Origin->City; ?> - <?php echo $rule->Destination->City; ?></span>
                        </a>
                      </li><?php 
                      } ?>
                    </ul>
                  </div>
                  <div class="tab-content card-block p-1"><?php 
                    $first_airline = null;
                    foreach($rules as $rule){ ?>
                    <div class="tab-pane<?php echo isset($first_airline) ? '' : ' active'; $first_airline = true; ?>" id="fare_rules_<?php echo $passenger_type; ?>_airline_<?php echo $rule->Airline->Code; ?>_<?php echo $rule->Origin->Code; ?>_<?php echo $rule->Destination->Code; ?>_tab" role="tabpanel">
                      <ul class="list-group"><?php 
                        foreach($rule->Category as $rule_category){ ?>
                        <li class="list-group-item d-block">
                          <strong><?php echo $rule_category->Name; ?></strong>
                          <p><?php echo $rule_category->_; ?></p>
                        </li><?php 
                        } ?>
                      </ul>
                    </div><?php 
                    } ?>
                  </div>
                </div>
              </div><?php 
              } ?>
            </div>
          </div><?php
          } ?>
        </div>
      </div>
    </div>
  </div>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>