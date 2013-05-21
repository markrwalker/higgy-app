$(document).bind("mobileinit", function(){
	$.mobile.pushStateEnabled = true;
	$.mobile.ajaxEnabled = false;
});


$(document).delegate("#main", "pageinit", function() {
	var menuStatus;
	
	// Show menu
	$("a.showMenu").click(function(){
		if (menuStatus != true){
			$("#menu").css({marginLeft: "0px"}, 100);
			$(".ui-page-active").animate({
				marginLeft: "165px"
			  }, 300, function(){menuStatus = true});
			  return false;
		  } else {
			$(".ui-page-active").animate({
				marginLeft: "0px"
			  }, 300, function(){menuStatus = false});
			$("#menu").animate({marginLeft: "-175px"}, 600);
			return false;
		  }
	});

});

// Menu behaviour
$("a.contentLink").click(function(){
	alert("hello");
//	$("#menu").animate({marginLeft: "0px"}, 100);
	var p = $(this).parent();
	console.log(p);
	if($(p).hasClass('active')){
		$("#menu ul li").removeClass('active');
	} else {
		$("#menu ul li").removeClass('active');
		$(p).addClass('active');
	}
});
