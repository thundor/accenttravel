export default {
  components : {
    'Versiunea1' : {
			props: {
				optional_services: {
					type: Array,
				},
				airports_by_code: {
					type: Object,
				},
			},
      data: () => ({
        selected_routes: [],
        selected_ptcs: [],
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
    <div class="v-theme--light bg-white rounded-theme"> 
      <div class="pa-4">
      <h5>Versiunea 1</h5>
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
      <v-list-subheader v-if="all_ptcs.length > 1" class="">Persoane</v-list-subheader>
      <v-btn-toggle v-if="all_ptcs.length > 1"
        v-model="selected_ptcs"
        multiple
        class="flex-wrap rounded-0 mb-4"
        dark style="gap:10px; height:auto;"
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
      <v-btn-toggle v-if="all_routes.length > 1"
        v-model="selected_routes"
        multiple
        class="flex-wrap rounded-0 mb-4"
        dark style="gap:10px; height:auto;"
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
          // console.warn('ticked');
          console.warn('ticked', newValue)
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
        return v == 'ALL,' && '- Toti pasagerii -' || (translate_ptc_short[v.split(',')[0]][0])
      },
      translatePtcWithIndex(v){
        return v == 'ALL,' && '- Toti pasagerii -' || (translate_ptc_short[v.split(',')[0]][0] + ' ' + (1 + parseInt(v.split(',')[1])))
      },
      isIncluded(s){
        var inc = s.Included;
        return '1' === inc || 'true' === inc || 1 === inc || true === inc;
      },
      reloadNodes(){
        this.nodes = [];
        // console.warn('selected_ptcs', this.selected_ptcs);
        // console.warn('selected_routes', this.selected_routes);
        var optional_services = this.optional_services;
        if(!this.with_included){
          optional_services = optional_services.filter(s => !this.isIncluded(s));
        }
        console.warn('optional_services', optional_services);

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
            id: category_label,
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
              id: category_label + '-' + service_label,
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
                id: category_label + '-' + service_label + '-' + option_label,
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
                    id: category_label + '-' + service_label + '-' + option_label + '-' + ptc_name,
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
                  var option = s.Options.Option.find(o => o.Description && o.Description[0] == option_name);
                  var formatted_price = (this.isIncluded(s) ? 'inclus' : (option.Price? '' + format_price(option.Price.Amount, option.Price.Currency)  + '' : '') );
                  var node5 = {
                    id: [ s.BookingCode, option.Code ].toString(),
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
                id: category_label + '-' + service_label + '-' + option_label,
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
                    id: category_label + '-' + service_label + '-' + option_label + '-' + ptc_name,
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
                    id: category_label + '-' + service_label + '-' + option_label + '-' + ptc_name + '-' + route_da,
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
        // console.warn(this.expanded);
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
        console.warn('nodes', this.nodes);
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
        this.reloadNodes();
      }
		}, 
    // END Versiunea1

    'VersiuneaDeBaza' : {
			props: {
				optional_services: {
					type: Array,
				},
			},
			template : `
    <div> 
      <h5>Versiunea de baza</h5>
    <div v-for="optionalService in optional_services" class="mb-4">
        <div>Route: {{ optionalService.Route }}</div>
        <div>BookingCode: {{ optionalService.BookingCode }}</div>
        <div>Service: {{ optionalService.Service }}</div>
        <div>Included: {{ optionalService.Included }}</div>
        <div>Target: {{ optionalService.Target }}</div>
        <div>PassengerIndex: {{ optionalService.PassengerIndex }}</div>
        <div>Options: </div>
        <div v-for="option in getObjectDotPathValue(optionalService,'Options.Option',[])||{} " class="pl-4">
          <div>{{ option }}</div>
        </div>  
    </div>  
    </div>`,
		},
  },
	data: () => ({
		optional_services: [],
		airports_by_code: {},
	}),
	template : `
    <Versiunea1 :optional_services="optional_services" :airports_by_code="airports_by_code" />
    <VersiuneaDeBaza :optional_services="optional_services" />
	`,
  methods: {
  },
  created: function() {
    // console.warn('flight_data', flight_data);
    getObjectDotPathValue(flight_data, 'Routes.*.Segment.*').flat(1).forEach(s => {
      ['Origin', 'Destination'].forEach(t => {
        if(!s[t] || !s[t].Airport) return;
        if(!this.airports_by_code[s[t].Airport.Code]){
          this.airports_by_code[s[t].Airport.Code] = s[t].Airport;
        }
      })
    });
    // console.warn('airports_by_code', this.airports_by_code);
		this.optional_services = getObjectDotPathValue(flight_data, 'OptionalServices.OptionalService', []) || [];
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
	  // console.warn('this.optional_services', this.optional_services);

    // this.optional_services_ptc = 
  },
  watch: {
  }
}
