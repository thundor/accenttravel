<?php defined('ENVIRONMENT') or die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<h3 class="subSecTicket mt-1 col-12">Extra detalii</h3>
<style>
.nav-tabs .nav-item.show .nav-link, .nav-tabs .nav-link.active {
    background-color: #E8F0FE;
}
</style>
<script src="https://unpkg.com/vue@3"></script>
<script src="https://unpkg.com/smooth-scroll-into-view-if-needed@1.1.33/umd/smooth-scroll-into-view-if-needed.min.js"></script>
<div class="boxFlightUpsell mb-0 bb0 col-12 px-0">
	<?php
	$this->_ci->load->model('Flights_model');
	$this->_ci->load->helper('currency');
	$this->_ci->load->helper('arr');
	$upsell = null;
	if (Arr::get($this->view_data, 'flight_details.UpsellSupport', 0)) {
		$upsell = $this->_ci->Flights_model->loadFlightUpsell($this->view_data['code'], $this->view_data['itinerary_code']);
	}
	?>
<?php if(in_array('FR', array_column($this->view_data['flight_details']->companies, 'code'))){ ?>
<button class="btn btn-warning mt-4" data-toggle="modal" data-target="#modal_flight_ryanair">Politica Ryanair</button>
<div class="modal fade" id="modal_flight_ryanair" tabindex="-1" role="dialog" aria-hidden="true" data-keyboard="false">
  <div class="d-flex flex-column justify-content-center" style="height:100%;">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header text-uppercase">Important!</div>
        <div class="modal-body p-0">
		  <div class="p-4">
			<p>Conform politicii adoptate de compania aeriena <b class="text-danger">Ryanair</b>, orice operatiune post-booking, voluntara sau involuntara, trebuie efectuata exclusiv de catre pasager pe site-ul companiei aeriene.</p>
			<p>Agentiile de turism, prin intermediul carora ati achizitionat biletele on-line sau off-line, nu au abilitatea de a procesa operatiuni de modificare a zborului si/sau de procesare a cererii de anulare si rambursarea sumelor avansate pentru achizitionarea biletelor de avion, in cazul zborururilor anulate din initiativa companiei, indiferent de cauza care a stat la baza anularii.</p>
			<p><span>Asadar, sumele de bani avansate pentru cumpararea biletelor in cazul zborurilor anulate din initiativa companiei aeriene Ryanair nu pot fi recuperate de la agentia de turism.</span></p>
			<p>Pentru mai multe informatii despre termenii si politica Ryanair va rugam sa accesati <a href="https://www.ryanair.com" target="_BLANK">www.ryanair.com.</a></p>
		  </div>
        </div>
		<div class="modal-footer">
			<button class="btn btn-success modal_flight_ryanair_button" type="button"" data-dismiss="modal" aria-label="Inchide" disabled>Am inteles <span class="modal_flight_ryanair_timer">(7)</span></button>
		</div>
      </div>
    </div>
  </div>
</div>
<?php } ?>
	<div id="flight-upsell">
		<input type="hidden" required name="vuesubmittable" :value="submittable ? 1 : ''">
		<input v-if="upsell" type="hidden" form="bookingCheckout" name="upsellCode" :value="upsell.Code" />
		<input type="hidden" form="bookingCheckout" name="expectedFlightPrice" :value="finalPrice.toFixed(2) + '' + pv('flight.Currency')" />
		<template v-for="(selectedBookingCode, sbc_index) in selectedBookingCodes2">
			<input type="hidden" form="bookingCheckout" :name="'optionalServices[' + sbc_index + '][bookingCode]'" :value="selectedBookingCode.replace(/(.*?)\:.*/,'$1')" />
			<input type="hidden" form="bookingCheckout" :name="'optionalServices[' + sbc_index + '][selectedOptionCode]'" :value="selectedBookingCode.replace(/.*?\:/,'')" />
		</template>
		<template v-for="(p, passengerIndex) in ptcArr" v-if="(paidIndex = 0,1)">
			<template v-for="route in pv('flight.Routes',[])">
				<template v-for="(segment, segmentIndex) in opv(route,'Segment',[])">
					<template v-if="(seatDetails = ptcSeat[[route.Index, segmentIndex,opv(segment, 'Origin.Airport.Code'), opv(segment, 'Destination.Airport.Code'),p]] && seatSegments[[route.Index, segmentIndex, opv(segment, 'Origin.Airport.Code'), opv(segment, 'Destination.Airport.Code')]] && getSeatDetails(seatSegments[[route.Index, segmentIndex, opv(segment, 'Origin.Airport.Code'), opv(segment, 'Destination.Airport.Code')]], ptcSeat[[route.Index, segmentIndex,opv(segment, 'Origin.Airport.Code'), opv(segment, 'Destination.Airport.Code'),p]])) && seatDetails.charge && ++paidIndex">
						<input type="hidden" form="bookingCheckout" :name="'paidSeats[' + (paidIndex-1) + '][passengerIndex]'" :value="passengerIndex" />
						<input type="hidden" form="bookingCheckout" :name="'paidSeats[' + (paidIndex-1) + '][segmentIndex]'" :value="segmentIndex" />
						<input type="hidden" form="bookingCheckout" :name="'paidSeats[' + (paidIndex-1) + '][legIndex]'" :value="route.Index" />
						<input type="hidden" form="bookingCheckout" :name="'paidSeats[' + (paidIndex-1) + '][seatColumn]'" :value="seatDetails.seat.Code" />
						<input type="hidden" form="bookingCheckout" :name="'paidSeats[' + (paidIndex-1) + '][seatNumber]'" :value="seatDetails.seat.Number" />
						<input type="hidden" form="bookingCheckout" :name="'paidSeats[' + (paidIndex-1) + '][amount]'" :value="seatDetails.charge.Price.Amount" />
						<input type="hidden" form="bookingCheckout" :name="'paidSeats[' + (paidIndex-1) + '][currency]'" :value="seatDetails.charge.Price.Currency" />
					</template>
					<template v-else>
						<template v-if="ptcSeat[[route.Index, segmentIndex, opv(segment, 'Origin.Airport.Code'), opv(segment, 'Destination.Airport.Code'), p]]">
							<input type="hidden" form="bookingCheckout" :name="'preferredSeats[' + p[0] + '][' + p[1] + '][details][SEAT:ROUTE_' + route.Index + '_' + segmentIndex + ':ORIGIN]'" :value="opv(segment, 'Origin.Airport.Code')" />
							<input type="hidden" form="bookingCheckout" :name="'preferredSeats[' + p[0] + '][' + p[1] + '][details][SEAT:ROUTE_' + route.Index + '_' + segmentIndex + ':DESTINATION]'" :value="opv(segment, 'Destination.Airport.Code')" />
						</template>
						<template v-if="seatDetails">
							<input type="hidden" form="bookingCheckout" :name="'preferredSeats[' + p[0] + '][' + p[1] + '][details][SEAT:ROUTE_' + route.Index + '_' + segmentIndex + ':NUMBER]'" :value="seatDetails.seat.Number" />
							<input type="hidden" form="bookingCheckout" :name="'preferredSeats[' + p[0] + '][' + p[1] + '][details][SEAT:ROUTE_' + route.Index + '_' + segmentIndex + ':CODE]'" :value="seatDetails.seat.Code" />
						</template>
						<template v-else-if="(s = ptcSeat[[route.Index, segmentIndex, opv(segment, 'Origin.Airport.Code'), opv(segment, 'Destination.Airport.Code'), p]])">
							<input type="hidden" form="bookingCheckout" :name="'preferredSeats[' + p[0] + '][' + p[1] + '][details][SEAT:ROUTE_' + route.Index + '_' + segmentIndex + ':PREFERENCE]'" :value="s.split(',')[4]" />
						</template>
					</template>
				</template>
			</template>
		</template>
		<div class="updagradeSeviceWrap" :class="{ 'show-map': !!(manual.seatSegmentCode || loading.seats)}">
			<h1 v-if="loading.upsells"><i class="fa fa-spinner fa-spin"></i> Se incarca optiunile de upgrade.</h1>
			<template v-else>
			<div id="upsellSection" class="borderBox" v-if="serviceKeys.length">
				<div class="d-flex justify-content-between icons-defination flex-column mt-2">
					<h3 class="d-flex fz-1 justify-content-between mb-0 ">Legenda</h3>
					<div class="py-2 d-flex align-items-center"><i class="fa fa-check mr-2"></i>
						<div>Elemente incluse <span class="d-none d-md-inline">(Servici incluse in pachetul de pret)</span></div>
					</div>
					<div class="py-2 d-flex align-items-center"><i class="fa fa-solid fa-ban mr-2"></i>Elemente indisponibile <span class="d-none d-md-inline">(nici vandabile)</span></div>
					<div class="py-2 d-flex align-items-center"><i class="fa fa-solid fa-wallet mr-2"></i>Elemente taxabile</div>
					<div class="py-2 d-flex align-items-center"><i class="fa fa-solid fa-list-check mr-2"></i>Elemente Combinate <span class="d-none d-md-inline"> - In aceasta lista de sub-servicii pot exista impreuna servicii taxabile, indisponibile si incluse in pret</span></div>
				</div>
				<div class="upsell-options-wrapper d-flex">
					<div class="bg-white mr-1 d-inline-block rounded upsell-options-group fixed">
						<div class="d-flex justify-content-around upsell-options-groupItem">
							<ul class="m-0 p-0 upsell-options-groupItem text-center">
								<li class="head p-2 bg-body text-purple">
									<span class="badge">&nbsp;</span>
									<strong class="d-block">&nbsp;</strong>
									<span class="badge badge-light">&nbsp;</span>
								</li>
								<li class="text-left">
									<ul class="m-0 p-0 upsell-options-services">
										<li class="service-icon d-flex align-items-center" v-for="serviceKey in serviceKeysAsArr" style="height:30px;">
											<span class="upsell-options-servicesName">
												<span class="rounded count-1 d-md-none">
													<div><i :class="serviceIconClass(serviceKey[1])"></i> {{ serviceKey[0] }}</div>
												</span>
												<i :class="serviceIconClass(serviceKey[1])"></i>
											</span>
											<span class="d-md-inline d-none pl-1" v-text="serviceKey[0]"></span>
										</li>
									</ul>
								</li>
							</ul>
						</div>
						<div class="bg-body d-md-block d-none fz-1_3 p-2 text-purple">
							<span class="badge text-muted">Pret de baza</span>
							<strong class="d-block" v-text="format_price(priceBase, pv('flight.Currency'))"></strong>
						</div>
					</div>
					<div class="upsell-options text-nowrap d-inline-block">
						<template v-for="upsellData in upsells">
							<div class="bg-white d-inline-block rounded upsell-options-group" :class="{'active-upgrade': isSelected('upsellServiceCode', opv(upsellData, 'Code', ''))}">
								<div class="d-flex justify-content-around upsell-options-groupItem">
									<ul v-for="(BrandDetailData, BrandDetailIndex) in ((is_same = opv(upsellData,'FareDetails.BrandedFare.BrandDetails.1')
										&& !opv(upsellData,'FareDetails.BrandedFare.BrandDetails.2')
										&& opv(upsellData,'FareDetails.BrandedFare.BrandDetails.1.From') == opv(upsellData,'FareDetails.BrandedFare.BrandDetails.0.To')
										&& opv(upsellData,'FareDetails.BrandedFare.BrandDetails.1.To') == opv(upsellData,'FareDetails.BrandedFare.BrandDetails.0.From')
										&& JSON.stringify({...opv(upsellData,'FareDetails.BrandedFare.BrandDetails.0'),'From':'', 'To':''}) == JSON.stringify({...opv(upsellData,'FareDetails.BrandedFare.BrandDetails.1'),'From':'', 'To':''})) ? [opv(upsellData,'FareDetails.BrandedFare.BrandDetails.0')] : opv(upsellData,'FareDetails.BrandedFare.BrandDetails'))" class="m-0 p-0 upsell-options-groupItem text-center">
										<li class="head pb-2 ps-3 pe-3 pt-2 text-white">
											<div class="text-capitalize mb-1" v-text="opv(BrandDetailData, 'Name').toLowerCase()"></div>
											<strong class="d-block text-nowrap mb-1" v-text="is_same ? 'Tur-Retur' : (!BrandDetailIndex ? 'Tur' : 'Retur')" :title="opv(BrandDetailData, 'From') + ' &#x2708; ' + opv(BrandDetailData, 'To')"></strong>
											<div class="text-capitalize" v-text="opv(BrandDetailData, 'Cabin', '').toLowerCase()"></div>
										</li>
										<li>
											<ul class="m-0 p-0 upsell-options-services">
												<li v-for="serviceKey in serviceKeysAsArr" class="px-0 d-flex align-items-center justify-content-center" style="height:30px;">
													{{ (services = opv(BrandDetailData,'Services',[]).filter((v) => opv(v,'CategoryName', '').toLowerCase() == serviceKey[0]), null ) }}
													<div v-if="services.length" class="upsell-options-servicesName tooltip-top d-flex flex-column" style="width:100%;">
														<span class="rounded" :class="{['count-' + services.length]:1}">
															<div v-for="service in services">
																<i class="fa fa-check" v-if="'included' == opv(service,'ChargeType')"></i>
																<i class="fa fa-solid fa-wallet" v-else-if="'chargeable' == opv(service,'ChargeType')"></i>
																<i class="fa fa-ban" v-else></i>
																{{ opv(service,'Name','').toLowerCase() }}
															</div>
														</span>
														<i class="fa fa-solid fa-list-check" v-if="services.map((a) => a.ChargeType).reduce((a,b) => (-1 == a.indexOf(b) && a.push(b), a),[]).length>1"></i>
														<i class="fa fa-check" v-else-if="'included' == opv(services,'0.ChargeType','')"></i>
														<i class="fa fa-solid fa-wallet" v-else-if="'chargeable' == opv(services,'0.ChargeType')"></i>
														<i class="fa fa-solid fa-ban" v-else-if="'notOffered' == opv(services,'0.ChargeType')"></i>
														<i class="fa fa-solid fa-home" v-else></i>
													</div>
													<span v-else>
														<i class="fa fa-ban disabled"></i>
													</span>
												</li>
											</ul>
										</li>
									</ul>
								</div>
								<div class="foot text-center">
									<span class="d-block font-custom-bold fz-1_3 mt-2 text-purple d-flex flex-column">
										{{ opv(upsellData, 'Price.Amount', 0) - pv('flight.Price',0) <= 0 ? '' : '+' }}
										{{ format_price(opv(upsellData, 'Price.Amount', 0) - pv('flight.Price',0), pv('flight.Currency')) }}
										<button :disabled="loading.ancillery" type="button" @click.prevent="upsellServiceCode = (upsellServiceCode == opv(upsellData, 'Code', '') ? null : opv(upsellData, 'Code', ''))" :data-amount="opv(upsellData, 'Price.Amount', 0) - pv('flight.Price',0)" class="btn mb-3 w-75 mx-auto" :class="{'btn-primary': isSelected('upsellServiceCode', opv(upsellData, 'Code', '')), 'btn-secondary': !isSelected('upsellServiceCode', opv(upsellData, 'Code', ''))}">{{ isSelected('upsellServiceCode', opv(upsellData, 'Code', '')) ? 'Ales' : 'Alege'}}</button>
								</div>
							</div>
						</template>
					</div>
				</div>
				<hr/>
			</div>
				<div id="basePassengerNames" class="borderBox">
					<div class="bg-white border mb-4">
						<div class=" book-form-section mb-2">
							<h3 class="d-flex fz-1 justify-content-between mb-0 mt-3 ">Identificatori pasageri
							</h3>
							<p class="mt-1 ">Introduceti numele pasagerilor in functie de tipul acestora, pentru o asociere facila</p>
							<div v-for="p in ptcArr">
								<div class="form-group row">
									<label class="control-label pt-2 col-xl-3 col-md-3 col-lg-4 col-sm-3 col-3">{{ format_ptc(p, true)}}</label>
									<div class="col-xl-9 col-md-9 col-lg-8 col-sm-9 col-9">
										<input class="form-control" v-model="ptcNames[p]" autocomplete="off" maxlength="50" />
									</div>
								</div>
							</div>
						</div>
					</div>
					<hr/>
				</div>
				<div id="ancillaryServiceWrap" class="borderBox">
					<h1 v-if="loading.ancillery"><i class="fa fa-spinner fa-spin"></i> Se incarca optiunile extra ... Va rugam sa asteptati.</h1>
					<template v-else-if="!loading.ancillery && ancillery && ancilleryServices.length">
					<div id="ancilleryCustom" class="bg-white border mb-4">
						<?php /* <div class="book-form-section mb-2">
							<h3 class="d-flex fz-1 justify-content-between mb-0 ">Alege extra servicii pentru zborul tau</h3>
							<p class="mt-1 ">Confortul tau primeaza! Adauga servicii zborului pentru a profita din plin de calatoria ta.</p>
							<div>
								<div v-for="p in ptcArr">
									<h3 class="d-flex fz-1 justify-content-between mb-0">{{ format_ptc(p) }}</h3>
									<div class="d-flex justify-content-start">
										<div class="upsell-options-wrapper d-flex">
											<div class="upsell-options text-nowrap d-inline-block w-100">
												<template v-for="ancilleryServiceKey in ancilleryServiceKeys">
													<template v-if="optionalService = ancilleryServices.find((v) => v._Code == ancilleryServiceKey)">
														<div class="bg-white d-inline-block rounded upsell-options-group" v-for="optionalService in ancilleryServices.filter((v) => v._Code == ancilleryServiceKey).reduce((a,b) => (a[b._Scode] = b, a ),{})" 
															@mouseover="hoverSeatmapping[optionalService._Scode] = true"
															@mouseleave="hoverSeatmapping[optionalService._Scode] = false" 
															:class="{'active-upgrade': hoverSeatmapping[optionalService._Scode], [optionalService._Scode]:1}">
															<div class="d-flex justify-content-around upsell-options-groupItem">
																<ul class="m-0 p-0 upsell-options-groupItem text-center">
																	<li class="head pb-2 ps-3 pe-3 pt-2 text-white d-flex flex-column align-items-center" style="height:100px;">
																		<div class="service-icon d-flex flex-column" style="height:100%;">
																			<div class="mb-1" v-html="serviceIcon(optionalService)"></div>
																			<div class="mx-auto mt-auto mb-auto" style="max-width:80%;min-width:80px;white-space:normal;line-height:90%;">{{ opv(optionalService,'Service.Name').toLowerCase() }}</div>
																		</div>
																	</li>
																	<li>
																		<ul class="m-0 p-0 upsell-options-services">
																			<div v-for="sv in ancilleryServices.filter((v) => v._Code == ancilleryServiceKey && v._Scode == optionalService._Scode && v.Target == p[0] && v.PassengerIndex == p[1] && (!this.selectedRouteFromTo || [opv(v, 'Route.From'), opv(v, 'Route.To')].join(',') == selectedRouteFromTo))">
																				<div class="upsell-options-servicesName tooltip-top">
																				<span class="rounded count-1 text-center">
																					<div>{{ airportNameByCode(opv(sv,'Route.From')) }} <br/> &#x2708;<br/> {{airportNameByCode(opv(sv,'Route.To'))}}</div>
																				</span>
																				<h5>{{ opv(sv,'Route.From') }} &#x2708; {{ opv(sv,'Route.To') }}</h5>
																				</div>
																				<div style="max-height:300px; overflow-y:auto;">
																					<div v-for="ov in opv(sv, 'Options.Option')" class="text-left d-flex w-100 justify-content-start">
																						<label class="d-flex align-center flex-wrap">
																							<input type="checkbox" v-model="selectedBookingCodes2" :value="[sv.BookingCode,ov.Code].join(':')" class="mr-2">
																							<span v-text="getOptionDesc(ov)"></span>
																						</label>
																						
																						<strong class="pl-2 ml-auto">{{ format_price(opv(ov,'Price.Amount', 0), pv('flight.Currency')) }}</strong>
																					</div>
																				</div>
																			</div>
																		</ul>
																	</li>
																</ul>
															</div>
														</div>
													</template>
												</template>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div> */ ?>
						<div class="book-form-section mb-2">
							<h3 class="d-flex fz-1 justify-content-between mb-0 ">Alege extra servicii pentru zborul tau</h3>
							<p class="mt-1 ">Confortul tau primeaza! Adauga servicii zborului pentru a profita din plin de calatoria ta.</p>
							<div>
								<div v-for="p in ptcArr">
									<h3 class="d-flex fz-1 justify-content-between mb-0 mt-3">{{ format_ptc(p) }}</h3>
								<nav class="nav nav-tabs justify-content-start mb-4" role="tablist" style="white-space: normal;overflow:visible;">
									<a 
										@click.prevent="selectedRouteFromTo2[p] = ''"
										class="nav-link mr-2"
										:class="{active: (selectedRouteFromTo2[p]||'') == ''}"
										role="tab"
									>
										<span class="upsell-options-servicesName tooltip-top d-flex flex-column">
											<span class="rounded count-1">
												<div v-text="'Toate rutele'"></div>
											</span>
											<div v-text="'Toate'"></div>
										</span>
									</a>
									<a v-for="routeFromTo in ancilleryRoutes" 
										:set ="(routeFromToArr = routeFromTo.split(','), null)"
										@click.prevent="selectedRouteFromTo2[p] = routeFromTo"
										class="nav-link mr-2"
										:class="{active: selectedRouteFromTo2[p] == routeFromTo}"
										role="tab"
									>
										<span class="upsell-options-servicesName tooltip-top d-flex">
											<span class="rounded count-1 text-center">
												<div>{{ airportNameByCode(routeFromToArr[0]) }} <br/> &#x2708;<br/> {{airportNameByCode(routeFromToArr[1])}}</div>
											</span>
											<div v-text="routeFromToArr[0] + ' &#x2708; ' + routeFromToArr[1]"></div>
										</span>
									</a>
								</nav>
								<div class="d-flex justify-content-start">
								
							<div class="upsell-options-wrapper">
								<?php /* <div class="bg-white mr-1 d-inline-block rounded upsell-options-group fixed">
									<div class="d-flex justify-content-around upsell-options-groupItem">
										<ul class="m-0 p-0 upsell-options-groupItem text-center">
											<li class="text-left">
												<ul class="m-0 p-0 upsell-options-services">
													<template v-for="ancilleryServiceKey in ancilleryServiceKeys">
														<template v-if="optionalService = ancilleryServices.find((v) => v._Code == ancilleryServiceKey)">
															<div v-for="optionalService in ancilleryServices.filter((v) => v._Code == ancilleryServiceKey).reduce((a,b) => (a[opv(b,'Service.CategoryCode',''), opv(b,'Service.AtpcoSubGroup',''), opv(b,'Service.Name','')] = b, a ),{})">
																<li class="service-icon d-flex">
																	<span class="upsell-options-servicesName">
																		<span class="rounded count-1 d-md-none">
																			<div>
																				<div>
																					<span v-html="serviceIcon(optionalService)"></span>
																					{{ opv(optionalService,'Service.Name').toLowerCase() }}
																				</div>
																				<div>(
																					<i :class="serviceIconClass(optionalService.Service.CategoryCode.toLowerCase())"></i> 
																					<b>{{ opv(optionalService,'Service.CategoryName').toLowerCase() }}</b>
																					)
																				</div>
																			</div>
																		</span>
																		<span v-html="serviceIcon(optionalService)"></span>
																	</span>
																	<span class="d-md-inline d-none pl-1">{{ opv(optionalService,'Service.Name').toLowerCase() }}</span>
																</li>
															</div>
														</template>
													</template>
												</ul>
											</li>
										</ul>
									</div>
									<div class="bg-body d-md-block d-none fz-1_3 p-2 text-purple">
										<span class="badge text-muted">Pret total optiuni extra</span>
										<strong class="d-block" v-text="format_price(priceOptions2, pv('flight.Currency'))"></strong>
									</div>
								</div> */ ?>
									<ul class="m-0 p-0 upsell-options-services">
									<template v-for="ancilleryServiceKey in ancilleryServiceKeys" class="row">
										<template  v-for="optionalService in ancilleryServices.filter((v) => v._Code == ancilleryServiceKey).reduce((a,b) => (a[b._Scode] = b, a ),{})">
										<template  v-for="svs in [ancilleryServices.filter((v) => v._Code == ancilleryServiceKey && v._Scode == optionalService._Scode && v.Target == p[0] && v.PassengerIndex == p[1] && (!this.selectedRouteFromTo2[p] || [opv(v, 'Route.From'), opv(v, 'Route.To')].join(',') == selectedRouteFromTo2[p]))]">
										<template v-if="svs.length">
										<li class="row m-0">
										<div class="col-12 col-md-4 col-lg-3"><span v-html="serviceIcon(optionalService)"></span>
																					{{ opv(optionalService,'Service.Name').toLowerCase() }}</div>
										<div class="col-12 col-md-8 col-lg-9 upsell-options-services">
											<div v-for="sv in svs">
												<div class="upsell-options-servicesName tooltip-top">
												<span class="rounded count-1 text-center">
													<div>{{ airportNameByCode(opv(sv,'Route.From')) }} <br/> &#x2708;<br/> {{airportNameByCode(opv(sv,'Route.To'))}}</div>
												</span>
												<h5>{{ opv(sv,'Route.From') }} &#x2708; {{ opv(sv,'Route.To') }}</h5>
												</div>
												<div class="row">
													<div v-for="ov in opv(sv, 'Options.Option')" class="col-12 col-sm-6 col-md-4 d-flex">
														<label class="" style="word-break: break-all;">
															<input type="checkbox" v-model="selectedBookingCodes2" :value="[sv.BookingCode,ov.Code].join(':')" class="d-inline-block mr-2" style="vertical-align:middle;">
															<span v-text="getOptionDesc(ov)"></span>
														</label>
														
														<strong class="pl-2 ml-auto text-nowrap">{{ format_price(opv(ov,'Price.Amount', 0), pv('flight.Currency')) }}</strong>
													</div>
												</div>
											</div>
										</div>
										</li>
										</template>
										</template>
										</template>
									</template>
									</ul>
								</div>
								
							</div>
								</div>
							</div>
						</div>
						<div class="bg-body d-md-block d-none fz-1_3 p-2 text-purple">
							<span class="badge text-muted">Pret total optiuni extra</span>
							<strong class="d-block" v-text="format_price(priceOptions, pv('flight.Currency'))"></strong>
						</div>
					</div>
					<?php /* <div  class="bg-white border mb-4">
						<div class="book-form-section mb-2">
							<h3 class="d-flex fz-1 justify-content-between mb-0 ">Alege extra servicii pentru zborul tau</h3>
							<p class="mt-1 ">Confortul tau primeaza! Adauga servicii zborului pentru a profita din plin de calatoria ta.</p>

							<nav class="nav nav-tabs justify-content-start mb-4" role="tablist" style="white-space: normal;overflow:visible;">
								<a 
									@click.prevent="selectedRouteFromTo = ''"
									class="nav-link mr-2"
									:class="{active: selectedRouteFromTo == ''}"
									role="tab"
								>
									<span class="upsell-options-servicesName tooltip-top d-flex flex-column">
										<span class="rounded count-1">
											<div v-text="'All routes'"></div>
										</span>
										<div v-text="'All'"></div>
									</span>
								</a>
								<a v-for="routeFromTo in ancilleryRoutes" 
									:set ="(routeFromToArr = routeFromTo.split(','), null)"
									@click.prevent="selectedRouteFromTo = routeFromTo"
									class="nav-link mr-2"
									:class="{active: selectedRouteFromTo == routeFromTo}"
									role="tab"
								>
									<span class="upsell-options-servicesName tooltip-top d-flex">
										<span class="rounded count-1 text-center">
											<div>{{ airportNameByCode(routeFromToArr[0]) }} <br/> &#x2708;<br/> {{airportNameByCode(routeFromToArr[1])}}</div>
										</span>
										<div v-text="routeFromToArr[0] + ' &#x2708; ' + routeFromToArr[1]"></div>
									</span>
								</a>
							</nav>

							
							<!--
							<select ref="ancilleryRouteSelector" class="form-control">
								<option>- All routes -</option>
								<option v-for="routeFromTo in ancilleryRoutes" :set ="(routeFromToArr = routeFromTo.split(','), null)" :value="routeFromTo" v-text="airportNameByCode(routeFromToArr[0]) + ' &#x2708; ' + airportNameByCode(routeFromToArr[1])"></option>
							</select>
							-->
							<div class="d-flex justify-content-start">
							<div class="upsell-options-wrapper d-flex">
								<div class="bg-white mr-1 d-inline-block rounded upsell-options-group fixed">
									<div class="d-flex justify-content-around upsell-options-groupItem">
										<ul class="m-0 p-0 upsell-options-groupItem text-center">
											<li class="head p-2 bg-body text-white d-flex align-items-center" style="height:100px;">
												<span class="m-auto" :set="(routeFromToArr = selectedRouteFromTo.split(','), null)" v-html="!selectedRouteFromTo ? 'All routes' : (routeFromToArr[0] + ' &#x2708; ' + routeFromToArr[1])"></span>
											</li>
											<li class="text-left">
												<ul class="m-0 p-0 upsell-options-services">
													<li v-for="p in ptcArr">
														<span class="service-icon">
														<span class="upsell-options-servicesName">
															<span class="rounded count-1 d-md-none">
																<div><i :class="ptcIconClass(p[0].toLowerCase())"></i> {{ format_ptc(p) }}</div>
															</span>
															<i :class="ptcIconClass(p[0].toLowerCase())"></i>
														</span>
														<span class="d-md-inline d-none pl-1">{{ format_ptc(p) }}</span>
														</span>
													</li>
													<!--
													<template v-for="ancilleryServiceKey in ancilleryServiceKeys">
														<template v-if="optionalService = ancilleryServices.find((v) => v._Code == ancilleryServiceKey)">
															<div v-for="optionalService in ancilleryServices.filter((v) => v._Code == ancilleryServiceKey).reduce((a,b) => (a[opv(b,'Service.CategoryCode',''), opv(b,'Service.AtpcoSubGroup',''), opv(b,'Service.Name','')] = b, a ),{})">
																<li class="service-icon d-flex">
																	<span class="upsell-options-servicesName">
																		<span class="rounded count-1 d-md-none">
																			<div>
																				<div>
																					<span v-html="serviceIcon(optionalService)"></span>
																					{{ opv(optionalService,'Service.Name').toLowerCase() }}
																				</div>
																				<div>(
																					<i :class="serviceIconClass(optionalService.Service.CategoryCode.toLowerCase())"></i> 
																					<b>{{ opv(optionalService,'Service.CategoryName').toLowerCase() }}</b>
																					)
																				</div>
																			</div>
																		</span>
																		<span v-html="serviceIcon(optionalService)"></span>
																	</span>
																	<span class="d-md-inline d-none pl-1">{{ opv(optionalService,'Service.Name').toLowerCase() }}</span>
																</li>
															</div>
														</template>
													</template>
													-->
												</ul>
											</li>
										</ul>
									</div>
									<div class="bg-body d-md-block d-none fz-1_3 p-2 text-purple">
										<span class="badge text-muted">Pret total optiuni extra</span>
										<strong class="d-block" v-text="format_price(priceOptions, pv('flight.Currency'))"></strong>
									</div>
								</div>
								<div class="upsell-options text-nowrap d-inline-block">
									<template v-for="ancilleryServiceKey in ancilleryServiceKeys">
										<template v-if="optionalService = ancilleryServices.find((v) => v._Code == ancilleryServiceKey)">
											<div class="bg-white d-inline-block rounded upsell-options-group" v-for="optionalService in ancilleryServices.filter((v) => v._Code == ancilleryServiceKey).reduce((a,b) => (a[b._Scode] = b, a ),{})" 
												@mouseover="hoverSeatmapping[optionalService._Scode] = true"
												@mouseleave="hoverSeatmapping[optionalService._Scode] = false" 
												:class="{'active-upgrade': hoverSeatmapping[optionalService._Scode], [optionalService._Scode]:1}">
												<div class="d-flex justify-content-around upsell-options-groupItem">
													<ul class="m-0 p-0 upsell-options-groupItem text-center">
														<li class="head pb-2 ps-3 pe-3 pt-2 text-white d-flex flex-column align-items-center" style="height:100px;">
															<div class="service-icon d-flex flex-column" style="height:100%;">
																<div class="mb-1" v-html="serviceIcon(optionalService)"></div>
																<div class="mx-auto mt-auto mb-auto" style="max-width:80px;min-width:80px;white-space:normal;line-height:90%;">{{ opv(optionalService,'Service.Name').toLowerCase() }}</div>
																<template v-if="isSamePriceForAllPtc(optionalService)">
																	<span class="mt-auto">
																		{{ format_price(ancilleryServices.filter((v) => v._Code == ancilleryServiceKey && v._Scode == optionalService._Scode && v.Target == optionalService.Target && v.PassengerIndex == optionalService.PassengerIndex && (!this.selectedRouteFromTo || [opv(v, 'Route.From'), opv(v, 'Route.To')].join(',') == selectedRouteFromTo)).reduce((t,a) => t += parseFloat(opv(a,'Options.Option.0.Price.Amount')), 0), pv('flight.Currency')) }}
																	</span>
																</template>
															</div>
														</li>
														<li>
															<ul class="m-0 p-0 upsell-options-services">
																<li v-for="p in ptcArr" class="px-0">
																	<div v-if="(
																		bookingCodes = getSameAncillery(optionalService, p).map(v => v.BookingCode) 
																		,select_status = !bookingCodes.length ? undefined : ((a=bookingCodes.filter(v => -1 != selectedBookingCodes.indexOf(v)).length - bookingCodes.length, a==-bookingCodes.length ? 0 : (!a ? 1 : -1)))
																		, 1)"
																		class="upsell-options-servicesName tooltip-top d-flex flex-column"
																		@click = "
																		bookingCodes = getSameAncillery(optionalService, p).map(v => v.BookingCode) 
																		,select_status = (a=bookingCodes.filter(v => -1 != selectedBookingCodes.indexOf(v)).length - bookingCodes.length, a==-bookingCodes.length ? 0 : (!a ? 1 : -1)),
																		selectedBookingCodes = (!select_status ? selectedBookingCodes.concat(bookingCodes) : selectedBookingCodes.filter((v) => -1 === bookingCodes.indexOf(v)))
																		" 
																		>
																		<span v-if="!selectedRouteFromTo && undefined !== select_status" class="rounded" :class="{'count-1':1}">
																			<div v-for="oservice in getSameAncillery(optionalService, p)">
																				<i class="fa fa-check" v-if="selectedBookingCodes && -1 !== selectedBookingCodes.indexOf(oservice.BookingCode)"></i>
																				<i class="fa fa-square" v-else></i>
																				<span class="pl-2">{{ opv(oservice,'Route.From') }} &#x2708; {{ opv(oservice,'Route.To') }}</span>
																			</div>
																		</span>
																		<div class="">
																		<template v-if="!isSamePriceForAllPtc(optionalService)">
																		{{ format_price(ancilleryServices.filter((v) => v._Code == ancilleryServiceKey && v._Scode == optionalService._Scode && v.Target == p[0] && v.PassengerIndex == p[1] && (!this.selectedRouteFromTo || [opv(v, 'Route.From'), opv(v, 'Route.To')].join(',') == selectedRouteFromTo)).reduce((t,a) => t += parseFloat(opv(a,'Options.Option.0.Price.Amount')), 0), pv('flight.Currency')) }}
																		</template>
																		<span v-if="!selectedRouteFromTo && undefined !== select_status">{{ ancilleryServices.filter((v) => v._Code == ancilleryServiceKey && v._Scode == optionalService._Scode && v.Target == optionalService.Target && v.PassengerIndex == optionalService.PassengerIndex && (!this.selectedRouteFromTo || [opv(v, 'Route.From'), opv(v, 'Route.To')].join(',') == selectedRouteFromTo)).reduce((t,a) => t += (selectedBookingCodes && -1 !== selectedBookingCodes.indexOf(a.BookingCode) ? 1 : 0), 0) }} / {{ ancilleryServices.filter((v) => v._Code == ancilleryServiceKey && v._Scode == optionalService._Scode && v.Target == optionalService.Target && v.PassengerIndex == optionalService.PassengerIndex && (!this.selectedRouteFromTo || [opv(v, 'Route.From'), opv(v, 'Route.To')].join(',') == selectedRouteFromTo)).reduce((t,a) => t += 1, 0) }} &#x2708; </span>
																		<i class="fa fa-ban disabled" v-if="undefined === select_status"></i>
																		<i class="far fa-square" v-if="0 === select_status"></i>
																		<i class="fa fa-list-check" v-else-if="select_status<0"></i>
																		<i class="fa fa-check-double" v-else-if="!selectedRouteFromTo && ancilleryRoutes.length > 1 && select_status>0"></i>
																		<i class="fa fa-check" v-else-if="select_status>0"></i>
																		</div>
																	</div>
																</li>
															</ul>
														</li>
													</ul>
												</div>
											</div>
										</template>
									</template>
								</div>
							</div>
							</div>
						
							<!--
							<div class="include-service-details anc-service-details mt-3 ">
								<div class="row" v-for="routeFromTo in ancilleryRoutes">
									<div class="col-12" :set ="(routeFromToArr = routeFromTo.split(','), null)">
										<div class="border-bottom d-flex justify-content-between mb-2 rounded pb-2">
											<span class="align-self-center font-custom-bold">
												{{ routeFromToArr[0] + '-' + routeFromToArr[1] }}
											</span>
										</div>
									</div>
									<div v-for="p in ptcArr" class="col-6 col-md-3 col-sm-4 seat-selected">
										<small>{{ format_ptc(p) }}</small>
										<template v-for="ancilleryServiceKey in ancilleryServiceKeys">
											<template v-if="optionalService = ancilleryServices.find((v) => v._Code == ancilleryServiceKey && v.Target == p[0] && v.PassengerIndex == p[1] && routeFromTo == v.Route.From + ',' + v.Route.To)">
												<div>
													<strong class="text-uppercase mb-1">
														<small>
															<u>{{ opv(optionalService,'Service.CategoryName').toLowerCase() }}</u>
														</small>
													</strong>
												</div>
												<div v-for="optionalService in ancilleryServices.filter((v) => v._Code == ancilleryServiceKey && v.Target == p[0] && v.PassengerIndex == p[1] && routeFromTo == v.Route.From + ',' + v.Route.To)">
													<label class="text-capitalize d-flex justify-content-between mb-0 p-2">
														<span>
															<input 
																type="checkbox" 
																:data-amount="opv(optionalService,'Options.Option[0].Price.Amount')" 
																:data-booking-code="opv(optionalService,'BookingCode')" 
																:data-option-code="opv(optionalService,'Options.Option.0.Code')"
																:data-currency="opv(optionalService,'Options.Option[0].Price.Currency')"
																>
															{{ opv(optionalService,'Service.Name').toLowerCase() }}
														</span>
														<span class="text-uppercase">{{ format_price(opv(optionalService,'Options.Option.0.Price.Amount'), opv(optionalService,'Options.Option.0.Price.Currency')) }}</span>
													</label>
												</div>
											</template>
										</template>
									</div>
								</div>
							</div>
	
						-->
						</div>
						<hr/>
					</div> */ ?>
					</template>
				</div>
			</template>

			
			<div class="bg-white border seatMapCabinWrap borderBox">
				<div class="book-form-section mb-2">
					<div id="seatMappingWrap" class="" v-if="1">
					<div class="bg-white border mb-4">
						<div class="book-form-section mb-2">
							<h3 class="d-flex fz-1 justify-content-between mb-0 ">Harta locuri avion
							</h3>
							<p class="mt-1 ">Alege unde doresti sa te asezi pentru a te relaxa in locul tau preferat, fie ca este pe culoar, extra legroom, sau cu vedere pe fereastra!</p>
							<ul class="pl-3">
								<li>Preferinta va fi trimisa catre compania aeriana si nu este garantata.</li>
								<li>Utilizeaza - Alege Loc - pentru a alege din optiunile de scaune disponibile, unele, contra cost</li>
							</ul>

							<div class="d-flex justify-content-start">
							<div class="upsell-options-wrapper d-flex">
								<div class="bg-white mr-1 d-inline-block rounded upsell-options-group fixed">
									<div class="d-flex justify-content-around upsell-options-groupItem">
										<ul class="m-0 p-0 upsell-options-groupItem text-center">
											<li class="head p-2 bg-body text-purple">
												<small class="m-0"></small>
											</li>
											<li class="text-left">
												<ul class="m-0 p-0 upsell-options-services">
													<li v-for="p in ptcArr" class="d-flex align-items-center" style="height:30px">
														<span class="service-icon">
															<span class="upsell-options-servicesName d-flex align-items-center">
																<span class="rounded count-1 d-md-none">
																	<div><i :class="ptcIconClass(p[0].toLowerCase())"></i> {{ format_ptc(p) }}</div>
																</span>
																<span>
																	<i :class="ptcIconClass(p[0].toLowerCase())"></i>
																	<span class="d-md-inline d-none pl-1">{{ format_ptc(p) }}</span>
																</span>
															</span>
														</span>
													</li>
												</ul>
											</li>
										</ul>
									</div>
									<div class="bg-body d-md-block d-none fz-1_3 p-2 text-purple">
										<span class="badge text-muted">Pret Total Locuri</span>
										<strong class="d-block" v-text="format_price(priceSeats, pv('flight.Currency'))"></strong>
									</div>
								</div>
								<div class="upsell-options text-nowrap d-inline-block">
									<template v-for="route in pv('flight.Routes',[])">
										<template v-for="(segment, segmentIndex) in opv(route,'Segment',[])">
											<div class="bg-white d-inline-block rounded upsell-options-group" 
													@mouseover="hoverSeatmapping[[route.Index, segmentIndex]] = true"
													@mouseleave="hoverSeatmapping[[route.Index, segmentIndex]] = false" :class="{'active-upgrade': hoverSeatmapping[[route.Index, segmentIndex]]}">
												<div class="d-flex justify-content-around upsell-options-groupItem">
												<ul class="m-0 p-0 upsell-options-groupItem text-center">
													<li class="head pb-2 ps-3 pe-3 pt-2 text-white d-flex">
														<div @click="chooseSeat(opv(segment, 'Origin.Airport.Code', ''), opv(segment, 'Destination.Airport.Code', ''), route.Index, segmentIndex)" class="d-block text-nowrap m-auto">
															<strong>{{ (airportsByCode[opv(segment, 'Origin.Airport.Code')].City || opv(segment, 'Origin.Airport.Code')) }}
																&#x2708;
																{{ (airportsByCode[opv(segment, 'Destination.Airport.Code')].City || opv(segment, 'Destination.Airport.Code')) }}
															</strong>
														</div>
														<!--<span @click="chooseSeat(opv(segment, 'Origin.Airport.Code', ''), opv(segment, 'Destination.Airport.Code', ''), route.Index, segmentIndex)" class="btn btn-outline-dark" type="button"><i class="fa fa-braille fa-3x"></i></span>-->
													</li>
													<li>
														<ul class="m-0 p-0 upsell-options-services">
															<li v-for="p in ptcArr" class="d-flex flex-column align-items-center" style="height:30px">
																<div style="width:100%;" v-if="seatSegments[[route.Index, segmentIndex, opv(segment, 'Origin.Airport.Code'), opv(segment, 'Destination.Airport.Code')]] && ptcSeat[[route.Index, segmentIndex,opv(segment, 'Origin.Airport.Code'), opv(segment, 'Destination.Airport.Code'),p]] && (seatDetails = getSeatDetails(seatSegments[[route.Index, segmentIndex, opv(segment, 'Origin.Airport.Code'), opv(segment, 'Destination.Airport.Code')]], ptcSeat[[route.Index, segmentIndex,opv(segment, 'Origin.Airport.Code'), opv(segment, 'Destination.Airport.Code'),p]]))" >
																	
																	<span class="d-flex justify-content-between" 
																		v-if="(seatDetails = getSeatDetails(seatSegments[[route.Index, segmentIndex, opv(segment, 'Origin.Airport.Code'), opv(segment, 'Destination.Airport.Code')]], ptcSeat[[route.Index, segmentIndex,opv(segment, 'Origin.Airport.Code'), opv(segment, 'Destination.Airport.Code'),p]]))">
																		<b class="seatnumber">&#128186; {{ (seatDetails.seat.Number + '' + seatDetails.seat.Code).toUpperCase() }}</b>
																		<b class="seatPrice pl-1" v-if="opv(seatDetails, 'charge.Price.Amount', 0)">{{ format_price(opv(seatDetails, 'charge.Price.Amount', 0), pv('flight.Currency')) }}</b>
																		<span class="text-danger pl-2" 
																			@click="
																			(e = ptcSeat[[route.Index, segmentIndex, opv(segment, 'Origin.Airport.Code'), opv(segment, 'Destination.Airport.Code'),p]]) && (
																				delete(seatPtc[e]),
																				delete(ptcSeat[[route.Index, segmentIndex, opv(segment, 'Origin.Airport.Code'), opv(segment, 'Destination.Airport.Code'),p]])
																			),
																			( !e &&  (
																				f = [route.Index, segmentIndex, opv(segment, 'Origin.Airport.Code'), opv(segment, 'Destination.Airport.Code'),'A',p].join(',')
																				,ptcSeat[[route.Index, segmentIndex,opv(segment, 'Origin.Airport.Code'), opv(segment, 'Destination.Airport.Code'),p]] = f
																				,seatPtc[f] = [route.Index, segmentIndex,opv(segment, 'Origin.Airport.Code'), opv(segment, 'Destination.Airport.Code'),p].join(',')
																			))
																			"><i class="fa fa-times"></i></span>
																	</span>
																	<!--
																	<span v-if="!ptcSeat[[route.Index,segmentIndex,opv(segment, 'Origin.Airport.Code'), opv(segment, 'Destination.Airport.Code'),p]] || 'W' == ptcSeat[[route.Index,segmentIndex,opv(segment, 'Origin.Airport.Code'), opv(segment, 'Destination.Airport.Code'),p]]">
																		<i class="fa fa-image"></i> Window
																	</span>
																	<span v-else-if="[route.Index, segmentIndex, opv(segment, 'Origin.Airport.Code'), opv(segment, 'Destination.Airport.Code'),'A',p].join(',') == ptcSeat[[route.Index,segmentIndex,opv(segment, 'Origin.Airport.Code'), opv(segment, 'Destination.Airport.Code'),p]]">
																		<i class="fa fa-users-between-lines"></i> Aisle
																	</span>
																	<span v-else-if="(seatDetails = getSeatDetails(seatSegments[[route.Index, segmentIndex, opv(segment, 'Origin.Airport.Code'), opv(segment, 'Destination.Airport.Code')]], ptcSeat[[route.Index, segmentIndex,opv(segment, 'Origin.Airport.Code'), opv(segment, 'Destination.Airport.Code'),p]]))">
																		<span class="seatnumber">{{ (seatDetails.seat.Number + '' + seatDetails.seat.Code).toUpperCase() }}</span>
																		<b class="seatPrice pl-1" v-if="opv(seatDetails, 'charge.Price.Amount', 0)">{{ format_price(opv(seatDetails, 'charge.Price.Amount', 0), pv('flight.Currency')) }}</b>
																	</span>
																	-->
																</div>
																<div class="d-flex align-items-center" v-else>
																	<select data-val="" 
																		class="form-control form-select disableSelect w-100 py-0" 
																		style="height:20px;" 
																		@change="($event.srcElement.value == 'S' && chooseSeat(opv(segment, 'Origin.Airport.Code', ''), opv(segment, 'Destination.Airport.Code', ''), route.Index, segmentIndex) && ($event.srcElement.value = !$event.isTrusted ? '' : $event.srcElement.getAttribute('data-val'))), ($event.srcElement.setAttribute('data-val', $event.srcElement.value)
