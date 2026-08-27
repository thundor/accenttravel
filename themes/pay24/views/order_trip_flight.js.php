export default {
	data: () => ({
    flight: undefined,
    prices: undefined,
    segments: {},
    selected_location_index: undefined,
  }),
	template : `
<div class="flight-order order-details" v-if="flight">
	<div class="px-4">
		<div class=" d-flex justify-space-between align-center">
			<v-list-subheader>Zbor {{ flight.FlightType == '1' ? 'Dus - Intors' : ''}}</v-list-subheader>

			<strong class="text-primary">{{ flight.Routes[0].OriginCityName }} - {{ flight.Routes.filter(v => v.RouteType == flight.Routes[0].RouteType).reverse()[0].DestinationCityName }}</strong>
		</div>
	</div>

	<div class="flight-summary bg-background rounded-theme pa-4">
		<div class="v-list-item-title d-flex justify-space-between"><strong>Nr. confirmare:</strong><strong class="text-primary">{{ flight.ConfirmationNo }}</strong></div>
		<div class="v-list-item-title d-flex justify-space-between"><strong>ID rezervare:</strong><strong class="text-primary">{{ flight.ReservationId }}</strong></div>
		<div class="v-list-item-title d-flex justify-space-between"><strong>Numar pasageri:</strong><strong class="text-primary">{{ flight.Passengers.length }}</strong></div>
		<?php /* <div><strong>StatusMessage: {{ flight.StatusMessage }}</strong></div> */ ?>
		<div class="v-list-item-title d-flex justify-space-between"><strong>Cost total:</strong><strong class="text-primary">{{ format_price(flight.Amount, flight.Currency) }}</strong></div>
		<div class="pl-4 d-flex justify-space-between" v-if="prices.base > 0"><span>Cost baza:</span><span>{{ format_price(prices.base, flight.Currency) }}</span></div>
		<div class="pl-4 d-flex justify-space-between" v-if="prices.optionsPrice > 0"><span>Cost optiuni:</span><span>{{ format_price(prices.optionsPrice, flight.Currency) }}</span></div>
		<div class="pl-4 d-flex justify-space-between" v-if="prices.seatsPrice > 0"><span>Cost locuri:</span><span>{{ format_price(prices.seatsPrice, flight.Currency) }}</span></div>
	</div>

	<v-list class="w-100 py-0" :lines="false">
		<v-list-item v-for="(route, routeLegSegmentIndex) in (segments||[])" class="bg-background rounded-theme text-left mt-4">
			<div v-if="route.FlightStopTime" class="v-theme--dark bg-surface rounded-theme mb-4 mt-3 py-2 text-center d-flex justify-space-between px-4">
				Escala: {{ minutesToFormattedDuration(route.FlightStopTime) }} <v-icon icon="mdi-clock"></v-icon>
			</div>
			<div class="d-flex w-100 mt-2 mb-4">
				<div class="d-flex flex-column pe-4 align-center justify-space-between pb-1">
					<v-icon :icon="'0' == route.RouteType ? 'mdi-airplane-takeoff' : 'mdi-airplane-landing'"></v-icon>
					<img v-if="airlines[route.CarrierMarketingCode]" :src="airlines[route.CarrierMarketingCode].img" style="max-width: 50px;max-height: 100%;object-fit: contain;height: 30px;background: #fff;padding: 2px;" />
				</div>
				<div class="flex-grow-1">
					<v-list-item-title>
						<div class="text-wrap">
							<strong v-html="route.OriginCityName + ' (' + route.OriginAirportCode + ')'"></strong>
							<small v-if="route.OriginTerminal"> (Terminal {{ route.OriginTerminal }})</small>
							-
							<strong v-html="route.DestinationCityName + ' (' + route.DestinationAirportCode + ')'"></strong>
							<small v-if="route.DestinationTerminal"> (Terminal {{ route.DestinationTerminal }})</small>
						</div>

						<div>
							<small v-html="route.OriginAirportName"></small> - <small v-html="route.DestinationAirportName"></small>
						</div>
						<div class="text-wrap">
							<small class="">{{ formatDateDM(route.OriginDate) }}</small> <small class="">{{ minutesToDuration(route.OriginTime) }}</small> - <small class="">{{ formatDateDM(route.OriginDate) }}</small> <small class="">{{ minutesToDuration(route.DestinationTime) }}</small>
						</div>
					</v-list-item-title>
					<v-list-item-subtitle>
						<strong v-html="route.FlightCabinType"></strong>
						<div>
							<small v-html="route.CarrierMarketingName"></small> - <small v-html="route.AircraftName + ' (nr.' + route.FlightNumber + ')'"></small>
						</div>


					</v-list-item-subtitle>
				</div>
			</div>
			<div v-if="route.IncludedServices" class="pb-2">
				<strong class="ps-0 text-primary pb-2">Beneficii incluse</strong>
				<div v-for="(optionalServices, departurearrival) in route.IncludedServices">
					<?php /* <strong class="color-dark-light">{{ departurearrival }}</strong> */ ?>
					<div v-for="optionalService in optionalServices" class="">
						{{ capitalizeWords(optionalService.Name, 1) }}
					</div>
				</div>
			</div>
			<div v-if="route.PassengerOptions" class="pb-2">
				<strong class="ps-0 text-primary pb-2">Beneficii / optiuni pasageri</strong>
				<div v-for="(passengerOptions, passengerIndex) in route.PassengerOptions">
					<ol>
						<li v-for="passenger in [flight.Passengers[passengerIndex]]">
							<span v-text="passenger.FirstName + ' ' + passenger.LastName"></span>
							<div v-if="passengerOptions.seat" class="pl-4">
								Loc: <span class="text-primary" v-if="passengerOptions.seat.PREFERENCE">{{ translate_seat[passengerOptions.seat.PREFERENCE] }}</span>
								<span class="text-primary">{{ passengerOptions.seat.NUMBER }}{{ passengerOptions.seat.CODE }}</span>
							</div>
							<div v-if="passengerOptions.paidSeat" class="pl-4">
								Loc: <span class="text-primary">{{ passengerOptions.paidSeat.SeatNumber }}{{ passengerOptions.paidSeat.SeatColumn }}</span> <small class="color-dark-light" v-if="passengerOptions.paidSeat.Amount">({{ format_price(passengerOptions.paidSeat.Amount, passengerOptions.paidSeat.Currency) }})</small>
							</div>
							<div v-if="passengerOptions.optionalService" class="pl-4">
								<div v-for="(optionalServices, departurearrival) in passengerOptions.optionalService">
									<?php /* <strong class="color-dark-light">{{ departurearrival }}</strong> */ ?>
									<div v-for="optionalService in optionalServices" class="">
										{{ capitalizeWords(optionalService.Name, 1) }} <small class="color-dark-light">({{ getObjectDotPathValue(optionalService,'Included', false) && 'Inclus' || format_price(optionalService.Amount, optionalService.Currency) }})</small>
									</div>
								</div>
							</div>
						</li>
					</ol>
				</div>
			</div>
		</v-list-item>
	</v-list>
	<v-list-subheader class="pl-4">Pasageri</v-list-subheader>
	<div class="passengers bg-background rounded-theme pa-4 mb-4">
		<div v-for="(k1, k2) in {
				'ADT': 'Adults',
				'SEN': 'Seniors',
				'CHD': 'Children',
				'INF': 'Infants',
				'INS': 'InfantsWithSeats',
				'YTH': 'Youths',
			}">
			<div v-if="'0' != flight[k1]">
				<strong>{{ translate_ptc[k2][1]}}</strong>
				<div v-for="passenger in flight.Passengers.filter(v => v.Type == k2)" class="pl-4">
					<strong v-text="passenger.FirstName + ' ' + passenger.LastName"></strong>
					<?php /*
						<div v-if="passenger.Details || passenger.PaidSeats">
							<div v-for="seatSegment in seatSegments(passenger)">
								
							</div>
						</div>
						*/ ?>
				</div>
			</div>
		</div>
	</div>
</div>
	`,
  methods: {
	  seatSegments(passenger){
		  
	  }
  },
  computed: {
  },
	beforeCreate: function() {
    // console.log('recent', this);
	},
	mounted: function() {
		this.flight = (order_data.trip_order.Services || []).find(v => v.Type == 'flight') || undefined;
		
		var flightPrice = parseFloat(this.flight.Amount);
		var seatsPrice = 0;
		var optionsPrice = 0;
		var PassengerOptions = {};
		if(this.flight.Passengers)
		this.flight.Passengers.forEach((passenger, passengerIndex) => {
		  if(passenger.Details)
		  for(var k in passenger.Details){
			  var m;
			  // console.log(passenger, k, k.match(/^SEAT:ROUTE_(\d+)_(\d+):(\w+)/));
			  if(m = k.match(/^SEAT:ROUTE_(\d+)_(\d+):(\w+)/)){
				  PassengerOptions[[m[1], m[2]]] = PassengerOptions[[m[1], m[2]]] || {};
				  PassengerOptions[[m[1], m[2]]][passengerIndex] = PassengerOptions[[m[1], m[2]]][passengerIndex] || {};
				  PassengerOptions[[m[1], m[2]]][passengerIndex]['seat'] = PassengerOptions[[m[1], m[2]]][passengerIndex]['seat'] || {};
				  PassengerOptions[[m[1], m[2]]][passengerIndex]['seat'][m[3]] = passenger.Details[k];
			  }
		  }
		  
		})
		
		var IncludedServices = {};
	  if(this.flight.BrandDetails && this.flight.BrandDetails.length)
	  this.flight.BrandDetails.forEach((BrandDetail, RouteType) => {
		  var m;
		  var departurearrival = BrandDetail.Departure + '-' + BrandDetail.Arrival;
		  var routes = this.flight.Routes.filter(r => r.RouteType == RouteType);
		  
		  if(BrandDetail.Services)
		  BrandDetail.Services.forEach((service) => {
				if(service.ChargeType != 'included'){
					return;
				}
				routes.forEach(route => {
					IncludedServices[[route.RouteType, route.SegmentId]] = IncludedServices[[route.RouteType, route.SegmentId]] || {};
					IncludedServices[[route.RouteType, route.SegmentId]][departurearrival] = IncludedServices[[route.RouteType, route.SegmentId]][departurearrival] || [];
					IncludedServices[[route.RouteType, route.SegmentId]][departurearrival].push(service);
				})
			})
	  })
	  if(this.flight.PaidSeats && this.flight.PaidSeats.length)
	  this.flight.PaidSeats.forEach((paidSeat) => {
		  var m;
		  var route = this.flight.Routes[paidSeat.LegIndex];
		  
		  PassengerOptions[[route.RouteType, route.SegmentId]] = PassengerOptions[[route.RouteType, route.SegmentId]] || {};
		  PassengerOptions[[route.RouteType, route.SegmentId]][paidSeat.PassengerIndex] = PassengerOptions[[route.RouteType, route.SegmentId]][paidSeat.PassengerIndex] || {};
		  PassengerOptions[[route.RouteType, route.SegmentId]][paidSeat.PassengerIndex]['paidSeat'] = PassengerOptions[[route.RouteType, route.SegmentId]][paidSeat.PassengerIndex]['paidSeat'] || {};
		  PassengerOptions[[route.RouteType, route.SegmentId]][paidSeat.PassengerIndex]['paidSeat'] = paidSeat;
		  
		  seatsPrice+=parseFloat(paidSeat.Amount||0);
	  })
	  
	  if(this.flight.OptionalServices && this.flight.OptionalServices.length)
	  this.flight.OptionalServices.forEach((optionalService) => {
		  var m;
		  var departurearrival = optionalService.Departure + '-' + optionalService.Arrival;
		  var start_route_index = this.flight.Routes.findIndex(r => optionalService.Departure == r.OriginAirportCode);

		  if(-1 == start_route_index){
			start_route_index = this.flight.Routes.findIndex(r => optionalService.Departure == r.DestinationAirportCode);
		  }

		  var start_route = this.flight.Routes[start_route_index];

		  if(optionalService.Departure == optionalService.Arrival){
			routes = [start_route];
		  } else {
			  var end_route_index = this.flight.Routes.findIndex((r) => r.RouteType == start_route.RouteType && optionalService.Arrival == r.DestinationAirportCode);
	
			  var routes = this.flight.Routes.slice(start_route_index, end_route_index + 1);
			  // console.error(routes, departurearrival, start_route, end_route_index, optionalService.Arrival);
		  }


		  var os = { ... optionalService};
		  var included = !optionalService.Amount || getObjectDotPathValue(optionalService,'Included', false);
		  if(!included && routes.length > 1){
			os.Amount = parseFloat(os.Amount / routes.length).toFixed(2);
		  }
		  routes.forEach((route) => {
			  if(included){
				var is_general = (getObjectDotPathValue(IncludedServices[[route.RouteType, route.SegmentId]],'*.*')||[]).flat(1).find(v => v.CategoryName == optionalService.CategoryName && v.Name == optionalService.Name);
				if(is_general)
					return;
			  }
			  PassengerOptions[[route.RouteType, route.SegmentId]] = PassengerOptions[[route.RouteType, route.SegmentId]] || {};
			  PassengerOptions[[route.RouteType, route.SegmentId]][optionalService.PassengerIndex] = PassengerOptions[[route.RouteType, route.SegmentId]][optionalService.PassengerIndex] || {};
			  PassengerOptions[[route.RouteType, route.SegmentId]][optionalService.PassengerIndex]['optionalService'] = PassengerOptions[[route.RouteType, route.SegmentId]][optionalService.PassengerIndex]['optionalService'] || {};
			  PassengerOptions[[route.RouteType, route.SegmentId]][optionalService.PassengerIndex]['optionalService'][departurearrival] = PassengerOptions[[route.RouteType, route.SegmentId]][optionalService.PassengerIndex]['optionalService'][departurearrival] || [];
			  PassengerOptions[[route.RouteType, route.SegmentId]][optionalService.PassengerIndex]['optionalService'][departurearrival].push(os);
		  })
		  
		  optionsPrice+=parseFloat(optionalService.Amount||0);
	  })

	  
	  
		this.segments = {};
		if(this.flight && this.flight.Routes){
			this.flight.Routes.forEach((route) => {
				this.segments[[route.RouteType, route.SegmentId]] = {...route, PassengerOptions: PassengerOptions[[route.RouteType, route.SegmentId]], IncludedServices: IncludedServices[[route.RouteType, route.SegmentId]]}
			});
		}
		
		this.prices = {
			base: (flightPrice - optionsPrice - seatsPrice).toFixed(2),
			optionsPrice: optionsPrice.toFixed(2),
			seatsPrice: seatsPrice.toFixed(2),
		};
		
		console.warn('segments', this.segments)
	}
}
