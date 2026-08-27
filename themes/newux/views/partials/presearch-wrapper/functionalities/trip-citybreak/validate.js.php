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
	Este necesara reverificarea disponibilitatii citybreak-ului
</div>
<div class="bg-background" v-if="validating">
	In curs de validare citybreak...
	<template v-if="0">
	<div>Preluare lista preturi: <span v-text="2 == result.Hotels.status && 'Da' || 'Nu'"></span></div>
	<div>Verificare hotel: <span v-text="result.Hotels.checking?.hotel && 'Da' || 'Nu'"></span></div>
	<div>Verificare oferta: <span v-text="result.Hotels.checking?.offer && 'Da' || 'Nu'"></span></div>
	<div>Verificare pachet: <span v-text="result.Flights.checking?.package && 'Da' || 'Nu'"></span></div>
	</template>
	<template v-if="0">
	<div>Preluare lista preturi: <span v-text="2 == result.Flights.status && 'Da' || 'Nu'"></span></div>
	<div>Verificare oferta: <span v-text="result.Flights.checking?.offer && 'Da' || 'Nu'"></span></div>
	<div>Verificare upsell: <span v-text="(result.Flights.checking?.upsells || result.Flights.checking?.upsell) && 'Da' || 'Nu'"></span></div>
	<div>Verificare optiuni: <span v-text="result.Flights.checking?.options && 'Da' || 'Nu'"></span></div>
	<div>Verificare locuri: <span v-text="result.Flights.checking?.seats && 'Da' || 'Nu'"></span></div>
	</template>
</div>
	`,
	computed: {
		is_valid(){
			return this.is_valid_flight && this.is_valid_hotel;
		},
		is_valid_flight(){
			return 1 === this.result?.Flights?.status 
			&& !this.result?.Flights?.checking?.offer
			&& !this.result?.Flights?.checking?.upsells
			&& !this.result?.Flights?.checking?.upsell
			&& !this.result?.Flights?.checking?.options
			&& !this.result?.Flights?.checking?.seats;
		},
		is_valid_hotel(){
			return 1 === this.result?.Hotels?.status 
			&& !this.result?.Hotels?.checking?.hotel
			&& !this.result?.Hotels?.checking?.offer
			&& !this.result?.Hotels?.checking?.package;
		},
		needsResearch(){
			return this.needsResearchFlight || this.needsResearchHotel;
		},
		needsResearchHotel(){
			return (this.result.Hotels.expiry - this.currentTime/1000) <= 1
		},
		needsResearchFlight(){
			return (this.result.Flights.expiry - this.currentTime/1000) <= 1
		},
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
			var needs_research = false;
			if(this.needsResearchHotel){
				needs_research = true;
				this.$emit('research', {...this.result.Hotels.research_hash, recheckForce: 1 + (this.result.Hotels.research_hash.recheckForce || 0)}, 'hotel');
			}
			if(this.needsResearchFlight){
				needs_research = true;
				this.$emit('research', {...this.result.Flights.research_hash, recheckForce: 1 + (this.result.Flights.research_hash.recheckForce || 0)}, 'flight');
			}
			if(needs_research){
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