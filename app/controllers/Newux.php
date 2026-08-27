<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class NewUX extends MX_Controller {
	private function query($sql){
		$db_vars = [
			'save_queries' => false,
			'db_debug' => true,
		];
		foreach($db_vars as $db_var => $db_var_val){
			$db_vars[$db_var] = $this->db->$db_var;
			$this->db->$db_var = $db_var_val;
		}
		$r = $this->db->query($sql);
		foreach($db_vars as $db_var => $db_var_val){
			$this->db->$db_var = $db_var_val;
		}
		return $r;
	}
	public function tesasda() {
		$this->load->model('TravelFuse_model');
		$charter_city = [
			'Transport' => 'plane',
			'CityCode' => '32',
			'DestinationType' => 'destination',
			'Id' => '3302',
		];
		$dates = $this->TravelFuse_model->searchTourCheckIn([
				'Transport' => $charter_city['Transport'],
				'destination' => $charter_city['CityCode'],
				'destinationType' => $charter_city['DestinationType'],
				'departureCity' => $charter_city['Id'],
			],[],false,false);
		dd($dates);
	}
	public function testarehtml() {
		// $_GET['newux'] = 1;
		$this->theme->set_theme('accent');
		$this->theme->set_layout('blank');
		$this->theme->set_sublayout('frontend/blank/index');
		$this->theme->view('404', $this->data, $this);
	}
	public function testing() {
		$date = '2024-04-22';
		$date = new DateTime($date);
		$date->modify('last day of this month');
		$date = $date->format('Y-m-d');
		echo $date;
	}
	public function fixFacilities() {
		$form = 1;
		
		// $this->db->query("SET GLOBAL regexp_time_limit=1024;");
		if($form){
			$sql = "SELECT Name FROM `tf_facilities2`";
			$facilities = $this->query($sql)->result('array');
			$facilities = array_map('array_shift', $facilities);
			// $facilities = array_flip($facilities);
			// array_walk($facilities, function(&$i, $v){ $i = ['' . $v, null]; });
		}
		$this->load->model('TravelFuse_model');
		$parsed = $this->TravelFuse_model->parseFacilitiesFromTitles($facilities);
		foreach($parsed as $type => $facilities){
			$this->db->query("INSERT IGNORE INTO `tf_facilities` (`Name`, `Type`) VALUES (" . implode('),(', array_map(function($name) use ($type){
				return $this->db->escape($name) . "," . $this->db->escape(strtolower($type));
			}, $facilities)) . ")");
		}
		dd($parsed);
		/* 
		
		if(!$form){
			$this->db->query("UPDATE `tf_facilities2` set facilities=NULL");
		}
		// Pre-cleanup - remove unimportant stuff 
		if(!$form){
			$querries = [
				"name_rest = TRIM(Name)",
				"name_rest = SUBSTRING(name_rest, 1, CHAR_LENGTH(name_rest)/2 -2) WHERE name_rest = CONCAT_WS(' - ', SUBSTRING(name_rest, 1, CHAR_LENGTH(name_rest)/2 -2), SUBSTRING(name_rest, 1, CHAR_LENGTH(name_rest)/2 -2))",
			];
			foreach($querries as $query){
				$this->db->query("UPDATE `tf_facilities2` set " . $query);
			}
		} else if($form){
			foreach($facilities as $Name => $facility){
				$facilities[$Name][0] = trim(preg_replace('~^(.+)?[\s*-]*\1$~i', '\1', trim($facility[0])));
			}
		}
		// Pre-cleanup - remove unimportant stuff 
		$querries = [
			["[,\.]$"],
			["^[,\.]"],
			["\".*?\""],
			["no *[^ ]+?allowed"],
			["\".*?\""],
			["with free access to the hotels\'", 'with '],
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
			if(!$form){
				$this->db->query("UPDATE `tf_facilities2` set name_rest = TRIM(REGEXP_REPLACE(name_rest, " . $this->db->escape($query[0]) . ", " . $this->db->escape($query[1] ?? ' ') . "))");
			}
			if($form){
				foreach($facilities as $Name => $facility){
					$facilities[$Name][0] = trim(preg_replace('~' . $query[0] . '~i', $query[1] ?? ' ', $facility[0]));
				}
				// $this->db->query("UPDATE `tf_facilities2` set " . $query);
			}
		}
		// Set facilities
		$numbers = "(un|doua|trei|patru|cinci|sase|sapte|one|two|three|four|five|six|seven|[0-9]+)";
		$views = "(mountain|sea|ocean|pool|interior|panoram(a|ic)|garden|harbor|resort|island|port|city|creek|inland|golf|(down)?town|marina|lagoon|skyline|land(scape)?|sunset|(bus*ines+ *)?lounge|street|bay|urban|atrium|canal|sew|lake|country|courtyard|river|village|beach|((aqua|water) *)?park|castle|fountain|waterfront|forest)";
		$querries = [
			["\b((or|and|\.|\/ \-)*((non?|w\/o|without|fara)(( |,|-|\b(or|and)\b)*(pr(iv(a(das?|te?))?)?\.?|own|vi?e?ws? *of( the)?)?( *(ind|outd)\.?(oors?)? *)?((sau|si|or|and|\.|\/ )*(a\/?c|ter+a(ce|z+as?)|smoking|balcony|kitchen(et+e)?|meals?|windows?|bucatarie|balcon|pool?)( *vi?e?ws?)?)+)+))+\b",'restrictions'],
			["\b(cancel ?free|free *cancellation)\b",'facility'],
			["\b( *((with|w\/|\(|\/) *)?(([,\s]| *(and|or|and|\/|&) *)?(side|full|partial|reduced|fronta?l?|back|lateral|direct|panoramic|panaromic|wall)? *(vi?e?ws? *of( the)?)? *$views( *side)?)+ *(v[ie]*ws?))+",'view'],
			["\b(cu *)?(vedere *(laterala|partiala)?(( |(si|sau)|la )*(mare|piscina|munte|oras|panoramica|parc|marina|laguna|gradina|plaja|golf|lac||jac+uz+[yi]))+)\b",'view'],
			["\b( *((with|free|w\/|\() *)?(ac+es+ *to *)?(shar(ed|ing) *)?(((with|w\/|or|and)|\/) *)?(pr(iv(a(das?|te?))?)?\.?|own)?( *(ind|outd)\.?(oors?)? *)?( *(bath *rooms?|gar[dt]ens?|kitchen(ette)?s?|((heated) *)?((plunge|swim+ing|whirl) *)?pools?|ter+a(ce|z+as?)|wi *fi|jac+uz+[yi]|beach|balc.?ony|smoking|jet+ed tub|hydro *mas+age( *bath)?( *tub)?|courtyard|rooftop|patio|butler|(walk *in *)?shower|(hot ?)?tub)( *areas?)?)( *ac+es+)?(?!(v[ie]*ws?)))+( *\))?\b",'facility'],
			["\b((((cu|si|sau|\()| ) *)?(ac+es+ *la *)?(( *(baie?|pis.?cin[ae]|fumatori|jac+uz+[yi]|balcon|terasa))+)( *(independ[ae]nt|(pr(iv(a(das?|te?))?)?\.?|propri[uei])|(interioa?r[ae]?|exterioa?r[ae]?)) *)*( *\))?)+\b",'facility'],
			["\b(((on|\()) *)?(pr(iv(a(das?|te?))?)?\.?|own)?( *(peninsula)+)( *\)?)\b",'facility'],
			["(with|w\/) *views",'view'],
			["\bpoolzugang\b",'facility'], // acces la piscina
			["\b[^\s]+ *zimmer\b",'view'], // camera de tipul (family, dubla) ... in germana
			["\b(seitl(\.|icher)? *)?[^\s]+ *blick\b",'view'], // vedere la ... in germana
			["\bvista *(mare?)( *lateral|frontal)?\b",'view'], // vedere la ... in spaniola sau ceva
			["\b(cu *)?(vedere *(laterala|partiala)?(( |(si|sau)|la )*(mare|piscina|munte|oras|panoramica|parc|marina|laguna|gradina|plaja|golf|lac))+)\b",'view'],
			["\b((Camer[ae]|Habitación) *)?(penthouse|junior|senior|accesible|económica|economy|budget|elite|premiere?|prestige|luxury|std\.?|dbl?\.?|basic|standar[dt]|de *luxe|superioa?r[ae]?|premium|priviledge|platinum|executive|elegance|comfort)(?!( *v[ie]*ws?))( *Rooms?)?\b",'quality', 1],
			["\b(classic)( *Rooms?)?\b",'quality'],
			["\b((wel+nes+) *)?((small|large) *)?(prime|romantic|privilege|exclus+ive|veranda|country *style|dream|love|relax|island|ter+a(ce|z+as?)|panoramic|select)( *Rooms?)\b",'quality'],
			["\b(r[or]|[bfh]b|(prem|u(ltra)?)? *a(i|(l(l[xd]?)?( *in(clus*ive?)?)))|(bed( *(and|&) *)?)?breaks?fast|bautur[ai]|beverages?|buf+et|drink|dinner|din[ae] *around|brunch|cina|(mic *)?dejun|(half|full) *board|((premium|ultra) *)?all *inclusive|demipensiune|pensiune *completa|room (only|rates?))( *(plus|\+))?( *included( *in *the *price)?)?\b", 'meal'],
			["\b(Camera *)?(($numbers *)?(inter *)?con+ect(ing|ed)|guests?|part(y|ies)|family|familiar|kings?|spacious|queens?|dubl[ae]|tripl[ae]|cvadrupl[ae]|quads?|grands?|dou?bles?|singles?|quadruples?|quintuples?|triples?|twin( *bed(ded)?)?s?|quatro|rooms?)( *(Rooms?|Camer[ae]))?\b",'size'],
			["\b((wel+nes+) *)?((private|exclusive) *)?(residences?|vil+as?|lofts?|bunga?lows?|cabana|aparta?mento?[se]?|(love|couple|master|supreme|presidential|royal|mini|designer|jr\.?|junior|sr\.?|senior|family|master|excecutive|exe\.?(cutive)?)?suit[ae]s?|e?studios?|duplex)( *(privat[ae]|exclusiv[ae]))?\b",'type'],
			["\b(((with|w\/|and|or|cu|and|or|si|sau| ) *)?($numbers? *(((extra|bunk) *)?beds?|pat(uri)?( ?suprapuse?)|sofas?|canape(a|ele))))+\b",'beds'],
			["\b(((with|w\/|and|or|cu|and|or|si|sau| ) *)?($numbers? *(living|bed(ded)? *ro*m?s?\.?|dormitoa?re?|dormitorios?)))+\b",'layout'],
			["\b($numbers? *(bed(s|ded)?))\b",'layout'],
			["((ground|[0-9]*(1(st)?|2(nd)?|3(rd)?|(4-9|0)(th)?)|upper|lower|top) *floor)", 'position'],
			["(front|back|middle) *yard", 'position'],
			["(la *)?(mansarda|parter)", 'position'],
			["$views *side( *room)?",'position'],
			["$views *front",'position'],
			["((pr\.|priv\.?(ate)|excl\.?(us+ive)) *)?$views *access( *room)?",'position'],
		];
		
		foreach($querries as $query){
			$query[0] = str_replace(' ', '[ \-\+,]', $query[0]);
			if(!$form){
				for($i = 1; $i<=($query[2] ?? 4); $i++){
					$this->db->query("UPDATE `tf_facilities2` set facilities=JSON_MERGE_PRESERVE(IFNULL(facilities, '{}'), JSON_OBJECT(" .  $this->db->escape($query[1]) . ", JSON_OBJECT(TRIM(REGEXP_REPLACE(name_rest, '.*?(" .  $this->db->escape_str($query[0]) . ").*', '$1')), 1))), name_rest = TRIM(REGEXP_REPLACE(name_rest, " .  $this->db->escape('^(.*?) *' . $query[0] . ' *') . ", '$1 ')) WHERE name_rest REGEXP " . $this->db->escape($query[0]) . "");
				}
			}
			if($form){
				foreach($facilities as $Name => $facility){
					$facs = null;
					$facilities[$Name][0] = trim(preg_replace_callback('~' . $query[0] . '~i', function($matches) use (&$facs, &$query){
						$formatted = trim($matches[0]);
						$formatted = trim(preg_replace('~^((\b(and|or|with|w|fara|cu|si|sau)\b|[\(\)[:punct:]])\s*)+~i', '', trim($formatted)));
						$formatted = trim(preg_replace('~[\s\(\)]+~', ' ', trim($formatted)));
						$formatted = trim(preg_replace('~[\s\(\)[:punct:]]+$~', '', trim($formatted)));
						// $formatted = trim(preg_replace('~(views?|zugang|blick|zimmer)$~i', '', trim($formatted)));
						// $formatted = trim(preg_replace('~(\b(vedere( partiala)?( la)?|front(al)?|lateral|side|(out|in)doors?)|(side\b))~i', '', trim($formatted)));
						$formatted = trim(preg_replace('~\s+~', ' ', trim($formatted)));
						$hash = strtolower($formatted);
						$facs[$query[1]][$hash] = $formatted;
						return ' ';
					}, $facility[0]));
					if($facs){
						$facilities[$Name][1] = array_replace_recursive($facilities[$Name][1] ?? [], $facs);
					}
				}
				// $this->db->query("UPDATE `tf_facilities2` set " . $query);
			}
		}
		
		dd(array_replace_recursive(...array_filter(array_values(array_map(function($v){return $v[1] ?? null;}, $facilities)))));
		dd(array_map(function($v){return $v[1] ?? null;}, $facilities));
		// CLEANUP
		$querries = [
			"name_rest = TRIM(REGEXP_REPLACE(name_rest, " . $this->db->escape("\([^a-z]*\)") . ", ' '))",
			"name_rest = TRIM(REGEXP_REPLACE(name_rest, '([^[:alnum:]])', ' '))",
			"name_rest = TRIM(REGEXP_REPLACE(name_rest, '  +', ' '))",
		];
		foreach($querries as $query){
			if(!$form){
				$this->db->query("UPDATE `tf_facilities2` set " . $query);
			}
		} */
	}
	public function determineFacilities() {
		$this->db->query("UPDATE `tf_hotels` SET `_extracted_facilities` = NULL");
		$sql = "SELECT * FROM `tf_facilities`  WHERE `type` = 'hotel' AND `regex` IS NOT NULL AND `regex` <> ''";
		$facilities = $this->query($sql)->result('array');
		foreach($facilities as $facility){
			$sql2 = "SELECT COUNT(Id) as total FROM `tf_hotels` 
			WHERE (Facilities IS NULL OR Facilities NOT LIKE '%" . $this->db->escape_str(json_encode($facility['Name'])) . "%') AND (ShortContent REGEXP " . $this->db->escape($facility['regex']) . ")";
			$total = $this->query($sql2)->row(0, 'array');
			$total = !empty($total['total']) ? $total['total'] : 0;
			$this->db->query("UPDATE  `tf_facilities` SET `total` = '" . (int)$total. "' WHERE `type` = 'hotel' AND `Name`=" . $this->db->escape($facility['Name']). "");
			if($total){
				$this->db->query("UPDATE `tf_hotels` SET `_extracted_facilities` = JSON_MERGE_PRESERVE(COALESCE(_extracted_facilities, '[]'), JSON_ARRAY(" . $this->db->escape($facility['Name']). ")) WHERE (Facilities IS NULL OR Facilities NOT LIKE '%" . $this->db->escape_str(json_encode($facility['Name'])) . "%') AND (_extracted_facilities IS NULL OR _extracted_facilities NOT LIKE '%" . $this->db->escape_str(json_encode($facility['Name'])) . "%') AND (ShortContent REGEXP " . $this->db->escape($facility['regex']) . ")");
			}
		}
	}
	public function importFacilities2($country_id = null) {
		$this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
		$cache_storage_path = 'travelfuse/' . 'test/';
		$cache_hash = $cache_storage_path . 'roomfacilities';
		$cache_time = 86400;
		$response = $this->cache->get($cache_hash, $cache_time);
		if(!$response){
			$sql = "SELECT (@j:=JSON_MERGE_PATCH(facilities, @j)) as j FROM `tf_facilities2` 
			JOIN (SELECT @j:='{}') dummy
			WHERE facilities is not null
			HAVING JSON_LENGTH(j) = MAX(JSON_LENGTH(j))";
			$q = $this->query($sql)->row(0, 'array');
			$response = !empty($q['j']) ? json_decode($q['j'], true) : [];
			setCacheStorage($cache_storage_path);
			$this->cache->save($cache_hash, $response, $cache_time);
		}
		
		// $j = array_keys($j);
		if($response){
			dd($response);
			// $this->db->query("INSERT IGNORE INTO `tf_facilities` (`Name`) VALUES (" . implode('),(', array_map([$this->db, 'escape'], $j)) . ")");
		}
		return;
		// echo '<pre>';
		// print_r($j);
		// die;
	}
	public function importFacilities($country_id = null) {
		$sql = "SELECT (@j:=JSON_MERGE_PATCH(CONCAT('{', REPLACE(substring(Facilities, 2, CHAR_LENGTH(Facilities) - 2),'\",\"','\":\"\", \"'), ':\"\"}'), @j)) as j FROM `tf_hotels` 
		JOIN (SELECT @j:='{}') dummy
		WHERE Facilities is not null " . ($country_id ? " AND CountryId = '" . (int)$country_id . "'" : '') . "
		HAVING JSON_LENGTH(j) = MAX(JSON_LENGTH(j))";
		$q = $this->query($sql)->row(0, 'array');
		$j = !empty($q['j']) ? json_decode($q['j'], true) : [];
		$j = array_keys($j);
		if($j){
			$this->db->query("INSERT IGNORE INTO `tf_facilities` (`Name`) VALUES (" . implode('),(', array_map([$this->db, 'escape'], $j)) . ")");
		}
		return;
		// echo '<pre>';
		// print_r($j);
		// die;
	}
	public function importCountries() {
		$_GET['only'] = 'countries';
		$this->import();
	}
	public function importCities() {
		$_GET['only'] = 'cities';
		$this->import();
	}
	public function importCityHotels() {
		$_GET['only'] = 'city_hotels';
		$this->import();
	}
	public function importProviders() {
		$_GET['only'] = 'providers';
		$this->import();
	}
	public function importCharterOffers() {
		$_GET['only'] = 'charter_offers';
		$r = $this->query("SELECT COUNT(id) as total FROM `tf_countries` ci WHERE missing IS NULL")->row();
		for ($i = 1; $i<=$r->total; $i++){
			$this->import();
		}
	}
	public function importTour() {
		set_time_limit(3600);
		$_GET['only'] = [
			'tour_dep_cities',
			'tour_cities',
			'tour_checkin',
		];
		$this->import();
	}
	public function importCharter() {
		set_time_limit(3600);
		$_GET['only'] = [
			'providers',
			'charter_cities',
			'charter_dep_cities',
			'charter_checkin',
			'charter_checkout',
		];
		$this->import();
	}
	public function importHotels() {
		$_GET['only'] = 'hotels';
			// $this->import();
			// die;
		$r = $this->query("SELECT COUNT(id) as total FROM `tf_countries` ci WHERE missing IS NULL")->row();
		for ($i = 1; $i<=$r->total; $i++){
			$this->import();
		}
	}
	public function import() {
		$this->load->model('TravelFuse_model');
		dlog("START");
		$tables = [
			'trip_countries' => [
				'prefix' => '',
				// 'drop' => true,
				// 'noskip' => true,
				'retriever' => function(){
					$this->load->model('Trip_model');
					$api = $this->Trip_model->get_api();
					$has_next_page = true;
					$page = 1;
					$countries = [];
					while($has_next_page){
						$get_request = 
						[
							'limit' => 50,
							'page' => $page,
						];
						array_walk_recursive($get_request, function(&$v){ $v = urlencode($v); });
						$url = 'index.php/static-data/countries?' . urldecode(http_build_query($get_request));
						$response = $api->apiCall($url);
						$has_result = !empty($response) && !empty($response->_embedded) && !empty($response->_embedded->countries);
						if($has_result){
							$countries = array_merge($countries, array_map(function($v){ return (array)$v; }, $response->_embedded->countries));
						}
						$has_next_page = $has_result && !empty($response->page_count) && !empty($response->page) && ($response->page == $page && $response->page != $response->page_count);
						if($has_next_page){
							$page++;
						}
					}
					return array_map(function($country){
						return $country;
					}, $countries);
				},
				'columns' => [
					'Id' => 'INT NOT NULL',
					'Name' => 'VARCHAR(255) NOT NULL',
					'ISO' => 'VARCHAR(2) NOT NULL',
				],
				'real_columns' => [
					'status' => 'TINYINT(1) DEFAULT "1"',
					'_name_en' => 'VARCHAR(255) DEFAULT NULL',
					'_name_ro' => 'VARCHAR(255) DEFAULT NULL',
				],
			],
			'trip_cities' => [
				'prefix' => '',
				'primary_key' => ['Id'],
				// 'drop' => true,
				// 'noskip' => true,
				'retriever' => function(){
					// return false;
					// dd($this->query("SELECT Id FROM `tf_countries`")->result());
					$countries = array_map(function($v){ return $v[0]; }, $this->query("SELECT Id as '0' FROM `trip_countries` WHERE `missing` IS NULL ORDER BY Id ASC")->result('array'));
					// $countries = [155];
					return function() use (&$countries){
						$this->load->model('Trip_model');
						$api = $this->Trip_model->get_api();
						
						foreach($countries as $country_id){
							dlog("Cities for CountryId=" . $country_id);
							
							$has_next_page = true;
							$page = 1;
							$cities = [];
							while($has_next_page){
								$get_request = 
								[
									'limit' => 50,
									'page' => $page,
									'filter' => [
										[
											'name' => 'CountryId',
											'term' => $country_id,
										],
									],
								];
								array_walk_recursive($get_request, function(&$v){ $v = urlencode($v); });
								$url = 'index.php/static-data/cities?' . urldecode(http_build_query($get_request));
								$response = $api->apiCall($url);
								// dump($response);
								$has_result = !empty($response) && !empty($response->_embedded) && !empty($response->_embedded->cities);
								if($has_result){
									$cities = array_merge($cities, array_map(function($v){ return (array)$v; }, $response->_embedded->cities));
								}
								$has_next_page = $has_result && !empty($response->page_count) && !empty($response->page) && ($response->page == $page && $response->page != $response->page_count);
								if($has_next_page){
									$page++;
								}
								// dd($cities);
							}
							
							
							dlog("Found " . (!$cities ? 0 : count($cities)));
							// dd($cities);
							yield array_map(function($city) use ($country_id){
								// $city['CountryId'] = $country_id; 
								// $city['Name'] = $city['Name'] ?? '';
								// $city['Name'] = html_entity_decode($city['Name'], ENT_QUOTES);
								// $city['Name'] = preg_replace('~\\\+~','',$city['Name']);
								return $city;
							}, $cities);
						}
					};
				},
				'columns' => [
					'Id' => 'INT NOT NULL',
					'CountryId' => 'INT NOT NULL',
					'Code' => 'VARCHAR(10) NOT NULL',
					'Name' => 'VARCHAR(255) NOT NULL',
					'SearchableOn' => 'VARCHAR(255) DEFAULT NULL',
					'NrHotels' => 'INT DEFAULT NULL',
					'AltName' => 'VARCHAR(255) DEFAULT NULL',
				],
				'real_columns' => [
					'city_hotels' => 'datetime NULL',
					'status' => 'TINYINT(1) DEFAULT "1"',
					'_name_en' => 'VARCHAR(255) DEFAULT NULL',
					'_name_ro' => 'VARCHAR(255) DEFAULT NULL',
				]
			],
			'trip_locations' => [
				'prefix' => '',
				'primary_key' => ['Id'],
				// 'drop' => true,
				// 'noskip' => true,
				'retriever' => function(){
					// return false;
					// dd($this->query("SELECT Id FROM `tf_countries`")->result());
					$countries = array_map(function($v){ return $v[0]; }, $this->query("SELECT Id as '0' FROM `trip_countries` WHERE `missing` IS NULL ORDER BY Id ASC")->result('array'));
					// $countries = [155];
					return function() use (&$countries){
						$this->load->model('Trip_model');
						$api = $this->Trip_model->get_api();
						
						foreach($countries as $country_id){
							dlog("Locations for CountryId=" . $country_id);
							
							$has_next_page = true;
							$page = 1;
							$cities = [];
							while($has_next_page){
								$get_request = 
								[
									'limit' => 50,
									'page' => $page,
									'filter' => [
										[
											'name' => 'CountryId',
											'term' => $country_id,
										],
									],
								];
								array_walk_recursive($get_request, function(&$v){ $v = urlencode($v); });
								$url = 'index.php/static-data/locations?' . urldecode(http_build_query($get_request));
								$response = $api->apiCall($url);
								// dd($response);
								$has_result = !empty($response) && !empty($response->_embedded) && !empty($response->_embedded->locations);
								if($has_result){
									$cities = array_merge($cities, array_map(function($v){ return (array)$v; }, $response->_embedded->locations));
								}
								$has_next_page = $has_result && !empty($response->page_count) && !empty($response->page) && ($response->page == $page && $response->page != $response->page_count);
								if($has_next_page){
									$page++;
								}
								// dd($cities);
							}
							
							
							dlog("Found " . (!$cities ? 0 : count($cities)));
							// dd($cities);
							yield array_map(function($city) use ($country_id){
								// $city['CountryId'] = $country_id; 
								// $city['Name'] = $city['Name'] ?? '';
								// $city['Name'] = html_entity_decode($city['Name'], ENT_QUOTES);
								// $city['Name'] = preg_replace('~\\\+~','',$city['Name']);
								return $city;
							}, $cities);
						}
					};
				},
				'columns' => [
					'Id' => 'INT NOT NULL',
					'CountryId' => 'INT NOT NULL',
					'CityId' => 'INT NOT NULL',
					'CountryISO' => 'VARCHAR(10) NOT NULL',
					'Code' => 'VARCHAR(10) NOT NULL',
					'Type' => 'VARCHAR(20) NOT NULL',
					'Name' => 'VARCHAR(255) NOT NULL',
					'Lat' => 'DECIMAL(10,8) NULL DEFAULT NULL',
					'Lng' => 'DECIMAL(11,8) NULL DEFAULT NULL',
				],
				'real_columns' => [
					'status' => 'TINYINT(1) DEFAULT "1"',
					'_name_en' => 'VARCHAR(255) DEFAULT NULL',
					'_name_ro' => 'VARCHAR(255) DEFAULT NULL',
				]
			],
			'providers' => [
				// 'drop' => true,
				// 'noskip' => true,
				'retriever' => function(){
					// dd($this->TravelFuse_model->getProviders());
					// return false;
                    
                    $providers = $this->TravelFuse_model->getProviders();
                    if(!$providers){
                        dd('ERROR $providers empty. Stopping');
                    }
					return array_map(function($country){
						$country['Caption'] = $country['Caption'] ?? '';
						$country['Caption'] = html_entity_decode($country['Caption'], ENT_QUOTES);
						$country['Caption'] = preg_replace('~\\\+~','',$country['Caption']);
						return $country;
					}, $providers);
				},
				'columns' => [
					'Id' => 'INT NOT NULL',
					'Caption' => 'VARCHAR(255) NOT NULL',
				],
				'real_columns' => [
					'status' => 'TINYINT(1) DEFAULT "1"',
				],
			],
			'countries' => [
				// 'drop' => true,
				// 'noskip' => true,
				'retriever' => function(){
					// return false;
					return array_map(function($country){
						$country['Name'] = $country['Name'] ?? '';
						$country['Name'] = html_entity_decode($country['Name'], ENT_QUOTES);
						$country['Name'] = preg_replace('~\\\+~','',$country['Name']);
						return $country;
					}, $this->TravelFuse_model->getCountries());
				},
				'columns' => [
					'Id' => 'INT NOT NULL',
					'Code' => 'VARCHAR(2) DEFAULT NULL',
					'Name' => 'VARCHAR(255) NOT NULL',
				],
				'real_columns' => [
					'city_hotels' => 'datetime NULL',
					'hotels' => 'datetime NULL',
					'charter_offers' => 'datetime NULL',
					'status' => 'TINYINT(1) DEFAULT "1"',
					'_name_en' => 'VARCHAR(255) DEFAULT NULL',
					'_name_ro' => 'VARCHAR(255) DEFAULT NULL',
				]
			],
			'destinations' => [
				'drop' => true,
				'noskip' => true,
				'retriever' => function(){
					// return false;
					$d = $this->TravelFuse_model->getDestinations(false, false);
					
					echo '<pre>';
					var_dump($this->TravelFuse_model->api->request);
					// echo 'bla'; 
					// var_dump($dates);
					dd($d);
					return array_map(function($country){
						$country['Name'] = $country['Name'] ?? '';
						$country['Name'] = html_entity_decode($country['Name'], ENT_QUOTES);
						$country['Name'] = preg_replace('~\\\+~','',$country['Name']);
						return $country;
					}, $this->TravelFuse_model->getDestinations());
				},
				'columns' => [
					'Id' => 'INT NOT NULL',
					'Code' => 'VARCHAR(2) DEFAULT NULL',
					'Name' => 'VARCHAR(255) NOT NULL',
				],
				'real_columns' => [
					'city_hotels' => 'datetime NULL',
					'hotels' => 'datetime NULL',
				]
			],
			'cities' => [
				'primary_key' => ['Id', 'type'],
				// 'drop' => true,
				// 'noskip' => true,
				'retriever' => function(){
					// return false;
					// dd($this->query("SELECT Id FROM `tf_countries`")->result());
					$countries = array_map(function($v){ return $v[0]; }, $this->query("SELECT Id as '0' FROM `tf_countries` WHERE `missing` IS NULL")->result('array'));
					// $countries = [155];
					return function() use (&$countries){
						foreach($countries as $country_id){
							dlog("Cities for CountryId=" . $country_id);
							$cities = $this->TravelFuse_model->getCities(['CountryId' => $country_id]);
							dlog("Found " . (!$cities ? 0 : count($cities)));
							// dd($cities);
							yield array_map(function($city) use ($country_id){
								$city['CountryId'] = $country_id; 
								$city['Name'] = $city['Name'] ?? '';
								$city['Name'] = html_entity_decode($city['Name'], ENT_QUOTES);
								$city['Name'] = preg_replace('~\\\+~','',$city['Name']);
								return $city;
							}, $cities);
						}
					};
				},
				'columns' => [
					'Id' => 'INT NOT NULL',
					'type' => 'VARCHAR(20) NOT NULL',
					'CountryId' => 'INT NOT NULL',
					'Name' => 'VARCHAR(255) NOT NULL',
				],
				'real_columns' => [
					'city_hotels' => 'datetime NULL',
					'status' => 'TINYINT(1) DEFAULT "1"',
					'_name_en' => 'VARCHAR(255) DEFAULT NULL',
					'_name_ro' => 'VARCHAR(255) DEFAULT NULL',
				]
			],
			'charter_cities' => [
				'primary_key' => ['Id', 'type', 'Transport'],
				// 'on_keys' => ['Id', 'type', 'Transport'],
				
				// 'drop' => true,
				// 'noskip' => true,
				'retriever' => function(){
					return function(){
                        $any_found = false;
						foreach(['bus', 'plane'] as $transport){
							dlog("Charter Cities for Transport=" . $transport);
                            $searchCharterCities = $this->TravelFuse_model->searchCharterCities(['Transport' => $transport], [], 43200);
                            
                            if($searchCharterCities) $any_found = true;
                            if('plane' == $transport){
                                if(!$any_found){
                                    dd('ERROR $searchCharterCities empty. Stopping');
                                }
                            }
							yield array_map(function($city) use ($transport){
								$city['Transport'] = $transport; 
								return $city;
							}, $searchCharterCities);
						}
					};
				},
				'columns' => [
					'Id' => 'INT NOT NULL',
					'type' => 'VARCHAR(20) NOT NULL',
					'Transport' => 'VARCHAR(10) NOT NULL',
				],
			],
			'charter_dep_cities' => [
				'primary_key' => ['Id', 'Transport', 'CityCode', 'DestinationType'],
				// 'drop' => true,
				// 'noskip' => true,
				'retriever' => function(){
					$charter_cities = $this->query("SELECT Id as '0', type as '1', Transport as '2' FROM `tf_charter_cities` WHERE `missing` IS NULL")->result('array');
					return function() use (&$charter_cities){
						// $countries = [155];
						foreach($charter_cities as $charter_city){
							dlog("Departure Cities for CityId=" . $charter_city[0] . " type " . $charter_city[1] . " transport " . $charter_city[2]);
							$cities = $this->TravelFuse_model->searchCharterDepartureCities([
								'Transport' => $charter_city[2],
								'destination' => $charter_city[0],
								'destinationType' => $charter_city[1],
							]);
							dlog("Found " . (!$cities ? 0 : count($cities)));
							yield !$cities ? [] : array_map(function($city) use ($charter_city){
								$city['Transport'] = $charter_city[2];
								$city['CityCode'] = $charter_city[0];
								$city['DestinationType'] = $charter_city[1];
								return $city;
							}, $cities);
						}
					};
				},
				'columns' => [
					'Id' => 'INT NOT NULL',
					'Transport' => 'VARCHAR(10) NOT NULL',
					'CityCode' => 'INT NOT NULL',
					'DestinationType' => 'VARCHAR(20) NOT NULL',
				],
			],
			'charter_checkin' => [
				'primary_key' => ['Id', 'Transport', 'CityCode', 'DestinationType', 'Date'],
				// 'drop' => true,
				// 'noskip' => true,
				'retriever' => function(){
					$charter_cities = $this->query("SELECT `Id`, `Transport`, `CityCode`, `DestinationType` FROM `tf_charter_dep_cities` WHERE `missing` IS NULL")->result('array');
					return function() use (&$charter_cities){
						// $countries = [155];
						foreach($charter_cities as $charter_city){
							dlog("Checkin for " . http_build_query($charter_city, '', ' '));
							$dates = $this->TravelFuse_model->searchCharterCheckIn([
								'Transport' => $charter_city['Transport'],
								'destination' => $charter_city['CityCode'],
								'destinationType' => $charter_city['DestinationType'],
								'departureCity' => $charter_city['Id'],
							]);
							dlog("Found " . (!$dates ? 0 : count($dates)));
							yield !$dates ? [] : array_map(function($date) use (&$charter_city){
								$city = $charter_city;
								$city['Date'] = $date;
								return $city;
							}, $dates);
						}
					};
				},
				'columns' => [
					'Id' => 'INT NOT NULL',
					'Transport' => 'VARCHAR(10) NOT NULL',
					'CityCode' => 'INT NOT NULL',
					'DestinationType' => 'VARCHAR(20) NOT NULL',
					'Date' => 'DATE NOT NULL',
				],
			],
			
			'charter_checkout' => [
				'primary_key' => ['Id', 'Transport', 'CityCode', 'DestinationType', 'DepartureDate', 'Date', 'Provider'],
				'keys' => ['Id', 'Transport', 'CityCode', 'DestinationType', 'DepartureDate', 'Date', 'Provider'],
				// 'drop' => true,
				// 'noskip' => true,
				'retriever' => function(){
					$charter_cities = $this->query("SELECT `Id`, `Transport`, `CityCode`, `DestinationType`, `Date` as DepartureDate FROM `tf_charter_checkin` WHERE `missing` IS NULL")->result('array');
					return function() use (&$charter_cities){
						// $countries = [155];
						foreach($charter_cities as $charter_city){
							dlog("Checkout for " . http_build_query($charter_city, '', ' '));
							$dates = $this->TravelFuse_model->searchCharterCheckOut([
								'Transport' => $charter_city['Transport'],
								'destination' => $charter_city['CityCode'],
								'destinationType' => $charter_city['DestinationType'],
								'departureCity' => $charter_city['Id'],
								'departureDate' => $charter_city['DepartureDate'],
							],[],43200, false);
							if(!is_array($dates)){
								/* 
								sleep(1);
								*/
								$dates = $this->TravelFuse_model->searchCharterCheckOut([
									'Transport' => $charter_city['Transport'],
									'destination' => $charter_city['CityCode'],
									'destinationType' => $charter_city['DestinationType'],
									'departureCity' => $charter_city['Id'],
									'departureDate' => $charter_city['DepartureDate'],
								],[],43200, false); 
								if(!is_array($dates)){
                                    dlog("ERROR EXPECTED ARRAY " . print_r($this->TravelFuse_model->api->request, true));
                                    $dates = [];
									// echo '<pre>';
									// var_dump($this->TravelFuse_model->api->request);
									// echo 'bla'; 
									// var_dump($dates);
									// dd($dates);
								}
							}
							dlog("Found " . (!$dates ? 0 : count($dates)));
							yield !$dates ? [] : array_reduce(array_map(function($date) use (&$charter_city){
								// dd($date);
								$city = $charter_city;
								$city['Date'] = $date['CheckOut'];
								$city['Providers'] = array_keys($date['Providers'] ? $date['Providers'] : []);
								return $city;
							}, $dates), function($carry, $item){
								foreach($item['Providers'] as $provider){
									$i = $item;
									unset($i['Providers']);
									$i['Provider'] = $provider;
									$carry[] = $i;
								}
								return $carry;
							}, []);
						}
					};
				},
				'columns' => [
					'Id' => 'INT NOT NULL',
					'Transport' => 'VARCHAR(10) NOT NULL',
					'CityCode' => 'INT NOT NULL',
					'DestinationType' => 'VARCHAR(20) NOT NULL',
					'Date' => 'DATE NOT NULL',
					'DepartureDate' => 'DATE NOT NULL',
					'Provider' => 'INT NOT NULL',
				],
				'real_columns' => [
					'offers' => 'datetime NULL',
				]
			],
			'tour_checkin' => [
				'primary_key' => ['Id', 'Transport', 'CityCode', 'DestinationType', 'Date'],
				'keys' => ['Id', 'Transport', 'CityCode', 'DestinationType', 'Date'],
				// 'drop' => true,
				// 'noskip' => true,
				'retriever' => function(){
					$charter_cities = $this->query("SELECT `Id` as CityCode, `Transport`, `CityCode` as Id, `type` as `DestinationType` FROM `tf_tour_cities` WHERE `missing` IS NULL")->result('array');
					return function() use (&$charter_cities){
						// echo file_get_contents('https://ifconfig.me'); die;
						// $countries = [155];
						foreach($charter_cities as $charter_city){
							dlog("Checkin for " . http_build_query($charter_city, '', ' '));
							$dates = $this->TravelFuse_model->searchTourCheckIn([
								'Transport' => $charter_city['Transport'],
								'destination' => $charter_city['CityCode'],
								'destinationType' => $charter_city['DestinationType'],
								'departureCity' => $charter_city['Id'],
							],[],false,false);
							// echo '<pre>';
								// var_dump($this->TravelFuse_model->api->request);
								// echo 'bla'; var_dump($dates);
								// dd($dates);
							// dd($dates);
							if(!is_array($dates)){
								$dates = $this->TravelFuse_model->searchTourCheckIn([
									'Transport' => $charter_city['Transport'],
									'destination' => $charter_city['CityCode'],
									'destinationType' => $charter_city['DestinationType'],
									'departureCity' => $charter_city['Id'],
								],[],false,false);
								if(!is_array($dates)){
									echo '<pre>';
									var_dump($this->TravelFuse_model->api->request);
									echo 'bla'; 
									var_dump($dates);
									dd($dates);
								}
							} else {
								// if(!empty($dates)){
									// dd($dates);
								// }
							}
							if($dates){
								$dates = array_values(array_reduce($dates, function($carry, $item) use ($charter_city){
									$date = preg_replace('/\-[0-9]{2}$/','-01',$item['Date']);
									
									$date = new DateTime($date);
									$date->modify('last day of this month');
									$date = $date->format('Y-m-d');
									
									$response_file = $this->TravelFuse_model->tourOfferList([
										'Transport' => $charter_city['Transport'],
										'TourCountryCode' => $charter_city['CityCode'],
										'DestinationType' => $charter_city['DestinationType'],
										'DepCityCode' => $charter_city['Id'],
										'CheckIn' => $date,
										'Adults' => [[2]],
										'Children' => 0,
										'ChildrenAge' => [],
										'Provider' => null,
									], [], false, true, false);
									if($response_file){
										if(!$this->TravelFuse_model->api->isValidJsonFile($response_file, ['[' => "{"])){
											return $carry;
										}
									}
									$item['Date'] = $date;
									$carry[$date] = $item;
									return $carry;
								}, []));
							}
							dlog("Found " . (!$dates ? 0 : count($dates)));
							yield !$dates ? [] : array_map(function($date) use (&$charter_city){
								$city = array_replace($charter_city, $date);
								return $city;
							}, $dates);
						}
					};
				},
				'columns' => [
					'Id' => 'INT NOT NULL',
					'Transport' => 'VARCHAR(10) NOT NULL',
					'CityCode' => 'INT NOT NULL',
					'DestinationType' => 'VARCHAR(20) NOT NULL',
					'Date' => 'DATE NOT NULL',
					'Providers' => 'LONGTEXT NULL',
				],
			],
			
			'tour_dep_cities' => [
				'primary_key' => ['Id', 'Transport'],
				// 'on_keys' => ['Id', 'type', 'Transport'],
				
				// 'drop' => true,
				// 'noskip' => true,
				'retriever' => function(){
					return function(){
						foreach(['bus', 'plane'] as $transport){
							dlog("Tour Departure Cities for Transport=" . $transport);
							yield array_map(function($city) use ($transport){
								$city['Transport'] = $transport; 
								// $city['type'] = $city['type'] ?? 'city'; 
								return $city;
							}, $this->TravelFuse_model->searchTourDepartureCities(['Transport' => $transport]));
						}
					};
				},
				'columns' => [
					'Id' => 'INT NOT NULL',
					'Transport' => 'VARCHAR(10) NOT NULL',
				],
			],
			
			'tour_cities' => [
				'primary_key' => ['Id', 'type', 'Transport', 'CityCode', 'Name'],
				'keys' => ['Id', 'type', 'Transport', 'CityCode'],
				'drop' => true,
				'noskip' => true,
				'afterfunction' => function(){
					$this->query("UPDATE `tf_destinations` SET missing=NOW() WHERE missing IS NULL;");
					$this->query("INSERT INTO `tf_destinations` (`Id`, `Name`) (SELECT Id, Name FROM `tf_tour_cities` WHERE type='destination' AND missing IS NULL GROUP by Id) ON DUPLICATE KEY UPDATE `Name` = VALUES(Name), `missing` = NULL");
				},
				'retriever' => function(){
					$tour_dep_cities = $this->query("SELECT Id as '0', Transport as '1' FROM `tf_tour_dep_cities` WHERE `missing` IS NULL")->result('array');
					return function() use (&$tour_dep_cities){
						// $countries = [155];
						foreach($tour_dep_cities as $tour_city){
							dlog("Tour Cities for CityId=" . $tour_city[0] . " transport " . $tour_city[1]);
							$cities = $this->TravelFuse_model->searchTourCities([
								'Transport' => $tour_city[1],
								'departureCity' => $tour_city[0],
							]);
							dlog("Found " . (!$cities ? 0 : count($cities)));
							yield !$cities ? [] : array_map(function($city) use ($tour_city){
								$city['Transport'] = $tour_city[1];
								$city['CityCode'] = $tour_city[0];
								return $city;
							}, $cities);
						}
					};
				},
				'columns' => [
					'Id' => 'INT NOT NULL',
					'type' => 'VARCHAR(20) NOT NULL',
					'Transport' => 'VARCHAR(10) NOT NULL',
					'CityCode' => 'INT NOT NULL',
					'Name' => 'VARCHAR(255) NOT NULL',
				],
			],
			'charter_offers' => [
				'primary_key' => ['Id', 'CityCode', 'DestinationType'],
				// 'drop' => true,
				// 'noskip' => true,
				'sql_where_extra' => function(){
					global $city_hotels_country_id;
					return " AND c.CountryId = '" . (int)$city_hotels_country_id . "'";
				},
				'retriever' => function(){
					// return false;
					// $this->query("UPDATE `tf_countries` SET charter_offers = NULL");
					$this->query("SELECT last_insert_id(0)");
					$this->query("UPDATE `tf_countries` SET id=last_insert_id(id), charter_offers = NOW(), date_modified=date_modified 
					ORDER BY charter_offers ASC, Id ASC LIMIT 1");
					$country_id = $this->db->insert_id();
					global $city_hotels_country_id;
					$city_hotels_country_id = $country_id;
					dump($city_hotels_country_id);
					if(!$country_id){
						return false;
					}
					$sql = "
					SELECT cco.Id DepCityCode, cco.Transport, cco.CityCode Destination, cco.DestinationType, cco.DepartureDate CheckIn, cco2.Date as CheckOut, cco2.Provider 
					FROM (
						SELECT cco.Id, cco.Transport, cco.CityCode, cco.DestinationType, cco.DepartureDate, MAX(cco2.Date) as Date, cco2.Provider FROM (
							SELECT cco.Id, cco.Transport, cco.CityCode, cco.DestinationType, MAX(cco.DepartureDate) DepartureDate
							FROM `tf_cities` ci
							JOIN `tf_charter_checkout` cco ON(cco.destinationType=ci.type AND cco.CityCode = ci.Id AND cco.missing IS NULL AND cco.DepartureDate > CURRENT_DATE)
							JOIN `tf_providers` pr ON (pr.Id = cco.Provider AND pr.missing IS NULL AND pr.status = 1)
							WHERE 	ci.CountryId='" . (int)$country_id . "'
								AND ci.status = 1
								AND ci.missing IS NULL
							GROUP BY cco.Id, cco.Transport, cco.CityCode, cco.DestinationType
						) cco
						JOIN `tf_charter_checkout` cco2 USING (Id, Transport, CityCode, DestinationType, DepartureDate)
						GROUP BY cco.Id, cco.Transport, cco.CityCode, cco.DestinationType
					) cco
					JOIN `tf_charter_checkout` cco2 USING (Id, Transport, CityCode, DestinationType, DepartureDate, Date)
					WHERE cco2.offers IS NULL
					";
					// dd($sql);
					$cities = $this->query($sql)->result('array');
					// dd($cities);
					
					return function() use (&$cities, &$country_id){
						foreach($cities as $city_arr){
							dump($city_arr);
							unset($city_arr['offers']);
							$offers = $this->TravelFuse_model->charterOfferList(array_replace($city_arr, [
								'Transport' => 'plane',
								'Adults' => [2],
								'Children' => [0],
								// 'ProductCode' => $city_arr['Id'],
							]));
							if($offers){
								$merches = array_reduce($offers, function($carry, $hotel) use ($city_arr){ 
									if(!empty($hotel['Offers'])){
										foreach($hotel['Offers'] as $offer){
											if(empty($offer['Items'])) continue;
											foreach($offer['Items'] as $item){
												if(empty($item['Merch'])) {
													dd("Item has no merch", $item, $offer, $hotel, $city_arr);
												}
												if(empty($item['Merch']['Title'])) {
													// dd("Item has no merch title", $item, $offer, $hotel, $city_arr);
												}
												if(empty($item['Merch']['type'])) {
													dd("Item has no merch type", $item, $offer, $hotel, $city_arr);
												}
												$item['Merch']['type'] = ucfirst(strtolower(trim($item['Merch']['type'])));
												if(!empty($item['Merch']['Title'])) {
													$item['Merch']['Title'] = trim(preg_replace('/\s+/', ' ', html_entity_decode($item['Merch']['Title'], ENT_QUOTES)));
													
													$test_regex = '/[0-9][\-\.\/]/';
													if(preg_match($test_regex, $item['Merch']['Title'])){
														$regex = '/\b(0[1-9]|[1-2][0-9]|3[0-1])([\.\-\/])(0[1-9]|1[0-2])\2([1-9][0-9]{3,}) (0[0-9]|1[0-9]|2[0-3]):((?:0|[1-5])[0-9])\b/';
														
														$item['Merch']['Title'] = preg_replace($regex, '{date:dmyhi}', $item['Merch']['Title']);
														$regex = '/\b(0[1-9]|[1-2][0-9]|3[0-1])([\.\-\/])(0[1-9]|1[0-2])\2([1-9][0-9]{3,})\b/';
														
														$item['Merch']['Title'] = preg_replace($regex, '{date:dmy}', $item['Merch']['Title']);
														
														if(preg_match('/([0-9]{2}[\-\.\/][0-3][0-9][\-\.\/]|[\-\.\/][0-9]{2}[\-\.\/][0-3][0-9])/', $item['Merch']['Title'])){
															dd("Unknown title format", $item, $offer, $hotel, $city_arr);
														}
													}
													
													if('Merch' == $item['Merch']['type']){
														// $regex = '/Transfer\s+aeroport-hotel-aeroport\s+.*/';
														// $item['Merch']['Title'] = preg_replace($regex, 'Transfer aeroport-hotel-aeroport {locations}', $item['Merch']['Title']);
														
														// if(preg_match($test_regex, $item['Merch']['Title'])){
															// dd("Unknown transport title format", $item, $offer, $hotel, $city_arr);
														// }
													}
													if('Transport' == $item['Merch']['type']){
														
														// if(!empty($item['Merch']['TransportType'])){
															// $test_regex = '/^(Dus|Retur)/';
															
															
														// } else {
															// dd("Unknown transport format", $item, $offer, $hotel, $city_arr);
														// }
														// $test_regex = '/(Dus|Retur): [^{]/';
														// if(preg_match($test_regex, $item['Merch']['Title'])){
															// $regex = '/(Dus|Retur):([\s\/]+[A-Z]{3})+/';
															// $item['Merch']['Title'] = preg_replace($regex, '\1: {airport_codes}', $item['Merch']['Title']);
															
															// if(preg_match($test_regex, $item['Merch']['Title'])){
																// dd("Unknown transport title format", $item, $offer, $hotel, $city_arr);
															// }
														// }
														
														// $test_regex = '/^(Transport\s+)Avion/i';
														// if(preg_match($test_regex, $item['Merch']['Title'])){
															// $regex = '/^(Transport\s+)Avion.*/i';
															// $item['Merch']['Title'] = preg_replace($regex, 'Transport avion', $item['Merch']['Title']);
														// }
													}
													
													$regex = '/202[45](\s*\/\s*202[45])?/';
													$item['Merch']['Title'] = preg_replace($regex, '{ani}', $item['Merch']['Title']);
													// $test_regex = '/2[0-9]{3}/';
													// if(preg_match($test_regex, $item['Merch']['Title'])){
														// dd("Unknown title format", $item, $offer, $hotel, $city_arr);
													// }
													
													if('Hotel' == $item['Merch']['type']){
														dd("Disallowed merch type", $item, $offer, $hotel, $city_arr);
													}
												}
												
												$carry[$item['Merch']['type']][strtolower($item['Merch']['Title'])] = [$item['Merch']['Title'], $item['Merch']];
											}
										}
									}
									return $carry; 
								}, []);
								
								foreach($merches as $merch_type => $merch_names){
									$merch_type_sql = $this->db->escape($merch_type);
									$j = array_map(function($merch) use ($merch_type_sql){
										$merch_data = $this->db->escape(json_encode($merch[1], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
										$merch_name = $merch[0];
										return "{$this->db->escape($merch_name)} ,{$merch_type_sql}, {$merch_data}";
									}, array_values($merch_names));
									$this->db->query("INSERT IGNORE INTO `tf_facilities2` (`Name`, `type`, `data`) VALUES (" . implode('),(', $j) . ")");
								}
								
								
							$this->db->query("UPDATE `tf_charter_checkout` SET `offers` = NOW(), `count` = 0, `hotels` = 0, date_modified=date_modified WHERE `Id` = {$this->db->escape($city_arr['DepCityCode'])} AND `Transport` = {$this->db->escape($city_arr['Transport'])} AND `CityCode` = {$this->db->escape($city_arr['Destination'])} AND `DestinationType` = {$this->db->escape($city_arr['DestinationType'])} AND `Date` = {$this->db->escape($city_arr['CheckOut'])} AND `DepartureDate` = {$this->db->escape($city_arr['CheckIn'])} AND `Provider` = {$this->db->escape($city_arr['Provider'])}");
							
								
								$this->db->query("UPDATE `tf_charter_checkout` SET `hotels` = '" . count($offers) . "', `count` = '" . array_reduce($offers, function($carry, $item){ return $carry + (!empty($item['Offers']) ? count($item['Offers']) : 0); }, 0) . "', date_modified=date_modified WHERE `Id` = {$this->db->escape($city_arr['DepCityCode'])} AND `Transport` = {$this->db->escape($city_arr['Transport'])} AND `CityCode` = {$this->db->escape($city_arr['Destination'])} AND `DestinationType` = {$this->db->escape($city_arr['DestinationType'])} AND `Date` = {$this->db->escape($city_arr['CheckOut'])} AND `DepartureDate` = {$this->db->escape($city_arr['CheckIn'])} AND `Provider` = {$this->db->escape($city_arr['Provider'])}");
								
								// dump($merches);
								// dd($offers);
							}
							
							continue;
							
							// dump($this->TravelFuse_model->charterOfferDetails(array_replace($city_arr['checkouts'], [
								// 'Transport' => 'plane',
								// 'Adults' => [2],
								// 'Children' => [0],
								// 'ProductCode' => $city_arr['Id'],
							// ])));
							// dump($this->TravelFuse_model->api->request);
							// dd($hotel_id);
							dd('iesit');
							
							list($city_id, $city_type) = $city_arr;
							dlog("Hotels for CityCode=$city_id DestinationType=$city_type CountryId=$country_id");
							$hotels = $this->TravelFuse_model->getHotels(['CityCode' => $city_id, 'DestinationType' => $city_type]);
							// $this->query("SELECT last_insert_id(0)");
							$this->query("UPDATE `tf_cities` SET city_hotels = NOW(), date_modified=date_modified WHERE `Id` = " . $this->db->escape($city_id) . " AND `type` = " . $this->db->escape($city_type) . " LIMIT 1");
							dlog("Found " . (!$hotels ? 0 : count($hotels)));
							yield array_map(function($hotel) use ($city_arr, &$country_id){
								return [
									'Id' => $hotel['Id'],
									'CountryId' => $country_id,
									'CityCode' => $city_arr[0],
									'DestinationType' => $city_arr[1],
								];
							}, $hotels);
						}
					};
					
					// dd($cities);
					dd($cities);
					
					$sql = "
					SELECT *
						, (
							SELECT JSON_OBJECT(
								'Transport', cco.transport
								,'DestinationType', ci.type
								,'Destination', ci.Id
								,'DepCityCode', cco.Id
								,'CheckIn', cco.DepartureDate
								,'CheckOut', cco.Date
								,'Providers', CAST(cco.Providers AS JSON)
							)
							FROM `tf_city_hotels` ch
							JOIN `tf_cities` ci ON (ci.Id = ch.CityCode AND ch.destinationType = ci.type AND ci.missing IS NULL AND ci.status = 1)
							JOIN `tf_charter_checkout` cco ON (cco.destinationType=ci.type AND cco.CityCode = ci.Id AND cco.missing IS NULL AND cco.DepartureDate > DATE_ADD(CURRENT_DATE, INTERVAL 5 MONTH))
							WHERE h.Id = ch.Id
							ORDER BY ci.type ASC, cco.DepartureDate ASC, cco.Date ASC
							LIMIT 1
						) as checkouts
					FROM (SELECT DISTINCT ch.Id
						FROM `tf_cities` ci
						JOIN `tf_city_hotels` ch ON (ch.CountryId = ci.CountryId AND ch.destinationType=ci.type AND ch.CityCode = ci.Id AND ch.missing IS NULL)
						WHERE 	ci.CountryId='" . (int)$country_id . "'
							AND ci.status = 1
							AND ci.missing IS NULL
							AND EXISTS(SELECT 1 FROM `tf_charter_checkout` cco WHERE cco.destinationType=ci.type AND cco.CityCode = ci.Id AND cco.missing IS NULL AND cco.DepartureDate > DATE_ADD(CURRENT_DATE, INTERVAL 5 MONTH))
					) h
					WHERE 1
					LIMIT 1
					";
					// dd($sql);
					$cities = $this->query($sql)->result('array');
					// dd($cities);
					// $countries = [155];
					return function() use (&$cities, &$country_id){
						foreach($cities as $city_arr){
							
							$city_arr['checkouts'] = json_decode($city_arr['checkouts'], true);
							$city_arr['checkouts']['Provider'] = array_shift($city_arr['checkouts']['Providers']);
							unset($city_arr['checkouts']['Providers']);
							// dd($city_arr);
							$hotel_id = $city_arr['Id'];
							dump($this->TravelFuse_model->charterOfferList(array_replace($city_arr['checkouts'], [
								'Transport' => 'plane',
								'Adults' => [2],
								'Children' => [0],
								// 'ProductCode' => $city_arr['Id'],
							])));
							// dump($this->TravelFuse_model->charterOfferDetails(array_replace($city_arr['checkouts'], [
								// 'Transport' => 'plane',
								// 'Adults' => [2],
								// 'Children' => [0],
								// 'ProductCode' => $city_arr['Id'],
							// ])));
							dump($this->TravelFuse_model->api->request);
							dd($hotel_id);
							dd('iesit');
							
							list($city_id, $city_type) = $city_arr;
							dlog("Hotels for CityCode=$city_id DestinationType=$city_type CountryId=$country_id");
							$hotels = $this->TravelFuse_model->getHotels(['CityCode' => $city_id, 'DestinationType' => $city_type]);
							// $this->query("SELECT last_insert_id(0)");
							$this->query("UPDATE `tf_cities` SET city_hotels = NOW(), date_modified=date_modified WHERE `Id` = " . $this->db->escape($city_id) . " AND `type` = " . $this->db->escape($city_type) . " LIMIT 1");
							dlog("Found " . (!$hotels ? 0 : count($hotels)));
							yield array_map(function($hotel) use ($city_arr, &$country_id){
								return [
									'Id' => $hotel['Id'],
									'CountryId' => $country_id,
									'CityCode' => $city_arr[0],
									'DestinationType' => $city_arr[1],
								];
							}, $hotels);
						}
					};
				},
				'columns' => [
					'Id' => 'INT NOT NULL',
					'CityCode' => 'INT NOT NULL',
					'DestinationType' => 'VARCHAR(20) NOT NULL',
					'CountryId' => 'INT NOT NULL',
				],
				'import_columns' => [
				]
			],
			'city_hotels' => [
				'primary_key' => ['Id', 'CityCode', 'DestinationType'],
				// 'drop' => true,
				// 'noskip' => true,
				'sql_where_extra' => function(){
					global $city_hotels_country_id;
					return " AND c.CountryId = '" . (int)$city_hotels_country_id . "'";
				},
				'retriever' => function(){
					// return false;
					$this->query("SELECT last_insert_id(0)");
					$this->query("UPDATE `tf_countries` SET id=last_insert_id(id), city_hotels = NOW(), date_modified=date_modified ORDER BY city_hotels ASC, Id ASC LIMIT 1");
					$country_id = $this->db->insert_id();
					global $city_hotels_country_id;
					$city_hotels_country_id = $country_id;
					if(!$country_id){
						return false;
					}
					$cities = $this->query("SELECT Id as '0', type as '1' FROM `tf_cities` WHERE CountryId='" . (int)$country_id . "'")->result('array');
					// dd($cities);
					// $countries = [155];
					return function() use (&$cities, &$country_id){
						foreach($cities as $city_arr){
							list($city_id, $city_type) = $city_arr;
							dlog("Hotels for CityCode=$city_id DestinationType=$city_type CountryId=$country_id");
							$hotels = $this->TravelFuse_model->getHotels(['CityCode' => $city_id, 'DestinationType' => $city_type]);
							// $this->query("SELECT last_insert_id(0)");
							$this->query("UPDATE `tf_cities` SET city_hotels = NOW(), date_modified=date_modified WHERE `Id` = " . $this->db->escape($city_id) . " AND `type` = " . $this->db->escape($city_type) . " LIMIT 1");
							dlog("Found " . (!$hotels ? 0 : count($hotels)));
							yield array_map(function($hotel) use ($city_arr, &$country_id){
								return [
									'Id' => $hotel['Id'],
									'CountryId' => $country_id,
									'CityCode' => $city_arr[0],
									'DestinationType' => $city_arr[1],
								];
							}, $hotels);
						}
					};
				},
				'columns' => [
					'Id' => 'INT NOT NULL',
					'CityCode' => 'INT NOT NULL',
					'DestinationType' => 'VARCHAR(20) NOT NULL',
					'CountryId' => 'INT NOT NULL',
				],
				'import_columns' => [
				]
			],
			'hotels' => [
				'primary_key' => ['Id'],
				'keys' => ['Id', 'CountryId', 'CountyId', 'CountyName', 'CityId', 'CityName'],
				'real_keys' => ['Name', '_name_en', '_name_ro', 'Stars', '_stars', 'MainImage', 'status'],
				// 'drop' => true,
				// 'noskip' => true,
				'sql_where_extra' => function(){
					global $city_hotels_country_id;
					return " AND c.CountryId = '" . (int)$city_hotels_country_id . "'";
				},
				'afterfunction' => function(){
					global $city_hotels_country_id;
					$this->importFacilities($city_hotels_country_id);
					// $sql = "UPDATE `tf_hotel_facilities` hf JOIN `tf_hotel` h ON (h.Id = hf.Id) SET hf.`missing` = NOW(), `date_modified` = `date_modified` WHERE AND h.CountryId = '" . (int)$city_hotels_country_id . "'";
					// $this->db->query($sql);
					
				},
				'retriever' => function(){
					// return false;
					$this->query("SELECT last_insert_id(0)");
					$this->query("UPDATE `tf_countries` SET id=last_insert_id(id), hotels = NOW(), date_modified=date_modified WHERE city_hotels IS NOT NULL ORDER BY hotels ASC, Id ASC LIMIT 1"); // AND id='126' -- ROMANIA
					$country_id = $this->db->insert_id();
					global $city_hotels_country_id;
					$city_hotels_country_id = $country_id;
					// dd($country_id);
					if(!$country_id){
						return false;
					}
					$hotels = array_map(function($v){ return $v[0]; }, $this->query("SELECT DISTINCT cih.Id as '0' FROM `tf_cities` ci JOIN `tf_countries` cnt ON (ci.CountryId = cnt.Id) JOIN `tf_city_hotels` cih ON (cih.CityCode = ci.Id AND ci.type = cih.DestinationType AND cih.CountryId = cnt.Id) WHERE cnt.Id='" . (int)$country_id . "'")->result('array'));
					if(!$hotels){
						return false;
					}
					$hotels_chunks = array_chunk($hotels, 1000);
					return function() use (&$hotels_chunks, $country_id){
						foreach($hotels_chunks as $hotels){
							$hotel_details = $this->TravelFuse_model->getHotelsDetails(['HotelIds' => implode(',', $hotels)], 43200, false, 'array');
							// dd($hotel_details);
							yield !$hotel_details ? [] : array_map(function($hotel_detail) use ($country_id){
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
								return [
									'Id' => $hotel_detail['Id'],
									'CountryId' => $country_id,
									'CityId' => $hotel_detail['Address']['City']['Id'] ?? null,
									'CityName' => $hotel_detail['Address']['City']['Name'] ?? null,
									'CountyId' => $hotel_detail['Address']['City']['County']['Id'] ?? null,
									'CountyName' => $hotel_detail['Address']['City']['County']['Name'] ?? null,
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
							}, $hotel_details);
						}
					};
					
				},
				'columns' => [
					'Id' => 'INT NOT NULL',
					'Info' => 'LONGTEXT NULL',
					'CountryId' => 'INT NOT NULL',
					'CityId' => 'INT NULL',
					'CountyId' => 'INT NULL',
					'Name' => 'VARCHAR(255) NULL DEFAULT NULL',
					'CityName' => 'VARCHAR(255) NULL DEFAULT NULL',
					'CountyName' => 'VARCHAR(255) NULL DEFAULT NULL',
					'Stars' => 'TINYINT(1) NULL DEFAULT NULL',
					'WebAddress' => 'VARCHAR(255) NULL DEFAULT NULL',
					'MainImage' => 'VARCHAR(255) NULL DEFAULT NULL',
					'ShortContent' => 'TEXT NULL DEFAULT NULL',
					'Facilities' => 'LONGTEXT NULL DEFAULT NULL',
					'Recommended' => 'LONGTEXT NULL DEFAULT NULL',
					'Content' => 'LONGTEXT NULL DEFAULT NULL',
					'ImageGallery' => 'LONGTEXT NULL DEFAULT NULL',
					'Latitude' => 'DECIMAL(10,8) NULL DEFAULT NULL',
					'Longitude' => 'DECIMAL(11,8) NULL DEFAULT NULL',
				],
				'real_columns' => [
					'_stars' => 'TINYINT(1) NULL DEFAULT NULL',
					'status' => 'TINYINT(1) DEFAULT "1"',
					'_name_en' => 'VARCHAR(255) DEFAULT NULL',
					'_name_ro' => 'VARCHAR(255) DEFAULT NULL',
					'_web_address' => 'VARCHAR(255) NULL DEFAULT NULL',
					'_images' => 'LONGTEXT NULL DEFAULT NULL',
					'_short_content_ro' => 'TEXT NULL DEFAULT NULL',
					'_short_content_en' => 'TEXT NULL DEFAULT NULL',
					'_facilities' => 'LONGTEXT NULL DEFAULT NULL',
					// '_extracted_facilities' => 'LONGTEXT NULL DEFAULT NULL',
					'_content_ro' => 'LONGTEXT NULL DEFAULT NULL',
					'_content_en' => 'LONGTEXT NULL DEFAULT NULL',
					'_latitude' => 'DECIMAL(10,8) NULL DEFAULT NULL',
					'_longitude' => 'DECIMAL(11,8) NULL DEFAULT NULL',
				],
				'import_columns' => [
				]
			],
		];
		if(isset($_GET['only'])){
			if(is_array($_GET['only'])){
				$only = $_GET['only'];
			} else {
				$only = preg_split('/,/', $_GET['only']);
			}
			$tables = array_intersect_key($tables, array_flip($only));
		}
		foreach($tables as $table => $table_data){
			$table_prefix = $table_data['prefix'] ?? 'tf_';
			dlog("Importing " . $table);
			$results = $table_data['retriever']();
			if(false === $results) {
				dump("Skipping $table");
				continue;
			}
			
			$table_name = $table_prefix . $table;
			$drop = !empty($table_data['drop']);
			$noskip = $table_data['noskip'] ?? false;
			$sql_where_extra = $table_data['sql_where_extra'] ?? '';
			$columns = $table_data['columns'] ?? [];
			$primary_key = (array)($table_data['primary_key'] ?? key($columns));
			$column_names = array_keys($columns);
			if(!$noskip && !$column_names) {
				dump("Skipping $table No column names");
				continue;
			}
			
			$updateable_column_names = array_diff(array_keys($columns), $primary_key);
			$keys = $table_data['keys'] ?? (isset($primary_key[1]) ? array_keys($columns) : $updateable_column_names);
			
			if($drop){
				$this->query("DROP TABLE IF EXISTS `{$table_name}`");
			}
			
			$sql = "CREATE TABLE IF NOT EXISTS `{$table_name}` (
				" . implode('', array_map(function($column_name) use ($columns, $table_data){ return "`{$column_name}` " . ($columns[$column_name] ?? $table_data['real_columns'][$column_name]) . ','; }, array_merge($column_names, array_keys($table_data['real_columns'] ?? [])))) . "
				`missing` datetime DEFAULT NULL,
				`date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
				`date_modified` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
				" . ($primary_key ? "PRIMARY KEY (`" . implode("`,`", $primary_key) . "`)," : "") . "
				" . implode('', array_map(function($column_name){ return "KEY(`{$column_name}`),"; }, array_merge($keys, $table_data['real_keys'] ?? (array_keys($table_data['real_columns'] ?? []))))) . "
				KEY `missing` (`missing`),
				KEY `date_added` (`date_added`),
				KEY `date_modified` (`date_modified`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;";
			$this->query($sql);
			
			$table_name_import = "{$table_name}_import";
			
			$this->query("DROP TABLE IF EXISTS `{$table_name_import}`");
			$sql = "CREATE TABLE IF NOT EXISTS `{$table_name_import}` (
				" . implode('', array_map(function($column_name) use ($columns, $table_data){ return "`{$column_name}` " . ($columns[$column_name] ?? $table_data['import_columns'][$column_name]) . ','; }, array_merge($column_names, array_keys($table_data['import_columns'] ?? [])))) . "
				`date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
				" . ($primary_key ? "PRIMARY KEY (`" . implode("`,`", $primary_key) . "`)," : "") . "
				" . implode('', array_map(function($column_name){ return "KEY(`{$column_name}`),"; }, array_merge($keys, $table_data['import_keys'] ?? (array_keys($table_data['import_columns'] ?? []))))) . "
				KEY `date_added` (`date_added`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;";
			
			$this->query($sql);
			$insert_function = function($results, $column_names) use (&$table_name_import){
				if(empty($results)){
					return;
				}
				$sql = "INSERT INTO `{$table_name_import}` (" . implode(',', array_map(function($column_name){ return "`{$column_name}`"; }, $column_names)) . ") VALUES (" . implode("),(", array_map( function($item) use ($column_names){
					return implode(',', array_map(function($column_name) use ($item){
						return $this->db->escape(
							(isset($item[$column_name]) && (is_array($item[$column_name]) || is_object($item[$column_name]))) 
							? (empty($item[$column_name]) ? NULL : json_encode($item[$column_name], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) 
							: ($item[$column_name] ?? NULL)
						); }, $column_names));
				}, $results)) . ")";
				$this->query($sql);
			};
			$import_column_names = array_merge($column_names, array_keys($table_data['import_columns'] ?? []));
			if($results instanceOf Closure){
				$r = new ReflectionFunction($results);
				if($r->isGenerator()){
					// $test = 2;
					foreach($results() as $chunk) {
						$insert_function($chunk, $import_column_names);
						// dd($chunk);
						// if(!(--$test)){
							// dd($chunk);
						// }
					}
				} else {
					dd('Caz netratat');
				}
			} else {
				$insert_function($results, $import_column_names);
			}
			
			
			$this->query("INSERT INTO `{$table_name}` (" . implode(',', array_map(function($column_name){ return "`{$column_name}`"; }, $column_names)) . ") (SELECT " . implode(',', array_map(function($column_name){ return "`{$column_name}`"; }, $column_names)) . " FROM `{$table_name_import}`) ON DUPLICATE KEY UPDATE " . implode('', array_map(function($column_name){ return "`{$column_name}` = VALUES(`{$column_name}`),"; }, $updateable_column_names)) . "`missing` = NULL");
			if($primary_key){
				if($sql_where_extra instanceOf Closure){
					$sql_where_extra = $sql_where_extra();
				}
				$sql = "UPDATE `{$table_name}` c LEFT JOIN `{$table_name_import}` ci USING(`" . implode("`,`", $table_data['on_keys'] ?? $primary_key) . "`) SET `missing` = NOW(), `date_modified` = `date_modified` WHERE ci.{$primary_key[0]} IS NULL AND missing IS NULL $sql_where_extra";
				// dd($sql);
				$this->query($sql);
			}
			
			if(isset($table_data['afterfunction'])) $table_data['afterfunction']();
			$this->query("DROP TABLE IF EXISTS `{$table_name_import}`");
		}
		dlog('DONE');
		return;
		exit;
		
		$countries = $this->query("SELECT * FROM `{$table_name}`")->result();
		header('Content-Type: application/json');
		echo json_encode($countries);
		exit;
	}
	public function test($subview, $subflight='') {
		// echo '<pre>';
		// print_r($this->theme->theme_path . '/test/flight_data.json');
		// die;
		$this->data = array(
			'homeview' => 'test', 
			'subview' => 'flight_' . $subview, 
			'flight_data' => file_get_contents($this->theme->theme_path . '/test/flight_data' . $subflight . '.json'),
		);
		$this->theme->view('test', $this->data, $this);
	}
	public function index(...$a) {
		$a = implode('/', $a);
		/* if(preg_match('/book/', $a)){
			$this->testarehtml();
			die;
		} */
		$ip = '';
		
		$_CGET = $_GET;
		
		$referrer = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
		if($referrer){
			$rquery = parse_url($referrer, PHP_URL_QUERY);
			if($rquery){
				parse_str($rquery, $_GGET);
			}
		}
		
		
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		if(preg_match('/.js$/', $a)){
			header('Content-Type: application/javascript');
		} elseif(preg_match('/.json$/', $a)){
			header('Content-Type: application/json');
		} elseif(preg_match('/.ts$/', $a)){
			header('Content-Type: text/typescript');
		} elseif(preg_match('/.css$/', $a)){
			header('Content-Type: text/css');
		}
		$provizoriu = false;
		if($ip == '82.76.174.47'){
			$_GET['testtudor'] = 1;
			$provizoriu = true;
		}
		if($provizoriu){
			if(file_exists($this->theme->theme_path . 'views/provizoriu/' . $a . '.php')){
				include $this->theme->theme_path . 'views/provizoriu/' . $a . '.php';
				return;
			}
		}
		if(file_exists($this->theme->theme_path . 'views/' . $a . '.php')){
			include $this->theme->theme_path . 'views/' . $a . '.php';
		} else {
			header("HTTP/1.0 404 Not Found");
			exit;
		}
	}
}