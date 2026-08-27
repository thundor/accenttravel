<?php
/* .htaccess 
php_value auto_prepend_file "/data/www/www_accenttravel_ro/audit/system/lisal-audit.php"
*/
// CONTINENT FORMAT: ['EU']
$block_continents = ['AS','AF','OC','SA','AN', 'NA'];
/* 
EU - Europe
NA - North America
AS - Asia
AF - Africa
OC - Oceania
SA - South America
AN - Antarctica
*/

// COUNTRIES FORMAT: ['RO']
$block_countries = ['CN','EG','MX','GT','PH','AU','BR','RU','NG','CI','KH','TH','MY','AE','SG','IN','IQ','SO'];
$allow_countries = [];
/* 
AD - Andorra
AE - United Arab Emirates
AF - Afghanistan
AG - Antigua and Barbuda
AI - Anguilla
AL - Albania
AM - Armenia
AO - Angola
AR - Argentina
AS - American Samoa
AT - Austria
AU - Australia
AW - Aruba
AX - Åland Islands
AZ - Azerbaijan
BA - Bosnia and Herzegovina
BB - Barbados
BD - Bangladesh
BE - Belgium
BF - Burkina Faso
BG - Bulgaria
BH - Bahrain
BI - Burundi
BJ - Benin
BM - Bermuda
BN - Brunei
BO - Bolivia
BQ - Bonaire, Sint Eustatius, and Saba
BR - Brazil
BS - Bahamas
BT - Bhutan
BW - Botswana
BY - Belarus
BZ - Belize
CA - Canada
CD - DR Congo
CD - Democratic Republic of the Congo
CF - Central African Republic
CG - Congo Republic
CH - Switzerland
CI - Ivory Coast
CL - Chile
CM - Cameroon
CN - China
CO - Colombia
CR - Costa Rica
CU - Cuba
CV - Cabo Verde
CW - Curaçao
CY - Cyprus
CZ - Czechia
DE - Germany
DK - Denmark
DM - Dominica
DO - Dominican Republic
DZ - Algeria
EC - Ecuador
EE - Estonia
EG - Egypt
ER - Eritrea
ES - Spain
ET - Ethiopia
FI - Finland
FJ - Fiji
FO - Faroe Islands
FR - France
GA - Gabon
GB - United Kingdom
GE - Georgia
GG - Guernsey
GH - Ghana
GI - Gibraltar
GL - Greenland
GM - Gambia
GN - Guinea
GP - Guadeloupe
GQ - Equatorial Guinea
GR - Greece
GT - Guatemala
GU - Guam
GY - Guyana
HK - Hong Kong
HN - Honduras
HR - Croatia
HT - Haiti
HU - Hungary
ID - Indonesia
IE - Ireland
IL - Israel
IM - Isle of Man
IN - India
IQ - Iraq
IR - Iran
IS - Iceland
IT - Italy
JE - Jersey
JM - Jamaica
JO - Jordan
JP - Japan
KE - Kenya
KG - Kyrgyzstan
KH - Cambodia
KI - Kiribati
KM - Comoros
KN - St Kitts and Nevis
KR - South Korea
KW - Kuwait
KY - Cayman Islands
KZ - Kazakhstan
LA - Laos
LB - Lebanon
LC - Saint Lucia
LI - Liechtenstein
LK - Sri Lanka
LS - Lesotho
LT - Lithuania
LU - Luxembourg
LV - Latvia
LY - Libya
MA - Morocco
MC - Monaco
MD - Moldova
ME - Montenegro
MG - Madagascar
MK - North Macedonia
ML - Mali
MM - Myanmar
MN - Mongolia
MO - Macao
MP - Northern Mariana Islands
MQ - Martinique
MR - Mauritania
MT - Malta
MU - Mauritius
MV - Maldives
MW - Malawi
MX - Mexico
MY - Malaysia
MZ - Mozambique
NA - Namibia
NE - Niger
NG - Nigeria
NI - Nicaragua
NL - Netherlands
NL - The Netherlands
NO - Norway
NP - Nepal
NR - Nauru
NZ - New Zealand
OM - Oman
PA - Panama
PE - Peru
PF - French Polynesia
PG - Papua New Guinea
PH - Philippines
PK - Pakistan
PL - Poland
PM - Saint Pierre and Miquelon
PR - Puerto Rico
PS - Palestine
PT - Portugal
PY - Paraguay
QA - Qatar
RE - Réunion
RO - Romania
RS - Serbia
RU - Russia
RW - Rwanda
SA - Saudi Arabia
SC - Seychelles
SD - Sudan
SE - Sweden
SG - Singapore
SI - Slovenia
SK - Slovakia
SL - Sierra Leone
SM - San Marino
SN - Senegal
SO - Somalia
SR - Suriname
SS - South Sudan
SV - El Salvador
SX - Sint Maarten
SY - Syria
SZ - Eswatini
TC - Turks and Caicos Islands
TD - Chad
TF - French Southern Territories
TG - Togo
TH - Thailand
TJ - Tajikistan
TL - Timor-Leste
TM - Turkmenistan
TN - Tunisia
TO - Tonga
TR - Turkey
TR - Türkiye
TT - Trinidad and Tobago
TV - Tuvalu
TW - Taiwan
TZ - Tanzania
UA - Ukraine
UG - Uganda
US - United States
UY - Uruguay
UZ - Uzbekistan
VC - Saint Vincent and the Grenadines
VC - St Vincent and Grenadines
VE - Venezuela
VG - British Virgin Islands
VI - U.S. Virgin Islands
VN - Vietnam
XK - Kosovo
YE - Yemen
YT - Mayotte
ZA - South Africa
ZM - Zambia
ZW - Zimbabwe
*/