,(e = ptcSeat[[route.Index, segmentIndex, opv(segment, 'Origin.Airport.Code'), opv(segment, 'Destination.Airport.Code'),p]]
	, e && (a = seatPtc[e], delete(seatPtc[e]), a && delete(ptcSeat[a]))
),(['A','W'].indexOf($event.srcElement.value) > -1 && (
	f = [route.Index, segmentIndex, opv(segment, 'Origin.Airport.Code'), opv(segment, 'Destination.Airport.Code'),$event.srcElement.value,p].join(',')
	,ptcSeat[[route.Index, segmentIndex,opv(segment, 'Origin.Airport.Code'), opv(segment, 'Destination.Airport.Code'),p]] = f
	,seatPtc[f] = [route.Index, segmentIndex,opv(segment, 'Origin.Airport.Code'), opv(segment, 'Destination.Airport.Code'),p].join(',')
)))"
																	>
																		<option value="">- Indiferent -</option>
																		<option v-for="(t, k) in seatLocations" :value="k" :selected="!!seatPtc[[route.Index,segmentIndex,opv(segment, 'Origin.Airport.Code'), opv(segment, 'Destination.Airport.Code'),k,p]]">
																			<template v-if="k=='W'">&#127745;</template>
																			<template v-if="k=='A'">&#128228;</template> 
																			{{ t }}
																		</option>
																		<option value="S">&#128186; - Alege loc -</option>
																	</select>
																	<i class="fa fa-refresh ml-1"  @click="$event.target.previousElementSibling.selectedIndex++;triggerChange($event.target.previousElementSibling)"></i>
																</div>
																<!--<span v-if="(
																	bookingCodes = getSameAncillery(optionalService, p).map(v => v.BookingCode) 
																	,select_status = (a=bookingCodes.filter(v => -1 != selectedBookingCodes.indexOf(v)).length - bookingCodes.length, a==-bookingCodes.length ? 0 : (!a ? 1 : -1))
																	, 1)"
																	@click = "
																	bookingCodes = getSameAncillery(optionalService, p).map(v => v.BookingCode) 
																	,select_status = (a=bookingCodes.filter(v => -1 != selectedBookingCodes.indexOf(v)).length - bookingCodes.length, a==-bookingCodes.length ? 0 : (!a ? 1 : -1)),
																	selectedBookingCodes = (!select_status ? selectedBookingCodes.concat(bookingCodes) : selectedBookingCodes.filter((v) => -1 === bookingCodes.indexOf(v)))
																	" 
																	>
																	<i class="far fa-square" v-if="!select_status"></i>
																	<i class="fa fa-list-check" v-else-if="select_status<0"></i>
																	<i class="fa fa-check-double" v-else-if="!selectedRouteFromTo && ancilleryRoutes.length > 1 && select_status>0"></i>
																	<i class="fa fa-check" v-else-if="select_status>0"></i>
																</span>-->
															</li>
														</ul>
													</li>
												</ul>
												</div>
											</div>
										</template>
									</template>
								</div>
							</div>
							</div>
						</div>
					</div>
					</div>
					<!--
					<div class="">
						<div class="row" v-for="route in pv('flight.Routes',[])">
							<template v-for="(segment, segmentIndex) in opv(route,'Segment',[])">
								<div class="col-12">
									<div class="border-bottom d-flex justify-content-between mb-2 rounded pb-2">
										<span class="align-self-center font-custom-bold">
											{{ opv(segment, 'Origin.Airport.Code', '') + '-' + opv(segment, 'Destination.Airport.Code', '') }}
										</span>
										<button @click="chooseSeat(opv(segment, 'Origin.Airport.Code', ''), opv(segment, 'Destination.Airport.Code', ''), route.Index, segmentIndex)" class="btn btn-outline-dark" type="button">Choose Seat</button>
									</div>
								</div>
								<div v-for="p in ptcArr" class="col-6 col-md-3 col-sm-4 seat-selected">
									<small>{{ format_ptc(p) }}</small>

									<div class="form-group selectedSeat">
										<div class="input-group mb-3">
											<input type="text" readonly class="form-control selectedSeat-value" placeholder="Recipient's username" aria-label="Recipient's username" aria-describedby="button-addon2">
											<button class="btn btn-primary" onclick="__template.flight.services.methods.removeSelectedSeatFromForm(this)" type="button" id="button-addon2"><i class="fa fa-times"></i></button>
										</div>
									</div>

									<div class="form-floating chooseSeat">
										<select class="form-control form-select disableSelect w-100 " id="seat_{{ p[0] +'_'+ p[1] +'_'+ route.Index +'_'+ segmentIndex }}_PREFERENCE">
											<option v-for="(t, k) in seatLocations" :value="k">{{ t }}</option>
										</select>
										<label for="seat_{{ p[0] +'_'+ p[1] +'_'+ route.Index +'_'+ segmentIndex }}_PREFERENCE">Select Seat Type</label>
									</div>
								</div>
							</template>
						</div>
					</div>
					-->
				</div>
				
				<hr/>
			</div>

			<template v-if="loading.seats || manual.seatSegmentCode">
				<div class="flight-book-seatMap-wrapper p-4" v-if="loading.seats">
					<h1><i class="fa fa-spinner fa-spin"></i> Se incarca locurile.</h1>
				</div>
				<div class="flight-book-seatMap-wrapper p-4" v-else>
					<div class="d-flex justify-content-between text-purple">
						<h4 class="align-self-center">Harta locuri avion</h4>
						<div class="mb-2 mt-2 seat-legend text-center">
							<span class="badge badge-light badge-pill border seat-legend-unavail">
								<em></em>
								Indisponibil</span>
							<span class="badge badge-light badge-pill border seat-legend-avail">
								<em></em>
								Disponibil</span>
							<span class="badge badge-light badge-pill border seat-legend-paid">
								<em></em>
								Platit</span>
							<span class="badge badge-light badge-pill border seat-legend-prefer">
								<em></em>
								Preferential</span>
							<span class="badge badge-light badge-pill border seat-legend-selected">
								<em></em>
								Ales</span>
							<span class="badge badge-light badge-pill border seat-legend-disable">
								<em></em>
								Dizabilitati</span>
	
						</div>
						<a href="javascript:void(0)" class="btn btn-primary text-white" @click="chooseSeat()">
							<i class="fa fa-arrow-circle-left"></i> Inapoi
						</a>
					</div>

					<nav class="nav nav-tabs justify-content-center mb-4 flight-book-seatMap-route" role="tablist">
						<template v-for="route in pv('flight.Routes',[])">
							<template v-for="(segment, segmentIndex) in opv(route,'Segment',[])">
								<a @click.prevent="chooseSeat(opv(segment, 'Origin.Airport.Code', ''), opv(segment, 'Destination.Airport.Code', ''), route.Index, segmentIndex)" class="nav-link mr-2" :class="{active: manual.seatSegmentCode == [route.Index, segmentIndex, opv(segment, 'Origin.Airport.Code', ''), opv(segment, 'Destination.Airport.Code', '')].join(',')}" role="tab">{{ opv(segment, 'Origin.Airport.Code', '') + ' &#x2708; ' + opv(segment, 'Destination.Airport.Code', '') }}</a>
							</template>
						</template>
					</nav>
					<div class="seatmap-container-wrapper tab-content">
						<div class="position-relative" v-if="!opv(seatSegment,'SeatMap')">
							<h3 class="text-danger text-center"><i class="fa fa-warning"></i> Din pacate, pentru acest zbor se pot alege locuri.</h3>
						</div>
						<div class="position-relative" v-else>
							<div class="d-flex justify-content-between seat-map-details text-purple">
								<div class="pax">
									<div class="fw-bold text-uppercase">Calatori</div>
									<div v-for="p in ptcArr" class="pax-type mb-1 hasValue d-flex justify-content-between" :class="{'hasSeatValue': ptcSeat[getPtcSeatCode(seatSegment, p)]}">
										<span>{{ format_ptc(p) }}</span>
										<span class="pax-detail-wrap d-inline-flex">
											<template v-if="(s = ptcSeat[getPtcSeatCode(seatSegment, p)]),1">
												<template v-if="(seatDetails = !ptcSeat[getPtcSeatCode(seatSegment, p)] ? false : getSeatDetails(seatSegment, ptcSeat[getPtcSeatCode(seatSegment, p)]))">
													<span class="d-flex border-start me-2 ms-2 pax-detail pl-2">
														<small class="seatnumber">{{ opv(seatDetails,'seat.Number') + '' + opv(seatDetails,'seat.Code') }}</small>
														<small class="pl-2 seatPrice">{{ format_price(opv(seatDetails, 'charge.Price.Amount', 0), pv('flight.Currency')) }}</small>
													</span>
													<span @click="
														delete(seatPtc[ptcSeat[getPtcSeatCode(seatSegment, p)]])
														,delete(ptcSeat[getPtcSeatCode(seatSegment, p)])"
														class="align-self-center pl-2">
														<i class="fa fa-times"></i>
													</span>
												</template>
												<template v-else-if="s && -1 < ['A','W'].indexOf(s.split(',')[4])">
													<div class="d-flex border-start me-2 ms-2 pax-detail pl-2">
														<small class="seatnumber">{{ seatLocations[s.split(',')[4]] }}</small>
													</div>
												</template>
												<template v-else>
													<div class="d-flex border-start me-2 ms-2 pax-detail pl-2">
														<small class="seatnumber">-</small>
													</div>
												</template>
											</template>
										</span>
									</div>
								</div>
								<div class="text-right">
									<div class="fw-bold text-uppercase">Detalii Zbor</div>
									<div>{{ opv(seatSegment, 'Carrier.Marketing._', '')}}</div>
									<div>Nr. Zbor: {{ opv(seatSegment, 'Flight.Number', '')}}</div>
									<div>Avion: {{ opv(seatSegment, 'Aircraft._', '')}}</div>
								</div>

							</div>
							<div class="plane-wrapper">
								<div class="plane-body">
									<div class="plane-cockpit">
										<span></span>
										<span></span>
									</div>

									<div class="d-flex flex-column justify-content-center seat-grid text-center" :class="{'minGrid' : opv(seatSegment, 'SeatMap.Rows.Row',[]).length < 16}">

										<div class="seat-tooltip-wrapper" :class="{active:!!seatCode}">
											<div class="seat-tooltip" :set="(seatDetails = !seatCode ? null : getSeatDetails(seatSegment, seatCode), {}, null)">
												<div class="prefer-text" :class="{'d-block' : !!opv(seatDetails,'seat.Preferential', false)}">
													<small class="d-block">Preferential seating refers to special seats reserved by the airlines for customers who are purchasing full price, unrestricted airfare and frequent flyers that have accumulated an especially high number of frequent flyer miles.</small>
													<small class="d-block mt-1">If you select preferential seating and the airline determines you are ineligible for those seats, you will be reassigned to other available seating.</small>
												</div>
												<div class="d-flex justify-content-between">
													<strong class="selected-seat-number">{{ opv(seatDetails,'seat.Number') + ' ' + opv(seatDetails,'seat.Code') }}</strong>
													<strong class="selected-seat-price">{{ format_price(opv(seatDetails, 'charge.Price.Amount', 0), pv('flight.Currency')) }}</strong>
												</div>

												<div class="form-floating">
													<label for="paxInfo" class="d-none">Calatori</label>
													<select class="form-control form-select mb-2" ref="paxInfo">
														<!--<option value="">Select Traveler</option>-->
														<option v-for="p in [...ptcArr].sort((a,b) => (((ptcSeat[getPtcSeatCode(seatSegment, a)]&&1)||0) - ((ptcSeat[getPtcSeatCode(seatSegment, b)]&&1)||0)))" :value="p" :selected="seatCode == ptcSeat[getPtcSeatCode(seatSegment, p)]">{{ format_ptc(p) }}</option>
													</select>
												</div>

												<div class="d-flex justify-content-between">
													<button type="button" class="btn btn-success" @click.prevent="
													(d = ptcSeat[getPtcSeatCode(seatSegment, $refs.paxInfo.value)]) && (
														delete(ptcSeat[getPtcSeatCode(seatSegment, $refs.paxInfo.value)])
														,delete(seatPtc[d])
													),
													(e = seatPtc[seatSegment.code]) && (
														delete(seatPtc[seatSegment.code])
														,delete(ptcSeat[e])
													),
													$refs.paxInfo.value && (
														ptcSeat[getPtcSeatCode(seatSegment, $refs.paxInfo.value)] = seatDetails.code
														,seatPtc[seatDetails.code] = getPtcSeatCode(seatSegment, $refs.paxInfo.value)
													)
													,seatCode = null, $refs.paxInfo.selectedIndex=0">
														<i class="fa fa-check"></i>
														Alege</button>
													<!--<button type="button" onclick="
														delete(seatPtc[[seatSegment.rindex,seatSegment.sindex,ptcSeat[[seatSegment.rindex,seatSegment.sindex,ptcID,index]]]])
														,delete(ptcSeat[[seatSegment.rindex,seatSegment.sindex,ptcID,index]])
														,seatCode = null" class="remove mt-2 font-custom-bold text-purple">
														<i class="fa fa-check"></i>
														Remove</button>-->
													<button type="button" class="btn btn-default" @click.prevent="seatCode = null">
														<i class="fa fa-times"></i>
														Inchide</button>
												</div>
											</div>
										</div>

										<div class="seat-row">
											<template v-for="column in opv(seatSegment, 'SeatMap.Columns.Column',[])">
												<div class="seat-block seat-column" :class="{
												'seat-window': !!column.Window,
												'seat-aisle': !!column.Aisle,
											}">{{ column.Code }}</div>
											</template>
										</div>

										<template v-for="(seatRow, seatRowIndex) in opv(seatSegment, 'SeatMap.Rows.Row',[])">
											<div v-if="seatRow.ExitRow" class="seat-row exit-row d-flex justify-content-between" :class="{
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
											<div class="seat-row" :class="{
												'seat-row-wings' : !!seatRow.OverWing,
												'seat-row-exit' : !!seatRow.ExitRow
											}" :data-number="seatRow.Number">
											<template v-for="colSeat in opv(seatSegment, 'SeatMap.Columns.Column',[]).reduce((cols, col, coli) => (cols.push({...col,'seatIndex': (i = opv(seatRow, 'Seat',[]).findIndex((v) => v.Code == col.Code)), 'seat': opv(seatRow, 'Seat.' + i) || {}}), cols), [])">
													<div :seat-index="colSeat.seatIndex" :seat-code="getSeatCode(seatSegment, seatRowIndex, colSeat.seatIndex)" class="seat-block upsell-options-servicesName" :class="{
															'seat-window': !!opv(colSeat,'Window'),
															'seat-aisle': !!opv(colSeat,'Aisle'),
															'seat-avail font-custom-bold': !!colSeat.seat.Available,
															'seat-unavail': !colSeat.seat.Available,
															'seat-disable': !!colSeat.seat.HandicapFriendly,
															'seat-noseat': !!colSeat.seat.NoSeat,
															'seat-paid': !!colSeat.seat.ChargeTypeReference,
															'seat-prefer': !!colSeat.seat.Preferential,
															'seat-selected': (sc = getSeatCode(seatSegment, seatRowIndex, colSeat.seatIndex), seatCode == sc || seatPtc[sc]),
															'active': seatCode == sc,
													}" @click="colSeat.seat.Available && (seatCode = getSeatCode(seatSegment, seatRowIndex, colSeat.seatIndex),clickedSeat($event));">
														<template v-if="colSeat.seat.Available">
															<span class="rounded count-1" v-if="seatPtc[sc]">
																<div><i class="fa fa-check"></i>
																	<span class="pl-2">{{ format_ptc(seatPtc[sc].split(',').slice(-2)) }}</span>
																</div>
															</span>
															{{ colSeat.seat.Code }}
															<template v-if="colSeat.seat.HandicapFriendly">
																<i class="fa fa-wheelchair handicap-friendly"></i>
															</template>
															<template v-if="colSeat.seat.ChargeTypeReference">
																<i class="fa fa-dollar paid-seat"></i>
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
							</div>
						</div>
					</div>
				</div>
			</template>
		</div>
	
		<div class="bg-white border static p-4">
			<div class="d-flex justify-content-between justify-content-md-center">
				<strong class="align-self-center fz-2 me-2">Pret zbor:
					<h1 id="finalPrice">{{ format_price(finalPrice, pv('flight.Currency')) }}</h1>
				</strong>
			</div>
			<!--
			<div class="d-flex justify-content-between justify-content-md-center mt-3">
				<button class="btn btn-primary bookingBtn" type="submit">Continue to Booking Page</button>
			</div>
			-->
		</div>
	</div>
