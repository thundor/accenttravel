<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<script type="text/javascript">
var hotelF;
var facilities = function() {
  if(hotelF < 1) {
    var containerIcon = document.getElementById('iconsFacilitati');
    var paraZero = document.createElement("p");
    var textPara = document.createTextNode("Nu exista facilitati disponibile momentan");
    containerIcon.appendChild(paraZero);
    paraZero.appendChild(textPara);
  }	
  else {
    return hotelF;
  }
};
(function($){$(function() {
hotelF = document.getElementById('facilitatiHotelDesc').innerHTML.toUpperCase();
  
var facilitati = {
  internet : "WI-FI",
  wifi: "WIRELESS",
  babysitting: "BABYSITT",
  tv : "TV",
  tv1: "LED T",
  air : "AIR",
  roomService : "ROOM SERVICE",
  bar : "BAR",
  business : "BUSINESS CENT",
  conferenceRoom: "CONFERENCE ",
  sauna : "SAUNA",
  spa: "SPA",
  room: "ROOM SERVICE",					  
  hairDry: "HAIRDRY",
  masaj: "MASSAGE", 
  piscinaExt: "OUTDOOR POOL",
  piscinaInt: "INDOOR POOL",
  parcare: "PARKING",
  fitness: "FITNESS",
  fitness1: "HEALTH CLUB",
  cafenea: "COFFEE SHOP",
  restaurant: "RESTAURANT",
  ziare: "NEWSPAPER",
  telefon: "PHONE", 
  minibar: "MINIBAR",
  expressCheck: "EXPRESS CHECK", 
  terasa: "TERRACE", 
  loungeOut: "OUTSIDE LOUNGE",
  seif: "SAFE",
  receptie24: "24-HOUR",
  exchange: "EXCHANGE",
  laundry: "LAUNDRY",
  dush: "SHOWER",
  baie: "BATH",
  gradina: "GARDEN",
  expresor1: "NESPRESSON",
  expresor: "COFFEE MACHINE",
  noPets: "NO PETS",
  pets: "PET ALLOWED",
  noSmoking: "NO SMOKING",
  tenis: "TENNIS",
  golf: "GOLF",
  agentie: "TRAVEL AGENCY",
  jacuzzi:"JACUZZI",
  locjoaca: "PLAYGROUND",
}

function createtImg(src, textT, alt) {
  var imgF = new Image(40, 40);
  imgF.src = src;
  imgF.setAttribute('data-toggle', 'tooltip');
  imgF.setAttribute('title', textT);
  imgF.setAttribute('alt', alt);
  imgF.setAttribute('class', 'iconFac');
  document.getElementById('iconsFacilitati').appendChild(imgF); 							 
}


if(hotelF.indexOf(facilitati.bar) > -1) {
  img = "<?php echo $this->theme_url; ?>assets/images/icons/bar.png";
  createtImg(img, "BAR");					
}
if(hotelF.indexOf(facilitati.tv) > -1 || hotelF.indexOf(facilitati.tv1) > -1) {
  img = "<?php echo $this->theme_url; ?>assets/images/icons/tv-camera.png";
  createtImg(img, "LED TV");					
}
if(hotelF.indexOf(facilitati.babysitting) > -1) {
  img = "<?php echo $this->theme_url; ?>assets/images/icons/babysitting.png";
  createtImg(img, "Babysitting",  "Babysitting");					
}
if(hotelF.indexOf(facilitati.air) > -1) {
  img = "<?php echo $this->theme_url; ?>assets/images/icons/aer-conditionat.png";
  createtImg(img, "Aer Conditionat", "Aer Conditionat");					
}		
if(hotelF.indexOf(facilitati.roomService) > -1) {
  img = "<?php echo $this->theme_url; ?>assets/images/icons/room-service.png";
  createtImg(img, "Room Service", "Room Service");					
}	
if(hotelF.indexOf(facilitati.business) > -1) {
  img = "<?php echo $this->theme_url; ?>assets/images/icons/business-center.png";
  createtImg(img, "Business Center", "Business Center");					
}
if(hotelF.indexOf(facilitati.parcare) > -1) {
  img = "<?php echo $this->theme_url; ?>assets/images/icons/parcare.png";
  createtImg(img, "Parcare", "Parcare");					
}
if(hotelF.indexOf(facilitati.spa) > -1) {
  img = "<?php echo $this->theme_url; ?>assets/images/icons/spa.png";
  createtImg(img, "Centru SPA", "Centru SPA");					
}
if(hotelF.indexOf(facilitati.masaj) > -1) {
  img = "<?php echo $this->theme_url; ?>assets/images/icons/masaj.png";
  createtImg(img, "Servicii Masaj", "Servicii Masaj");					
}
if(hotelF.indexOf(facilitati.sauna) > -1) {
  img = "<?php echo $this->theme_url; ?>assets/images/icons/sauna.png";
  createtImg(img, "Sauna", "Sauna");					
}	
if(hotelF.indexOf(facilitati.fitness) > -1 || hotelF.indexOf(facilitati.fitness1) > -1) {
  img = "<?php echo $this->theme_url; ?>assets/images/icons/fitness.png";
  createtImg(img, "Sala Fitness", "Sala Fitness");					
}					
if(hotelF.indexOf(facilitati.cafenea) > -1) {
  img = "<?php echo $this->theme_url; ?>assets/images/icons/cafenea.png";
  createtImg(img, "Cafenea", "Cafenea");					
}
if(hotelF.indexOf(facilitati.restaurant) > -1) {
  img = "<?php echo $this->theme_url; ?>assets/images/icons/restaurant.png";
  createtImg(img, "Restaurant", "Restaurant");					
}	
if(hotelF.indexOf(facilitati.ziare) > -1) {
  img = "<?php echo $this->theme_url; ?>assets/images/icons/ziare.png";
  createtImg(img, "Ziare", "Ziare");					
}
if(hotelF.indexOf(facilitati.telefon) > -1) {
  img = "<?php echo $this->theme_url; ?>assets/images/icons/telefon.png";
  createtImg(img, "Telefon in Camera", "Telefon in Camera");					
}
if(hotelF.indexOf(facilitati.minibar) > -1) {
  img = "<?php echo $this->theme_url; ?>assets/images/icons/minibar.png";
  createtImg(img, "Minibar in Camera", "Minibar in Camera");					
} 
if(hotelF.indexOf(facilitati.hairDry) > -1) {
  img = "<?php echo $this->theme_url; ?>assets/images/icons/uscator-par.png";
  createtImg(img, "Uscator de Par", "Uscator de Par");					
}	
if(hotelF.indexOf(facilitati.expressCheck) > -1) {
  img = "<?php echo $this->theme_url; ?>assets/images/icons/zona-receptie.png";
  createtImg(img, "Express Check-In", "Express Check-In");					
}
if(hotelF.indexOf(facilitati.conferenceRoom) > -1) {
  img = "<?php echo $this->theme_url; ?>assets/images/icons/sala-conferinte.png";
  createtImg(img, "Sala de Conferinte", "Sala de Conferinte");					
}	
if(hotelF.indexOf(facilitati.wifi) > -1 || hotelF.indexOf(facilitati.internet) > -1) {
  img = "<?php echo $this->theme_url; ?>assets/images/icons/wifi.png";
  createtImg(img, "Internet Wireless", "Internet Wireless");					
}
if(hotelF.indexOf(facilitati.loungeOut) > -1 || hotelF.indexOf(facilitati.terrace) > -1) {
  img = "<?php echo $this->theme_url; ?>assets/images/icons/terasa.png";
  createtImg(img, "Terasa", "Terasa");					
}	
if(hotelF.indexOf(facilitati.piscinaExt) > -1) {
  img = "<?php echo $this->theme_url; ?>assets/images/icons/piscina-ext.png";
  createtImg(img, "Piscina Exterioara", "Piscina Exterioara");					
}
if(hotelF.indexOf(facilitati.piscinaInt) > -1) {
  img = "<?php echo $this->theme_url; ?>assets/images/icons/piscina-int.png";
  createtImg(img, "Piscina Interioara", "Piscina Interioara");					
}	
if(hotelF.indexOf(facilitati.seif) > -1) {
  img = "<?php echo $this->theme_url; ?>assets/images/icons/seif-hotel.png";
  createtImg(img, "Seif Hotel", "Seif Hotel");					
}				
if(hotelF.indexOf(facilitati.receptie24) > -1) {
  img = "<?php echo $this->theme_url; ?>assets/images/icons/receptie-non-stop.png";
  createtImg(img, "Receptie 24/7", "Receptie 24/7");					
}	
if(hotelF.indexOf(facilitati.lift) > -1) {
  img = "<?php echo $this->theme_url; ?>assets/images/icons/lift.png";
  createtImg(img, "Lift", "Lift");					
}	
if(hotelF.indexOf(facilitati.exchange) > -1) {
  img = "<?php echo $this->theme_url; ?>assets/images/icons/exchange.png";
  createtImg(img, "Schimb Valutar", "Schimb Valutar");					
}
if(hotelF.indexOf(facilitati.laundry) > -1) {
  img = "<?php echo $this->theme_url; ?>assets/images/icons/spalatorie.png";
  createtImg(img, "Spalatorie", "Spalatorie");					
}	
if(hotelF.indexOf(facilitati.dush) > -1) {
  img = "<?php echo $this->theme_url; ?>assets/images/icons/dush-camera.png";
  createtImg(img, "Dus", "Dus");					
}	
if(hotelF.indexOf(facilitati.baie) > -1) {
  img = "<?php echo $this->theme_url; ?>assets/images/icons/baie-camera.png";
  createtImg(img, "Baie Camera", "Baie Camera");					
}
if(hotelF.indexOf(facilitati.gradina) > -1) {
  img = "<?php echo $this->theme_url; ?>assets/images/icons/gradina.png";
  createtImg(img, "Gradina", "Gradina");					
}	
if(hotelF.indexOf(facilitati.expresor1) > -1 || hotelF.indexOf(facilitati.expresor) > -1) {
  img = "<?php echo $this->theme_url; ?>assets/images/icons/expresor-cafea.png";
  createtImg(img, "Expresor Cafea", "Expresor Cafea");					
}	
if(hotelF.indexOf(facilitati.agentie) > -1 ) {
  img = "<?php echo $this->theme_url; ?>assets/images/icons/agentie-turism.png";
  createtImg(img, "Agentie de Turism", "Agentie de Turism");					
}
if(hotelF.indexOf(facilitati.pets) > -1 ) {
  img = "<?php echo $this->theme_url; ?>assets/images/icons/cu-animale.png";
  createtImg(img, "Animale de Companie Permise", "Animale de Companie Permise");					
}
if(hotelF.indexOf(facilitati.noPets) > -1 ) {
  img = "<?php echo $this->theme_url; ?>assets/images/icons/fara-animale.png";
  createtImg(img, "Animale de Companie Permise", "Animale de Companie Permise");					
}
if(hotelF.indexOf(facilitati.noSmoking) > -1 ) {
  img = "<?php echo $this->theme_url; ?>assets/images/icons/non-smoking.png";
  createtImg(img, "Camere Nefumatori", "Camere Nefumatori");					
}
if(hotelF.indexOf(facilitati.calcat) > -1 ) {
  img = "<?php echo $this->theme_url; ?>assets/images/icons/serviciu-calcat.png";
  createtImg(img, "Serviciu de Calcat",  "Serviciu de Calcat");					
}
if(hotelF.indexOf(facilitati.tenis) > -1 ) {
  img = "<?php echo $this->theme_url; ?>assets/images/icons/teren-tenis.png";
  createtImg(img, "Teren tenis",  "Teren tenis");					
}
if(hotelF.indexOf(facilitati.golf) > -1 ) {
  img = "<?php echo $this->theme_url; ?>assets/images/icons/teren-golf.png";
  createtImg(img, "Teren golf",  "Teren golf");					
}
if(hotelF.indexOf(facilitati.jacuzzi) > -1 ) {
  img = "<?php echo $this->theme_url; ?>assets/images/icons/jacuzzi.png";
  createtImg(img, "Jacuzzi",  "Jacuzzi");					
}
if(hotelF.indexOf(facilitati.locjoaca) > -1 ) {
  img = "<?php echo $this->theme_url; ?>assets/images/icons/loc-de-joaca.png";
  createtImg(img, "Loc de Joaca Copii",  "Loc de Joaca Copii");					
}
})})(jQuery);
</script>
<?php themeFunctions::debugFileLine('end'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('start'); ?>