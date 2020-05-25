$(document).ready(function() {
  //E-mail Ajax Send
  $("#form").submit(function() { //Change
    var th = $(this);
    $.ajax({
      type: "POST",
      url: "mail.php", //Change
      data: th.serialize()
    }).done(function() {
      setTimeout(function() {
        $(".thank").fadeIn(500);
         $(".thank").delay(3000).fadeOut(500);
           $('.modal').modal('hide');
        // Done Functions
        th.trigger("reset");
      });
    });
    return false;
  });

});

//--------------------------scrollTO...---------------------
$("#better").click(function (){
		$('html, body').animate({
				scrollTop: $(".better").offset().top
		}, 500);
})
$("#why").click(function (){
		$('html, body').animate({
				scrollTop: $(".tripleprofit").offset().top
		}, 800);
})
$("#stat").click(function (){
		$('html, body').animate({
				scrollTop: $(".statistic_sys").offset().top
		}, 1000);
})
$("#quest").click(function (){
		$('html, body').animate({
				scrollTop: $(".questions").offset().top
		}, 1300);
})
$("#news").click(function (){
		$('html, body').animate({
				scrollTop: $(".news").offset().top
		}, 1500);
})


// ----------------hamburger-------------------
$("#nab_btn_open").click(function(){
	 $(".open_menu").slideToggle( "slow" );
})

