(function($, window) {
  if (undefined !== typeof $) {
    $('.share-button').click(function(e) {
      e.preventDefault();
      var url = $(this).attr('href'),
        documentWidth = $(document).innerWidth();
      var leftPosition, topPosition,
        width = 550,
        height = 300;
      if (documentWidth <= 768) {
        leftPosition = 0;
        topPosition = 0;
        width = documentWidth;
      } else {
        leftPosition = (window.screen.width / 2) - (width / 2);
        topPosition = (window.screen.height / 2) - (height / 2);
      }
      var windowFeatures = "status=no,height=" + height + ",width=" + width + ",resizable=yes,left=" + leftPosition + ",top=" + topPosition + ",screenX=" + leftPosition + ",screenY=" + topPosition + ",toolbar=no,menubar=no,scrollbars=no,location=no,directories=no";
      window.open(url, 'sharer', windowFeatures);
      return false;
    });
  }

})(jQuery, window);
