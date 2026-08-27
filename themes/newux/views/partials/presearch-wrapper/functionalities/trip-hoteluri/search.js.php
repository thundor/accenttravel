import BaseFunctionality from '../common/search.js?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';
export default {
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	extends: BaseFunctionality,
	data: () => {
		return {
			use_inspection: true,
			list_variable: 'hotels',
			filters_url: `${newux_url}/partials/presearch-wrapper/functionalities/<?php echo basename(dirname($a)); ?>/filters-search.json?${append_url}`,
			summary_url: `${newux_url}/partials/presearch-wrapper/functionalities/<?php echo basename(dirname($a)); ?>/summary-search.json?${append_url}`,
			initiate_url: `${newux_url}/partials/presearch-wrapper/functionalities/<?php echo basename(dirname($a)); ?>/initiate-search.json?${append_url}`,
			inspect_url: `${newux_url}/partials/presearch-wrapper/functionalities/<?php echo basename(dirname($a)); ?>/inspect-search.json?${append_url}`,
			fetch_url: `${newux_url}/partials/presearch-wrapper/functionalities/<?php echo basename(dirname($a)); ?>/offer-list.json?${append_url}`,
			filter_component: 'partials/presearch-wrapper/functionalities/<?php echo basename(dirname($a)); ?>/filters',
			results_component: 'partials/presearch-wrapper/functionalities/<?php echo basename(dirname($a)); ?>/results',
			offer_component: 'partials/presearch-wrapper/functionalities/<?php echo basename(dirname($a)); ?>/offer',
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
				{title: 'Hoteluri', step: 0},
				...(dest_city && [{title: dest_city, step: 1}] || []),
			];
		},
		fetch_data() {
			var obj = {
				hotelId: (this.data['<?php echo basename(dirname($a)); ?>/hotel-id'] || {}) || '',
				cityId: (this.data['<?php echo basename(dirname($a)); ?>/destination-city'] || {}).Id || '',
				dIn: (this.data['<?php echo basename(dirname($a)); ?>/check-in'] || {}).Id || '',
				dOut: (this.data['<?php echo basename(dirname($a)); ?>/check-out'] || {}).Id || '',
				r: this.data['<?php echo basename(dirname($a)); ?>/travellers'] || [],
			}
			return obj;
		},
		country() {
			return (this.data['<?php echo basename(dirname($a)); ?>/destination-city'] || {}).type == 'country' && (this.data['<?php echo basename(dirname($a)); ?>/destination-city'] || {});
		},
		full_search_data() {
			var passengers = (this.data['<?php echo basename(dirname($a)); ?>/travellers'] || []);
			var travellers = (passengers || []).reduce((c, i) => {
				c['ROM'] = c['ROM'] ?? 0;
				c['ROM']++;
				Object.keys(i).forEach(a => {
					if(!i[a]) return;
					if(Array.isArray(i[a])){
						if(!i[a].length) return;
						if(!c[a]) {
							c[a] = i[a];
						} else {
							if(!Array.isArray(c[a])){
								console.error(passengers);
								throw "Invalid travellers";
							}
							c[a] = c[a].concat(i[a]);
						}
					} else {
						if(isNaN(i[a])) return;
						if(!c[a]) {
							c[a] = parseInt(i[a]);
						} else {
							if(Array.isArray(c[a])){
								console.error(passengers);
								throw "Invalid travellers";
							}
							c[a] = parseInt(c[a]) + parseInt(i[a]);
						}
					}
					// console.warn(a, i[a], i, c);
				});
				return c;
			}, {});
			var obj = {
				... this.fetch_data,
				full: {
					ADT: (!isNaN(parseInt(travellers.ADT)) && (parseInt(travellers.ADT) > 0) && parseInt(travellers.ADT) || 0),
					SEN: (!isNaN(parseInt(travellers.SEN)) && (parseInt(travellers.SEN) > 0) && parseInt(travellers.SEN) || 0),
					YTH: (!isNaN(parseInt(travellers.YTH)) && (parseInt(travellers.YTH) > 0) && parseInt(travellers.YTH) || 0),
					CHD: (travellers.CHD && Array.isArray(travellers.CHD) && travellers.CHD || []).length,
					Children: (travellers.CHD && Array.isArray(travellers.CHD) && travellers.CHD || []),
					Departure: this.data['<?php echo basename(dirname($a)); ?>/departure-city'] && {
						Id: this.data['<?php echo basename(dirname($a)); ?>/departure-city'].Id,
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
					Nights: Math.round((((new Date((this.data['<?php echo basename(dirname($a)); ?>/check-out'] || {}).Id))) - (new Date((this.data['<?php echo basename(dirname($a)); ?>/check-in'] || {}).Id))) / 86400000),
				}
			}
			return obj;
		},
	},
}
