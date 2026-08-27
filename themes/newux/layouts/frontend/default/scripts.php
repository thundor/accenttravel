<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<?php 
$this->_ci->load->model('TravelFuse_model');
// $this->providers = $this->_ci->TravelFuse_model->getProviders();
$travelfuse_countries = $this->_ci->TravelFuse_model->searchCountries();
?>
<!-- Webfence by LISAL -->
<script data-host="https://webfence.eu" data-dnt="false" src="https://webfence.eu/js/script.js" id="ZwSg9rf6GA" async defer></script>
<!-- End Webfence Code -->
<script type="importmap">
{
  "imports": {
    "vue": "https://accenttravel.ro/themes/newux/assets/plugins/vue/3.5.11/vue.esm-browser.js",
	"vue-demi": "https://cdn.jsdelivr.net/npm/vue-demi@0.13.11/lib/index.mjs",
    "vuetify": "https://accenttravel.ro/themes/newux/assets/plugins/vuetify/3.7.2/dist/vuetify.esm.js",
    "@vueuse/shared": "https://accenttravel.ro/themes/newux/assets/plugins/vueuse/11.1.0/shared/index.mjs",
    "@vueuse/core": "https://accenttravel.ro/themes/newux/assets/plugins/vueuse/11.1.0/core/index.mjs",
    "@vueuse/components": "https://accenttravel.ro/themes/newux/assets/plugins/vueuse/11.1.0/components/index.mjs",
	"awesome-phonenumber": "https://accenttravel.ro/themes/newux/assets/plugins/awesome-phonenumber/7.2.0/index-esm.mjs",
	"v-phone-input": "https://accenttravel.ro/themes/newux/assets/plugins/v-phone-input/4.3.2/v-phone-input.js",
	"dompurify": "https://cdn.jsdelivr.net/npm/dompurify@3.2.4/dist/purify.es.mjs"
  }
}
</script>

<script type="text/javascript">

/* document.addEventListener("DOMContentLoaded", function(event) {
    console.warn('DOMContentLoaded', event);
}); */
const elementIsVisibleInViewport = (el, partiallyVisible = false) => {
  const { top, left, bottom, right } = el.getBoundingClientRect();
  const { innerHeight, innerWidth } = window;
  return partiallyVisible
    ? ((top > 0 && top < innerHeight) ||
        (bottom > 0 && bottom < innerHeight)) &&
        ((left > 0 && left < innerWidth) || (right > 0 && right < innerWidth))
    : top >= 0 && left >= 0 && bottom <= innerHeight && right <= innerWidth;
};

let window_url = new URL(window.location.href);
const base_window_url = window_url;
window.base_window_url = base_window_url;
const newux_url  = '<?php echo site_url('newux'); ?>';
const append_url='newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : NEWUX_VERSION ; ?>';
const tryJsonParse = function(d, def) {
  var r = def;
  try{
    r = JSON.parse(d);
  } catch(e){}
  return r;
};
function escapeRegExp(text) {
  return text.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, '\\$&');
}
const saveStorage = function(key, data, dot_path) {
	try{
		if(dot_path){
			var d = data;
			data = getStorage(key, '', {});
			if(true === dot_path){
				data = {...data, ...d};
			} else {
				var d2 = data;
				dot_path.split('.').forEach((p,i,arr) => {
					if(i == arr.length -1){
						d2[p] = d;
					} else {
						d2[p] = {};
						d2 = d2[p];
					}
				});
			}
		}
		localStorage.setItem(key, JSON.stringify(data));
	} catch( e ){
		//
	}
};

const getObjectDotPathValue = function(obj, dot_path, def, def_obj, def_arr, nt) {
  var c,d,e,f;
  if(undefined === dot_path || '' === dot_path || ('object' === typeof dot_path && Array.isArray(dot_path) && !dot_path.length)){
    d = obj;
  } else {
    d = (Array.isArray(dot_path) ? dot_path : dot_path.split('.')).reduce((o, i, k, a) => (undefined === o || null === o || undefined === o[i] ? (c = a.splice(k+1), ('*' === i || '' === i) && typeof o == 'object'? (f = Object.values(o).map(v => getObjectDotPathValue(v, [...c], undefined, undefined, undefined, 1)), !a.length || !nt?f:[...f].flat(1).filter((v) => undefined !== v)) : undefined) : o[i]), obj);
  }
  
  if (undefined === def) return d;
  if (typeof def === 'function') return def(d);
  //if (typeof def === 'object' && Array.isArray(def) && (!((typeof d === 'string' && Array.isArray(tryJsonParse(d))) || (typeof d === 'object' && Array.isArray(d))))) return def_arr;

  if ((typeof d == typeof def) && (typeof d !=='object')) return d;
  
  if(undefined === d) return def;
  
  switch (typeof def) {
    case 'boolean':
      return (('false' === d || 'no' === d || '0' === d) ? false : !!d);
    case 'number':
      if (!isNaN(d)) return Number(d);
      return def;
    case 'object':
      if (null === d) return def;
      var def_a = Array.isArray(def);
      switch (typeof d) {
        case 'string':
          var r = tryJsonParse(d);
          if(def_a) return undefined !== r && typeof r=='object' && Array.isArray(r) && r || def_arr;
          return undefined !== r && typeof r=='object' ? (!r && undefined !== def_obj ? def_obj : r) : def_obj;
          break;
      }
      break;
    case 'string':
      switch (typeof d) {
        case 'number':
          return '' + d;
        case 'boolean':
          return d ? 'true' : 'false';
        case 'object':
          return JSON.stringify(d);
		default: 
			return d || def;
		break;
      }
      break;
  }
  return d;
}

// Looks for a local storage item and returns if present
const getStorage = function(key, item, def, def_obj, def_arr) {
    var d = localStorage.getItem(key);
    if(null !== item && false !== item && undefined !== item) {
      d = tryJsonParse(d, def);
    }
    return getObjectDotPathValue(d, item, def, def_obj, def_arr);
};

// Clear a single item or the whole local storage
const clearStorage = function(key=false) {
    if(key) {
        localStorage.removeItem(key);
    } else {
        localStorage.clear();
    }
}
const getPairs = (obj, keys = []) =>
  Object.entries(obj).reduce((pairs, [key, value]) => {
    if (typeof value === undefined){
      
    }
    else if (typeof value === 'object'){
      if(null !== value)
        pairs.push(...getPairs(value, [...keys, key]));
    }
    else
      pairs.push([[...keys, key], encodeURIComponent(value)]);
    return pairs;
  }, []);
