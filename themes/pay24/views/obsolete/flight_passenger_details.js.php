let search_axios, search_axios_timer, search_axios_cancel;
const CancelToken = axios.CancelToken;

export default {
  emits: ['edit', 'result', 'save'],
  props: {
      modelValue: {
          type: Object,
          default: () => ({}),
      },
      flight_data: {
          type: Object,
          default: () => (flight_data),
      },
  },
  components : {
		'FlightOptionalServices' : {
			emits: ['update:modelValue'],
			props: {
        modelValue: {
          type: Object,
          default: () => ({}),
        },
				optional_services: {
					type: Array,
				},
				airports_by_code: {
					type: Object,
				},
				ptc: {
					type: String,
				},
			},
      data: () => ({
        selected_routes: [],
        selected_ptcs: [],
        secured: false,
        identification_required: false,
        referenceDateEnd: new Date(),
        referenceDate: new Date(),
        with_included: false,
        all_ptcs: [],
        all_routes: [],
        nodes: [],
        selected: [],
        ticked: [],
        expanded: [],
        filter: '',
      }),
			template : `
    <div class="bg-white rounded-theme"> 
      <v-list-item-title class="pa-4 pb-2 pt-0 text-h5">Optiuni {{ ptc ? ' ' + translatePtcWithIndex(ptc) : ''}}</v-list-item-title>

      <div class="pa-4">
      <?php /*
      <v-select v-if="all_ptcs.length > 1" multiple clearable filled color="purple-12" v-model="selected_ptcs" :items="all_ptcs.map(v => { return {
        value: v,
        title: translatePtcWithIndex(v)
      }})" label="PTC">
        <template v-slot:selection="{ item, index }">
          <v-chip>{{ translatePtcWithIndex(item.value) }}</v-chip>
        </template>
      </v-select>
      */ ?>
      <template v-if="!ptc">
      <v-list-subheader v-if="all_ptcs.length > 1" class="">Persoane</v-list-subheader>
      <v-btn-toggle v-if="all_ptcs.length > 1"
        v-model="selected_ptcs"
        multiple
        class="flex-wrap rounded-0 mb-4"
        style="gap:10px; height:auto;"
        theme="dark"
      >
        <v-btn v-for="ptc in all_ptcs" :value="ptc" :active="false" class="text-none min-content rounded-theme" :class="{[selected_ptcs && (-1 !== selected_ptcs.indexOf(ptc)) ? 'bg-primary' : 'bg-secondary text-black'] : 1}">
          <small class="">{{ translatePtc(ptc) }}</small>
          <div v-if="'' !== ptc.split(',')[1]" class="pl-2">{{ 1 + parseInt(ptc.split(',')[1]) }}</div>
        </v-btn>
      </v-btn-toggle>
      <?php /*
      <v-select v-if="all_routes.length > 1" multiple clearable filled color="purple-12" v-model="selected_routes" :items="all_routes.map((v,i) => { return {
        value: v.toString(),
        title: routesLabel(('' + v).split(','))
      }})" label="Route">
        <template v-slot:selection="{ item, index }">
          <v-chip>{{ routesLabel(('' + item.value).split(',')) }}</v-chip>
        </template>
      </v-select>
      */ ?>
      <v-list-subheader v-if="all_routes.length > 1" class="">Rute</v-list-subheader>
      </template>
      <v-btn-toggle v-if="all_routes.length > 1"
        v-model="selected_routes"
        multiple
        class="flex-wrap rounded-0 mb-4"
        dark style="gap:10px; height:auto;"
        theme="dark"
      >
        <v-btn v-for="route in all_routes" :value="route" :active="false" class="text-none min-content rounded-theme" :class="{[selected_routes && (-1 !== selected_routes.indexOf(route)) ? 'bg-primary' : 'bg-secondary text-black'] : 1}">
          <small>{{ routesLabel(('' + route).split(',')) }}</small>
        </v-btn>
      </v-btn-toggle>
      <?php /*
      <div>
        <v-checkbox
          v-model="with_included"
          hide-details
          label="Afiseaza serviciile incluse"
        />
      </div>
      */ ?>
    </div>
      <?php /*
        <q-input
        ref="filterRef"
        filled
        v-model="filter"
        label="Search - only filters labels that have also '(*)'"
      >
        <template v-slot:append>
          <q-icon v-if="filter !== ''" name="clear" class="cursor-pointer" @click="resetFilter" />
        </template>
      </q-input> */ ?>
      <q-tree
        :nodes="nodes"
        icon="mdi-chevron-right"
        default-expand-all
        node-key="id"
        tick-strategy="leaf"
        v-model:ticked="ticked"
        v-model:expanded="expanded"
        <?php /*
        :filter="filter"
        :filter-method="myFilterMethod"
        */ ?>
      >
      <template v-slot:default-header="prop">
        <div @click="(!(prop.node.children && prop.node.children.length) && (prop.ticked = !prop.ticked))" class="v-theme--dark w-100 d-flex justify-space-between">
        <span class="">
          {{ prop.node.label }}
        </span>
        <strong v-if="prop.node.price" class="ms-auto text-pre color-dark-light">
          <small>{{ prop.node.price }}</small>
        </strong>
        </div>
      </template>
      </q-tree>
    </div>`,
    watch: {
      'ticked':{
        handler(newValue, oldValue){
			var nv = newValue||[];
			var ov = oldValue||[];
          // console.warn('ticked');
          console.warn('ticked', newValue)
		  if(nv && nv.length){
				var s = [];
				var e = [];
				var dv = nv.filter(a => -1 == ov.indexOf(a));
				
				
				dv.forEach((i,v) => {
					var a = i.split(',');
					if(-1 == e.indexOf(i)){
						e.push(i);
						s.push(a[0]);
					}
				});
				
				newValue.forEach((i,v) => {
					var a = i.split(',');
					if(-1 != e.indexOf(i)){
						// do nothing
					} else if(-1 == s.indexOf(a[0])){
						s.push(a[0]);
					} else {
						newValue.splice(v,1);
					}
				});
		  }
          this.$emit('update:modelValue', newValue);
        },
        // immediate: true,
      },
      'with_included':{
        handler(newValue, oldValue){
          // console.warn('shouldReload');
          this.reloadNodes();
        },
        // immediate: true,
      },
      'selected_routes':{
        handler(newValue, oldValue){
          // console.warn('shouldReload');
          this.reloadNodes();
        },
        // immediate: true,
      },
      'selected_ptcs':{
        handler(newValue, oldValue){
          // console.warn('shouldReload');
          this.reloadNodes();
        },
        // immediate: true,
      },
    },
    methods: {
      /* resetFilter (node, filter) {
        this.filter.value = ''
        this.$refs.filterRef.value.focus()
      },
      myFilterMethod (node, filter, b) {
        const filt = filter.toLowerCase()

        //console.log(filter);
        return node.label && node.label.toLowerCase().indexOf(filt) > -1
      }, */
      translatePtc(v){
        return v == 'ALL,' && 'Toti pasagerii' || (translate_ptc_short[v.split(',')[0]][0])
      },
      translatePtcWithIndex(v){
        return v == 'ALL,' && 'Toti pasagerii' || (translate_ptc_short[v.split(',')[0]][0] + ' ' + (1 + parseInt(v.split(',')[1])))
      },
      isIncluded(s){
        var inc = s.Included;
        return '1' === inc || 'true' === inc || 1 === inc || true === inc;
      },
      reloadNodes(){
        this.nodes = [];
        // console.warn('selected_ptcs', this.selected_ptcs);
        // console.warn('selected_routes', this.selected_routes);
        var all_option_services = this.optional_services.map((v,i) => {return {...v, index: i}});
        var optional_services = all_option_services;

        var options = (getObjectDotPathValue(this.optional_services, '*.Options.Option',[]) || []).flat(1);
        // console.warn(options);
        options.forEach(o => {
          if(!o){
            return;
          }
          if(!o.Description){
            return;
          }
          if(!o.Price){
            return;
          }
          var descriptions = [];
          var r = new RegExp('\\s*\\(\\s*' + o.Price.Amount + '\\s*' + o.Price.Currency + '\\)\\s*');
          (getObjectDotPathValue(o,'Description', []) || []).forEach(d => {
            descriptions.push(d.replace(r,' ').replace(/\s+/g, ' ').trim())
          })
          o.Description = descriptions;
          // console.warn(descriptions);
        })

        if(!this.with_included){
          optional_services = optional_services.filter(s => !this.isIncluded(s));
        }
        // console.warn('optional_services', optional_services);

        this.all_routes = [... new Set(optional_services.map(s => [s.Route.From,s.Route.To].toString()))];
        if(1 == this.all_routes.length){
          this.selected_routes = this.all_routes;
        }
        this.all_ptcs = [... new Set(optional_services.map(s => [s.Target,s.PassengerIndex].toString()))];
        // this.all_ptcs = [... new Set(optional_services.map(s => [s.Target,s.PassengerIndex].toString()))].filter(p => p != 'ALL,');

        if(1 == this.all_ptcs.length){
          this.selected_ptcs = this.all_ptcs;
        }

        // console.warn('all_routes', this.all_routes);
        // console.warn('all_ptcs', this.all_ptcs);
        
        var selected_ptcs = [...this.selected_ptcs];
        
        if(this.selected_ptcs && this.selected_ptcs.length){
          // selected_ptcs.push('ALL,');
          optional_services = optional_services.filter(s => -1 != selected_ptcs.indexOf([s.Target, s.PassengerIndex].toString()));
        }
        
        if(this.selected_routes && this.selected_routes.length){
          optional_services = optional_services.filter(s => -1 !== this.selected_routes.indexOf([s.Route.From, s.Route.To].toString()));
          // console.error('optional_services', optional_services, this.selected_routes)
        }

        //this.all_ptcs = [];
        //this.all_routes = [];

        var category_names = [... new Set(getObjectDotPathValue(optional_services,'*.Service.CategoryName').flat(1) || [])];
        // console.warn(category_names);
        category_names.forEach(category_name => {
          var category_label = capitalizeWords(category_name, 1);
          var node = {
            id: category_name,
            label: category_label,
            // icon: 'mdi-check',
            children : [],
            noTick : true,
          }
          var services = (getObjectDotPathValue(optional_services,'*').flat(1) || []).filter(s => s.Service.CategoryName == category_name);

          var service_names = [... new Set(services.map(s => s.Service.Name))];
          service_names.forEach(service_name => {
            var service_label = capitalizeWords(service_name, 1);
            var node2 = {
              id: category_name + '-' + service_name,
              label: service_label,
              // icon: 'mdi-check',
              children : [],
              tickable : true,
            }
            var services2 = (services || []).filter(s => s.Service.Name == service_name);
            var option_names = [... new Set(services2.filter(s => (s.Options && s.Options.Option && s.Options.Option.length)).map(s => s.Options.Option.map(o=>!o.Description && "?" || o.Description[0])).flat(1))];
            
            option_names.forEach(option_name => {
              var option_label = capitalizeWords(option_name, 1);
              var node3 = {
                id: category_name + '-' + service_name + '-' + option_name,
                label: option_label,
                // icon: 'mdi-check',
                children : []
              }

              var services3 = services2.filter(s => s.Options && s.Options.Option && s.Options.Option.find(o => o.Description && o.Description[0] == option_name));

              var ptc_names = [... new Set(services3.map(s => [s.Target, s.PassengerIndex].toString()))];
              
              //this.all_ptcs = [... new Set([... this.all_ptcs, ...ptc_names])];

              if(selected_ptcs && selected_ptcs.length){
                ptc_names = ptc_names.filter(p => -1 != selected_ptcs.indexOf(p));
                if(!ptc_names.length){
                  return;
                }
              }

              node2.children.push(node3);

              ptc_names.forEach(ptc_name => {
                if(!(selected_ptcs && 1 == this.selected_ptcs.length)){
                  var node4 = {
                    id: category_name + '-' + service_name + '-' + option_name + '-' + ptc_name,
                    label: this.translatePtcWithIndex(ptc_name.toString()),
                    // icon: 'mdi-check',
                    children : []
                  }
                  node3.children.push(node4);
                }

                var services4 = services3.filter(s => [s.Target, s.PassengerIndex].toString() == ptc_name.toString());
                services4.forEach((s) => {
                  var route_da = [s.Route.From, s.Route.To];
                  //this.all_routes = [... new Set([...this.all_routes, route_da.toString()])];
                  var option_index = s.Options.Option.findIndex(o => o.Description && o.Description[0] == option_name);
                  var option = s.Options.Option[option_index];
                  var formatted_price = (this.isIncluded(s) ? 'inclus' : (option.Price? '' + format_price(option.Price.Amount, option.Price.Currency)  + '' : '') );
                  var node5 = {
                    id: [ s.index, option_index ].toString(),
                    label: this.routesLabel(route_da),
                    price: formatted_price,
                    icon: this.isIncluded(s) ? 'mdi-head-check-outline' : 'mdi-check',
                    enabled: false,
                    children : []
                  }
                  if(!(selected_ptcs && 1 == this.selected_ptcs.length)){
                    if(!(this.selected_routes && 1 == this.selected_routes.length)){
                      node4.children.push(node5);
                      node2.noTick = true;
                    } else {
                      node4.price = node5.price;
                      node4.icon = node5.icon;
                      node4.id = node5.id;
                    }
                  } else {
                    if(!(this.selected_routes && 1 == this.selected_routes.length) || !(selected_ptcs && 1 == this.selected_ptcs.length)){
                      node3.children.push(node5);
                    } else {
                      node3.price = node5.price;
                      node3.icon = node5.icon;
                      node3.id = node5.id;
                    }
                  }
                })
              })
            })
			
            var services4 = services2.filter(s => !(s.Options && s.Options.Option));
            var no_option_names = [... new Set(services4.map(s => s.Service.Name))];
            // console.warn('no_option_names', no_option_names)
            no_option_names.forEach(no_option_name => {
              var option_label = capitalizeWords(no_option_name, 1);
              var node3 = {
                id: category_name + '-' + service_name + '-' + option_name,
                label: option_label,
                // icon: 'mdi-check',
                children : []
              }

              var services3 = services4.filter(s => s.Service.Name == no_option_name);

              var ptc_names = [... new Set(services3.map(s => [s.Target, s.PassengerIndex].toString()))];

              //this.all_ptcs = [... new Set([... this.all_ptcs, ...ptc_names])];

              if(selected_ptcs && selected_ptcs.length){
                ptc_names = ptc_names.filter(p => -1 != selected_ptcs.indexOf(p));
                if(!ptc_names.length){
                  return;
                }
              }
              node2.children.push(node3);

              ptc_names.forEach(ptc_name => {
                if(!(selected_ptcs && 1 == this.selected_ptcs.length)){
                  var node4 = {
                    id: category_name + '-' + service_name + '-' + option_name + '-' + ptc_name,
                    label: this.translatePtcWithIndex(ptc_name.toString()),
                    // icon: 'mdi-check',
                    children : []
                  }
                  node3.children.push(node4);
                }
                var services4 = services3.filter(s => [s.Target, s.PassengerIndex].toString() == ptc_name.toString());
                services4.forEach((s) => {
                  var route_da = [s.Route.From, s.Route.To];
                  //this.all_routes = [... new Set([...this.all_routes, route_da.toString()])];
                  var formatted_price = (this.isIncluded(s) ? ' (inclus)' : ' (ciudat)');

                  var node5 = {
                    id: category_name + '-' + service_name + '-' + option_name + '-' + ptc_name + '-' + route_da,
                    label: this.routesLabel(route_da)  + formatted_price,
                    icon: this.isIncluded(s) ? 'mdi-book-check-outline' : 'mdi-head-question-outline',
                    children : [],
                    noTick: true,
                  }
                  if(!(selected_ptcs && 1 == this.selected_ptcs.length)){
                    if(!(this.selected_routes && 1 == this.selected_routes.length)){
                      node4.children.push(node5);
                    } else {
                      node4.label += ' ' + formatted_price;
                      node4.icon = node5.icon;
                      node4.noTick = node5.noTick;
                      node4.id = node5.id;
                    }
                  } else {
                    if(!(this.selected_routes && 1 == this.selected_routes.length) || !(selected_ptcs && 1 == this.selected_ptcs.length)){
                      node3.children.push(node5);
                    } else {
                      node3.label += ' ' + formatted_price;
                      node3.icon = node5.icon;
                      node3.noTick = node5.noTick;
                      node3.id = node5.id;
                    }
                  }
                })
              })
            })
			
            if(node2.children.length == 1 && node2.label == node2.children[0].label){
              node2 = node2.children[0];
            }
            node.children.push(node2);
          })
          if(node.children.length == 1 && node.label == node.children[0].label){
            node = node.children[0];
          }
          this.nodes.push(node);
        })
        this.expanded = [];
        if(selected_ptcs && selected_ptcs.length || this.selected_routes && this.selected_routes.length){
          // this.expanded = [...new Set([...(getObjectDotPathValue(this.nodes, '*.id') || []).flat(1), ...(getObjectDotPathValue(this.nodes, '*.children.*.id') || []).flat(1), ...(getObjectDotPathValue(this.nodes, '*.children.*.children.*.id') || []).flat(1), ...(getObjectDotPathValue(this.nodes, '*.children.*.children.*.children.*.id') || []).flat(1)])];
          this.expanded = [...new Set([...(getObjectDotPathValue(this.nodes, '*.id') || []).flat(1), ...(getObjectDotPathValue(this.nodes, '*.children.*.children.*.id') || []).flat(1), ...(getObjectDotPathValue(this.nodes, '*.children.*.children.*.children.*.id') || []).flat(1)])];
        } else {
          this.expanded = [...new Set([...(getObjectDotPathValue(this.nodes, '*.id') || []).flat(1)])];
        }

        if(this.ticked && this.ticked.length){
          for(var i in this.ticked || {}){
            var os = this.ticked[i];
            if('string' == typeof os){
              os = os.split(',');
            }
            var optionalServiceIndex = os[0];
            var optionIndex = os[1];
            var s = all_option_services[optionalServiceIndex];
            var o = s.Options.Option[optionIndex];
            var category_name = s.Service.CategoryName;
            this.expanded.push(category_name);
            var service_name = s.Service.Name;
            this.expanded.push(category_name + '-' + service_name);
            var option_name = !o.Description && "?" || o.Description[0];
            this.expanded.push(category_name + '-' + service_name + '-' + option_name);
            var ptc_name = [s.Target, s.PassengerIndex];
            this.expanded.push(category_name + '-' + service_name + '-' + option_name + '-' + ptc_name);
            // var route_da = [s.Route.From, s.Route.To];
            // this.expanded.push(category_name + '-' + service_name + '-' + option_name + '-' + ptc_name + '-' + route_da);
          }
          this.expanded = [...new Set([...this.expanded])];
        } else {
          // console.warn('nothing ticked');
        }
        // console.warn('expanded', this.expanded);
        this.nodes.forEach(n => {
          if(!(n.children && n.children.length)) return;
          if(n.children.length >= 1){
            n.noTick = true;
          }
          n.children.forEach(n2 => {
            if(!(n2.children && n2.children.length)) return;
            if(n2.children.length >= 1){
              n2.noTick = true;
            }
            n2.children.forEach(n3 => {
              // console.error(n3);
              if(!(n3.children && n3.children.length)) return;
              if(n3.children.length >= 1){
                n3.noTick = true;
              }
              n3.children.forEach(n4 => {
                // console.error(n4);
                if(!(n4.children && n4.children.length)) return;
                if(n4.children.length >= 1){
                  n4.noTick = true;
                }
              })
            })
          })
        })
      },
      routesLabel(s){
        if(s[0] == s[1]){
          return this.airports_by_code[s[0]].City;
        } else if(this.airports_by_code[s[0]].City == this.airports_by_code[s[1]].City){
          return '('+ this.airports_by_code[s[0]].City +') ' + this.airports_by_code[s[1]]._ + ' - ' + this.airports_by_code[s[1]]._;
        }
        return this.airports_by_code[s[0]].City + ' - ' + this.airports_by_code[s[1]].City;
      }
    },
    mounted: function() {
        // console.warn('asdf', this.optional_services);
        if(this.ptc){
          this.selected_ptcs = [this.ptc]
        }
        if(this.modelValue){
          this.ticked = this.modelValue;
        }
        this.reloadNodes();
      }
		},
		'FlightDetailsPassenger' : loadViewAsync('flight_details_passenger'),
		'FlightDetailsCreatePassenger' : loadViewAsync('flight_details_create_passenger'),
		'FlightDetailsSeatAssignment' : loadViewAsync('flight_details_seat_assignment'),
		'BaggageAllowance' : {
			emits: [],
			props: {
				segments: {
					type: Array,
          default: () => ([]),
				},
			},
      data: () => ({
        pcs: [],
        same_pc: false,
        no_pcs: false,
      }),
			template : `
        <template v-if="segments && segments.length">
          <v-btn class="rounded-theme w-100 d-flex py-4 modal-button text-none align-center justify-space-between pa-4" variant="outlined">
            <template v-slot:prepend>
              <v-icon size="x-large" icon="mdi-bag-suitcase"></v-icon>
            </template>
            <div class="d-flex flex-wrap align-center">
              <span class="me-2">Bagaj de mana</span>
              <small>{{ pcs.join(' | ')}}</small>
            </div>
            <template v-slot:append>
              <strong v-html="no_pcs ? 'FARA': (pcs.length == 1 ? 'Inclus' : 'Partial')"></strong>
            </template>
          </v-btn>
        </template>
        <template v-else>
          Nicio informatie despre bagaje
        </template>
      `,
      mounted: function() {
        this.pcs = this.segments && [... new Set(getObjectDotPathValue(this.segments, '*.Baggage.Allowed').flat(1))] || [];
        this.same_pcs = this.pcs.length === 1 ? this.pcs[0] : false;
        this.no_pcs = this.same_pcs == '0 PC';
      }
		},
	},
	data: () => ({
    SeatMaps: {},
    pax_type: undefined,
    pax_item: undefined,
    result: {},
    serviceData: {},
    optionalServicesPriceFor: {},
    optional_services: {},
    all_optional_services: {},
    airports_by_code: {},
    optionalServices: [],
    version: '1',
    loading: {
      seats: false,
    },
    passengers: [],
    ptc_errors: {},
    requested_validation: false,
    errors: [],
    validations: [],
    assigned_passengers: {},
    seats: {},
  }),
	template : `
  <div class="passenger-details v-theme--dark" v-if="flight_data">
  <div v-for="pax_fare in [{Type:'ALL'}]" v-if="optional_services['ALL,']" :set="(pax_split_index=undefined)">
    <Modal v-if="optional_services[[pax_fare.PTC, pax_split_index]] && optional_services[[pax_fare.PTC, pax_split_index]].filter((v) => !getObjectDotPathValue(v,'Included', false)).length">
        <template v-slot:activator="{ props }">
          <v-btn v-bind="props" class="rounded-theme w-100 d-flex py-4 modal-button text-none align-center justify-space-between pa-4" variant="outlined">
            <template v-slot:prepend>
              <v-icon size="x-large" icon="mdi-bag-suitcase"></v-icon>
            </template>
            <div class="d-flex flex-wrap align-center">
              <span class="me-2">Alege servicii aditionale toti passagerii</span>
            </div>
            <template v-slot:append v-if="optionalServicesPriceFor[[pax_fare.PTC, pax_split_index]]">
              <strong class="text-primary text-pre" v-html="format_price(optionalServicesPriceFor[[pax_fare.PTC, pax_split_index]], flight_data.Currency)"></strong>
            </template>
          </v-btn>
        </template>
        <v-list lines="two" subheader theme="light" class="ma-4 mt-0 max-height pt-4" rounded="theme">
          <FlightOptionalServices :optional_services="all_optional_services" :airports_by_code="airports_by_code" :ptc="[pax_fare.PTC, pax_split_index].toString()" v-model="optionalServices" />
          <?php /*
          <div class="px-4" v-for="(optionalService, serviceIndex) in optional_services[[pax_fare.PTC, pax_split_index]].filter((v) => !getObjectDotPathValue(v,'Included', false))">
            <h6 class="d-flex align-center justify-space-between">{{ getObjectDotPathValue(optionalService,'Service.Name') }} ({{ getObjectDotPathValue(optionalService,'Service.CategoryName') }}) ({{ getObjectDotPathValue(optionalService,'Route.From') + '-' + getObjectDotPathValue(optionalService,'Route.To') }})
            </h6>
            <div v-if="getObjectDotPathValue(optionalService,'Options.Option')" class="v-theme--dark">
              <template v-for="(option,optionIndex) in optionalService.Options.Option">
                <v-checkbox theme="dark" color='primary' multiple hide-details density="compact" :value="[optionalService._index,optionIndex]" v-model="optionalServices" class="align-start optional-service-check">
                  <template v-slot:label>
                    <small class="color-dark-light" style="word-break: break-all;">{{ [...(option.Description || [])].join('; ') }}</small>
                  </template>
                  <template v-slot:append v-if="option.Price">
                    <strong class="text-nowrap">
                    {{ format_price(getObjectDotPathValue(option,'Price.Amount',0), getObjectDotPathValue(option,'Price.Currency'))}}</strong>
                  </template>
                </v-checkbox>
              </template>
            </div>
          </div>
          */ ?>
        </v-list>
        <template v-slot:footer="{ props }">
          <?php /*<v-btn @click="props.close()" class="text-none font-weight-normal cancel-button" size="x-large" color="secondary" rounded="theme"><v-icon icon="mdi-arrow-left"></v-icon></v-btn> */ ?>
          <v-btn @click="props.close()" class="text-none font-weight-normal flex-grow-1" size="x-large" color="primary" rounded="theme">Confirma<template v-if="optionalServicesPriceFor[[pax_fare.PTC, pax_split_index]]"> ({{ format_price(optionalServicesPriceFor[[pax_fare.PTC, pax_split_index]], flight_data.Currency) }})</template></v-btn>
      </template>
    </Modal>
  </div>
  <p v-if="secured || identification_required">Pentru acest zbor trebuie completate corect informatiile referitoare la documentul de identificare (CI / Pasaport) pentru fiecare pasager</p>
  <template v-for="pax_fare in flight_data.FareDetails.PaxFare">
    <template v-for="(pax_split_item, pax_split_index) in parseInt(pax_fare.Count)">
      <div class="bg-background rounded-theme mb-4" style="overflow:hidden;">
        <div class="bg-background2 pa-4 d-flex justify-start align-center">
          <v-badge color="#979BA0" :content="pax_split_item" inline></v-badge>
          <span class="ml-4">
            {{ translate_ptc[pax_fare.PTC][0] }}
          </span>
        </div>
        <div class="pa-4">
          <div class="mb-4">
            <FlightDetailsPassenger :referenceDate="referenceDate" :referenceDateEnd="referenceDateEnd" :identification_required="identification_required" v-on:remove_passenger="(p) => removePassenger(p)" v-on:assign_passenger="(p) => addPassenger(p, pax_fare.PTC, pax_split_index)" :secured="secured" :type="pax_fare.PTC" v-on:open_create_passenger="(p) => openCreatePassengerModel(p, pax_split_index, pax_fare.PTC)" :passengers="passengers" :assigned_passengers="assigned_passengers" :assigned_passenger="assigned_passengers[[pax_fare.PTC,pax_split_index]]"></FlightDetailsPassenger>

            <v-messages color="error" class="v-theme--light pa-4 mt-4 bg-theme bg-background rounded-theme" v-show="!!ptc_errors[[pax_fare.PTC, pax_split_index]] && !!ptc_errors[[pax_fare.PTC, pax_split_index]].length" :active="!!ptc_errors[[pax_fare.PTC, pax_split_index]] && !!ptc_errors[[pax_fare.PTC, pax_split_index]].length" :messages="ptc_errors[[pax_fare.PTC, pax_split_index]]"></v-messages>
          </div>
          <div class="mb-4">
            <BaggageAllowance :segments="pax_fare.BaggageAllowance"></BaggageAllowance>
          </div>
          <div class="bg-background2 pa-4 rounded-theme mb-4 text-left" v-if="optional_services[[pax_fare.PTC, pax_split_index]] && optional_services[[pax_fare.PTC, pax_split_index]].filter((v) => getObjectDotPathValue(v,'Included', false)).length">
            <h4 class="text-left mb-4" style="
                font-weight: normal;
            ">Servicii incluse</h4>
            <div v-for="(optionalService, serviceIndex) in optional_services[[pax_fare.PTC, pax_split_index]].filter((v) => getObjectDotPathValue(v,'Included', false))">
              <div class="text-caption d-flex align-center justify-space-between">{{ capitalizeWords(getObjectDotPathValue(optionalService,'Service.Name'),1) }} ({{ capitalizeWords(getObjectDotPathValue(optionalService,'Service.CategoryName'),1) }}) ({{ getObjectDotPathValue(optionalService,'Route.From') + '-' + getObjectDotPathValue(optionalService,'Route.To') }})
              </div>
            </div>
          </div>
          <Modal v-if="optional_services[[pax_fare.PTC, pax_split_index]] && optional_services[[pax_fare.PTC, pax_split_index]].filter((v) => !getObjectDotPathValue(v,'Included', false)).length">
            <template v-slot:activator="{ props }">
              <v-btn v-bind="props" class="rounded-theme w-100 d-flex py-4 modal-button text-none align-center justify-space-between pa-4" variant="outlined">
                <template v-slot:prepend>
                  <v-icon size="x-large" icon="mdi-bag-suitcase"></v-icon>
                </template>
                <div class="d-flex flex-wrap align-center">
                  <span class="me-2">Alege servicii aditionale</span>
                </div>
                <template v-slot:append v-if="optionalServicesPriceFor[[pax_fare.PTC, pax_split_index]]">
                  <strong class="text-primary text-pre" v-html="format_price(optionalServicesPriceFor[[pax_fare.PTC, pax_split_index]], flight_data.Currency)"></strong>
                </template>
              </v-btn>
            </template>
            <v-list lines="two" subheader theme="light" class="ma-4 mt-0 max-height pt-4" rounded="theme">
              <FlightOptionalServices :optional_services="all_optional_services" :airports_by_code="airports_by_code" :ptc="[pax_fare.PTC, pax_split_index].toString()" v-model="optionalServices"  />
              <?php /*
              <div class="px-4" v-for="(optionalService, serviceIndex) in optional_services[[pax_fare.PTC, pax_split_index]].filter((v) => !getObjectDotPathValue(v,'Included', false))">
                <h6 class="d-flex align-center justify-space-between">{{ getObjectDotPathValue(optionalService,'Service.Name') }} ({{ getObjectDotPathValue(optionalService,'Service.CategoryName') }}) ({{ getObjectDotPathValue(optionalService,'Route.From') + '-' + getObjectDotPathValue(optionalService,'Route.To') }})
                </h6>
                <div v-if="getObjectDotPathValue(optionalService,'Options.Option')" class="v-theme--dark">
                  <template v-for="(option,optionIndex) in optionalService.Options.Option">
                    <v-checkbox theme="dark" color='primary' multiple hide-details density="compact" :value="[optionalService._index,optionIndex]" v-model="optionalServices" class="align-start optional-service-check">
                      <template v-slot:label>
                        <small class="color-dark-light" style="word-break: break-all;">{{ [...(option.Description || [])].join('; ') }}</small>
                      </template>
                      <template v-slot:append v-if="option.Price">
                        <strong class="text-nowrap">
                        {{ format_price(getObjectDotPathValue(option,'Price.Amount',0), getObjectDotPathValue(option,'Price.Currency'))}}</strong>
                      </template>
                    </v-checkbox>
                  </template>
                </div>
              </div>
              */ ?>
            </v-list>
            <template v-slot:footer="{ props }">
              <?php /*<v-btn @click="props.close()" class="text-none font-weight-normal cancel-button" size="x-large" color="secondary" rounded="theme"><v-icon icon="mdi-arrow-left"></v-icon></v-btn> */ ?>
              <v-btn @click="props.close()" class="text-none font-weight-normal flex-grow-1" size="x-large" color="primary" rounded="theme">Confirma<template v-if="optionalServicesPriceFor[[pax_fare.PTC, pax_split_index]]"> ({{ format_price(optionalServicesPriceFor[[pax_fare.PTC, pax_split_index]], flight_data.Currency) }})</template></v-btn>
          </template>
          </Modal>
          <template v-if="'INF' != pax_fare.PTC" v-for="(route, routeIndex) in flight_data.Routes">
            <div class="d-flex mt-4 align-center">
              <v-icon :icon="!routeIndex ? 'mdi-airplane-takeoff' : 'mdi-airplane-landing'" class="mr-4 flex-grow-0"></v-icon>
              <v-btn @click="openSeatAssignment(pax_split_index, pax_fare.PTC, assigned_passengers[[pax_fare.PTC,pax_split_index]], flight_data, routeIndex)" class="rounded-theme flex-grow-1 justify-space-between d-flex py-4 text-none modal-button" append-icon="mdi-chevron-right" variant="outlined" style="width:calc(100% - 40px);">
                <div class="d-flex flex-column">
                  <template v-if="seats[[pax_fare.PTC, pax_split_index]] && seats[[pax_fare.PTC, pax_split_index]][routeIndex]">
                    <template v-for="(seat,segmentIndex) in seats[[pax_fare.PTC, pax_split_index]][routeIndex]">
                      <template v-if="seat">
                        <div class="d-flex mr-4">
                          {{ this.flight_data.Routes[routeIndex].Segment[segmentIndex].Origin.Airport.Code }} - {{ this.flight_data.Routes[routeIndex].Segment[segmentIndex].Destination.Airport.Code }}
                          <template v-if="seat=='A'">
                            <span class="ml-4">Culoar</span>
                          </template>
                          <template v-else-if="seat=='W'">
                            <span class="ml-4">Fereastra</span>
                          </template>
                          <template v-else>
                            <span class="ml-4">{{ (s = SeatMaps[[routeIndex, segmentIndex]], r = s.Rows.Row[seat.split(',')[0]].Seat[seat.split(',')[1]], '' + r.Number + r.Code) + ' ' + (r.ChargeTypeReference ? (c = s.ChargeList.ChargeType.find((v) => v.Reference == r.ChargeTypeReference), c && format_price(c.Price.Amount, c.Price.Currency)) : '') }}</span>
                          </template>
                        </div>
                      </template>
                    </template>
                  </template>
                  <template v-else>
                    Alege loc in avion{{ !routeIndex ? (!flight_data.Routes[1] ? '' : ' - DUS') : ' - INTORS'}}
                  </template>
                </div>
              </v-btn>
            </div>
          </template>
        </div>
      </div>
	</template>
</template>
<FlightDetailsCreatePassenger :referenceDate="referenceDate" :referenceDateEnd="referenceDateEnd" :identification_required="identification_required" :secured="secured" ref="create_passenger_modal" v-on:save="(p, ptc, cnt) => this.addPassenger(p, ptc, cnt)"></FlightDetailsCreatePassenger>
<FlightDetailsSeatAssignment ref="assign_seat_modal" v-on:save="(p, ptc, cnt, routeIndex, segmentIndex, selectedSeat, defaultSeat) => this.saveSeatAssignment(p, ptc, cnt, routeIndex, segmentIndex, selectedSeat, defaultSeat)" v-on:seat_map="setSeatMap"></FlightDetailsSeatAssignment>
</div>
	`,
  methods: {
    reset(){
      this.SeatMaps = {};
      this.assigned_passengers = {};
      this.optional_services = {};
      this.all_optional_services = {};
      this.airports_by_code = {};
      this.optionalServices = [];
      this.seats = {};
      this.$refs.create_passenger_modal && this.$refs.create_passenger_modal.reset();
      this.$refs.assign_seat_modal && this.$refs.assign_seat_modal.reset();
    },
    setSeatMap(routeIndex, segmentIndex, sm){
      this.SeatMaps[[routeIndex, segmentIndex]] = sm;
      console.warn('SeatMaps',this.SeatMaps);
      console.warn('SeatMap',sm);
    },
    saveSeatAssignment(p, ptc, cnt, routeIndex, segmentIndex, selectedSeat){
      this.seats[[ptc,cnt]] || (this.seats[[ptc,cnt]] = {});
      this.seats[[ptc,cnt]][routeIndex] || (this.seats[[ptc,cnt]][routeIndex] = {});
      this.seats[[ptc,cnt]][routeIndex][segmentIndex] = selectedSeat || '';
      if(!this.seats[[ptc,cnt]][routeIndex][segmentIndex]){
        delete(this.seats[[ptc,cnt]][routeIndex][segmentIndex]);
        if(!Object.keys(this.seats[[ptc,cnt]][routeIndex]).length){
          delete(this.seats[[ptc,cnt]][routeIndex]);
        }
        if(!Object.keys(this.seats[[ptc,cnt]]).length){
          delete(this.seats[[ptc,cnt]]);
        }
      }
    },
    removePassenger(hash){
      console.warn('removing passenger', hash);
      if(!hash) return;
      
      var passenger_index = this.passengers.findIndex(p3 => p3.hash == hash);
      if(-1 !== passenger_index){
        this.passengers.splice(passenger_index, 1);
        console.warn('passenger removed');
        this.savePassengers();
      }

      
      for(var k in this.assigned_passengers){
        var p = this.assigned_passengers[k];
        if(p.hash == hash){
          delete(this.assigned_passengers[k]);
        }
      }
      console.warn('this.assigned_passengers', this.assigned_passengers);
      return -1 !== passenger_index;
    },
    addPassenger(p, ptc, cnt){
      var px = {...p};
      delete(px.hash);
      console.warn('addpassenger', p);
      var p2 = {...px, hash: JSON.stringify(px)};
      if(p.hash){
        for(var k in this.assigned_passengers){
          var p3 = this.assigned_passengers[k];
          if(p3.hash == p.hash){
            this.assigned_passengers[k] = p2;
          }
        }
        this.removePassenger(p.hash);
      }
      if(p2.hash){
        this.removePassenger(p2.hash);
      }
      this.passengers.push(p2);
      this.savePassengers();
      this.assigned_passengers[[ptc,cnt]] = p2;
    },
    savePassengers(){
      this.passengers = JSON.parse(JSON.stringify(this.passengers));
      saveStorage('pay24.flight.passengers',{
        version: this.version,
        passengers: this.passengers.filter(p => p.hash),
      });
    },
    clearValidations(){
      // this.$refs.form.resetValidation();
      this.errors = [];
      this.ptc_errors = {};
    },
    validateAndSave(){
      this.requested_validation = true;
      var valid = this.validate();
      if(valid){
        this.requested_validation = false;
        this.$emit('save');
      } else {
        var first_error = document.querySelector('.v-messages.text-error');
        if(first_error){
          scrollElemIntoView(first_error)
        }
      }
    },
    validate(){
      this.validations = [
        () => {
          var c = (getObjectDotPathValue(this.flight_data,'FareDetails.PaxFare',[]) || []).find(pax_fare => {
            for(var i = 1; i<= pax_fare.Count; i++){
              var pax_split_index = i - 1;
              var pax_split_item = i;
              if(!this.assigned_passengers[[pax_fare.PTC, pax_split_index]]){

                var m = 'Nu ati selectat pasager pentru ' + translate_ptc[pax_fare.PTC][0] + ' ' + pax_split_item;

                this.ptc_errors[[pax_fare.PTC, pax_split_index]] = this.ptc_errors[[pax_fare.PTC, pax_split_index]] || [];
                this.ptc_errors[[pax_fare.PTC, pax_split_index]].push(m);
                return m;
              }
            }
            return false;
          })
          //
          return c
        }
      ];
      this.clearValidations();
      this.validations.every(f => {
        var v = f.bind(this)();
        v && this.errors.push(v);
        return !v;
      })
      var valid = !this.errors.length;
      return valid;
    },
    recalculate(){
      var options_price = 0;
      var seats_price = 0;
      var paidOptions = [];
      var paidSeats = [];
      var optionalServicesPriceFor = {};
      var serviceData = {
        upsellCode: (this.flight_data || {}).upsellCode,
        paidSeats: [],
        optionalServices: [],
        passenger: {},
      }
      var index_passengers = [];
      var passenger_indexes = (getObjectDotPathValue(this.flight_data || {}, 'FareDetails.PaxFare') || []).reduce((a, pf) => ([...Array(parseInt(pf.Count)).keys()].forEach(k => (a[[pf.PTC, k]] = index_passengers.length, index_passengers.push([pf.PTC, k])) ),a),{});
      for(var i in this.optionalServices || {}){
        var os = this.optionalServices[i];
        if('string' == typeof os){
          os = os.split(',');
        }
        var optionalServiceIndex = os[0];
        var optionIndex = os[1];
        var optionalService = this.flight_data.OptionalServices.OptionalService[optionalServiceIndex];
        var optionalServiceOption = optionalService.Options.Option[optionIndex];
        options_price += parseFloat(optionalServiceOption.Price.Amount);
        serviceData.optionalServices.push({
          bookingCode: optionalService.BookingCode,
          selectedOptionCode: optionalServiceOption.Code,
        })

        paidOptions.push({ ... optionalService, options:undefined, Option: optionalServiceOption });

        if(!optionalServicesPriceFor[[optionalService.Target, optionalService.PassengerIndex]]){
          optionalServicesPriceFor[[optionalService.Target, optionalService.PassengerIndex]] = 0;
        }
        optionalServicesPriceFor[[optionalService.Target, optionalService.PassengerIndex]]+= optionalServiceOption.Price && optionalServiceOption.Price.Amount && parseFloat(optionalServiceOption.Price.Amount) || 0;
      }
      var preferredSeats = {};
      for(var passenger_key in this.seats || {}){
        for(var routeIndex in this.seats[passenger_key]){
          for(var segmentIndex in this.seats[passenger_key][routeIndex]){
            var s = this.seats[passenger_key][routeIndex][segmentIndex];
            var sa = s && s.split(',') || [];
            var sm = this.SeatMaps[[routeIndex, segmentIndex]];
            var seat = sm && sa.length > 1 && sm.Rows.Row[sa[0]].Seat[sa[1]];
            if(sm && seat.ChargeTypeReference){
              var charge = sm.ChargeList.ChargeType.find((v) => v.Reference == seat.ChargeTypeReference);
              seats_price += parseFloat(charge.Price.Amount);

              var ps = {
                passengerIndex: passenger_indexes[passenger_key],
                segmentIndex: segmentIndex,
                legIndex: this.flight_data.Routes[routeIndex].Index,
                seatColumn: seat.Code,
                seatNumber: seat.Number,
                amount: charge.Price.Amount,
                currency: charge.Price.Currency,
              };
              serviceData.paidSeats.push(ps);

              paidSeats.push({ ... ps, Target: passenger_key.split(',')[0], PassengerIndex: passenger_key.split(',')[1], Route: {
                From: this.flight_data.Routes[routeIndex].Segment[segmentIndex].Origin.Airport.Code,
                To: this.flight_data.Routes[routeIndex].Segment[segmentIndex].Destination.Airport.Code,
              }});
            } else if(s) {
              preferredSeats[passenger_key] = preferredSeats[passenger_key] || {};
              preferredSeats[passenger_key] = {...preferredSeats[passenger_key], ...{
                ['SEAT:ROUTE_' + this.flight_data.Routes[routeIndex].Index + '_' + segmentIndex + ':ORIGIN']: this.flight_data.Routes[routeIndex].Segment[segmentIndex].Origin.Airport.Code,
                ['SEAT:ROUTE_' + this.flight_data.Routes[routeIndex].Index + '_' + segmentIndex + ':DESTINATION']: this.flight_data.Routes[routeIndex].Segment[segmentIndex].Destination.Airport.Code,
                ... (seat ? {
                  ['SEAT:ROUTE_' + this.flight_data.Routes[routeIndex].Index + '_' + segmentIndex + ':NUMBER']: seat.Number,
                  ['SEAT:ROUTE_' + this.flight_data.Routes[routeIndex].Index + '_' + segmentIndex + ':CODE']: seat.Code,
                } : {
                  ['SEAT:ROUTE_' + this.flight_data.Routes[routeIndex].Index + '_' + segmentIndex + ':PREFERENCE']: s,
                })
              }}
            }
          }
        }
      }

      serviceData.passenger = (getObjectDotPathValue(this.flight_data || {}, 'FareDetails.PaxFare') || []).reduce((a, pf) => (a[pf.PTC] = [], [...Array(parseInt(pf.Count)).keys()].forEach(k => (a[pf.PTC].push({
        title: (this.assigned_passengers[[pf.PTC, k]] || {}).title,
        firstName: (this.assigned_passengers[[pf.PTC, k]] || {}).firstname,
        lastName: (this.assigned_passengers[[pf.PTC, k]] || {}).lastname,
        birthDate: (this.assigned_passengers[[pf.PTC, k]] || {}).birth_date,
        country: (this.assigned_passengers[[pf.PTC, k]] || {}).nationality,
        email: (this.assigned_passengers[[pf.PTC, k]] || {}).email || (this.assigned_passengers[['ADT', 0]] || {}).email || (this.assigned_passengers[['SEN', 0]] || {}).email || 'testemail@yahoo.com',
        phone: (this.assigned_passengers[[pf.PTC, k]] || {}).phone || (this.assigned_passengers[['ADT', 0]] || {}).phone || (this.assigned_passengers[['SEN', 0]] || {}).phone || '0700000000',
        details: preferredSeats[[pf.PTC, k]] || null,
		... (this.secured || this.identification_required ? {
			idDocType: (this.assigned_passengers[[pf.PTC, k]] || {}).doctype || '0',
			idDocNumber: ((this.assigned_passengers[[pf.PTC, k]] || {}).doctype || '0') == '0' ? ((this.assigned_passengers[[pf.PTC, k]] || {}).pass) : (''),
			idDocIssuingCountry: ((this.assigned_passengers[[pf.PTC, k]] || {}).doctype || '0') == '0' ? ((this.assigned_passengers[[pf.PTC, k]] || {}).pass_c) : ((this.assigned_passengers[[pf.PTC, k]] || {}).nationality),
			idDocPaxNationality: (this.assigned_passengers[[pf.PTC, k]] || {}).nationality,
			idDocIssuingDate: ((this.assigned_passengers[[pf.PTC, k]] || {}).doctype || '0') == '0' ? ((this.assigned_passengers[[pf.PTC, k]] || {}).pass_s) : ((this.assigned_passengers[[pf.PTC, k]] || {}).ci_s),
			idDocExpiryDate: ((this.assigned_passengers[[pf.PTC, k]] || {}).doctype || '0') == '0' ? ((this.assigned_passengers[[pf.PTC, k]] || {}).pass_e) : ((this.assigned_passengers[[pf.PTC, k]] || {}).ci_e),
		} : {})
      }))),a),{});

      
      var total_price = parseFloat((this.flight_data || {}).Price);
      total_price += options_price;
      total_price += seats_price;
      
      this.result = {
        paidOptions: paidOptions,
        paidSeats: paidSeats,
        serviceData: serviceData,
        optionsPrice: options_price,
        seatsPrice: seats_price,
        totalPrice: total_price,
      };
      this.optionalServicesPriceFor = optionalServicesPriceFor;

      this.$emit('result', this.result);

      console.log(this.result);

      if(this.requested_validation){
        this.validate();
      }
      return false;
    },
    openSeatAssignment(pax_split_index, ptc, assigned_passenger, flight_data, routeIndex){
      this.$refs.assign_seat_modal.ptc_seats = this.seats;
      this.$refs.assign_seat_modal.cnt = parseInt(pax_split_index);
      this.$refs.assign_seat_modal.type = ptc;
      this.$refs.assign_seat_modal.passenger = assigned_passenger;
      this.$refs.assign_seat_modal.flight_data = flight_data;
      this.$refs.assign_seat_modal.routeIndex = routeIndex;
      this.$refs.assign_seat_modal.dialog=true;
    },
    openCreatePassengerModel(p, pax_split_index, ptc){
      this.$refs.create_passenger_modal.cnt = parseInt(pax_split_index);
      this.$refs.create_passenger_modal.hash = p.hash;
      this.$refs.create_passenger_modal.title = p.title || 'mr';
      var nationality = '' + (p.nationality || 'RO');
      var country = countries.find(c => c.value.toLowerCase() == nationality.toLowerCase());
      this.$refs.create_passenger_modal.nationality = country && country.value || 'RO';
      this.$refs.create_passenger_modal.firstname = p.firstname || '';
      this.$refs.create_passenger_modal.lastname = p.lastname || '';
      this.$refs.create_passenger_modal.date = undefined;
      this.$refs.create_passenger_modal.dob = undefined;
      this.$refs.create_passenger_modal.pass_c = this.$refs.create_passenger_modal.nationality;
      this.$refs.create_passenger_modal.pass = p.pass || '';
      this.$refs.create_passenger_modal.doctype = p.doctype || '';
      this.$refs.create_passenger_modal.ci = p.ci || '';
      this.$refs.create_passenger_modal.ci_s = new Date();
      this.$refs.create_passenger_modal.ci_e = new Date();
      this.$refs.create_passenger_modal.pass_s = new Date();
      this.$refs.create_passenger_modal.pass_e = new Date();
	  console.warn('openCreatePassengerModel', p);
      if(p.birth_date){
        this.$refs.create_passenger_modal.date = new Date(p.birth_date);
        this.$refs.create_passenger_modal.dob = new Date(p.birth_date);
      }
      if(p.ci_s){
        this.$refs.create_passenger_modal.ci_s = new Date(p.ci_s);
      }
      if(p.ci_e){
        this.$refs.create_passenger_modal.ci_e = new Date(p.ci_e);
      }
      if(p.pass_s){
        this.$refs.create_passenger_modal.pass_s = new Date(p.pass_s);
      }
      if(p.pass_e){
        this.$refs.create_passenger_modal.pass_e = new Date(p.pass_e);
      }
      this.$refs.create_passenger_modal.dialog=true;
      this.$refs.create_passenger_modal.type = p.type || ptc;
    }
  },
  mounted: function() {
    var storageItem = getStorage('pay24.flight.passengers','', {}, {}, {});
    if(storageItem && storageItem.version == this.version){
      this.passengers = storageItem.passengers;
    }

    if(pay24Account){
      var personal_data;
      if(personal_data = getObjectDotPathValue(pay24Account,'profile.personal_data')){
        var citizenship = (personal_data.citizenship || '').trim().replace(/\s+/g, ' ');
        var country;
        if(citizenship.length){
          var country = countries.find(c => c.value.toLowerCase() == citizenship.toLowerCase());
        }
        var p = {
          title: 'mr',
          firstname: (personal_data.last_name || '').trim().replace(/\s+/g, ' '),
          lastname: (personal_data.first_name || '').trim().replace(/\s+/g, ' '),
          nationality: country && country.value || 'RO',
          birth_date: (personal_data.birth_date || '').trim().replace(/\s+/g, ' '),
        };
        
        this.passengers.unshift(p);
      }
      var associated_persons;
      if((associated_persons = getObjectDotPathValue(pay24Account,'associated_persons.*.personal_data.first_name')) && (associated_persons = associated_persons.flat(1)).length){
        associated_persons.forEach((n) => {
          var n = '' + n;
          if(!n.length) return;
          var p = {
            title: 'mr',
            firstname: capitalizeWords((n.replace(/\s.*/, '') || '').trim().replace(/\s+/g, ' ')),
            lastname: capitalizeWords((n.replace(/.*?(\s|$)/, '') || '').trim().replace(/\s+/g, ' ')),
          };
          
          this.passengers.unshift(p);
        })
      }

      console.warn(this.passengers)
    }
		setTimeout(() => {
			
		},1000)
	},
  watch:{
    'flight_data': {
      handler(newValue, oldValue){
        this.reset();
		this.secured = false;
		this.identification_required = false;
		var go_dates = getObjectDotPathValue(this.flight_data || {},'Routes.*.Segment.*.Origin.Date').flat(1);
		this.referenceDate = new Date();
		this.referenceDateEnd = new Date();
		// if(go_dates && go_dates.length){
			// this.referenceDate = new Date(go_dates[0]);
			// this.referenceDateEnd = new Date(go_dates[go_dates.length - 1]);
		// }
		var ident_req = getObjectDotPathValue(this.flight_data || {}, 'OfferConfiguration.IdentificationDocument.Required');
		if(ident_req && (true === ident_req || 'true' == ident_req)){
			this.identification_required = true;
		}
        this.optional_services = (getObjectDotPathValue(newValue || {}, 'OptionalServices.OptionalService') || [])
          .map((v,i) => {return {_index: i, ...v}})
          .reduce((result, service) => (
              (undefined === result[[service.Target,service.PassengerIndex]] && (result[[service.Target,service.PassengerIndex]] = []), 
              result[[service.Target,service.PassengerIndex]].push(service)
          ), result), {});

        this.all_optional_services = getObjectDotPathValue(newValue, 'OptionalServices.OptionalService', []) || [];
        console.warn('optional_services',this.optional_services)
            
        // console.warn('flight_data', flight_data);
        this.airports_by_code = {};
        getObjectDotPathValue(newValue, 'Routes.*.Segment.*').flat(1).forEach(s => {
			if(s['Secured']){
				this.secured = true;
			}
          ['Origin', 'Destination'].forEach(t => {
            if(!s[t] || !s[t].Airport) return;
            if(!this.airports_by_code[s[t].Airport.Code]){
              this.airports_by_code[s[t].Airport.Code] = s[t].Airport;
            }
          })
        });
        this.recalculate();
      },
      immediate: true,
    },
    'seats': {
      handler(newValue, oldValue){
        console.warn('seats watch recalculate'),
        this.recalculate();
      },
      immediate: true,
      deep: true,
    },
    'optionalServices': {
      handler(newValue, oldValue){
        console.warn('optionalServices watch recalculate'),
        this.recalculate();
      },
      immediate: true,
      deep: true,
    },
    'assigned_passengers': {
      handler(newValue, oldValue){
        console.warn('Assigned passenger watch recalculate'),
        this.recalculate();
      },
      immediate: true,
      deep: true,
    },
  }
}
