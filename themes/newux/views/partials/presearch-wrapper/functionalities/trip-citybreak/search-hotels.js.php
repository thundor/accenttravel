import BaseFunctionality from '../trip-hoteluri/search.js?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';
export default {
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	extends: BaseFunctionality,
	data: () => {
		return {
			list_variable: 'hotels',
		}
	},
	mounted() {
		this.$emit('set-value', {'step': 1});
	},
	computed: {
		breadcrumbs() {
			return [
				{title: 'Acasa', step: 0},
				{title: 'Hoteluri', step: 0},
				{title: (this.data['<?php echo basename(dirname($a)); ?>/destination-city'] || {}).Name, step: 1},
			];
		},
		fetch_data() {
			var obj = {
				cityId: (this.data['<?php echo basename(dirname($a)); ?>/destination-city'] || {}).CityId || (this.data['<?php echo basename(dirname($a)); ?>/destination-city'] || {}).Id || '',
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
			var passengers = (this.data['<?php echo basename(dirname($a)); ?>/travellers'] || {});
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
