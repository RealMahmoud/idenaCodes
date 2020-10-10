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
function loadProfilePage(){
if (Number(window.location.pathname.split('/')[2]) > 0) {
    ajax_get('/api/app/checkID.php?id=' + Number(window.location.pathname.split('/')[2]), function (data) {
        data = JSON.parse(data);
        if (!data.error) {
            changeContent('id', data.id);
            changeContent('status', data.status);
            changeContent('referredBy', data.referredBy);
            changeContent('joined', data.joined);
            changeContent('reports', data.reports);

            changeContent('flipChallengeScore', data.flipChallengeScore);
            changeContent('quizScore', data.quizScore);
            changeContent('socialScore', data.socialScore);
            changeContent('bio', data.bio);
            changeContent('trustScore', data.trustScore);
            document.getElementById('content-image').src = 'https://robohash.org/' + data.image;




            if (Object.size(data.contacts) > 0) {
                document.getElementById('content-contactList').innerHTML =
                '<h5 class="m-0">Contact at</h5>';
                Object.keys(data.contacts).forEach(key => {
                    document.getElementById('content-contactList').innerHTML = document
                        .getElementById('content-contactList').innerHTML + '<p class="m-0">' + key +
                        ' : <span>@' + data.contacts[key] + '</span></p>';
                });
            }else{
                document.getElementById('content-contactList').innerHTML = '';
            }

            if (data.connected.length > 0) {
                document.getElementById('content-accountsList').innerHTML =
                    '<h5 class="m-0">Accounts verified</h5>';
                data.connected.forEach(key => {
                    document.getElementById('content-accountsList').innerHTML = document
                        .getElementById('content-accountsList').innerHTML + '<p class="m-0">' +
                        key + '</p>';
                });
            }else{
                document.getElementById('content-accountsList').innerHTML = '';
            }




            document.getElementById('pageLoading').classList.add('d-none');
            document.getElementById('pageContent').classList.remove('d-none');
        } else {
            document.getElementById('pageLoadingText').innerHTML = 'Error - account not found';
        }

    });
} else {
    document.getElementById('pageLoadingText').innerHTML = 'Error - ID missing';
}}