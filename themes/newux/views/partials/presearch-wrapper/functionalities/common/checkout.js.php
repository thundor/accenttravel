import DatePickerSelect from '../../../form/datepicker-select.js?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';
import FormLegend from '../../../form/legend.js?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';
import { VPhoneInput } from 'v-phone-input';

<?php themeFunctions::loadLang('payment_gateways/payu'); ?>
<?php 
$this->load->model('Options_model');
$payu_payment_methods = $this->Options_model->getKeys('payu_payment_methods');
if(!$payu_payment_methods || !is_array($payu_payment_methods)){
  $payu_payment_methods = array();
}
$payu_payment_methods = array_map(function($payment_method){
	return [
		'value' => $payment_method,
		'title' => lang($payment_method),
	];
}, $payu_payment_methods);
$payu_payment_methods = array_values($payu_payment_methods);
$first_payu_payment_method = $payu_payment_methods[0]['value'];

$this->load->model('Options_model');

$allowed_statuses = array(1,-2);
if($this->user->can('backend-config-save')){
  $allowed_statuses[] = -1;
}

$trip_24_pay = config_item('trip_24_pay');

$active_payment_methods = [];

$agency_status = $this->Options_model->get('payment_methods_status','agency');
if(!$trip_24_pay && in_array($agency_status,$allowed_statuses)){
	$active_payment_methods['agency'] = 1;
}
$bank_status = (int)$this->Options_model->get('payment_methods_status','bank');
if(!$trip_24_pay && in_array($bank_status,$allowed_statuses)){
  $active_payment_methods['bank'] = 1;
}
$online_status = (int)$this->Options_model->get('payment_methods_status','online');
if(in_array($online_status,$allowed_statuses)){
	$this->db->where_in('option_value',$allowed_statuses);
	$static_settings = $this->Options_model->getKeys('payment_gateways_status');
	if($static_settings){
		$available_payment_gateways = $static_settings;
	  
		$available_payment_gateways = array_intersect($available_payment_gateways, array('payu'));
		if($trip_24_pay){
			$available_payment_gateways = array_intersect($available_payment_gateways, array('pay24'));
		}
		
		if($available_payment_gateways){
			$active_payment_methods['online'] = 1;
		}
	}
}
$first_payment_method = '';
foreach($active_payment_methods as $active_payment_method => $active_payment_method_status){
	$first_payment_method = $active_payment_method; break;
}
?>
export default {
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	emits: ['research', 'set-value'],
	props: {
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
      result: {
          type: Object,
          default: () => (undefined),
      },
	},
	components:{
		'DatePickerSelect': DatePickerSelect,
		'FormLegend': FormLegend,
		'VPhoneInput': VPhoneInput,
	},
	data() {
		return {
			active_payment_methods: <?php echo json_encode($active_payment_methods, JSON_UNESCAPED_SLASHES); ?>,
			payment_dialog: false,
			error_dialog: false,
			error_dialog_html: '',
			validate_component: '',
			coupons_component: '',
			abortController: new AbortController(),
			delayed_entrance: 0,
			mode: 'tf',
			filtered_countries: countries,
		  rules: {
			  Required:[
				v => !(v && ('' + v).trim().length) && 'Necesar' || true,
			  ],
			  TravellerName:[
				v => /[0-9]/.test(v) && 'Fara cifre' || true,
			  ],
			  TravellerEmail:[
				v => !/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*\.[a-zA-Z]{2,}$/.test(v) && 'Invalid' || true,
			  ],
			  TravellerCNP:[
				v => v && !/^[0-9]{13}$/.test(v) && '13 Cifre' || true,
			  ],
			  TravellerCardSeries:[
				v => v && !/^[a-z]{2}$/i.test(v) && '2 Litere' || true,
			  ],
			  TravellerCardNumber:[
				v => v && !/^[0-9]{6}$/.test(v) && '6 Cifre' || true,
			  ],
			  IBAN:[
				v => v && !/^[a-z0-9]{24}$/i.test((v||'').replace(/\s+/,'')) && '24 Litere+Cifre' || true,
			  ],
		  },
		  coupon_discount: 0,
		  coupon_discount_object: null,
		  checkoutSuccess: false,
		  checkoutData: false,
		  loadingCheckout: false,
		  payment_method: <?php echo json_encode($first_payment_method, JSON_UNESCAPED_SLASHES); ?>,
		  post_data:{
			  payu_payment_method: <?php echo json_encode($first_payu_payment_method); ?>,
		  },
		  tos: false, // Termeni si conditii
		  tog: false, // Conditii garantare
		  pp: false, // Politica de confidentialitate
		  coupon_code: '',
		  travellers: [],
		  billing_person: {},
		  book_url: `${newux_url}/partials/presearch-wrapper/functionalities/<?php echo basename(dirname($a)); ?>/book.json?${append_url}`,
		};
	},
	beforeUnmount() {
		this.abortController.abort();
	},
	created() {
		var storageItem = getStorage('newux.checkout.' + this.mode,'', {}, {}, {});
		if(storageItem && storageItem.version == '1.0.0'){
			console.warn('this.billing_person', storageItem);
			this.billing_person = JSON.parse(JSON.stringify(storageItem.data.billing_person));
			// this.travellers = JSON.parse(JSON.stringify(storageItem.data.travellers));
			this.tos = true;
			this.tog = true;
			this.pp = true;
		}
	},
	mounted() {
	},
	methods: {
		getAge: (birthdate, referenceDate) => (((bd, rd) => rd.getFullYear() - bd.getFullYear() - (rd.getMonth() < bd.getMonth() && 1 || rd.getMonth() == bd.getMonth() && rd.getDate() < bd.getDate() && 1 || 0 ))(new Date(birthdate), new Date(...(referenceDate && [referenceDate] || [])))),
		filterFn (val, update) {
			if (val === '') {
			  update(() => {
				this.filtered_countries = countries

				// here you have access to "ref" which
				// is the Vue reference of the QSelect
			  })
			  return
			}

			update(() => {
			  const needle = val.toLowerCase()
			  this.filtered_countries = countries.filter(v => v.label.toLowerCase().indexOf(needle) > -1)
			})
		},
		updatedNationality () {
			console.warn('updatedNationality');
			setTimeout(() => {
				document.activeElement.blur();
				
			}, 500)
		},
		validateAndCheckout: function(){
			var saveObj = JSON.parse(JSON.stringify({
				version: '1.0.0',
				data: {
					billing_person: this.billing_person,
					travellers: this.travellers,
				},
			}));
			console.warn('Saving to storage', saveObj);
			saveStorage('newux.checkout.' + this.mode,saveObj);
			this.$refs.myform.validate().then((v) => {
				console.warn('validate', v);
				if(v.valid){
					
					this.checkout();
				} else {
					var first_error_id = ((v.errors || [])[0] || {}).id;
					if(first_error_id){
						var first_error_element = document.getElementById(first_error_id);
						if(first_error_element){
							first_error_element.focus();
						}
					}
				}
			})
		},
		checkout: function(){
			this.loadingCheckout = true;
			var data = {
				data: this.data,
				search_data: this.search_data, 
				...this.post_data,
				<?php if ($this->config->item('csrf_protection')){ ?>
				<?php echo json_encode($this->security->get_csrf_token_name()); ?>: <?php echo json_encode($this->security->get_csrf_hash()); ?>,
				<?php } ?>
				Children: this.search_data.Children?.length || 0,
				ChildrenAge: this.search_data.Children || [],
				ProductCode: this.result.Id,
				SearchId: this.offer.SearchId,
				OfferId: this.offer.Code,
				Transport: this.offer.Transport,
				payment_method: this.total_to_pay ? this.payment_method : 'free',
				tos: this.tos ? 1 : 0,
				tog: this.tog ? 1 : 0,
				pp: this.pp ? 1 : 0,
				coupon_code: this.coupon_code,
				travellers: this.travellers,
				billing_person: this.billing_person,
				expectedPrice: [this.offer.Price, (this.offer.Currency || {}).Code].join(''),
			};
			var fetch_url = this.book_url;
			fetch(fetch_url, {
				signal: this.abortController.signal,
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
			}).then((response) => response.json()).then((result) => {
				console.warn(result);
				if(result?.data?.html){
					this.payment_dialog = true;
					let paymentInterval;
					paymentInterval = setInterval(() => {
						if(this.$refs.paymentIframe){
							clearInterval(paymentInterval);
							const iframe = this.$refs.paymentIframe;
							console.warn('iframe', this);
							const doc = iframe.contentDocument || iframe.contentWindow.document;
							doc.open();
							doc.write(result.data.html);
							doc.close();
						}
					}, 500);
				} else if(result.status && result.status == 'success'){
					this.$emit('set-value',{'step': 4});
					setTimeout(() => {
						this.checkoutSuccess = true;
						this.checkoutData = result;
					}, 500)
				} else {
					if(result.message){
						this.error_dialog_html = result.messages.error && result.messages.error.join(', ') || result.message;
						this.error_dialog = true;
					}
				}
			}).catch((e) => {
				console.error("Failed to checkout details", e);
				// Do nothing
			}).finally(() => {
				this.loadingCheckout = false;
			})
			
		},
	},
	watch:{
		search_data: {
			immediate: true,
			handler(nv, ov){
				if(JSON.stringify(nv) === JSON.stringify(ov)) return;
				
				var storage_travellers = [];
				var storageItem = getStorage('newux.checkout.' + this.mode,'', {}, {}, {});
				if(storageItem && storageItem.version == '1.0.0'){
					storage_travellers = JSON.parse(JSON.stringify(storageItem.data.travellers || [])) || [];
				}
				
				
				var travellers = [];
				for(var i = 0; i<(((this.search_data || {}).full || {}).ADT || 0); i++){
					var adt;
					if(storage_travellers.length){
						var adt_index = storage_travellers.findIndex(a => a.type === 'ADT');
						if(-1 !== adt_index) {
							adt = {...storage_travellers[adt_index]};
							storage_travellers.splice(adt_index, 1);
						}
					}
					if(adt){
						adt['age'] = [18,];
						travellers.push(adt);
					} else {
						travellers.push({
							type: 'ADT',
							gender: 1,
							age: [18,],
						});
					}
				}
				<?php /*
				for(var i = 0; i<(((this.search_data || {}).full || {}).YTH || 0); i++){
					travellers.push({
						type: 'YTH',
						gender: 1,
						age: [12,17],
					});
				} */ ?>
				var children_ages = (((this.search_data || {}).full || {}).Children || []);
				for(var i = 0; i<children_ages.length; i++){
					
					var chd;
					if(storage_travellers.length){
						var chd_index = storage_travellers.findIndex(a => a.type === 'CHD' && (a => -1 !== children_ages.indexOf(a))(this.getAge(a.Birthdate)));
						if(-1 !== chd_index) {
							chd = {...storage_travellers[chd_index]};
							storage_travellers.splice(chd_index, 1);
						}
					}
					if(chd){
						chd['age'] = children_ages[i];
						travellers.push(chd);
					} else {
						travellers.push({
							type: 'CHD',
							gender: 1,
							age: children_ages[i],
						});
					}
				}
				this.travellers = travellers;
			}
		},
		'data.step': {
			immediate: true,
			handler(val){
				this.delayed_entrance = 0;
				setTimeout(() => {this.delayed_entrance = 1}, 1000);
			}
		}
	},
	computed: {
		totalPriceObject() {
			if(this.offer){
				return {
					Amount: this.offer.Price,
					Currency: (this.offer.Currency || {}).Code
				}
			} else {
				var hotel = this.hotel;
				var flight = this.flight;
				var flight2 = this.flight2;
				return (hotel?.Package?.PackageRooms?.PackageRoom || []).reduce((c, packageRoom, packageRoom_index) => (c.Amount+=((packageRoom.RoomRefs.RoomRef.filter(roomRef => (!hotel.Package.SelectedRooms ? roomRef.Selected : (packageRoom.PackageRoomCode + ':' + roomRef.RoomCode == hotel.Package.SelectedRooms[packageRoom_index]))).reduce((a, r) => a + ((c.Currency = r.Price.Currency), (r.Price||{}).Amount || 0),0)) || (hotel.Package.Price || {}).Amount && (c.Currency = hotel.Package.Price.Currency, hotel.Package.Price.Amount) || 0), c),{Amount: (flight?.result?.totalPrice || 0) + (flight2?.result?.totalPrice || 0), Currency: (flight?.Currency || 'RON')})
			}
		},
		totalPrice() {
			return this.totalPriceObject.Amount;
		},
		total_to_pay() {
			return this.totalPrice - this.coupon_discount;
		},
		offer() {
			return (this.result.Offers || [])[0]
		},
		hotel() {
			return this.result.Hotel
		},
		flight2() {
			return this.result.Flight2
		},
		flight() {
			console.warn('this.result.Flight', this.result);
			return this.result.Flight
		},
		breadcrumbs() {
			return [
				... this.prepend_breadcrumbs,
				{title: 'Rezervare', step: 3},
			];
		},
	},
	template : `
<v-container class="bg-background pa-0">
	<v-breadcrumbs :items="breadcrumbs">
		<template v-slot:divider>
			<v-icon icon="mdi-menu-right"></v-icon>
		</template>
		<template v-slot:item="{ item }">
			<v-breadcrumbs-item href="javascript:void(0)" :active="item.active" active-color="green" :disabled="item.step == 3" @click.stop="$emit('set-value', {'step': item.step})" v-text="item.title"></v-breadcrumbs-item>
		</template>
	</v-breadcrumbs>
<component is="style" v-if="data.step == 3 && delayed_entrance">
.v-window{
	overflow: visible;
}
.v-card{
	overflow: visible;
}
div.v-window-item {
	transition: overflow 2s ease-in 0s ease-out;
}
div[class="v-window-item"] {
    overflow: hidden;
}
.v-card.checkout-summary{
    position: sticky;
    top: 100px;
}
</component>
	<v-expansion-panels v-if="0">
	  <v-expansion-panel
		title="JSON result"
	  >
		<v-expansion-panel-text>
		<pre v-text="JSON.stringify(result,null,2)"></pre>
		</v-expansion-panel-text>
	  </v-expansion-panel>
	  <v-expansion-panel
		title="JSON Data"
	  >
		<v-expansion-panel-text>
		<pre v-text="JSON.stringify(data,null,2)"></pre>
		</v-expansion-panel-text>
	  </v-expansion-panel>
	  <v-expansion-panel
		title="JSON Search data"
	  >
		<v-expansion-panel-text>
		<pre v-text="JSON.stringify(search_data,null,2)"></pre>
		</v-expansion-panel-text>
	  </v-expansion-panel>
	  <v-expansion-panel
		title="JSON Travellers"
	  >
		<v-expansion-panel-text>
		<pre v-text="JSON.stringify(travellers,null,2)"></pre>
		</v-expansion-panel-text>
	  </v-expansion-panel>
	  <v-expansion-panel
		title="JSON Billing Person"
	  >
		<v-expansion-panel-text>
		<pre v-text="JSON.stringify(billing_person,null,2)"></pre>
		</v-expansion-panel-text>
	  </v-expansion-panel>
	</v-expansion-panels>
<template v-if="!result">
	<component :is="loadViewAsync('partials/presearch-wrapper/functionalities/common/loading')"></component>
</template>
<template v-else>
<v-form ref="myform" validate-on="lazy blur">
	<v-container class="px-0 px-md-4">
	<v-row>
	<v-col cols="12" lg="9" :class="{'loading-section': loadingCheckout}">
		<h1>Rezervare si plata</h1>
		<p>Mai ai doar un singur pas pana la finalizarea rezervarii si esti gata de zbor!</p>
		<template v-if="flight">
		</template>
		<div v-else>
			<hr />
			<FormLegend title="Detalii Calatori" subtitle="Completeaza cu datele tale de calatorie!"></FormLegend>
			<div class="checkout-travellers">
				<div class="checkout-traveller" v-for="(traveller, traveller_index) in travellers">
					<FormLegend :title="'Calator #' + (traveller_index + 1)" :subtitle="(translate_ptc[traveller.type][0] || traveller.type)  + ' ' + (traveller.age == 1 ? '1 An' : (traveller.age + ' Ani'))"></FormLegend>
					<v-container class="px-0">
						<v-row class="flex-wrap">
							<v-col>
								<v-row class="align-center">
									<v-col cols="12" sm="3" md="" order="1">
										
										<v-select v-if="traveller.type != 'CHD'" label="Titlu" v-model="traveller.Gender" :items="[{value:1, title: 'Dl.'},{value:2, title: 'Dna.'}]"
											hide-details-obs
											:rules="rules.Required||[]"
											validate-on="lazy input"
											>
										</v-select>
									</v-col>
									<v-col cols="12" sm="6" md="" order="3">
										<v-text-field
											v-model="traveller.Name"
											label="Nume"
											hide-details-obs
											:rules="(rules.Required||[]).concat(rules.TravellerName||[])"
										  ></v-text-field>
									</v-col>
									<v-col cols="12" sm="6" md="" order="4">
										
										<v-text-field
											v-model="traveller.Firstname"
											label="Prenume"
											hide-details-obs
											:rules="(rules.Required||[]).concat(rules.TravellerName||[])"
										  ></v-text-field>
									</v-col>
									<v-col cols="12" sm="9" md="5" order="5" order-sm="2" order-md="5">
										<span class="group-input-title">Data nasterii</span>
										<DatePickerSelect v-model="traveller.Birthdate" :age="traveller.age" :rules="rules.Required||[]" :reference-date="search_data.CheckIn || search_data[0]?.CheckIn" hide-details-obs  validate-on="lazy input"></DatePickerSelect>
									</v-col>
								</v-row>
							</v-col>
						</v-row>
					</v-container>
					
				</div>
			</div>
		</div>
		<hr />
		<FormLegend title="Detalii Facturare" subtitle="Completeaza cu datele persoanei care face plata"></FormLegend>
	<div class="checkout-billing-details">
		<div class="checkout-common-billing-details">
		<v-text-field class="billing-last-name"
			v-model="billing_person.Name"
			label="Nume"
			hide-details-obs
			:rules="(rules.Required||[]).concat(rules.TravellerName||[])"
		  ></v-text-field>
		<v-text-field class="billing-first-name"
			v-model="billing_person.Firstname"
			label="Prenume"
			hide-details-obs
			:rules="(rules.Required||[]).concat(rules.TravellerName||[])"
		  ></v-text-field>
		<v-text-field class="billing-email"
			v-model="billing_person.Email"
			type="email"
			label="Email"
			:rules="(rules.Required||[]).concat(rules.TravellerEmail||[])"
			hide-details-obs
		  ></v-text-field>
		
		<VPhoneInput country-label="Tara" label="Telefon" countryIconMode="svg" v-model="billing_person.Phone" class="billing-phone"
			hide-details-obs default-country="RO" :rules="rules.Required||[]" display-format="national"></VPhoneInput>
		<v-text-field class="billing-cnp" v-if="'tf' == mode"
			v-model="billing_person.UniqueIdentifier"
			label="CNP"
			hide-details-obs
			:rules="rules.TravellerCNP||[]"
		  ></v-text-field>
		<v-text-field
			 class="billing-ci-serie" v-if="'tf' == mode"
			v-model="billing_person.IdentityCardSeries"
			label="Serie"
			hide-details-obs
			:rules="rules.TravellerCardSeries||[]"
		  ></v-text-field>
		<v-text-field
			class="billing-ci-nr" v-if="'tf' == mode"
			v-model="billing_person.IdentityCardNumber"
			label="Numar"
			hide-details-obs
			:rules="rules.TravellerCardNumber||[]"
		  ></v-text-field>
		  <q-select v-if="'trip' == mode"
              v-model="(billing_person.Address || (billing_person.Address = {})).Country"
              :rules="rules.Required||[]"
              class="pb-0 billing-country"
              use-input
              input-debounce="0"
              label="Tara"
              :options="filtered_countries"
              emit-value
              map-options
              outlined
			  @update:modelValue="updatedNationality()"
              @filter="filterFn"
            >
            <template v-slot:option="scope">
                <q-item v-bind="scope.itemProps">
                  <q-item-section avatar>
                    <span class="v3q_tel__flag q-mr-sm" :class="{[scope.opt.value.toLowerCase()]: true}"></span>
                  </q-item-section>
                  <q-item-section>
                    <q-item-label>{{ scope.opt.label }}</q-item-label>
                  </q-item-section>
                </q-item>
              </template>
              <template v-slot:no-option>
                <q-item>
                  <q-item-section class="text-grey">
                    Nimic gasit
                  </q-item-section>
                </q-item>
              </template>
            </q-select>
		<v-text-field
			class="billing-oras"
			v-model="(billing_person.Address || (billing_person.Address = {})).City"
			label="Oras"
			:rules="rules.Required||[]"
			hide-details-obs
		  ></v-text-field>
		<v-text-field
			class="billing-adresa" v-if="'tf' == mode"
			v-model="(billing_person.Address || (billing_person.Address = {})).Details"
			label="Adresa"
			:rules="rules.Required||[]"
			hide-details-obs
		  ></v-text-field>
		<v-text-field
			class="billing-strada" v-if="'trip' == mode"
			v-model="(billing_person.Address || (billing_person.Address = {})).Street"
			label="Strada"
			:rules="rules.Required||[]"
			hide-details-obs
		  ></v-text-field>
		<v-text-field
			class="billing-nr-strada" v-if="'trip' == mode"
			v-model="(billing_person.Address || (billing_person.Address = {})).StreetNo"
			label="Nr.Strada"
			hide-details-obs
		  ></v-text-field>
		<v-text-field
			class="billing-cod-postal" v-if="'trip' == mode"
			v-model="(billing_person.Address || (billing_person.Address = {})).PostalCode"
			label="Cod postal"
			hide-details-obs
		  ></v-text-field>
		</div>
		<div class="checkout-toggle-pj">
		  <v-switch
			class="billing-facturare"
			v-model="billing_person.BillCompany"
			label="Facturare PJ"
			color="success"
			hide-details-obs
		  ></v-switch>
		</div>
		<div class="checkout-only-pj">
		<template v-if="billing_person.BillCompany">
		<div>
		<v-text-field
			class="billing-companie"
			v-model="(billing_person.Company || (billing_person.Company = {})).Name"
			label="Companie"
			:rules="rules.Required||[]"
			hide-details-obs
		  ></v-text-field>
		<v-text-field
			class="billing-cif"
			v-model="(billing_person.Company || (billing_person.Company = {})).TaxIdentificationNo"
			label="CIF"
			:rules="rules.Required||[]"
			hide-details-obs
		  ></v-text-field>
		<v-text-field
			class="billing-onrc"
			v-model="(billing_person.Company || (billing_person.Company = {})).RegistrationNo"
			label="ONRC"
			:rules="rules.Required||[]"
			hide-details-obs
		  ></v-text-field>
		</div>
		<div>
		<v-text-field
			class="billing-banca"
			v-model="(billing_person.Company || (billing_person.Company = {})).Bank"
			label="Banca"
			hide-details-obs
		  ></v-text-field>
		<v-text-field
			class="billing-cont"
			v-model="(billing_person.Company || (billing_person.Company = {})).BankAccount"
			label="IBAN"
			:rules="rules.IBAN"
			hide-details-obs
		  ></v-text-field>
		<v-text-field class="adresa-companie" v-if="'tf' == mode"
			v-model="((billing_person.Company || (billing_person.Company = {})).HeadOffice || (billing_person.Company.HeadOffice = {})).Details"
			label="Adresa companie"
			hide-details-obs
		  ></v-text-field>
		</div>
		</template>
		</div>
	</div>
		<component ref="coupons" v-if="coupons_component" :is="loadViewAsync(coupons_component)" :total-object="totalPriceObject" :phone="billing_person.Phone" :result="result" v-model="coupon_discount" v-on:coupon_object="(obj) => (coupon_discount_object=obj)"></component>
		<hr />
		<div class="variante-de-plata" v-if="total_to_pay">
		<FormLegend title="Variante de plata" subtitle="Alege optiunea de plata preferata"></FormLegend>
		<v-chip-group
			v-model="payment_method"
			variant="outlined"
			group
			selected-class="bg-primary text-white"
			class="mt-3 flex-wrap"
		  >
			<v-chip v-if="active_payment_methods.online" size="x-large" class="px-8" value="online" prepend-icon="mdi-card-account-details-star-outline">
				Online
			</v-chip>
			<v-chip v-if="active_payment_methods.agency" size="x-large" class="px-8" value="agency" prepend-icon="mdi-home-city-outline">
				La Agentie
			</v-chip>

			<v-chip v-if="active_payment_methods.bank" size="x-large" class="px-8" value="bank" prepend-icon="mdi-bank-transfer-in">
				Prin Banca
			</v-chip>
		</v-chip-group>
		</div>
		<v-select v-if="'online' == payment_method" v-model="post_data.payu_payment_method" :items="<?php echo htmlspecialchars(json_encode($payu_payment_methods), ENT_QUOTES)?>">
		</v-select>
		<hr />
		<div class="bife-tos">
		<FormLegend title="Termeni si conditii"></FormLegend>
		<v-checkbox hide-details-obs v-model="tos" :rules="rules.Required||[]" validate-on="lazy input"><template v-slot:label>Am citit si sunt de acord cu termenii si conditiile pentru utilizarea website-ului.</template></v-checkbox>
		<v-checkbox hide-details-obs v-model="tog" :rules="rules.Required||[]" validate-on="lazy input"><template v-slot:label>Am citit si sunt de acord cu conditiile de garantare ale pachetului.</template></v-checkbox>
		<FormLegend title="Politica de confidentialitate"></FormLegend>
		<v-checkbox hide-details-obs v-model="pp" :rules="rules.Required||[]" validate-on="lazy input"><template v-slot:label>Am citit si sunt de acord cu politica de confidentialitate privind utilizarea website-ului.</template></v-checkbox>
		</div>
		<component ref="validator" v-if="validate_component" :is="loadViewAsync(validate_component)" :result="result" v-on:research="(h, t) => $emit('research', h, t)"></component>
		<v-btn
			class="ms-2 mt-2"
			id="finalizare_rezervare"
			:loading="loadingCheckout"
			@click="validateAndCheckout()"
			size="large"
			text="Finalizeaza rezervarea"
			variant="outlined"
		></v-btn>
	</v-col>
	<v-col cols="12" lg="3" class="checkout-summary-col">
		<v-card class="checkout-summary">
			<v-card-title>Sumar rezervare</v-card-title>
			<v-card-subtitle class="text-wrap">
				<v-icon
				  class="me-2 pb-1"
				  icon="mdi-list-box-outline"
				  size="18"
				></v-icon>
				Recomandam sa reverifici ca totul e corect: nume si clasificare hotel, perioada, servicii incluse in pretul afisat.
			</v-card-subtitle>
			<v-card-text>
				<template v-if="flight">
					<template v-if="flight2">
						<div class="text-h6">Zbor dus-intors</div>
						<v-list-item prepend-icon="mdi-map-marker-outline">
							<span v-text="[search_data.full.Departure.Name, search_data.full.Departure.City, search_data.full.Departure.Country].filter(v => !!v).join(' - ')"></span>
						</v-list-item>
						<v-list-item prepend-icon="mdi-map-marker-outline">
							<span v-text="[search_data.full.Destination.Name, search_data.full.Destination.City, search_data.full.Destination.Country].filter(v => !!v).join(' - ')"></span>
						</v-list-item>
						<div class="text-h6">Data tur-retur</div>
						<span v-text="search_data.full.CheckIn.Name"></span> - <span v-text="search_data.full.CheckOut.Name"></span>
						
						<template v-if="hotel">
							<div class="text-h6">Hotel</div>
							<div class="text-body"v-text="hotel.Name"></div>
							<div class="text-h6" v-if="undefined !== hotel.Stars">
								<v-icon v-if="hotel.Stars" icon="mdi-star" v-for="n in parseInt(hotel.Stars||0)"></v-icon>
								<v-icon v-else icon="mdi-star-off-outline" color="#fcc200"></v-icon>
							</div>
							<div class="text-h6">Perioada</div>
							<span v-text="search_data.full.CheckIn.Name"></span> - <span v-text="search_data.full.CheckOut.Name"></span>
						</template>
					</template>
					<template v-else-if="flight.Routes[1]">
						<div class="text-h6">Zbor dus-intors</div>
						<v-list-item prepend-icon="mdi-map-marker-outline">
							<span v-text="[search_data.full.Departure.Name, search_data.full.Departure.City, search_data.full.Departure.Country].filter(v => !!v).join(' - ')"></span>
						</v-list-item>
						<v-list-item prepend-icon="mdi-map-marker-outline">
							<span v-text="[search_data.full.Destination.Name, search_data.full.Destination.City, search_data.full.Destination.Country].filter(v => !!v).join(' - ')"></span>
						</v-list-item>
						<div class="text-h6">Data tur-retur</div>
						<span v-text="search_data.full.CheckIn.Name"></span> - <span v-text="search_data.full.CheckOut.Name"></span>
					</template>
					<template v-else>
						<div class="text-h6">Zbor doar dus</div>
						<v-list-item prepend-icon="mdi-map-marker-outline">
							<span v-text="[search_data.full.Departure.Name, search_data.full.Departure.City, search_data.full.Departure.Country].filter(v => !!v).join(' - ')"></span>
						</v-list-item>
						<v-list-item prepend-icon="mdi-map-marker-outline">
							<span v-text="[search_data.full.Destination.Name, search_data.full.Destination.City, search_data.full.Destination.Country].filter(v => !!v).join(' - ')"></span>
						</v-list-item>
						<div class="text-h6">Data</div>
						<span v-text="search_data.full.CheckIn.Name"></span>
					</template>
					
					<template v-if="hotel">
						<div class="text-h6">Hotel</div>
						<div class="text-body"v-text="hotel.Name"></div>
						<div class="text-h6" v-if="undefined !== hotel.Stars">
							<v-icon v-if="hotel.Stars" icon="mdi-star" v-for="n in parseInt(hotel.Stars||0)"></v-icon>
							<v-icon v-else icon="mdi-star-off-outline" color="#fcc200"></v-icon>
						</div>
						<div class="text-h6">Perioada</div>
						<span v-text="search_data.full.CheckIn.Name"></span> - <span v-text="search_data.full.CheckOut.Name"></span>
					</template>
				</template>
				<template v-else-if="hotel">
					<div class="text-h6">Hotel</div>
					<div class="text-body"v-text="hotel.Name"></div>
					<div class="text-h6" v-if="undefined !== hotel.Stars">
						<v-icon v-if="hotel.Stars" icon="mdi-star" v-for="n in parseInt(hotel.Stars||0)"></v-icon>
						<v-icon v-else icon="mdi-star-off-outline" color="#fcc200"></v-icon>
					</div>
					<div class="text-h6">Perioada</div>
					<span v-text="search_data.full.CheckIn.Name"></span> - <span v-text="search_data.full.CheckOut.Name"></span>
				</template>
				<template v-else>
				<div v-if="result.Name" class="text-h6" v-text="result.Name"></div>
				<v-list>
					<v-list-item prepend-icon="mdi-map-marker-outline" v-if="result.Address">
						<span v-if="result.Address" v-text="[(result.Address.City||({})).Name, (result.Address.Destination||({})).Name, ((result.Address.City||({})).Country||{}).Name].filter(v => !!v).join(' - ')"></span>
					</v-list-item>
					<v-list-item prepend-icon="mdi-star-circle" v-if="result.Stars">
						Nr. stele <span v-text="result.Stars"></span>
					</v-list-item>
				</v-list>
				<template v-if="search_data.full.CheckIn">
				<div class="text-h6">Check-In</div>
				<span v-text="search_data.full.CheckIn.Name"></span>
				</template>
				<template v-if="search_data.full.CheckOut">
				<div class="text-h6">Check-Out</div>
				<span v-text="search_data.full.CheckOut.Name"></span>
				</template>
				</template>
			</v-card-text>
			<v-card-actions>
				<v-icon
				  class="me-2 pb-1"
				  icon="mdi-credit-card-outline"
				  size="18"
				></v-icon>
				<span class="text-body-2 checkout-offer-price-text">Pretul pachetului ales de tine:</span>
				<span class="ms-auto text-primary text-h5 font-weight-bold text-no-wrap" v-text="format_price_obj_amount_currency(totalPriceObject)"></span>
			</v-card-actions>
			<v-card-actions v-if="coupon_discount">
				<v-icon
				  class="me-2 pb-1"
				  icon="mdi-ticket-percent-outline"
				  size="18"
				></v-icon>
				
				<span class="text-body-2 checkout-offer-price-text">Cupon:</span>
				<span class="ms-auto text-primary text-h5 font-weight-bold text-no-wrap" v-text="'-' + format_price_obj_amount_currency(coupon_discount_object)"></span>
			</v-card-actions>
			<template v-if="offer">
			<v-card-title class="text-wrap">
				Servicii incluse
			</v-card-title>
			<v-card-text>
				<v-list>
					<template v-for="i in (offer.Items.filter(v =>  (-1 == ['0','1'].indexOf((v.Merch || {}).Code))))">
					<v-list-item>
						<p v-if="i.Merch && i.Merch.Title" class="ma-0"><span v-if="i.Merch.type != 'Merch'" v-text="i.Merch.type + ':'"></span> <span v-text="i.Merch.Title"></span></p>
						<div class="ps-5" v-if="0">
							<p v-if="i.Availability" class="ma-0"><span>Availability:</span> <span v-text="i.Availability"></span></p>
							<?php /* <p v-if="i.Quantity" class="ma-0"><span>Quantity:</span> <span v-text="i.Quantity"></span></p>
							<p v-if="i.UnitPrice" class="ma-0"><span>UnitPrice:</span> <span v-text="i.UnitPrice"></span></p>
							<p v-if="i.CheckinBefore" class="ma-0"><span>CheckinBefore:</span> <span v-text="i.CheckinBefore"></span></p>
							<p v-if="i.CheckinAfter" class="ma-0"><span>CheckinAfter:</span> <span v-text="i.CheckinAfter"></span></p>
							<p v-if="i.Currency && i.Currency.Code" class="ma-0"><span>Currency:</span> <span v-text="i.Currency.Code"></span></p>
							<p v-if="i.Merch && i.Merch.type" class="ma-0"><span>Type:</span> <span v-text="i.Merch.type"></span></p>
							<p v-if="i.Merch && i.Merch.Code" class="ma-0"><span>Code:</span> <span v-text="i.Merch.Code"></span></p> */ ?>
						</div>
					</v-list-item>
					</template>
				</v-list>
			</v-card-text>
			</template>
			<template v-if="hotel">
				<v-card-title class="text-wrap">
					Camere
				</v-card-title>
				<v-card-text>
					<template v-if="hotel.Package" v-for="chosen_package in [hotel.Package]">
						<template v-if="chosen_package && chosen_package.SelectedRooms">
							<template v-for="(SelectedRoom, packageRoom_index) in chosen_package.SelectedRooms">
								<hr class="my-4" v-if="packageRoom_index"/>
								<div class="d-flex flex-column ga-2">
									<template v-for="room in [(hotel && chosen_package && chosen_package.PackageRooms.PackageRoom || []).reduce((c, packageRoom, packageRoom_index2) => (packageRoom_index2 != packageRoom_index && c) || (c.Amount+=((packageRoom.RoomRefs.RoomRef.filter(roomRef => (!chosen_package.SelectedRooms ? roomRef.Selected : (packageRoom.PackageRoomCode + ':' + roomRef.RoomCode == chosen_package.SelectedRooms[packageRoom_index]))).reduce((a, r) => a + ((c.Currency = r.Price.Currency, c.Room = r), (r.Price||{}).Amount || 0),0)) || (chosen_package.Price || {}).Amount && (c.Currency = chosen_package.Price.Currency, chosen_package.Price.Amount) || 0), c),{Amount: 0, Currency: 'RON', Room: undefined})]">
									<div class="d-flex w-100 justify-space-between ga-2"><span>Camera #{{ packageRoom_index + 1 }}</span><strong class="text-no-wrap">{{ format_price_obj_amount_currency(room) }}</strong></div>
									<template v-if="room.Room" v-for="roomRef in [room.Room]">
									<div class="d-flex w-100 justify-space-between ga-2" v-if="roomRef.Name">
										<span v-text="roomRef.Name"></span>
									</div>
									<div class="d-flex w-100 justify-space-between ga-2" v-if="roomRef.Board">
										Board: <span v-text="roomRef.Board"></span>
									</div>
									</template>
									</template>
								</div>
								<v-spacer></v-spacer>
							</template>
						</template>
					</template>
				</v-card-text>
			</template>
			<template v-if="flight">
			<v-card-title class="text-wrap">
				Detalii zbor
			</v-card-title>
			<v-card-text>
				<div class="d-flex w-100 justify-space-between mb-2"><span>Bilete</span><strong class="text-no-wrap">{{ format_price((getObjectDotPathValue(flight,'FareDetails.FullFare',0) + getObjectDotPathValue(flight2,'FareDetails.FullFare',0)), getObjectDotPathValue(flight,'Currency')) }}</strong></div>
						<div v-for="pf in (getObjectDotPathValue(flight,'FareDetails.PaxFare') || [])" class="d-flex w-100 justify-space-between"><span>{{ translate_ptc[pf.PTC][1] }} x {{ pf.Count }}</span><span class="text-no-wrap">{{ format_price((getObjectDotPathValue(pf,'FullFare',0) * pf.Count), getObjectDotPathValue(flight,'Currency')) }}</span></div>
						<div v-for="pf in (getObjectDotPathValue(flight2,'FareDetails.PaxFare') || [])" class="d-flex w-100 justify-space-between"><span>{{ translate_ptc[pf.PTC][1] }} x {{ pf.Count }}</span><span class="text-no-wrap">{{ format_price((getObjectDotPathValue(pf,'FullFare',0) * pf.Count), getObjectDotPathValue(flight,'Currency')) }}</span></div>
						<template v-if="flight?.result?.paidOptions?.length || flight2?.result?.paidOptions?.length ">
							<hr class="my-4" />
							<div  class="d-flex w-100 justify-space-between mb-2"><span>Servicii Extra</span><strong class="text-no-wrap">{{ format_price((getObjectDotPathValue(flight,'result.optionsPrice',0) + getObjectDotPathValue(flight2,'result.optionsPrice',0)), getObjectDotPathValue(flight,'Currency')) }}</strong></div>
							<template v-if="flight?.result?.paidOptions?.length">
								<div class="d-flex w-100 justify-space-between mb-2" v-for="option in flight.result.paidOptions">
									<?php ob_start(); ?>
									<div>
										<div v-if="option.Target == 'ALL' && isNaN(parseInt(option.PassengerIndex))"><b>{{ translate_ptc[option.Target][1] }}</b> {{ getObjectDotPathValue(option,'Service.Name') }} ({{ getObjectDotPathValue(option,'Service.CategoryName') }}): <b>{{ getObjectDotPathValue(option,'Option.Description.0') }}</b> ({{ getObjectDotPathValue(option,'Route.From') + '-' + getObjectDotPathValue(option,'Route.To') }})</div>
										<div v-else><b>{{ translate_ptc[option.Target][0] }} #{{ (parseInt(option.PassengerIndex) + 1) }}</b> {{ getObjectDotPathValue(option,'Service.Name') }} ({{ getObjectDotPathValue(option,'Service.CategoryName') }}): <b>{{ getObjectDotPathValue(option,'Option.Description.0') }}</b> ({{ getObjectDotPathValue(option,'Route.From') + '-' + getObjectDotPathValue(option,'Route.To') }})</div>
										<div>{{ [...(option.Description || [])].join('; ') }}</div>
									</div>
									<span class="text-no-wrap">{{ format_price(getObjectDotPathValue(option,'Option.Price.Amount',0), getObjectDotPathValue(option,'Option.Price.Currency')) }}</span>
									<?php $os_text = ob_get_flush(); ?>
								</div>
							</template>
							<template v-if="flight2?.result?.paidOptions?.length">
								<div class="d-flex w-100 justify-space-between mb-2" v-for="option in flight2.result.paidOptions">
									<?php echo $os_text; ?>
								</div>
							</template>
						</template>
						<template v-if="flight?.result?.paidSeats?.length || flight2?.result?.paidSeats?.length">
							<hr class="my-4" />
							<div class="d-flex w-100 justify-space-between mb-2"><span>Locuri preferentiale</span><strong>{{ format_price((getObjectDotPathValue(flight,'result.seatsPrice',0) + getObjectDotPathValue(flight2,'result.seatsPrice',0)), getObjectDotPathValue(flight,'Currency')) }}</strong></div>
							<template v-if="flight?.result?.paidSeats?.length">
								<div class="d-flex w-100 justify-space-between mb-2" v-for="paidSeat in flight.result.paidSeats">
									<?php ob_start(); ?>
									<div v-if="paidSeat.Target == 'ALL' && isNaN(parseInt(paidSeat.PassengerIndex))"><b>{{ translate_ptc[paidSeat.Target][1] }}</b> Loc: {{ getObjectDotPathValue(paidSeat,'seatNumber') }}{{ getObjectDotPathValue(paidSeat,'seatColumn') }} Ruta: {{ getObjectDotPathValue(paidSeat,'Route.From') + '-' + getObjectDotPathValue(paidSeat,'Route.To') }}</div>
									<div v-else><b>{{ translate_ptc[paidSeat.Target][0] }} #{{ (parseInt(paidSeat.PassengerIndex) + 1) }}</b> Loc: {{ getObjectDotPathValue(paidSeat,'seatNumber') }}{{ getObjectDotPathValue(paidSeat,'seatColumn') }} Ruta: {{ getObjectDotPathValue(paidSeat,'Route.From') + '-' + getObjectDotPathValue(paidSeat,'Route.To') }}</div>
									<span class="text-no-wrap">{{ format_price(getObjectDotPathValue(paidSeat,'amount',0), getObjectDotPathValue(paidSeat,'currency')) }}</span>
									<?php $ps_text = ob_get_flush(); ?>
								</div>
							</template>
							<template v-if="flight2?.result?.paidSeats?.length">
								<div class="d-flex w-100 justify-space-between mb-2" v-for="paidSeat in flight2.result.paidSeats">
									<?php echo $ps_text; ?>
								</div>
							</template>
						</template>
						<hr class="my-4" />
						<div class="d-flex w-100 justify-space-between mb-2"><span>Taxa de serviciu</span><strong class="text-no-wrap">{{ format_price((getObjectDotPathValue(flight,'FareDetails.ServiceFee',0) + getObjectDotPathValue(flight2,'FareDetails.ServiceFee',0)), getObjectDotPathValue(flight2,'FareDetails.Currency')) }}</strong></div>
						<div v-for="pf in (getObjectDotPathValue(flight,'FareDetails.PaxFare') || [])" class="d-flex w-100 justify-space-between"><span>{{ translate_ptc[pf.PTC][1] }} x {{ pf.Count }}</span><span class="text-no-wrap">{{ format_price((getObjectDotPathValue(pf,'ServiceFee',0) * pf.Count), getObjectDotPathValue(flight,'Currency')) }}</span></div>
						<div v-for="pf in (getObjectDotPathValue(flight2,'FareDetails.PaxFare') || [])" class="d-flex w-100 justify-space-between"><span>{{ translate_ptc[pf.PTC][1] }} x {{ pf.Count }}</span><span class="text-no-wrap">{{ format_price((getObjectDotPathValue(pf,'ServiceFee',0) * pf.Count), getObjectDotPathValue(flight2,'Currency')) }}</span></div>
						<template v-if="coupon_discount">
							<hr class="my-4" />
							<div class="d-flex w-100 justify-space-between mb-2"><span>Cupoane</span><strong class="text-no-wrap">-{{ format_price(coupon_discount, getObjectDotPathValue(totalPriceObject,'Currency')) }}</strong></div>
						</template>
			</v-card-text>
			</template>
		</v-card>
	</v-col>
	</v-row>
	</v-container>
</v-form>
</template>

<v-dialog v-model="error_dialog" class="detalii-zbor-modal">
<template v-slot:default="{ isActive }">
	<v-card class="align-self-center w-100" style="max-width: min(95vw, 630px);">
	<v-card-text v-html="error_dialog_html"></v-card-text>
	<v-card-actions>
		<v-spacer></v-spacer>
		<v-btn class="d-flex text-none font-weight-normal cancel-button" size="large" variant="outlined" @click="isActive.value = false"><v-icon icon="mdi-arrow-left"></v-icon> Inchide</v-btn>
	</v-card-actions>
  </v-card>
</template>
</v-dialog>

<v-dialog persistent v-model="payment_dialog" class="detalii-zbor-modal" ref="paymentDialog">
<template v-slot:default="{ isActive }">
	<v-card class="align-self-center w-100" style="max-width: min(95vw, 630px);">
	<v-card-text>
		<iframe ref="paymentIframe" style="width: 100%; height: 600px; border: none;"></iframe>
	</v-card-text>
	<v-card-actions>
		<v-spacer></v-spacer>
		<v-btn class="d-flex text-none font-weight-normal cancel-button" size="large" variant="outlined" @click="isActive.value = false"><v-icon icon="mdi-arrow-left"></v-icon> Inchide</v-btn>
	</v-card-actions>
  </v-card>
</template>
</v-dialog>
	<teleport to="#checkout-success-data" v-if="checkoutSuccess">
		<v-expansion-panels v-show="0">
		  <v-expansion-panel
			title="JSON Checkout data"
		  >
			<v-expansion-panel-text>
			<pre class="text-left" v-text="JSON.stringify(checkoutData,null,2)"></pre>
			</v-expansion-panel-text>
		  </v-expansion-panel>
		</v-expansion-panels>
	</teleport>
</v-container>
	`,
}