const objToSerialize = (obj) => getPairs(obj)
  .map(([[key0, ...keysRest], value]) =>
    `${key0}${keysRest.map(a => `[${a}]`).join('')}=${value}`)
  .join('&');


const formatDate = function (date, options){
	date = date instanceof Date ? date : new Date(date);
	return date.toLocaleDateString('ro', options || {
        year: "numeric",
        month: "long",
        day: "numeric"
      });
}
const dateIntervalFormatted = function (date, days, forceyear){
  var txts = [];
  if(date){
    date = date instanceof Date ? date : new Date(date);
    var today = new Date();
    var date_start = date;
	
	var date_end;
	if(days instanceof Date){
		date_end = days;
	} else if(!isNaN(days) && days) {
		var date_end = new Date(date_start);
		date_end.setDate(date_end.getDate() + days);
	} else if(days) {
		var date_end = new Date(days);
		days = Math.floor(Math.abs(date_end - date_start) / (1000 * 60 * 60 * 24));
		if(!days) date_end = null;
	}
    if(date_end){
      txts.push(date_start.toLocaleDateString('ro', {
        // weekday: "short",
        year: date_start.getYear() == date_end.getYear() ? undefined : "numeric",
        month: date_start.getYear() == date_end.getYear() && date_start.getMonth() == date_end.getMonth() ? undefined : "long",
        day: "numeric"
      }));

      txts.push(date_end.toLocaleDateString('ro', {
        // weekday: "short",
        year: !forceyear && today.getYear() == date_end.getYear() ? undefined : "numeric",
        month: "long",
        day: "numeric"
      }));
    } else {
      txts.push(date_start.toLocaleDateString('ro', {
        // weekday: "short",
        year: today.getYear() == date_start.getYear() ? undefined : "numeric",
        month: "long",
        day: "numeric"
      }));
    }
  }
  if(txts.length) return txts.join(' - ');
}
const minutesToFormattedDuration = function(minutes){
  var m = minutes % 60;
  var h = (minutes - m) / 60;
  return (h ? h.toString() + "h " : "")  + (m < 10 ? "0" : "") + m.toString() + 'm'
}
const secondsToFormattedDuration = function(seconds){
  var minutes = parseInt(seconds / 60);
  var s = seconds % 60;
  var m = minutes % 60;
  var h = (minutes - m) / 60;
  return (h ? h.toString() + "h " : "") + (m ? (m < 10 ? "0" : "") + m.toString() + 'm ' : '') + (s ? (s < 10 ? "0" : "") + s.toString() + 's' : '')
}
const minutesToDuration = function(minutes){
  var m = minutes % 60;
  var h = (minutes - m) / 60;
  return (h < 10 ? "0" : "") + h.toString() + ':'  + (m < 10 ? "0" : "") + m.toString() + ''
}
const durationToMin = function(time){
  var r = time.split(':');
  return 60 * parseInt(r[0]) + parseInt(r[1]);
}
const durationToFormatted = function(time){
  return minutesToFormattedDuration(durationToMin(time))
}
function capitalizeWords(str, lowerfirst) {
  var s = '' + str;
  s = s.trim();
  if(lowerfirst){
    s = s.toLowerCase();
  }
  return s
    .split(/\s+/)
    .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
    .join(' ');
}
const formatDateDM = function(a) {
  a = a instanceof Date ? a : new Date(a);
  return a.toLocaleDateString('ro', {
    month: "short",
    day: "numeric" 
  }); 
};
const formatDateFull = function(a) {
  a = a instanceof Date ? a : new Date(a);
  return a.toLocaleDateString('ro', {
    month: "short",
    day: "numeric" ,
    year: "numeric" 
  }) + ' ' + ('00' + a.getHours()).slice(-2) + ":" + ('00' + a.getMinutes()).slice(-2) + ":" + ('00' + a.getSeconds()).slice(-2) + ''; 
};
const dateToISODate = function(date, format) {
  var d = new Date(date);
  var gmtDate = new Date(d.getTime() - d.getTimezoneOffset() * 60000);
  if(format == 'Y-m-d'){
	  return gmtDate.toISOString().replace(/T.*/,'');
  }
  return gmtDate;
};
function format_price(amount, currency, none_val, plus_minus) {
  if(!none_val && (!amount || !parseFloat(amount) || ('0.00' == amount))){
    return 'Gratuit';
  }
  var symbol = currency;
  var amount_float = parseFloat(amount);
  if (isNaN(amount_float)) {
    return '-';
  } else {
    var amount_formatted = amount_float.toLocaleString('ro', {
      minimumFractionDigits: 0
    });
  }
  if (currency === 'RON') {
    symbol = 'Lei';
  } else if (currency === 'EUR') {
    symbol = '€';
  }
  return (plus_minus ? (amount_float < 0 ? '' : '+') : '') + amount_formatted + ' ' + symbol;
};
const translate_seat = {
  'W': 'Fereastra',
  'A': 'Culoar',
};

const translate_ptc = {
  'ALL': ['Tot','Toti'],
  'ADT': ['Adult','Adulti'],
  'CHD': ['Copil','Copii'], //'Child',
  'YTH': ['Tanar','Tineri'], //'Youth',
  'SEN': ['Senior','Seniori'],
  'INF': ['Infant in brate','Infanti in brate'],
  'INS': ['Infant cu loc scaun','Infanti cu loc scaun'],
};

const translate_ptc_short = {
  ... translate_ptc,
  'INF': ['Inf.br','Inf.br'],
  'INS': ['Inf.br','Inf.sc'],
};

const general_translate_ptc = {
  ... translate_ptc,
  'ALL': ['Toti','Toti'],
  'INF': ['Infant','Infanti'],
  'INS': ['Infant','Infanti'],
};

const scrollElemIntoView = function(elem, opts){
  var o = { 
      scrollMode: 'if-needed',
      block: 'nearest',
      inline: 'nearest',
      ...opts
  };
  scrollIntoView(elem, o);
}
<?php 

$this->_ci->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file')); 
$cache_storage_path = 'pay24/'; 

$cache_hash = $cache_storage_path . 'companies';
$cache_time = 86400;
$companii_marketing = null;
$retrieve_from_cache = true;
if($cache_time !== false && $retrieve_from_cache !== false){
  if($response = $this->_ci->cache->get($cache_hash, $cache_time)){
  $companii_marketing = $response;
  }
} 
if(!isset($companii_marketing)){
  $companii_marketing = array();
  $this->_ci->load->model('Trip/Flights_airlines_model');
  $companies_from_db = $this->_ci->Flights_airlines_model->getAirlines();
  foreach($companies_from_db as $k=>$company_from_db){
    if($company_from_db->image){
    $companii_marketing[$company_from_db->code]['img'] = $this->theme_url . '../accent/assets/images/' . $company_from_db->image;
    }
  }
  // clearExpiredCache($cache_storage_path, $this->_ci->cache);
  setCacheStorage($cache_storage_path);
  $this->_ci->cache->save($cache_hash, $companii_marketing, $cache_time);
}

