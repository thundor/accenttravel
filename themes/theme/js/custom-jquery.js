// JavaScript Document Opened
$(document).ready(function(){

	
	// optiuni login area			
	$( "#contulMeu" ).on("click", function(e) {
  			$( "#contOpt" ).toggle();
			$( "#utileVacanta" ).hide();
			});

	// login modal	
    $("#login").on("click", function(e) {
			$("#loginWindow").show();
			$( "#contOpt" ).hide();
	});
	
	$("#loginClose").on("click", function(e) {
			$("#loginWindow").hide();
			$( "#contOpt" ).hide();
	});
	
	// servicii utile top area
	$("#servUtile").on("click", function(e) {
  			$( "#utileVacanta" ).toggle();
			$( "#contOpt" ).hide();
			});
	
	
	$("#regClose").on("click", function(e) {
			$("#regWindow").hide();
			$( "#contOpt" ).hide();
	});
	 
	 
	 // fereastra modala inregistrare cont
    $( "#register" ).on("click", function(e) {
		$("#regWindow").show();
	 });
	 
	 $( "#register2" ).on("click", function(e) {
		$("#regWindow").show();
		$("#loginWindow").hide();
	 });
	 
 
	// calendar Hotel
	
	
  	$("#dateHotel").caleran({
            startEmpty: true,
			showFooter: false,
			autoCloseOnSelect: true,
			
        });
		
		
	// calendar Bilete Avion
  	$("#dateZborAvion").caleran({
            startEmpty: true,
			showFooter: false,
			autoCloseOnSelect: true
        });
		
	// calendar City Break
  	$("#dateZborCB").caleran({
            startEmpty: true,
			showFooter: false,
			autoCloseOnSelect: true
        });	
		
	// calendar City Break
  	$("#datePax").caleran({
            startEmpty: true,
			showFooter: false,
			autoCloseOnSelect: true
        });	
		
		$("#datePax1").caleran({
            startEmpty: true,
			showFooter: false,
			autoCloseOnSelect: true
        });	
		
		$("#dataCircuit").caleran({
            startEmpty: true,
			showFooter: false,
			autoCloseOnSelect: true
        });		
		
		
// adaugare varste copii motor de cautare: HOTEL
			
	$("#copiiCam1").on("change", function(){
        $(this).find("option:selected").each(function(){
            
			var optionValue = $(this).attr("value");
           
		    if (optionValue == 2){
                $("#cam1Hotel .varsteCopii").show();
				$( "#varstaCop1Cam1").show();
				$("#varstaCop2Cam1").hide();
				$("#cam1Hotel .varsteCopii p#v1").show();
				$("#cam1Hotel .varsteCopii p#v2").hide();
				
            } 
			if (optionValue == 3){
				 $("#cam1Hotel .varsteCopii").show();
				$("#varstaCop2Cam1").show();
				$("#varstaCop1Cam1").show();
				$("#cam1Hotel .varsteCopii p#v1").show();
				$("#cam1Hotel .varsteCopii p#v2").show();
            } 
			 if (optionValue == 1){
				 $("#cam1Hotel .varsteCopii").hide();
				$( "#varstaCop1Cam1").hide();
				$("#varstaCop2Cam1").hide();
				$("#cam1Hotel .varsteCopii p#v1").hide();
				$("#cam1Hotel .varsteCopii p#v2").hide();
				 }
        });
    });

//adaugare & stergere camera 2 hotel

	$("#addCam2").on("click", function() {
		$("#cam2Hotel").show();
		$("#addCam2").hide();
		$("#addCam3").show();
		$("#remCam3").show();
		});
		
	$("#remCam2").on("click", function() {
		$("#cam2Hotel").hide();
		$("#addCam2").show();
		$("#cam2Hotel .varsteCopii").hide();
		$('#copiiCam2 option').prop('selected', function() {
        return this.defaultSelected;	});
		});

	$("#copiiCam2").on("change", function(){
        $(this).find("option:selected").each(function(){
            var optionValue = $(this).attr("value");
            if (optionValue == 2){
                $("#cam2Hotel .varsteCopii").show();
				$( "#varstaCop1Cam2").show();
				$("#varstaCop2Cam2").hide();
				$("#cam2Hotel .varsteCopii p#v1C2").show();
				$("#cam2Hotel .varsteCopii p#v2C2").hide();				
            } 
			
			if (optionValue == 3){
				 $("#cam2Hotel .varsteCopii").show();
				$("#varstaCop2Cam2").show();
				$("#varstaCop1Cam2").show();
				$("#cam2Hotel .varsteCopii p#v1C2").show();
				$("#cam2Hotel .varsteCopii p#v2C2").show();
            } 
			
			 if (optionValue == 1){
				 $("#cam2Hotel .varsteCopii").hide();
				$( "#varstaCop1Cam2").hide();
				$("#varstaCop2Cam2").hide();
				$("#cam2Hotel .varsteCopii p#v1C2").hide();
				$("#cam2Hotel .varsteCopii p#v2C2").hide();
				 }
        });
    });
	
	
	//adaugare & stergere camera 3 hotel

	$("#addCam3").on("click", function() {
		$("#cam3Hotel").show();
		$("#addCam3").hide();
		$("#remCam2").hide();
		});
		
	$("#remCam3").on("click", function() {
		$("#cam3Hotel").hide();
		$("#addCam3").show();
		$("#remCam2").show();
		$("#cam3Hotel .varsteCopii").hide();
		$('#copiiCam3 option').prop('selected', function() {
        return this.defaultSelected;	});
		});

	$("#copiiCam3").on("change", function(){
        $(this).find("option:selected").each(function(){
            var optionValue = $(this).attr("value");
            if (optionValue == 2){
                $("#cam3Hotel .varsteCopii").show();
				$( "#varstaCop1Cam3").show();
				$("#varstaCop2Cam3").hide();
				$("#cam3Hotel .varsteCopii p#v1C3").show();
				$("#cam3Hotel .varsteCopii p#v2C3").hide();
            } 
			if (optionValue == 3){
				 $("#cam3Hotel .varsteCopii").show();
				$("#varstaCop2Cam3").show();
				$("#varstaCop1Cam3").show();
				$("#cam3Hotel .varsteCopii p#v1C3").show();
				$("#cam3Hotel .varsteCopii p#v2C3").show();
            } 
			 if (optionValue == 1){
				 $("#cam3Hotel .varsteCopii").hide();
				$( "#varstaCop1Cam3").hide();
				$("#varstaCop2Cam3").hide();
				$("#cam3Hotel .varsteCopii p#v1C3").hide();
				$("#cam3Hotel .varsteCopii p#v2C3").hide();
				 }
        });
    });
	
$("#addAvionHotel").on("change", function(){
		$("#inpZborHot").toggle();
	});	
	
// varste copii rezervare Bilete Avion

$("#copiiZbor").on("change", function(){
        $(this).find("option:selected").each(function(){
            var optionValue = $(this).attr("value");
            if (optionValue == 2){
                $("#copiiZborArea .varsteCopii").show();
				$("#aniCop1Zbor").show();
				$("#aniCop2Zbor").hide();
				$("#copiiZborArea .varsteCopii p#v1Z").show();
				$("#copiiZborArea .varsteCopii p#v2Z").hide();
            } 
			
			if (optionValue == 3){
				 $("#copiiZborArea .varsteCopii").show();
				$("#aniCop2Zbor").show();
				$("#aniCop1Zbor").show();
				$("#copiiZborArea .varsteCopii p#v1Z").show();
				$("#copiiZborArea .varsteCopii p#v2Z").show();
            } 
			
			 if (optionValue == 1){
				 $("#copiiZborArea .varsteCopii").hide();
				$( "#aniCop2Zbor").hide();
				$("#aniCop1Zbor").hide();
				$("#copiiZborArea .varsteCopii p#v1Z").hide();
				$("#copiiZborArea .varsteCopii p#v2Z").hide();
				 }
        });
    });
	
	// adaugare varste copii motor de cautare: City Break (CB)
			
	$("#copiiCam1CB").on("change", function(e){
        $(this).find("option:selected").each(function(e){
            var optionValue = $(this).attr("value");
            if (optionValue == 2){
                $("#cam1CB .varsteCopii").show();
				$( "#varstaCop1Cam1CB").show();
				$("#varstaCop2Cam1CB").hide();
				$("#cam1CB .varsteCopii p#v1CB1").show();
				$("#cam1CB .varsteCopii p#v2CB1").hide();
				} 
			if (optionValue == 3){
				 $("#cam1CB .varsteCopii").show();
				$("#varstaCop2Cam1CB").show();
				$("#varstaCop1Cam1CB").show();
				$("#cam1CB .varsteCopii p#v1CB1").show();
				$("#cam1CB .varsteCopii p#v2CB1").show();
            } 
			 if (optionValue == 1){
				 $("#cam1CB .varsteCopii").hide();
				$( "#varstaCop1Cam1CB").hide();
				$("#varstaCop2Cam1CB").hide();
				$("#cam1CB .varsteCopii p#v1CB1").hide();
				$("#cam1CB .varsteCopii p#v2CB1").hide();
				 }
        });
    });

//adaugare & stergere camera 2 CB

	$("#addCam2CB").on("click", function(e) {
		$("#cam2CB").show();
		$("#addCam2CB").hide();
		});
	$("#remCam2CB").on("click", function(e) {
		$("#cam2CB").hide();
		$("#addCam2CB").show();
		$("#cam2CB .varsteCopii").hide();
		$('#copiiCam2CB option').prop('selected', function() {
        return this.defaultSelected;	});
		});

	$("#copiiCam2CB").on("change", function(e){
        $(this).find("option:selected").each(function(){
            var optionValue = $(this).attr("value");
            if (optionValue == 2){
                $("#cam2CB .varsteCopii").show();
				$( "#varstaCop1Cam2CB").show();
				$("#varstaCop2Cam2CB").hide();
				$("#cam2CB .varsteCopii p#v1CB2").show();
				$("#cam2CB .varsteCopii p#v2CB2").hide();
            } 
			
			if (optionValue == 3){
				 $("#cam2CB .varsteCopii").show();
				$("#varstaCop1Cam2CB").show();
				$("#varstaCop2Cam2CB").show();
				$("#cam2CB .varsteCopii p#v1CB2").show();
				$("#cam2CB .varsteCopii p#v2CB2").show();
            } 
			
			 if (optionValue == 1){
				 $("#cam2CB .varsteCopii").hide();
				$("#varstaCop1Cam2CB").hide();
				$("#varstaCop2Cam2CB").hide();
				$("#cam2CB .varsteCopii p#v1CB2").hide();
				$("#cam2CB .varsteCopii p#v2CB2").hide();
				 }
        });
    });
	
	
	//adaugare & stergere camera 3 CB

	$("#addCam3CB").on("click", function(e) {
		$("#cam3CB").show();
		$("#remCam2CB").hide();
		$("#addCam2CB").hide();
		$("#addCam3CB").hide();
		});
		
	$("#remCam3CB").on("click", function(e) {
		$("#cam3CB").hide();
		$("#remCam2CB").show();
		$("#addCam3CB").show();
		$("#cam3CB .varsteCopii").hide();
		$('#copiiCam3CB option').prop('selected', function() {
        return this.defaultSelected;
    });
		});

	$("#copiiCam3CB").on("change", function(e){
        $(this).find("option:selected").each(function(){
            var optionValue = $(this).attr("value");
            if (optionValue == 2){
                $("#cam3CB .varsteCopii").show();
				$( "#varstaCop1Cam3CB").show();
				$("#varstaCop2Cam3CB").hide();
				$("#cam3CB .varsteCopii p#v1CB3").show();
				$("#cam3CB .varsteCopii p#v2CB3").hide();
				
            } 
			if (optionValue == 3){
				 $("#cam3CB .varsteCopii").show();
				$("#varstaCop2Cam3CB").show();
				$("#varstaCop1Cam3CB").show();
				$("#cam3CB .varsteCopii p#v1CB3").show();
				$("#cam3CB .varsteCopii p#v2CB3").show();
            } 
			 if (optionValue == 1){
				 $("#cam3Hotel .varsteCopii").hide();
				$( "#varstaCop1Cam3CB").hide();
				$("#varstaCop2Cam3CB").hide();
				$("#cam3CB .varsteCopii p#v1CB3").hide();
				$("#cam3CB .varsteCopii p#v2CB3").hide();
				 }
        });
    });
	
//adaugare & stergere copii camera si pachete

	
	$("#copiiCam1Pax").on("change", function(e){
        $(this).find("option:selected").each(function(){
            var optionValue = $(this).attr("value");
            if (optionValue == 2){
                $("#cam1Pax .varsteCopii").show();
				$( "#varstaCop1Cam1Pax").show();
				$("#varstaCop2Cam1Pax").hide();
				$("#cam1Pax .varsteCopii p#v1Pax1").show();
				$("#cam1Pax .varsteCopii p#v2Pax1").hide();
			}
			
			if (optionValue == 3){
				 $("#cam1Pax .varsteCopii").show();
				$("#varstaCop2Cam1Pax").show();
				$("#varstaCop1Cam1Pax").show();
				$("#cam1Pax .varsteCopii p#v1Pax1").show();
				$("#cam1Pax .varsteCopii p#v2Pax1").show();
            } 
			 if (optionValue == 1){
				 $("#cam1Pax .varsteCopii").hide();
				$( "#varstaCop1Cam1Pax").hide();
				$("#varstaCop2Cam1Pax").hide();
				$("#cam1Pax .varsteCopii p#v1Pax1").hide();
				$("#cam1Pax .varsteCopii p#v2Pax1").hide();
				 }
        });
    });

//adaugare & stergere camera 2 pachete

	$("#addCam2Pax").on("click", function(e) {
		$("#cam2Pax").show();
		$("#addCam2Pax").hide();
		});
		
	$("#remCam2Pax").on("click", function() {
		$("#cam2Pax").hide();
		$("#addCam2Pax").show();
		   $("#cam2Pax .varsteCopii").hide();
		$('#copiiCam2Pax option').prop('selected', function() {
        return this.defaultSelected;	});
		});

	$("#copiiCam2Pax").on("change", function(e){
        $(this).find("option:selected").each(function(){
            var optionValue = $(this).attr("value");
            if (optionValue == 2){
                $("#cam2Pax .varsteCopii").show();
				$( "#varstaCop1Cam2Pax").show();
				$("#varstaCop2Cam2Pax").hide();
				$("#cam2Pax .varsteCopii p#v1Pax2").show();
				$("#cam2Pax .varsteCopii p#v2Pax2").hide();
				
            } 
			if (optionValue == 3){
				 $("#cam2Pax .varsteCopii").show();
				$("#varstaCop2Cam2Pax").show();
				$("#varstaCop1Cam2Pax").show();
				$("#cam2Pax .varsteCopii p#v1Pax2").show();
				$("#cam2Pax .varsteCopii p#v2Pax2").show();
            } 
			 if (optionValue == 1){
				 $("#cam2Pax .varsteCopii").hide();
				$( "#varstaCop1Cam2Pax").hide();
				$("#varstaCop2Cam2Pax").hide();
				$("#cam2Pax .varsteCopii p#v1Pax2").hide();
				$("#cam2Pax .varsteCopii p#v2Pax2").hide();
				 }
        });
    });
	
	
	//adaugare & stergere camera 3 pachete

	$("#addCam3Pax").on("click", function() {
		$("#cam3Pax").show();
		$("#remCam2Pax").hide();
		$("#addCam3Pax").hide();
		});
	$("#remCam3Pax").on("click", function() {
		$("#cam3Pax").hide();
		$("#remCam2Pax").show();
		$("#addCam3Pax").show();
		$("#cam3Pax .varsteCopii").hide();
		$('#copiiCam3Pax option').prop('selected', function() {
        return this.defaultSelected;	});
		});

	$("#copiiCam3Pax").on("change", function(e){
        $(this).find("option:selected").each(function(){
            var optionValue = $(this).attr("value");
            if (optionValue == 2){
                $("#cam3Pax .varsteCopii").show();
				$("#varstaCop1Cam3Pax").show();
				$("#varstaCop2Cam3Pax").hide();
				$("#cam3Pax .varsteCopii p#v1Pax3").show();
				$("#cam3Pax .varsteCopii p#v2Pax3").hide();
				
            } 
			if (optionValue == 3){
				 $("#cam3Pax .varsteCopii").show();
				$("#varstaCop2Cam3Pax").show();
				$("#varstaCop1Cam3Pax").show();
				$("#cam3Pax .varsteCopii p#v1Pax3").show();
				$("#cam3Pax .varsteCopii p#v2Pax3").show();
            } 
			 if (optionValue == 1){
				 $("#cam3Pax .varsteCopii").hide();
				$( "#varstaCop1Cam3Pax").hide();
				$("#varstaCop2Cam3Pax").hide();
				$("#cam3Pax .varsteCopii p#v1Pax3").hide();
				$("#cam3Pax .varsteCopii p#v2Pax3").hide();
				 }
        });
    });
	
//oferte pachete strainatate
	$("#copiiCam1Pax1").on("change", function(e){
        $(this).find("option:selected").each(function(){
            var optionValue = $(this).attr("value");
            if (optionValue == 2){
                $("#cam1Pax1 .varsteCopii").show();
				$( "#varstaCop1Cam1Pax1").show();
				$("#varstaCop2Cam1Pax1").hide();
				$("#cam1Pax1 .varsteCopii p#v1Pax11").show();
				$("#cam1Pax1 .varsteCopii p#v2Pax11").hide();
				
            } 
			if (optionValue == 3){
				 $("#cam1Pax1 .varsteCopii").show();
				$("#varstaCop2Cam1Pax1").show();
				$("#varstaCop1Cam1Pax1").show();
				$("#cam1Pax1 .varsteCopii p#v1Pax11").show();
				$("#cam1Pax1 .varsteCopii p#v2Pax11").show();
            } 
			 if (optionValue == 1){
				 $("#cam1Pax1 .varsteCopii").hide();
				$( "#varstaCop1Cam1Pax1").hide();
				$("#varstaCop2Cam1Pax1").hide();
				$("#cam1Pax1 .varsteCopii p#v1Pax11").hide();
				$("#cam1Pax1 .varsteCopii p#v2Pax11").hide();
				 }
        });
    });

//adaugare & stergere camera 2 pachete

	$("#addCam2Pax1").on("click", function(e) {
		$("#cam2Pax1").show();
		$("#addCam2Pax1").hide();
		});
	$("#remCam2Pax1").on("click", function() {
		$("#cam2Pax1").hide();
		$("#addCam2Pax1").show();
		$("#cam2Pax1 .varsteCopii").hide();
		$('#copiiCam2Pax1 option').prop('selected', function() {
        return this.defaultSelected;	});
		});

	$("#copiiCam2Pax1").on("change", function(e){
        $(this).find("option:selected").each(function(){
            var optionValue = $(this).attr("value");
            if (optionValue == 2){
                $("#cam2Pax1 .varsteCopii").show();
				$( "#varstaCop1Cam2Pax1").show();
				$("#varstaCop2Cam2Pax1").hide();
				$("#cam2Pax1 .varsteCopii p#v1Pax12").show();
				$("#cam2Pax1 .varsteCopii p#v2Pax12").hide();
				
            } 
			if (optionValue == 3){
				 $("#cam2Pax1 .varsteCopii").show();
				$("#varstaCop2Cam2Pax1").show();
				$("#varstaCop1Cam2Pax1").show();
				$("#cam2Pax1 .varsteCopii p#v1Pax12").show();
				$("#cam2Pax1 .varsteCopii p#v2Pax12").show();
            } 
			 if (optionValue == 1){
				 $("#cam2Pax1 .varsteCopii").hide();
				$( "#varstaCop1Cam2Pax").hide();
				$("#varstaCop2Cam2Pax").hide();
				$("#cam2Pax .varsteCopii p#v1Pax12").hide();
				$("#cam2Pax .varsteCopii p#v2Pax12").hide();
				 }
        });
    });
	
	
	//adaugare & stergere camera 3 pachete

	$("#addCam3Pax1").on("click", function(e) {
		$("#cam3Pax1").show();
		$("#remCam2Pax1").hide();
		$("#addCam3Pax1").hide();
		});
	$("#remCam3Pax1").on("click", function() {
		$("#cam3Pax1").hide();
		$("#remCam2Pax1").show();
		$("#addCam3Pax1").show();
		$("#cam3Pax1 .varsteCopii").hide();
		$('#copiiCam3Pax1 option').prop('selected', function() {
        return this.defaultSelected;	});
		});

	$("#copiiCam3Pax1").on("change", function(e){
        $(this).find("option:selected").each(function(){
            var optionValue = $(this).attr("value");
            if (optionValue == 2){
                $("#cam3Pax1 .varsteCopii").show();
				$( "#varstaCop1Cam3Pax1").show();
				$("#varstaCop2Cam3Pax1").hide();
				$("#cam3Pax1 .varsteCopii p#v1Pax13").show();
				$("#cam3Pax1 .varsteCopii p#v2Pax13").hide();
				
            } 
			if (optionValue == 3){
				 $("#cam3Pax1 .varsteCopii").show();
				$("#varstaCop2Cam3Pax1").show();
				$("#varstaCop1Cam3Pax1").show();
				$("#cam3Pax1 .varsteCopii p#v1Pax13").show();
				$("#cam3Pax1 .varsteCopii p#v2Pax13").show();
            } 
			 if (optionValue == 1){
				 $("#cam3Pax1 .varsteCopii").hide();
				$( "#varstaCop1Cam3Pax1").hide();
				$("#varstaCop2Cam3Pax1").hide();
				$("#cam3Pax1 .varsteCopii p#v1Pax13").hide();
				$("#cam3Pax1 .varsteCopii p#v2Pax13").hide();
			 }
        });
    });
	

//carousel home page vechi, nu cel actual

  $( "#insideCarousel1" ).fadeIn( 1500 );
  
  $("#insideCarousel1 i").on("click", function(){
	  $( "#insideCarousel2" ).fadeIn(1500);
	   $( "#insideCarousel1" ).hide();
	  });
	  
  $("#insideCarousel2 i").on("click", function(){
	  $( "#insideCarousel1" ).fadeIn( 1500 );
	  $( "#insideCarousel2" ).hide();
	  });
	  
	  
	//scroll top
	  
	  $(window).scroll(function () {
            if ($(this).scrollTop() > 450) {
                $('#back-to-top').fadeIn();
            } else {
                $('#back-to-top').fadeOut();
            }
        });
		
        // scroll body to 0px on click
        $('#back-to-top').on("click", function () {
            $('#back-to-top').tooltip('hide');
            $('body,html').scrollTop();
			console.log("Hello!");
            return false;
        });
        
       
		
	var browserWidth =  $(window).width();
	
	if (browserWidth < 480) { 
				$(".searchMenu1 > ul").addClass('flex-column'); 
				console.log("hello");
	} 
	
	
	/* ----------- 	pentru afisare filtre din paginile de categorie la rezolutie < 768px 	------------------ */
	 
	 
	if(browserWidth < 769) { 
	
		$(".filterTitle").on("click", function(e){
			$("#allFilters").toggleClass("hiddenFilt");	
			
			if ($("#allFilters").hasClass("hiddenFilt") == false) {
				
					$(".filterTitle i:last-child").removeClass("fa-plus-square-o").addClass("fa-minus-square-o");
					
				}
				
				else {$(".filterTitle i:last-child").removeClass("fa-minuss-square-o").addClass("fa-plus-square-o");}
		
			});
	}
	
	
	 if($(".filterTitle i:last-child").hasClass("fa-minus-square-o")) {
		          
				  $(".filterTitle i:last-child").removeClass("fa-minus-square-o").addClass("fa-plus-square-o"); 	
				   }
				   
	/* ---------------- 	buton de aplica & resetare filtre 			   
				   
	$(window).scroll(function () {
            if ($(this).scrollTop() > 250) {
                $('#applyFilters').fadeIn();
			 } 
        });
	------------------ */	
		  
		$("#showMapBalcescu, #showMapBravu, #showMapPh").css("width", browserWidth -(browserWidth/2));
			
	/* ---------------- 	cookie info 	------------------ */
	
	$(".cookieInfo").delay(1000).fadeIn(500);
	$(".cookieInfo .btn").on("click", function(e){
		$(".cookieInfo").fadeOut(1000);
		});
	
	
	
	/* display escale */
	
	
	
	$(".detEscale").on("click", function(e){
			
			$(this).parents(".tur, .retur").children(".infoEscale").toggle('slow');
			$(this).toggleClass("warning, danger");
			
			
			
			if($(this).hasClass("danger")) {
				$(this).html("<i class='fa fa-times-circle'></i> Inchide");
				$(".boxTicket > span, .boxTicket > img, .boxTicket p").addClass("opacity");
				$(".boxTicket").find(".detEscale, .escale > span").addClass("opacity");
				$(this).removeClass("opacity");
				var timeE = $(this).parents(".tur, .retur").children(".infoEscale").find(".timeEsc1").length;
				
				if(timeE < 2) {
					 $(this).parents(".tur, .retur").children(".infoEscale").children(".timeEsc1").css("width", "60%");
					}
					
				}
				 else {
					 $(this).html("<i class='fa fa-info-circle'></i> Detalii"); 
					 $(".boxTicket > span, .boxTicket > img, .boxTicket p").removeClass("opacity");
					 $(".boxTicket").find(".detEscale, .escale > span").removeClass("opacity");
					 }
		}); 	

$(".filterTitleT").on("click", function(e){
			$("#allFiltersT").toggleClass("hiddenFiltT");	
			
			if ($("#allFiltersT").hasClass("hiddenFiltT") == false) {
				
					$(".filterTitleT i:last-child").removeClass("fa-plus-square-o").addClass("fa-minus-square-o").css("color","#F00");
					
				}
				
				else {$(".filterTitleT i:last-child").removeClass("fa-minuss-square-o").addClass("fa-plus-square-o").css("color","#0275d8");}
		
			});

	
	
	 if($(".filterTitleT i:last-child").hasClass("fa-minus-square-o")) {
		          
				  $(".filterTitleT i:last-child").removeClass("fa-minus-square-o").addClass("fa-plus-square-o"); 	
				   };
				   
				   
				   
$(".calendarT").on("click", function(e){
			$("#calendarFlights").toggleClass("hiddenFiltT");	
			
			if ($("#calendarFlights").hasClass("hiddenFiltT") == false) {
				
					$(".calendarT i:last-child").removeClass("fa-plus-square-o").addClass("fa-minus-square-o").css("color","#F00");
					
				}
				
				else {$(".calendarT i:last-child").removeClass("fa-minuss-square-o").addClass("fa-plus-square-o").css("color","#0275d8");}
		
			});

	
	
	 if($(".calendarT i:last-child").hasClass("fa-minus-square-o")) {
		          
				  $(".calendarT i:last-child").removeClass("fa-minus-square-o").addClass("fa-plus-square-o"); 	
				   };			
				   
				   
				   /*---------- JQUERY FOR +/- # Days table from FLIGHTS SEARCH RESULTS ---------- */

$(".table3Days td").on("mouseenter", function(e){
    
							e.preventDefault();
							
							var index = $(this).index();
								
								jQuery(".table3Days tr:first-child th").eq(index).addClass("hoverBGT");
								$(this).parents(".table3Days tr").find("th").addClass("hoverBGL");
								jQuery(this).addClass("hoverTD");
								jQuery(this).children(".toolTipPrice").show();
								jQuery(this).css("position", "relative");
							
						}).on("mouseleave", function(e){
							
							e.preventDefault();
							var index = $(this).index();
							
								
								jQuery(".table3Days tr:first-child th").eq(index).removeClass("hoverBGT");
								$(this).parents(".table3Days tr").find("th").removeClass("hoverBGL");
								jQuery(this).removeClass("hoverTD");
								jQuery(this).children(".toolTipPrice").hide();
								jQuery(this).css("position", "static"); 
								}); 

 				jQuery(".plus3Days").on("click", function(e) { jQuery(".table3Days").toggle("slow"); }); 
  
 
/*------------------------- Assurance for flight details page ---------------------*/
		
		
		var assuranceCal = $("#asigCal").prop('checked', false);
		var assuranceSto = $("#asigSto").prop('checked', false);
		
			$(assuranceCal).on("change", function(e) {
								
							
									$("#rowIns").toggle();
									$(".firstP").toggleClass("greenBG");	
									$(".insuranceTBL .firstP i").toggle()	;
									$("#asigSto").toggle().attr('disabled');
									$("#asigSLB").toggle().attr('disabled');
									
			 });	
			
			$(assuranceSto).on("change", function(e) {
								
									$("#rowIns").toggle();
									$(".secondP").toggleClass("greenBG");	
									$(".insuranceTBL .secondP i").toggle();	
									$("#asigCal").toggle().attr('disabled');
								$("#asigCLB").toggle().attr('disabled');
			}); 
			
			
			
			jQuery("#telefon").on("mouseenter  focus", function(e) {
			
				$(".infoTEL").show(); }).on("mouseleave", function(e) { 
						$(".infoTEL").hide();  
				});
		
				jQuery("#telefonC").on("mouseenter  focus", function(e) { 
						$(".infoTELC").show();	}).on("mouseleave", function(e) { $(".infoTELC").hide(); 
				});
			
				jQuery("#facturaPJ").on("change", function(e) { 	jQuery("#infoPlataFirma").toggle("slow"); 	jQuery("#infoPlataPers").toggle();  });
					
				jQuery(".infoCC").on("click", function(e) { 
				
					jQuery(".infoCupon").toggle(); 	
				});
					
				jQuery("#cvvBut").on("click", function(e) { jQuery("#cvv").show(); });
						
				jQuery("#cvv").on("click", function(e) { 	jQuery("#cvv").hide(); });
		
				jQuery("#veziText").on("click", function(e) {	
					
				jQuery(".paraPad").toggleClass("heightDown", "slow");
				
				jQuery("#veziText .fa-angle-down").toggleClass("fa-rotate-180");		
			
		});
		
		
		$(".insuranceTBL td:has(p.alert)").css("border-right", "none");
		
		
		
			/*scroll la info asigurare dupa ce input-ul a fost checked pe mobil*/
			
			if(browserWidth < 576) {
			
			$("#asigCal").on("change", function(e) {
				
    			var elem = document.getElementById('rowIns');
				elem.scrollIntoView();
				
				});
			}
	
	// la click apar campurile de setare parola in pagina de rezervare
	var daCont, nuCont;
	
	$("#vreauCont").on('click', function(){
		
		daCont = $("#vreauCont").prop('checked');
		
		
		if(daCont) {
			
			$(".passSet, .passConf").show();
			$("#nuVreauCont").removeAttr("checked");
			}
			
		else if(!daCont) {
			
			$(".passSet, .passConf").hide();
			$("#nuVreauCont").attr("checked", true);
			}
			
		
		else {	/* */ }	
			
		});
		
		
		$("#nuVreauCont").on('click', function(){
		
		nuCont = $('#nuVreauCont').prop('checked');
		
		if(nuCont) {
			
			$(".passSet, .passConf").hide();
			$("#vreauCont").removeAttr('checked');
			}
			
		else if(!nuCont) {
			
			$(".passSet, .passConf").show();
			$("#vreauCont").attr("checked");
			}
			
		else {/* */}	
			
		});
		
		
		
// document closed
});