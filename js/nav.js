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
        case '':
            return {
                htmlPath: '/html/home.html', title: 'Home'
            }
            break;
        case 'Flip-Challenge':
            return {
                htmlPath: '/html/flip-challenge.html', title: 'Flip Challenge'
            }
            break;
        case 'admin':
            return {
                htmlPath: '/html/admin.html', title: 'admin'
            }
            break;
        case 'invite':
            return {
                htmlPath: '/html/invite.html', title: 'invite'
            }
            break;
        case 'profile':
            return {
                htmlPath: '/html/profile.html', title: 'profile'
            }
            break;
        case 'quiz':
            return {
                htmlPath: '/html/quiz.html', title: 'quiz'
            }
            break;
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
        
        document.title = resolvePathAndTitle(PF).title;
        window.history.pushState({
            "html": partialsCache[PF],
            "pageTitle": resolvePathAndTitle(PF).title
        }, "", path);
        return
    } else {

        ajax_get(resolvePathAndTitle(PF).htmlPath, function (data) {
            partialsCache[PF] = data;
            document.getElementById("content").innerHTML = data;
            document.title = resolvePathAndTitle(PF).title;
            window.history.pushState({
                "html": data,
                "pageTitle": resolvePathAndTitle(PF).title
            }, "", path);
        })
    }


}
navigate(currentPath);
console.log(currentPath.split('/')[1]);

window.onpopstate = function (e) {
    if (e.state) {
        document.getElementById("content").innerHTML = e.state.html;
        document.title = e.state.pageTitle;
    }
};