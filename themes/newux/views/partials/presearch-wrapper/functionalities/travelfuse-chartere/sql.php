<?php
$sql = "
	FROM `tf_charter_checkout` co
	JOIN `tf_providers` pr ON (pr.Id = co.Provider)
	JOIN `tf_charter_checkin` ci ON (ci.Id = co.Id AND ci.Transport = co.Transport AND ci.CityCode = co.CityCode AND ci.DestinationType = co.DestinationType AND ci.Date = co.DepartureDate)
	JOIN `tf_charter_dep_cities` dc ON (dc.Id = co.Id AND dc.Transport = co.Transport AND dc.CityCode = co.CityCode AND dc.DestinationType = co.DestinationType)
	JOIN `tf_charter_cities` c ON (c.Id = dc.CityCode AND c.type = dc.DestinationType AND c.Transport = dc.Transport)
	JOIN `tf_cities` cit_dep ON (cit_dep.type = 'city' AND cit_dep.Id = dc.Id)
	JOIN `tf_countries` cnt_dep ON (cnt_dep.Id = cit_dep.CountryId)
	JOIN `tf_cities` cit_dest ON (cit_dest.type = c.type AND cit_dest.Id = c.Id)
	JOIN `tf_countries` cnt_dest ON (cnt_dest.Id = cit_dest.CountryId)
	WHERE co.missing IS NULL
		AND pr.missing IS NULL 
		AND pr.status = 1
		AND ci.missing IS NULL
		AND dc.missing IS NULL
		AND c.missing IS NULL
		AND cit_dep.missing IS NULL
		AND cit_dep.status = 1
		AND cit_dest.missing IS NULL
		AND cit_dest.city_hotels IS NOT NULL
		AND cit_dest.status = 1
		AND cnt_dep.missing IS NULL
		AND cnt_dep.status = 1
		AND cnt_dest.missing IS NULL
		AND cnt_dest.status = 1
		AND cnt_dest.city_hotels IS NOT NULL
		AND cnt_dest.hotels IS NOT NULL
		AND ci.date > NOW()
";

if($this->input->post('hotelId', null)){
	$sql .= " AND EXISTS(SELECT 1 FROM `tf_hotels` h WHERE h.Id=" . $this->db->escape($this->input->post('hotelId', null)) . " AND (CASE co.DestinationType WHEN 'city' THEN h.CityName = cit_dest.Name WHEN 'county' THEN h.CountyName = cit_dest.Name ELSE h.CountryId = co.CityCode END))";
}
if($this->input->post('Transport', null)){
	$transports = array_map([$this->db, 'escape'], (array)$this->input->post('Transport', null));
	$sql .= " AND co.Transport IN (" . implode(', ', $transports) . ")";
}
if($this->input->post('departureCity', null)){
	$sql .= " AND co.Id=" . $this->db->escape($this->input->post('departureCity', null));
}
if($this->input->post('destination', null)){
	$sql .= " AND co.CityCode=" . $this->db->escape($this->input->post('destination', null));
}
if($this->input->post('destinationType', null)){
	$sql .= " AND co.DestinationType=" . $this->db->escape($this->input->post('destinationType', null));
}
if($this->input->post('departureDate', null)){
	$sql .= " AND co.DepartureDate=" . $this->db->escape($this->input->post('departureDate', null));
}

return $sql;