

(function() {
  var partialsCache = {}

  function getContent(fragmentId, callback) {
    if (partialsCache[fragmentId]) {
      callback(partialsCache[fragmentId]);
  } else {
    $.ajax({
      url: fragmentId + ".html",
      type: 'GET',
      success: function(data) {
        partialsCache[fragmentId] = data;

       callback(data);
      },
      error: function(datax) {
        $.get('404.html', function(data) {
          partialsCache[fragmentId] = data;
        //  setTimeout(function() {callback(data);}, 500);
        callback(data);
      });
      }
    });}
  }

  function setActiveLink(fragmentId) {
    
  /* if(fragmentId.substring(0, 5) == 'Utils'){
    Array.prototype.forEach.call(document.getElementsByClassName('NavElement'), function(el) {
      el.classList.add("buttonC2");
      el.classList.add("btn-outline-danger");
    el.classList.remove("btn-danger");
      if (el.id == '#Utils') {
          el.classList.remove("buttonC2");
      el.classList.remove("btn-outline-danger");
      el.classList.add("btn-danger");
        
      }
    });
   }else{
    Array.prototype.forEach.call(document.getElementsByClassName('NavElement'), function(el) {
      el.classList.add("buttonC2");
      el.classList.add("btn-outline-danger");
    el.classList.remove("btn-danger");
      if (el.id == "#" + fragmentId) {
          el.classList.remove("buttonC2");
      el.classList.remove("btn-outline-danger");
      el.classList.add("btn-danger");
        
      }
    });
   }
*/
  }

  function navigate() {
    var fragmentId = location.hash.substr(1);
     getContent(fragmentId, function(content) {
      $("#content").html(content);
    });
    window.caches.delete;
    setActiveLink(fragmentId);
  }
  if (!location.hash) {
    location.hash = "#home";
  }
  navigate();
  $(window).bind('hashchange', navigate);
}());