// CITIES FORMAT: ['RO' => ['Bucharest']]
$block_cities = [];
$allow_cities = [];

// ASN FORMAT: ['*' => ['123123'], 'continent' => ['EU' => ['2345234']], 'country' => ['RO' => ['2345234']]]
$block_asn = [
	'*' => [
		'53667', // PONYNET
		'5617', // Orange Polska Spolka Akcyjna
		'24940', // Hetzner Online GmbH
		'13238', // YANDEX LLC
		'57523', // Chang Way Technologies Co. Limited
		'14618', // Amazon.com, Inc.
		'200107', // Kaspersky Lab Switzerland GmbH
		'6939', // Hurricane Electric LLC
		'209366', // SEMrush CY LTD
		'16509', // Amazon.com, Inc.
		'23033', // Wowrack.com
		'216071', // Servers Tech Fzco
		'16276', // OVH SAS - not a spammer, but Frequently abused by spammers Requires case-by-case IP checking
		'200373', // 3xK Tech GmbH
		'203020', // HostRoyale Technologies Pvt Ltd
		'51765', // Oy Crea Nova Hosting Solution Ltd
		'35830', // Fast Servers (Pty) Ltd
		'26548', // PUREVOLTAGE-INC
	]
];
// SELECT GROUP_CONCAT(CONCAT(SUBSTRING(substring_index(name, ' ', 1),3,255), "', //", substring(name, 1 + CHAR_LENGTH(substring_index(name, ' ', 1))), " ") SEPARATOR ",'") FROM `oc_audit_as` WHERE block=0
$allow_asn = [
	'*' => [
		'32934', // Facebook, Inc.
		'54115', // Facebook Inc
		'396982', // Google LLC
		'15169', // Google LLC
		'36384', // Google LLC
		'16591', // Google Fiber Inc.
		'36492', // Google, LLC
		'395973', // Google LLC
		'36040', // Google LLC
		'394089', // Google LLC
		'36383', // Google LLC
	]
];

// ORG FORMAT: ['*' => ['zscaler switzerland gmbh'], 'continent' => ['EU' => ['zscaler switzerland gmbh']], 'country' => ['RO' => ['zscaler switzerland gmbh']]]
$block_org = [];
$allow_org = [
	'*' => [
		'FACEBOOK',
		'GOOGLE',
		'GOOGLE-CLOUD-PLATFORM',
		'MICROSOFT-CORP-MSN-AS-BLOCK',
	],
];

$log_access = true;

$ip_allowed = [];


if(($_SERVER['REMOTE_ADDR'] ?? '') == '82.76.174.47'){
	$log_access = true;
	// $block_continents[] = 'EU';
	// $block_countries[] = 'RO';
} else {
	// return;
}

$use_geoip_city = true;

if($log_access){
	$use_geoip_city = true;
}

$current_url = getCurrentUrlProxySafe();
$current_time = nowWithMilliseconds();

