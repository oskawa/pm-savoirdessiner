jQuery(document).ready(function ($) {
    let bool = false
    $('.is-mobile__menu button').on('click', function(){
      console.log('oui!')
  
      if (!bool){
        $(this).addClass('is-active')
        $('.mobile-menu').addClass('show')
        
      }else{
        $(this).removeClass('is-active')
        $('.mobile-menu').removeClass('show')
  
      }
      bool = !bool
      console.log(bool)
    })
  
    $('.mobile-menu a').on('click', function(){
      console.log('allo')
      if ($(this).parent('.menu-item-has-children')){
        console.log('non on ferme pas')
      }else{

        $('.is-mobile__menu button').removeClass('is-active')
        $('.mobile-menu').removeClass('show')
      }
      bool = !bool
    })


    function toggle_video_modal() {
	    $(".hero-media__video").on("click", function(e){
          
        
	        e.preventDefault();         
          var id = $(this).attr('data-src');
          var autoplay = '?autoplay=1';
          var related_no = '&rel=0';
          var src = '//www.youtube.com/embed/'+id+autoplay+related_no;
          console.log(src)
          
          // Pass the YouTube video ID into the iframe template...
          // Set the source on the iframe to match the video ID
          $("#youtube").attr('src', src);
          
          // Add class to the body to visually reveal the modal
          $("body").addClass("show-video-modal noscroll");
	    
      });

	    // Close and Reset the Video Modal
      function close_video_modal() {
        
        event.preventDefault();

        // re-hide the video modal
        $("body").removeClass("show-video-modal noscroll");

        // reset the source attribute for the iframe template, kills the video
        $("#youtube").attr('src', '');
        
      }
      // if the 'close' button/element, or the overlay are clicked 
	    $('body').on('click', '.close-video-modal, .video-modal .overlay', function(event) {
	        
          // call the close and reset function
          close_video_modal();
	        
      });
      // if the ESC key is tapped
      $('body').keyup(function(e) {
          // ESC key maps to keycode `27`
          if (e.keyCode == 27) { 
            
            // call the close and reset function
            close_video_modal();
            
          }
      });
	}
	toggle_video_modal();


  boolChild = false
  function menu_child_mobile(){
    $('.menu-item-has-children').on('click', function(){
      if (!bool){
        $(this).addClass('active')
      }else{
        $(this).removeClass('active')
      }

      boolChild = !boolChild
    })
  }
  menu_child_mobile()
  
})
