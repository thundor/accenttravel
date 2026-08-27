<?php
$this->load->model('Options_model');
$offers_settings = $this->Options_model->get('offers_weekend_settings');
if(!$offers_settings){
  $offers_settings = array();
}
$offers_settings = array_filter($offers_settings, function($offers_settings){ return !empty($offers_settings['enabled']); });

$this->load->model('Trip/Offer_weekend_model');

$time = time();
$n_friday = strtotime('next friday', $time);
$n_sunday = strtotime('next sunday', $n_friday);
$CheckIn = date('Y-m-d', $n_friday);
$CheckOut = date('Y-m-d', $n_sunday);

$items = array_reduce(array_keys($offers_settings), function($offers, $item_key) use ($offers_settings, $CheckIn, $CheckOut){
	$item_value = $offers_settings[$item_key];
	
    $item_key = preg_replace("/[^A-Z_]/", '', $item_key);
    if(strpos($item_key,'_') === false){
      $this->db->where('`zone` LIKE "' . $item_key . '\_%"');
    } else {
      $this->db->where('`zone` = "' . $item_key . '"');
    }
	$this->db->where('`type` = "hotel"');
    $this->db->order_by("SUBSTRING_INDEX(`time_modified`, ' ', 1) DESC");
    $this->db->order_by('price ASC');
    $this->db->where('time_modified > date_sub(NOW(), INTERVAL 1 MONTH)');
    $this->db->where('LENGTH(data)>0');
    // $zone_offers = $this->Offer_weekend_model->getOffers(array('limit'=>4));
    $zone_offers = $this->Offer_weekend_model->getOffers();
	$zone_offers = array_map(function($zone_offer) use ($offers_settings, $CheckIn, $CheckOut) {
		$zone_offer->data = unserialize($zone_offer->data);
		$zone_offer->url = site_url('trip/hotel/' . $zone_offer->type_id . '?sdate=' . $CheckIn . '&edate=' . $CheckOut . '?n=1');
		return $zone_offer;
	}, $zone_offers);
	$offers = array_merge($offers, $zone_offers);
	return $offers;
}, []);
?>
import BaseTemplate from './base.js?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';
import DOMPurify from 'dompurify';
export default {
	extends: BaseTemplate,
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	data: () => ({
		template: '<?php echo basename(__FILE__,'.js.php'); ?>',
		card_index: 0,
		company_index: 0,
		data: {
			CheckIn: '<?php echo $CheckIn; ?>',
			CheckOut: '<?php echo $CheckOut; ?>',
			children: <?php echo json_encode($items, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?>,
		}
	}),
	beforeMount() {
	},
	computed: {
	},
	watch:{},
	methods:{},
	template : `
	<v-row>
	<v-carousel
		height="auto"
		hide-delimiters
		fade
	  >
		<v-carousel-item
		  v-for="(slide, i) in Math.ceil((data?.children?.length || 0)/4)"
		  :key="i"
		>
		  <v-container>
			<v-row dense>
				<v-col
				  v-for="(child, j) in data.children.slice(i*4, (i + 1)*4)"
				  :key="j"
				  cols="12"
				  md="3"
				>
				<template v-for="item in [child.data]">
				<v-card
					class="mx-auto force-default h-100 d-flex flex-column text-decoration-none"
					max-width="344"
					@click="$emit('offer', item);"
					rounded="lg"
					tag="a"
					:href="child.url"
				  >
					<v-img :src="item.Image || '/themes/newux/assets/images/placeholder.webp'" rounded="lg" aspect-ratio="1.52" cover>
						<div class="text-body position-absolute bg-white rounded-xl px-1" style="left:5px;top:5px;" v-if="undefined !== item.Stars">
							<v-icon v-if="item.Stars" icon="mdi-star" v-for="n in parseInt(item.Stars||0)" color="#fcc200"></v-icon>
							<v-icon v-else icon="mdi-star-off-outline" color="#fcc200"></v-icon>
						</div>
					</v-img>
					<div class="d-flex flex-column flex-fill">
						<v-card-title v-text="item.Name"></v-card-title>

						<v-card-subtitle>
							<div class="d-flex flex-fill justify-space-between ga-3 flex-column flex-lg-row" >
								<div class="d-flex">
									<v-icon size="28" icon="mdi-map-marker-outline" class="me-3"></v-icon>
									<div class="d-flex flex-column text-wrap">
										<span v-text="[(item.CityName||''), (item.CountryName||'').Name].filter(v => !!v).join(', ')"></span>
										<small>Pret pentru doua persoane / 2 nopti / camera</small>
									</div>
								</div>
								<span class="d-flex flex-column text-right">
									<span>de la</span>
									<span class="text-primary text-body-2 font-weight-bold" v-text="format_price(item.MinPrice,item.Currency)"></span>
								</span>
							</div>
						</v-card-subtitle>

					</div>
					<v-card-actions>
						<div class="d-flex align-center">
							<v-icon size="28" icon="mdi-calendar-blank-outline" class="me-3"></v-icon>
							<div class="d-flex flex-column">
								<span v-text="dateIntervalFormatted(data.CheckIn, data.CheckOut, true)"></span>
							</div>
						</div>
					  <v-spacer></v-spacer>
					  <v-btn
						class="ms-2"
						size="large"
						text="Rezerva"
						variant="outlined"
					  ></v-btn>
					</v-card-actions>
				  </v-card>
				  </template>
				</v-col>
			  </v-row>
		  </v-container>
		</v-carousel-item>
	</v-carousel>
	</v-row>
	`,
}