function getCurrentUrlProxySafe() {
    $https = $_SERVER['HTTPS'] ?? null;
    $xfp   = $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? null;

    $isHttps = filter_var($https, FILTER_VALIDATE_BOOLEAN)
             || strtolower($xfp) === 'https';

    $protocol = $isHttps ? 'https://' : 'http://';
    $host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $uri      = $_SERVER['REQUEST_URI'] ?? '/';

    return $protocol . $host . $uri;
}
function appendCsvRowsToAccessLog(string $filename, array $rows){
    if (empty($rows)) return false;

    // Open a memory stream
    $stream = fopen('php://temp', 'r+');

    foreach ($rows as $row) {
        if (!is_array($row)) continue;
        fputcsv($stream, $row);
    }

    rewind($stream);
    $csvContent = stream_get_contents($stream);
    fclose($stream);

    // Append CSV string to the file in one shot
    return file_put_contents($filename, $csvContent, FILE_APPEND | LOCK_EX) !== false;
}
function extractIpsFromHeader($headerValue) {
    $regex = '/
        (?:\b(?:\d{1,3}\.){3}\d{1,3}\b)                # IPv4
        |
        (?<![:.\w])\[?([a-f0-9:]{2,})\]?               # IPv6, optional []
    /ix';

    preg_match_all($regex, $headerValue, $matches);

    return array_unique($matches[0]);
}

function getIps($server = null, $first = false, $joined = true) {
    $server = $server ?? $_SERVER;
    $ips = [];

    foreach ([
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_FORWARDED',
        'HTTP_X_CLUSTER_CLIENT_IP',
        'HTTP_FORWARDED_FOR',
        'HTTP_FORWARDED',
        'REMOTE_ADDR'
    ] as $key) {
        if (!isset($server[$key])) continue;
		$can_have_multiple = in_array($key, ['HTTP_X_FORWARDED', 'HTTP_X_FORWARDED_FOR', 'HTTP_FORWARDED', 'HTTP_FORWARDED_FOR']);
		$key_ips = $can_have_multiple ? extractIpsFromHeader($server[$key]) : [$server[$key]];
        foreach ($key_ips as $ip) {
            $ip = trim($ip);
            $prefix = preg_replace('/(\w)[^_]*(_|$)/', '\1', $key);

            // Determine IP type
            if (!filter_var($ip, FILTER_VALIDATE_IP)) {
                $type = '!'; // invalid
            } elseif (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE)) {
                $type = '#'; // private
            } elseif (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE)) {
                $type = '~'; // reserved
            } elseif (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                $type = '4';
            } else {
                $type = '6';
            }

            $ips[$ip][$prefix] = $type;

            if ($first) break;
        }
        if ($first && !empty($ips)) break;
    }

    return $joined ? json_encode($ips, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : $ips;
}

function nowWithMilliseconds() {
    $micro = microtime(true);
    $microSeconds = sprintf("%06d", ($micro - floor($micro)) * 1000000);
    $date = new DateTime(date('Y-m-d H:i:s.' . $microSeconds, (int) $micro));
    return $date->format("Y-m-d H:i:s.v");
}

function blockClient(string $reason = 'Access Denied') {
    http_response_code(451);
    header('Content-Type: text/plain');
    echo "Blocked: $reason\n";
    exit;
}
function getRefererProxySafe(){
    $referer = $_SERVER['HTTP_REFERER'] ?? null;
    if (!$referer) {
        return null;
    }

    // Optional: Normalize HTTPS if behind proxy
    $xfp = $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? null;
    $proto = strtolower($xfp) === 'https' || (!empty($_SERVER['HTTPS']) && filter_var($_SERVER['HTTPS'], FILTER_VALIDATE_BOOLEAN))
        ? 'https' : 'http';

    // Optional: Override host if behind proxy
    $host = $_SERVER['HTTP_X_FORWARDED_HOST'] ?? $_SERVER['HTTP_HOST'] ?? 'localhost';

    // If referer is relative or internal, rebuild fully qualified URL
    if (strpos($referer, '/') === 0) {
        return "$proto://$host$referer";
    }

    return $referer;
}

