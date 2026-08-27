export default {
	components : {
		'FlightsSearchRecent' : loadViewAsync('flights_search_recent'),
		'FlightsSearchPassengers' : loadViewAsync('flights_search_passengers'),
		'FlightsSearchDates' : loadViewAsync('flights_search_dates'),
		'FlightsSearchClass' : loadViewAsync('flights_search_class'),
		'FlightsSearchOrigin' : loadViewAsync('flights_search_origin'),
		'FlightsSearchDestination' : loadViewAsync('flights_search_destination'),
	},
	data () {
		return {
			type: '1',
			direct: false,
			flex: false,
			searches: [],
			kept: undefined,
			saved: {
				dates: undefined,
				origin: undefined,
				destination: undefined,
				passengers: undefined,
				cabine: undefined,
			},
			errors: [],
			mapper: Object.freeze({
				'now': 'n',
				'type': 't',
				'direct': 'r',
				'cabine': 'h',
				'flex': 'f',
				'date': 'd',
				'days': 'i',
				'origin': ['o', {
					'CityId': 'c',
					'LocationId': 'l',
					'CountryId': 'o',
					'LocationName': 'a',
					'CityName': 'i',
					'CountryName': 'u',
					'LocationCode': 'e',
					'CityCode': 'y',
				}],
				'destination': ['e', {
					'CityId': 'c',
					'LocationId': 'l',
					'CountryId': 'o',
					'LocationName': 'a',
					'CityName': 'i',
					'CountryName': 'u',
					'LocationCode': 'e',
					'CityCode': 'y',
				}],
				'passengers': ['p',{
					'adt': 'a',
					'sen': 's',
					'chd': 'c',
					'yth': 'y',
					'inf': 'f',
					'ins': 'n',
				}],
			}),
			texts: Object.freeze({
				no_origin: "Nu ati ales locatia de plecare",
				no_destination: "Nu ati ales destinatia",
				no_destination_date: "Nu ati ales data intoarcerii",
				same_origin_destination_city: "Orasul de plecare nu trebuie sa coincida cu cel destinatia",
				no_date: "Nu ati ales data plecarii",
				no_dates: "Nu ati ales intervalul calendaristic pentru plecare-sosire",
				no_passengers: "Nu ati ales pasagerii",
			}),
			validations:Object.freeze([
				() => !this.saved.origin || !this.saved.origin.CityId ? 'no_origin' : null,
				() => !this.saved.destination || !this.saved.destination.CityId ? 'no_destination' : null,
				() => this.saved.origin && this.saved.destination && this.saved.origin.CityId && (this.saved.origin.CityId == this.saved.destination.CityId) ? 'same_origin_destination_city' : null,
				() => !this.saved.dates ? ('0' == this.saved.type ? 'no_date' : 'no_dates') : null,
				() => this.saved.dates && !this.saved.dates.days && '0' !== this.type ? 'no_destination_date' : null,
				() => !this.saved.passengers ? 'no_passengers' : null,
			]),
		}
    },
	template : `
		<div class="search-form-wrapper search-flights-wrapper">
			<div class="search-form search-flights rounded-theme bg-background">
				<div class="px-4 pt-3">
				<v-radio-group inline color="primary" v-model="type" class="ms-2 mt-2 mb-3" hide-details="true">
					<v-radio label="Dus - intors" value="1"></v-radio>
					<v-radio label="Dus" value="0" class="ms-5"></v-radio>
				</v-radio-group>
			</div>
				<div class="rounded-theme border-theme mx-4 mb-4 v-btn--variant-outlined pt-4" style="overflow:hidden;">
					<FlightsSearchOrigin v-on:save="saveOrigin" v-model="saved.origin" :searches="searches" />
					<FlightsSearchDestination v-on:save="saveDestination" v-model="saved.destination" :searches="searches" :origin="saved.origin" />
					<?php /*
					<!--<span class="text-caption text-grey-darken-1">
						This is the email you will use to login to your Vuetify account
					</span>--> */ ?>
				</div>
				<div class="px-4">
					<div class="pb-4">
						<FlightsSearchDates v-on:save="saveDates" :single="this.type == '0'" v-model="saved.dates"  />
					</div>
					<div class="pb-4">
					<FlightsSearchPassengers v-on:save="savePassengers"  v-model="saved.passengers" />
					</div>
					<FlightsSearchClass v-on:save="saveClass" v-model="saved.cabine"  />
					<?php /*
						<!--
					<v-text-field
						label="Pasageri"
						placeholder="Alegeti numarul de pasageri"
					></v-text-field>
--> */ ?>
				</div>

				<div class="px-4">
					<v-switch
						hide-details
						inset
						color="primary"
						label="Doar zbor direct"
						class="flex-row-reverse"
						v-model="direct"
					></v-switch>
				</div>

				<div class="px-4">
					<v-switch
						hide-details
						color="primary"
						inset
						label="Date flexibile"
						class="flex-row-reverse"
						v-model="flex"
					></v-switch>
				</div>
			</div>

			<v-messages color="error" class="v-theme--light pa-4 mt-4 bg-theme bg-background rounded-theme" v-show="!!errors.length" :active="!!errors.length" :messages="errors"></v-messages>

			<FlightsSearchRecent v-model="searches" v-on:save="setSearch"/>
		</div>
	`,
	computed: {
	},
	methods: {
		saveDates(dates){
			if(!dates) return;
			this.saved.dates = dates;
		},
		saveClass(cls){
			this.saved.cabine = cls || '1';
		},
		savePassengers(passengers){
			this.saved.passengers = passengers;
		},
		saveOrigin(origin){
			// console.warn('saveOrigin');
			this.saved.origin = origin;
		},
		saveDestination(destination){
			this.saved.destination = destination;
		},
		clearValidations(){
			this.errors = [];
		},
		restoreSearch(){
			if(this.kept){
				setSearch(this.kept);
			}
		},
		saveSearch(){
			var s = {
				type: this.type,
				direct: this.direct,
				flex: this.flex,
				cabine: this.saved.cabine || '1',
				date: this.saved.dates.date.toString(),
				days: '0' == this.type ? 0 : this.saved.dates.days,
				passengers: this.saved.passengers,
				origin: this.saved.origin,
				destination: this.saved.destination,
			};
			this.kept = s;
		},
		setSearch(search){
			console.warn('setting search', search);
			if(!search) return;
			if(undefined !== search.type){
				this.type = search.type;
			}
			if(undefined !== search.direct){
				this.direct = search.direct;
			}
			if(undefined !== search.flex){
				this.flex = search.flex;
			}
			if(undefined !== search.passengers){
				this.saved.passengers = search.passengers;
			}
			if(undefined !== search.origin){
				this.saved.origin = search.origin;
			}
			if(undefined !== search.cabine){
				this.saved.cabine = search.cabine || '1';
			}
			if(undefined !== search.destination){
				this.saved.destination = search.destination;
			}
			if(undefined !== search.date || undefined !== search.days){
				this.saved.dates = {
					date: new Date(search.date),
					days: search.days,
				};
			}
			// console.warn(this.saved.dates);
		},
		addSearch(){
			console.warn('addSearch');
			if(!this.isValid()) return;
			var s = {
				now: new Date().toString(),
				type: this.type,
				direct: this.direct,
				flex: this.flex,
				date: this.saved.dates.date.toString(),
				days: '0' == this.type ? 0 : this.saved.dates.days,
				passengers: this.saved.passengers,
				origin: this.saved.origin,
				cabine: this.saved.cabine,
				destination: this.saved.destination,
			};
			console.warn(s);
			var sj = JSON.stringify(mapObjKeys(this.mapper, Object.assign(Object.assign({},s),{now:''})));
			var d;
			var index = this.searches.findIndex((v) => (JSON.stringify(mapObjKeys(this.mapper, Object.assign(Object.assign({},v),{now:''}))) == sj));
			if(index > -1){
				this.searches.splice(index, 1);
			}
			this.searches.unshift(s);
			this.searches.splice(10);
			saveStorage('pay24.flight.searches',this.searches.map((v) => (mapObjKeys(this.mapper, v))));
			return true;
		},
		isValid(){
			return !this.validations.find(f => {
				var v = f.bind(this)();
				return !!v;
			})
		},
		validate(){
			console.error('validate', this.errors)
			this.clearValidations();
			this.validations.every(f => {
				var v = f.bind(this)();
				v && this.errors.push(this.texts[v]);
				return true;
			})
			return !this.errors.length;
		},
	},
	created: function(){
		var s = getStorage('pay24.flight.searches','', [], [], []);
		this.searches = s.map((v) => (mapObjKeys(this.mapper, v, true)));
	},
	watch:{
		'saved': {
			handler(newValue, oldValue){
				var revalidate = !!this.errors.length;
				// console.warn('saved', revalidate);
				if(revalidate){
					this.validate();
				} else {
					this.clearValidations();
				}

			},
			deep: true
		},
	}
}
