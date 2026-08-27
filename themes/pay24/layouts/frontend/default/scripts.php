<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::debugFileLine('start'); ?>

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
<?php /* echo '<pre>'; print_r($this); die; */ ?>
<?php /* <button type="button" style="position:fixed;right:0;bottom:0;z-index:999999999;background: red;color:white;padding: 10px;" onclick="window.location.href = window.location.href.replace(/(&|\?)pay24=1/,'$1') + (-1 == window.location.href.indexOf('?') ? '?' : '&') + 'pay24=1'">Reload</button>*/ ?>
<textarea id="accountinfo" style="display:none;position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    height: 50vh;
    overflow: scroll;
    z-index: 1;
    background: red;"></textarea>
<script src="https://cdn.jsdelivr.net/npm/quasar@2.11.6/dist/quasar.umd.prod.js"></script>
<script src="<?php echo $this->_ci->theme->theme_url ?>assets/plugins/vue3-q-tel-input/js/vue3-q-tel-input.min.js?v=1.0.0"></script>
<?php /*<script type="module" src="https://cdn.jsdelivr.net/npm/vue3-scroll-picker@0.1.15/dist/vue3-scroll-picker.umd.min.js"></script>*/ ?>
<?php if (!empty($_GET['testtudor'])) { ?>
<script type="module" name="VueSearch">
/* import {VueFuse, useVueFuse} from 'https://cdn.jsdelivr.net/npm/vue-fuse@4.1.1/+esm'
window.VueFuse = VueFuse;
window.useVueFuse = useVueFuse; */
/* const { useVueFuse } = Fuse; */
/* const searcher = {
    bikes: [{name: "def"}, {name: "bcd"}, {name: "cde"}, {name: "abc"}],
    keys: ['name'],
};

const { search, results, noResults } = useVueFuse(searcher)
console.warn(searcher); */
</script>
<?php } ?>
<script type="text/javascript">
const {createRouter, createWebHistory, RouterLink, RouterView } = VueRouter
const { createApp, defineAsyncComponent, computed } = Vue
const { createVuetify, ThemeDefinition } = Vuetify;
<?php if (!empty($_GET['testtudor'])) { ?>
/* import('https://cdn.jsdelivr.net/npm/vue-fuse@4.1.1/+esm').then(function(d){
	var flight_locations;
	var obj = fetch('/resources/flight_locations.js').then((a) => {
		flight_locations = await a.json();
		console.warn('flight_locations', flight_locations);
	});
	const {VueFuse, useVueFuse} = d;
	console.warn(useVueFuse);
	const list = [{name:'sadf'}, {name:'zxcv'}];
	const options = {
		keys:[{name: 'name', weight: 1}],
		defaultAll: true,
	};
	const duseVueFuse = useVueFuse(list, options);
	const { search, results, noResults } = duseVueFuse;
	search.value = 'sadf';
	// var res = await results.value;
	// console.log(res);
	console.log(duseVueFuse);
	console.log(search);
	console.log(results);
	console.log(results.value);
	setTimeout(()=> console.log(results.value), 1000)
}); */
// const VueFuse = import('https://cdn.jsdelivr.net/npm/vue-fuse@4.1.1/+esm');
/* 
 */
/*  */
<?php } ?>
// const { ScrollPicker } = 'https://cdn.jsdelivr.net/npm/vue3-scroll-picker@0.1.15/dist/vue3-scroll-picker.umd.min.js';
// const { ScrollPicker } = 'vue3-scroll-picker';
if(!window['flight_data']){
	const flight_data = undefined;
}
const countries = <?php echo json_encode($countries); ?>;
const airlines = <?php echo json_encode($companii_marketing); ?>;

<?php if(empty($_GET['debug'])){ ?>
console = {
	  log: function(){},
	  warn: function(){},
	  error: function(){},
	  table: function(){},
};
<?php } ?>


