global.$ = global.jQuery = require("jquery");
require("jquery.marquee");
require("slick");

global.app = {
  initQtReady: require("./qt_ready"),
  startHomePreview: require("./home_preview"),
  startStoryPreview: require("./story_preview"),
  video: require("./video"),
  quintypeLoadMore : require("./load_more"),
  rating : require("./rating"),
  mapOverlay : require("./map_overlay"),
  slickSlideShow : require("./slick_slideshow_settings"),
  analytics: require("./analytics"),
  members: require("./members")
};

$(document).ready(function() {
  app.slickSlideShow.slickSettings();
  app.video.setupYoutubeVideo();
  app.video.loadYoutubeLibrary();
  app.mapOverlay.locationOverlay();
});

$('a.social-share').click(function(){
  global.app.analytics.trackStoryShare(event);
});

$('#logout_link').click(function(){ 

  $.ajax({
    method: 'POST', // Type of response and matches what we said in the route
    url: '/logout', // This is the url we gave in the route
    data: {
    }, // a JSON object to send back
    success: function(response) { // What to do if we succeed
  
       var str = document.location.href;
       var n = str.indexOf("profile");
       if(!(n == -1))
        {
          console.log("Im coming ddddddddddd here");
          alert("Im coming aaaa here");
          document.location.href="/";
        }
        else{     
          console.log("Im coming ddddddddddd here");
          alert("Im coming xxxx here");
          document.location.href = str;
          location.reload(true);
        }
    },
    
    error: function(jqXHR, textStatus, errorThrown) {
      // What to do if we fail
      alert(response);
      console.log(JSON.stringify(jqXHR));
      console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
    }
  });



});

$(document).ready(function(){
  $.ajax({
      type: 'GET',
      url: '/api/v1/members/me',
      success: function(output, status, xhr) {
        document.getElementById("loggedout").style.display = "none";
        $('#insurge-avatar').attr("src", output['member']['avatar-url']);
        console.log(output['member']['avatar-url']);
        if(document.location.pathname == "/")
        {
          document.location.href="/profile";
        }      
      },
      error: function(xhr, exception){
          document.getElementById("loggedin").style.display = "none";
          console.log("error")
    },
      cache: false,
      async: false
  });
});



$(document).ready(function(){ 

var m = $('.bulb').map(function () {
    return this.id;
})
var result = m;

var data_length = result.length;

for (var i = 0; i < data_length; i++) {
  
  var div_id = result[i];
  var story_id = div_id.split("_");
      story_id = story_id[1];
      console.log('this is story id' + story_id);
  $.ajax({
      type: 'GET',
      url: '/api/stories/'+story_id+'/engagement?fields=shrubbery',
      success: function(output, status, xhr) {
        response = output['shrubbery'];
        if(response != null) {
          console.log(output['shrubbery']['views']);
          if (response['views'] == null) {
            $( "."+div_id ).html(0);
          } else {
            $( "."+div_id ).html( response['views']);
          }
          
        } else {
          $( "."+div_id ).html(0);
        }

      },
      error: function(xhr, exception){
        console.log("error")
      },
      cache: false,
      async: false
  });

  $.ajax({
      type: 'GET',
      url: '/api/stories/'+story_id+'/engagement?fields=shrubbery',
      success: function(output, status, xhr) {
        response = output['shrubbery'];
        if(response != null) {
          console.log(output['shrubbery']['views']);
          if (response['views'] == null) {
            $( "."+div_id ).html(0);
          } else {
            $( "."+div_id ).html( response['views']);
          }
          
        } else {
          $( "."+div_id ).html(0);
        }

      },
      error: function(xhr, exception){
        console.log("error")
      },
      cache: false,
      async: false
  });

}

});
