<?php
class TravelFuse_model extends CI_Model {

  public $api;
  private $retrieved_from_cache = false;
  
  function __construct() {
    parent::__construct();
    $this->load->helper("travelfuse");
    $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
  }
  
  public function isRetrievedFromCache(){
	  return $this->retrieved_from_cache;
  }
  public function getApi(){
    if($this->api){
      return $this->api;
    }
    $settings = array(
      // 'endpoint' => 'https://accenttravel-ro.testing001.travelfuse.ro/api.php',
      'endpoint' => 'https://accenttravel-ro.testing001.travelfuse.ro/api-v2',
      // 'endpoint' => 'https://egato.ro/accapi.php',
      'api_key' => 'j9s1WWeDDPJzZiGq2mv9URmFKp9orTqJYGantv11o0sV5Tpc',
    );
    $this->api = new TravelFuse_API($settings['endpoint'],$settings['api_key']);
    return $this->api;
  }
  
  public function request($get = [], $post = [], $cache_time = false, $retrieve_from_cache = true, $return_content = 'array'){
	$this->retrieved_from_cache = true;
	$request_type = $get['type'] ?? '-';
	$request_call = $get['call'] ?? '-';
    $api = $this->getApi();
    $cache_storage_path = 'travelfuse/' . $request_type . '/' . $request_call . '/';
    $cache_hash = $cache_storage_path . md5(json_encode([$get, $post]));
    if($cache_time !== false && $retrieve_from_cache !== false){
      $response = $this->cache->get($cache_hash, $cache_time);
	  // dd($response);
	  if($response && is_file(APPPATH . $response)){
		if(!$return_content){
			return APPPATH . $response;
		}
		$response = json_decode(file_get_contents(APPPATH . $response), $return_content == 'array');
		if(is_array($response) && empty($response['error'])){
			return $response;
		}
	  }
    }
	$this->retrieved_from_cache = false;
	// print_r($get);
	$response = $api->request($get, $post, false);
	// echo 'test';
	 // echo '<pre>';
		// print_r($get);
		// print_r($post);
		// var_dump($response);
		  // echo '</pre>';
	if($response && is_file(APPPATH . $response)){
		if($cache_time !== false){
		  if (!$cache_check = $this->cache->get($cache_storage_path . 'cache_check')){
			clearExpiredCache($cache_storage_path, $this->cache);
			setCacheStorage($cache_storage_path);
			$this->cache->save($cache_storage_path . 'cache_check', 1, $cache_time);
		  }
		  setCacheStorage($cache_storage_path);
		  $this->cache->save($cache_hash, $response, $cache_time);
		}
		if(!$return_content){
			return APPPATH . $response;
		}
		$response = json_decode(file_get_contents(APPPATH . $response), $return_content == 'array');
		if(is_array($response) && empty($response['error'])){
			return $response;
		}
	}
    return false;
  }
  
