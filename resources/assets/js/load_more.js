var _ = require("lodash");

function quintypeLoadMore (container, params, api, storyTemplate) {

  var api_search = '/api/v1/search?';
  var api_collections = '/api/v1/collections/';
  var moreStoriesTemplate = require("./templates")[storyTemplate];
  var loadButton = container.find('#load-more-button');
  var loadImage = container.find('#load-more-loader');
  params = _.extend({limit: 20}, params);
  container.find("#load-more-button").click(function() {
    
    loadButton.hide();
    loadImage.show();
    //Each time, fetch 1 story extra than the required limit so that we can use this to identify
    //whether or not to show the load more button. Once this is done, restore the original value.
    //Also, remove(slice) the extra story from being sent over to the template.
    $.get(api, _.extend(params, { offset: params.offset, limit: params.limit + 1 }), function(response){

      params.limit--;//Restoring the original value.
      if (api.indexOf(api_search) != -1){ //check for search api.
        var stories = response.results.stories;
      } else if(api.indexOf(api_collections) != -1) {
        var stories = response.items;
      }else {
        var stories = response.stories;
      }

      if(stories.length > 0) {

        params.offset += params.limit;
        container.find(".load-more-results").append(moreStoriesTemplate.render({stories: _.slice(stories, 0, params.limit)}));//Slice the extra story.
        if(stories.length > params.limit){
          loadButton.show();
          /* getting the page view count of the load more stories */   
            
          var length = stories.length;
          for (var i = 0; i < length; i++) {
              
          story_id = stories[i]['id'];
          if(stories[i]['slug']){
            story_slug = stories[i]['slug'];
          }else{
            story_slug = stories[i]['story']['slug'];
          }

          var host_url = "https://metype-api.staging.quintype.com/api/v1/accounts/49/pages/" + btoa("https://insurge-web.staging.quintype.io/" + story_slug) + "/comments.json";

              $.ajax({
                  type: 'GET',
                  url: '/api/stories/'+story_id+'/engagement?fields=shrubbery',
                  success: function(output, status, xhr) {
                    response = output['shrubbery'];
                    console.log(response['views']);
                    if(response != null) {
                      if (response['views'] == null) {
                        $( ".bulb_"+story_id ).html(0);
                      } else {
                        $( ".bulb_"+story_id ).html( response['views']);
                      }
                      
                    } else {
                      $( ".bulb_"+story_id ).html(0);
                    }

                  },
                  error: function(xhr, exception){
                    console.log("error")
                  },
                  cache: false,
                  async: false
              });

              //AJAX Call for showing comment count
              $.ajax({
                  type: 'GET',
                  url: host_url,
                  success: function(output, status, xhr) {
                    response = output;
                    if(response != null) {
                       if (response['total_count'] == null) {
                          //console.log(response['total_count']);
                         $( ".comment_"+story_id ).html(0);
                       } else {
                         $( ".comment_"+story_id ).html( response['total_count']);
                       }
                      
                      
                    } else {
                      $( ".comment"+story_id ).html(0);
                    }

                  },
                  error: function(xhr, exception){
                    console.log("hello")
                  },
                  cache: false,
                  async: false
              });


            }
            /* page view code ends here */
        }
      } else{
        loadButton.hide();
      }
      loadImage.hide();
    });
  });
}

module.exports = quintypeLoadMore;