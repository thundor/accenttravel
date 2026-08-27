import FormLegend from '../../../form/legend.js?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';
export default {
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	inject: ['search'],
	emits: ['offer','set-value','research'],
	components:{
		'FormLegend': FormLegend,
	},
	data: () => {
		return {
			assigned_passengers: null,
			fare_rules: undefined,
			fare_rules_show: {},
			prev_hash: undefined,
			result: undefined,
			ancillery: undefined,
			selected_upsell: undefined,
			chosen_package: undefined,
			details_package: undefined,
			applied_package: undefined,
			flight2_real: null,
			room_details_dialog: false,
			room_details_dialog_tab: 0,
			view_offer_count: 3,
			view_room_offer_limit: 3,
			view_room_offer_count: {},
			expand2: [],
			expand: [],
			flight: {},
			result_details_url: `${newux_url}/partials/presearch-wrapper/functionalities/<?php echo basename(dirname($a)); ?>/result-details.json?${append_url}`,
			offer_details_url: `${newux_url}/partials/presearch-wrapper/functionalities/<?php echo basename(dirname($a)); ?>/offer-details.json?${append_url}`,
			ancillery_details_url: `${newux_url}/partials/presearch-wrapper/functionalities/<?php echo basename(dirname($a)); ?>/ancillery-details.json?${append_url}`,
			fullscreen_image: '',
			fullscreen_image2: '',
			chosen_index: undefined,
			text_result_type: [],
			checkout_component: undefined,
		}
	},
	props: {
		show_breadcrumbs: {
			type: Boolean,
			default: () => (true),
		},
      results: {
          type: [Array, Object],
          default: () => ([]),
      },
      applied_filters: {
          type: Object,
          default: () => ({}),
      },
      hotel: {
          type: Object,
          default: () => (undefined),
      },
      hotel_inspection: {
          type: Object,
          default: () => (undefined),
      },
      flight2: {
          type: Object,
          default: () => (undefined),
      },
      flight2_inspection: {
          type: Object,
          default: () => (undefined),
      },
      data: {
          type: Object,
          default: () => ({}),
      },
      search_data: {
          type: Object,
          default: () => (undefined),
      },
      prepend_breadcrumbs: {
          type: Array,
          default: () => ([]),
      },
      inspection: {
          type: Object,
          default: () => (undefined),
      },
      offer: {
          type: Object,
          default: () => (undefined),
      },
      searching: {
          type: Boolean,
          default: () => (false),
      },
      set_checkout_component: {
          type: String,
          default: () => (undefined),
      },
      sorted: {
          type: String,
          default: () => (''),
      },
	  search_wrapper_step: {
		  type: Number,
		  default: () => (0),
		},
	},
	watch: {
		'flight2': {
			handler: function(nv,ov){
				this.flight2_real = {...nv};
			},
			immediate: true
		},
		'ancilleryOrFlight': {
			handler: function(nv,ov){
				console.warn('ancilleryOrFlight', nv);
				this.computeFareRules();
			},
			immediate: true
		},
		'chosen_index': {
			handler: function(nv,ov){
				if(nv){
					this.setResearchHash();
				}
			},
			immediate: true
		},
		'hotel': {
			handler: function(nv,ov){
				console.error('hotel', nv);
			},
			immediate: true
		},
		'offer': {
			handler: function(nv,ov){
				this.prev_hash = this.inspection.research_hash && JSON.parse(JSON.stringify(this.inspection.research_hash)) || undefined;
				console.error('offer', nv);
				this.flight = nv;
				this.view_offer_count = 6;
				this.expand = [];
				this.chosen_package = undefined;
				this.selected_upsell = undefined;
				this.ancillery = undefined;
				if(!this.flight) return;
				// this.setResearchHash();
				this.fetchFlightDetails(this.flight);
			},
			immediate: true
		},
		'chosen_package': {
			handler: function(nv,ov){
				this.applied_package = undefined;
				// this.expand = Object.keys(nv && ((nv.PackageRooms || {}).PackageRoom) || []);
			},
			immediate: true
		},
		'results': {
			handler: function(nv,ov){
				console.warn('flight results',nv); 
			},
			immediate: true
		},
		'selected_upsell': {
		  handler(newValue, oldValue){
			console.warn('changed_upsell', newValue);
			this.ancillery = undefined;
			// this.$emit('flight_data', undefined);
			if(newValue){
			  this.flight.loadingUpsells[newValue] = 1;
			  this.loadAncillery(newValue).then((d) => {
				if(this.selected_upsell != newValue) return;
				this.ancillery = d;
				console.warn('selected_ancillery', this.ancillery);
				// this.$emit('flight_data', this.ancillery)
			  }).finally(() => {
				delete(this.flight.loadingUpsells[newValue]);
			  });
			} else {
			  console.warn('selected_ancillery', this.flight_data);
			  // this.$emit('flight_data', this.flight_data)
			}
		  },
		  immediate: true
		},
	},
	beforeCreate() {
	},
	mounted() {
		var step = parseInt(window_url.searchParams.get("step"));
		if(!isNaN(step) && step > 2){
			console.error('forced offer click');
			this.chosen_index = (this.chosen_index || 0)+1;
			this.$emit('set-value',{'step': 3});
		} else {
			console.error('blocked forced offer click');
		}
		/*
		
		*/
	},
	computed: {
		hash() {
			return {
				search_data: {
					depLocationId: this.search_data.depLocationId,
					depCityId: this.search_data.depCityId,
					destLocationId: this.search_data.destLocationId,
					destCityId: this.search_data.destCityId,
					class: this.search_data.class,
					type: this.search_data.type,
					dIn: this.search_data.dIn,
					dOut: this.search_data.dOut,
					r: this.search_data.r,
				},
				flight: {
					result: this.result,
					Price: this.flight?.PriceDetail?.Amount,
					Currency: this.flight?.PriceDetail?.Currency,
					DepartureDate: this.flight?.DepartureDate,
					DestinationDate: this.flight?.DestinationDate,
					System: this.flight?.System,
					NonRefundable: this.flight?.NonRefundable,
					BrandedFare: JSON.stringify(this.flight?.BrandedFare),
					Upsell: this.selected_upsell && {
						Price: this.flight.Ancilleries[this.selected_upsell]?.PriceDetail?.Amount,
						Currency: this.flight.Ancilleries[this.selected_upsell]?.PriceDetail?.Currency,
						BrandedFare: this.flight.Upsells.find(u => u.Code == this.selected_upsell)?.FareDetails?.BrandedFare,
					} || undefined,
					Flights: (this.flight?.Flights || []).map(f => JSON.stringify((f.Segment || []).map(v => JSON.parse(JSON.stringify(v))).map(a => (a.Flight.NumberOfSeats=null, a)))),
					/* Flights: (this.flight?.Flights || []).map(f => ({
						Duration: f?.Duration,
						(f?.Segment || []).reduce((carry, segment) => {
							carry.push({
								BaggageAllowance: segment?.BaggageAllowance,
								Carrier: {
									Marketing: segment?.Carrier?.Marketing?.Code,
									Operating: segment?.Carrier?.Operating?.Code,
								},
								Destination: {
									Marketing: segment?.Carrier?.Marketing?.Code,
									Operating: segment?.Carrier?.Operating?.Code,
								},
							})
						}, [])
					})), */
				},
			};
		},
		
		passport_required () {
			console.error('passport_required', this.search_data);
			var destination_country = getObjectDotPathValue(this.search_data || {}, 'full.Destination.Country', '');
			var origin_country = getObjectDotPathValue(this.search_data || {}, 'full.Departure.Country', '');
			var UE_countries = [
				'Austria', 'Belgium', 'Bulgaria', 'Croatia', 'Cyprus', 'Czech Republic', 'Denmark', 'Estonia', 'Finland', 'France', 'Germany', 'Greece', 'Hungary', 'Ireland', 'Italy', 'Latvia', 'Lithuania', 'Luxembourg', 'Malta', 'Netherlands', 'Poland', 'Portugal', 'Romania', 'Slovakia', 'Slovenia', 'Spain', 'Sweden',
				
				"Belgia","Cipru","Cehia","Germania","Danemarca","Spania","Finlanda","Franta","Grecia","Ungaria","Republica Irlanda","Italia","Lituania","Luxemburg","Letonia","Tarile de Jos","Polonia","Portugalia","Suedia","Slovacia"
			];
			var US_passport_free = [
				'United States',
				'Guam',
				'American Samoa',
				'Northern Mariana Islands',
				'Puerto Rico',
				'United States Virgin Islands',
				
				'Samoa americana',
				'Insulele Mariane de Nord',
				'Statele Unite ale Americii',
			];
			if(-1 !== US_passport_free.indexOf(destination_country) && -1 !== US_passport_free.indexOf(origin_country)){
			} else if(-1 !== UE_countries.indexOf(destination_country) && -1 !== UE_countries.indexOf(origin_country)){
			} else if(destination_country != origin_country){
				return true;
			}
			return false;
		},
		company_images() {
			var ci = (((this.inspection || {}).filters || {}).companies || []).reduce((c, i) => (c[i.code] = i.img, c), {});
			// console.warn('company_images', ci);
			return ci;
		},
		ancilleryOrFlight() {
			return this.selected_upsell && this.flight.Ancilleries && this.flight.Ancilleries[this.selected_upsell] || (this.inspection.research_hash?.flight?.Upsell ? null : this.flight.Details);
		},
		firstoffer() {
			return {};
		},
		breadcrumbs() {
			return [
				... this.prepend_breadcrumbs,
				{title: 'Detalii zbor', step: 2},
			];
		},
	},
	methods: {
		setFlightOffer(offer){
			console.error('setFlightOffer', offer)
		},
		 basisDescription(b){
		if(!this.fare_rules || !this.fare_rules.basis_description || !this.fare_rules.basis_description[b]) return 'Regula ' + b;
		var basis = this.fare_rules.basis_description[b];
		var keys = Object.keys(basis);
		
		return Object.keys(basis).reduce((a, k) => {
			var r = basis[k];
			a.push('' 
				+ (r.AirportCode && (r.AirportName && r.AirportName != r.AirportCode ? r.AirportName + ' (' + r.AirportCode + ')' : r.AirportCode))
				+ (r.OriginCode && (' ' + (r.OriginCityName && r.OriginCityName != r.OriginCode ? r.OriginCode + ' (' + r.OriginCode + ')' : r.OriginCode)) || '')
				+ (r.DestinationCode && r.DestinationCode != r.OriginCode && (' - ' + (r.DestinationCityName && r.DestinationCityName != r.DestinationCode ? r.DestinationCode + ' (' + r.DestinationCode + ')' : r.DestinationCode)) || '')
			);
			return a;
		}, []).join(', ') + ' (' + b + ')'
	},
    fixCategory(r){
      r = ('' + (r || '')).trim();
	  r = r.replace(/(^|[\r\n]+)((\s*([^\s].+?)([\r\n])){1,1})(\s*\2)+/g, '$2');
	  r = r.replace(/(^|[\r\n]+)((\s*([^\s].+?)([\r\n])){1,2})(\s*\2)+/g, '$2');
	  r = r.replace(/(^|[\r\n]+)((\s*([^\s].+?)([\r\n])){1,3})(\s*\2)+/g, '$2');
	  r = r.replace(/(^|[\r\n]+)((\s*([^\s].+?)([\r\n])){1,4})(\s*\2)+/g, '$2');
	  r = r.replace(/(^|[\r\n]+)((\s*([^\s].+?)([\r\n])){1,5})(\s*\2)+/g, '$2');
	  r = r.replace(/(^|[\r\n]+)((\s*([^\s].+?)([\r\n])){1,6})(\s*\2)+/g, '$2');
	  r = r.replace(/(^|[\r\n]+)((\s*([^\s].+?)([\r\n])){1,7})(\s*\2)+/g, '$2');
	  r = r.replace(/(^|[\r\n]+)((\s*([^\s].+?)([\r\n])){1,8})(\s*\2)+/g, '$2');
	  r = r.replace(/(^|[\r\n]+)((\s*([^\s].+?)([\r\n])){1,9})(\s*\2)+/g, '$2');
	  r = r.replace(/(^|[\r\n]+)((\s*([^\s].+?)([\r\n])){1,10})(\s*\2)+/g, '$2');
	  return r;
    },
		computeFareRules(){
      this.fare_rules = undefined;
      var af = this.ancilleryOrFlight;
      if(!af) return;

      var f = {};
      var ptcs = af && Object.keys(af.FareRules) || [];
	  
	var fare_rules = JSON.parse(JSON.stringify(af.FareRules));
	var basis_description = {};
	for (var ptc in fare_rules){
		for (var rule_index in fare_rules[ptc]){
			var fare_rule = fare_rules[ptc][rule_index];
			var fare_basis = getObjectDotPathValue(fare_rule, 'FareBasis') || '';
			var k = [getObjectDotPathValue(fare_rule,'Airline.Code'), getObjectDotPathValue(fare_rule,'Origin.Code'), getObjectDotPathValue(fare_rule,'Origin.Code')]
			basis_description[fare_basis] = basis_description[fare_basis] || {};
			basis_description[fare_basis][k] = basis_description[fare_basis][k] || {};
			basis_description[fare_basis][k]['AirportCode'] = basis_description[fare_basis][k]['AirportCode'] || getObjectDotPathValue(fare_rule,'Airline.Code');
			basis_description[fare_basis][k]['AirportName'] = basis_description[fare_basis][k]['AirportName'] || getObjectDotPathValue(fare_rule,'Airline.Name');
			basis_description[fare_basis][k]['OriginCityName'] = basis_description[fare_basis][k]['OriginCityName'] || getObjectDotPathValue(fare_rule,'Origin.City');
			basis_description[fare_basis][k]['DestinationCityName'] = basis_description[fare_basis][k]['DestinationCityName'] || getObjectDotPathValue(fare_rule,'Destination.City');
			basis_description[fare_basis][k]['OriginCode'] = basis_description[fare_basis][k]['OriginCode'] || getObjectDotPathValue(fare_rule,'Origin.Code');
			basis_description[fare_basis][k]['DestinationCode'] = basis_description[fare_basis][k]['DestinationCode'] || getObjectDotPathValue(fare_rule,'Destination.Code');
			basis_description[fare_basis][k]['OriginCityCode'] = basis_description[fare_basis][k]['OriginCityCode'] || getObjectDotPathValue(fare_rule,'Origin.CityCode');
			basis_description[fare_basis][k]['DestinationCityCode'] = basis_description[fare_basis][k]['DestinationCityCode'] || getObjectDotPathValue(fare_rule,'Destination.CityCode');
			
			var cats_cnt = {};
			// console.warn(fare_rules[ptc]);
			for (var category_index in fare_rules[ptc][rule_index].Category){
				var cat = fare_rules[ptc][rule_index].Category[category_index];
				if(0 == category_index){
					var index, found=1;
					while(found && (-1 != (index = fare_rules[ptc][rule_index].Category.slice(parseInt(category_index) + 1).findIndex((c) => c.Name == cat.Name)))){
						var c = fare_rules[ptc][rule_index].Category[1 + parseInt(index) + parseInt(category_index)];
						found = false;
						if(c.Url === cat.Url && c._ === cat._){
							if(JSON.stringify(fare_rules[ptc][rule_index].Category.slice(parseInt(category_index), parseInt(category_index) + 1 + parseInt(index))) == JSON.stringify(fare_rules[ptc][rule_index].Category.slice(1 + parseInt(index) + parseInt(category_index), parseInt(category_index) + 2 * (1 + parseInt(index))))){
								fare_rules[ptc][rule_index].Category.splice(1 + parseInt(index) + parseInt(category_index), parseInt(category_index) + 2 * (1 + parseInt(index)));
								found = 1;
							}
						}
					}
				}
				if(cats_cnt[cat.Name]){
					cat.Name += " (" + cats_cnt[cat.Name] + ")";
				}
				
				cats_cnt[cat.Name] = (cats_cnt[cat.Name] || 0) + 1;
			}
		}
	}
	  
      var cats = (getObjectDotPathValue(fare_rules,'*.*.Category') || []).flat(1);
      var airline = (getObjectDotPathValue(fare_rules,'*.*.Airline') || []).flat(1);
      var all_categories = [...new Set((getObjectDotPathValue(fare_rules,'*.*.Category.*.Name') || []).flat(1))];
      var category_name_to_general = all_categories.reduce((o, v, y, z, d) => (o[v] = [...new Set(((d = cats.filter((a) => a.Name == v).map(a => a._)) && d.length == airline.length ? d : []))].length==1 , o), {});
	  var basises_arr = [...new Set((getObjectDotPathValue(fare_rules,'*.*.FareBasis') || []).flat(1))];
      if(!basises_arr.length) return;
	  if(basises_arr.length == 1){
		  for(var i in category_name_to_general){
			  category_name_to_general[i] = false;
		  }
	  }
      var general_categories_arr = Object.keys(category_name_to_general).filter((v) => !!category_name_to_general[v])
      var particular_categories_arr = Object.keys(category_name_to_general).filter((v) => !category_name_to_general[v])
      var all_fare_rules_arr = (getObjectDotPathValue(fare_rules,'*') || []).flat(1) || [];
      
	   var general_per_basis = basises_arr.reduce((o, b, d) => ((d = particular_categories_arr.filter((c, e) => ((e = all_fare_rules_arr.filter(r => r.FareBasis == b).reduce((p,r) => p.concat((r.Category || []).filter(c2 => c2.Name == c).map(c2 => c2._)), [])), e.length == ptcs.length && [...new Set(e)].length == 1) )), d.length && (o[b] = d.reduce((o2,c) => ((o2[c] = all_fare_rules_arr.filter(r => r.FareBasis == b).reduce((p,r) => p.concat((r.Category || []).filter(c2 => c2.Name == c)), [])[0]), o2), {})), o), {});
      var general_categories = general_categories_arr.reduce((o, c) => ((o[c] = all_fare_rules_arr.reduce((p,r,y,z) => (z.splice(y+1),p.concat((r.Category || []).filter(c2 => c2.Name == c)[0])), [])[0]), o), {});
      var particular_per_basis = basises_arr.reduce((o, b, d) => {
        var p = ptcs.reduce((pr,pi) => (pr[pi] = {}, pr), {});
        o[b] = p;
        return o;
      },{});
      if(af){
        for(var ptc in fare_rules){
          var ptc_fareRule = fare_rules[ptc];
          for(var fi in ptc_fareRule){
            var ptc_f_fare_rule = ptc_fareRule[fi];
            // console.warn(ptc_f_fare_rule);
            for(var ci in ptc_f_fare_rule.Category){
              var c_categ = ptc_f_fare_rule.Category[ci];
              if(!general_categories[c_categ.Name] && (!general_per_basis[ptc_f_fare_rule.FareBasis] || !general_per_basis[ptc_f_fare_rule.FareBasis][c_categ.Name])){
                particular_per_basis[ptc_f_fare_rule.FareBasis][ptc][c_categ.Name] = c_categ;
              }
            }
          }
		}
		for(var ptc in particular_per_basis){
          if(particular_per_basis[ptc]){
            for(var k in particular_per_basis[ptc]){
              if(!particular_per_basis[ptc][k] || !Object.keys(particular_per_basis[ptc][k]).length){
                delete(particular_per_basis[ptc][k]);
              }
            }
          }
		  if(!particular_per_basis[k] || !Object.keys(particular_per_basis[ptc]).length){
            delete(particular_per_basis[ptc]);
          }
        }
      }



      this.fare_rules = {
        basises_arr: basises_arr,
        general_per_basis: general_per_basis,
        particular_per_basis: particular_per_basis,
        general_categories: !Object.keys(general_categories).length ? undefined : general_categories,
        basis_description: basis_description,
      };
      console.warn('fare_rules', this.fare_rules);
    },
		setResearchHash(){
			this.inspection.research_hash = JSON.parse(JSON.stringify(this.hash));
		},
		format_price_obj_amount_currency(obj){
			return this.format_price(obj.Amount, obj.Currency);
		},
		format_transport(text){
			text = (text||'').trim();
			text = text.replace(/^transport\b[\:\s\-]*/i, '');
			text = text.replace(/^avion\b[\:\s\-]*/i, '');
			text = text.replace(/^dus\b[\:\s\-]*/i, '');
			text = text.replace(/^retur\b[\:\s\-]*/i, '');
			text = text.replace(/\s*\(.*?\)\s*/g, ' ');
			text = text.replace(/([a-z])\s*[1-9][0-9]{3,}.*/ig, '$1');
			text = text.replace(/[\:\s\-]*\b(0[1-9]|[1-2][0-9]|3[0-1])([\.\-\/])(0[1-9]|1[0-2])\2([1-9][0-9]{3,})\b[\:\s\-]*(([0-1][0-9]|2[0-3])([\:\s\-]*[0-5][0-9])?)?[\:\s\-]*/g, ' ');
			// text = text.replace(/\s*[a-z]+\s+-\s+.*?()/g, ' ');
			text = text.replace(/\s+/, ' ').trim();
			
			return text;
		},
		allHotelImages(flight,len){
			return this.arrayChunk([(flight.Image || '')].concat(flight.Gallery|| []).filter(i => i).filter((v, i, a) => a.indexOf(v) === i), len);
		},
		arrayChunk(arr, len){
			len = len || 4;
			return arr.reduce((all,one,i) => {
			   const ch = Math.floor(i/len); 
			   all[ch] = [].concat((all[ch]||[]),one); 
			   return all
			}, []);
		},
		loadAncillery(ancillery_code) {
		  var fetch_url = this.ancillery_details_url;
			if(!fetch_url) return;
			
			this.inspection.checking = this.inspection.checking || {};
			this.inspection.checking.upsell = true;
			this.flight.loadingDetails = true;
			var data = {
				<?php if ($this->config->item('csrf_protection')){ ?>
				<?php echo json_encode($this->security->get_csrf_token_name()); ?>: <?php echo json_encode($this->security->get_csrf_hash()); ?>,
				<?php } ?>
				Code: this.inspection.code,
				ItineraryCode: this.flight.Details.ItineraryCode,
				AncilleryCode: ancillery_code,
			};
			return fetch(fetch_url, {
				method: 'POST',
				headers: {
				  'Accept': 'application/json'
				},
				body: new URLSearchParams(objToSerialize(data))
			}).then((response) => {
				if (!response.ok) {
					if(response.status == 403){
						// CSRF
						window.location = window.location.href.replace(/#.*/, '');
						throw new Error("Network response failed. Redirecting to self", {cause: response });
					}
					throw new Error("Network response was not ok", {cause: response });
				}
				return response;
			}).then((response) => response.json()).then((h) => {
				if(!h || undefined !== h.length && !h.length){
					throw 'Could not load';
				}
				if(!h || !h.ItineraryCode) return;
				this.flight.Ancilleries[ancillery_code] = h;
				
				console.error("loaded Ancillery", this.flight);
				
			}).catch((e) => {
				this.$emit('research', JSON.parse(JSON.stringify(this.hash)), 'flight');
				console.error('Should research', JSON.parse(JSON.stringify(this.hash)), 'flight');
				console.error("Failed to fetch flight details", e);
				// Do nothing
			}).finally(() => {
				this.flight.loadingDetails = false;
				this.inspection.checking.upsell = false;
			})
		},
		fetchFlightDetails(flight){
			var fetch_url = this.result_details_url;
			if(!fetch_url) return;
			
			flight.loadingDetails = true;
			
			this.inspection.checking = this.inspection.checking || {};
			this.inspection.checking.offer = true;
			
			var data = {
				<?php if ($this->config->item('csrf_protection')){ ?>
				<?php echo json_encode($this->security->get_csrf_token_name()); ?>: <?php echo json_encode($this->security->get_csrf_hash()); ?>,
				<?php } ?>
				Code: this.inspection.code,
				ItineraryCode: this.flight.ItineraryCode,
				CombinationIndex: (this.flight.RealCombinations || this.flight.Combinations).indexOf(flight.Combination),
			};
			/* this.data.trip_avion_tries = this.data.trip_avion_tries ?? 0;
			if(!this.data.trip_avion_tries){
				this.data.trip_avion_tries++;
				this.$emit('research', JSON.parse(JSON.stringify(this.hash)), 'flight');
				console.error('Should research', JSON.parse(JSON.stringify(this.hash)), 'flight');
				flight.loadingDetails = false;
				return;
			} */
			fetch(fetch_url, {
				method: 'POST',
				headers: {
				  'Accept': 'application/json'
				},
				body: new URLSearchParams(objToSerialize(data))
			}).then((response) => {
				if (!response.ok) {
					if(response.status == 403){
						// CSRF
						window.location = window.location.href.replace(/#.*/, '');
						throw new Error("Network response failed. Redirecting to self", {cause: response });
					}
					throw new Error("Network response was not ok", {cause: response });
				}
				return response;
			}).then((response) => response.json()).then((h) => {
				if(!h || undefined !== h.length && !h.length){
					throw 'Could not load';
				}
				return h
			}).catch((e) => {
				this.$emit('research', JSON.parse(JSON.stringify(this.hash)), 'flight');
				console.error('Should research', JSON.parse(JSON.stringify(this.hash)), 'flight');
				console.error("Failed to fetch flight details", e);
				// Do nothing
			}).then(h=>{
				flight.Details = h;
				this.fetchDetails(flight);
			}).finally(() => {
				flight.loadingDetails = false;
				if(this.inspection?.checking)
					this.inspection.checking.offer = false;
			})
		},
		base_sort_price_asc(a, b){
			return a.Price - b.Price;
		},
		base_sort_price_desc(a, b){
			return -this.base_sort_price_asc(a,b);
		},
		base_sort_special(a, b){
			return (-(a._SpecialPercent - b._SpecialPercent)) || (a.Price - b.Price);
		},
		fetchDetails(flight, offer){
			
			var fetch_url = this.offer_details_url;
			if(!flight?.Details?.UpsellSupport) return;
			if(!fetch_url) return;
			if(offer){
				offer.loadingDetails = true;
			} else {
				flight.loadingDetails = true;
			}
			var data = {
				...this.search_data, 
				<?php if ($this->config->item('csrf_protection')){ ?>
				<?php echo json_encode($this->security->get_csrf_token_name()); ?>: <?php echo json_encode($this->security->get_csrf_hash()); ?>,
				<?php } ?>
				Code: this.inspection.code,
				ItineraryCode: this.flight.ItineraryCode,
				CombinationIndex: (this.flight.RealCombinations || this.flight.Combinations).indexOf(flight.Combination),
			};
			if(offer){
				data.OfferId = offer.Code;
			}
			console.warn(data);
			
			this.inspection.checking = this.inspection.checking || {};
			this.inspection.checking.upsells = true;
			fetch(fetch_url, {
				method: 'POST',
				headers: {
				  'Accept': 'application/json'
				},
				body: new URLSearchParams(objToSerialize(data))
			}).then((response) => {
				if (!response.ok) {
					if(response.status == 403){
						// CSRF
						window.location = window.location.href.replace(/#.*/, '');
						throw new Error("Network response failed. Redirecting to self", {cause: response });
					}
					throw new Error("Network response was not ok", {cause: response });
				}
				return response;
			}).then((response) => response.json()).then((h) => {
				if(!h || undefined !== h.length && !h.length){
					throw 'Could not load';
				}
				console.warn('upsell', h._embedded.upsell);
				flight.Ancilleries = {};
				flight.loadingUpsells = {};
				flight.Upsells = h._embedded.upsell;
				
				if(this.inspection.research_hash?.flight?.Upsell){
					var hu = JSON.stringify(this.inspection.research_hash?.flight?.Upsell?.BrandedFare);
					var u = flight.Upsells.find(u => JSON.stringify(u.FareDetails.BrandedFare) == hu);
					if(u){
						setTimeout(() => {
							this.selected_upsell = u.Code;
							console.error('SELECTED UPSELL', u.Code, this.flight, this);
						}, 0);
					} else {
						console.error('SELECTED UPSELL NOT FOUND', hu);
						console.warn('SETTING STEP', 2);
						this.data.step = this.data.step > 2 ? 2 : this.data.step;
					}
				}
				
				return;
			}).catch((e) => {
				this.$emit('research', JSON.parse(JSON.stringify(this.hash)), 'flight');
				console.error('Should research', JSON.parse(JSON.stringify(this.hash)), 'flight');
				console.error("Failed to fetch offer details", e);
				// Do nothing
			}).finally(() => {
				if(this.inspection?.checking)
				this.inspection.checking.upsells = false;
				if(offer){
					offer.loadingDetails = false;
				} else {
					flight.loadingDetails = false;
				}
			})
		},
	},
	template : `
	<v-container class="bg-background" v-if="flight" id="wrapper-oferta-zboruri-roundtrip">
		<div class="offer-above px-4">
		<v-breadcrumbs v-if="show_breadcrumbs" :items="breadcrumbs">
			<template v-slot:divider>
				<v-icon icon="mdi-menu-right"></v-icon>
			</template>
			<template v-slot:item="{ item }">
				<v-breadcrumbs-item href="javascript:void(0)" :active="item.active" active-color="green" :disabled="item.step == 2" @click.stop="$emit('set-value', {'step': item.step})" v-text="item.title"></v-breadcrumbs-item>
			</template>
		</v-breadcrumbs>
		</div>
		<FormLegend title="Zbor TUR"></FormLegend>
		<component v-if="flight" :is="loadViewAsync('partials/presearch-wrapper/functionalities/trip-avion-roundtrip/offer')" :show_breadcrumbs="false" :offer="flight" :inspection="inspection" :searching="searching" :prepend_breadcrumbs="breadcrumbs" v-on:hash="(h, t) => $emit('research-hash', h, t)" v-on:research="(h, t) => $emit('research', h, t)"
		 v-on:offer="(r) => r && setFlightOffer(r)"
		:results="[]" :applied_filters="undefined" v-on:set-value="(r) => ($emit('set-value', r))" :search_data="search_data" :set_checkout_component="set_checkout_component || checkout_component" :data="data" :search_wrapper_step="data.step"
		  v-on:assigned_passengers="(v) => (this.assigned_passengers = v)" :set_assigned_passengers="assigned_passengers"
		 :flight2="{...flight2_real?.ancilleryOrFlight, result: flight2_real?.result}" :flight2_inspection="flight2_inspection"
		 ></component>
		
		<FormLegend title="Zbor RETUR"></FormLegend>
		<component v-if="flight2" :is="loadViewAsync('partials/presearch-wrapper/functionalities/trip-avion-roundtrip/offer')" :show_breadcrumbs="false" :offer="flight2_real" :inspection="flight2_inspection" :searching="searching" :prepend_breadcrumbs="breadcrumbs" v-on:hash="(h, t) => $emit('research-hash', h, t)" v-on:research="(h, t) => $emit('research', h, 'flight2')"
		 v-on:offer="(r) => r && setFlightOffer(r)"
		:results="[]" :applied_filters="undefined" v-on:set-value="(r) => ($emit('set-value', r))" :search_data="search_data" :set_checkout_component="set_checkout_component || checkout_component" :data="data" :search_wrapper_step="data.step"
		 v-on:assigned_passengers="(v) => (this.assigned_passengers = v)" :set_assigned_passengers="assigned_passengers"
		:no_next="true"
		 ></component>
		
	</v-container>
	`,
}
