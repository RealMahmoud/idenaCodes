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

  function makeLogged(address) {
    document.getElementById("navbar-SignIN").innerHTML = '<ul class="navbar-nav ml-auto">' +
      '<li class="nav-item dropdown">' +
      '<a class="nav-link dropdown-toggle p-0" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' +
      '<img src="https://robohash.org/' + address +
      '" width="40" height="40" style="background-color:#00000057;"class="rounded-circle">' +
      '</a>' +
      '<div style="left: -200%;" class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">' +
      '<a class="dropdown-item" >Balance : 5 Votes</a>' +
      '<a class="dropdown-item"href="#" onclick="'+"navigate('/invite');"+'">Invite</a>' +
      '<a class="dropdown-item" href="#" onclick="'+"navigate('/profile');"+'">Edit Profile</a>' +
      '<a class="dropdown-item" href="#" onclick="'+"navigate('/support');"+'">Support</a>' +
      '<a class="dropdown-item" href="#"onclick="logout(); '+"navigate('/home');"+'">Log Out</a>' +
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

  function checkLoggedStatus() {
    ajax_get('/api/auth/checkLogin.php', function (data) {
      if (JSON.parse(data)['Logged'] == true) {
        makeLogged(JSON.parse(data)['Address']);
      } else {
        makeNotLogged();
      }
    });
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

  function logout() {
    ajax_get('/api/auth/logout.php', function (data) {
      makeNotLogged();
    });

  }
  window.addEventListener("load", function () {
    checkLoggedStatus();
  });