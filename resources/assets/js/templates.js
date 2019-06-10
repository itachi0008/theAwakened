var _ = require("lodash");
var Twig = require("twig");

var FocusedImage = require("quintype-js").FocusedImage;

global.transformTemplates = function(x) {
    return _.extend(x, {
        id: x.id.replace(/resources\/views\//, "").replace(/.twig/, ''),
        path: x.path.replace(/resources\/views\//, "").replace(/.twig/, '')
    })
};

Twig.extendFunction("focusedImageUrl", function(slug, aspectRatio, metadata, options) {
    var cdn = global.qtConfig["image-cdn"];
    var image = new FocusedImage(slug, metadata);
    return cdn + "/" + image.path(aspectRatio, options);
});

Twig.extend(function(Twig) {
    var importFile = Twig.Template.prototype.importFile;
    Twig.Template.prototype.importFile = function(path) {
        var cachedTemplate = Twig.Templates.registry[path] || Twig.Templates.registry["/" + path];
        if (cachedTemplate)
            return cachedTemplate;
        if (console) console.warn("Unable to find template: ", path);
        importFile.call(this, path);
    }
});

Twig.extendFunction("decode64", function(string) {
    return atob(string);
});

Twig.extendFunction("getPhotoStoryImages", function(story) {
  var storyHeroImage = {
      'image-s3-key' : story['hero-image-s3-key'] ,
      'image-metadata' : story['hero-image-metadata'],
      'title' :  story['title']
    };

    var storyImages = [storyHeroImage];

    _.forEach(story['cards'], function(card) {
      _.forEach(card['story-elements'], function (storyElement) {
        if (storyElement['type'] === 'image') {
          storyImages.push(storyElement);
        }
      });
    });
    return storyImages;
});

Twig.extendFunction("prepareMenuUrl", function(menuType, menuSlug, parentHierarchy) {
  switch(menuType){
    case 'section':
      if(parentHierarchy.length > 0){
        return '/section/'+ parentHierarchy.join("/") + '/' + menuSlug;
      }
      return '/section/' + menuSlug;
      break;
    case 'link':
      return menuSlug;
      break;
    case 'tag':
      return '/tag?tag=' + menuSlug;
      break;
    default:
      return '#';
      break;
  }
});

Twig.extendFunction("dateIsoFormat", function(date) {
   return new Date(date).toISOString();
});

require("../../../resources/views/author/body.twig");
require("../../../resources/views/author/index.twig");

require("../../../resources/views/home/body.twig");
require("../../../resources/views/home/index.twig");

require("../../../resources/views/search/body.twig");
require("../../../resources/views/search/index.twig");

require("../../../resources/views/section/body.twig");
require("../../../resources/views/section/index.twig");

require("../../../resources/views/shared/article_list.twig");
require("../../../resources/views/shared/load_more_stories.twig");
require("../../../resources/views/shared/axiom_icon.twig");


require("../../../resources/views/story/author_info.twig");
require("../../../resources/views/story/body.twig");
require("../../../resources/views/story/index.twig");
require("../../../resources/views/story/related_stories.twig");
require("../../../resources/views/story/share.twig");
require("../../../resources/views/story/story_card.twig");
require("../../../resources/views/story/story_elements.twig");
require("../../../resources/views/story/tags.twig");
require("../../../resources/views/story/video_story_elements.twig");

require("../../../resources/views/story/social_icons/facebook_svg_icon.twig");
require("../../../resources/views/story/social_icons/gplus_svg_icon.twig");
require("../../../resources/views/story/social_icons/img_expanded.twig");
require("../../../resources/views/story/social_icons/linkedin_svg_icon.twig");
require("../../../resources/views/story/social_icons/slide_close.twig");
require("../../../resources/views/story/social_icons/twitter_svg_icon.twig");
require("../../../resources/views/story/social_icons/whatsapp_svg_icon.twig");

require("../../../resources/views/story/story_elements/composite.twig");
require("../../../resources/views/story/story_elements/external-file.twig");
require("../../../resources/views/story/story_elements/image.twig");
require("../../../resources/views/story/story_elements/jsembed.twig");
require("../../../resources/views/story/story_elements/polltype.twig");
require("../../../resources/views/story/story_elements/references.twig");
require("../../../resources/views/story/story_elements/soundcloud-audio.twig");
require("../../../resources/views/story/story_elements/text.twig");
require("../../../resources/views/story/story_elements/title.twig");
require("../../../resources/views/story/story_elements/youtube-video.twig");

require("../../../resources/views/story/story_elements/story_elements_sub_type/bigfact.twig");
require("../../../resources/views/story/story_elements/story_elements_sub_type/blockquote.twig");
require("../../../resources/views/story/story_elements/story_elements_sub_type/blurb.twig");
require("../../../resources/views/story/story_elements/story_elements_sub_type/gallery.twig");
require("../../../resources/views/story/story_elements/story_elements_sub_type/ingredients.twig");
require("../../../resources/views/story/story_elements/story_elements_sub_type/jwplayer.twig");
require("../../../resources/views/story/story_elements/story_elements_sub_type/playlist.twig");
require("../../../resources/views/story/story_elements/story_elements_sub_type/q-and-a.twig");
require("../../../resources/views/story/story_elements/story_elements_sub_type/quote.twig");
require("../../../resources/views/story/story_elements/story_elements_sub_type/references.twig");
require("../../../resources/views/story/story_elements/story_elements_sub_type/slideshow.twig");
require("../../../resources/views/story/story_elements/story_elements_sub_type/summary.twig");

require("../../../resources/views/story/templates/default.twig");
require("../../../resources/views/story/templates/photo.twig");
require("../../../resources/views/story/templates/recipe.twig");
require("../../../resources/views/story/templates/video.twig");

require("../../../resources/views/tag/body.twig");
require("../../../resources/views/tag/index.twig");


var TEMPLATES = {
    "home_body": require("../../../resources/views/home/body.twig"),
    "story_body": require("../../../resources/views/story/body.twig"),
    "list_articles": require("../../../resources/views/shared/article_list.twig"),
    "list_collections": require("../../../resources/views/shared/collections_more.twig"),
    "list_collections_authors": require("../../../resources/views/shared/More_authorStories.twig"),
    "list_collections_search": require("../../../resources/views/shared/More_searchStories.twig")
};

module.exports = TEMPLATES;
    
