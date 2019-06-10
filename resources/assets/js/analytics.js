var _ = require("lodash");
// inview plugin downloaded from https://github.com/protonet/jquery.inview
require('./vendor/jquery.inview/jquery.inview');
var storySelector = '.story-view',
    storyElementsSelector = '.story-elements';

function setup() {
  $(storySelector).bind('inview', trackStory);
  $(storyElementsSelector).one('inview', trackStoryElement);
}

function trackStory(event, visible) {
  if (visible == true) {
    var storyContentId = event.target.dataset['storyContentId'];
    qlitics('track', 'story-view', {
      'story-content-id': storyContentId,
    });
    $(storySelector).unbind('inview');
  }
}

function trackStoryElement(event, visible) {
  if (visible) {
    var cardDetails = $(event.target).closest('.story-card-view');
    var attributes = {
      'story-content-id': $(storySelector).data('story-content-id'),
      'story-version-id': $(storySelector).data('story-version-id'),
      'card-content-id': cardDetails.data('card-content-id'),
      'card-version-id': cardDetails.data('card-version-id'),
      'story-element-id': event.target.dataset['storyElementId'],
      'story-element-type': event.target.dataset['storyElementType']
    }
    qlitics('track', 'story-element-view', attributes);
  }
}

function trackStoryShare(event){
    var sharebtn = $(event.target).closest('.social-share');
    var attributes = {
    'story-content-id': $(storySelector).data('story-content-id'),
    'social-media-type': sharebtn.data('service'),
    'url':  sharebtn.data('social-media-share-url')
  }
  qlitics('track','story-share', attributes);
}

function trackYouTubeStoryElement(event) {
  var iframe = event.target.getIframe(),
      action;
  var storyCardDetails = $(iframe).closest('.story-card-view');
  var storyElementDetails = $(iframe).closest(storyElementsSelector);
  var attributes = {
    'story-content-id': $(storySelector).data('story-content-id'),
    'story-version-id': $(storySelector).data('story-version-id'),
    'card-content-id': storyCardDetails.data('card-content-id'),
    'card-version-id': storyCardDetails.data('card-version-id'),
    'story-element-id': storyElementDetails.data('story-element-id'),
    'story-element-type': storyElementDetails.data('story-element-type')
  }

  switch(event.data) {
  case YT.PlayerState.PLAYING: action = 'play';     break;
  case YT.PlayerState.PAUSED:  action = 'pause';    break;
  case YT.PlayerState.ENDED:   action = 'complete'; break;
  }
  if (action) {
    attributes['story-element-action'] = action;
    qlitics('track', 'story-element-action', attributes);
  }
}

module.exports = {
  setup: setup(),
  trackYouTubeStoryElement: trackYouTubeStoryElement,
  trackStoryShare: trackStoryShare
};
