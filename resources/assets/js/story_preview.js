var template = require("./templates").story_body;

function postStoryPageRender() {
  global.app.video.setupYoutubeVideo();
  global.app.video.loadYoutubeLibrary();
}

module.exports = function() {
  window.addEventListener("message", function(event){
    var story = event.data['story'];
    if (story) {
      $("#container").html(template.render({
        story :story,
        relatedStories: Array(8).fill(story),
        preview: true
      }));
      postStoryPageRender();
    }
  });
}