// Get all IPs
$ips = getIps(null, false, false);
$access_hash = md5(json_encode($ips));
$browser_ip = null;
$browser_ip_ra = null;
// Check and act
$end_ips = [];
foreach ($ips as $ip => $sources) {
    foreach ($sources as $source => $type) {
        switch ($type) {
            case '!':
                blockClient("Invalid IP detected: $ip from $source");
				break;
            case '#':
				break;
            case '~':
				break;
			default:
				$end_ips[$ip][] = $source;
				$browser_ip = $browser_ip ?? $ip;
				if('RA' == $source){
					$browser_ip_ra = $ip;
				}
				break;
        }
        // Only act on the first usable IP
        break 2;
    }
}
if(!$browser_ip){
	if($_SERVER['REMOTE_ADDR'] !== $_SERVER['SERVER_ADDR']){
		blockClient("Blocked internal/reserved IP: " . $_SERVER['REMOTE_ADDR']);
	}
}
$end_blocked = false;
$access_rows = [];
if($log_access || $block_continents || $block_countries || $block_cities || $block_org || $block_asn){
	require_once(__DIR__ . '/library/GeoIP2-php-main/vendor/autoload.php');
	$targetDir = __DIR__ . '/geolite';
	$country_reader = null;
	if(!$use_geoip_city){
		if($block_continents || $block_countries){
			$country_reader = new GeoIp2\Database\Reader($targetDir . '/GeoLite2-Country.mmdb');
		}
	}
	
	$city_reader = null;
	if($block_cities || ($use_geoip_city && ($log_access || $block_continents || $block_countries || $allow_countries))){
		$city_reader = new GeoIp2\Database\Reader($targetDir . '/GeoLite2-City.mmdb');
	}
	
	$asn_reader = null;
	if($log_access || $block_asn || $block_org || $allow_asn || $allow_org){
		$asn_reader = new GeoIp2\Database\Reader($targetDir . '/GeoLite2-ASN.mmdb');
	}
	
	foreach($end_ips as $end_ip => $sources){
		$record = null;
		if(isset($country_reader) || ($use_geoip_city && isset($city_reader))){
			if($use_geoip_city){
				$record = $city_reader->city($end_ip);
			} else {
				$record = $country_reader->country($end_ip);
			}
			if($record){
				$continent_code = $record->continent->code ?? null;
				$blocked = false;
				if($continent_code && in_array($continent_code, $block_continents)){
					$blocked = "Continent not permitted: " . $continent_code;
				}
				if((!$blocked && $block_countries) || ($blocked && $allow_countries)){
					$country_code = $record->country->isoCode ?? null;
					if($country_code){
						if(in_array($country_code, $block_countries)){
							$blocked = "Country not permitted: " . $country_code;
						} elseif(in_array($country_code, $allow_countries)){
							$blocked = false;
						}
					}
				}
			} else {
				// @TODO WHAT TO DO WHEN COUNTRY IS NOT FOUND IN GeoIP DB?
			}
		}
		if(isset($city_reader)){
			if(!$use_geoip_city){
				$record = $city_reader->city($end_ip);
			}
			if($record && isset($record->city) && isset($record->country) && isset($record->country->code) && ((!$blocked && !empty($block_cities[$record->country->code])) || ($blocked && !empty($allow_cities[$record->country->code])))){
				$city_names = [];
				if(isset($record->city->names)){
					$city_names = array_values($record->city->names);
				} elseif(isset($record->city->name)){
					$city_names = [$record->city->name];
				}
				$city_names = array_map('mb_strtolower', $city_names);
				if(!empty(array_intersect($city_names, $block_cities[$record->country->code]))){
					$blocked = "City not permitted: " . $record->country->code . ' ' . ($record->country->name ?? $city_names[0]);
				} elseif($blocked && !empty($allow_cities[$record->country->code])){
					if(!empty(array_intersect($city_names, $allow_cities[$record->country->code]))){
						$blocked = false;
					}
				}
			}
		}
		$record_asn = null;
		if(isset($asn_reader)){
			$record_asn = $asn_reader->asn($end_ip);
			
			if($record_asn){
				$asn = $record_asn->autonomousSystemNumber ?? null;
				if($asn){
					$asn_block_reason = null;
					$asn_allowed = null;
					if((!$blocked && $block_asn && (
						($asn_block_reason = (isset($block_asn['*']) && in_array($asn, $block_asn['*'])) ? 1 : 0)
						|| ($asn_block_reason = ($record && ($record->continent->code ?? null) && !empty($block_asn['continent'][$record->continent->code]) && in_array($asn, $block_asn['continent'][$record->continent->code])) ? 2 : 0)
						|| ($asn_block_reason = ($record && ($record->country->isoCode ?? null) && !empty($block_asn['country'][$record->country->isoCode]) && in_array($asn, $block_asn['country'][$record->country->isoCode])) ? 3 : 0)
					)) || ($blocked && $allow_asn && ($asn_allowed = (isset($allow_asn['*']) && in_array($asn, $allow_asn['*']))
						|| ($record && ($record->continent->code ?? null) && !empty($allow_asn['continent'][$record->continent->code]) && in_array($asn, $allow_asn['continent'][$record->continent->code]))
						|| ($record && ($record->country->isoCode ?? null) && !empty($allow_asn['country'][$record->country->isoCode]) && in_array($asn, $allow_asn['country'][$record->country->isoCode]))))){
						if($asn_block_reason){
							$blocked = "ASN " . $asn . " not permitted" . ($asn_block_reason == 3 ? ' in your country (' . $record->country->isoCode . ')' : ($asn_block_reason == 2 ? ' in your continent (' . $record->continent->code . ')' : ' anywhere'));
						} elseif($asn_allowed){
							$blocked = false;
						}
					}
				}
				$org = $record_asn->autonomousSystemOrganization ?? null;
				if($org){
					$org = mb_strtolower($org);
					$org_block_reason = null;
					$org_allowed = null;
					if((!$blocked && $block_org && (
						($org_block_reason = (isset($block_org['*']) && in_array($org, $block_org['*'])) ? 1 : 0)
						|| ($org_block_reason = ($record && ($record->continent->code ?? null) && !empty($block_org['continent'][$record->continent->code]) && in_array($org, $block_org['continent'][$record->continent->code])) ? 2 : 0)
						|| ($org_block_reason = ($record && ($record->country->isoCode ?? null) && !empty($block_org['country'][$record->country->isoCode]) && in_array($org, $block_org['country'][$record->country->isoCode])) ? 3 : 0)
					)) || ($blocked && $allow_org && ($org_allowed = (isset($allow_org['*']) && in_array($org, $allow_org['*']))
						|| ($record && ($record->continent->code ?? null) && !empty($allow_org['continent'][$record->continent->code]) && in_array($org, $allow_org['continent'][$record->continent->code]))
						|| ($record && ($record->country->isoCode ?? null) && !empty($allow_org['country'][$record->country->isoCode]) && in_array($org, $allow_org['country'][$record->country->isoCode]))))){
						if($org_block_reason){
							$blocked = "Organization " . $org . " not permitted" . ($org_block_reason == 3 ? ' in your country (' . $record->country->isoCode . ')' : ($org_block_reason == 2 ? ' in your continent (' . $record->continent->code . ')' : ' anywhere'));
						} elseif($org_allowed){
							$blocked = false;
						}
					}
				}
			}
		}
		// if(($_SERVER['REMOTE_ADDR'] ?? '') == '82.76.174.47'){
			// echo '<pre>';
			// print_r($record_asn);
			// print_r($record);
			// die;
		// }
		$access_rows[] = [$current_time, $access_hash, $end_ip, $record->traits->network ?? '', $record_asn->autonomousSystemNumber ?? '', $record_asn->autonomousSystemOrganization ?? '', $record->continent->code ?? '', $record->continent->name ?? '', $record->country->isoCode ?? '', $record->country->name ?? '', $record->city->name ?? '', ($blocked ? 'blocked' : ''), ($blocked ? $blocked : ''), $_SERVER['HTTP_USER_AGENT'] ?? '', $_SERVER['REQUEST_METHOD'] ?? '', $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '', $current_url, $_SERVER['HTTP_REFERER'] ?? '', getRefererProxySafe()];
		if($blocked && !$end_blocked){
			$end_blocked = $blocked;
		}
	}
}

