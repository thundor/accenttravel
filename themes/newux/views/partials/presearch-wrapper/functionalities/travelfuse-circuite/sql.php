<?php
$sql = "
	FROM `tf_tour_checkin` ci
	JOIN `tf_tour_dep_cities` dc ON (dc.Id = ci.Id AND dc.Transport = ci.Transport)
	JOIN `tf_tour_cities` c ON (c.Id = ci.CityCode AND c.type = ci.DestinationType AND c.Transport = dc.Transport)
	JOIN `tf_cities` cit_dep ON (cit_dep.type = 'city' AND cit_dep.Id = dc.Id)
	JOIN `tf_countries` cnt_dep ON (cnt_dep.Id = cit_dep.CountryId)
	LEFT JOIN `tf_countries` cnt_dest ON (c.type = 'country' AND cnt_dest.Id = c.Id)
	LEFT JOIN `tf_destinations` dest ON (c.type = 'destination' AND dest.Id = c.Id)
	WHERE ci.missing IS NULL
		AND dc.missing IS NULL
		AND c.missing IS NULL
		AND cit_dep.missing IS NULL
		AND cit_dep.status = 1
		AND cnt_dep.missing IS NULL
		AND cnt_dep.status = 1
		AND IF(cnt_dest.Id IS NULL, TRUE, cnt_dest.status = 1)
		AND IF(dest.Id IS NULL, TRUE, dest.status = 1)
		AND ci.date > NOW()
";

if($this->input->post('Transport', null)){
	$sql .= " AND ci.Transport=" . $this->db->escape($this->input->post('Transport', null));
}
if($this->input->post('departureCity', null)){
	$sql .= " AND ci.Id=" . $this->db->escape($this->input->post('departureCity', null));
}
if($this->input->post('destination', null)){
	$sql .= " AND ci.CityCode=" . $this->db->escape($this->input->post('destination', null));
}
if($this->input->post('destinationType', null)){
	$sql .= " AND ci.DestinationType=" . $this->db->escape($this->input->post('destinationType', null));
}
if($this->input->post('departureDate', null)){
	$sql .= " AND ci.DepartureDate=" . $this->db->escape($this->input->post('departureDate', null));
}

return $sql;