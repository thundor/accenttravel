<?php
ini_set('display_errors', 1);
$assets_dir = realpath(__DIR__ . '/../../../../../assets');
if(!empty($_GET['force_new']) || !is_file($assets_dir . '/trip-locations-air-hotel.json.gz')){
	$fp = fopen($assets_dir . '/trip-locations-air-hotel-partial.json', 'w');
	fwrite($fp, '[');
	$has_results = true;
	$limit = 10000;
	$page = 0;
	while($has_results){
		$offset = $limit * $page;
		$sql = "
			FROM `trip_countries` c
			JOIN `trip_cities` ci ON (ci.CountryId = c.Id)
			JOIN `trip_locations` l ON (l.CountryId = c.Id AND l.CityId = ci.Id)
			WHERE c.status = 1
			AND ci.status = 1
			AND l.status = 1
			AND c.missing is NULL
			AND ci.missing is NULL
			AND l.missing is NULL
			AND ci.SearchableOn regexp 'air'
			AND ci.SearchableOn regexp 'hotel'
			AND l.type = 'airport'
		";

		$sql = "SELECT l.Id, ci.Id CityId, ci.CountryId, COALESCE(l._name_ro, l._name_en, l.Name) Name, COALESCE(ci._name_ro, ci._name_en, ci.Name) City
			,COALESCE((SELECT COALESCE(c._name_ro, acn.name_RO, c._name_en, acn.name, c.Name) FROM trip_countries c LEFT JOIN ac_country acn ON (c.ISO = acn.iso_2) WHERE c.Id = ci.CountryId),c.Name) Country,
			CONCAT_WS('-', l.Name, c.ISO) alias
			"
			. $sql;

		// $sql .= " GROUP BY ci.Id ";
		$sql .= " ORDER BY Country ASC, City ASC, Name ASC";
		$sql .= " LIMIT $offset, $limit";
		$cities = $this->query($sql)->result('array');
		if($cities){
			$out = '';
			foreach($cities as $city_index => $city){
				if(!(!$page && !$city_index)){
					$out .= ',';
				}
				$out .= json_encode($city);
			}
			fwrite($fp, $out);
		}
		if(!$cities || !isset($cities[$limit -1])){
			$has_results = false; break;
		} else {
			$page ++;
		}
	}
	fwrite($fp, ']');
	fclose($fp);
	rename($assets_dir . '/trip-locations-air-hotel-partial.json', $assets_dir . '/trip-locations-air-hotel.json');
	gzCompressFile($assets_dir . '/trip-locations-air-hotel.json');
}

header("Content-Encoding: gzip");
$fp = fopen($assets_dir . '/trip-locations-air-hotel.json.gz', 'rb');
fpassthru($fp);
fclose($fp);
return;