Object.size = function (obj) {
    var size = 0,
        key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) size++;
    }
    return size;
};






function changeContent(id, text) {
    document.getElementById('content-' + id).innerHTML = text;
}

function loadProfilePage() {
    if (Number(window.location.pathname.split('/')[2]) > 0) {
        ajax_get('/api/app/checkID.php?id=' + Number(window.location.pathname.split('/')[2]), function (data) {
            data = JSON.parse(data);
            if (!data.error) {
                changeContent('id', data.id);
                changeContent('status', data.status);
                changeContent('joined', data.joined);
                changeContent('reports', data.reports);

                changeContent('flipChallengeScore', data.flipChallengeScore);
                changeContent('quizScore', data.quizScore);
                changeContent('socialScore', data.socialScore);
                changeContent('votes', data.votes);
                document.getElementById('content-image').src = 'https://robohash.org/' + data.image;


                if (data.accounts.length > 0) {
                    document.getElementById('content-accountsList').innerHTML = '<h5 class="m-0">Accounts connected</h5>';
                    data.accounts.forEach(key => {
                        document.getElementById('content-accountsList').innerHTML += '<dl>';
                        document.getElementById('content-accountsList').innerHTML += '<dt class="">' + key.name + '</dt>';
                        document.getElementById('content-accountsList').innerHTML += '<dd> - Creation Date : ' + timeConverter(key['creationTime']) + '</dd>';
                        document.getElementById('content-accountsList').innerHTML += '</dl>';

                    });
                } else {
                    document.getElementById('content-accountsList').innerHTML = '<h5 class="m-0">No accounts connected</h5>';

                }
                document.getElementById('pageLoading').classList.add('d-none');
                document.getElementById('pageContent').classList.remove('d-none');
            } else {
                document.getElementById('pageLoadingText').innerHTML = 'Error - account not found';
            }

        });
    } else {
        document.getElementById('pageLoadingText').innerHTML = 'Error - ID missing';
    }
}