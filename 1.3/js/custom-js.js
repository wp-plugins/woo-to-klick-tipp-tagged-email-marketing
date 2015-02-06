jQuery(document).ready(function($) {
	$(".gh_accordian_div").css("display","none");
	$('.gh_accordian_tab').click(function(){   
$(this).toggleClass("MenuTopon");  
var id = $(this).attr("id");
var number = id.substring(6,id.length);
$("#myDiv"+number).slideToggle("slow");});
	
});

jQuery(document).ready(function($) {
	//Default Action
	$("#hangout_main .gh_container .gh_tabs_div").hide(); //Hide all content
	$("#hangout_main .gh_tabs .gh_tabs_list li:first").addClass("selected_tab").show(); //Activate first tab
	$("#hangout_main .gh_container .gh_tabs_div:first").show(); //Show first tab content
	//On Click Event
	$("#hangout_main .gh_tabs .gh_tabs_list li").click(function() {
		$("#hangout_main .gh_tabs .gh_tabs_list li").removeClass("selected_tab"); //Remove any "active" class
		$(this).addClass("selected_tab"); //Add "active" class to selected tab
		$("#hangout_main .gh_container .gh_tabs_div").hide(); //Hide all tab content
		var activeTab = $(this).find("a").attr("href"); //Find the rel attribute value to identify the active tab + content
		$(activeTab).fadeIn(); //Fade in the active content
		return false;
	});
});

jQuery(document).ready(function($) {
	//Default Action
	$("#hangout_main .gh_container .gh_inner_tabs").hide(); //Hide all content
	$("#hangout_main .hornav li:first").addClass("current_tab").show(); //Activate first tab
	$("#hangout_main .gh_container .gh_inner_tabs:first").show(); //Show first tab content
	//On Click Event
	$("#hangout_main .hornav li").click(function() {
		$("#hangout_main .hornav li").removeClass("current_tab"); //Remove any "active" class
		$(this).addClass("current_tab"); //Add "active" class to selected tab
		$("#hangout_main .gh_container .gh_inner_tabs").hide(); //Hide all tab content
		var activeTab = $(this).find("a").attr("href"); //Find the rel attribute value to identify the active tab + content
		$(activeTab).fadeIn(); //Fade in the active content
		return false;
	});
});

