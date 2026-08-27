<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<input type="hidden" id="flightsFilterForDateDeparture" />
<input type="hidden" id="flightsFilterForDateReturn" />
<div id="calendarFlights"  class="hiddenFiltT">
  <?php /* <table cellpadding="0" cellspacing="0" class="table3Days">
    <tbody>
      <tr>
        <th class="showDir">
          Sosire <i class="fa fa-hand-o-right"></i><br />
          <i class="fa fa-hand-o-down"></i> Plecare
        </th>
        <th>Mie, 12 Mai</th>
        <th>Joi, 13 Mai</th>
        <th>Vin, 14 Mai</th>
        <th>Sam, 15 Mai</th>
        <th>Dum, 16 Mai</th>
        <th>Luni, 17 Mai</th>
        <th>Mar, 18 Mai</th>
      </tr>
      <tr>
        <th>Luni, 1 Mai</th>
        <td>
          <a name="bestPrice">185 &euro;</a>
          <div class="toolTipPrice">
            <div class="topTitle">
              <p>Tarif total zbor dus-intors</p>
            </div>
            <div class="priceTool">
              <span class="price">399 &euro;</span>
            </div>
            <div class="firstLine">
              <span> <img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>BUCURESTI, BUH</strong><br />
                <strong><i class="fa fa-plane"></i> Plecare</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 13:00</p>
            </div>
            <div class="secondLine">
              <span><img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>LONDON, HEATR</strong><br />
                <strong><i class="fa fa-plane rotate90"></i> Sosire</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 17:50</p>
            </div>
          </div>
        </td>
        <td>
          <a name="bestPrice">263 &euro;</a>
          <div class="toolTipPrice">
            <div class="topTitle">
              <p>Tarif total zbor dus-intors</p>
            </div>
            <div class="priceTool">
              <span class="price">399 &euro;</span>
            </div>
            <div class="firstLine">
              <span> <img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>BUCURESTI, BUH</strong><br />
                <strong><i class="fa fa-plane"></i> Plecare</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 13:00</p>
            </div>
            <div class="secondLine">
              <span><img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>LONDON, HEATR</strong><br />
                <strong><i class="fa fa-plane rotate90"></i> Sosire</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 17:50</p>
            </div>
          </div>
        </td>
        <td>
          <a name="bestPrice">257 &euro;</a>
          <div class="toolTipPrice">
            <div class="topTitle">
              <p>Tarif total zbor dus-intors</p>
            </div>
            <div class="priceTool">
              <span class="price">399 &euro;</span>
            </div>
            <div class="firstLine">
              <span> <img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>BUCURESTI, BUH</strong><br />
                <strong><i class="fa fa-plane"></i> Plecare</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 13:00</p>
            </div>
            <div class="secondLine">
              <span><img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>LONDON, HEATR</strong><br />
                <strong><i class="fa fa-plane rotate90"></i> Sosire</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 17:50</p>
            </div>
          </div>
        </td>
        <td>
          <a name="bestPrice">301 &euro;</a>
          <div class="toolTipPrice">
            <div class="topTitle">
              <p>Tarif total zbor dus-intors</p>
            </div>
            <div class="priceTool">
              <span class="price">399 &euro;</span>
            </div>
            <div class="firstLine">
              <span> <img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>BUCURESTI, BUH</strong><br />
                <strong><i class="fa fa-plane"></i> Plecare</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 13:00</p>
            </div>
            <div class="secondLine">
              <span><img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>LONDON, HEATR</strong><br />
                <strong><i class="fa fa-plane rotate90"></i> Sosire</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 17:50</p>
            </div>
          </div>
        </td>
        <td>
          <a name="bestPrice">311 &euro;</a>
          <div class="toolTipPrice">
            <div class="topTitle">
              <p>Tarif total zbor dus-intors</p>
            </div>
            <div class="priceTool">
              <span class="price">399 &euro;</span>
            </div>
            <div class="firstLine">
              <span> <img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>BUCURESTI, BUH</strong><br />
                <strong><i class="fa fa-plane"></i> Plecare</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 13:00</p>
            </div>
            <div class="secondLine">
              <span><img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>LONDON, HEATR</strong><br />
                <strong><i class="fa fa-plane rotate90"></i> Sosire</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 17:50</p>
            </div>
          </div>
        </td>
        <td>
          <a name="bestPrice">246 &euro;</a>
          <div class="toolTipPrice">
            <div class="topTitle">
              <p>Tarif total zbor dus-intors</p>
            </div>
            <div class="priceTool">
              <span class="price">399 &euro;</span>
            </div>
            <div class="firstLine">
              <span> <img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>BUCURESTI, BUH</strong><br />
                <strong><i class="fa fa-plane"></i> Plecare</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 13:00</p>
            </div>
            <div class="secondLine">
              <span><img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>LONDON, HEATR</strong><br />
                <strong><i class="fa fa-plane rotate90"></i> Sosire</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 17:50</p>
            </div>
          </div>
        </td>
        <td>
          <a name="bestPrice">237 &euro;</a>
          <div class="toolTipPrice">
            <div class="topTitle">
              <p>Tarif total zbor dus-intors</p>
            </div>
            <div class="priceTool">
              <span class="price">399 &euro;</span>
            </div>
            <div class="firstLine">
              <span> <img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>BUCURESTI, BUH</strong><br />
                <strong><i class="fa fa-plane"></i> Plecare</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 13:00</p>
            </div>
            <div class="secondLine">
              <span><img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>LONDON, HEATR</strong><br />
                <strong><i class="fa fa-plane rotate90"></i> Sosire</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 17:50</p>
            </div>
          </div>
        </td>
      </tr>
      <tr>
        <th>Mar, 2 Mai</th>
        <td>
          <a name="bestPrice">253 &euro;</a>
          <div class="toolTipPrice">
            <div class="topTitle">
              <p>Tarif total zbor dus-intors</p>
            </div>
            <div class="priceTool">
              <span class="price">399 &euro;</span>
            </div>
            <div class="firstLine">
              <span> <img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>BUCURESTI, BUH</strong><br />
                <strong><i class="fa fa-plane"></i> Plecare</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 13:00</p>
            </div>
            <div class="secondLine">
              <span><img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>LONDON, HEATR</strong><br />
                <strong><i class="fa fa-plane rotate90"></i> Sosire</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 17:50</p>
            </div>
          </div>
        </td>
        <td>
          <a name="bestPrice">169 &euro;</a>
          <div class="toolTipPrice">
            <div class="topTitle">
              <p>Tarif total zbor dus-intors</p>
            </div>
            <div class="priceTool">
              <span class="price">399 &euro;</span>
            </div>
            <div class="firstLine">
              <span> <img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>BUCURESTI, BUH</strong><br />
                <strong><i class="fa fa-plane"></i> Plecare</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 13:00</p>
            </div>
            <div class="secondLine">
              <span><img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>LONDON, HEATR</strong><br />
                <strong><i class="fa fa-plane rotate90"></i> Sosire</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 17:50</p>
            </div>
          </div>
        </td>
        <td>
          <a name="bestPrice">340 &euro;</a>
          <div class="toolTipPrice">
            <div class="topTitle">
              <p>Tarif total zbor dus-intors</p>
            </div>
            <div class="priceTool">
              <span class="price">399 &euro;</span>
            </div>
            <div class="firstLine">
              <span> <img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>BUCURESTI, BUH</strong><br />
                <strong><i class="fa fa-plane"></i> Plecare</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 13:00</p>
            </div>
            <div class="secondLine">
              <span><img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>LONDON, HEATR</strong><br />
                <strong><i class="fa fa-plane rotate90"></i> Sosire</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 17:50</p>
            </div>
          </div>
        </td>
        <td>
          <a name="bestPrice">174 &euro;</a>
          <div class="toolTipPrice">
            <div class="topTitle">
              <p>Tarif total zbor dus-intors</p>
            </div>
            <div class="priceTool">
              <span class="price">399 &euro;</span>
            </div>
            <div class="firstLine">
              <span> <img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>BUCURESTI, BUH</strong><br />
                <strong><i class="fa fa-plane"></i> Plecare</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 13:00</p>
            </div>
            <div class="secondLine">
              <span><img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>LONDON, HEATR</strong><br />
                <strong><i class="fa fa-plane rotate90"></i> Sosire</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 17:50</p>
            </div>
          </div>
        </td>
        <td>
          <a name="bestPrice">182 &euro;</a>
          <div class="toolTipPrice">
            <div class="topTitle">
              <p>Tarif total zbor dus-intors</p>
            </div>
            <div class="priceTool">
              <span class="price">399 &euro;</span>
            </div>
            <div class="firstLine">
              <span> <img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>BUCURESTI, BUH</strong><br />
                <strong><i class="fa fa-plane"></i> Plecare</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 13:00</p>
            </div>
            <div class="secondLine">
              <span><img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>LONDON, HEATR</strong><br />
                <strong><i class="fa fa-plane rotate90"></i> Sosire</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 17:50</p>
            </div>
          </div>
        </td>
        <td>
          <a name="bestPrice">196 &euro;</a>
          <div class="toolTipPrice">
            <div class="topTitle">
              <p>Tarif total zbor dus-intors</p>
            </div>
            <div class="priceTool">
              <span class="price">399 &euro;</span>
            </div>
            <div class="firstLine">
              <span> <img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>BUCURESTI, BUH</strong><br />
                <strong><i class="fa fa-plane"></i> Plecare</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 13:00</p>
            </div>
            <div class="secondLine">
              <span><img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>LONDON, HEATR</strong><br />
                <strong><i class="fa fa-plane rotate90"></i> Sosire</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 17:50</p>
            </div>
          </div>
        </td>
        <td>
          <a name="bestPrice">184 &euro;</a>
          <div class="toolTipPrice">
            <div class="topTitle">
              <p>Tarif total zbor dus-intors</p>
            </div>
            <div class="priceTool">
              <span class="price">399 &euro;</span>
            </div>
            <div class="firstLine">
              <span> <img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>BUCURESTI, BUH</strong><br />
                <strong><i class="fa fa-plane"></i> Plecare</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 13:00</p>
            </div>
            <div class="secondLine">
              <span><img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>LONDON, HEATR</strong><br />
                <strong><i class="fa fa-plane rotate90"></i> Sosire</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 17:50</p>
            </div>
          </div>
        </td>
      </tr>
      <tr>
        <th>Mie, 3 Mai</th>
        <td>
          <a name="bestPrice">340 &euro;</a>
          <div class="toolTipPrice">
            <div class="topTitle">
              <p>Tarif total zbor dus-intors</p>
            </div>
            <div class="priceTool">
              <span class="price">399 &euro;</span>
            </div>
            <div class="firstLine">
              <span> <img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>BUCURESTI, BUH</strong><br />
                <strong><i class="fa fa-plane"></i> Plecare</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 13:00</p>
            </div>
            <div class="secondLine">
              <span><img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>LONDON, HEATR</strong><br />
                <strong><i class="fa fa-plane rotate90"></i> Sosire</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 17:50</p>
            </div>
          </div>
        </td>
        <td>
          <a name="bestPrice">478 &euro;</a>
          <div class="toolTipPrice">
            <div class="topTitle">
              <p>Tarif total zbor dus-intors</p>
            </div>
            <div class="priceTool">
              <span class="price">399 &euro;</span>
            </div>
            <div class="firstLine">
              <span> <img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>BUCURESTI, BUH</strong><br />
                <strong><i class="fa fa-plane"></i> Plecare</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 13:00</p>
            </div>
            <div class="secondLine">
              <span><img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>LONDON, HEATR</strong><br />
                <strong><i class="fa fa-plane rotate90"></i> Sosire</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 17:50</p>
            </div>
          </div>
        </td>
        <td>
          <a name="bestPrice">324 &euro;</a>
          <div class="toolTipPrice">
            <div class="topTitle">
              <p>Tarif total zbor dus-intors</p>
            </div>
            <div class="priceTool">
              <span class="price">399 &euro;</span>
            </div>
            <div class="firstLine">
              <span> <img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>BUCURESTI, BUH</strong><br />
                <strong><i class="fa fa-plane"></i> Plecare</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 13:00</p>
            </div>
            <div class="secondLine">
              <span><img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>LONDON, HEATR</strong><br />
                <strong><i class="fa fa-plane rotate90"></i> Sosire</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 17:50</p>
            </div>
          </div>
        </td>
        <td>
          <a name="bestPrice">174 &euro;</a>
          <div class="toolTipPrice">
            <div class="topTitle">
              <p>Tarif total zbor dus-intors</p>
            </div>
            <div class="priceTool">
              <span class="price">399 &euro;</span>
            </div>
            <div class="firstLine">
              <span> <img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>BUCURESTI, BUH</strong><br />
                <strong><i class="fa fa-plane"></i> Plecare</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 13:00</p>
            </div>
            <div class="secondLine">
              <span><img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>LONDON, HEATR</strong><br />
                <strong><i class="fa fa-plane rotate90"></i> Sosire</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 17:50</p>
            </div>
          </div>
        </td>
        <td>
          <a name="bestPrice">198 &euro;</a>
          <div class="toolTipPrice">
            <div class="topTitle">
              <p>Tarif total zbor dus-intors</p>
            </div>
            <div class="priceTool">
              <span class="price">399 &euro;</span>
            </div>
            <div class="firstLine">
              <span> <img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>BUCURESTI, BUH</strong><br />
                <strong><i class="fa fa-plane"></i> Plecare</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 13:00</p>
            </div>
            <div class="secondLine">
              <span><img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>LONDON, HEATR</strong><br />
                <strong><i class="fa fa-plane rotate90"></i> Sosire</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 17:50</p>
            </div>
          </div>
        </td>
        <td>
          <a name="bestPrice">254 &euro;</a>
          <div class="toolTipPrice">
            <div class="topTitle">
              <p>Tarif total zbor dus-intors</p>
            </div>
            <div class="priceTool">
              <span class="price">399 &euro;</span>
            </div>
            <div class="firstLine">
              <span> <img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>BUCURESTI, BUH</strong><br />
                <strong><i class="fa fa-plane"></i> Plecare</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 13:00</p>
            </div>
            <div class="secondLine">
              <span><img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>LONDON, HEATR</strong><br />
                <strong><i class="fa fa-plane rotate90"></i> Sosire</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 17:50</p>
            </div>
          </div>
        </td>
        <td>
          <a name="bestPrice">340 &euro;</a>
          <div class="toolTipPrice">
            <div class="topTitle">
              <p>Tarif total zbor dus-intors</p>
            </div>
            <div class="priceTool">
              <span class="price">399 &euro;</span>
            </div>
            <div class="firstLine">
              <span> <img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>BUCURESTI, BUH</strong><br />
                <strong><i class="fa fa-plane"></i> Plecare</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 13:00</p>
            </div>
            <div class="secondLine">
              <span><img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>LONDON, HEATR</strong><br />
                <strong><i class="fa fa-plane rotate90"></i> Sosire</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 17:50</p>
            </div>
          </div>
        </td>
      </tr>
      <tr>
        <th>Joi, 4 Mai</th>
        <td class="lowestPrice" title="Cel mai mic pret!">
          <a name="bestPrice">156 &euro;</a>
          <div class="toolTipPrice">
            <div class="topTitle">
              <p>Tarif total zbor dus-intors</p>
            </div>
            <div class="priceTool">
              <span class="price">399 &euro;</span>
            </div>
            <div class="firstLine">
              <span> <img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>BUCURESTI, BUH</strong><br />
                <strong><i class="fa fa-plane"></i> Plecare</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 13:00</p>
            </div>
            <div class="secondLine">
              <span><img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>LONDON, HEATR</strong><br />
                <strong><i class="fa fa-plane rotate90"></i> Sosire</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 17:50</p>
            </div>
          </div>
        </td>
        <td>
          <a name="bestPrice">197 &euro;</a>
          <div class="toolTipPrice">
            <div class="topTitle">
              <p>Tarif total zbor dus-intors</p>
            </div>
            <div class="priceTool">
              <span class="price">399 &euro;</span>
            </div>
            <div class="firstLine">
              <span> <img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>BUCURESTI, BUH</strong><br />
                <strong><i class="fa fa-plane"></i> Plecare</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 13:00</p>
            </div>
            <div class="secondLine">
              <span><img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>LONDON, HEATR</strong><br />
                <strong><i class="fa fa-plane rotate90"></i> Sosire</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 17:50</p>
            </div>
          </div>
        </td>
        <td>
          <a name="bestPrice">265 &euro;</a>
          <div class="toolTipPrice">
            <div class="topTitle">
              <p>Tarif total zbor dus-intors</p>
            </div>
            <div class="priceTool">
              <span class="price">399 &euro;</span>
            </div>
            <div class="firstLine">
              <span> <img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>BUCURESTI, BUH</strong><br />
                <strong><i class="fa fa-plane"></i> Plecare</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 13:00</p>
            </div>
            <div class="secondLine">
              <span><img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>LONDON, HEATR</strong><br />
                <strong><i class="fa fa-plane rotate90"></i> Sosire</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 17:50</p>
            </div>
          </div>
        </td>
        <td>
          <a name="bestPrice">340 &euro;</a>
          <div class="toolTipPrice">
            <div class="topTitle">
              <p>Tarif total zbor dus-intors</p>
            </div>
            <div class="priceTool">
              <span class="price">399 &euro;</span>
            </div>
            <div class="firstLine">
              <span> <img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>BUCURESTI, BUH</strong><br />
                <strong><i class="fa fa-plane"></i> Plecare</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 13:00</p>
            </div>
            <div class="secondLine">
              <span><img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>LONDON, HEATR</strong><br />
                <strong><i class="fa fa-plane rotate90"></i> Sosire</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 17:50</p>
            </div>
          </div>
        </td>
        <td>
          <a name="bestPrice">298 &euro;</a>
          <div class="toolTipPrice">
            <div class="topTitle">
              <p>Tarif total zbor dus-intors</p>
            </div>
            <div class="priceTool">
              <span class="price">399 &euro;</span>
            </div>
            <div class="firstLine">
              <span> <img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>BUCURESTI, BUH</strong><br />
                <strong><i class="fa fa-plane"></i> Plecare</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 13:00</p>
            </div>
            <div class="secondLine">
              <span><img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>LONDON, HEATR</strong><br />
                <strong><i class="fa fa-plane rotate90"></i> Sosire</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 17:50</p>
            </div>
          </div>
        </td>
        <td>
          <a name="bestPrice">312 &euro;</a>
          <div class="toolTipPrice">
            <div class="topTitle">
              <p>Tarif total zbor dus-intors</p>
            </div>
            <div class="priceTool">
              <span class="price">399 &euro;</span>
            </div>
            <div class="firstLine">
              <span> <img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>BUCURESTI, BUH</strong><br />
                <strong><i class="fa fa-plane"></i> Plecare</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 13:00</p>
            </div>
            <div class="secondLine">
              <span><img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>LONDON, HEATR</strong><br />
                <strong><i class="fa fa-plane rotate90"></i> Sosire</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 17:50</p>
            </div>
          </div>
        </td>
        <td>
          <a name="bestPrice">321 &euro;</a>
          <div class="toolTipPrice">
            <div class="topTitle">
              <p>Tarif total zbor dus-intors</p>
            </div>
            <div class="priceTool">
              <span class="price">399 &euro;</span>
            </div>
            <div class="firstLine">
              <span> <img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>BUCURESTI, BUH</strong><br />
                <strong><i class="fa fa-plane"></i> Plecare</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 13:00</p>
            </div>
            <div class="secondLine">
              <span><img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>LONDON, HEATR</strong><br />
                <strong><i class="fa fa-plane rotate90"></i> Sosire</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 17:50</p>
            </div>
          </div>
        </td>
      </tr>
      <tr>
        <th>Vin, 5 Mai</th>
        <td>
          <a name="bestPrice">340 &euro;</a>
          <div class="toolTipPrice">
            <div class="topTitle">
              <p>Tarif total zbor dus-intors</p>
            </div>
            <div class="priceTool">
              <span class="price">399 &euro;</span>
            </div>
            <div class="firstLine">
              <span> <img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>BUCURESTI, BUH</strong><br />
                <strong><i class="fa fa-plane"></i> Plecare</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 13:00</p>
            </div>
            <div class="secondLine">
              <span><img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>LONDON, HEATR</strong><br />
                <strong><i class="fa fa-plane rotate90"></i> Sosire</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 17:50</p>
            </div>
          </div>
        </td>
        <td>
          <a name="bestPrice">265 &euro;</a>
          <div class="toolTipPrice">
            <div class="topTitle">
              <p>Tarif total zbor dus-intors</p>
            </div>
            <div class="priceTool">
              <span class="price">399 &euro;</span>
            </div>
            <div class="firstLine">
              <span> <img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>BUCURESTI, BUH</strong><br />
                <strong><i class="fa fa-plane"></i> Plecare</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 13:00</p>
            </div>
            <div class="secondLine">
              <span><img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>LONDON, HEATR</strong><br />
                <strong><i class="fa fa-plane rotate90"></i> Sosire</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 17:50</p>
            </div>
          </div>
        </td>
        <td>
          <a name="bestPrice">360 &euro;</a>
          <div class="toolTipPrice">
            <div class="topTitle">
              <p>Tarif total zbor dus-intors</p>
            </div>
            <div class="priceTool">
              <span class="price">399 &euro;</span>
            </div>
            <div class="firstLine">
              <span> <img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>BUCURESTI, BUH</strong><br />
                <strong><i class="fa fa-plane"></i> Plecare</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 13:00</p>
            </div>
            <div class="secondLine">
              <span><img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>LONDON, HEATR</strong><br />
                <strong><i class="fa fa-plane rotate90"></i> Sosire</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 17:50</p>
            </div>
          </div>
        </td>
        <td>
          <a name="bestPrice">382 &euro;</a>
          <div class="toolTipPrice">
            <div class="topTitle">
              <p>Tarif total zbor dus-intors</p>
            </div>
            <div class="priceTool">
              <span class="price">399 &euro;</span>
            </div>
            <div class="firstLine">
              <span> <img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>BUCURESTI, BUH</strong><br />
                <strong><i class="fa fa-plane"></i> Plecare</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 13:00</p>
            </div>
            <div class="secondLine">
              <span><img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>LONDON, HEATR</strong><br />
                <strong><i class="fa fa-plane rotate90"></i> Sosire</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 17:50</p>
            </div>
          </div>
        </td>
        <td>
          <a name="bestPrice">340 &euro;</a>
          <div class="toolTipPrice">
            <div class="topTitle">
              <p>Tarif total zbor dus-intors</p>
            </div>
            <div class="priceTool">
              <span class="price">399 &euro;</span>
            </div>
            <div class="firstLine">
              <span> <img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>BUCURESTI, BUH</strong><br />
                <strong><i class="fa fa-plane"></i> Plecare</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 13:00</p>
            </div>
            <div class="secondLine">
              <span><img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>LONDON, HEATR</strong><br />
                <strong><i class="fa fa-plane rotate90"></i> Sosire</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 17:50</p>
            </div>
          </div>
        </td>
        <td>
          <a name="bestPrice">361 &euro;</a>
          <div class="toolTipPrice">
            <div class="topTitle">
              <p>Tarif total zbor dus-intors</p>
            </div>
            <div class="priceTool">
              <span class="price">399 &euro;</span>
            </div>
            <div class="firstLine">
              <span> <img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>BUCURESTI, BUH</strong><br />
                <strong><i class="fa fa-plane"></i> Plecare</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 13:00</p>
            </div>
            <div class="secondLine">
              <span><img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>LONDON, HEATR</strong><br />
                <strong><i class="fa fa-plane rotate90"></i> Sosire</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 17:50</p>
            </div>
          </div>
        </td>
        <td>
          <a name="bestPrice">340 &euro;</a>
          <div class="toolTipPrice">
            <div class="topTitle">
              <p>Tarif total zbor dus-intors</p>
            </div>
            <div class="priceTool">
              <span class="price">399 &euro;</span>
            </div>
            <div class="firstLine">
              <span> <img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>BUCURESTI, BUH</strong><br />
                <strong><i class="fa fa-plane"></i> Plecare</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 13:00</p>
            </div>
            <div class="secondLine">
              <span><img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>LONDON, HEATR</strong><br />
                <strong><i class="fa fa-plane rotate90"></i> Sosire</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 17:50</p>
            </div>
          </div>
        </td>
      </tr>
      <tr>
        <th>Sam, 6 Mai</th>
        <td>
          <a name="bestPrice">173 &euro;</a>
          <div class="toolTipPrice">
            <div class="topTitle">
              <p>Tarif total zbor dus-intors</p>
            </div>
            <div class="priceTool">
              <span class="price">399 &euro;</span>
            </div>
            <div class="firstLine">
              <span> <img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>BUCURESTI, BUH</strong><br />
                <strong><i class="fa fa-plane"></i> Plecare</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 13:00</p>
            </div>
            <div class="secondLine">
              <span><img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>LONDON, HEATR</strong><br />
                <strong><i class="fa fa-plane rotate90"></i> Sosire</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 17:50</p>
            </div>
          </div>
        </td>
        <td>
          <a name="bestPrice">252 &euro;</a>
          <div class="toolTipPrice">
            <div class="topTitle">
              <p>Tarif total zbor dus-intors</p>
            </div>
            <div class="priceTool">
              <span class="price">399 &euro;</span>
            </div>
            <div class="firstLine">
              <span> <img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>BUCURESTI, BUH</strong><br />
                <strong><i class="fa fa-plane"></i> Plecare</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 13:00</p>
            </div>
            <div class="secondLine">
              <span><img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>LONDON, HEATR</strong><br />
                <strong><i class="fa fa-plane rotate90"></i> Sosire</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 17:50</p>
            </div>
          </div>
        </td>
        <td>
          <a name="bestPrice">302 &euro;</a>
          <div class="toolTipPrice">
            <div class="topTitle">
              <p>Tarif total zbor dus-intors</p>
            </div>
            <div class="priceTool">
              <span class="price">399 &euro;</span>
            </div>
            <div class="firstLine">
              <span> <img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>BUCURESTI, BUH</strong><br />
                <strong><i class="fa fa-plane"></i> Plecare</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 13:00</p>
            </div>
            <div class="secondLine">
              <span><img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>LONDON, HEATR</strong><br />
                <strong><i class="fa fa-plane rotate90"></i> Sosire</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 17:50</p>
            </div>
          </div>
        </td>
        <td>
          <a name="bestPrice">360 &euro;</a>
          <div class="toolTipPrice">
            <div class="topTitle">
              <p>Tarif total zbor dus-intors</p>
            </div>
            <div class="priceTool">
              <span class="price">399 &euro;</span>
            </div>
            <div class="firstLine">
              <span> <img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>BUCURESTI, BUH</strong><br />
                <strong><i class="fa fa-plane"></i> Plecare</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 13:00</p>
            </div>
            <div class="secondLine">
              <span><img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>LONDON, HEATR</strong><br />
                <strong><i class="fa fa-plane rotate90"></i> Sosire</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 17:50</p>
            </div>
          </div>
        </td>
        <td>
          <a name="bestPrice">354 &euro;</a>
          <div class="toolTipPrice">
            <div class="topTitle">
              <p>Tarif total zbor dus-intors</p>
            </div>
            <div class="priceTool">
              <span class="price">399 &euro;</span>
            </div>
            <div class="firstLine">
              <span> <img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>BUCURESTI, BUH</strong><br />
                <strong><i class="fa fa-plane"></i> Plecare</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 13:00</p>
            </div>
            <div class="secondLine">
              <span><img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>LONDON, HEATR</strong><br />
                <strong><i class="fa fa-plane rotate90"></i> Sosire</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 17:50</p>
            </div>
          </div>
        </td>
        <td>
          <a name="bestPrice">236 &euro;</a>
          <div class="toolTipPrice">
            <div class="topTitle">
              <p>Tarif total zbor dus-intors</p>
            </div>
            <div class="priceTool">
              <span class="price">399 &euro;</span>
            </div>
            <div class="firstLine">
              <span> <img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>BUCURESTI, BUH</strong><br />
                <strong><i class="fa fa-plane"></i> Plecare</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 13:00</p>
            </div>
            <div class="secondLine">
              <span><img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>LONDON, HEATR</strong><br />
                <strong><i class="fa fa-plane rotate90"></i> Sosire</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 17:50</p>
            </div>
          </div>
        </td>
        <td>
          <a name="bestPrice">211 &euro;</a>
          <div class="toolTipPrice">
            <div class="topTitle">
              <p>Tarif total zbor dus-intors</p>
            </div>
            <div class="priceTool">
              <span class="price">399 &euro;</span>
            </div>
            <div class="firstLine">
              <span> <img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>BUCURESTI, BUH</strong><br />
                <strong><i class="fa fa-plane"></i> Plecare</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 13:00</p>
            </div>
            <div class="secondLine">
              <span><img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>LONDON, HEATR</strong><br />
                <strong><i class="fa fa-plane rotate90"></i> Sosire</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 17:50</p>
            </div>
          </div>
        </td>
      </tr>
      <tr>
        <th>Dum, 7 Mai</th>
        <td>
          <a name="bestPrice">340 &euro;</a>
          <div class="toolTipPrice">
            <div class="topTitle">
              <p>Tarif total zbor dus-intors</p>
            </div>
            <div class="priceTool">
              <span class="price">399 &euro;</span>
            </div>
            <div class="firstLine">
              <span> <img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>BUCURESTI, BUH</strong><br />
                <strong><i class="fa fa-plane"></i> Plecare</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 13:00</p>
            </div>
            <div class="secondLine">
              <span><img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>LONDON, HEATR</strong><br />
                <strong><i class="fa fa-plane rotate90"></i> Sosire</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 17:50</p>
            </div>
          </div>
        </td>
        <td>
          <a name="bestPrice">210 &euro;</a>
          <div class="toolTipPrice">
            <div class="topTitle">
              <p>Tarif total zbor dus-intors</p>
            </div>
            <div class="priceTool">
              <span class="price">399 &euro;</span>
            </div>
            <div class="firstLine">
              <span> <img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>BUCURESTI, BUH</strong><br />
                <strong><i class="fa fa-plane"></i> Plecare</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 13:00</p>
            </div>
            <div class="secondLine">
              <span><img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>LONDON, HEATR</strong><br />
                <strong><i class="fa fa-plane rotate90"></i> Sosire</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 17:50</p>
            </div>
          </div>
        </td>
        <td>
          <a name="bestPrice">345 &euro;</a>
          <div class="toolTipPrice">
            <div class="topTitle">
              <p>Tarif total zbor dus-intors</p>
            </div>
            <div class="priceTool">
              <span class="price">399 &euro;</span>
            </div>
            <div class="firstLine">
              <span> <img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>BUCURESTI, BUH</strong><br />
                <strong><i class="fa fa-plane"></i> Plecare</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 13:00</p>
            </div>
            <div class="secondLine">
              <span><img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>LONDON, HEATR</strong><br />
                <strong><i class="fa fa-plane rotate90"></i> Sosire</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 17:50</p>
            </div>
          </div>
        </td>
        <td>
          <a name="bestPrice">323 &euro;</a>
          <div class="toolTipPrice">
            <div class="topTitle">
              <p>Tarif total zbor dus-intors</p>
            </div>
            <div class="priceTool">
              <span class="price">399 &euro;</span>
            </div>
            <div class="firstLine">
              <span> <img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>BUCURESTI, BUH</strong><br />
                <strong><i class="fa fa-plane"></i> Plecare</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 13:00</p>
            </div>
            <div class="secondLine">
              <span><img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>LONDON, HEATR</strong><br />
                <strong><i class="fa fa-plane rotate90"></i> Sosire</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 17:50</p>
            </div>
          </div>
        </td>
        <td>
          <a name="bestPrice">340 &euro;</a>
          <div class="toolTipPrice">
            <div class="topTitle">
              <p>Tarif total zbor dus-intors</p>
            </div>
            <div class="priceTool">
              <span class="price">399 &euro;</span>
            </div>
            <div class="firstLine">
              <span> <img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>BUCURESTI, BUH</strong><br />
                <strong><i class="fa fa-plane"></i> Plecare</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 13:00</p>
            </div>
            <div class="secondLine">
              <span><img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>LONDON, HEATR</strong><br />
                <strong><i class="fa fa-plane rotate90"></i> Sosire</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 17:50</p>
            </div>
          </div>
        </td>
        <td>
          <a name="bestPrice">340 &euro;</a>
          <div class="toolTipPrice">
            <div class="topTitle">
              <p>Tarif total zbor dus-intors</p>
            </div>
            <div class="priceTool">
              <span class="price">399 &euro;</span>
            </div>
            <div class="firstLine">
              <span> <img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>BUCURESTI, BUH</strong><br />
                <strong><i class="fa fa-plane"></i> Plecare</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 13:00</p>
            </div>
            <div class="secondLine">
              <span><img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>LONDON, HEATR</strong><br />
                <strong><i class="fa fa-plane rotate90"></i> Sosire</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 17:50</p>
            </div>
          </div>
        </td>
        <td>
          <a name="bestPrice">340 &euro;</a>
          <div class="toolTipPrice">
            <div class="topTitle">
              <p>Tarif total zbor dus-intors</p>
            </div>
            <div class="priceTool">
              <span class="price">399 &euro;</span>
            </div>
            <div class="firstLine">
              <span> <img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>BUCURESTI, BUH</strong><br />
                <strong><i class="fa fa-plane"></i> Plecare</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 13:00</p>
            </div>
            <div class="secondLine">
              <span><img src="<?php echo $this->theme_url; ?>assets/images/icons/tarom-icon.jpg" alt="TAROM" /><br />Tarom</span>
              <p>
                <strong>LONDON, HEATR</strong><br />
                <strong><i class="fa fa-plane rotate90"></i> Sosire</strong>: 01.05.2016, <br /><i class="fa fa-clock-o"></i> Ora: 17:50</p>
            </div>
          </div>
        </td>
      </tr>
    </tbody>
  </table>
  */ ?>
</div>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>