const fallbackCopyTextToClipboard = function(text) {
  var textArea = document.createElement("textarea");
  textArea.value = text;
  
  // Avoid scrolling to bottom
  textArea.style.top = "0";
  textArea.style.left = "0";
  textArea.style.position = "fixed";

  document.body.appendChild(textArea);
  textArea.focus();
  textArea.select();

  try {
    var successful = document.execCommand('copy');
    var msg = successful ? 'successful' : 'unsuccessful';
    console.log('Fallback: Copying text command was ' + msg);
  } catch (err) {
    console.error('Fallback: Oops, unable to copy', err);
  }

  document.body.removeChild(textArea);
}
const loseActiveFocus = function () {
	document.activeElement.blur()
	return true;
}
const copyTextToClipboard = function (text) {
  document.getElementById('accountinfo').innerHTML = text;
  if (!navigator.clipboard) {
    fallbackCopyTextToClipboard(text);
    return;
  }
  navigator.clipboard.writeText(text).then(function() {
    console.log('Async: Copying to clipboard was successful!');
  }, function(err) {
    console.error('Async: Could not copy text: ', err);
  });
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
  
class customURLSearchParams {
	constructor(data){
		this.URLSearchParams = new URLSearchParams();
		if(data){
			for (const property in data) {
        this.append(property, data[property]);
			}
		}
	}
	append(k, v){
		if(undefined === v || null === v){
			return;
		}
		this.URLSearchParams.append(k,v);
	}
	toString(){
		return this.URLSearchParams.toString();
	}
}
const dateIntervalFormatted = function (date, days){
  var txts = [];
  if(date){
    date = date instanceof Date ? date : new Date(date);
    var today = new Date();
    var date_start = date;
    if(days){
      var date_end = new Date(date_start);
      date_end.setDate(date_end.getDate() + days);

      txts.push(date_start.toLocaleDateString('ro', {
        // weekday: "short",
        year: date_start.getYear() == date_end.getYear() ? undefined : "numeric",
        month: date_start.getYear() == date_end.getYear() && date_start.getMonth() == date_end.getMonth() ? undefined : "long",
        day: "numeric"
      }));

      txts.push(date_end.toLocaleDateString('ro', {
        // weekday: "short",
        year: today.getYear() == date_end.getYear() ? undefined : "numeric",
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
  }) + ' ' + ('00' + a.getHours()).slice(-2) + ":" + ('00' + a.getMinutes()).slice(-2) + ":" + ('00' + a.getSeconds()).slice(-2); 
};
const format_price = function(amount, currency, none_val) {
  if(!none_val && (!amount || !parseFloat(amount) || ('0.00' == amount))){
    return 'Gratuit';
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
};
const tryJsonParse = function(d, def) {
  var r = def;
  try{
    r = JSON.parse(d);
  } catch(e){}
  return r;
};
// Sets an item with a Key to local storage
const saveStorage = function(key, data) {
	try{
		localStorage.setItem(key, JSON.stringify(data));
	} catch( e ){
		//
	}
};
const back_press = function(e){
	try{
		var dialog__backdrop = document.querySelector('.q-dialog__backdrop');
		if(dialog__backdrop){
			dialog__backdrop.click();
			return 'false';
		}
		var popupcloser = document.querySelector('.popup-closer');
		if(popupcloser){
			popupcloser.click();
			return 'false';
		}
		var backbutton = document.querySelector('#backbutton a.backlink');
		if(backbutton){
			backbutton.click();
			return 'false';
		}
	} catch(err){
		console.error(err);
	}
	communicateWithPay24('close');
	return true;
}
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

const mapObjKeys = function(keys, data, unmap){
  var r = {};
  if(unmap){
    r = Object.assign({}, data);
    for (k in keys){
      var k2 = keys[k] || 0 === keys[k] ? keys[k] : k;
      if(typeof k2 === 'object' && Array.isArray(k2)){
        if('object' === typeof data[k2[0]] && data[k2[0]]){
          r[k] = mapObjKeys(k2[1],data[k2[0]], true);
        }
        if(undefined !== r[k2[0]]) delete(r[k2[0]]);
      } else {
        if(undefined !== data[k2]){
          r[k] = data[k2];
          if(undefined !== r[k2]) delete(r[k2]);
        }
      }
    }
  } else {
    for (k in keys){
      var k2 = keys[k] || 0 === keys[k] ? keys[k] : k;
      if(undefined !== data[k]){
        if(typeof k2 === 'object' && Array.isArray(k2)){
          r[k2[0]] = mapObjKeys(k2[1],data[k]);
        } else {
          r[k2] = data[k];
        }
      }
    }
  }
  return r;
}

const scrollElemIntoView = function(elem, opts){
  var o = { 
      scrollMode: 'if-needed',
      block: 'nearest',
      inline: 'nearest',
      ...opts
  };
  scrollIntoView(elem, o);
}
let pay24Account = undefined;
<?php /* // const pay24Account = {"profile":{"id":[],"drive_licence":{"birth_date":"1989-11-23","cnp":"1891123375477","first_name":"Chirvasa","last_name":"Tudor"},"personal_data":{"adress":"Furtunei 2A","birth_date":"1989-11-23","citizenship":"Romana","cnp":"1891123375477","email":"tchirvasa@gmail.com","first_name":"Chirvasa","last_name":"Tudor","ocupation":"Programator","phone":"+4077125527"}},"associated_persons":[{"id":[],"drive_licence":{"first_name":"lucian oprea"},"personal_data":{"first_name":"lucian oprea"}},{"id":[],"drive_licence":{"first_name":"oprea alexandra"},"personal_data":{"first_name":"oprea alexandra"}}]}; */ ?>
const payUmethodMapper = {
	'accountinfo' : 'getMyAccount',
}
const browserDevice = 'object' == typeof window['I24Pay'] ? 'android' : ('object' == typeof window['webkit'] && 'object' == typeof webkit.messageHandlers ? 'ios' : 'site');
const communicateWithPay24 = function(){
  var args = [...arguments];
  var func = args.shift();
  try{
		var func2 = payUmethodMapper[func] || func;
	  if('object' == typeof window['I24Pay']){
		if('function' === typeof I24Pay[func2]){
		  return I24Pay[func2](...args);
		} else {
		  console.error('Unable to communicate with Pay24 Android-style' + JSON.stringify(Object.keys(I24Pay)));
		}
	  } else if('object' == typeof window['webkit'] && 'object' == typeof webkit.messageHandlers){
		if('undefined' !== typeof webkit.messageHandlers[func2]){
		  if('function' === typeof webkit.messageHandlers[func2].postMessage){
			args = args.length == 0 ? [""] : args;
			// document.getElementById('datetoch').innerText = JSON.stringify(arguments);
			return webkit.messageHandlers[func2].postMessage(...args);
		  } else {
			console.error('Unable to communicate with Pay24 IOS-style endpoint');
		  }
		} else {
		  console.error('Unable to communicate with Pay24 IOS-style');
		}
	  } else {
		console.error('Unable to communicate with Pay24 ANY-style');
	  }
  } catch(e){
	  console.error(func, args, e);
  }
}
function saveLogData(log_data){
	var url = '<?php echo site_url('pay24/setStep?force_ajax=1&pay24=1'); ?>';
	axios.post(url, new customURLSearchParams({
        <?php if ($this->_ci->config->item('csrf_protection')){ ?>
        <?php echo json_encode($this->_ci->security->get_csrf_token_name()); ?>: <?php echo json_encode($this->_ci->security->get_csrf_hash()); ?>,
        <?php } ?>
		...log_data
  }).URLSearchParams, {
        headers: {
          'X-Requested-With': '<?php echo $_SERVER['HTTP_X_REQUESTED_WITH']; ?>',
        },
  })
}
saveLogData({device: browserDevice});
const myAccount = function(json){
  pay24Account = json ? (typeof json =='string' && JSON.parse(json) || json) : null;
  if(pay24Account){
	  saveLogData({step_data: JSON.stringify({account: pay24Account || {}})});
  }
  
  // document.getElementById('accountinfo').innerHTML = "1: " + document.getElementById('accountinfo').innerHTML + json + ' ' + pay24Account + "\n";
  // document.getElementById('accountinfo').style.display='block';
  
  //copyTextToClipboard(json);
<?php /*  
  alert(communicateWithPay24('pay', JSON.stringify({
    order_id:<?php echo json_encode(preg_replace('/\//','$', $this->_ci->encryption->encrypt('444'))); ?>,
    description: 'blabla',
    amount: '120.00',
    currency: 'EUR',
    date: new Date().toISOString(),
    contact:{
      nume: "Tudor",
      prenume: "Tudor",
      phone: "+407712551121",
      email: "tudor.chirvasa@lisal.ro",
    },
  })));
  */ ?>
}




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
      }
      break;
  }
  return d;
}

// Looks for a local storage item and returns if present
const getStorage = function(key, item, def, def_obj, def_arr) {
    var d = localStorage.getItem(key);
    if(typeof def == 'object') {
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

function loadView(view) {
  return import(`<?php echo site_url('pay24'); ?>/${view}.js?pay24=1`);
}
function loadViewAsync(view) {
  return defineAsyncComponent(() => import(`<?php echo site_url('pay24'); ?>/${view}.js?pay24=1`));
}

const HomeView = loadViewAsync('<?php echo isset($this->view_data['homeview']) ? $this->view_data['homeview'] : 'flights'; ?>');
const Modal = loadViewAsync('modal');
const FlightsLoader = {
	template : `<div class="text-center mt-5" style="min-height:150px;position:relative;">
	<h5 class="v-theme--dark text-primary text-center mb-5 mt-5">Esti foarte aproape de destinatia ta!</h5>
	<div class="position-absolute w-100">
	<div class="w-100 mx-auto text-center">
    <v-progress-circular
      theme="dark"
      :size="70"
      :width="7"
      color="primary"
      indeterminate
    ></v-progress-circular>
	</div>
	</div>
  </div>`,
};
const IOSDatepicker = loadViewAsync('datepicker');
/* 
const router = createRouter({
	history: createWebHistory('/'),
	routes: [
		{
			path: '/',
			name: 'flights',
			displayName: 'Flights',
			component: () => loadView('flights'),
		}
	]
});
<?php if ($u0 = $this->_ci->uri->segment(0)){ ?>
  var v = <?php echo json_encode($u0); ?>;
  router.addRoute({
      path: '/' + v,
      name: v,
      displayName: v,
      component: () => loadView(v),
  })
<?php } ?>
 */
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

const vuetify = createVuetify({
  theme: {
    defaultTheme: 'dark',
    // defaultTheme: 'dark',
    themes: {
      dark,
    }
  }
})
const redirectToFlightsWithError = function(err){
  console.error(arguments);
}

const app = createApp()
app
  .use(Quasar)
  .use(vuetify)
  // .use(router)
  // .use(ScrollPicker)
function syntaxHighlight(json) {
	if(undefined === json) json = null;
    if (typeof json != 'string') {
         json = JSON.stringify(json, undefined, 2);
    }
    json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
        var cls = 'number';
        if (/^"/.test(match)) {
            if (/:$/.test(match)) {
                cls = 'key';
            } else {
                cls = 'string';
            }
        } else if (/true|false/.test(match)) {
            cls = 'boolean';
        } else if (/null/.test(match)) {
            cls = 'null';
        }
        return '<span class="' + cls + '">' + match + '</span>';
    });
}
app.component('Vue3QTelInput', Vue3QTelInput)
app.component('Datepicker', VueDatePicker)
app.component('IOSDatepicker', IOSDatepicker)
app.component('home-view', HomeView)
app.component('Modal', Modal)
app.component('FlightsLoader', FlightsLoader)
// app.component('ScrollPicker', ScrollPicker)
// app.component('scroll-picker', ScrollPicker)
app.mixin({
  data: function() {
    return {
      document: document,
      translate_seat: translate_seat,
      translate_ptc: translate_ptc,
      general_translate_ptc: general_translate_ptc,
      translate_ptc_short: translate_ptc_short,
      console: console,
      countries: countries,
      airlines: airlines,
    }
  },
  computed: {
    order_data:{
      get() {
        return order_data;
      },
    },
    testtudor:{
      get() {
        return /testtudor/.test(window.location.search);
      },
    },
    pay24Account:{
      get() {
        return syntaxHighlight(pay24Account);
      },
    },
  },
  methods: {
    loseActiveFocus: loseActiveFocus,
    format_price: format_price,
    formatDateFull: formatDateFull,
    dateIntervalFormatted: dateIntervalFormatted,
    formatDateDM: formatDateDM,
    minutesToFormattedDuration: minutesToFormattedDuration,
    minutesToDuration: minutesToDuration,
    durationToMin: durationToMin,
    durationToFormatted: durationToFormatted,
    capitalizeWords: capitalizeWords,
    getObjectDotPathValue: getObjectDotPathValue,
    redirectToFlightsWithError: redirectToFlightsWithError,
    communicateWithPay24: communicateWithPay24,
    ob: ob,
  },
})

// function surpressRouterError(){
//   console.log('supress')
// }
// router.onError('surpressRouterError');
/*
app.config._warnHandler_ = app.config.warnHandler;

app.config.warnHandler = (msg, instance, trace) =>
  ![
    '<router-view> can no longer be used directly inside <transition> or <keep-alive>.',
    // 'built-in or reserved HTML elements as component id: component',
    // '"class" is a reserved attribute and cannot be used as component prop',
    // 'Cannot find element: #__nuxt'
  ].some((warning) => msg.includes(warning)) &&
  app.config._warnHandler_(msg, instance, trace)
*/
app.mount('#page-content')
// console.log(router);

function ob(e){
	var el = e.srcElement;
	// console.warn(el);
	//console.warn(e);
	if(('object' == typeof window['webkit'] && 'object' == typeof webkit.messageHandlers) || ('object' == typeof window['I24Pay'] && 'function' === typeof I24Pay['openBrowser'])){
		e.preventDefault();
		e.stopPropagation();
		// document.getElementById('datetoch').innerText = 'try';
		var d = communicateWithPay24('openBrowser', el.href);
		return false;
	}
	// document.getElementById('datetoch').innerText = el.href;
	return true;
};
</script>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('end'); ?>