</div><!-- #flight-upsell -->

<script>
	window.vue_upsell = Vue.createApp({
		data() {
			var _vue = this;
			console.log(_vue);
			return {
				flight: Object.freeze(<?php echo json_encode(Arr::get($this->view_data, 'flight_details', []), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>),
				ptcIconClasses: Object.freeze({
					'adt': 'fa fa-male',
					'chd': 'fa fa-child-reaching',
					'yth': 'fa fa-face-diagonal-mouth',
					'sen': 'fa fa-user-secret',
					'inf': 'fa fa-baby',
					'ins': 'fa fa-person-breastfeeding'
				}),
				ptcTypes: Object.freeze({
					'adt': 'Adult',
					'chd': 'Copil', //'Child',
					'yth': 'Adolescent', //'Youth',
					'sen': 'Senior',
					'inf': 'Infant',
					'ins': 'Bebelus' // Infant <1y
				}),
				seatLocations: Object.freeze({
					'W': 'Fereastra', // Window
					'A': 'Culoar', // Aisle
				}),
				serviceIconClasses: Object.freeze({
					'bg': 'fa fa-suitcase-rolling',
					'cy': 'fa fa-suitcase',
					'ff': 'fa fa-percent',
					'ie': 'fa fa-headphones-simple',
					'lg': 'fa fa-couch',
					'ml': 'fa fa-utensils',
					'other': 'fa fa-bell-concierge',
					'se': 'fa fa-chair',
					'ts': 'fa fa-earth-europe',
					'vc': 'fa fa-right-left',
					'vr': 'fa fa-wallet',
					'gt': 'fa fa-bus',
					'up': 'fa fa-level-up-alt',
				}),
				loading: {
					ancillery: false,
					upsells: false,
					seats: false,
				},
				manual: {
					upsellServiceCode: undefined,
					segmentCodeTab: undefined,
					seatSegmentCode: undefined,
					seatSegment: undefined,
					seatCode: null,
				},
				
				ptcNames: {},
				upsells: [],
				seatPtc: {},
				ptcSeat: {},
				seats_requests: {},
				upsell_request: undefined,
				ancillery_requests: {},
				ancillery: undefined,
				selectedRouteFromTo: '',
				selectedRouteFromTo2: {},
				selectedBookingCodes: [],
				selectedBookingCodes2: [],
				seatSegments: [],
				hoverSeatmapping: {},
			}
		},
		computed: {
			submittable: {
				get() { 
					return !this.loading.upsells
						&& !this.loading.ancillery
						&& !this.loading.seats
				}
			},
			priceBase: {
				get() { return this.upsell ? this.opv(this.upsell, 'Price.Amount',0) : this.pv('flight.Price',0) }
			},
			/* priceOptions: {
				get() { return this.ancilleryServices.filter((v) => -1 != this.selectedBookingCodes.indexOf(v.BookingCode)).map((v) => this.opv(v,'Options.Option.0.Price.Amount', 0)).reduce((t, v) => (t+=parseFloat(v), t),0) }
			}, */
			priceOptions: {
				get() { 
					var booking_codes = this.selectedBookingCodes2.reduce((t, v) => {
						var bc = v.replace(/\:.*/, '');
						if(-1 != t.indexOf(bc)) return t;
						t.push(bc);
						return t;
					}, []);
					
					return this.ancilleryServices.filter((v) => -1 != booking_codes.indexOf(v.BookingCode)).map((v) => this.opv(v,'Options.Option', []).filter(o => {
						if(!o.Price || !o.Price.Amount || !o.Price.Currency || -1 === this.selectedBookingCodes2.indexOf(v.BookingCode + ':' + o.Code)) return false;
						return true;
					}).reduce((a,b) => (a+=parseFloat(b.Price.Amount)),0)).reduce((t, v) => (t+=parseFloat(v), t),0) 
				}
			},
			priceSeats: {
				get() { return !this.seatPtc ? 0 : Object.keys(this.seatPtc).reduce((t, c) => (t+=!this.seatSegments[c.split(',').slice(0,4)] || !c ? 0 : this.opv(this.getSeatDetails(this.seatSegments[c.split(',').slice(0,4)], c), 'charge.Price.Amount', 0)), 0) }
			},
			finalPrice: {
				get() { return this.priceBase + this.priceOptions + this.priceSeats }
			},
			console: {
				get() { return console },
			},
			upsellServiceCode: {
				get() {
					return this.manual.upsellServiceCode || this.opv(this.upsells.find((v) => !(this.opv(v, 'Price.Amount') - this.pv('flight.Price'))), 'Code') || '';
				},
				set(value) {
					(!value || this.upsells.find((v) => v.Code === value)) && (this.manual.upsellServiceCode = value || null);
				}
			},
			upsell() {
				return this.upsells.find((v) => v.Code === this.upsellServiceCode);
			},
			segmentCodeTab: { // TODO
				get() {
					return this.manual.segmentCodeTab || null;
				},
				set(value) {
					this.pv('flight.Routes', []).find((v) => opv(v, 'Segment.Origin.Airport.Code', '') + '-' + opv(v, 'Segment.Destination.Airport.Code', '') === value) && (this.manual.segmentCodeTab = value);
				}
			},
			seatCode: {
				get() {
					return this.manual.seatCode || null;
				},
				set(value) {
					this.manual.seatCode = value
				}
			},
			ptcArr() {
				let d = [],
					c, k;
				for (k in this.ptc) {
					c = this.ptc[k];
					[...Array(c).keys()].forEach((v, i) => {
						d.push([k, i]);
					});
				}
				return d;
			},
			hasUpsells() {
				return !!this.pv('flight.UpsellSupport', false);
			},
			countAdult() {
				return this.countAdtChd(false)
			},
			countChild() {
				return this.countAdtChd(true)
			},
			ptc() {
				return this.pv('flight.FareDetails.PaxFare', []).reduce((q, w) => (q[this.opv(w, 'PTC', '')] = this.opv(w, 'Count', 0), q), {});
			},
			isSecure() {
				return !!this.pv('flight.Routes', []).find((v) => this.opv(v, 'Segment.Secured', false));
			},
			serviceKeys() {
				return this.upsells.reduce((o, i) => (this.opv(i, 'FareDetails.BrandedFare.BrandDetails', []).map((v) => this.opv(v, 'Services', []).map((v) => (this.opv(v, 'CategoryName', '') + ':' + this.opv(v, 'CategoryCode', '')).toLowerCase()).reduce((q, w) => (-1 == q.indexOf(w) && q.push(w), q), []).filter((v) => -1 == o.indexOf(v)).forEach((v) => o.push(v))), o), []).sort()
			},
			ancilleryServices() {
				return this.pv('ancillery.OptionalServices.OptionalService', [])
					.map(v => (
						v['_Code'] = this.opv(v, 'Service.CategoryName', '') + ':' + this.opv(v, 'Service.CategoryCode', '').toLowerCase(),
						v['_Scode'] = [this.opv(v, 'Service.CategoryCode', ''), this.opv(v, 'Service.AtpcoSubGroup', ''), this.opv(v, 'Service.Name', '')].join(',')
						, v))
					.filter((v) => !v.Included);
			},
			ancilleryServiceKeys() {
				return this.ancilleryServices
					.map((v) => v._Code)
					.reduce((q, w) => (-1 == q.indexOf(w) && q.push(w), q), [])
					.sort();
			},
			ancilleryRoutes() {
				return this.ancilleryServices.map((v) => [v.Route.From,v.Route.To].join(','))
					.reduce((q, w) => (-1 == q.indexOf(w) && q.push(w), q), []);
			},
			airportsByCode() {
				var result = {};
				this.pv('flight.Routes',[]).forEach((r) => {
					this.opv(r,'Segment',[]).forEach((s) => {
						['Origin', 'Destination'].forEach((t) => {
							var dc = this.opv(s,t + '.Airport.Code','');
							if(dc && undefined == result[dc]){
								result[dc] = this.opv(s, t + '.Airport',{});
							}
							var dc = this.opv(s, t + '.Airport.Code','');
						})
					})
				})
				return result;
			},
			seatSegment() {
				return this.seatSegments[this.manual.seatSegmentCode];
			},
			serviceKeysAsArr() {
				var r;
				return this.serviceKeys.map((v) => (r = v.split(':'), [r[0], r[1]]));
			}
		},
		methods: {
			capitalizeWords(str, lowerfirst) {
			  var s = '' + str;
			  s = s.trim();
			  if(lowerfirst){
				s = s.toLowerCase();
			  }
			  return s
				.split(/\s+/)
				.map((word) => word.charAt(0).toUpperCase() + word.slice(1))
				.join(' ');
			},
			getOptionDesc(o) {
				var desc = this.opv(o,'Description.0') || '';
				// console.warn(o);
				if(o.Price && o.Price.Amount && o.Price.Currency){
					var r = new RegExp('\\s*\\(\\s*' + o.Price.Amount + '\\s*' + o.Price.Currency + '\\)\\s*');
					desc = desc.replace(r,' ');
				}
				desc = desc.replace(/\s+/g, ' ').trim();
				if(desc.toUpperCase() === desc){
					return this.capitalizeWords(desc, true);
				} else if(desc.toLowerCase() === desc){
					return this.capitalizeWords(desc);
				}
				return desc;
			},
			triggerChange(element) {
				element.dispatchEvent(new Event('change'));
			},
			airportByCode(code) {
				return this.opv(this.airportsByCode,code);
			},
			airportNameByCode(code) {
				var d = this.airportByCode(code);
				if(d){
					return this.opv(d,'_') + ', ' + this.opv(d,'City');
				}
				return d;
			},
			ptcIconClass(code) {
				return this.ptcIconClasses[code] || 'certificate';
			},
			getSameAncillery(optionalService, p) {
				return this.ancilleryServices.filter((a) => {
					if(this.selectedRouteFromTo){
						if([this.opv(a, 'Route.From'), this.opv(a, 'Route.To')].join(',') != this.selectedRouteFromTo) return;
					}
					if(p){
						if(p[0] != this.opv(a, 'Target', '')) return;
						if(p[1] != this.opv(a, 'PassengerIndex', '')) return;
					}
					if(this.opv(a, 'Service.CategoryName', '') + ':' + this.opv(a, 'Service.CategoryCode', '').toLowerCase() != optionalService._Code) return;
					if(this.opv(a, 'Service.Name', '') != this.opv(optionalService, 'Service.Name', '')) return;

					if((this.opv(a, 'Options.Option.0.Price.Amount') != this.opv(optionalService, 'Options.Option.0.Price.Amount'))
						&& (this.opv(a, 'Options.Option.0.Price.Currency') != this.opv(optionalService, 'Options.Option.0.Price.Currency'))) {
							return;
						}
					return true;
				})
			},
			isSamePriceForAllPtc(optionalService, ps) {
				var ptcArr2, r2;
				if(this.selectedRouteFromTo){
					r2 = [this.selectedRouteFromTo]
				} else {
					r2 = this.ancilleryRoutes;
				}
				if(!ps){
					ptcArr2 = [[this.opv(optionalService, 'Target', ''), this.opv(optionalService, 'PassengerIndex', '')]];
				} else {
					ptcArr2 = this.ptcArr;
					
				}
				var d = r2.find((r) => {
					var m;
					return ptcArr2.find((p) => {
						return this.ancilleryServices.find((a) => {
							if([this.opv(a, 'Route.From'), this.opv(a, 'Route.To')].join(',') != r) return;
							if(p){
								if(p[0] != this.opv(a, 'Target', '')) return;
								if(p[1] != this.opv(a, 'PassengerIndex', '')) return;
							}
							if(this.opv(a, 'Service.CategoryName', '') + ':' + this.opv(a, 'Service.CategoryCode', '').toLowerCase() != optionalService._Code) return;
							if(this.opv(a, 'Service.Name', '') != this.opv(optionalService, 'Service.Name', '')) return;
							if(undefined === m) m = this.opv(a, 'Options.Option.0.Price.Amount');

							if(this.opv(a, 'Options.Option.0.Price.Amount') == m) return;
							return true;
						});
					});
				});

				return !d;
			},
			serviceIconClass(code) {
				return this.serviceIconClasses[code] || 'certificate';
			},
			serviceIcon(optionalService) {
				var html = [];
				var cat_name = this.opv(optionalService,'Service.CategoryName').toLowerCase();
				var service_name = this.opv(optionalService,'Service.Name').toLowerCase();

				html.push(`<i class="` + this.serviceIconClass(optionalService.Service.CategoryCode.toLowerCase()) + `"></i>`);
				
				var stack = [];
				var service_name_arr = service_name.split(/\s+/);
				service_name_arr.forEach((v) => {
					switch(v){
						case 'pet':
							stack.push(`<i class="fa fa-dog"></i>`);
							break;
						case '1st':
							stack.push(`<i class="fa fa-first text-danger" style="position:absolute;z-index:1;font-size:60%;right:-10px;top:-3px;width:auto;height:auto;">1<sup>st</sup></i>`);
							break;
						case '2nd':
							stack.push(`<i class="fa fa-second text-danger" style="position:absolute;z-index:1;font-size:60%;right:-10px;top:-3px;width:auto;height:auto;">2<sup>nd</sup></i>`);
							break;
						case '3rd':
							stack.push(`<i class="fa fa-third text-danger" style="position:absolute;z-index:1;font-size:60%;right:-10px;top:-3px;width:auto;height:auto;">3<sup>rd</sup></i>`);
							break;
						case 'bag':
							stack.push(`<i class="fa fa-shopping-bag"></i>`);
							break;
						case 'bicycle':
							stack.push(`<i class="fa fa-bicycle"></i>`);
							break;
						case 'additional':
							stack.push(`<i class="fa fa-plus text-danger r-0" style="position:absolute;z-index:1;font-size:80%;left:-3px;top:-3px;width:auto;height:auto;"></i>`);
							break;
						case 'eqpmt':
						case 'equipment':
							stack.push(`<i class="fa fa-swatchbook"></i>`);
							break;
						case 'excess':
							stack.push(`<i class="fa fa-warning text-danger" style="position:absolute;z-index:1;font-size:60%;right:-3px;top:-3px;width:auto;height:auto;"></i>`);
							break;
						case 'prepaid':
							stack.push(`<i class="fa fa-dollar text-success" style="position:absolute;z-index:1;font-size:60%;left:-3px;bottom:-3px;width:auto;height:auto;"></i>`);
							break;
						case 'baggage':
							stack.push(`<i class="fa fa-suitcase"></i>`);
							break;
						case 'luggage':
							stack.push(`<i class="fa fa-cart-flatbed-suitcase"></i>`);
							break;
						case 'dim':
							stack.push(`<i class="fa fa-up-right-and-down-left-from-center"></i>`);
							break;
						case 'weight':
							stack.push(`<i class="fa fa-weight-scale"></i>`);
							break;
						case 'oxygen':
							stack.push(`<i class="fa fa-mask-ventilator"></i>`);
							break;
					}
				});
				if(stack && stack.length){
					return `<span class="fa-stacks" style="position:relative;display:inline-block;">` + stack.join('') + '</span>';
				}

				return html.join('');
			},
			clickedSeat($event) {
				var $seat = $($event.srcElement);
				var $grid = $seat.closest(".seat-grid");
				var $tooltipWrapper = $grid.find(".seat-tooltip-wrapper");
				var calculateTop = $seat.offset().top - 5 - $grid.offset().top - $tooltipWrapper.outerHeight();
				$tooltipWrapper.css('top', calculateTop);
				var $plane_wrapper = $($grid).closest('.plane-wrapper');

				scrollIntoView($tooltipWrapper[0], {
					scrollMode: 'if-needed',
					block: 'nearest',
					inline: 'nearest',
				});
				//if($tooltipWrapper.offset().top < $plane_wrapper.offset().top)
				//$plane_wrapper.scrollTop($plane_wrapper.offset().top - $tooltipWrapper.offset().top)

				//console.log($plane_wrapper.scrollTop(), $plane_wrapper.offset().top,  $tooltipWrapper.offset().top, $plane_wrapper.offset().top - $tooltipWrapper.offset().top);
			},
			getPtcSeatCode(seatSegment, p) {
				return [seatSegment.rindex,seatSegment.sindex,this.opv(seatSegment, 'Origin.Airport.Code'), this.opv(seatSegment, 'Destination.Airport.Code'),p].join(',')
			},
			getSeatCode(seatSegment, seatRowIndex, seatIndex) {
				return [seatSegment.rindex, seatSegment.sindex,seatSegment.Origin.Airport.Code, seatSegment.Destination.Airport.Code, seatRowIndex, seatIndex].join(',')
			},
			getSeatDetails(segment, seatCode) {
				// console.error('getSeatDetails', seatCode);
				if (!segment) {console.error('nosegment'); return null;}
				if (!seatCode) {console.error('noseatcode'); return null;}
				let ocode, dcode, rindex, sindex, col, seat, row, charge;
				[rindex, sindex, ocode, dcode, srindex, scindex] = seatCode.split(',');

				if(isNaN(srindex)) return;
				if(isNaN(scindex)) return;

				if (ocode != this.opv(segment, 'Origin.Airport.Code')) {console.error('noOriginAirportCode'); return false;}
				if (dcode != this.opv(segment, 'Destination.Airport.Code')) {console.error('noDestAirportCode'); return false;}
				row = this.opv(segment, 'SeatMap.Rows.Row.' + srindex);
				if (!row) {console.error('noRow', segment, seatCode); return false;}
				seat = this.opv(row, 'Seat.' + scindex);
				if (!seat) {console.error('noSeat'); return false;}
				col = this.opv(segment, 'SeatMap.Columns.Column').find((v) => v.Code == seat.Code);
				if (!col) {console.error('noCol'); return false;}

				if (seat.ChargeTypeReference) {
					charge = this.opv(segment, 'SeatMap.ChargeList.ChargeType').find((v) => v.Reference == seat.ChargeTypeReference);
					if (!charge) {console.error('noCharge'); return false;}
				}

				return {
					code: seatCode,
					row: row,
					seat: seat,
					column: col,
					charge: charge,
				}
			},
			isSelected(key, value) {
				return this[key] == value;
			},
			countAdtChd(type) {
				return this.pv('flight.FareDetails.PaxFare', []).filter((v) => (d = ['adt', 'sen'].indexOf(this.opv(v, 'PTC', '').toLowerCase()), !type ? d != -1 : d == -1)).reduce((o, i, k, a) => o + parseInt(this.opv(i, 'Count', '')), 0);
			},
			loadAncillery(ancillery_code) {
				return this.ancillery_requests[[ancillery_code]] || (this.ancillery_requests[[ancillery_code]] = fetch('<?php echo site_url('/trip/flight/ancillery'); ?>?code=' + this.pv('flight.FlightsCode') + '&itinerary_code=' + this.pv('flight.ItineraryCode') + '&ancillery_code=' + ancillery_code).then(response => response.json()).then((a) => {
					if (!a.data) {
						throw "Could not load";
					}
					return Object.freeze(a.data);
				})).catch(() => {
					this.redirectToFlightsWithError('Could not load the ancillery');
				});
			},
			loadUpsells() {
				return this.upsell_request || (this.upsell_request = fetch('<?php echo site_url('/trip/flight/upsell'); ?>?code=' + this.pv('flight.FlightsCode') + '&itinerary_code=' + this.pv('flight.ItineraryCode')).then(response => response.json()).then((a) => {
					if (!a.data) {
						throw "Could not load";
					}
					return Object.freeze(a.data);
				})).catch(() => {
					this.redirectToFlightsWithError('Could not load the upsells');
				});
			},
			loadSeats(a, b, c, d) {
				return this.seats_requests[[a, b, c, d]] || (this.seats_requests[[a, b, c, d]] = fetch('<?php echo site_url('/trip/flight/seats'); ?>?code=' + this.pv('flight.FlightsCode') + '&itinerary_code=' + this.pv('flight.ItineraryCode') + '&ocode=' + a + '&dcode=' + b + '&rindex=' + c + '&pseat=' + 1).then(response => response.json()).then((a) => {
					if (!a.data) {
						throw "Could not load";
					}
					a.data['rindex'] = c;
					a.data['sindex'] = d;
					return Object.freeze(a.data);
				})).catch(() => {
					this.redirectToFlightsWithError('Could not load the ancillery');
				});
			},
			chooseSeat(a, b, c, d) {
				this.manual.seatSegmentCode = undefined;
				this.manual.seatCode = undefined;
				if (undefined === a) return;
				this.loading.seats = true;
				return this.loadSeats(a, b, c, d).then((e) => (
					this.seatSegments[[c, d, a, b]] = e,
					this.manual.seatSegmentCode = [c, d, a, b],
					e
				)).finally(() => {
					this.loading.seats = false;
				});
			},
			pv(dot_path, def) {
				return this.opv(this.$data, dot_path, def);
			},
			redirectToFlightsWithError(message) {
				// TODO, if we encounter any error, there is absolutely nothing we can do, except redirect to the search page
				console.error('SHOULD redirect to flights search (TODO)');
			},
			format_ptc(p, base) {
				if(!base && this.ptcNames[p]){
					return this.ptcNames[p].replace(/^[\u00C0-\u1FFF\u2C00-\uD7FF\w]|\s[\u00C0-\u1FFF\u2C00-\uD7FF\w]/g, function(letter) {
						return letter.toUpperCase();
					});
				}
				if('ALL' == p[0]) return 'Toti';
				return this.ptcTypes[(p[0] || '').toLowerCase()] + ' #' + (1 + parseInt(p[1]))
			},
			format_price(amount, currency) {
				if(undefined == currency){
					currency = this.pv('flight.Currency');
				}
				var symbol = currency;
				var amount_float = parseFloat(amount);
				if (isNaN(amount_float)) {
					return '-';
				} else {
					var amount_formatted = amount_float.toLocaleString('ro', {
						minimumFractionDigits: 2
					});
				}
				if (currency === 'RON') {
					symbol = 'Lei';
				} else if (currency === 'EUR') {
					symbol = '€';
				}
				return amount_formatted + ' ' + symbol;
			},
			opv(obj, dot_path, def) {
				var c,d;
				d = (Array.isArray(dot_path) ? dot_path : dot_path.split('.')).reduce((o, i, k, a) => (undefined === o || null === o || undefined === o[i] ? (c = a.splice(k+1), '*' === i && Array.isArray(o) ? o.map(v => this.opv(v, c)) : undefined) : o[i]), obj);
				
				if (undefined === def) return d;
				if (typeof def === 'function') return def(d);
				if (Array.isArray(def) && !Array.isArray(d)) return def;

				if (typeof d == typeof def) return d;

				switch (typeof def) {
					case 'boolean':
						return (('false' === d || 'no' === d || '0' === d) ? false : !!d);
					case 'number':
						if (!isNaN(d)) return Number(d);
						return def;
					case 'object':
						if (null === d) return def;
						break;
					case 'string':
						switch (typeof def) {
							case 'number':
								return '' + d;
							case 'boolean':
								return d ? 'true' : 'false';
							case 'object':
								return JSON.stringify(d);
						}
						break;
				}
				return d;
			}
		},
		mounted() {
			console.warn('mounted', this);
			if(this.hasUpsells){
				this.loading.upsells = true;
				this.loadUpsells().then((d) => this.upsells = Object.freeze(this.opv(d,'_embedded.upsell', []))).finally(() => {
					// this.manual.upsellServiceCode = this.opv(this.upsells.find((v) => !(this.opv(v, 'Price.Amount') - this.pv('flight.Price'))), 'Code')
					this.loading.upsells = false;
				}).catch(() => {
					this.redirectToFlightsWithError('Could not load the upsell');
				});
			}
		},
		watch: {
			'manual.seatSegmentCode': {
				immediate: true,
				handler: function(val, oldVal) {
					if (val === oldVal) return;
					if (!val && !val === !oldVal) return;
					$('body').toggleClass('modal-open');
				},
			},
			'finalPrice': {
				immediate: true,
				handler: function(val, oldVal) {
					if (val === oldVal) return;
					if (!val && !val === !oldVal) return;
					console.warn('calculateTotal', val, oldVal);
					if('function' == typeof window['calculateTotal']){
						// console.warn('calculateTotal', val);
						calculateTotal();
					}
				},
			},
			'selectedBookingCodes2': {
				immediate: true,
				handler: function(val, oldVal) {
					// console.warn('selectedBookingCodes2', val);
					
					var nv = val||[];
					var ov = oldVal||[];
					if(nv && nv.length){
						var s = [];
						var e = [];
						var dv = nv.filter(a => -1 == ov.indexOf(a));
						if(dv.length){
							dv.forEach((i,v) => {
								var a = i.split(':');
								if(-1 == e.indexOf(i)){
									e.push(i);
									s.push(a[0]);
								}
							});
							
							val.forEach((i,v) => {
								var a = i.split(':');
								if(-1 != e.indexOf(i)){
									// do nothing
								} else if(-1 == s.indexOf(a[0])){
									s.push(a[0]);
								} else {
									val.splice(v,1);
								}
							});
							this.selectedBookingCodes2 = val;
							return;
						}
					}
					
					if('function' == typeof window['calculateFinal']){
						calculateFinal(this, this.loading.ancillery || this.loading.upsells || this.loading.seats);
					}
				}
			},
			'seatPtc': {
				immediate: true,
				deep: true,
				handler: function(val, oldVal) {
					if('function' == typeof window['calculateFinal']){
						calculateFinal(this, this.loading.ancillery || this.loading.upsells || this.loading.seats);
					}
				}
			},
			'loading': {
				deep: true,
				immediate: true,
				handler: function(val, oldVal) {
					if('function' == typeof window['calculateFinal']){
						calculateFinal(this, this.loading.ancillery || this.loading.upsells || this.loading.seats);
					}
				}
			},
			'upsellServiceCode': {
				immediate: true,
				handler: function(val, oldVal) {
					console.warn('upsellServiceCode', val, oldVal);
					if (val === oldVal) return;
					if (!val && val === oldVal) return;
					this.ancillery = null;
					if(val){
						this.loading.ancillery = true;
						this.loadAncillery(val).then((d) => {
							this.ancillery = d;
						}).finally(() => {
							this.loading.ancillery = false;
						});
					} else {
						this.ancillery = this.flight;
					}
				},
			},
		}
	}).mount('#flight-upsell');
</script>
<?php themeFunctions::debugFileLine('end'); ?>