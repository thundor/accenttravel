/* Dubla regim single ?? */
let merch_type = {
	Room: {
		"1*": [/\s(1)\s*\*/mi,`
			<i class="mdi-star mdi v-icon notranslate v-theme--light v-icon--size-default" aria-hidden="true" style="color: #fcc200; caret-color: #fcc200;"></i>
		`],
		"2*": [/\s(2)\s*\*/mi,`
			<i class="mdi-star mdi v-icon notranslate v-theme--light v-icon--size-default" aria-hidden="true" style="color: #fcc200; caret-color: #fcc200;"></i>
			<i class="mdi-star mdi v-icon notranslate v-theme--light v-icon--size-default" aria-hidden="true" style="color: #fcc200; caret-color: #fcc200;"></i>
		`],
		"3*": [/\s(3)\s*\*/mi,`
			<i class="mdi-star mdi v-icon notranslate v-theme--light v-icon--size-default" aria-hidden="true" style="color: #fcc200; caret-color: #fcc200;"></i>
			<i class="mdi-star mdi v-icon notranslate v-theme--light v-icon--size-default" aria-hidden="true" style="color: #fcc200; caret-color: #fcc200;"></i>
			<i class="mdi-star mdi v-icon notranslate v-theme--light v-icon--size-default" aria-hidden="true" style="color: #fcc200; caret-color: #fcc200;"></i>
		`],
		"4*": [/\s(4)\s*\*/mi,`
			<i class="mdi-star mdi v-icon notranslate v-theme--light v-icon--size-default" aria-hidden="true" style="color: #fcc200; caret-color: #fcc200;"></i>
			<i class="mdi-star mdi v-icon notranslate v-theme--light v-icon--size-default" aria-hidden="true" style="color: #fcc200; caret-color: #fcc200;"></i>
			<i class="mdi-star mdi v-icon notranslate v-theme--light v-icon--size-default" aria-hidden="true" style="color: #fcc200; caret-color: #fcc200;"></i>
			<i class="mdi-star mdi v-icon notranslate v-theme--light v-icon--size-default" aria-hidden="true" style="color: #fcc200; caret-color: #fcc200;"></i>
		`],
		"5*": [/\s(5)\s*\*/mi,`
			<i class="mdi-star mdi v-icon notranslate v-theme--light v-icon--size-default" aria-hidden="true" style="color: #fcc200; caret-color: #fcc200;"></i>
			<i class="mdi-star mdi v-icon notranslate v-theme--light v-icon--size-default" aria-hidden="true" style="color: #fcc200; caret-color: #fcc200;"></i>
			<i class="mdi-star mdi v-icon notranslate v-theme--light v-icon--size-default" aria-hidden="true" style="color: #fcc200; caret-color: #fcc200;"></i>
			<i class="mdi-star mdi v-icon notranslate v-theme--light v-icon--size-default" aria-hidden="true" style="color: #fcc200; caret-color: #fcc200;"></i>
			<i class="mdi-star mdi v-icon notranslate v-theme--light v-icon--size-default" aria-hidden="true" style="color: #fcc200; caret-color: #fcc200;"></i>
		`],
		"6*": [/\s(6)\s*\*/mi,`
			<i class="mdi-star mdi v-icon notranslate v-theme--light v-icon--size-default" aria-hidden="true" style="color: #fcc200; caret-color: #fcc200;"></i>
			<i class="mdi-star mdi v-icon notranslate v-theme--light v-icon--size-default" aria-hidden="true" style="color: #fcc200; caret-color: #fcc200;"></i>
			<i class="mdi-star mdi v-icon notranslate v-theme--light v-icon--size-default" aria-hidden="true" style="color: #fcc200; caret-color: #fcc200;"></i>
			<i class="mdi-star mdi v-icon notranslate v-theme--light v-icon--size-default" aria-hidden="true" style="color: #fcc200; caret-color: #fcc200;"></i>
			<i class="mdi-star mdi v-icon notranslate v-theme--light v-icon--size-default" aria-hidden="true" style="color: #fcc200; caret-color: #fcc200;"></i>
			<i class="mdi-star mdi v-icon notranslate v-theme--light v-icon--size-default" aria-hidden="true" style="color: #fcc200; caret-color: #fcc200;"></i>
		`],
		"7*": [/\s(7)\s*\*/mi,`
			<i class="mdi-star mdi v-icon notranslate v-theme--light v-icon--size-default" aria-hidden="true" style="color: #fcc200; caret-color: #fcc200;"></i>
			<i class="mdi-star mdi v-icon notranslate v-theme--light v-icon--size-default" aria-hidden="true" style="color: #fcc200; caret-color: #fcc200;"></i>
			<i class="mdi-star mdi v-icon notranslate v-theme--light v-icon--size-default" aria-hidden="true" style="color: #fcc200; caret-color: #fcc200;"></i>
			<i class="mdi-star mdi v-icon notranslate v-theme--light v-icon--size-default" aria-hidden="true" style="color: #fcc200; caret-color: #fcc200;"></i>
			<i class="mdi-star mdi v-icon notranslate v-theme--light v-icon--size-default" aria-hidden="true" style="color: #fcc200; caret-color: #fcc200;"></i>
			<i class="mdi-star mdi v-icon notranslate v-theme--light v-icon--size-default" aria-hidden="true" style="color: #fcc200; caret-color: #fcc200;"></i>
			<i class="mdi-star mdi v-icon notranslate v-theme--light v-icon--size-default" aria-hidden="true" style="color: #fcc200; caret-color: #fcc200;"></i>
		`],
		"neparsat": [/neparsat/mi,'NEPARSAT'],
		"room": [/\b(room|camera)\b/mi,'Camera'],
		"garsoniera": [/garsoniera/mi,'Garsoniera'],
		"apartment": [/aparta?ment/mi,'Apartament'],
		"duplex": [/duplex/mi,'Duplex'],
		"suite": [/suit[ae]/mi,'Suite'],
		"family": [/famil(y|ie)/mi,'Family'],
		"king": [/king/mi,'King'],
		"golden": [/golden/mi,'Golden'],
		"prestige": [/prestige/mi,'Prestige'],
		"premier": [/premier/mi,'Premier'],
		"premium": [/premium/mi,'Premium'],
		"junior": [/junior/mi,'Junior'],
		"deluxe": [/\bde\s*luxe?/mi,'Deluxe'],
		"business": [/business/mi,'Business'],
		"executive": [/executive/mi,'Executive'],
		"economy": [/economy/mi,'Economy'],
		"superior": [/superio(r|ara)/mi,'Superioara'],
		"standard": [/standard/mi,'Standard'],
		"matrimonial": [/matrimonial/mi,'Matrimoniala'],
		"single": [/single/mi,'Single'],
		"double": [/d(bl|ubl[ua]|ouble)/mi,'Dubla'],
		"triple": [/tripl[ae]/mi,'Tripla'],
		"cvadrupla": [/[cq][uv]adrupl[ae]/mi,'Cvadrupla'],
		"terrace": [/(ter(race|asa))/mi,'Terasa'],
		"bungalow": [/bungalo[uw]/mi,'Bungalow'],
		"hotel": [/hotel/mi,'Hotel'],
		"bedroom": [/\bbedroom\b/mi,'2+ dormitoare'],
		"rooms": [/\b(rooms|camere)\b/mi,'2+ camere'],
		"vip": [/\bvip\b/mi,'VIP'],
		"garden": [/\bgarden\b/mi,'Gradina'],
		"twin": [/twin/mi,'Twin'],
		"own-bathroom": [/baie?\s+(privat|propri[ie])/mi,'Baie privata'],
		"balcon": [/cu balcon/mi,'Balcon'],
		// "cu vedere": [/\b(cu vedere|view)\b/mi,'Cu vedere'],
		"mountain view": [/(mountain view|vedere la munte)/mi,'Vedere la munte'],
		"park view": [/(park view|vedere la parc)/mi,'Vedere la parc'],
		"sea view": [/(sea view|vedere la mare)/mi,'Vedere la mare'],
		"garden view": [/(garden view|vedere la gradina)/mi,'Vedere la gradina'],
		"pool view": [/(pool view|vedere la piscina)/mi,'Vedere la piscina'],
		"forest view": [/(forest view|vedere la padure)/mi,'Vedere la padure'],
		"pool access": [/(pool\s+access|acces\s+(gratuit\s+)?(la\s+)?piscina)/mi,'Acces la piscina'],
		"jacuzzi access": [/(jacuzzi\s+access|acces\s+(gratuit\s+)?(la\s+)?jacuzzi)/mi,'Acces la jacuzzi'],
		"jacuzzi": [/jacuzz/mi,'Jacuzzi'],
		"parter": [/parter/mi,'Parter'],
		"etaj": [/etaj/mi,'Etaj'],
		"pod": [/\b(pod|attic)\b/mi,'Pod'],
		"mansarda": [/\b(mansarda|loft)\b/mi,'Mansarda'],
		"baldachin": [/\b(baldachin)\b/mi,'Baldachin'],
		"oferta": [/\b(oferta)\b/mi,'Oferta'],
		// "large": [/\b(large)\b/mi,'Large'],
	},
	Merch: {
		"pool": [/piscina/mi,'Piscina'],
		"sauna": [/sauna/mi,'Sauna'],
		"fitness": [/fitness/mi,'Fitness'],
		"wellness": [/fitness/mi,'Wellness'],
		"spa": [/\bspa\b/mi,'Spa'],
		"pool access": [/(pool\s+access|acces\s+(gratuit\s+)?(la\s+)?piscina)/mi,'Acces la piscina'],
		"jacuzzi access": [/(jacuzzi\s+access|acces\s+(gratuit\s+)?(la\s+)?jacuzzi)/mi,'Acces la jacuzzi'],
	},
	Meal: {
		"bb": [/(\bbb\b|mic dejun)/mi,'Mic dejun'],
		"dp": [/\b(dp|hb|demipensiune)\b/mi,'Demipensiune'],
		"fb": [/^(fb|pc)$/mi,'Pensiune completa'],
		"ro": [/^ro$/mi,'In camera'],
		"fm": [/^(fm|fara masa)$/mi,'Fara masa'],
		"ai": [/^ai$/mi,'All inclusive'],
		"uai": [/^uai$/mi,'Ultra All inclusive'],
		"pranz": [/\bpranz\b/mi,'Pranz'],
		"cina": [/\bcina\b/mi,'Cina'],
		"bufet": [/\bbufet\b/mi,'Bufet'],
		"platit": [/\bplatit\b/mi,'Neinclus'],
		"inclus": [/\binclus\b/mi,'Inclus'],
	},
	Other: {
		"aer conditionat": [/aer conditionat/mi,'Aer conditionat'],
		"wi-fi": [/(wi\s*-?\s*fi|wireless)/mi,'Wi-Fi'],
		"parcare": [/parcare/mi,'Parcare'],
		"terrace": [/teras[ae]/mi,'Terasa'],
		"bar": [/\bbar(\b|[A-Z])/,'Bar'],
		"fumatori": [/\bfumatori\b/,'Fumatori'],
		"nefumatori": [/nefumat/,'Nefumatori'],
		"pool": [/piscin[ae]/mi,'Piscina'],
		"jacuzzi": [/jacuzz/mi,'Jacuzzi'],
		"own-bathroom": [/baie\s+(privat|cu\s+dus)/mi,'Baie privata'],
		"gradina": [/gradina/mi,'Gradina'],
		"hidromasaj": [/hidromasaj/mi,'Hidromasaj'],
		"istoric": [/istoric/mi,'Specific istoric'],
		"lounge": [/lounge/mi,'Lounge'],
		"minibar": [/minibar/mi,'Minibar'],
		"restaurant": [/restaurant/mi,'Restaurant'],
		"dus tropical": [/dus tropical/mi,'Dus tropical'],
		"baie de aburi": [/baie de aburi/mi,'Baie de aburi'],
		"tv": [/\b(tv|televizor)\b/mi,'TV'],
		"spalatorie": [/\bspalatorie\b/mi,'Spalatorie'],
		"room service": [/\broom service\b/mi,'Room service'],
		"pos": [/\bplata\s+(cu\s+)?card(ul)?\b/mi,'Plata card'],
		"foisor": [/\bfoisor\b/mi,'Foisor'],
		"gratar": [/\b(bbq|barbeque|gratar)\b/mi,'Gratar'],
		"sala conferinte": [/\b(Sal[ai]\s+(de+)?conferint[ae])\b/mi,'Sala de conferinte'],
		"internet cablu": [/(internet.*cablu)|(cablu.*internet)/mi,'Internet prin cablu'],
		"sauna": [/sauna/mi,'Sauna'],
		"sala de sport": [/sala de sport/mi,'Sala de sport'],
		"dus walk in": [/dus(uri)? walk\s*-?\s*in/mi,'Dus walk-in'],
	},
}
export default {
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	data: () => {
		return {
			step: 0,
			offer: undefined,
			formatted_results: [],
			filtered_results: [],
			searching: false,
		}
	},
	template : `
	<component :is="loadViewAsync('partials/presearch')" v-on:results="(r) => this.formatted_results = mapResults(r)" v-on:searching="(r) => this.searching = r"></component>
	<v-container>
	<v-row>
		<v-col cols="4">
			<component :is="loadViewAsync('partials/filters')" :results="formatted_results" :searching="searching" v-on:filtered="(r) => this.filtered_results = r"></component>
		</v-col>
		<v-col cols="8">
			<component :is="loadViewAsync('partials/results')" :results="filtered_results" :searching="searching" v-on:offer="(r) => r && (this.offer = r, this.step = 1)"></component>
		</v-col>
	</v-row>
	</v-container>
	
	<v-window direction="vertical" v-model="step" style="position:fixed;left:0;right:0;bottom:0;top:90px;z-index:1000" :style="{'pointer-events': !step ? 'none' : null}" class="fixed-fwh d-flex">
		<v-window-item style="pointer-events:none;" class="fill-width fill-height overflow-auto"></v-window-item>
		<v-window-item class="fill-width fill-height overflow-auto">
			<component :is="loadViewAsync('partials/offer')" :offer="offer" :searching="searching" v-on:back="(r) => (this.step = 0)"></component>
		</v-window-item>
	</v-window>
	`,
	beforeCreate() {},
	mounted() {},
	computed: {},
	methods: {
		mapResults(results){
			return Object.freeze(JSON.parse( JSON.stringify( results ) ).map((h) => {
				h.Stars = h.Stars || 0;
				h.Offers = h.Offers.map(o => {
					o.facilities = Object.keys(merch_type).reduce((c,type) => {
						c[type] = [...new Set((c[type] || []).concat(Object.keys(merch_type[type]).filter(r => -1 !== [...o.Items,{
							"Merch": {
								"Title": (h.Content || {}).Content || h.ShortContent,
								"type": "Other"
							}
						}].findIndex((i) => i.Merch && i.Merch.Title && i.Merch.type == type && merch_type[type][r][0].test(i.Merch.Title + (/^(fm|fara masa)$/mi.test(i.Merch.Title) ? '' : "\n" + (i.UnitPrice ? 'platit' : 'inclus')) + (type == 'Merch' ? "\n" + o.Info : '') )))
						
						))];
						if('Meal' == type && !c[type].length){
							c[type].push('fm');
						}
						if('Room' == type && !c[type].length){
							c[type].push('neparsat');
						}
						return c;
					}, {});
					o.all_facilities = [...new Set(Object.values(o.facilities).flat())];
					return o;
				})
				return h;
			}));
		}
		
	},
	watch: {
		'filtered_results': {
			handler: function(nv,ov){
				console.warn('showing filtering results', nv);
			},
			immediate: true
		},
	},
	provide() {
		return {
			search:{
				merch_type: merch_type,
			},
		}
	}
}
