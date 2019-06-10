function rating(storyId, user_rating) {
  var ratingValue = getCookie(user_rating);
  if (ratingValue >= 1 && ratingValue <= 5){
    ratedOnce(ratingValue);
  } else {
    $(".star").click(function(){
      var rating = $(this).attr("data-rating");
      document.cookie = "user_rating = "+rating;
      ratedOnce(rating);
      updateRating(storyId, rating);
    });
  }

  function ratedOnce(rating){
    this.ratingStars = document.querySelectorAll('#rating span');
    for (var i = 0; i < rating ; i++) {
      this.ratingStars[i].classList.add('star-rated');
    }
    $(".star").off("click");
  }

  function updateRating(storyId, rating){
    $.ajax({
      type: "POST",
      url: "/api/stories/"+storyId+"/votes",
      data: JSON.stringify({ 'magnitude': parseInt(rating) }),
      contentType: 'application/json',
      success: function (data) {
        getAverageRating(storyId);
      },
      error: function (data) {
         console.log('Error:', data);
       }
     });
  }

  function getAverageRating(){
    $.get("/api/stories/"+storyId+"", function(data, status){
      var votes = data.story.votes;
      var numerator = 0; var noOfVoters = 0;
      for (var key in votes) {
       numerator += key * votes[key];
       noOfVoters += votes[key];
     }
     var rating = (numerator/noOfVoters);
     var averageRating = Math.round(rating * 10)/10; var ratingPercent = (averageRating * 100)/5;
     $(".rater_count").text(noOfVoters);
     $(".star-percentage-rated").width(ratingPercent+'%');
     $(".rated-value").text(averageRating);
   });
  }

  function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for (var i = 0; i < ca.length; i++) {
      var c = ca[i];
      while (c.charAt(0) == ' ') {
        c = c.substring(1);
      }
      if (c.indexOf(name) == 0) {
         return parseInt(c.substring(name.length, c.length));
      }
    }
    return "";
  }
}

module.exports = rating;
