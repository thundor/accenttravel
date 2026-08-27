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
			fare_rules: undefined,
			fare_rules_show: {},
			prev_hash: undefined,
			result: undefined,
			ancillery: undefined,
			selected_upsell: undefined,
			chosen_package: undefined,
			details_package: undefined,
			applied_package: undefined,
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
	<v-container class="bg-background" v-if="flight" id="wrapper-oferta-zboruri">
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
		<div class="d-flex justify-space-between w-100 flight-details-wrapper flex-column">
			<div v-if="!ancilleryOrFlight || ancilleryOrFlight.loadingDetails" class="mb-3">
				<component :is="loadViewAsync('partials/presearch-wrapper/functionalities/common/loading')"></component>
			</div>
			
			<div v-if="ancilleryOrFlight" class="d-flex justify-space-between">
				<span v-text="ancilleryOrFlight.NonRefundable"></span>
				<span v-text="ancilleryOrFlight.FareDetails?.NonRefundable ? 'NonRefundable' : 'Refundable'"></span>
				<span v-text="(ancilleryOrFlight.FareDetails?.IsAutoTicketable === 'true' || true === ancilleryOrFlight.FareDetails?.IsAutoTicketable) ? 'Is Auto Ticketable' : ''"></span>
			</div>
			
			<v-list v-if="ancilleryOrFlight" class="w-100 flight-routes">
				<v-list-item v-for="(route, routeIndex) in ancilleryOrFlight.Routes||[]" class="bg-background rounded-theme mb-4 text-left">
				  <div class="d-flex w-100 mt-2 mb-4">
					<v-icon :icon="!routeIndex ? 'mdi-airplane-takeoff' : 'mdi-airplane-takeoff mdi-flip-h'" class="mr-4"></v-icon>
					<div class="flex-grow-1 d-flex justify-space-between flex-wrap">
					  <v-list-item-title>
					  <div v-if="route.Segment[0].Origin && route.Segment[route.Segment.length-1].Destination" class="text-wrap flight-oras-plecare-destinatie">
						<strong class="flight-oras oras-plecare" v-html="route.Segment[0].Origin.Airport.City"></strong>
						―
						<strong class="text-capitalize flight-stop" v-html="durationToFormatted(route.Duration)"></strong>
						→
						<strong class="flight-oras oras-destinatie" v-html="route.Segment[route.Segment.length-1].Destination.Airport.City"></strong>
					  </div>
					  </v-list-item-title>
					  <v-list-item-subtitle>
						<strong class="text-capitalize" v-html="route.Segment.length == 1 ? 'Direct' : (route.Segment.length == 2 ? '1 Escala' : (route.Segment.length -1) + ' Escale')"></strong>
					  </v-list-item-subtitle>
					</div>
				  </div>
				  <hr class="my-4" style="border-color: transparent;
				margin: 0 -15px;
				border-bottom-width: 0;
				height: 1px !important;
				box-shadow: 0px 0px 2px rgb(var(--v-theme-surface)) inset;
				border-left-width: 0;
				border-right-width: 0;"/>
				  <template v-for="segment in route.Segment">
					<div v-if="segment.Flight.StopTime" class="d-flex justify-space-between w-100 px-4 durata-escala">
					  Escala in {{ segment.Origin.Airport.City }} pentru {{ durationToFormatted(segment.Flight.StopTime) }}
					</div>
					<div class="d-flex justify-start align-center mb-4" style="gap:15px;">
					  <img v-if="company_images[segment.Carrier.Marketing.Code]" :src="company_images[segment.Carrier.Marketing.Code]" style="max-width: 50px;max-height: 100%;object-fit: contain;height: 40px;background: #fff;padding: 2px; flex:0;" />
					  <div class="d-flex flex-column" style="flex:50%;">
						<strong v-text="segment.Carrier.Marketing._"></strong>
						<small v-if="segment.Flight && segment.Flight.CabinType" class="">{{ segment.Flight.CabinType }}</small>
					  </div>
					  <div class="d-flex justify-end ga-2 ml-auto" style="flex:50%;">
						<strong v-if="segment.Aircraft" v-text="segment.Aircraft._"></strong>
						<div v-if="segment.Origin.Terminal || getObjectDotPathValue(segment,'Secured',false)">
						  <span v-if="segment.Origin.Terminal" class="">Terminal: {{ segment.Origin.Terminal }}</span>
						  <span v-if="getObjectDotPathValue(segment,'Secured',false)" title="Secured" class=""><v-icon icon="mdi-shield-airplane-outline"></v-icon></span>
						</div>
					  </div>
					</div>
					<?php /*
					<div class="d-flex justify-start align-center mb-2" style="gap:15px;">
					  <strong>Operator</strong>
					  <img v-if="company_images[segment.Carrier.Operating.Code]" :src="company_images[segment.Carrier.Operating.Code]" style="max-width: 50px;max-height: 100%;object-fit: contain;height: 30px;background: #fff;padding: 2px;" />
					  <strong v-text="segment.Carrier.Operating._"></strong>
					</div>
					*/ ?>
					
					<div class="d-flex flex-wrap line-before mb-4 justify-space-between">
					  <div class="d-flex flex-column mb-4">
						<span class="">{{ segment.Origin.Time.replace(/^([0-9]+:[0-9]+).*/,'$1') }} - {{ dateIntervalFormatted(segment.Origin.Date) }}</span>
						<span class="color-dark-light">{{ segment.Origin.Airport.Code }}, {{ segment.Origin.Airport._ }}, {{ segment.Origin.Airport.CityCode }}, {{ segment.Origin.Airport.City }}</span>
					  </div>
					  
						<div class="d-flex flex-column">
						<div class="my-auto duration-line" v-if="segment.Flight.Duration"><strong>{{ durationToFormatted(segment.Flight.Duration) }}</strong></div>
					  </div>
					  
					  <div class="d-flex flex-column">
						<span class="">{{ segment.Destination.Time.replace(/^([0-9]+:[0-9]+).*/,'$1') }} - {{ dateIntervalFormatted(segment.Destination.Date) }}</span>
						<span class="color-dark-light">{{ segment.Destination.Airport.Code }}, {{ segment.Destination.Airport._ }}, {{ segment.Destination.Airport.City }}</span>
					  </div>
					</div>
				  </template>
				  
					<small class="d-flex flex-column" v-if="ancilleryOrFlight.FareDetails && ancilleryOrFlight.FareDetails.BrandedFare && ancilleryOrFlight.FareDetails.BrandedFare.BrandDetails && ancilleryOrFlight.FareDetails.BrandedFare.BrandDetails[routeIndex]">
						<span v-if="ancilleryOrFlight.FareDetails.BrandedFare.BrandDetails[routeIndex].Cabin"><b>Clasa:</b> {{ ancilleryOrFlight.FareDetails.BrandedFare.BrandDetails[routeIndex].Cabin }}</span>
						<span v-if="ancilleryOrFlight.FareDetails.BrandedFare.BrandDetails[routeIndex].Code"><b>Fare Family:</b> {{ ancilleryOrFlight.FareDetails.BrandedFare.BrandDetails[routeIndex].Code }}</span>
						<span v-if="ancilleryOrFlight.FareDetails.BrandedFare.BrandDetails[routeIndex].Description">{{ ancilleryOrFlight.FareDetails.BrandedFare.BrandDetails[routeIndex].Description }}</span>
						<span v-if="ancilleryOrFlight.FareDetails.BrandedFare.BrandDetails[routeIndex].Name">{{ ancilleryOrFlight.FareDetails.BrandedFare.BrandDetails[routeIndex].Name }}</span>
					</small>
				</v-list-item>
			  </v-list>
			  
			<template v-if="flight.Upsells && flight.Upsells.length">
				<component :is="loadViewAsync('partials/presearch-wrapper/functionalities/<?php echo basename(dirname($a)); ?>/flight-upsell')"
					 v-model="selected_upsell" :upsells="flight.Upsells" :flight_data="flight" :loading="flight.loadingUpsells" ></component>
			</template>
			  
			<component v-if="ancilleryOrFlight" :is="loadViewAsync('partials/presearch-wrapper/functionalities/<?php echo basename(dirname($a)); ?>/flight_passenger_details')" ref="passenger_details" :passport_required="passport_required" :flight_data="ancilleryOrFlight" :data="data" :inspection="inspection" v-on:result="(r) => (this.result = r)" v-on:save="(chosen_index = (chosen_index || 0)+1, console.warn('setting step', 3), $.emit('set-value', {step: 3}))" v-on:research="(h, t) => $emit('research', h, t)"></component>
			
			<template v-if="fare_rules && fare_rules.basises_arr">
				<div class="bg-background pa-4 rounded-theme mb-4">
				
				<div v-if="fare_rules.general_categories" class="text-left">
					<FormLegend title="Reguli si informatii zbor general valabile"></FormLegend>
				  <div class="bg-background rounded-theme py-2">
				  <div v-for="(category, title) in fare_rules.general_categories">
					<div class="open-fare-detail" v-text="capitalizeWords(title, true)" @click="fare_rules_show[('general-' + '-' + title)] = !fare_rules_show[('general-' + '-' + title)]"></div>
					<div style="white-space: pre;overflow-x:auto;font-size:9px;" touchless class="pl-0" v-show="fare_rules_show[('general-' + '-' + title)]">
					<template v-if="category.Url">
					  <a :href="category.Url" target="_BLANK" v-text="fixCategory(category._)"></a>
					</template>
					<template v-else>
					  <span v-text="fixCategory(category._)"></span>
					</template>
					</div>
				  </div>
				  </div>
				</div>
				<div v-if="fare_rules.basises_arr.filter(basis => fare_rules.particular_per_basis[basis] || fare_rules.general_per_basis[basis]).length" class="text-left">
					<FormLegend title="Reguli si informatii zbor particulare"></FormLegend>
				  <template v-for="basis in fare_rules.basises_arr">
					<div v-if="fare_rules.particular_per_basis[basis] || fare_rules.general_per_basis[basis]">
					<h6 class="open-fare-detail" v-text="basisDescription(basis)" @click="fare_rules_show[('basis-' + basis)] = !fare_rules_show[('basis-' + basis)]"></h6>
					<div v-show="fare_rules_show[('basis-' + basis)]" class="bg-background rounded-theme py-2">
					<template v-if="fare_rules.general_per_basis[basis]">
					<div v-for="(category, title) in fare_rules.general_per_basis[basis]">
					  <div class="open-fare-detail" v-text="capitalizeWords(title, true)" @click="fare_rules_show[('basis-' + basis + '-' + title)] = !fare_rules_show[('basis-' + basis + '-' + title)]"></div>
					  <div style="white-space: pre;overflow-x:auto;font-size:9px;" touchless class="pl-0" v-show="fare_rules_show[('basis-' + basis + '-' + title)]">
					  <template v-if="category.Url">
						<a :href="category.Url" target="_BLANK" v-text="fixCategory(category._)"></a>
					  </template>
					  <template v-else>
						<span v-text="fixCategory(category._)"></span>
					  </template>
					  </div>
					</div>
					</template>
					<template v-if="fare_rules.particular_per_basis[basis]">
					<div v-for="(categories, ptc) in fare_rules.particular_per_basis[basis]">
					  <strong class="open-fare-detail" v-text="general_translate_ptc[ptc] && general_translate_ptc[ptc][0] || ptc" @click="fare_rules_show[('basis-' + ptc + '-' + basis)] = !fare_rules_show[('basis-' + ptc + '-' + basis)]"></strong>
					  <div v-show="fare_rules_show[('basis-' + basis)]" class="bg-background rounded-theme py-2">
					  <div v-for="(category, title) in categories">
						<div class="open-fare-detail" v-text="capitalizeWords(title, true)" @click="fare_rules_show[('basis-' + ptc + '-' + basis + '-' + '-' + title)] = !fare_rules_show[('basis-' + ptc + '-' + basis + '-' + '-' + title)]"></div>
						<div style="white-space: pre;overflow-x:auto;font-size:9px;" touchless class="pl-0" v-show="fare_rules_show[('basis-' + ptc + '-' + basis + '-' + '-' + title)]">
						<template v-if="category.Url">
						  <a :href="category.Url" target="_BLANK" v-text="category._"></a>
						</template>
						<template v-else>
						  <span v-text="fixCategory(category._)"></span>
						</template>
						</div>
					  </div>
					  </div>
					</div>
					</template>
					
					</div>
					</div>
				  </template>
				</div>
				</div>
			  </template>
			
			<v-btn @click="$emit('research', JSON.parse(JSON.stringify(hash)), 'flight')">Refresh</v-btn>
			
			<?php /* HASH:
			<pre v-text="JSON.stringify(hash, null, 2)"></pre> */ ?>

			<teleport to="#pos-stick-b-t" v-if="(2 == search_wrapper_step)">
				<div class="d-flex w-100 justify-space-between py-1"  v-if="ancilleryOrFlight && (!flight.loadingUpsells || !Object.values(flight.loadingUpsells).length)" v-for="flight in [{...ancilleryOrFlight, result:(result || {})}]">
					<v-dialog>
						<template v-slot:activator="{ props }">
						  <v-btn v-bind="props" class="buton-sumar" variant="outlined" size="large">Vezi detalii</v-btn>
						</template>
						<template v-slot:default="{ isActive }">
							<v-card class="align-self-center" style="max-width: min(95vw, 630px);width:630px">
								<v-card-title>Sumar plata</v-card-title>
								<v-card-text class="max-height overflow-y-auto">
								<template v-if="hotel">
									<div class="d-flex w-100 justify-space-between mb-2"><span>Cazare</span><strong>{{ format_price_obj_amount_currency((hotel && hotel.Package && hotel.Package.PackageRooms.PackageRoom || []).reduce((c, packageRoom, packageRoom_index) => (c.Amount+=((packageRoom.RoomRefs.RoomRef.filter(roomRef => (!hotel.Package.SelectedRooms ? roomRef.Selected : (packageRoom.PackageRoomCode + ':' + roomRef.RoomCode == hotel.Package.SelectedRooms[packageRoom_index]))).reduce((a, r) => a + ((c.Currency = r.Price.Currency), (r.Price||{}).Amount || 0),0)) || (hotel.Package.Price || {}).Amount && (c.Currency = hotel.Package.Price.Currency, hotel.Package.Price.Amount) || 0), c),{Amount: 0, Currency: 'RON'})) }}</strong></div>
								</template>
					<div class="text-h6">Clasa zbor: {{ (getObjectDotPathValue(flight, 'FareDetails.BrandedFare.BrandDetails.*.Name') || []).filter((n, i, arr) => i == arr.indexOf(n)).join(', ') }}</div>
					
						<div class="d-flex w-100 justify-space-between mb-2"><span>Bilete</span><strong>{{ format_price((getObjectDotPathValue(flight,'FareDetails.FullFare',0)), getObjectDotPathValue(flight,'Currency')) }}</strong></div>
						<div v-for="pf in (getObjectDotPathValue(flight,'FareDetails.PaxFare') || [])" class="d-flex w-100 justify-space-between"><small>{{ translate_ptc[pf.PTC][1] }} x {{ pf.Count }}</small><small>{{ format_price((getObjectDotPathValue(pf,'FullFare',0) * pf.Count), getObjectDotPathValue(flight,'Currency')) }}</small></div>
						<template v-if="(flight.result.paidOptions || []).length">
							<hr class="my-4" />
							<div  class="d-flex w-100 justify-space-between mb-2"><span>Servicii Extra</span><strong>{{ format_price((getObjectDotPathValue(flight.result,'optionsPrice',0)), getObjectDotPathValue(flight,'Currency')) }}</strong></div>
							<div class="d-flex w-100 justify-space-between mb-2" v-for="option in flight.result.paidOptions">
								<div>
									<div>{{ translate_ptc[option.Target][0] }} #{{ (parseInt(option.PassengerIndex) + 1) }} {{ getObjectDotPathValue(option,'Service.Name') }} ({{ getObjectDotPathValue(option,'Service.CategoryName') }}): <b>{{ getObjectDotPathValue(option,'Option.Description.0') }}</b> ({{ getObjectDotPathValue(option,'Route.From') + '-' + getObjectDotPathValue(option,'Route.To') }})</div>
									<div>{{ [...(option.Description || [])].join('; ') }}</div>
								</div>
								<small>{{ format_price(getObjectDotPathValue(option,'Option.Price.Amount',0), getObjectDotPathValue(option,'Option.Price.Currency')) }}</small></div>
						</template>
						<template v-if="(flight.result.paidSeats || []).length">
							<hr class="my-4" />
							<div class="d-flex w-100 justify-space-between mb-2"><span>Locuri preferentiale</span><strong>{{ format_price((getObjectDotPathValue(flight.result,'seatsPrice',0)), getObjectDotPathValue(flight,'Currency')) }}</strong></div>
							<div class="d-flex w-100 justify-space-between mb-2" v-for="paidSeat in flight.result.paidSeats">
								<div>{{ translate_ptc[paidSeat.Target][0] }} #{{ (parseInt(paidSeat.PassengerIndex) + 1) }} Loc: {{ getObjectDotPathValue(paidSeat,'seatNumber') }}{{ getObjectDotPathValue(paidSeat,'seatColumn') }} Ruta: {{ getObjectDotPathValue(paidSeat,'Route.From') + '-' + getObjectDotPathValue(paidSeat,'Route.To') }}
								</div>
								<small>{{ format_price(getObjectDotPathValue(paidSeat,'amount',0), getObjectDotPathValue(paidSeat,'currency')) }}</small></div>
						</template>
						<hr class="my-4" />
						<div class="d-flex w-100 justify-space-between mb-2"><span>Taxa de serviciu</span><strong>{{ format_price((getObjectDotPathValue(flight,'FareDetails.ServiceFee',0)), getObjectDotPathValue(flight,'FareDetails.Currency')) }}</strong></div>
						<div v-for="pf in (getObjectDotPathValue(flight,'FareDetails.PaxFare') || [])" class="d-flex w-100 justify-space-between"><small>{{ translate_ptc[pf.PTC][1] }} x {{ pf.Count }}</small><small>{{ format_price((getObjectDotPathValue(pf,'ServiceFee',0) * pf.Count), getObjectDotPathValue(flight,'Currency')) }}</small></div>

								</v-card-text>
								<v-card-actions>
									<v-spacer></v-spacer>
									<v-btn @click="isActive.value = false" class="text-none" size="large" variant="outlined" rounded="theme">Inchide</v-btn>
								</v-card-actions>
							</v-card>
						</template>
					</v-dialog>
					<div class="d-none d-md-flex flex-column ga-1">
						<div class="d-flex w-100 justify-space-between ga-2"><span>Bilete</span><strong>{{ format_price((getObjectDotPathValue(flight,'FareDetails.FullFare',0)), getObjectDotPathValue(flight,'Currency'), 1) }}</strong></div>
						<div class="d-flex w-100 justify-space-between ga-2"><span>Taxa de serviciu</span><strong>{{ format_price((getObjectDotPathValue(flight,'FareDetails.ServiceFee',0)), getObjectDotPathValue(flight,'FareDetails.Currency'), 1) }}</strong></div>
					</div>
					<div class="d-none d-md-flex flex-column ga-1">
						<div class="d-flex w-100 justify-space-between ga-2"><span>Locuri preferentiale</span><strong>{{ format_price((getObjectDotPathValue(flight.result,'seatsPrice',0)), getObjectDotPathValue(flight,'Currency'), 1) }}</strong></div>
						<div class="d-flex w-100 justify-space-between ga-2"><span>Servicii Extra</span><strong>{{ format_price((getObjectDotPathValue(flight.result,'optionsPrice',0)), getObjectDotPathValue(flight,'Currency'), 1) }}</strong></div>
					</div>	
					<div v-if="hotel" class="d-none d-md-flex flex-column ga-1">
						<div class="d-flex w-100 justify-space-between ga-2"><span>Cazare</span><strong>{{ format_price_obj_amount_currency((hotel && hotel.Package && hotel.Package.PackageRooms.PackageRoom || []).reduce((c, packageRoom, packageRoom_index) => (c.Amount+=((packageRoom.RoomRefs.RoomRef.filter(roomRef => (!hotel.Package.SelectedRooms ? roomRef.Selected : (packageRoom.PackageRoomCode + ':' + roomRef.RoomCode == hotel.Package.SelectedRooms[packageRoom_index]))).reduce((a, r) => a + ((c.Currency = r.Price.Currency), (r.Price||{}).Amount || 0),0)) || (hotel.Package.Price || {}).Amount && (c.Currency = hotel.Package.Price.Currency, hotel.Package.Price.Amount) || 0), c),{Amount: 0, Currency: 'RON'})) }}</strong></div>
					</div>
					
					<v-btn
						:disabled="!ancilleryOrFlight || ancilleryOrFlight.loadingDetails"
						class="button-alege"
						@click="$refs.passenger_details.validateAndSave()"
						size="large"
						variant="outlined"
					>
					<span>Finalizare </span> 
					<span v-if="result" v-text="'(' + format_price_obj_amount_currency((hotel && hotel.Package && hotel.Package.PackageRooms.PackageRoom || []).reduce((c, packageRoom, packageRoom_index) => (c.Amount+=((packageRoom.RoomRefs.RoomRef.filter(roomRef => (!hotel.Package.SelectedRooms ? roomRef.Selected : (packageRoom.PackageRoomCode + ':' + roomRef.RoomCode == hotel.Package.SelectedRooms[packageRoom_index]))).reduce((a, r) => a + ((c.Currency = r.Price.Currency), (r.Price||{}).Amount || 0),0)) || (hotel.Package.Price || {}).Amount && (c.Currency = hotel.Package.Price.Currency, hotel.Package.Price.Amount) || 0), c),{Amount: result.totalPrice, Currency: flight.Currency})) + ')'"></span>
					</v-btn>
				</div>
				<div v-else>
					<component :is="loadViewAsync('partials/presearch-wrapper/functionalities/common/loading')"></component>
				</div>
			</teleport>
		</div>
		<teleport to="#search-wrapper-item-checkout" v-if="result && undefined !== chosen_index && ancilleryOrFlight">
			<component :is="loadViewAsync(set_checkout_component || checkout_component)" :search_data="search_data" :result="{Flight: ancilleryOrFlight && {...ancilleryOrFlight, result:result} || undefined, Flights: inspection, Hotel: hotel, Hotels: hotel_inspection}" :prepend_breadcrumbs="breadcrumbs" :data="data" :search_wrapper_step="search_wrapper_step" v-on:set-value="(v) => $emit('set-value', v)" v-on:research="(h, t) => $emit('research', h, t)"></component>
		</teleport>
	</v-container>
	`,
}
