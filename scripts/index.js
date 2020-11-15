toastr.options = {
  "newestOnTop": false,
  "progressBar": true,
  "positionClass": "toast-bottom-right",
  "preventDuplicates": false,
  "showDuration": "300",
  "hideDuration": "1000",
  "timeOut": "5000",
  "extendedTimeOut": "1000",
  "showEasing": "swing",
  "hideEasing": "linear",
  "showMethod": "fadeIn",
  "hideMethod": "fadeOut"
};

function makeLogged(id, balance, votes) {
  document.getElementById("navbar-SignIN").innerHTML = '<ul class="navbar-nav ml-auto">' +
    '<li class="nav-item dropdown">' +
    '<a class="nav-link dropdown-toggle p-0 pointer"  id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' +
    '<img src="/api/images/?id=' + id +
    '" width="40" height="40" style="background-color:#00000057;"class="rounded-circle">' +
    '</a>' +
    '<div style="left: -200%;" class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">' +
    '<a class="dropdown-item" >Balance : ' + balance + ' iDNA</a>' +
    '<a class="dropdown-item" >Votes : ' + votes + ' left</a>' +
    '<a class="dropdown-item pointer" onclick="navigate(`/invite`);">Invite</a>' +
    '<a class="dropdown-item pointer" onclick="navigate(`/settings`);">Settings</a>' +
    '<a class="dropdown-item pointer" onclick="logout(); navigate(`/home`);">Log Out</a>' +
    '</div>' +
    '</li>   ' +
    '</ul>';
}

function makeNotLogged() {
  document.getElementById("navbar-SignIN").innerHTML = '<a class="btn btn-signin ml-auto" onclick="openIdena();">' +
    '<img alt="signin" class="icon icon-logo-white-small"' +
    ' src="https://scan.idena.io/static/images/idena_white_small.svg" width="24px">' +
    ' <span style="color: #fff;">Sign-in</span>' +
    '  </a>';
}

function openIdena() {
  ajax_get('/api/auth/getToken.php', function (data) {
    var urlofwebsite = window.location.origin;
    var url = 'dna://signin/v1?nonce_endpoint=' + urlofwebsite + '/api/auth/start-session.php&token=' + JSON.parse(data)['token'] +
      '&callback_url=' +
      urlofwebsite +
      '&authentication_endpoint=' +
      urlofwebsite +
      '/api/auth/auth.php';
    window.open(encodeURI(url), '_self');
    console.log(encodeURI(url));
  });
}

function loadPrivateTemplate() {
  document.getElementById("content").innerHTML = '<div>'
  +'<div class="mt-5 text-center">'
      +'<h4>You have to login to view this page</h4>'
      +'</div>'
      +'</div>';
}

function logout() {
  ajax_get('/api/auth/logout.php', function (data) {
    makeNotLogged();
  });

}

function checkLogged(callback, private = false) {
  ajax_get('/api/auth/checkLogin.php', function (data) {
    if (JSON.parse(data)['logged'] == true) {
      makeLogged(JSON.parse(data)['id'], JSON.parse(data)['balance'], JSON.parse(data)['votes']);
      callback();
    } else {
      makeNotLogged();
      if (!private) {
        callback();
      } else {
        loadPrivateTemplate();
      }
    }
  });
}