  public function getProviders($cache_time = 986400, $retrieve_from_cache = true){
	  return $this->request([
		'type' => 'general',
		'call' => 'general-providers',
	  ], [], $cache_time, $retrieve_from_cache);
  }
  public function searchCountries($cache_time = 986400, $retrieve_from_cache = true){
	  return $this->request([
		'type' => 'individual',
		'call' => 'search-countries',
	  ], [], $cache_time, $retrieve_from_cache);
  }
  public function getCountries($cache_time = 986400, $retrieve_from_cache = true){
	  return $this->request([
		'type' => 'geography',
		'call' => 'geography-countries',
	  ], [], $cache_time, $retrieve_from_cache);
  }
  public function getDestinations($cache_time = 986400, $retrieve_from_cache = true){
	  return $this->request([
		'type' => 'geography',
		'call' => 'geography-destinations',
	  ], [], $cache_time, $retrieve_from_cache);
  }
  public function searchCities($post = [], $get = [], $cache_time = 986400, $retrieve_from_cache = true){
	  /* $post:
		[
			'Country' => 126, // Romania
		]
	  */
	  return $this->request(array_replace([
		'type' => 'individual',
		'call' => 'search-cities',
	  ], $get), $post, $cache_time, $retrieve_from_cache);
  }
  public function searchTourCheckIn($post = [], $get = [], $cache_time = 986400, $retrieve_from_cache = true){
	  /* $post:
		[
		]
	  */
	  return $this->request(array_replace([
		'type' => 'tour',
		'call' => 'search-check-in',
	  ], $get), $post, $cache_time, $retrieve_from_cache);
  }
  public function searchCharterCheckIn($post = [], $get = [], $cache_time = 986400, $retrieve_from_cache = true){
	  return $this->request(array_replace([
		'type' => 'charter',
		'call' => 'search-check-in',
	  ], $get), $post, $cache_time, $retrieve_from_cache);
  }
  public function searchCharterCheckOut($post = [], $get = [], $cache_time = 986400, $retrieve_from_cache = true){
	  return $this->request(array_replace([
		'type' => 'charter',
		'call' => 'search-check-out',
	  ], $get), $post, $cache_time, $retrieve_from_cache);
  }
  public function searchTourCities($post = [], $get = [], $cache_time = 986400, $retrieve_from_cache = true){
	  return $this->searchCities($post, array_replace([
		'type' => 'tour',
		'call' => 'search-cities',
	  ], $get), $cache_time, $retrieve_from_cache);
  }
  public function searchCharterCities($post = [], $get = [], $cache_time = 986400, $retrieve_from_cache = true){
	  return $this->searchCities($post, array_replace([
		'type' => 'charter',
		'call' => 'search-cities',
	  ], $get), $cache_time, $retrieve_from_cache);
  }
  public function searchTourDepartureCities($post = [], $get = [], $cache_time = 986400, $retrieve_from_cache = true){
	  return $this->searchTourCities($post, array_replace([
		'call' => 'search-departure-cities',
	  ], $get), $cache_time, $retrieve_from_cache);
  }
  public function searchCharterDepartureCities($post = [], $get = [], $cache_time = 986400, $retrieve_from_cache = true){
	  return $this->searchCharterCities($post, array_replace([
		'call' => 'search-departure-cities',
	  ], $get), $cache_time, $retrieve_from_cache);
  }
  public function individualOfferList($post = [], $get = [], $cache_time = 986400, $retrieve_from_cache = true, $return_content = true){
	  /* $post:
		{
			"CityCode": "42200",
			"DestinationType": "city",
			"CheckIn": "2024-08-14",
			"CheckOut": "2024-08-22",
			"Adults": "1",
			"Children": 2,
			"ChildrenAge": [
				"1",
				"2"
			]
		}
	  */
	  return $this->request(array_replace([
		'type' => 'individual',
		'call' => 'offer-list',
	  ], $get), $post, $cache_time, $retrieve_from_cache, $return_content);
  }
  public function charterOfferDetails($post = [], $get = [], $cache_time = 986400, $retrieve_from_cache = false, $return_content = 'object'){
	  return $this->request(array_replace([
		'type' => 'charter',
		'call' => 'offer-details',
	  ], $get), $post, $cache_time, $retrieve_from_cache, $return_content);
  }
  public function tourOfferDetails($post = [], $get = [], $cache_time = 986400, $retrieve_from_cache = false, $return_content = 'object'){
	  return $this->request(array_replace([
		'type' => 'tour',
		'call' => 'offer-details',
	  ], $get), $post, $cache_time, $retrieve_from_cache, $return_content);
  }
  public function tourOfferList($post = [], $get = [], $cache_time = 986400, $retrieve_from_cache = true, $return_content = 'object'){
	  return $this->individualOfferList($post, array_replace([
		'type' => 'tour',
	  ], $get), $cache_time, $retrieve_from_cache, $return_content);
  }
  public function charterOfferList($post = [], $get = [], $cache_time = 986400, $retrieve_from_cache = true, $return_content = 'object'){
	  $offer_list = $this->individualOfferList($post, array_replace([
		'type' => 'charter',
	  ], $get), $cache_time, $retrieve_from_cache, $return_content);
	  if(false && IS_LISAL_IP){
		  pr($post);
	  // dd($offer_list);
	  spl_autoload_register(require BASEPATH . 'vendor/json-machine-master/src/autoloader.php');
	  switch($return_content){
		case 'object':
		case 'array':
			$hotels_machine = $offer_list;
			break;
		default:
			$hotels_machine = \JsonMachine\Items::fromFile($offer_list, [
				'decoder' => new \JsonMachine\JsonDecoder\ExtJsonDecoder(true),
			]);
			break;
	  }
	  foreach ($hotels_machine as $key => $hotel) {
			$this->db->select("h.Id");
			$this->db->where_in('h.Id', $hotel['Id']);
			$q = $this->db->get('tf_hotels h');
			$row = $q->row();
			if($row) continue;
			$hotel_detail = $this->TravelFuse_model->getHotelsDetails(['HotelIds' => (int)$hotel['Id']], 986400, false, 'array');
			$hotel_detail = array_shift($hotel_detail);
			if(!$hotel_detail){
				continue;
			}
			
			$a = array_diff_key($hotel_detail, array_flip([
				'Id',
				'CountryId',
				'ShortContent',
				'Stars',
				'Name',
				'Latitude',
				'Longitude',
				'Address',
				'MainImage',
				'Content',
				'WebAddress',
				'Facilities',
				'Recommended',
			]));
			if(!empty($a)){
				echo 'Found extra info';
				print_R($a);
				die;
			}
			$country_id = $hotel_detail['Address']['City']['Country']['Id'] ?? 0;
			if(empty($country_id)){
				echo 'Country ID NOT FOUND';
				print_R($country_id);
				// prd($hotel_detail['Address']['City']['Country']);
				die;
			}
			$details = [
				'Id' => $hotel_detail['Id'],
				'CountryId' => $country_id,
				'Info' => $hotel_detail,
				'ShortContent' => $hotel_detail['ShortContent'] ?? NULL,
				'Stars' => $hotel_detail['Stars'] ?? NULL,
				'Name' => $hotel_detail['Name'] ?? NULL,
				'Facilities' => array_values(array_unique(array_filter(array_map(function($v){ return $v['Name'] ?? null; }, $hotel_detail['Facilities'] ?? []), function($v){return !empty($v); }))),
				'Latitude' => ($hotel_detail['Address'] ?? [])['Latitude'] ?? null,
				'Longitude' => ($hotel_detail['Address'] ?? [])['Longitude'] ?? null,
				'MainImage' => ($hotel_detail['MainImage'] ?? [])['ExternalUrl'] ?? NULL,
				'Content' => ($hotel_detail['Content'] ?? [])['Content'] ?? NULL,
				'ImageGallery' => array_values(array_unique(array_filter(array_map(function($v){ return $v['ExternalUrl'] ?? null; }, (($hotel_detail['Content'] ?? [])['ImageGallery'] ?? [])['Items'] ?? []), function($v) use ($hotel_detail){return !empty($v) && $v != ($hotel_detail['MainImage'] ?? null); }))),
			];
			
			// prd($details);
			$table_name_import = 'tf_hotels';
			$column_names = array_keys($details);
			$results = [$details];
			$sql = "INSERT INTO `{$table_name_import}` (" . implode(',', array_map(function($column_name){ return "`{$column_name}`"; }, $column_names)) . ") VALUES (" . implode("),(", array_map( function($item) use ($column_names){
				return implode(',', array_map(function($column_name) use ($item){
					return $this->db->escape(
						(isset($item[$column_name]) && (is_array($item[$column_name]) || is_object($item[$column_name]))) 
						? (empty($item[$column_name]) ? NULL : json_encode($item[$column_name], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) 
						: ($item[$column_name] ?? NULL)
					); }, $column_names));
			}, $results)) . ")";
			
			prd($sql);
			// $this->query($sql);
			
			
			if($return_content == 'object'){
				$hotel = json_decode(json_encode($hotel), true);
			}
			prd($hotel);
			$details = $this->TravelFuse_model->getHotelsDetails(['HotelIds' => (int)$hotel['Id']]);
			prd($details);
			
			$s_data = [
				'Transport' => $post['Transport'],
				'Destination' => $post['Destination'],
				'DestinationType' => $post['DestinationType'],
				'DepCityCode' => $post['DepCityCode'],
				'CheckIn' => $post['CheckIn'],
				'CheckOut' => $post['CheckOut'],
				'Adults' => $post['Adults'],
				'Children' => $post['Children'] ?? null,
				'ChildrenAge' => $post['ChildrenAge'] ?? null,
				'Provider' => $post['Provider'] ?? null,
				'ProductCode' => $hotel['Id'],
				// 'OfferId' => $this->input->post('OfferId', null),
			];
			// dd();
			$offer_details = $this->TravelFuse_model->charterOfferDetails($s_data);
			dd($offer_details);
		  unset($hotel['Offers']);
		  pr($hotel);
	  }
	  die;
	  }
	  
	  return $offer_list;
  }
  public function individualOfferDetails($post = [], $cache_time = 986400, $retrieve_from_cache = true){
	  /* $post:
		{
			"ProductCode": "42200",
			"CityCode": "42200",
			"DestinationType": "city",
			"CheckIn": "2024-08-14",
			"CheckOut": "2024-08-22",
			"Adults": "1",
			"Children": 2,
			"ChildrenAge": [
				"1",
				"2"
			]
		}
	  */
	  return $this->request([
		'type' => 'individual',
		'call' => 'offer-details',
	  ], $post, $cache_time, $retrieve_from_cache);
  }
  public function getCities($post = [], $cache_time = 986400, $retrieve_from_cache = true){
	  /* $post:
		[
			'CountryId' => 126, // Romania
		]
	  */
	  return $this->request([
		'type' => 'geography',
		'call' => 'geography-cities',
	  ], $post, $cache_time, $retrieve_from_cache);
  }
  public function getHotels($post = [], $cache_time = 986400, $retrieve_from_cache = true){
	  /* $post:
		[
			'CityCode' => 193, 
			'DestinationType' => 'county'
		]
	  */
	  return $this->request([
		'type' => 'geography',
		'call' => 'geography-hotels',
	  ], $post, $cache_time, $retrieve_from_cache);
  }
  public function getHotelsDetails($post = [], $cache_time = 986400, $retrieve_from_cache = true, $return_content = 'object'){
	  /* $post:
		[
			'HotelIds' => '81255,81276'
		]
	  */
	  return $this->request([
		'type' => 'geography',
		'call' => 'geography-hotels-details',
	  ], $post, $cache_time, $retrieve_from_cache, $return_content);
  }
	public function parseFacilitiesFromTitles($titles){
		$facilities = [];
		foreach($titles as $title){
			$facilities = array_replace_recursive($facilities, array_map(function($facilities){
				$facs = [];
				foreach($facilities as $facility){
					$facs[strtolower($facility)] = $facility;
				}
				return $facs;
			}, $this->parseFacilitiesFromTitle($title)));
		}
		$facilities = array_map('array_values', $facilities);
		// return $facilities;
		
		$ordering = [
			'room',
			'type',
			'quality',
			'meal',
			'beds',
			'layout',
			'facility',
			'position',
			'size',
			'price',
			'view',
			'merch',
			'other',
			'availability',
			'merch',
			'restrict',
			'transfer',
			'transport',
			'tax',
		];
		uksort($facilities, function($a, $b) use ($ordering){
			$as = array_search(strtolower($a), $ordering);
			$bs = array_search(strtolower($b), $ordering);
			return $as - $bs;
		});
		// dd($facilities);
		return $facilities;
	}
	public function parseFacilitiesFromTitle($title){
		static $parsed_facilities = [];
		if(isset($parsed_facilities[$title])){
			return $parsed_facilities[$title];
		}
		$rest_title = $title;
		$rest_title = trim(preg_replace('~^(.{8,})?[\s*-]*\1$~i', '\1', trim($rest_title)));
		$facilities = [];
		$querries = [
			["[,\.]$"],
			["^[,\.]"],
			["\".*?\""],
			["no *[^ ]+?allowed"],
			["\".*?\""],
			["with free access to the hotels\'", 'with '],
			["\(?complimentary([ ]+[^ ]*)[ ]+transfer.*?(\)|$)", 'transfer'],
			["\b(premium dinner)\b"],
			["\b(main[ -]*bui?lding)\b"],
			["\b((flexible|standard|package|ta) rate|low cost)\b"],
			["([0-9[:punct:]]\+?)+(ad?)?$"],
			["\([^a-z]*\)"],
			["\*+(SPECIAL *OFFER|NEW)\*+"],
			["\(?(\bfor )?(up[ -]*to *)?[0-9\/\-]+ *(pax|persons?|ad?u?l?t?s?)([ -]*[1-9]ad?u?l?t?s?)?\)?([ -]*\+[ -]*[0-9]+( child(ren)?)?)?\)?"],
			
			// CLEANUP
			["\([^a-z]*\)"],
			["  +"],
		];
		foreach($querries as $query){
			$rest_title = trim(preg_replace('~' . $query[0] . '~i', $query[1] ?? ' ', $rest_title));
		}
		
		$numbers = "(un|doua|trei|patru|cinci|sase|sapte|opt|noua|zece|one|two|three|four|five|six|seven|eight|nine|ten|[0-9]+)";
		$views = "(mountain|sea|ocean|pool|interior|panoram(a|ic)|garden|harbor|resort|island|port|city|creek|inland|golf|(down)?town|marina|lagoon|skyline|land(scape)?|sunset|(bus*ines+ *)?lounge|street|bay|urban|atrium|canal|sew|lake|country|courtyard|river|village|beach|((aqua|water) *)?park|castle|fountain|waterfront|forest)";
		$querries = [
			["\b((or|and|\.|\/ \-)*((non?|w\/o|without|fara)( *(\b(or|and)\b)*(pr(iv(a(das?|t[ae]?))?)?\b\.?|own|v[ie]*ws? *of( the)?)?( *(ind|outd)\.?(oors?)? *)?((sau|si|or|and|\.|\/ )*((smoking|windows?)|((a\/?c|air *condition+(er|ing)?|ter+a(sa|ce|z+as?)|balcony|kitchen(et+e)?|bucatarie|balcon|pool?)([\s]*v[ie]*ws?)?)))+)))+\b",'restrict'],
			["\b(with *)?(free *)?(airport *)?transfer( *(inclus|aeroport|hotel))*",'transfer'],
			["\b(cancel ?free|free *cancellation)\b",'facility'],
			["\b( *((with|free|w\/|\() *)?(ac+es+ *to *)?(shar(ed|ing) *)?(((with|w\/|or|and)|\/) *)?((pr(iv(a(das?|t[ae]?))?)?\b\.?|own|excl(us+iv[ae])?)\.?)?( *(ind|outd)\.?(oors?)? *)?( *(a\/?c|air *condition+(er|ing)?|bath *rooms?|gar[dt]ens?|kitchen(ette)?s?|((heated) *)?((plunge|swim+ing|whirl) *)?pools?|ter+a(sa|ce|z+as?)|wi *fi|jac+uz+[yi]|beach|balc.?ony|smoking|jet+ed tub|hydro *mas+age( *bath)?( *tub)?|courtyard|rooftop|patio|butler|(walk *in *)?shower|(hot ?)?tub)( *areas?)?)( *ac+es+)?(?! *(v[ie]*ws?)))( *\))?\b",'facility'],
			["$views *front",'position'],
			["\b( *((with|w\/|\(|\/) *)?(([,\s]| *(and|or|and|\/|&) *)?(side|full|partial|reduced|fronta?l?|back|lateral|direct|panoramic|panaromic|wall)? *(v[ie]*ws? *of( the)?)? *$views( *side)?)+ *(v[ie]*ws?))+",'view'],
			["$views *side( *room)?",'position'],
			["\b(cu *)?(vedere *(laterala|partiala)?(( |(si|sau)|la )*(mare|piscina|munte|oras|panoramica|parc|marina|laguna|gradina|plaja|golf|lac|jac+uz+[yi]|))+)\b",'view'],
			["\b((((cu|si|sau|\()| ) *)?(ac+es+ *la *)?(( *(baie?|pis.?cin[ae]|fumatori|jac+uz+[yi]|balcon|terasa))+)( *(independ[ae]nt|(pr(iv(a(das?|t[ae]?))?)?\b\.?|propri[uei])|(interioa?r[ae]?|exterioa?r[ae]?)) *)*( *\))?)\b",'facility'],
			["\b(((on|\()) *)?((pr(iv(a(das?|t[ae]?))?)?\b\.?|own|excl(us+iv[ae])?)\.?)?( *(peninsula)+)( *\)?)\b",'facility'],
			["(with|w\/) *views",'view'],
			["\bpoolzugang\b",'facility'], // acces la piscina
			["\b[^\s]+ *zimmer\b",'type'], // camera de tipul (family, dubla) ... in germana
			["\b(seitl(\.|icher)? *)?[^\s]+ *blick\b",'view'], // vedere la ... in germana
			["\bvista *(mare?)( *lateral|frontal)?\b",'view'], // vedere la ... in spaniola sau ceva
			["\b(cu *)?(vedere *(laterala|partiala)?(( |(si|sau)|la )*(mare|piscina|munte|oras|panoramica|parc|marina|laguna|gradina|plaja|golf|lac))+)\b",'view'],
			["\b((Camer[ae]|Habitación) *)?(penthouse|junior|senior|accesible|económica|economy|budget|elite|premiere?|prestige|luxury|std\.?|dbl?\.?|basic|standar[dt]|de *luxe|superioa?r[ae]?|premium|priviledge|platinum|executive|classic|elegance|comfort)(?!( *v[ie]*ws?))([\s]*Rooms?)?\b",'type', 1],
			["\b((wel+nes+) *)?((small|large) *)?(prime|romantic|privilege|exclus+ive|veranda|country *style|dream|love|relax|island|ter+a(sa|ce|z+as?)|panoramic|select)([\s]*Rooms?)\b",'type'],
			["\b(fara *masa|no *meals?)\b", 'meal'],
			["\b((Mese|masa) *conform *program)\b", 'meal'],
			["\b(r[or]|[bfh]b|(prem|u(ltra)?)? *a(i|(l(l[xd]?)?( *in(clus*ive?)?)))|(bed( *(and|&) *)?)?breaks?fast|bautur[ai]|beverages?|buf+et|drink|dinner|din[ae] *around|brunch|cina|(mic *)?dejun|(half|full) *board|((premium|ultra) *)?all *inclusive|demipensiune|pensiune *completa|room (only|rates?))( *(plus|\+))?( *included( *in *the *price)?)?\b", 'meal'],
			["\b(Camera *)?(($numbers\s*)?(inter *)?con+ect(ing|ed)|guests?|part(y|ies)|family|familiar|kings?|spacious|queens?|dubl[ae]|tripl[ae]|cvadrupl[ae]|quads?|grands?|dou?bles?|singles?|quadruples?|quintuples?|roh|triples?|twin( *bed(ded)?)?s?|quatro|rooms?)([\s]*(Rooms?|Camer[ae]))?\b",'type'],
			["\b((wel+nes+) *)?(((pr(iv(a(das?|t[ae]?))?)?\b\.?|own|excl(us+iv[ae])?)\.?) *)?(residences?|vil+as?|lofts?|bunga?lows?|cabana|aparta?mento?[se]?|(love|couple|master|supreme|presidential|royal|mini|designer|jr\.?|junior|sr\.?|senior|family|master|excecutive|exe\.?(cutive)?)?suit[ae]s?|e?studios?|duplex)([\s]*(priva[dt][ae]|exclusiv[ae]))?\b",'type'],
			["\b(((with|w\/|and|or|cu|and|or|si|sau| ) *)?($numbers?\s*(((extra|bunk) *)?bed(s|ded)?|pat(uri)?( ?suprapuse?)|sofas?|canape(a|ele))))\b",'beds'],
			["\b(((with|w\/|and|or|cu|and|or|si|sau| ) *)?($numbers?\s*(living|bed *ro*m?s?\.?|dormitoa?re?|dormitorios?)))\b",'layout'],
			["\b($numbers?\s*(bed(s|ded)?))\b",'layout'],
			["((ground|[0-9]*(1(st)?|2(nd)?|3(rd)?|(4-9|0)(th)?)|upper|lower|top) *floor)", 'position'],
			["(front|back|middle) *yard", 'position'],
			["(la *)?(mansarda|parter)", 'position'],
			["(((pr(iv(a(das?|t[ae]?))?)?\b\.?|own|excl(us+iv[ae])?)\.?) *)?$views *access",'facility'],
			[".*?tax.*?aerop.*",'tax'],
		];
		
		foreach($querries as $query){
			$query[0] = str_replace(' ', '[ \-\+,]', $query[0]);
			$facs = null;
			$rest_title = trim(preg_replace_callback('~' . $query[0] . '~i', function($matches) use (&$facs, &$query){
				switch($query[1]){
					case 'tax':
						if(true){
							$facs['Tax']['taxe aeroport'] = 'Taxe aeroport';
							return ' ';
						}
					break;
					case 'transfer':
						if(true){
							$facs['Transfer']['transfer'] = 'Transfer inclus';
							return ' ';
						}
					break;
				}
				$formatted = trim($matches[0]);
				$formatted = trim(preg_replace('~^((\b(and|or|with|w|cu|si|sau)\b|[\(\)[:punct:]])\s*)+~i', '', trim($formatted)));
				$formatted = trim(preg_replace('~((\b(and|or|with|w|cu|si|sau)\b|[\(\)[:punct:]])\s*)+$~i', '', trim($formatted)));
				$formatted = trim(preg_replace('~[\s\(\)]+~', ' ', trim($formatted)));
				$formatted = trim(preg_replace('~[\s\(\)[:punct:]]+$~', '', trim($formatted)));
				$formatted = trim(preg_replace('~\b(pool)\s*~i', ' pool ', trim($formatted)));
				$formatted = trim(preg_replace('~\s*(pool)\b~i', ' pool ', trim($formatted)));
				$formatted = trim(preg_replace('~(\b.{3,}\b)[ \-]+\1~i', '\1', trim($formatted)));
				// $formatted = trim(preg_replace('~(views?|zugang|blick|zimmer)$~i', '', trim($formatted)));
				// $formatted = trim(preg_replace('~(\b(vedere( partiala)?( la)?|front(al)?|lateral|side|(out|in)doors?)|(side\b))~i', '', trim($formatted)));
				switch($query[1]){
					case 'facility':
					$formatted = preg_replace('~\bair *condition+(er|ing)?\b~i', 'A/C', $formatted);
					$formatted = preg_replace('~jac+uz+[yi]~i', 'Jacuzzi', $formatted);
					$formatted = preg_replace('~^on\b~i', ' ', $formatted);
					$formatted = preg_replace('~(WALK[ \-]*IN)[ \-]*~i', 'Walk-in ', $formatted);
					$formatted = preg_replace('~(acces la|access to) (.*)~i', '\2 access', $formatted);
					$formatted = preg_replace('~\b(acces)\b~i', 'access', $formatted);
					$formatted = preg_replace('~(zugang)\b~i', ' access', $formatted);
					$formatted = preg_replace('~\bpisi?cin[ae]~i', ' pool ', $formatted);
					$formatted = preg_replace('~\bter+a(sa|ce|z+as?)\b~i', ' terrace ', $formatted);
					$formatted = preg_replace('~\bBALC0?ONY?\b~i', ' balcony ', $formatted);
					$formatted = preg_replace('~\b(.*?)((pr(iv(a(das?|t[ae]?))?)?\b\.?|own|excl(us+iv[ae])?)\.?)\b[\s\-]*~i', ' \1', $formatted);
					$formatted = preg_replace('~\b(ind|outd)\.?(oors?)?\b\s*~i', ' ', $formatted);
					
					$formatted = preg_replace_callback('~\b(ind|outd)\.?(oors?)?\b\s*~i', function($matches) use (&$facs){
						return ucfirst(strtolower($matches[1] . 'oor')). ' ';
					}, $formatted);
					
					$formatted = preg_replace('~\b(free[ \-]*)?(wi[ \-]*fi)\b~i', 'WiFi', $formatted);
					break;
					case 'view':
					$formatted = preg_replace_callback('~(.*?)blick$~i', function($matches){
						$current = $matches[1];
						$current = preg_replace('~\b(meer)\b~i', 'Sea', $current);
						$current = preg_replace('~\b(berg)\b~i', 'Mountain', $current);
						$current = preg_replace('~\b(stadt)\b~i', 'City', $current);
						$current = preg_replace('~\b(aus)\b~i', 'Panoramic', $current);
						$current = preg_replace('~\b(strassen)\b~i', 'Street', $current);
						return $current . ' View';
					}, $formatted);
					$formatted = preg_replace_callback('~vista\s*(.*)~i', function($matches){
						$current = $matches[1];
						$current = preg_replace('~\b(mare?)\b~i', 'Sea', $current);
						return $current . ' View';
					}, $formatted);
					$formatted = preg_replace('~\s*side\b~i', ' ', $formatted);
					$formatted = preg_replace('~\bside\s*~i', ' ', $formatted);
					$formatted = preg_replace('~(Seitl(icher)?|laterala?|partiala?|full|partial|reduced|fronta?l?|lateral|direct)~i', ' ', $formatted);
					$formatted = preg_replace('~\b(Sew)\b~i', 'Sea', $formatted);
					$formatted = preg_replace('~\s+(and|si)\s+~i', ' & ', $formatted);
					$formatted = preg_replace('~\s*v[ie]*ws?\b~i', ' View', $formatted);
					$formatted = preg_replace('~[\-]~i', ' ', $formatted);
					$formatted = preg_replace_callback('~(.*?)\s*((?:\b(?:views?|or|and|si|sau)\b|(?:,|&|\/|\+)))+\s*(.*)~i', function($matches) use (&$facs){
						$current = $matches[1];
						$middle = $matches[2];
						if(preg_match('~^views?~i', $middle, $m)){
							$current .= ' ' . $m[0];
						}
						$formatted = $matches[3];
						$formatted = trim(preg_replace('~\s*(\.)\s*~', ' ', trim($formatted)));
						$formatted = trim(preg_replace('~\s*([\-])\s*~', '\1', trim($formatted)));
						$formatted = trim(preg_replace('~\s+~', ' ', trim($formatted)));
						$formatted = trim(preg_replace('~^((\b(and|or|with|w|cu|si|sau)\b|[\(\)[:punct:]])\s*)+~i', '', trim($formatted)));
						if($formatted){
							if(preg_match('~views?$~i', $formatted) && !preg_match('~views?$~i', $current)){
								$current .= " View";
							}
							if(preg_match('~^vedere ~i', $current)){
								if(!preg_match('~^vedere ~i', $formatted)){
									if(!preg_match('~^la ~i', $formatted)){
										$formatted = " la " . trim($formatted);
									}
									$formatted = "Vedere " . trim($formatted);
								}
								$formatted = preg_replace_callback('~^vedere(?: la)?\b\s*(.*)~i', function($matches){
									$current = $matches[1];
									$current = preg_replace('~\b(mare?)\b~i', 'Sea', $current);
									$current = preg_replace('~\b(piscina)\b~i', 'Pool', $current);
									$current = preg_replace('~\b(gradina)\b~i', 'Garden', $current);
									$current = preg_replace('~\b(lac)\b~i', 'Lake', $current);
									$current = preg_replace('~\b(munte)\b~i', 'Mountain', $current);
									$current = preg_replace('~\b(plaja)\b~i', 'Beach', $current);
									$current = preg_replace('~\b(panoramica)\b~i', 'Panoramic', $current);
									$current = preg_replace('~\b(oras)\b~i', 'City', $current);
									return $current . ' View';
								}, $formatted);
							}
							$facs['View'][strtolower($formatted)] = $formatted;
						}
						return trim($current);
					}, $formatted);
						$formatted = preg_replace_callback('~^vedere(?: la)?\b\s*(.*)~i', function($matches){
							$current = $matches[1];
							$current = preg_replace('~\b(mare)\b~i', 'Sea', $current);
							$current = preg_replace('~\b(piscina)\b~i', 'Pool', $current);
							$current = preg_replace('~\b(gradina)\b~i', 'Garden', $current);
							$current = preg_replace('~\b(lac)\b~i', 'Lake', $current);
							$current = preg_replace('~\b(munte)\b~i', 'Mountain', $current);
							$current = preg_replace('~\b(plaja)\b~i', 'Beach', $current);
							$current = preg_replace('~\b(panoramica)\b~i', 'Panoramic', $current);
							$current = preg_replace('~\b(oras)\b~i', 'City', $current);
							return $current . ' View';
						}, $formatted);
					
					break;
					case 'position':
					$formatted = preg_replace('~[\-]~i', ' ', $formatted);
					break;
					case 'layout':
					$formatted = preg_replace('~(Dormitorio|BEDRM?S?\b)~i', 'Bedroom', $formatted);
					$formatted = preg_replace('~(dormitoare\b)~i', 'Bedrooms', $formatted);
					$formatted = preg_replace('~(dormitor\b)~i', 'Bedroom', $formatted);
					break;
					case 'beds':
					$formatted = preg_replace('~(bedded)~i', 'beds', $formatted);
					$formatted = preg_replace('~(BUNK[\s\-]*BEDS)~i', 'Bunkbeds', $formatted);
					$formatted = preg_replace('~(BUNK[\s\-]*BED)~i', 'Bunkbed', $formatted);
					$formatted = preg_replace('~(EXTRA[\s\-]*BEDS)~i', 'Extra beds', $formatted);
					$formatted = preg_replace('~(EXTRA[\s\-]*BED)~i', 'Extra bed', $formatted);
					break;
					case 'size':
					case 'quality':
					case 'room':
					case 'type':
					
					$formatted = preg_replace('~\bVIL+AS?\b\s*~i', ' Villa ', $formatted);
					$formatted = preg_replace_callback('~\b(Bunga?low)s?\b~i', function($matches) use (&$facs){
						return 'Bungalow';
					}, $formatted);
					$formatted = preg_replace_callback('~\be?(Studio)s?\b~i', function($matches) use (&$facs){
						return 'Studio';
					}, $formatted);
					$formatted = preg_replace_callback('~((pr(iv(a(das?|t[ae]?))?)?\b\.?|own|excl(us+iv[ae])?)\.?)~i', function() use (&$facs){
						$facs['Type']['private'] = 'Private';
						return ' ';
					}, $formatted);
					$formatted = preg_replace_callback('~(wel+nes+)~i', function() use (&$facs){
						$facs['Type']['wellness'] = 'Wellness';
						return ' ';
					}, $formatted);
					$formatted = preg_replace_callback('~aparta?mento?[se]?~i', function() use (&$facs){
						$facs['Type']['apartment'] = 'Apartment';
						return ' ';
					}, $formatted);
					$formatted = preg_replace_callback('~Suit[ae]s?~i', function() use (&$facs){
						$facs['Type']['suite'] = 'Suite';
						return ' ';
					}, $formatted);
					
					$formatted = preg_replace('~zimmer$~i', '', $formatted);
					$formatted = preg_replace_callback('~rooms?$~i', function() use (&$facs){
						$facs['Type']['room'] = 'Room';
						return ' ';
					}, $formatted);
					$formatted = preg_replace('~\b(Familiar|Familien)\b~i', 'Family', $formatted);
					$formatted = preg_replace_callback('~^(camer[ea]|Habitación)\b~i', function() use (&$facs){
						$facs['Type']['room'] = 'Room';
						return ' ';
					}, $formatted);
					$formatted = preg_replace('~^\b(Doppel|Dubla|dou?bles?|dbl?\.?)\b~i', 'Double', $formatted);
					$formatted = preg_replace('~^\b(std\.?)\b~i', 'Standard', $formatted);
					$formatted = preg_replace('~^\b(Económica)\b~i', 'Economy', $formatted);
					$formatted = preg_replace('~^\b(Superioara)\b~i', 'Superior', $formatted);
					$formatted = preg_replace('~^\b(STANDART)\b~i', 'Standard', $formatted);
					$formatted = preg_replace('~^\b(Cabana)\b~i', 'Cabin', $formatted);
					$formatted = preg_replace('~^\bde[ \-]*luxe\b~i', 'Deluxe', $formatted);
					$formatted = preg_replace('~\b(Zweibett|twins?\s*(beds?)?)~i', 'Twin ', $formatted);
					$formatted = preg_replace('~^\b(Cvadrupla|QUAD)\b~i', 'Quadruple ', $formatted);
					$formatted = preg_replace('~^\b(Dreibett|Tripla)\b~i', 'Triple ', $formatted);
					break;
					case 'restrict':
					// $formatted = preg_replace('~\bmasa\b~i', ' meal ', $formatted);
					$formatted = preg_replace('~\bBALC0?ONY?\b~i', ' balcony ', $formatted);
					$formatted = preg_replace('~^(non?|w\/o|without|fara)[ \-]*~i', 'No ', $formatted);
					break;
					case 'meal':
					$formatted = preg_replace('~^(BB|Bed.?(&|and)?.?Breaks?fast)~i', 'Breakfast', $formatted);
					$formatted = preg_replace('~\s+included.*~i', '', $formatted);
					$formatted = preg_replace('~fara masa~i', 'no meal', $formatted);
					$formatted = preg_replace('~\bMic dejun~i', 'Breakfast', $formatted);
					$formatted = preg_replace_callback('~\bUAI~i', function() use (&$facs){
						$facs['Meal']['all inclusive'] = 'All inclusive';
						return 'Ultra';
					}, $formatted);
					$formatted = preg_replace_callback('~\bUltra~i', function() use (&$facs){
						$facs['Meal']['ultra'] = 'Ultra';
						return ' ';
					}, $formatted);
					$formatted = preg_replace('~\b(ai|all[ \-]*in(clus*ive)?)\b~i', 'All Inclusive', $formatted);
					$formatted = preg_replace('~\b(Demi[ \-]*pensiune|DP|HB|Half[ \-]*board)~i', 'Halfboard ', $formatted);
					$formatted = preg_replace('~\b(Pensiune[ \-]*Completa|PC|FB|Full[ \-]*board)~i', 'Fullboard ', $formatted);
					$formatted = preg_replace('~\b(RO\b|Room[ \-]*only)~i', 'Room only ', $formatted);
					$formatted = preg_replace('~\b(Bautur[ia]|Drinks?|beverages?)~i', 'Beverages ', $formatted);
					$formatted = preg_replace('~\b(RR\b|Room[ \-]*rates?)~i', 'Room rate ', $formatted);
					$formatted = preg_replace('~\b(Cina)\b~i', 'Dinner ', $formatted);
					$formatted = preg_replace('~\bdin.[ \-]around~i', 'Dine around ', $formatted);
					$formatted = preg_replace_callback('~\bplus\b~i', function() use (&$facs){
						$facs['Meal']['plus'] = 'Plus';
						return ' ';
					}, $formatted);
					break;
				}
				switch($query[1]){
					case 'type':
					case 'size':
					case 'layout':
					case 'beds':
					
					$formatted = preg_replace('~.*?(Inter)?con+ect(ing|ed)\s*~i', 'Connected ', $formatted);
					
					$formatted = preg_replace('~^three\s*~i', '3 ', $formatted);
					$formatted = preg_replace('~^seven\s*~i', '7 ', $formatted);
					$formatted = preg_replace('~^eight\s*~i', '8 ', $formatted);
					$formatted = preg_replace('~^four\s*~i', '4 ', $formatted);
					$formatted = preg_replace('~^five\s*~i', '5 ', $formatted);
					$formatted = preg_replace('~^nine\s*~i', '9 ', $formatted);
					$formatted = preg_replace('~^one\s*~i', '1 ', $formatted);
					$formatted = preg_replace('~^two\s*~i', '2 ', $formatted);
					$formatted = preg_replace('~^six\s*~i', '6 ', $formatted);
					
					$formatted = preg_replace('~^un ~i', '1 ', $formatted);
					$formatted = preg_replace('~^doua\s*~i', '2 ', $formatted);
					$formatted = preg_replace('~^trei\s*~i', '3 ', $formatted);
					$formatted = preg_replace('~^patru\s*~i', '4 ', $formatted);
					$formatted = preg_replace('~^cinci\s*~i', '5 ', $formatted);
					$formatted = preg_replace('~^sase\s*~i', '6 ', $formatted);
					$formatted = preg_replace('~^sapte\s*~i', '7 ', $formatted);
					$formatted = preg_replace('~^opt ~i', '8 ', $formatted);
					$formatted = preg_replace('~^noua\s*~i', '9 ', $formatted);
					$formatted = preg_replace('~^zece\s*~i', '10 ', $formatted);
					
					
					$formatted = preg_replace('~^(\d+)\s*~i', '\1 ', $formatted);
					$formatted = preg_replace('~^(([2-9]|[1-9][0-9]+) )?beds?\b~i', 'Bed', $formatted);
					$formatted = preg_replace('~^(([2-9]|[1-9][0-9]+) )?sofas?\b~i', 'Sofa', $formatted);
					$formatted = preg_replace('~(Paturi suprapuse)~i', 'Bunkbeds', $formatted);
					$formatted = preg_replace('~^(([2-9]|[1-9][0-9]+) )?bunkbeds?~i', 'Bunkbed', $formatted);
					$formatted = preg_replace('~^(([2-9]|[1-9][0-9]+) )?bedrooms?~i', 'Bedroom', $formatted);
					$formatted = preg_replace('~^1\s+~i', '', $formatted);
					break;
				}
				$formatted = trim(preg_replace('~\s*(\.)\s*~', ' ', trim($formatted)));
				$formatted = trim(preg_replace('~\s*([\-])\s*~', '\1', trim($formatted)));
				$formatted = trim(preg_replace('~\s+~', ' ', trim($formatted)));
				$formatted = ucfirst($formatted);
				$hash = strtolower($formatted);
				if($formatted){
					$facs[ucfirst($query[1])][$hash] = $formatted;
				}
				return ' ';
			}, $rest_title));
			if($facs){
				$facilities = array_replace_recursive($facilities ?? [], $facs);
			}
		}
		// if(preg_match('/fara masa/', $title)){
			// dd($facilities);
		// }
		return $parsed_facilities[$title] = $facilities;
	}
  public function parseHotelOfferFacilities(&$hotel){
	  $cached_facilities = $this->TravelFuseFacilities_model->getCachedFacilities();
	  if(empty($hotel->Offers)) return;
	  foreach($hotel->Offers as &$offer){
		  if(empty($offer->Items)) continue;
		  $titles = [];
		  $force_facilities = [];
		  foreach($offer->Items as &$item){
			  if(empty($item->Merch)) continue;
			  if(empty($item->Merch->Title)) continue;
			  if(empty($item->Merch->type)) continue;
			  if(!empty($item->Availability) && 'no' == $item->Availability) continue;
			  if('Transport' == $item->Merch->type){
				  $force_facilities['Transport'][] = $item->Merch->TransportType;
				  continue;
			  }
			  $titles[] = $item->Merch->Title;
			  // dd($item);
		  }
		  if(!empty($offer->Facilities)){
			  $offer->facilities->Other = $offer->Facilities;
		  }
		  $offer_facilities = $this->parseFacilitiesFromTitles($titles);
		  $offer_facilities = array_replace($offer_facilities, $force_facilities);
		  $offer->facilities = [];
		  foreach($offer_facilities as $type => $facilities){
			  $goodfacilities = [];
			  foreach($facilities as $facility){
				  $goodfacilities[strtolower($facility)] = $facility;
			  }
			  $ltype = strtolower($type);
			  foreach($goodfacilities as $lfac => $fac){
				  if(isset($cached_facilities[$ltype][$lfac])){
					  if(empty($cached_facilities[$ltype][$lfac])){
						  unset($goodfacilities[$lfac]);
						  continue;
					  }
					$goodfacilities[$lfac] = $cached_facilities[$ltype][$lfac];
				  }
			  }
			  $offer->facilities[$type] = array_values($goodfacilities);
		  }
	  }
  }
  public function parseHotelsOfferFacilities(&$hotels){
	  foreach($hotels as &$hotel){
		  $this->parseHotelOfferFacilities($hotel);
	  }
  }
  public function cancelBooking($type = 'tour', $travelfuse_order_id, $reason = ''){
	  $data = [
		'OrderId' => $travelfuse_order_id,
		'Reason' => $reason,
	  ];
	  $response = $this->request([
		'type' => $type,
		'call' => 'cancel-booking',
	  ], $data, false, false);
	  
	  return $response;
  }
  public function bookServices($order, $services = null){
	  if(!$services){
			if(isset($order->services)){
				$services = unserialize($order->services);
			}
	  }
	  if(empty($services)){
        $this->outputError('Niciun serviciu adaugat in comanda.');
      }
	  $data = [];
	  $type = '';
	  foreach($services as $service_key => $service){
		  if($data){
			  throw new Exception('TravelFuse does not support multiple services');
		  }
		  if(empty($service['BookingData'])){
			  throw new Exception('BookingData corrupt');
			  continue;
		  }
		  $data = $service['BookingData'];
		  if(empty($service['offer']['SearchId'] ?? '')){
			  throw new Exception('No SearchId!');
			  continue;
		  }
		  if(empty($service['offer']['Code'] ?? '')){
			  throw new Exception('No Code!');
			  continue;
		  }
		  $type = $service['result']['type'] ?? '';
		  $data['SearchId'] = $service['offer']['SearchId'] ?? '';
		  $data['Offers'][] = $service['offer']['Code'] ?? '';
	  }
	  if(!in_array($type, ['charter', 'tour'])){
		  throw new Exception($type . ' is not supported');
	  }
	  
	  // throw new Exception('BOOKING BLOCAT FORTAT!');
	  
	  $response = $this->request([
		'type' => $type,
		'call' => 'booking',
	  ], $data, false, false);
	  
	  if($response && !empty($response['Id'])){
		$remote_order_id = $response['Id'];
		$order_data = array();
		$order_data['id'] = $order->id;
		$order_data['trip_order_id'] = $remote_order_id;
		$order_data['calls'] = serialize($response);
		$this->TripOrder_model->saveOrder($order_data);
	  }
	  
	  return $response;
  }
  public function bookOrder($order){
	  return $this->bookServices($order);
	  throw new Exception('in curs de implementare');
	  return false;
  }
}