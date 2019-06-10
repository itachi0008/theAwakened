function locationOverlay() {
  $(document).on('click', '.story-element-location', function() {
    $(this).find('iframe').css('pointer-events', 'auto');
  });

  $(document).on('mouseenter', '.story-element-location', function() {
    $(this).find('iframe').css('pointer-events', 'none');
  });

  $(document).on('mouseleave', '.story-element-location', function() {
    $(this).find('iframe').css('pointer-events', 'none');
  });
}

module.exports = {
  locationOverlay:locationOverlay
};