if($log_access){
	$preg_logs = [
		__DIR__ . '/logs/lisal-audit-allowed' . '-' . date('Y-m-d', strtotime('yesterday')) . '.log',
		__DIR__ . '/logs/lisal-audit-blocked' . '-' . date('Y-m-d', strtotime('yesterday')) . '.log',
	];
	foreach($preg_logs as $preg_log){
		if(is_file($preg_log)){
			$new_filename = dirname($preg_log) . '/' . basename($preg_log, '.log') . '.csv';
			@rename($preg_log, $new_filename);
			
			$gz = gzencode(	'current_time,access_hash,end_ip,network,autonomous_system_number,autonomous_system_organization,continent_code,continent_name,country_iso_code,country_name,city_name,blocked_status,blocked_reason,user_agent,request_method,requested_with,current_url,http_referer,proxy_safe_referer' .
				PHP_EOL .
				file_get_contents($new_filename)
			);
			file_put_contents($new_filename . '.gz', $gz);
			
			if(is_file($new_filename . '.gz')){
				@unlink($new_filename);
			}
		}
	}
	appendCsvRowsToAccessLog(__DIR__ . '/logs/lisal-audit-' . ($end_blocked ? 'blocked' : 'allowed') . '-' . date('Y-m-d') . '.log', $access_rows);
}
if($end_blocked){
	blockClient($blocked);
}

// getIps(null, false, true)
// file_put_contents(__DIR__ . '/lisal-audit.log', nowWithMilliseconds() . ' ' . json_encode($_SERVER, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ': ' . getCurrentUrlProxySafe() . "\n", FILE_APPEND);