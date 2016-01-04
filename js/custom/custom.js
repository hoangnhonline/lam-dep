// JavaScript Document

$(document).ready(function(){				
	
    'use strict';
	
/************************************************************************************ DROPDOWN MENU STARTS */
	
	$('.dropdown-toggle').dropdown();  
	
/************************************************************************************ DROPDOWN MENU ENDS */

/************************************************************************************ SIDR OFFCANVASS MENU STARTS */

	$('#responsive-menu-button').sidr({
		name: 'sidr-main',
		source: '#navigation'
	});
	
/************************************************************************************ SIDR OFFCANVASS MENU STARTS */

/************************************************************************************ SLIDER CAROUSEL STARTS */

      $(".slider").owlCarousel({

      	slideSpeed : 300,
      	paginationSpeed : 400,
      	singleItem : true,        
        items: 1,
        itemsDesktop: [1199, 1],
        itemsDesktopSmall: [979, 1],
        itemsTablet: [768, 1],
        itemsMobile: [479, 1],
        autoHeight: true,
        pagination: false,
        navigation: true,
        transitionStyle: "fade",
		navigationText: [
            "<i class='fa fa-angle-left'></i>",
            "<i class='fa fa-angle-right'></i>"
        ],
      });

/************************************************************************************ SLIDER CAROUSEL ENDS */

/************************************************************************************ NEWSTICKER CAROUSEL STARTS */

      $(".newsticker").owlCarousel({

      	autoPlay : 3000,      
      	slideSpeed : 300,
      	paginationSpeed : 1000,
      	singleItem : true,        
        items: 1,
        itemsDesktop: [1199, 1],
        itemsDesktopSmall: [979, 1],
        itemsTablet: [768, 1],
        itemsMobile: [479, 1],
        autoHeight: true,
        pagination: false,
        navigation: true,
        transitionStyle: "fade",
		navigationText: [
            "<i class='fa fa-angle-left'></i>",
            "<i class='fa fa-angle-right'></i>"
        ],
      });

/************************************************************************************ NEWSTICKER CAROUSEL ENDS */

/************************************************************************************ FEATURED VIDEO CAROUSEL STARTS */

      $(".featured-video-carousel").owlCarousel({
		autoPlay : 3000,      
      	slideSpeed : 300,
      	paginationSpeed : 400,
     	singleItem : true,        
        items: 1,
        itemsDesktop: [1199, 1],
        itemsDesktopSmall: [979, 1],
        itemsTablet: [768, 1],
        itemsMobile: [479, 1],
        autoHeight: true,
        pagination: false,
        navigation: true,
        transitionStyle: "fade",
		navigationText: [
            "<i class='fa fa-angle-left'></i>",
            "<i class='fa fa-angle-right'></i>"
        ],
      });

/************************************************************************************ FEATURED VIDEO CAROUSEL ENDS */	

/************************************************************************************ GALLERY CAROUSEL STARTS */

      $(".gallery-carousel").owlCarousel({
		autoPlay : 3000,
      	slideSpeed : 300,
      	paginationSpeed : 400,
      	singleItem : true,
        items: 1,
        itemsDesktop: [1199, 1],
        itemsDesktopSmall: [979, 1],
        itemsTablet: [768, 1],
        itemsMobile: [479, 1],
        autoHeight: true,
        pagination: false,
        navigation: true,
        transitionStyle: "fade",
		navigationText: [
            "<i class='fa fa-angle-left'></i>",
            "<i class='fa fa-angle-right'></i>"
        ],
      });

/************************************************************************************ GALLERY CAROUSEL ENDS */	

/************************************************************************************ FITVID STARTS */

    $(".fitvid").fitVids();

/************************************************************************************ FITVID ENDS */

/************************************************************************************ TO TOP STARTS */

    $(window).scroll(function () {
        if ($(this).scrollTop() > 100) {
            $('.scrollup').fadeIn();
        } else {
            $('.scrollup').fadeOut();
        }
    });

    $('.scrollup').on("click" ,function() {
        $("html, body").animate({
            scrollTop: 0
        }, 600);
        return false;
    });

/************************************************************************************ TO TOP ENDS */

/************************************************************************************ CURRENT DATE */

var d = new Date();
document.getElementById("date").innerHTML = d.toDateString();

/************************************************************************************ CURRENT ENDS */

}); //$(document).ready(function () {