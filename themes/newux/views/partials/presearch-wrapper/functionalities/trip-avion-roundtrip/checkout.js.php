import BaseFunctionality from '../common/<?php echo basename($a, '.php'); ?>?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';
export default {
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	extends: BaseFunctionality,
	data: () => ({
		mode: 'trip',
		validate_component: 'partials/presearch-wrapper/functionalities/<?php echo basename(dirname($a)); ?>/validate',
		coupons_component: 'partials/presearch-wrapper/functionalities/<?php echo basename(dirname($a)); ?>/coupons',
	}),
	
	methods: {
		checkout: function(){
			this.loadingCheckout = true;
			var v = this.$refs.validator.validate();
			v && v.then(() => {
				/* var billing_info = {};
				billing_info.regcom = billing_info.regcom || '-';
				billing_info.iban = billing_info.iban || '-';
				billing_info.bank = billing_info.bank || '-';
				var phone_prefix_country = countries.find(c => (c.prefix || '').replace(/[^0-9]/,'') == billing_info.phone_prefix);
				billing_info.phone_prefix_country = phone_prefix_country ? phone_prefix_country.value : 'RO'; */
				
				var priceObj = (this.hotel && this.hotel.Package && this.hotel.Package.PackageRooms.PackageRoom || []).reduce((c, packageRoom, packageRoom_index) => (c.Amount+=((packageRoom.RoomRefs.RoomRef.filter(roomRef => (!this.hotel.Package.SelectedRooms ? roomRef.Selected : (packageRoom.PackageRoomCode + ':' + roomRef.RoomCode == this.hotel.Package.SelectedRooms[packageRoom_index]))).reduce((a, r) => a + ((c.Currency = r.Price.Currency), (r.Price||{}).Amount || 0),0)) || (this.hotel.Package.Price || {}).Amount && (c.Currency = this.hotel.Package.Price.Currency, this.hotel.Package.Price.Amount) || 0), c),{Amount: ((this?.flight?.result?.totalPrice || 0) + (this?.flight2?.result?.totalPrice || 0)), Currency: this.flight.Currency});
				
				var data = 
				{
					<?php if ($this->config->item('csrf_protection')){ ?>
					<?php echo json_encode($this->security->get_csrf_token_name()); ?>: <?php echo json_encode($this->security->get_csrf_hash()); ?>,
					<?php } ?>
					data: this.data,
					search_data: this.search_data, 
					...this.post_data,
					billing_person: this.billing_person,
					payment_method: this.total_to_pay ? this.payment_method : 'free',
					flight: {
						code: this.flight.FlightsCode,
						itinerary_code: this.flight.ItineraryCode,
						expected_price: this.flight.result.totalPrice,
						type: this.search_data.type,
						passenger: this.flight.result.serviceData.passenger,
						optionalServices: this.flight.result.serviceData.optionalServices,
						paidSeats: this.flight.result.serviceData.paidSeats,
						upsellCode: this.flight.upsellCode || null,
					},
					flight2: this.flight2 && {
						code: this.flight2.FlightsCode,
						itinerary_code: this.flight2.ItineraryCode,
						expected_price: this.flight2.result.totalPrice,
						type: this.search_data.type,
						passenger: this.flight2.result.serviceData.passenger,
						optionalServices: this.flight2.result.serviceData.optionalServices,
						paidSeats: this.flight2.result.serviceData.paidSeats,
						upsellCode: this.flight2.upsellCode || null,
					} || null,
					hotel: this.hotel && {
						code: this.result.Hotels.code,
						hotel_id: this.hotel.Id,
						expected_price: priceObj.Amount - (this?.flight?.result?.totalPrice || 0) - (this?.flight2?.result?.totalPrice || 0),
						package_code: this.hotel.Package.PackageCode,
						rooms_combinations: this.hotel.Package.SelectedRooms.join('-'),
					} || null,
					price: priceObj.Amount - this.coupon_discount,
					currency: priceObj.Currency,
				};
				
				var fetch_url = `${newux_url}/partials/presearch-wrapper/functionalities/<?php echo basename(dirname($a)); ?>/book.json?${append_url}`; 
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
				})
			}).catch((e) => {
				console.error("Failed to checkout details", e);
				// Do nothing
			}).finally(() => {
				this.loadingCheckout = false;
			});
			// this.$emit('research');
			// return;
			
		},
		format_price_obj_amount_currency(obj){
			return this.format_price(obj.Amount, obj.Currency);
		},
	},
}