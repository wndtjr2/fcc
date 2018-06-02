$(function(){
	var boo = false;
  var video = document.getElementById("fcctv__player");

	$('.details__review .control__play').bind('click', function(e){
		boo = ( $('#details').hasClass('show-onair') ) ? false : true;

		if ( boo ) {
			$('#details').addClass('show-onair')
			setTimeout(function(){
				( video == null ) ? false : vidplay();
			}, 500);
			return;
		}
	});


  function vidplay(evt) {
    if (video.paused) {   // play the file, and display pause symbol
      video.play();
    } else {              // pause the file, and display play symbol  
      video.pause();
    }
  }

  $('.product_tab').bind('click', function(e){
  	console.log($(this).hasClass('coments'));
  	if ( $(this).hasClass('coments') ) {
  		$('.details__products').addClass('show-comment');
  		$('.details__products').removeClass('show-detail');
  		return;
  	}
  	$('.details__products').addClass('show-detail');
  	$('.details__products').removeClass('show-comment');
  })
})