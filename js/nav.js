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

    xmlhttp.open("POST", url, true);
    xmlhttp.send(form_Data);
}

function timeConverter(UNIX_timestamp, type = 'half') {
    var a = new Date(UNIX_timestamp * 1000);
    var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    var year = a.getFullYear();
    var month = months[a.getMonth()];
    var date = a.getDate();
    var hour = a.getHours();
    var min = a.getMinutes();
    var sec = a.getSeconds();
    if (type == 'full') {
        var time = date + ' ' + month + ' ' + year + ' ' + hour + ':' + min + ':' + sec;
    }

    if (type == 'half') {
        var time = date + ' ' + month + ' ' + year;
    }

    return time;
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
        case 'flipChallenge':
            return {
                htmlPath: '/html/flipChallenge.html', title: 'Flip Challenge', callback: loadFCPage
            }
            break;
        case 'admin':
            return {
                htmlPath: '/html/admin.html', title: 'admin', callback: loadHomePage
            }
            break;
        case 'invite':
            return {
                htmlPath: '/html/invite.html', title: 'invite', callback: loadInvitePage
            }
            break;
        case 'profile':
            return {
                htmlPath: '/html/profile.html', title: 'Profile', callback: loadProfilePage
            }
            break;
        case 'quiz':
            return {
                htmlPath: '/html/quiz.html', title: 'quiz', callback: loadQuizPage
            }
            break;
        case 'settings':
            return {
                htmlPath: '/html/settings.html', title: 'Settings', callback: loadSettingsPage
            }
            break;

        default:
            return {
                htmlPath: '/html/home.html', title: 'Home', callback: loadHomePage
            }
    }
}

function navigate(path) {
    let PF = path.split('/')[1];
    if (partialsCache[PF]) {
        document.getElementById("content").innerHTML = partialsCache[PF];
        document.title = resolvePathAndTitle(PF).title;
        window.history.pushState({
            "html": partialsCache[PF],
            "pageTitle": resolvePathAndTitle(PF).title,
            "PF": PF
        }, "", path);
        resolvePathAndTitle(PF).callback();
        return
    } else {
        ajax_get(resolvePathAndTitle(PF).htmlPath, function (data) {
            partialsCache[PF] = data;
            document.getElementById("content").innerHTML = data;
            document.title = resolvePathAndTitle(PF).title;
            window.history.pushState({
                "html": data,
                "pageTitle": resolvePathAndTitle(PF).title,
                "PF": PF
            }, "", path);
            resolvePathAndTitle(PF).callback();
        })
    }


}

window.onpopstate = function (e) {
    if (e.state) {
        document.getElementById("content").innerHTML = e.state.html;
        document.title = e.state.pageTitle;
        resolvePathAndTitle(e.state.PF).callback();
    }
};
navigate(currentPath);