$cache_hash = $cache_storage_path . 'countries';
$cache_time = 86400;
$countries = null;
$retrieve_from_cache = true;
if($cache_time !== false && $retrieve_from_cache !== false){
  if($response = $this->_ci->cache->get($cache_hash, $cache_time)){
  $countries = $response;
  }
} 
if(!isset($countries)){
  $countries = array();
  $this->_ci->load->model('Country_model');
  $filters = array(
    'select' => array('iso_2 as value','IFNULL(`name_RO`,`name`) as label, phone_prefix as prefix, country_id'),
    'status' => 1,
    'return_rows' => 1,
    'ordering' => 'name ASC',
  );
  $result_countries = $this->_ci->Country_model->getCountries($filters);
  foreach($result_countries as $country){
    $countries[] = $country;
  }
  // clearExpiredCache($cache_storage_path, $this->_ci->cache);
  setCacheStorage($cache_storage_path);
  $this->_ci->cache->save($cache_hash, $countries, $cache_time);
}
?>
const countries = <?php echo json_encode($countries); ?>;
const airlines = <?php echo json_encode($companii_marketing); ?>;
</script>
<script type="module">
import { UseFullscreen } from '@vueuse/components';
import { provide, reactive, ref, createApp, defineAsyncComponent, computed, markRaw, toRaw } from 'vue';
// import { useRoute, useRouter } from 'vue-router'
import { createVuetify, useDisplay, components, directives } from 'vuetify';
import { QTree, QSelect, QItem, QItemLabel, QItemSection, Quasar } from '<?php echo $this->theme_url; ?>/assets/plugins/quasar/2.17.7/dist/quasar.client.js';
import AbsoluteSticky from '<?php echo $this->theme_url; ?>/assets/plugins/absoluteSticky.js'


let step_funcs = [];
let current_step;
let backing = false;
window.step_funcs = step_funcs;
let no_step_funcs = false;
let main_router;
function setMainRouter(obj) {
	main_router = obj;
}
function noStepFuncs(value) {
	if(undefined === value) return no_step_funcs;
	no_step_funcs = value;
}
function setRouterStep(func, link_args) {
	if(no_step_funcs) return;
	console.error('setting Router Step', func, link_args);
	current_step = step_funcs.length;
	
	const url = new URL(window.location.href);
	var obj = { step_funcs_index: step_funcs.length };
	url.searchParams.delete('active_menu');
	url.searchParams.delete('step');
	Object.entries(link_args).forEach(([key, value]) => {
		console.warn('main_router', main_router.$props.activate_menu);
		if(value){
			obj[key] = value;
			if(key === 'active_menu' && main_router.$props.activate_menu && value == main_router.$props.activate_menu && !base_window_url.searchParams['active_menu']){
				return;
			}
			url.searchParams.set(key, value);
		}
	});

	const new_url = url.toString(); // full URL with updated query args
	url.searchParams.delete('step');
	var new_url_no_step = url.toString(); // full URL with updated query args
	
	if(!step_funcs.length){
		if(obj.step){
			// history.replaceState({...obj, step: 0}, document.title, new_url);
			history.pushState(obj, document.title, new_url);
		} else {
			history.replaceState(obj, document.title, new_url);
		}
	} else {
		history.pushState(obj, document.title, new_url);
	}
	window_url = new URL(window.location.href);
	step_funcs.push(func);
}
if ('scrollRestoration' in history) {
	history.scrollRestoration = 'manual';
}
window.addEventListener("popstate",function(event){
	console.error('popstate', event, main_router);
	if(typeof event.state == "object" && event.state && undefined !== event.state.step_funcs_index){
		if(main_router){
			console.log('Should activate', event.state.active_menu);
			noStepFuncs(true);
			main_router.activateMenu(event.state.active_menu || '');
			main_router.data.step = event.state.step || 0;
			main_router.$nextTick(() => {
				noStepFuncs(false);
			});
			return;
		}
	}

},false);
// import { VListItem as Un, VSelect as Dn, VTextField as on } from "vuetify/components";

// history.pushState(null, document.title, window.location);
function loadView(view, fresh) {
  return import(`${newux_url}/${view}.js?${append_url}<?php echo empty($_GET['original']) ? '' : '&original=1'; ?>` + (fresh && '&_=' + Date.now() || ''));
}
let loadedViewAsyncs = {};
function loadViewAsync(view, fresh) {
  return loadedViewAsyncs[view] || (loadedViewAsyncs[view] = defineAsyncComponent(() => import(`${newux_url}/${view}.js?${append_url}<?php echo empty($_GET['original']) ? '' : '&original=1'; ?>` + (fresh && '&_=' + Date.now() || ''))));
}
const deepCopy = obj => undefined === obj && {} || JSON.parse(JSON.stringify(obj))

function removeEmptyObjectsJson(obj) {
	return JSON.stringify(removeEmptyObjects(obj));
}
function removeEmptyObjects(obj) {
	obj = deepCopy(obj);
	if (Array.isArray(obj)) {
		return obj
		  .map(removeEmptyObjects); // Remove empty objects
	} else if (typeof obj === "object" && obj !== null) {
		return Object.keys(obj).reduce((carry, key) => {
		  var item = obj[key];
		  if(item && ('object' === typeof item)) item = removeEmptyObjects(item);
		  if(undefined === item) return carry;
		  if(null === item) return carry;
		  if('' === item) return carry;
		  carry = carry || {};
		  carry[key] = item;
		  return carry;
		}, null);
	}
	if(undefined === obj) return null;
	return obj; // Return non-object values as they are
}

const extendObj = (defaults, obj) => $.extend(true, deepCopy(defaults), deepCopy(obj))
const Modal = loadViewAsync('partials/form/modal');
const VIMG = loadViewAsync('partials/form/v-img');
const VCAROUSELITEM = loadViewAsync('partials/form/v-carousel-item');
const dark = {
  dark: true,
  colors: {
    background: '#252C35',
    surface: '#181D23',
    primary: '#E8D433',
    secondary: '#3B4552',
    // error: '#B00020',
    // info: '#2196F3',
    // success: '#4CAF50',
    // warning: '#FB8C00',
  }
}

