function ajax_get(url, callback) {
  var xmlhttp = new XMLHttpRequest();
  xmlhttp.onreadystatechange = function () {
      if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {

          try {
              var data = xmlhttp.responseText;
          } catch (err) {
              console.log(err.message + " in " + xmlhttp.responseText);
              return;
          }
          callback(data);
      }
  };

  xmlhttp.open("GET", url, true);
  xmlhttp.send();
}

var currentPath = window.location.pathname;




var partialsCache = {}

function resolvePathAndTitle(path) {
  switch (path) {
      case '/':
          return {
              htmlPath: '/html/home.html', title: 'Home'
          }
          case '/Flip-Challenge':
              return {
                  htmlPath: '/html/flip-challenge.html', title: 'Flip Challenge'
              }
              case '/admin':
                return {
                    htmlPath: '/html/admin.html', title: 'admin'
                }
          default:
              return {
                  htmlPath: '/html/home.html', title: 'Home'
              }
  }
}

function navigate(path) {
  let PF = path.split('/')[1];
  if (partialsCache[PF]) {

      document.getElementById("content").innerHTML = partialsCache[PF];
      document.title = resolvePathAndTitle(path).title;
      window.history.pushState({
          "html": partialsCache[PF],
          "pageTitle": resolvePathAndTitle(path).title
      }, "", path);
      return
  } else {

      ajax_get(resolvePathAndTitle(path).htmlPath, function (data) {
          partialsCache[PF] = data;
          document.getElementById("content").innerHTML = data;
          document.title = resolvePathAndTitle(path).title;
          window.history.pushState({
              "html": data,
              "pageTitle": resolvePathAndTitle(path).title
          }, "", path);
      })
  }


}
navigate(currentPath);


window.onpopstate = function (e) {
  if (e.state) {
      document.getElementById("content").innerHTML = e.state.html;
      document.title = e.state.pageTitle;
  }
};