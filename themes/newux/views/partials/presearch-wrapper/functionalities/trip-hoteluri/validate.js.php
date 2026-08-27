import BaseFunctionality from '../common/<?php echo basename($a, '.php'); ?>?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';
let check_interval;
export default {
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	emits: ['research'],
	extends: BaseFunctionality,
	data: () => ({
		validating: false,
		currentTime: Date.now(),
		timeInterval: undefined,
	}),
	template : `
<div class="bg-background" v-if="needsResearch">
	Este necesara reverificarea disponibilitatii hotelului
</div>
<div class="bg-background" v-if="validating">
	In curs de validare hotel...
	<template v-if="0">
	<div>Preluare lista preturi: <span v-text="2 == result.Hotels.status && 'Da' || 'Nu'"></span></div>
	<div>Verificare hotel: <span v-text="result.Hotels.checking?.hotel && 'Da' || 'Nu'"></span></div>
	<div>Verificare oferta: <span v-text="result.Hotels.checking?.offer && 'Da' || 'Nu'"></span></div>
	<div>Verificare pachet: <span v-text="result.Flights.checking?.package && 'Da' || 'Nu'"></span></div>
	</template>
</div>
	`,
	computed: {
		is_valid(){
			return 1 === this.result?.Hotels?.status 
			&& !this.result?.Hotels?.checking?.hotel
			&& !this.result?.Hotels?.checking?.offer
			&& !this.result?.Hotels?.checking?.package;
		},
		needsResearch(){
			return (this.result.Hotels.expiry - this.currentTime/1000) <= 1
		}
	},
	mounted() {
		this.timeInterval = setInterval(() => {this.currentTime = Date.now()}, 1000);
	},
	/* beforeUnmount() {
		clearInterval(check_interval);
	}, */
	methods:{
		validate(){
			if(this.validating) return;
			this.validating = true;
			if(this.needsResearch){
				this.$emit('research', {...this.result.Hotels.research_hash, recheckForce: 1 + (this.result.Hotels.research_hash.recheckForce || 0)}, 'hotel');
				return;
			}
			return new Promise((resolver) => {
				check_interval = setInterval(() => {
					if(this.is_valid){
						clearInterval(check_interval);
						this.validating = false;
						resolver(true);
					}
				}, 1000)
				
			});
		}
	},
	watch: {
		'data.step': {
			handler: function(nv,ov){
				clearInterval(check_interval);
				this.validating = false;
			},
			immediate: true
		},
	}
}