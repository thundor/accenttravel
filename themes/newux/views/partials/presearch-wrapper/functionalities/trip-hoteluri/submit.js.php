import BaseFunctionality from '../common/submit.js?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';
let autoSubmitTimer;
export default {
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	extends: BaseFunctionality,
	data(){
		return {
			key: '<?php echo basename(dirname($a)) . '/' . basename($a, '.js'); ?>',
		}
	},
	computed: {
		fetch_object() {
			var HotelId = this.data['<?php echo basename(dirname($a)); ?>/hotel-id'] || '';
			var obj = {
				...(HotelId 
					? { HotelId: HotelId } 
					: { Destination: (this.data['<?php echo basename(dirname($a)); ?>/destination-city'] || {}).Id || '' }),
				CheckIn: (this.data['<?php echo basename(dirname($a)); ?>/check-in'] || {}).Id || '',
				CheckOut: (this.data['<?php echo basename(dirname($a)); ?>/check-out'] || {}).Id || '',
			}
			// console.warn('OBJ', this.data);
			return obj;
		},
		first_empty_key() {
			var obj = this.fetch_object;
			var objs = {
				...(!obj.HotelId ? {'Destination': 'destination-city'} : {}),
				'CheckIn': 'check-in',
				'CheckOut': 'check-out',
			};
			return objs[Object.keys(obj).find(k => '' === obj[k])];
		},
	},
	mounted() {
		if(this.fetch_object.HotelId){
			this.initiate_on_start = true;
		}
	},
	watch: {},
	methods: {
		<?php /* initiatePreSearch(){
			console.warn('initiatePreSearch', this.data);
			return;
		} */ ?>
	},
}
