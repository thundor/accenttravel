export default {
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
  emits: ['save', 'seat_map', 'research'],
  props: {
      referenceDate: {
          type: Date,
          default: () => (new Date()),
      },
      passengers: {
          type: Array,
          default: () => ([]),
      },
	  inspection: {
          type: Object,
          default: () => ({}),
      },
	  data: {
          type: Object,
          default: () => ({}),
      },
	  flight_data: {
          type: Object,
          default: () => (undefined),
      },
  },
	data: () => ({
    loading: {},
    seatSegment: undefined,
    seatSegments: {},
    selectedSeat: undefined,
    selected_seat: '',
    seats_requests: {},
    ptc_seats: {},
    selected_seats: {},
    dialog: false,
    type: 'ADT',
    cnt: 0,
    routeIndex: 0,
    segmentIndex: undefined,
    errors: [],
    texts: Object.freeze({
    }),
    validations:Object.freeze([
    ]),
  }),
	template : `
<v-dialog v-model="dialog" class="custom-dialog flight-dialog dialog-loc-in-avion">
	<template v-slot:default="{ isActive }">
		<v-card class="align-self-center" style="max-width: min(95vw, 630px);width:630px">
		<v-card-title>Loc in avion</v-card-title>
		<v-card-text class="max-height overflow-y-auto px-0">
      <v-list theme="light" class="d-flex flex-column text-left" rounded="theme">
        <v-messages class="pl-4" color="error" :active="!!errors.length" :messages="errors"></v-messages>
        <div class="plane-wrapper">
        <template v-if="undefined === segmentIndex && flight_data.Routes[routeIndex].Segment.length > 1">
          <v-list-item class="text-left" theme="light" v-for="(segment, segIndex) in flight_data.Routes[routeIndex].Segment" @click="segmentIndex = segIndex">
            <strong v-text="segment.Origin.Airport.City + ' (' + segment.Origin.Airport.Code + ')'"></strong> - <strong v-text="segment.Destination.Airport.City + ' (' + segment.Destination.Airport.Code + ')'"></strong>
            <template v-if="ptc_seats[[type, cnt]] && ptc_seats[[type, cnt]][routeIndex] && ptc_seats[[type, cnt]][routeIndex][segIndex]">
                <template v-for="(seat) in [ptc_seats[[type, cnt]][routeIndex][segIndex]]">
                  <template v-if="seat">
                    <template v-if="seat=='A'">
                      <span class="ml-4 text-primary">Culoar</span>
                    </template>
                    <template v-else-if="seat=='W'">
                      <span class="ml-4 text-primary">Fereastra</span>
                    </template>
                    <template v-else>
						<template v-if="seatSegments[[routeIndex, segIndex]]">
                      <span class="ml-4 text-primary">{{ (s = seatSegments[[routeIndex, segIndex]].SeatMap, r = s.Rows.Row[seat.split(',')[0]].Seat[seat.split(',')[1]], '' + r.Number + r.Code) + ' ' + (r.ChargeTypeReference ? (c = s.ChargeList.ChargeType.find((v) => v.Reference == r.ChargeTypeReference), c && format_price(c.Price.Amount, c.Price.Currency)) : '') }}</span>
						</template>
						<template v-else>
							Loading...
						</template>
                    </template>
                  </template>
                </template>
              </template>

            <template v-slot:append="{ item }">
              <v-icon icon="mdi-chevron-right"></v-icon>
            </template>
          </v-list-item>
        </template>
        <template v-if="undefined !== segmentIndex">
          <v-radio-group inline color="primary" v-model="selected_seat" class="ms-2 mt-2 mb-3" hide-details="true">
            <v-radio label="Indiferent" value=""></v-radio>
            <v-radio label="Culoar" value="A"></v-radio>
            <v-radio label="Fereastra" value="W"></v-radio>
          </v-radio-group>
		  <p><b>SAU</b> Alege Loc:</p>
		  <component :is="loadViewAsync('partials/presearch-wrapper/functionalities/common/loading')" v-if="loading.seats"></component>
          <template v-if="seatSegment && seatSegment.SeatMap">
            <div class="plane-body">
              <div class="plane-cockpit">
                <span></span>
                <span></span>
                
                <div class="seat-legend text-center d-flex flex-column">
                  <span class="badge badge-light badge-pill">
                    <span class="seat-legend-avail"><em></em></span>
                    Disponibil</span>
                  <span class="badge badge-light badge-pill ">
                    <span class="seat-legend-unavail"><em></em></span>
                    Indisponibil</span>
                  <span class="badge badge-light badge-pill ">
                    <span class="seat-legend-paid"><em></em></span>
                    Extra cost</span>
                  <span class="badge badge-light badge-pill ">
                    <span class="seat-legend-selected"><em></em></span>
                    Loc ales</span>
                </div>

              </div>

              <div class="d-flex flex-column justify-content-center seat-grid text-center" :class="{'minGrid' : getObjectDotPathValue(seatSegment, 'SeatMap.Rows.Row',[]).length < 16}">

                <div class="seat-row px-4 column-names">
                  <template v-for="column in getObjectDotPathValue(seatSegment, 'SeatMap.Columns.Column',[])">
                    <div class="seat-block seat-column" :class="{
                    'seat-window': !!column.Window,
                    'seat-aisle': !!column.Aisle,
                  }">{{ column.Code }}</div>
                  </template>
                </div>

                <template v-for="(seatRow, seatRowIndex) in getObjectDotPathValue(seatSegment, 'SeatMap.Rows.Row',[])">
                  <div v-if="seatRow.ExitRow" class="seat-row px-4 exit-row d-flex justify-center" :class="{
                  'seat-row-wings' : !!seatRow.OverWing,
                  'seat-row-exit' : !!seatRow.ExitRow,
                }">
                    <div class="seat-block seat-door">
                      <i class="fa fa-sign-out-alt fa-flip-horizontal"></i>
                    </div>
                    <span class="align-self-center">Exit</span>
                    <div class="seat-block seat-door">
                      <i class="fa fa-sign-out-alt"></i>
                    </div>
                  </div>
                  <div class="seat-row px-4" :class="{
                    'seat-row-wings' : !!seatRow.OverWing,
                    'seat-row-exit' : !!seatRow.ExitRow
                  }" :data-number="seatRow.Number">
                  <template v-for="colSeat in getObjectDotPathValue(seatSegment, 'SeatMap.Columns.Column',[]).reduce((cols, col, coli) => (cols.push({...col,'seatIndex': (i = getObjectDotPathValue(seatRow, 'Seat',[]).findIndex((v) => v.Code == col.Code)), 'seat': getObjectDotPathValue(seatRow, 'Seat.' + i) || {}}), cols), [])">
                      <div :seat-index="colSeat.seatIndex" class="seat-block upsell-options-servicesName" :class="{
                          'seat-window': !!getObjectDotPathValue(colSeat,'Window'),
                          'seat-aisle': !!getObjectDotPathValue(colSeat,'Aisle'),
                          'seat-avail font-custom-bold': !!colSeat.seat.Available,
                          'seat-unavail': !colSeat.seat.Available,
                          'seat-disable': !!colSeat.seat.HandicapFriendly,
                          'seat-noseat': !!colSeat.seat.NoSeat,
                          'seat-paid': !!colSeat.seat.ChargeTypeReference,
                          'seat-prefer': !!colSeat.seat.Preferential,
                          'seat-selected': !!selected_seats[[seatRowIndex, colSeat.seatIndex]],
                          'seat-active': selected_seat == [seatRowIndex, colSeat.seatIndex].join(','),
                      }" @click="colSeat.seat.Available && !colSeat.seat.NoSeat && !selected_seats[[seatRowIndex, colSeat.seatIndex]] && (selected_seat = [seatRowIndex, colSeat.seatIndex].join(','))" :data-row-number="seatRow.Number">
                        <template v-if="colSeat.seat.Available">
                          <?php /*
                          <span class="rounded count-1" v-if="seatPtc[sc]">
                            <div><i class="fa fa-check"></i>
                              <span class="pl-2">{{ format_ptc(seatPtc[sc].split(',').slice(-2)) }}</span>
                            </div>
                          </span>
                          */ ?>
                          <template v-if="colSeat.seat.HandicapFriendly">
                            <i class="fa fa-wheelchair handicap-friendly"></i>
                          </template>
                          <?php /*
                          <template v-if="colSeat.seat.ChargeTypeReference">
                            <i class="fa fa-dollar paid-seat"></i>
                          </template>
                          */ ?>
                          <template v-if="colSeat.seat.Preferential">
                            <i class="fa fa-exclamation preferential-seat"></i>
                          </template>
                        </template>
                        <template v-else>
                          <i class="fa fa-ban"></i>
                        </template>
                      </div>
                  </template>
                  </div>
                </template>

              </div>

              <div class="plane-end"></div>

            </div>
          </template>
          <template v-else-if="!loading.seats">
			<p>Din pacate, pe acest zbor nu se pot alege locuri.</p>
          </template>
        </template>
        </div>
      </v-list>
	  </v-card-text>
			<v-card-actions>
				<v-spacer></v-spacer>

				<v-btn class="text-none font-weight-normal cancel-button" size="large" variant="outlined" rounded="theme" @click="(undefined === segmentIndex || flight_data.Routes[routeIndex].Segment.length == 1) && (dialog = false) || (segmentIndex = undefined)"><v-icon icon="mdi-arrow-left"></v-icon> Inapoi</v-btn>
				<v-btn v-if="undefined !== segmentIndex" class="d-flex text-capitalize font-weight-normal" style="flex:1;" variant="outlined" size="large" :color="true ? 'primary' : 'secondary'" rounded="theme" @click="validateAndSave() && flight_data.Routes[routeIndex].Segment.length == 1 && (dialog = false) || (segmentIndex = undefined)">
				  Confirma
				  <template v-if="selectedSeat">
				  {{ selectedSeat.seatNumber + selectedSeat.seatColumn + ' (' + format_price(selectedSeat.amount, selectedSeat.currency) + ')' }}
				  </template>
				</v-btn>
			  </v-card-actions>
			
		</v-card>
		</template>
</v-dialog>
	`,
	mounted: function() {
	},
  methods: {
    refresh(research_result){
		this.inspection.checking = this.inspection.checking || {};
		this.inspection.checking.seats = true;
		
		var paidSeats = research_result.paidSeats;
		console.warn('this.flight_data!', this.flight_data);
		
		var flights_to_load = {};
		Object.keys(research_result?.serviceData?.passenger || {}).forEach((ptc) => {
			(research_result.serviceData.passenger[ptc] || []).forEach(((passenger, ptc_index) => {
				if(passenger.details){
					var pseats = {};
					Object.keys(passenger.details).forEach((detail_key) => {
						var val = passenger.details[detail_key];
						if(/^SEAT:ROUTE_/.test(detail_key)){
							var dk = detail_key.replace(/^SEAT:ROUTE_/, '');
							var da = dk.split(':');
							var rk = da[0].split('_');
							pseats[rk] = pseats[rk] || {};
							pseats[rk][da[1]] = val;
						}
					});
					if(Object.keys(pseats).length){
						Object.keys(pseats).forEach((sk) => {
							var rk = sk.split(',');
							var routeIndex = (this.flight_data?.Routes || []).findIndex(r => r.Index == rk[0]);
							var segment_key = [routeIndex, rk[1]].join(',');
							var pseat = pseats[sk];
							var selected_seat = {
								Route: {
									From: pseat.ORIGIN,
									To: pseat.DESTINATION,
								},
								Target: ptc,
								PassengerIndex: ptc_index,
							};
							if(undefined !== pseat.CODE && undefined !== pseat.NUMBER){
								selected_seat['seatNumber'] = pseat.NUMBER;
								selected_seat['seatColumn'] = pseat.CODE;
							} else if(pseat.PREFERENCE){
								selected_seat['preference'] = pseat.PREFERENCE;
							} else {
								return;
							}
							flights_to_load[segment_key] = flights_to_load[segment_key] || [];
							flights_to_load[segment_key].push(selected_seat);
						})
					}
				}
			}));
		});
		if(paidSeats && paidSeats.length){
			paidSeats.forEach(paidSeat => {
				var segment_key = [paidSeat.legIndex, paidSeat.segmentIndex].join(',');
				var selected_seat = paidSeat;
				flights_to_load[segment_key] = flights_to_load[segment_key] || [];
				flights_to_load[segment_key].push(selected_seat);
			})
		}
		
	  var segment_keys = Object.keys(flights_to_load);
	  if(segment_keys.length){
		  console.warn('flights_to_load', flights_to_load);
		  var invalidSeats = [];
		  var fetches = [];
		  segment_keys.forEach(segment_key => {
			  var a = segment_key.split(',');
			  fetches.push(this.loadFlightSeats(a[1], a[0]).then(response => {
				  var checks = flights_to_load[segment_key];
				  checks.forEach(paidSeat => {
					  var newSeat = '';
					  if(paidSeat.preference){
						  newSeat = paidSeat.preference;
					  } else {
						  (this.seatSegments?.[a]?.SeatMap?.Rows?.Row || []).find((row, rowIndex) => {
							  return row?.Number === paidSeat?.seatNumber 
								&& (row?.Seat || []).find((seat, seatIndex) => {
									return seat.Available
										&& !seat.NoSeat
										&& seat.Code === paidSeat.seatColumn
										&& (newSeat = [rowIndex,seatIndex].join(','));
								})
						  });
						  if(!newSeat){
							  console.error('Invalidat loc in zbor: ' + (' Ruta: ' + (parseInt(a[0]))) + (' Segment: ' + (parseInt(a[1]))) + ((' Loc: ' + paidSeat.seatNumber + '-' + paidSeat.seatColumn)|| ' - necunoscut -'), paidSeat);
							  invalidSeats.push('Invalidat loc in zbor: ' + (' Ruta: ' + (parseInt(a[0]))) + (' Segment: ' + (parseInt(a[1]))) + ((' Loc: ' + paidSeat.seatNumber + '-' + paidSeat.seatColumn)|| ' - necunoscut -'));
						  } else {
							  console.log('ALES loc in zbor: ' + (' Ruta: ' + (parseInt(a[0]))) + (' Segment: ' + (parseInt(a[1]))) + ((' Loc: ' + paidSeat.seatNumber + '-' + paidSeat.seatColumn)|| ' - necunoscut -'), newSeat, paidSeat);
						  }
					  }
					  this.$emit('save', paidSeat.Target, paidSeat.PassengerIndex, a[0], a[1], newSeat);
				  });
				  return response;
			  }));
		  });
		  return Promise.all(fetches).then(responses => {
			  console.warn('Refreshed all', invalidSeats);
			  this.inspection.checked = this.inspection.checked = this.inspection.checked || {};
			  this.inspection.checked.seats = invalidSeats;
			  if(invalidSeats.length){
				  this.data.step = this.data.step > 2 ? 2 : this.data.step;
			  }
		  }).catch(e => {
			  console.error(e);
			  this.data.step = 0;
		  }).finally(()=>{
			if(this.inspection?.checking)
			this.inspection.checking.seats = false;
		  })
	  } else {
		if(this.inspection?.checking)
			this.inspection.checking.seats = false;
	  }
    },
    selectSeat(rowi, seati){
      console.warn('Selected Seat', rowi, seati);
    },
    save(){
      console.warn('save seat assignment');
      this.$emit('save', this.type, this.cnt, this.routeIndex, this.segmentIndex, this.selected_seat);
      return true;
      // emit
    },
    reset(){
      this.seats_requests = {};
      this.seatSegments = {};
      this.selected_seats = {};
    },
    validateAndSave(){
      this.save();
      return true;
    },
    loadFlightSeats(d, routeIndex) {
        var fc = getObjectDotPathValue(this.flight_data, 'FlightsCode');
        var it = getObjectDotPathValue(this.flight_data,'ItineraryCode');
        var a = getObjectDotPathValue(this.flight_data, 'Routes.' + routeIndex + '.Segment.' + d + '.Origin.Airport.Code');
        var b = getObjectDotPathValue(this.flight_data, 'Routes.' + routeIndex + '.Segment.' + d + '.Destination.Airport.Code');
        var c = getObjectDotPathValue(this.flight_data, 'Routes.' + routeIndex + '.Index');
		return (this.seats_requests[[fc, it, a, b, c, d]] || (this.seats_requests[[fc, it, a, b, c, d]] = fetch(`${newux_url}/partials/presearch-wrapper/functionalities/<?php echo basename(dirname($a)); ?>/seats.json?${append_url}&code=` + getObjectDotPathValue(this.flight_data, 'FlightsCode') + '&itinerary_code=' + getObjectDotPathValue(this.flight_data,'ItineraryCode') + '&ocode=' + a + '&dcode=' + b + '&rindex=' + c + '&pseat=' + 1).then(response => response.json()).then(response => {
				console.warn('loadSeats', response);
				if (!response) {
					throw "Could not load";
				}
			  this.seatSegments[[routeIndex, d]] = {...response, rindex: routeIndex, sindex: d};
			  this.$emit('seat_map', routeIndex, d, response.SeatMap);
			  return response;
		  }).catch(e => {
			  this.$emit('research');
				console.error('Could not load the seats', e);
		  })));
	},
    loadSeats(d, routeIndex) {
		if(undefined === routeIndex){
			routeIndex = this.routeIndex;
		}
        console.warn('should loadSeats');
        this.seatSegment = undefined;
        this.loading.seats = true;
		var c = getObjectDotPathValue(this.flight_data, 'Routes.' + routeIndex + '.Index');
				return this.loadFlightSeats(d, routeIndex).then((a) => {
					this.seatSegment = this.seatSegments[[routeIndex, d]];
				}).finally(() => {
				  this.loading.seats = false;
				});
			},
  },
  computed: {
  },
  watch:{
    'flight_data': {
      handler(newValue, oldValue){
        var research_result = this.inspection?.research_hash?.flight?.result;
		console.warn('INSPECTION HASH:', research_result);
		
		if(research_result){
			this.refresh(research_result);
		}
      },
      immediate: true,
    },
    'selected_seat': {
      handler(newValue, oldValue){
        this.selectedSeat = undefined;
        if(newValue){
          var sa = newValue && newValue.split(',') || [];
          var sm = this.seatSegments[[this.routeIndex, this.segmentIndex]];
          console.warn('sm', sm, this.seatSegments, this.routeIndex, this.segmentIndex);
          var seat = sm && sa.length > 1 && sm.SeatMap && sm.SeatMap.Rows.Row[sa[0]].Seat[sa[1]];
          if(sm && seat.ChargeTypeReference){
            var charge = sm.SeatMap.ChargeList.ChargeType.find((v) => v.Reference == seat.ChargeTypeReference);
            this.selectedSeat = {
              seatColumn: seat.Code,
              seatNumber: seat.Number,
              amount: charge.Price.Amount,
              currency: charge.Price.Currency,
            };
          }
        }
      },
      immediate: true,
    },
    'segmentIndex': {
      handler(newValue, oldValue){
        var ptc_key = [this.type, this.cnt].join(',');
        var segment_key = [this.routeIndex, newValue].join(',');
        this.selected_seats = Object.keys(this.ptc_seats).filter(v => v != ptc_key).reduce((o, p) => (this.ptc_seats[p][this.routeIndex] && this.ptc_seats[p][this.routeIndex][newValue] && (o[this.ptc_seats[p][this.routeIndex][newValue]] = segment_key), o), {});
        this.selected_seat = (this.ptc_seats[ptc_key] && this.ptc_seats[ptc_key][this.routeIndex] || {})[newValue] || '';

        console.warn('segmentIndex', ptc_key, segment_key, this.ptc_seats, this.selected_seats, this.selected_seat);
        if(undefined !== newValue){
          this.loadSeats(newValue)
        }
      },
      immediate: true,
    },
    'modelValue': {
      handler(newValue, oldValue){
        
      },
    },
    'dialog': {
      handler(newValue, oldValue){
        console.warn('this.segmentIndex', this.segmentIndex, this.routeIndex, this.flight_data);
        if(!newValue){
          this.segmentIndex = undefined;
          return;
        }
        var ptc_key = [this.type, this.cnt].join(',');
        if(this.flight_data && 1 == this.flight_data.Routes[this.routeIndex].Segment.length){
          this.segmentIndex = 0;
        }
        console.warn(this.segmentIndex);
        console.warn('openend seat assignment', this);
      },
      // immediate: true,
    },
  }
}
