import BaseFunctionality from '../common/search.js?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';
export default {
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	extends: BaseFunctionality,
	data: () => {
		return {
			fetch_url: `${newux_url}/partials/presearch-wrapper/functionalities/<?php echo basename(dirname($a)); ?>/offer-list.json?${append_url}`,
			filter_component: 'partials/presearch-wrapper/functionalities/<?php echo basename(dirname($a)); ?>/filters',
			results_component: 'partials/presearch-wrapper/functionalities/<?php echo basename(dirname($a)); ?>/results',
			offer_component: 'partials/presearch-wrapper/functionalities/<?php echo basename(dirname($a)); ?>/offer',
			loading_component: 'partials/presearch-wrapper/functionalities/<?php echo basename(dirname($a)); ?>/loading',
			checkout_component: 'partials/presearch-wrapper/functionalities/<?php echo basename(dirname($a)); ?>/checkout',
		}
	},
	mounted() {
		this.$emit('set-value', {'step': 1});
	},
	computed: {
		breadcrumbs() {
			var dest_city = (this.data['<?php echo basename(dirname($a)); ?>/destination-city'] || {}).Name;
			return [
				{title: 'Acasa', step: 0},
				{title: 'Vacante', step: 0},
				<?php switch (basename(dirname($a))){ 
				case 'travelfuse-circuite': ?>
				{title: 'Circuite', step: 0},
				<?php
					break;
				default:
				?>
				{title: 'Chartere', step: 0},
				<?php 
					break;
				} ?>
				...(dest_city && [{title: dest_city, step: 1}] || []),
			];
		},
		fetch_data() {
			var travellers = (this.data['<?php echo basename(dirname($a)); ?>/travellers'] || {});
			var obj = {
				<?php switch (basename(dirname($a))){ 
				case 'travelfuse-circuite':
				?>
				// Transport: (this.data['<?php echo basename(dirname($a)); ?>/transport'] || {}).Id || (this.data['<?php echo basename(dirname($a)); ?>/check-in'] || {}).Transport || '',
				Transport: (this.data['<?php echo basename(dirname($a)); ?>/check-in'] || {}).Transport || '',
				// Transport: ['plane', 'bus'],
				<?php 
					break;
				default:
				?>
				// Transport: (this.data['<?php echo basename(dirname($a)); ?>/transport'] || {}).Id || (this.data['<?php echo basename(dirname($a)); ?>/check-out'] || {}).Transport || '',
				Transport: (this.data['<?php echo basename(dirname($a)); ?>/check-out'] || {}).Transport || '',
				CheckOut: (this.data['<?php echo basename(dirname($a)); ?>/check-out'] || {}).Id || '',
				<?php 
					break;
				} ?>
				
				Destination: (this.data['<?php echo basename(dirname($a)); ?>/destination-city'] || {}).Id || '',
				DestinationType: (this.data['<?php echo basename(dirname($a)); ?>/destination-city'] || {}).type || '',
				DepCityCode: (this.data['<?php echo basename(dirname($a)); ?>/departure-city'] || {}).Id || '',
				CheckIn: (this.data['<?php echo basename(dirname($a)); ?>/check-in'] || {}).Id || '',
				Adults: [(!isNaN(parseInt(travellers.ADT)) && (parseInt(travellers.ADT) > 0) && parseInt(travellers.ADT) || 0)
						+ (!isNaN(parseInt(travellers.YTH)) && (parseInt(travellers.YTH) > 0) && parseInt(travellers.YTH) || 0)],
				ChildrenAge: [travellers.CHD && Array.isArray(travellers.CHD) && travellers.CHD || []].filter(c => c && c.length),
			}
			obj.Children = obj.ChildrenAge.map((i) => (i||[]).length);
			if(this.$props.hotel){
			} else {
				console.warn('fetch_data', obj, this.data);
				if(obj.Transport && Array.isArray(obj.Transport)){
					return obj.Transport.map(tr => ({...obj, Transport: tr}));
				}
			}
			return obj;
		},
		country() {
			return (this.data['<?php echo basename(dirname($a)); ?>/destination-city'] || {}).type == 'country' && (this.data['<?php echo basename(dirname($a)); ?>/destination-city'] || {});
		},
		full_search_data() {
			var travellers = (this.data['<?php echo basename(dirname($a)); ?>/travellers'] || {});
			var obj = {
				... this.fetch_data,
				full: {
					ADT: (!isNaN(parseInt(travellers.ADT)) && (parseInt(travellers.ADT) > 0) && parseInt(travellers.ADT) || 0),
					YTH: (!isNaN(parseInt(travellers.YTH)) && (parseInt(travellers.YTH) > 0) && parseInt(travellers.YTH) || 0),
					CHD: (travellers.CHD && Array.isArray(travellers.CHD) && travellers.CHD || []).length,
					Children: (travellers.CHD && Array.isArray(travellers.CHD) && travellers.CHD || []),
					Transport: this.data['<?php echo basename(dirname($a)); ?>/transport'] && {
						Id: this.data['<?php echo basename(dirname($a)); ?>/transport'].Id,
						Name: this.data['<?php echo basename(dirname($a)); ?>/transport'].Name,
					} || undefined,
					Departure: this.data['<?php echo basename(dirname($a)); ?>/departure-city'] && {
						Id: this.data['<?php echo basename(dirname($a)); ?>/departure-city'].Id,
						type: this.data['<?php echo basename(dirname($a)); ?>/departure-city'].type,
						Name: this.data['<?php echo basename(dirname($a)); ?>/departure-city'].Name,
						Country: this.data['<?php echo basename(dirname($a)); ?>/departure-city'].Country,
						County: this.data['<?php echo basename(dirname($a)); ?>/departure-city'].County
					} || undefined,
					CheckIn: this.data['<?php echo basename(dirname($a)); ?>/check-in'] && {
						...this.data['<?php echo basename(dirname($a)); ?>/check-in']
					} || undefined,
					CheckOut: this.data['<?php echo basename(dirname($a)); ?>/check-out'] && {
						...this.data['<?php echo basename(dirname($a)); ?>/check-out']
					} || undefined,
					Nights: Math.round((((new Date((this.data['<?php echo basename(dirname($a)); ?>/check-out'] || {}).Id)).getTime()) - (new Date((this.data['<?php echo basename(dirname($a)); ?>/check-in'] || {}).Id)).getTime()) / 86400000),
				}
			}
			return obj;
		},
	},
}
