export default {
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	inject: ['search'],
	data: () => {
		return {
		}
	},
	props: {
      offer: {
          type: Object,
          default: () => (undefined),
      },
      searching: {
          type: Boolean,
          default: () => (false),
      },
  },
	template : `
	<div class="bg-background" v-if="offer">
		<v-btn
			class="ms-2"
			@click="$emit('back')"
			size="large"
			text="Inapoi"
			variant="outlined"
		></v-btn>
		<div>
			<div v-if="(result = offer || {})" >
				<v-card class="mt-3">
					<div class="d-flex flex-column flex-no-wrap justify-space-between w-100">
					<div class="mb-4">
					  <?php /* <v-avatar
						class="ma-4 mb-0"
						rounded="lg"
						density="compact"
						style="height:200px;width:300px"
					  >
						<v-img :src="(result.MainImage || {}).ExternalUrl"></v-img>
					  </v-avatar> */ ?>
					  <v-carousel v-if="(result.MainImage || {}).ExternalUrl || (((result.Content|| {}).ImageGallery || {}).Items || []).length">
						<v-carousel-item v-if="(result.MainImage || {}).ExternalUrl"
							:src="(result.MainImage || {}).ExternalUrl"
							eager
						  ></v-carousel-item>
						<template
							v-for="i in ((result.Content|| {}).ImageGallery || {}).Items || []"
						  >
						  <v-carousel-item 
							v-if="i.ExternalUrl != (result.MainImage || {}).ExternalUrl"
							:src="i.ExternalUrl"
							eager
						  ></v-carousel-item>

						</template>
					  </v-carousel>
					  <?php /*
					  <template
						v-for="i in ((result.Content|| {}).ImageGallery || {}).Items || []"
					  >
					  <v-avatar
						class="ma-4 mb-0"
						v-if="i.ExternalUrl != (result.MainImage || {}).ExternalUrl"
						rounded="lg"
						density="compact"
						style="height:200px;width:300px"
					  >
						<v-img :src="i.ExternalUrl"></v-img>
					  </v-avatar>
					  </template> */ ?>
					</div>
					  <div class="pa-5 flex-fill">
						<div class="d-flex justify-space-between w-100">
							<div class="flex-fill">
								<v-card-title class="pa-0">
									<div class="text-h6">
										<v-icon v-if="result.Stars" icon="mdi-star" v-for="n in parseInt(result.Stars||0)" color="#fcc200"></v-icon>
										<v-icon v-else icon="mdi-star-off-outline" color="#fcc200"></v-icon>
									</div>
									<div class="text-h5 font-weight-bold" v-text="result.Name"></div>
								</v-card-title>

								<v-card-subtitle class="pa-0">
									<v-icon size="28" icon="mdi-map-marker-outline" class="me-3"></v-icon>
									<span v-text="((result.Address || {}).City||({})).Name + ' - ' + (((result.Address || {}).City||({})).Country||{}).Name"></span>
								</v-card-subtitle>
							</div>
							<div v-for="offer in [(result.Offers||[])[0]||{}]" class="d-flex flex-column">
								<span>de la</span>
								<span class="text-primary text-h4 font-weight-bold" v-text="format_price(offer.Price,(offer.Currency || {}).Code)"></span>
								<span>/persoana/sejur</span>
							</div>
						</div>
						
						<v-card-text class="pa-0">
							<hr class="my-3" />
							<div v-html="(result.Content || {}).Content || ''"></div>
							<div v-for="o in result.Offers">
								<div class="d-flex w-100 justify-space-between">
									<div class="text-h6" v-text="o.Info"></div>
									<div v-for="(tfacilities,ftype) in o.facilities">
										<strong v-text="ftype" v-if="tfacilities.length"></strong>
										<span v-for="(facility) in tfacilities" class="ms-2" v-html="search.merch_type[ftype][facility][1]"></span>
									</div>
									<div class="text-h6 text-primary" v-text="format_price(o.Price, o.Currency.Code)"></div>
								</div>
								<ol class="ps-5">
								<li v-for="i in o.Items">
									<p v-if="i.Merch && i.Merch.Title" class="ma-0"><span>Merch:</span> <span v-text="i.Merch.Title"></span></p>
									<div class="ps-5">
										<p v-if="i.Availability" class="ma-0"><span>Availability:</span> <span v-text="i.Availability"></span></p>
										<p v-if="i.Quantity" class="ma-0"><span>Quantity:</span> <span v-text="i.Quantity"></span></p>
										<p v-if="i.UnitPrice" class="ma-0"><span>UnitPrice:</span> <span v-text="i.UnitPrice"></span></p>
										<p v-if="i.CheckinBefore" class="ma-0"><span>CheckinBefore:</span> <span v-text="i.CheckinBefore"></span></p>
										<p v-if="i.CheckinAfter" class="ma-0"><span>CheckinAfter:</span> <span v-text="i.CheckinAfter"></span></p>
										<p v-if="i.Currency && i.Currency.Code" class="ma-0"><span>Currency:</span> <span v-text="i.Currency.Code"></span></p>
										<p v-if="i.Merch && i.Merch.type" class="ma-0"><span>Type:</span> <span v-text="i.Merch.type"></span></p>
										<p v-if="i.Merch && i.Merch.Code" class="ma-0"><span>Code:</span> <span v-text="i.Merch.Code"></span></p>
									</div>
								</li>
								</ol>
								
								  <v-btn
									class="ms-2"
									@click="(offer = o, hotel = result)"
									size="large"
									text="Rezerva"
									variant="outlined"
								  ></v-btn>
							</div>
						</v-card-text>


						<v-card-actions>
						</v-card-actions>
					  </div>

					</div>
				</v-card>
				
			</div>
		</div>
	</div>
	`,
	beforeCreate() {
	},
	mounted() {
		
	},
	computed: {},
	methods: {},
	watch: {
	}
}
