// JavaScript Document
(function(){
$(document).tooltip({
      position: {
        my: "center bottom-20",
        at: "center top",
        using: function( position, feedback ) {
          $( this ).css( position );
          $( "<div>" )
            .addClass( "arrow" )
            .addClass( feedback.vertical )
            .addClass( feedback.horizontal )
            .appendTo( this );
        }
      }
    });

  
  $( function() {
    $( "#slider-range" ).slider({
      range: true,
      min: 150,
      max: 2500,
      values: [ 175, 1300 ],
      slide: function( event, ui ) {
        $( "#amount" ).val( "€ " + ui.values[ 0 ] + " - € " + ui.values[ 1 ] );
      }
    
	 });
	
	
	$( function() {
    $( "#amount" ).val( "€ " + $( "#slider-range" ).slider( "values", 0 ) +
      " - € " + $( "#slider-range" ).slider( "values", 1 ) );
  		});
  
	  $(".inchideH").on("click", function(){
    	$(this).parents(".boxHotel, .boxTicket").hide("slow");
		}); 

$(".hartaHotel").on("click", function(){
	$('#modalMapH').show();
	});
$("#modalMapH .btn").on("click", function(){
	$('#modalMapH').hide();
	});
	
$(".mapInfo").on("click", function(){
	$('#modalMapCity').show();
	});
$("#modalMapCity .btn").on("click", function(){
	$('#modalMapCity').hide();
	});
	
	$("#hartaBalcescu").on("click", function(){
	$('#showMapBalcescu').show("fade");
	});
	
	$("#showMapBalcescu i.fa-close").on("click", function(){
	$('#showMapBalcescu').hide();
	});		
	
	
 });
 
 var printeaza = document.getElementById('printBook').onclick = function() {
	 		window.print();
	 };

var sendEmail = document.getElementById('emailBook').onclick = function() {
	window.open('mailto:test@example.com');
	
	};	 
	 
 	
	})();