const light = {
  light: true,
  colors: {
    anchor: '#42A8FF',
    background: '#fff',
    surface: '#fafafa',
    primary: '#5A9FFC',
    // secondary: '#3B4552',
    error: '#F4971A',
    info: '#02A0FF',
    success: '#86D7B3',
    warning: '#FFC825',
  }
}
console.error('UseFullscreen', UseFullscreen);
const vuetify = createVuetify({
	components,
	directives,
  theme: {
    defaultTheme: 'light',
    themes: {
      light,
      dark,
    }
  }
})
const app = createApp()
app
  .use(Quasar)
  .use(vuetify)
  .component('usefullscreen', UseFullscreen)
  .component('absolute-sticky', AbsoluteSticky);
app.component('v-img', VIMG);
app.component('v-carousel-item', VCAROUSELITEM);

let designGlobal = ref(<?php echo json_encode($this->_ci->theme->_can_edit && !empty($_GET['edit_mode'])); ?>);
let loadingPage = ref(true);
let authpopup = ref('');
window.authPopup = (type) => {
	authpopup.value = type;
};
let feedbackpopup = ref('');
window.feedbackPopup = (type) => {
	feedbackpopup.value = type;
};
<?php if($this->_ci->theme->_can_edit){ ?>
function makeHtmlEditor(selector){
	var $elem = $(selector).makeEditor({height: '500px'});
	var editor = $elem.data().ckeditorInstance;
	//console.warn("Should makeHtmlEditor", editor, editor.ui.contentsElement);
	return editor;
}
let related_window;
function openFilemanager(CKEditorFuncNum, type){
	if(related_window) related_window.close();
	
	type = (type || 'image');
	
	var w = 800;
	var h = 800;
	var left = (screen.width/2)-(w/2);
	var top = (screen.height/2)-(h/2);
	var windowFeatures = 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left;
	related_window = window.open('<?php echo site_url('fileman/index.html')?>?type=' + type + '&CKEditor=newux_file&CKEditorFuncNum=' + CKEditorFuncNum + '&langCode=en&d=resources/images', 'hotelImage', windowFeatures)
}
<?php } ?>
$.holdReady(true);
console.log('Hold ready');
app.mixin({
  data: function() {
    return {
		auth: '<?php echo $this->_ci->user->type == 'guest' ? 'logged-out' : 'logged-in'; ?>',
      travelfuse: {
		  countries: <?php echo json_encode($travelfuse_countries); ?> || [],
	  },
	  mydata: {},
	  translate_seat: translate_seat,
      translate_ptc: translate_ptc,
      general_translate_ptc: general_translate_ptc,
      translate_ptc_short: translate_ptc_short,
      document: document,
      console: console,
    }
  },
  computed: {
    useDisplay: useDisplay,
	breakpoints:{
      get() {
		var a = this.useDisplay;
		
		var breakpoint = a.name.value;
		return Object.keys(a[breakpoint]._object).filter(v => 'boolean' == typeof a[breakpoint]._object[v]).map(v => {
			return {['breakpoint-' + (v.replace(/[A-Z]/g, m => "-" + m.toLowerCase()))]: a[breakpoint]._object[v]};
		});
      },
    },
	loadingPage:{
      get: function() {
		return loadingPage.value;
      },
      set: function(v) {
		loadingPage.value = v;
      },
    },
	authpopup:{
      get: function() {
		return authpopup.value;
      },
      set: function(v) {
		authpopup.value = v;
      },
    },
	feedbackpopup:{
      get: function() {
		return feedbackpopup.value;
      },
      set: function(v) {
		feedbackpopup.value = v;
      },
    },
	designGlobal:{
      get: function() {
		return designGlobal.value;
      },
      set: function(v) {
		designGlobal.value = v;
      },
    },
  },
  methods: {
	setRouterStep: setRouterStep,
	setMainRouter: setMainRouter,
	noStepFuncs: noStepFuncs,
	reactive: reactive,
	titleCase: titleCase,
	markRaw: markRaw,
	toRaw: toRaw,
	loadViewAsync: loadViewAsync,
	format_price: format_price,
	formatDateFull: formatDateFull,
    dateIntervalFormatted: dateIntervalFormatted,
    formatDate: formatDate,
    formatDateDM: formatDateDM,
    minutesToFormattedDuration: minutesToFormattedDuration,
    minutesToDuration: minutesToDuration,
    secondsToFormattedDuration: secondsToFormattedDuration,
    durationToMin: durationToMin,
    durationToFormatted: durationToFormatted,
    capitalizeWords: capitalizeWords,
    getObjectDotPathValue: getObjectDotPathValue,
    deepCopy: deepCopy,
    removeEmptyObjects: removeEmptyObjects,
    removeEmptyObjectsJson: removeEmptyObjectsJson,
    extendObj: extendObj,
	format_price_obj_amount_currency(obj){
		return this.format_price(obj.Amount, obj.Currency);
	},
<?php if($this->_ci->theme->_can_edit){ ?>
    makeHtmlEditor: makeHtmlEditor,
    openFilemanager: openFilemanager,
<?php } ?>
  },
	mounted() {
		// window.addEventListener('scroll', (e) => {document.documentElement.style.setProperty('--scroll-top', `${window.scrollY}px`)});
		/* this.$nextTick(() => {
		window.addEventListener('load', () => {
			this.loadingPage = false;
		})
		});
		setTimeout(() => {
		}, 1000); */
	},
  components: {
	Module: {
		props: {
			id: {
			  type: String,
			},
			template: {
			  type: String,
			  default: () => ('grid'),
			},
		},
		methods:{
			renderFunction(name, template) {
				return {
					emits: ['custom'],
					props: {
						custom: {
						  default: () => ({}),
						},
					},	
					template: `<component :is="loadViewAsync('partials/module/templates/module')" force-name="<?php echo (isset(Modules::$page) ? Modules::$page->page_id : 0) ?>-${name}" mod-template="${template}"></component>`
				}
			}
		},
		template: `<component :is="renderFunction(id, template)"></component>`,
	},
	Gallery: {
		props: {
			images: {
			  type: Array,
			},
		},
		data: () => ({
			fullscreen_image: null,
			errors: {},
			carousel_slide: 0,
		}),
		methods:{
			arrayChunk(arr, len){
				len = len || 4;
				return arr.reduce((all,one,i) => {
				   const ch = Math.floor(i/len); 
				   all[ch] = [].concat((all[ch]||[]),one); 
				   return all
				}, []);
			},
		},
		computed: {
			countimages:{
			  get() {
				let images = this.images;
				return images.length >= 6 ? [6,4,2,260] : (images.length >= 4 ? [4,4,2,260 + 50] : (images.length > 2 ? [2,images.length,2,260 + 100] : [images.length,images.length,images.length,260+150]))
			  },
			},
			myslides:{
			  get() {  
				return this.useDisplay.lgAndUp.value ? this.countimages[0] : (this.useDisplay.mdAndUp.value ? this.countimages[1] : (this.useDisplay.smAndUp.value ? this.countimages[2] : 1))
			  },
			},
		},
		template: `<template v-if="images.length" >
			<UseFullscreen v-slot="{ toggle, enter, isFullscreen }" class="offer-gallery">
			  <div v-if="fullscreen_image && isFullscreen" :class="{'d-none': !isFullscreen}">
				<img :src="errors[fullscreen_image]&& '/themes/newux/assets/images/placeholder.webp' || fullscreen_image" style="width: 100%;height: 80vh;object-fit: contain;" />
			
				<v-btn @click="(carousel_slide = 0), toggle($event)" rounded="xl" style="position:fixed; top: 5px; right: 5px;">
				  Exit Fullscreen
				</v-btn>
			  </div>
			  <template v-for="slides in [(!isFullscreen || myslides > 1) ? myslides : 2]">
				<template v-for="chunked_images in [arrayChunk(images, slides)]">
				  <v-carousel v-model="carousel_slide" :height="isFullscreen ? '20vh' : countimages[3]" class="offer-gallery" hide-delimiters :show-arrows="chunked_images.length > 1">
					<v-carousel-item v-for="images in ((carousel_slide >= chunked_images.length && (carousel_slide = chunked_images.length-1)),chunked_images)">
						<v-container class="offer-gallery-chunk d-flex ga-4 justify-center fill-height px-10">
							<v-row class="fill-height justify-center">
								<v-col v-for="image in images" :cols="12/slides" class="fill-height">
									<v-img aspect-ratio="16/9" :src="errors[image] && '/themes/newux/assets/images/placeholder.webp' || image" cover rounded="lg" @click.stop="((fullscreen_image = image), (!isFullscreen && (carousel_slide = 0)), enter())" class="fill-height" v-on:error="errors[image] = 1"></v-img>
								</v-col>
							</v-row>
						</v-container>
					</v-carousel-item>
				  </v-carousel>
				</template>
			  </template>
			</UseFullscreen>
		</template>`,
	},
	Content: {
		mounted() {
			// $('#content-moved').replaceWith($('#content-real'));
			
			this.$nextTick(() => {
				setTimeout(() => {
					$('#content-real').removeClass('opacity-0');
					loadingPage.value = false;
					console.log('Ready false');
					$.holdReady(false);
				}, 500)
			});
			
		},
		template: `<template v-if="1"><div id="content-real" class="opacity-0"><slot /></div></template>`,
	},
	'Authpopup': {
		emits: ['update:modelValue'],
		props: {
			modelValue: {
			  type: String,
			  default: () => (''),
			},
		},
		data: () => ({
			dialog: false,
			tab: 'login',
			rules: {
				required: [
				  v => !!v && !!(v || '') || 'Necesar',
				],
			},
			forms: {
				login: {},
				register: {
					title: 'mr',
				},
				forgotten: {},
			},
			errors: {
				login: '',
				register: '',
				forgotten: '',
			},
		}),
		methods:{
			submitForm(ref_name) {
				let $ref = this.$refs[ref_name]?.[0];
				if(!$ref) return;
				console.warn('submitForm', ref_name, this.$refs);
				var ref = ref_name.replace(/_form$/, '');
				$ref.validate().then(validation => {
					if(validation.valid){
						var url = '';
						switch(ref){
							case 'login':
								url = "<?php echo site_url('account/login?force_ajax=1'); ?>";
							break;
							case 'forgotten':
								url = "<?php echo site_url('account/resetpass?force_ajax=1'); ?>";
							break;
							case 'register':
								url = "<?php echo site_url('account/register?force_ajax=1'); ?>";
							break;
						}
						var data = {
							<?php if ($this->_ci->config->item('csrf_protection')){ ?>
							<?php echo json_encode($this->_ci->security->get_csrf_token_name()); ?>: <?php echo json_encode($this->_ci->security->get_csrf_hash()); ?>,
							<?php } ?>
							... this.forms[ref]
						};
						fetch(url, {
							// signal: this.abortController.signal,
							method: 'POST',
							headers: {
							  'Accept': 'application/json'
							},
							body: new URLSearchParams(objToSerialize(data))
						}).then((response) => {
							if (!response.ok) {
								if(response.status == 403){
									// CSRF
									window.location.href = window.location.href;
									return;
								}
								throw new Error("Network response was not ok", {cause: response });
							}
							response.json().then((resp) => {
								console.warn('Response', resp)
								if(resp?.message){
                                    this.errors[ref] = resp?.message;
                                }
								if(resp?.data?.url){
									window.location.replace(resp?.data?.url);
								}
							});
							return response;
						}).catch((error) => {
							console.warn('Fetch error', error);
						}).finally((data) => {
							this.testFetching = false;
						}).then(data => {
							console.log('received data', data);
						});
						
						console.warn('Form is valid');
					} else {
						console.warn('Form is NOT valid');
					}
				})
			}
		},
		created() {
			// console.error('AuthPopup', this);
		},
		mounted() {
			// console.error('AuthPopup', this);
		},
		template: `<v-dialog v-model="dialog">
<template v-slot:default="{ isActive }">
<v-card class="align-self-center" style="max-width: 100%;">
	<v-btn variant="outlined"
		icon="mdi-close"
		color="error"
		@click="isActive.value = false"
		density="compact"
		class="position-absolute"
		style="right: 15px;top: 15px;z-index:1;"
	></v-btn>
	<v-sheet>
	<v-window v-model="tab">
		<v-window-item value="login" class="text-center" style="max-width:100%; width:400px">
			<v-card-text>
			  <h3>Intra in cont</h3>
			  <span class="fa fa-2x fa-arrow-circle-o-down"></span>
			  <p>Ai cont deja? Autentifica-te mai jos:</p>
			  <v-form v-for="form in [forms.login]" ref="login_form">
				<div class="d-flex w-100 flex-wrap justify-space-between ga-2 pb-2">
					<v-text-field v-model="form.username" :rules="rules.required" name="username" autofocus type="email" maxlength="255" id="userLogin" label="Introduceti email" min-width="200"/>
					<v-text-field v-model="form.password" :rules="rules.required" name="password" type="password" maxlength="255" id="passLogin" label="Introduceti parola" min-width="200"/>
				</div>
                <v-alert v-if="errors.login" v-html="errors.login"></v-alert>
				<div class="d-flex w-100 flex-wrap justify-space-between ga-2 pb-2">
					<v-checkbox density="compact" name="remember" hide-details id="userRemember" label="Tine-ma minte" class="pt-2"/>
					<v-btn class="flex-fill" min-width="200" density="comfortable" size="large" variant="outlined" @click="submitForm('login_form')">Intra in cont</v-btn>
				</div>
			  </v-form>
			  <?php 
			  $this->_ci->load->model('Options_model');
			  $enabled_social_networks = $this->_ci->Options_model->getKeys('social_networks_status');
			  if($enabled_social_networks) { ?>
			  <p class="text-center">SAU</p>
			  <?php if(in_array('fb', $enabled_social_networks)){
			  $this->_ci->load->library('facebook');
			  $authenticated = $this->_ci->facebook->is_authenticated();
			  ?>
			  <a href="<?php echo $this->_ci->facebook->login_url(); ?>" class="btn btn-info btn-block btn-lg facebook-login text-center"><i class="fab fa-facebook"></i> Conectare cu Facebook</a>
			  <?php } ?>
			  <?php } ?>
			</v-card-text>
		</v-window-item>
		<v-window-item value="forgotten" class="text-center" style="max-width:100%; width:400px">
			<v-card-text>
			  <h3>Am uitat parola</h3>
			  <span class="fa fa-2x fa-arrow-circle-o-down"></span>
			  <p>Ai uitat parola? Reseteaz-o pe e-mail:</p>
			  <v-form v-for="form in [forms.forgotten]" ref="forgotten_form">
				<v-text-field v-model="form.email" autofocus :rules="rules.required" name="email" type="email" maxlength="255" id="userLoginForgot" label="Adresa de email"/>
                <v-alert v-if="errors.forgotten" v-html="errors.forgotten"></v-alert>
				<v-btn class="flex-fill" min-width="200" density="comfortable" size="large" variant="outlined" @click="submitForm('forgotten_form')">Trimite codul pe email</v-btn>
			  </v-form>
			</v-card-text>
		</v-window-item>
		<v-window-item value="register" class="text-center" style="max-width:100%; width:600px">
			<v-card-text>
			  <h3>Creeaza un cont</h3>
			  <span class="fa fa-2x fa-arrow-circle-o-down"></span>
			  <p>Completeaza formularul pentru a-ti crea cont:</p>
			  <v-form v-for="form in [forms.register]" ref="register_form">
				<v-select v-model="form.title" name="title" :items="[{value: 'mr', title: 'Dl.'}, {value: 'mrs', title: 'Dna.'}, {value: 'ms', title: 'Dra.'}]" label="Titlu" max-width="100"></v-select>
			<div class="d-flex w-100 flex-wrap justify-space-between ga-2 pb-2">
				<v-text-field v-model="form.firstname" autofocus name="firstname" type="text" maxlength="255" label="Prenume" min-width="200"/>
				<v-text-field v-model="form.lastname" :rules="rules.required" name="lastname" type="text" maxlength="255" label="Nume" min-width="200"/>
			</div>
			<div class="d-flex w-100 flex-wrap justify-space-between ga-2 pb-2">
				<v-text-field v-model="form.phone" :rules="rules.required" name="phone" type="tel" maxlength="255" label="Numar de telefon" min-width="200"/>
				<v-text-field v-model="form.email" :rules="rules.required" name="email" type="email" maxlength="255" label="Adresa ta de email" min-width="200"/>
			</div>
			<div class="d-flex w-100 flex-wrap justify-space-between ga-2 pb-2">
				<v-text-field v-model="form.password" :rules="rules.required" name="password" type="password" maxlength="255" label="Introduceti parola" min-width="200"/>
				<v-text-field v-model="form.confirm_password" :rules="rules.required" name="confirm_password" type="password" maxlength="255" label="Confirmare parola" min-width="200"/>
			</div>
				<v-checkbox v-model="form.newsletter" hide-details density="compact" name="newsletter" label="Tine-ma la curent cu promotiile si ofertele Accent Travel &amp; Events"></v-checkbox>
			<div class="d-flex w-100 flex-wrap justify-space-between ga-2 pb-2">
				<v-checkbox v-model="form.tos" density="compact" :rules="rules.required" name="tos"><template v-slot:label><div class="text-left">Sunt de acord cu <a target="_BLANK" href="/termeni-si-conditii">Termenii si Conditiile</a> Accent Travel &amp; Events</div></template></v-checkbox>
				<v-checkbox v-model="form.tpc" density="compact" :rules="rules.required" name="tpc"><template v-slot:label><div class="text-left">Sunt de acord cu prelucrarea datelor cu caracter personal conform <a target="_BLANK" href="/declaratie-de-consimtamant">Declaratiei de consimtamant</a></div></template></v-checkbox>
			</div>
                <v-alert v-if="errors.register" v-html="errors.register"></v-alert>
				<div class="d-flex w-100 flex-wrap justify-space-between ga-2 pb-2">
				<v-btn class="flex-fill" min-width="200" density="comfortable" size="large" variant="outlined" @click="submitForm('register_form')">Inregistrare</v-btn>
				</div>
			  </v-form>
			</v-card-text>
		</v-window-item>
	</v-window>
	
		<v-list>
			<v-list-item v-if="'forgotten' != tab" prepend-icon="mdi-lock-question" @click="tab='forgotten'">Mi-am uitat parola</v-list-item>
			<v-list-item v-if="'login' != tab" prepend-icon="mdi-lock" @click="tab='login'">Autentifica-te</v-list-item>
			<v-list-item v-if="'register' != tab" prepend-icon="mdi-key" @click="tab='register'">Nu ai cont? Inregistreaza-te</v-list-item>
		</v-list>
	</v-sheet>
</v-card>
</template>
</v-dialog>`,
		watch: {
			'modelValue': {
				handler(newValue, oldValue){
					console.warn('popup modelValue', newValue);
					this.dialog = !!newValue;
					if(newValue){
						this.tab = newValue;
					}
				},
				immediate: true,
			},
			'dialog': {
				handler(newValue, oldValue){
					!newValue && this.$emit('update:modelValue', '');
				},
			},
		}
	},
	'Feedbackpopup': {
		emits: ['update:modelValue'],
		props: {
			modelValue: {
			  type: String,
			  default: () => (''),
			},
		},
		data: () => ({
			dialog: false,
			snackbar: false,
			snackbar2: false,
			snackbar2_text: '',
			rules: {
				required: [
				  v => !!v && !!(v || '') || 'Necesar',
				],
				email:[
					v => v && !/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*\.[a-zA-Z]{2,}$/.test(v) && 'Invalid' || true,
				],
			},
			forms_d: {
				feedback: {
					disabled: false,
				},
			},
			forms: {
				feedback: {
					category: 'accessibility',
					accessibility: {},
				},
			},
		}),
		methods:{
			submitForm(ref_name) {
				let $ref = this.$refs[ref_name]?.[0];
				if(!$ref) return;
				this.forms_d.feedback.disabled = true;
				console.warn('submitForm', ref_name, this.$refs);
				var ref = ref_name.replace(/_form$/, '');
				$ref.validate().then(validation => {
					if(validation.valid){
						var url = '';
						switch(ref){
							case 'feedback':
								url = "<?php echo site_url('forms/feedback/submit/feedback?force_ajax=1'); ?>";
							break;
						}
						var data = {
							<?php if ($this->_ci->config->item('csrf_protection')){ ?>
							<?php echo json_encode($this->_ci->security->get_csrf_token_name()); ?>: <?php echo json_encode($this->_ci->security->get_csrf_hash()); ?>,
							<?php } ?>
							... this.forms[ref]
						};
						if(data?.category == 'accessibility'){
							var body = [];
							data?.accessibility?.difficulties &&
							body.push("<strong>" + this.$refs['accessibility.difficulties'][0].innerText + '</strong>' + ": \t" + (this.$refs['accessibility.difficulties.' + data.accessibility.difficulties]?.[0]?.label || '-'));
							
							data?.accessibility?.asistive_technology &&
							body.push("<strong>" + this.$refs['accessibility.asistive_technology'][0].label + '</strong>' + ": <div>" + (data.accessibility.asistive_technology || '-') + '</div>');
							
							data?.accessibility?.keyboard &&
							body.push("<strong>" + this.$refs['accessibility.keyboard'][0].innerText + '</strong>' + ': ' + (this.$refs['accessibility.keyboard.' + data.accessibility.keyboard]?.[0]?.label || '-'));
							
							data?.accessibility?.color_read &&
							body.push("<strong>" + this.$refs['accessibility.color_read'][0].innerText + '</strong>' + ': ' + (this.$refs['accessibility.color_read.' + data.accessibility.color_read]?.[0]?.label || '-'));
							
							data?.accessibility?.suggestions &&
							body.push("<strong>" + this.$refs['accessibility.suggestions'][0].label + '</strong>' + ': <div>' + (data.accessibility.suggestions || '-') + '</div>');
							//data.accessibility
							
							data.body = body.join("\n\n");
						}
						fetch(url, {
							// signal: this.abortController.signal,
							method: 'POST',
							headers: {
							  'Accept': 'application/json'
							},
							body: new URLSearchParams(objToSerialize(data))
						}).then((response) => {
							if (!response.ok) {
								if(response.status == 403){
									// CSRF
									window.location.href = window.location.href;
									return;
								}
								throw new Error("Network response was not ok", {cause: response });
							}
							response.json().then((resp) => {
								console.warn('Response', resp)
								if(resp?.data?.url){
									window.location.replace(resp?.data?.url);
								}
								this.forms_d.feedback.disabled = false;
								if(resp?.status == 'success'){
									$ref.reset();
									this.forms.feedback.category = 'accessibility';
									this.dialog = false;
									this.snackbar = true;
								} else {
									this.snackbar2 = true;
									this.snackbar2_text = resp?.message;
								}
							});
							return response;
						}).catch((error) => {
							console.warn('Fetch error', error);
							this.forms_d.feedback.disabled = false;
						}).finally((data) => {
							this.testFetching = false;
						}).then(data => {
							console.log('received data', data);
						});
						
						console.warn('Form is valid');
					} else {
						this.forms_d.feedback.disabled = false;
						console.warn('Form is NOT valid');
					}
				}).catch(e => {
					this.forms_d.feedback.disabled = false;
				})
			}
		},
		created() {
			// console.error('AuthPopup', this);
		},
		mounted() {
			// console.error('AuthPopup', this);
		},
		template: `<v-dialog v-model="dialog">
<template v-slot:default="{ isActive }">
<v-card class="align-self-center" style="max-width: 100%;">
	<v-btn variant="outlined"
		icon="mdi-close"
		color="error"
		@click="isActive.value = false"
		density="compact"
		class="position-absolute"
		style="right: 15px;top: 15px;z-index:1;"
	></v-btn>
	<v-sheet>
	<v-card-text style="max-width:100%; width:800px;" class="text-center">
	  <h3>Formular feedback</h3>
	  <?php /*
	  <span class="fa fa-2x fa-arrow-circle-o-down"></span>
	  <p class="text-start">Completeaza formularul pentru a ne transmite observatii, sugestii, probleme intampinate. Campurile cu date personale de mai jos sunt optionale. Completarea acestora permite inter-comunicarea prin mediul dorit si faciliteaza identificarea dumneavoastra pentru o rezolvare rapida a chestiunii raportate:</p> */ ?>
	  <v-form v-for="form in [forms.feedback]" ref="feedback_form" class="text-start" :disabled="forms_d.feedback.disabled">
		<?php /*<div class="d-flex w-100 flex-wrap justify-space-between ga-2 pb-2">
			<v-text-field v-model="form.firstname" autofocus name="firstname" type="text" maxlength="255" label="Prenume" min-width="200"/>
			<v-text-field v-model="form.lastname" name="lastname" type="text" maxlength="255" label="Nume" min-width="200"/>
		</div>
		<div class="d-flex w-100 flex-wrap justify-space-between ga-2 pb-2">
			<v-text-field v-model="form.phone" name="phone" type="tel" maxlength="20" label="Numar de telefon" min-width="200"/>
			<v-text-field v-model="form.email" name="email" type="email" :rules="rules.email" validate-on="blur" maxlength="255" label="Adresa de email" min-width="200"/>
		</div>
		<div class="d-flex w-100 flex-wrap justify-space-between ga-2 pb-2">
			<v-select v-model="form.category" name="category" :items="[{value: 'accessibility', title: 'Accesibilitate'}, {value: 'suggestion', title: 'Sugestie'}, {value: 'observation', title: 'Observatie'}, {value: 'complaint', title: 'Reclamatie'}, {value: 'other', title: 'Alt tip'}]" label="Tip feedback"  min-width="200"></v-select>
			<v-text-field v-show="form.category != 'accessibility'" v-model="form.subject" name="subject" type="text" maxlength="255" label="Subiect" min-width="200"/>
		</div> */ ?>
		<template v-if="form.category == 'accessibility'">
			<p class="text-start">Ne dorim să îmbunătățim experiența utilizatorilor pe site-ul nostru. Dacă ai o dizabilitate sau folosești tehnologii asistive, te rugăm să ne spui cum ți s-a părut navigarea. Feedback-ul tău este valoros.:</p>
		
			<p class="text-body-1" ref="accessibility.difficulties">Ai întâmpinat dificultăți în utilizarea site-ului?</p>
			<div class="d-flex flex-wrap ga-4">
			  <v-checkbox ref="accessibility.difficulties.yes"
				v-model="form.accessibility.difficulties"
				:value="'yes'"
				label="Da"
				hide-details
				density="compact"
			  ></v-checkbox>

			  <v-checkbox ref="accessibility.difficulties.no"
				v-model="form.accessibility.difficulties"
				:value="'no'"
				label="Nu"
				hide-details
				density="compact"
			  ></v-checkbox>
			</div>
			<v-textarea v-model="form.accessibility.asistive_technology" ref="accessibility.asistive_technology" label="Ce tip de tehnologie asistivă folosești (dacă este cazul)?" min-width="200" rows="2" />
			<p class="text-body-1" ref="accessibility.keyboard">Ai putut accesa conținutul folosind doar tastatura?</p>
			<div class="d-flex flex-wrap ga-4">
			  <v-checkbox ref="accessibility.keyboard.yes"
				v-model="form.accessibility.keyboard"
				:value="'yes'"
				label="Da"
				hide-details
				density="compact"
			  ></v-checkbox>

			  <v-checkbox ref="accessibility.keyboard.no"
				v-model="form.accessibility.keyboard"
				:value="'no'"
				label="Nu"
				hide-details
				density="compact"
			  ></v-checkbox>

			  <v-checkbox ref="accessibility.keyboard.partially"
				v-model="form.accessibility.keyboard"
				:value="'partially'"
				label="Partial"
				hide-details
				density="compact"
			  ></v-checkbox>
			</div>
			<p class="text-body-1" ref="accessibility.color_read">Cum apreciezi lizibilitatea textului și contrastul de culori?</p>
			<div class="d-flex flex-wrap ga-4">
			  <v-checkbox ref="accessibility.color_read.very_good"
				v-model="form.accessibility.color_read"
				:value="'very_good'"
				label="Foarte bun"
				hide-details
				density="compact"
			  ></v-checkbox>

			  <v-checkbox ref="accessibility.color_read.good"
				v-model="form.accessibility.color_read"
				:value="'good'"
				label="Bun"
				hide-details
				density="compact"
			  ></v-checkbox>

			  <v-checkbox ref="accessibility.color_read.satisfactory"
				v-model="form.accessibility.color_read"
				:value="'satisfactory'"
				label="Satisfăcător"
				hide-details
				density="compact"
			  ></v-checkbox>
			  
			  <v-checkbox ref="accessibility.color_read.unsatisfactory"
				v-model="form.accessibility.color_read"
				:value="'unsatisfactory'"
				label="Nesatisfăcător"
				hide-details
				density="compact"
			  ></v-checkbox>
			</div>
			<v-textarea v-model="form.accessibility.suggestions" ref="accessibility.suggestions" name="body" label="Ai sugestii pentru îmbunătățirea accesibilității?" min-width="200" rows="2"/>
		</template>
		<v-textarea v-else-if="form.category == 'suggestion'" v-model="form.body" name="body" label="Sugestia ta:" min-width="200" :rules="rules.required"/>
		<v-textarea v-else-if="form.category == 'observation'" v-model="form.body" name="body" label="Observatia ta:" min-width="200" :rules="rules.required"/>
		<v-textarea v-else-if="form.category == 'complaint'" v-model="form.body" name="body" label="Reclamatia ta:" min-width="200" :rules="rules.required"/>
		<v-textarea v-else v-model="form.body" name="body" label="Mesajul tau:" min-width="200" :rules="rules.required"/>
		
		<v-text-field v-model="form.email" name="email" type="email" :rules="rules.email" validate-on="blur" maxlength="255" label="Adresa de email" min-width="200"/>
		<div class="d-flex w-100 flex-wrap justify-space-between ga-2 pb-2">
			<v-checkbox hide-details v-model="form.tos" density="compact" :rules="rules.required" name="tos"><template v-slot:label><div class="text-left">Sunt de acord cu <a target="_BLANK" href="/termeni-si-conditii">Termenii si Conditiile</a> Accent Travel &amp; Events</div></template></v-checkbox>
			<v-checkbox hide-details v-model="form.tpc" density="compact" :rules="rules.required" name="tpc"><template v-slot:label><div class="text-left">Sunt de acord cu prelucrarea datelor cu caracter personal conform <a target="_BLANK" href="/declaratie-de-consimtamant">Declaratiei de consimtamant</a></div></template></v-checkbox>
		</div>
		<div class="d-flex w-100 flex-wrap justify-space-between ga-2 pb-2">
		<v-btn class="flex-fill" min-width="200" density="default" size="large" variant="outlined" :disabled="forms_d.feedback.disabled" @click="submitForm('feedback_form')">Trimite formularul</v-btn>
		</div>
	  </v-form>
	</v-card-text>
	</v-sheet>
</v-card>
</template>
</v-dialog>
<v-snackbar
  v-model="snackbar"
  :timeout="5000"
>
  Feedback-ul a fost trimis. Iți mulțumim pentru contribuția ta. Daca ai completat adresa de email, vei primi un email de confirmare.
  <template v-slot:actions>
	<v-btn
	  color="primary"
	  variant="outlined"
	  @click="snackbar = false"
	>
	  Inchide
	</v-btn>
  </template>
</v-snackbar>
<v-snackbar
  v-model="snackbar2"
  :timeout="5000"
>
	<div v-html="snackbar2_text"></div>
  <template v-slot:actions>
	<v-btn
	  color="primary"
	  variant="outlined"
	  @click="snackbar2 = false"
	>
	  Inchide
	</v-btn>
  </template>
</v-snackbar>
`,
		watch: {
			'modelValue': {
				handler(newValue, oldValue){
					console.warn('popup modelValue', newValue);
					this.dialog = !!newValue;
					if(newValue){
						this.tab = newValue;
					}
				},
				immediate: true,
			},
			'dialog': {
				handler(newValue, oldValue){
					!newValue && this.$emit('update:modelValue', '');
				},
			},
		}
	},
	Modal: Modal,
	UseFullscreen: UseFullscreen,
	QTree: QTree,
	QSelect: QSelect,
	QItem: QItem,
	QItemLabel: QItemLabel,
	QItemSection: QItemSection,
  },
})

app.mount('main')
// console.warn(app);
</script>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>