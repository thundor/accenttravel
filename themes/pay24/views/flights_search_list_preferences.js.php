export default {
  emits: ['save'],
  props: {
      modelValue: {
          type: Object,
          default: () => ({}),
      },
      calendar_location_mounded: {
          type: Boolean,
          default: () => (false),
      },
      search_data: {
          type: Object,
          default: () => ({}),
      },
      filters: {
          type: Object,
          default: () => (undefined),
      },
      flights: {
          type: Array,
          default: () => ([]),
      },
      single: {
          type: Boolean,
          default: false,
      },
  },
	data: () => ({
    companies: [],
    iis: [],
    mover: 0,
    step: 1,
    markers: [],
    calendar_prices: {
      1: true,
      2: true,
      3: true,
    },
    saved: {
      sortPrice: '0',
      direct: '0',
      priceMinMax: [],
      durationMinMax: [],
      stops: [],
      airlines: [],
    },
    multicalendars: false,
    sortPrice: '0',
    direct: '0',
    priceMinMax: [],
    priceMinMax2: {min:0,max:0},
    durationMinMax: [],
    durationMinMax2: {min:0,max:0},
    stops: [],
    stops2: {min:0,max:0},
    airlines: {},
    min_price: 0,
    max_price: 500,
    min_duration: 0,
    max_duration: 48 * 60,
    min_stops: 0,
    max_stops: 10,
    swipe: '',
    date: undefined,
    min_date: undefined,
    max_date: undefined,
    year_range: [],
    kept: undefined,
    ddate: [],
    dialog: false,
    errors: [],
    texts: Object.freeze({
      // no_end_date: "Alegeti data de intoarcere",
    }),
    validations:Object.freeze([
      // function(){ return !this.single && !this.saved.days ? 'no_end_date' : null },
    ]),
  }),
	template : `
<Modal v-model="dialog" v-if="filters">
  <template v-slot:activator="{ props }">
    <div class="d-flex flex-wrap mb-4 justify-space-between v-theme--dark" style="gap:10px">
    <v-btn v-if="min_price < max_price -1" :color="('0' !== saved.sortPrice || (saved.priceMinMax.length && !(saved.priceMinMax[0] == min_price && saved.priceMinMax[1] == max_price) )) ? 'primary' : 'default'" v-bind="props" class="flex-grow-1 justify-space-between d-inline-flex py-2 px-1 bg-background text-capitalize text-caption btn-preference" append-icon="mdi-chevron-down" variant="flat" @click="step=1">
      Pret
    </v-btn>
    <v-btn :color="(saved.durationMinMax.length && !(saved.durationMinMax[0] == min_duration && saved.durationMinMax[1] == max_duration) ) ? 'primary' : 'default'" v-bind="props" class="flex-grow-1 justify-space-between d-inline-flex py-2 px-1 bg-background text-capitalize text-caption btn-preference" append-icon="mdi-chevron-down" variant="flat" @click="step=2">
      Durata
    </v-btn>
    <v-btn v-if="!search_data.direct && max_stops > 0 && min_stops != max_stops" :color="(saved.stops.length && !(saved.stops[0] == saved.min_stops && saved.stops[1] == max_stops) ) ? 'primary' : 'default'" v-bind="props" class="flex-grow-1 justify-space-between d-inline-flex py-2 px-1 bg-background text-capitalize text-caption btn-preference" append-icon="mdi-chevron-down" variant="flat" @click="step=3">
      Escale
    </v-btn>
    <v-btn v-if="companies && companies.length>1" :color="(Object.keys(saved.airlines).length) ? 'primary' : 'default'" v-bind="props" class="flex-grow-1 justify-space-between d-inline-flex py-2 px-1 bg-background text-capitalize text-caption btn-preference" append-icon="mdi-chevron-down" variant="flat" @click="step=4">
      Companii
    </v-btn>
    <Teleport v-if="calendar_location_mounded" to="#calendar-tarife-wrapper">
      <v-btn v-bind="props" class="rounded-theme square-but-inp justify-center d-inline-flex py-2 px-1 bg-background text-capitalize text-caption ml-4 square-but-inp rounded-theme" :color="this.date && this.date.length ? 'primary' : 'default'" variant="flat" @click="step=5">
        <v-icon icon="mdi-calendar" size="x-large"></v-icon>
      </v-btn>
    </Teleport>
    </div>
  </template>
    <v-window v-model="step" class="w-100 d-table" :touch="false">
      <v-window-item :value="1" class="">
        <v-list lines="two" subheader theme="light" class="ma-4 mt-0 max-height pt-4" rounded="theme">
          <h3 class="text-h6 font-weight-light mb-2 ps-4" :touch="{left: allowTouchLeft, right: allowTouchRight}">
            Pret
          </h3>
          <v-radio-group inline color="primary" v-model="sortPrice" class="v-theme--dark ms-2 mt-2 mb-3" hide-details="true">
            <v-radio label="Crescator" value="0"></v-radio>
            <v-radio label="Descrescator" value="1" class="ms-5"></v-radio>
          </v-radio-group>
          <h6 class="ps-4"><span v-html="priceMinMax[0] || min_price"></span> - <span v-html="priceMinMax[1] || max_price"></span> EUR</h6>
          <div class="v-theme--dark px-4">
          <q-range
            v-model="priceMinMax2"
            color="primary"
            :min="min_price"
            :max="max_price"
            :step="1"
          />
</div>
        </v-list>
        <div class="mt-auto justify-center d-flex pa-4 pt-0 pb-0" style="gap:15px;">
        <v-btn class="d-flex text-capitalize font-weight-normal" size="x-large" style="flex:1;" :color="true ? 'primary' : 'secondary'" rounded="theme" @click="validate() ? save() && (dialog = false) : null" v-html="'Confirma'"></v-btn>
      </div>
      </v-window-item>

      <v-window-item :value="2">
        <v-list lines="two" subheader theme="light" class="ma-4 mt-0 max-height pt-4" rounded="theme">
            <h3 class="text-h6 font-weight-light mb-2 ps-4" :touch="{left: allowTouchLeft, right: allowTouchRight}">
              Durata maxima per tur<br/><small>(inclusiv escale)</small>
            </h3>
            <h6 class="ps-4"><span v-html="minutesToFormattedDuration(durationMinMax[0] || min_duration)"></span> - <span v-html="minutesToFormattedDuration(durationMinMax[1] || max_duration)"></span></h6>
            <?php /*<v-range-slider
              class="v-theme--dark mx-4 px-3 mt-4"
              color="primary"
              v-model="durationMinMax"
              :min="min_duration"
              :max="max_duration"
              step="60"
            ></v-range-slider> */ ?>
            <div class="v-theme--dark px-4">
                <q-slider
                      v-model="durationMinMax2.max"
                      color="primary"
                      :min="min_duration"
                      :max="max_duration"
                      :step="60"
                    />
            </div>
          </v-list>
          <div class="mt-auto justify-center d-flex pa-4 pt-0 pb-0" style="gap:15px;">
          <v-btn class="d-flex text-capitalize font-weight-normal" size="x-large" style="flex:1;" :color="true ? 'primary' : 'secondary'" rounded="theme" @click="validate() ? save() && (dialog = false) : null" v-html="'Confirma'"></v-btn>
        </div>
      </v-window-item>

      <v-window-item :value="3">
        <v-list lines="two" subheader theme="light" class="ma-4 mt-0 max-height pt-4" rounded="theme">
          <h3 class="text-h6 font-weight-light mb-2 ps-4" :touch="{left: allowTouchLeft, right: allowTouchRight}">
            Escale
          </h3>
          <v-radio-group inline color="primary" v-model="direct" class="v-theme--dark ms-2 mt-2 mb-3" hide-details="true">
            <v-radio label="Zbor Direct" value="1"></v-radio>
            <v-radio label="Inclusiv Escale" value="0" class="ms-5"></v-radio>
          </v-radio-group>
          <h6 class="ps-4"><span v-html="stops[0] || min_stops"></span> - <span v-html="stops[1] || max_stops"></span> Escale</h6>
          <?php /* <v-range-slider
            :disabled="'0' !== direct"
            class="v-theme--dark mx-4 px-3 mt-4"
            color="primary"
            v-model="stops"
            :min="min_stops"
            :max="max_stops"
            step="1"
          ></v-range-slider> */ ?>
          <div class="v-theme--dark px-4">
                <q-range
                  :disable="'0' !== direct"
                      v-model="stops2"
                      color="primary"
                      :min="min_stops"
                      :max="max_stops"
                      :step="1"
                    />
            </div>
        </v-list>
        <div class="mt-auto justify-center d-flex pa-4 pt-0 pb-0" style="gap:15px;">
        <v-btn class="d-flex text-capitalize font-weight-normal" size="x-large" style="flex:1;" :color="true ? 'primary' : 'secondary'" rounded="theme" @click="validate() ? save() && (dialog = false) : null" v-html="'Confirma'"></v-btn>
      </div>
      </v-window-item>
      <v-window-item :value="4">
      <v-list lines="two" subheader theme="light" class="ma-4 mt-0 max-height pt-4" rounded="theme">
          <h3 class="text-h6 font-weight-light mb-2 ps-3" :touch="{left: allowTouchLeft, right: allowTouchRight}">
            Companii aeriene
          </h3>
          <div class="ps-4 v-theme--dark">
            <v-checkbox color='primary' :label="company.name + ' (' + company.code + ')'" hide-details density="compact" v-model="airlines[company.code]" v-for="(company) in companies"></v-checkbox>
          </div>
        </v-list>
        <div class="mt-auto justify-center d-flex pa-4 pt-0 pb-0" style="gap:15px;">
        <v-btn class="d-flex text-capitalize font-weight-normal" size="x-large" style="flex:1;" :color="true ? 'primary' : 'secondary'" rounded="theme" @click="validate() ? save() && (dialog = false) : null" v-html="'Confirma'"></v-btn>
      </div>
      </v-window-item>
      <v-window-item :value="5">
        <v-list lines="two" subheader theme="light" class="ma-4 mt-0 max-height pt-4" rounded="theme">
          <h3 class="text-h6 font-weight-light mb-2 px-4 d-flex justify-space-between" :touch="{left: allowTouchLeft, right: allowTouchRight}">
            <span>Calendar tarife</span>
            <div class="euros d-flex">
              <v-checkbox v-for="i in iis"
                hide-details
                inline
                density="compact"
                class="d-inline-flex"
                v-model="calendar_prices[i]"
                :color="i==1?'green':(i==2 ? 'yellow' : 'red')"
                :class="{['text-' + (i==1?'green':(i==2 ? 'yellow' : 'red'))]:true}"
                :label="'€'.repeat(i)"
                :value="true"
              ></v-checkbox>
            </div>
          </h3>
          <Datepicker ref="calendar" :start-date="min_date" :markers="shown_markers" :class="{'ranged-picker': true, 'range-picked': true}" :min-date="min_date" :max-date="max_date" :model-value="date" @internal-model-change="changed" v-model="date" @update:model-value="changed2" no-disabled-range :year-range="year_range" month-name-format="long" locale="ro" inline :preview-format="a => formatDates(a)" :range="true" :multi-calendars="multicalendars" calendar-class-name="v-theme--dark" month-change-on-scroll class="px-4 w-100 euro-calendar" :enable-time-picker="false">
            <template #action-select>
            </template>
          </Datepicker>
        </v-list>
        <div class="mt-auto justify-center d-flex pa-4 pt-0 pb-0" style="gap:15px;">
        <v-btn class="d-flex text-capitalize font-weight-normal" size="x-large" style="flex:1;" :color="true ? 'primary' : 'secondary'" rounded="theme" @click="validate() ? save() && (dialog = false) : null" v-html="'Confirma'"></v-btn>
      </div>
      </v-window-item>
    </v-window>
</Modal>
	`,

  methods: {
    formatDates(a){
      return !a || !a.length ? undefined : this.formatDate(a[0]) + (!a[1] ? '' :  ' - ' + this.formatDate(a[1]) );
    },
    formatDate(a){
      return a.toLocaleDateString('ro', {
				year: "numeric",
				month: "long",
				day: "numeric" 
      });
    },
    changed2(a){
      console.log('date', a);
      // this.date = a;
      return true;
    },
    changed(a){
      console.log('date2', a);
      this.ddate = a;
      return true;
    },
		allowTouchRight () {
			if(this.step > 1){
				this.step --;
			}
		},
		allowTouchLeft () {
			if(this.step < 5){
				this.step ++;
			}
			return false;
		},
    reset(){
    },
    clearValidations(){
      this.errors = [];
    },
    save(){
      var company_codes = [];
      for(var company_code in this.airlines){
        if(this.airlines[company_code]){
          company_codes.push(company_code);
        }
      }
      var airlines = company_codes.reduce((o,v) => (o[v] = 1, o), {});
      this.saved = Object.freeze({
        sortPrice: this.sortPrice,
        direct: this.direct,
        priceMinMax: [...this.priceMinMax],
        durationMinMax: [...this.durationMinMax],
        stops: [...this.stops],
        airlines: airlines
      });
      
      // this.kept = Object.assign({}, this.saved);
      var r = {before:[],flights:[],route:[],routes:[]};
      
      if(this.saved.direct == '1'){
        r.route.push(route => !getObjectDotPathValue(route, 'Segment.1'));
      } else if(this.saved.stops.length > 0){
        if(this.saved.stops[1] == this.saved.stops[0]){
          r.route.push(route => !!getObjectDotPathValue(route, 'Segment.' + (this.saved.stops[0])) && !getObjectDotPathValue(route, 'Segment.' + (parseInt(this.saved.stops[0]) + 1)))
        } else {
          if(this.saved.stops[1]){
            r.route.push(route => !getObjectDotPathValue(route, 'Segment.' + (parseInt(this.saved.stops[1]) + 1)));
          }
          if(this.saved.stops[0]){
            r.route.push(route => !!getObjectDotPathValue(route, 'Segment.' + (this.saved.stops[0])));
          }
        }
        /* r.flights.push(v => {
          var d = getObjectDotPathValue(v, 'Routes.*.Route.*.Segment') || [];
          if(this.saved.stops[1] == this.saved.stops[0]){
            return getObjectDotPathValue(v, 'Routes.*.Route.*.Segment.' + (this.saved.stops[0] -1)).length != d.length 
            || getObjectDotPathValue(d, '' + (this.saved.stops[0]-1));
          }
          if(this.saved.stops[1] && getObjectDotPathValue(v, 'Routes.*.Route.*.Segment.' + this.saved.stops[1])){
            return false;
          }
          if(this.saved.stops[0] && getObjectDotPathValue(v, 'Routes.*.Route.*.Segment.' + this.saved.stops[0]).length){
            return false;
          }
          return true;
        }); */
      }
      if(this.saved.durationMinMax.length && ((this.saved.durationMinMax[0] && this.saved.durationMinMax[0] != this.min_duration) || (this.saved.durationMinMax[1] && this.saved.durationMinMax[1] != this.max_duration))){
        var min_duration = this.saved.durationMinMax[0] && this.saved.durationMinMax[0] != this.min_duration && this.minutesToStandardDuration(this.saved.durationMinMax[0]);
        var max_duration = this.saved.durationMinMax[1] && this.saved.durationMinMax[1] != this.max_duration && this.minutesToStandardDuration(this.saved.durationMinMax[1]);
        r.route.push(route => {
          var a = route.Duration;
          return !((min_duration && a < min_duration) || (max_duration && a>max_duration))
        });
      }
		r.before.push(flights => flights.filter((v) => {
			var a = getObjectDotPathValue(v,'BrandedFare.BrandDetails.0.Cabin');
			// console.warn(a,v);
			var economy = /economy/i.test(a);
			var business = /business/i.test(a);
			var premium = /premium/i.test(a);
			
			if(this.search_data.cabine == '3'){
				return business;
			} else if(this.search_data.cabine == '4'){
				return premium;
			}
			return true;
			// return !(business || premium);
        }));
	  
      if(this.saved.priceMinMax.length && ((this.saved.priceMinMax[0] && this.saved.priceMinMax[0] != this.min_price) || (this.saved.priceMinMax[1] && this.saved.priceMinMax[1] != this.max_price))){
        var min_price = this.saved.priceMinMax[0] && this.saved.priceMinMax[0] != this.saved.min_price && this.saved.priceMinMax[0];
        var max_price = this.saved.priceMinMax[1] && this.saved.priceMinMax[1] != this.saved.max_price && this.saved.priceMinMax[1];
        r.before.push(flights => flights.filter((v) => {
          var price = getObjectDotPathValue(v, 'PriceDetail.Amount');
          return !((min_price && price < min_price) || (max_price && price>max_price))
        }));
      }
      if(company_codes.length){
        r.routes.push(route => {
          var codes = getObjectDotPathValue(route, '*.Segment.*.Carrier.Marketing.Code').flat(1);
          return codes.find((a) => !!this.saved.airlines[a]);
        });
      }
      this.date = this.ddate;
      if(this.ddate && this.ddate.length){
        var ds = this.ddate.map(v => v ? v.toISOString().split('T')[0] : '');

        console.warn('ddates',this.ddate, ds);
        r.routes.push(route => {
          var d = getObjectDotPathValue(route, '0.Segment.0.Origin.Date');
          var ld = ds[ds.length-1] || ds[0];
          return d >= ds[0] && d <= ld
        });
      }
      if(this.saved.sortPrice == '1'){
        r.before.push(flights => flights.slice().reverse());
      }
console.warn('flight_filters', r, this.saved, this);
      this.$emit('save', r);
      return true;
      // emit
    },
    minutesToStandardDuration(minutes){
      var m = minutes % 60;
      var h = (minutes - m) / 60;
      return (h < 10 ? "0" : "") + h.toString() + ":" + (m < 10 ? "0" : "") + m.toString()
    },
    validate(){
      this.clearValidations();
      this.validations.every(f => {
        var v = f.bind(this)();
        v && this.errors.push(this.texts[v]);
        return !v;
      })
      return !this.errors.length;
    }
  },
  computed: {
    shown_markers:{
      get() { 
        return !this.markers || !this.markers.length ? [] : this.markers.filter(v => !!this.calendar_prices[v.i]).map((v) => { 
          var i = v.i;
          return {
            date: v.date,
            type: 'line',
            color: i==1?'green':(i==2 ? 'yellow' : 'red'),
            // tooltip: [{ text: '€'.repeat(i), color: i==1?'green':(i==2 ? 'yellow' : 'red') }],
          }
        })
      }
    },
    selectedDatesAreValid:{
      get() { return this.ddate ? (this.single || this.ddate.length==2) : false; },
    },
    button_text:{
      get() { 
        if(this.kept){
          var txts = [];

          if(this.kept.date){
            txts.push(this.kept.date.toLocaleDateString('ro', {
              weekday: "short",
              year: "numeric",
              month: "short",
              day: "numeric"
            }));
          }
          if(!this.single && this.kept.days){
            txts.push(this.kept.days);
          }
          if(txts.length) return txts.join(' + ');
        }
        return !this.single ? 'Plecare - Sosire' : 'Plecare';
      },
    },
  },
	beforeCreate: function(){
		// console.warn('created', this);
	},
  watch:{
    'dialog': {
      handler(newValue, oldValue){
        /* if(newValue && !oldValue){
        } */
      },
    },
    'modelValue': {
      handler(newValue, oldValue){
        // console.warn('date', newValue);
        if(!newValue) return;
        
        this.validate() && this.save()
      }
    },
    'flights': {
      handler(newValue, oldValue){
        console.warn('myflights', newValue)
        if(undefined == this.min_date){

          var m = [];
          this.date = undefined;
          this.multicalendars = false;
          this.min_date = undefined;
          this.max_date = undefined;
          this.year_range = [];
          this.iis = [];
          if(newValue && newValue.length){
            var dates = [... new Set((getObjectDotPathValue(newValue,'*.Combination.0.Segment.0.Origin.Date') || []).filter(v => !!v))];
            dates.sort();
            // var prices = [... new Set((getObjectDotPathValue(newValue,'*.Flight.PriceDetail.Amount') || []).filter(v => !!v))];
            // prices.sort((a,b)=>a-b);
            // console.warn('prices', prices);
            console.warn('dates', dates);
            if(dates.length){
              this.min_date = new Date(dates[0]);
              this.max_date = new Date(dates[dates.length-1]);
              // this.date = [this.min_date, this.max_date];
              this.year_range = [this.min_date.getFullYear(), this.max_date.getFullYear()];
  
              if(this.min_date.getYear() * 12 + this.min_date.getMonth() != this.min_date.getYear() * 12 + this.min_date.getMonth()){
                this.multicalendars = true;
              }
            }
            
            //console.warn('dates2', dates);
            var dates_obj = dates.reduce((o,v) => ((o[v] = {date: v, price: undefined}), o),{});
  
            (getObjectDotPathValue(newValue, '*') || []).forEach((f) => {
              var p = getObjectDotPathValue(f,'Flight.PriceDetail.Amount');
              if(isNaN(p)) return;
              var d = getObjectDotPathValue(f,'Combination.0.Segment.0.Origin.Date');
              if(!d) return;
              if(!dates_obj[d]['price'] || dates_obj[d]['price'] > p){
                dates_obj[d]['price'] = p;
              }
            });
  
            var date_by_prices = Object.values(dates_obj).sort((a,b) => a.price - b.price);
            var date_prices = [...new Set(date_by_prices.map(v => v.price))];
            
            var _is = {};
            date_by_prices.forEach((d) => {
              var p_index = date_prices.indexOf(d.price);
              var i = !p_index ? 1 : (p_index == date_prices.length-1 && p_index > 1 ? '3' : 2);
              _is[i] = true;
              m.push({
                date: new Date(d.date),
                i: i,
              });;
            })
  
            this.iis = [... new Set(Object.keys(_is).sort((a,b)=>a-b))];
            
          }
          console.log('markers', m);
          this.markers = m;
        }
      }
    },
    'filters': {
      handler(newValue, oldValue){
        // console.error('Should load new preferences', newValue);
        this.min_date = undefined;
          this.max_date = undefined;
        this.ddate= [],
        this.date= [],
        this.sortPrice = '0';
        this.direct = '0';
        this.priceMinMax = [];
        this.durationMinMax = [];
        this.stops = [];
        this.priceMinMax2 = {min:0,max:0};
        this.durationMinMax2 = {min:0,max:0};
        this.stops2 = {min:0,max:0};
        this.airlines = {};
        this.companies = newValue && newValue.companies || [];
        this.min_price = newValue && newValue.min_price && Math.floor(newValue.min_price) || 0;
        this.max_price = newValue && newValue.max_price && Math.ceil(newValue.max_price) || 0;
        this.min_stops = newValue && newValue.stops && newValue.stops.length && newValue.stops[0] || 0;
        this.max_stops = newValue && newValue.stops && newValue.stops.length && newValue.stops[newValue.stops.length - 1] || 0;
		
		
        this.saved = {
          sortPrice: '0',
          direct: '0',
          priceMinMax: [],
          durationMinMax: [],
          stops: [],
          airlines: [],
        };
        this.save();
      },
      immediate: true
    },
    /* 'single': {
      handler(newValue, oldValue){
        if(this.saved.date){
          if(newValue){
            this.saved.days = 0;
          } else {
            this.saved.days = 1;
          }
          this.save();
        }
      },
      deep: true
    },*/
    'priceMinMax2': {
      handler(newValue, oldValue){
        this.priceMinMax=newValue && (newValue.min || newValue.max) && [newValue.min, newValue.max] || [];
      },
      deep: true
    },
    'durationMinMax2': {
      handler(newValue, oldValue){
        this.durationMinMax=newValue && (newValue.min || newValue.max) && [newValue.min, newValue.max] || [];
      },
      deep: true
    },
    'stops2': {
      handler(newValue, oldValue){
        this.stops=newValue && (newValue.min || newValue.max) && [newValue.min, newValue.max] || [];
      },
      deep: true
    },
    'saved': {
      handler(newValue, oldValue){
        // console.warn('filters', this.saved);
        this.clearValidations();
      },
      deep: true
    },
  }
}
