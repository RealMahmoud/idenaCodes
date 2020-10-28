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
function ajax_post(url, form_Data, callback) {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {

            try {
                var data = xmlhttp.responseText;
            } catch(err) {
                console.log(err.message + " in " + xmlhttp.responseText);
                return;
            }
            callback(data);
        }
    };

    xmlhttp.open("POST", url, true);
    xmlhttp.send(form_Data);
}

var currentPath = window.location.pathname;


function loadHomePage() {

}

var partialsCache = {}

function resolvePathAndTitle(path) {
    switch (path) {
        case '':
            return {
                htmlPath: '/html/home.html', title: 'Home', callback: loadHomePage
            }
            break;
        case 'Flip-Challenge':
            return {
                htmlPath: '/html/flip-challenge.html', title: 'Flip Challenge', callback: loadHomePage
            }
            break;
        case 'admin':
            return {
                htmlPath: '/html/admin.html', title: 'admin', callback: loadHomePage
            }
            break;
        case 'invite':
            return {
                htmlPath: '/html/invite.html', title: 'invite', callback: loadHomePage
            }
            break;
        case 'profile':
            return {
                htmlPath: '/html/profile.html', title: 'profile', callback: loadProfilePage
            }
            break;
        case 'quiz':
            return {
                htmlPath: '/html/quiz.html', title: 'quiz', callback: loadHomePage
            }
            break;
        case 'settings':
            return {
                htmlPath: '/html/settings.html', title: 'settings', callback: loadSettingsPage
            }
            break;
        
        default:
            return {
                htmlPath: '/html/home.html', title: 'Home', callback: loadHomePage
            }
    }
}

function navigate(path,hash = window.location.hash) {
    let PF = path.split('/')[1];
    if (partialsCache[PF]) {

        document.getElementById("content").innerHTML = partialsCache[PF];

        document.title = resolvePathAndTitle(PF).title;
        window.history.pushState({
            "html": partialsCache[PF],
            "pageTitle": resolvePathAndTitle(PF).title
        }, "", path+hash);

        return
    } else {

        ajax_get(resolvePathAndTitle(PF).htmlPath, function (data) {
            partialsCache[PF] = data;
            document.getElementById("content").innerHTML = data;
            document.title = resolvePathAndTitle(PF).title;
            window.history.pushState({
                "html": data,
                "pageTitle": resolvePathAndTitle(PF).title
            }, "", path+hash);
            resolvePathAndTitle(PF).callback();
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