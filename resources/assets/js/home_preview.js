var template = require("./templates").home_body;

var $ = require("jquery");

module.exports = function() {
  window.addEventListener("message", function(event){
    var story = event.data['story'];
    if (story) {
      var html = template.render({
        stories: Array(4).fill(story),
        stacks : {
          Stack_1: { heading:"Stack_1", stories: Array(5).fill(story) },
          Stack_2: { heading:"Stack_2", stories: Array(5).fill(story) },
          Stack_3: { heading:"Stack_3", stories: Array(5).fill(story) }
        },
        preview: true
      });
      $("#container").html(html);
    }
  });
}
