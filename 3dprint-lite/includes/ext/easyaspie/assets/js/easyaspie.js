/** Easy As Pie Responsive Navigation Plugin - Version 1.1

 The MIT License (MIT)

 * Copyright (c) 2014 Chris Divyak

 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:

 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.

 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
*/
(function(jQuery){

 	jQuery.fn.extend({

		//pass the options variable to the function
 		easyPie: function(options) {


			//Set the default values, use comma to separate the settings, example:
			var defaults = {
				  icon: "+", //icon for mobile push menu
				  navID: "nav", // nav id for ul
				  navClass: "applePie",//Navigation class
				  collapseClass: "pieCollapse", //class for collapsing menu on mobile
				  slideTop: true //change to false if you wish to not have a scrollTo function on your menu
			}

			var options =  jQuery.extend(defaults, options);

    		return this.each(function() {
				var o = options;

				if (jQuery(window).width() > 800) {
					//Call Superfish menu for nice drop-downs
					jQuery("."+o.navClass).superfish({delay:0,speed:'fast',});
					}
				//IF WINDOW SIZE kindle
				if (jQuery(window).width() <= 800) {
					//IF NAV LI CONTAINS DROPDOWN, ADD PLUS SIGN
					jQuery("li").find('ul').addClass(o.collapseClass);
					jQuery("."+o.navClass+ "span").remove();
					jQuery(this).find("ul."+o.collapseClass).before('<span>'+o.icon+'</span>');
				}

				//ON WINDOW RESIZE
				jQuery(window).on('resize', function(){

			      	if (jQuery(window).width() <= 800) {
			      		//on resize make sure hidden nav even if wasn't hidden first time
				     	jQuery("#"+o.navID).css("display","none");
			      		 //ON CLICK SLIDETOGGLE vertical menu
						 jQuery("."+o.navClass+" li span").unbind('click').click(function(e){
						 	 e.preventDefault();
							     jQuery(this).next().slideToggle(function(){
								     jQuery(this).parent().toggleClass("menuOpen");

								  });
							 //If slideTop equals true then slide
						     if(o.slideTop == true){
							       navigateTo(jQuery(this));
								   return false;
							   }
							 //else, return false
							 else{
								   return false;
							   }
							});

					  	//Destroy Superfish to prevent hovering on resize
					  	jQuery("."+o.navClass).superfish('destroy');

					    //Check if span exists within li, if not, add
					    if(jQuery("."+o.navClass+" li span").length < 1){
					  	   jQuery("."+o.navClass+" li > ul").before("<span>"+o.icon+"</span>");
					  	}

					  	//If slideToggle was open, close
						//if (jQuery("."+o.navClass+" ul").is(":visible")){
							//jQuery("."+o.navClass+" ul:first").hide();
							//}
						//FIX menu hide issue when nav gets to bottom of device
						if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
					
							jQuery("."+o.navClass+" ul ul:first").show();
							}
						jQuery('ul.nav').show();

					 }
					 if (jQuery(window).width() > 800) {
					 	//Show nav even if slideToggle collapsed
						jQuery("."+o.navClass+" ul:first").show();

						//CALL SUPERFISH ON RESIZE
						jQuery("."+o.navClass).superfish({delay:0,speed:'fast',});
						}
				});

				//ON CLICK SLIDETOGGLE
				 jQuery("."+o.navClass+" li span, .menubtn").unbind("click").click(function(e){
					  e.preventDefault();

					  //remove all classes and slidetoggle


					 //Add class to open slidetoggle menu
				     jQuery(this).next("ul").slideToggle(function(e){
					     jQuery(this).parent().toggleClass("menuOpen");
						 });

				     if(jQuery("."+o.navClass+"ul:first").is(":visible")){
					     jQuery(".menubtn").addClass("menuOpen");
				     }
				     //If slideToggle is close, remove class
				    if(jQuery("."+o.navClass+"ul:first").is(":hidden")){
					     jQuery(".menubtn").removeClass("menuOpen");
				     }
				     //If slideTop equals true then slide
				     if(o.slideTop == true){
					       navigateTo(jQuery(this));
						   return false;
					   }
					 //else, return false
					 else{
						   return false;
					   }

				 });

				 //Slide to li on click
				 function navigateTo(destination) {
					 jQuery('html,body').delay(500).animate({scrollTop: jQuery(destination).offset().top - 48},'fast');
					 }
    		});
    	}
	});
})(jQuery);
//If the menu has a hash tag in href, slidetoggle menu
jQuery(window).on("hashchange", function () {

        if (jQuery(window).width() <= 800) {
            jQuery("nav.applePie").slideToggle();
            }

});
