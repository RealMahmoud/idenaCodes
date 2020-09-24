(function () {
  var partialsCache = {}

  function getContent(fragmentId, callback) {
    if (partialsCache[fragmentId]) {
      callback(partialsCache[fragmentId]);
    } else {
      if(fragmentId == ''){
        fragmentId = 'home';
      }
      $.ajax({
        url: fragmentId + ".html",
        type: 'GET',
        success: function (data) {
          partialsCache[fragmentId] = data;

          callback(data);
        },
        error: function (datax) {
          $.get('home.html', function (data) {
            partialsCache[fragmentId] = data;
          
            callback(data);
          });
        }
      });
    }
  }



  function navigate() {
    var fragmentId = location.hash.substr(1);
    getContent(fragmentId, function (content) {
      $("#content").html(content);
    });
    window.caches.delete;

  }
  if (!location.hash) {
    location.hash = "#home";
  }
  navigate();
  $(window).bind('hashchange', navigate);
}());