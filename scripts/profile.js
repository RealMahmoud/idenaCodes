Object.size = function (obj) {
    var size = 0,
        key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) size++;
    }
    return size;
};






function changeContentProfile(id, text) {
    document.getElementById('profile-' + id).innerHTML = text;
}

function loadProfilePage() {
    checkLogged(viewProfilePage,true);
}
function viewProfilePage(){
    if (Number(window.location.pathname.split('/')[2]) > 0) {
        ajax_get('/api/app/checkID.php?id=' + Number(window.location.pathname.split('/')[2]), function (data) {
            data = JSON.parse(data);
            if (!data.error) {
                changeContentProfile('id', data.id);
                changeContentProfile('status', data.status);
                changeContentProfile('joined', timeConverter(data.joined));
                changeContentProfile('lastSeen', timeConverter(data.lastSeen));
                changeContentProfile('reports', data.reports);

                changeContentProfile('flipChallengeScore', data.flipChallengeScore);
                changeContentProfile('quizScore', data.quizScore);
                changeContentProfile('votes', data.votes);
                changeContentProfile('ipCount', data.ipCount);
                changeContentProfile('country', data.country);
                document.getElementById('profile-image').src = '/api/images/?id=' + data.id;
                if (data.accounts.length > 0) {
                    document.getElementById('profile-accountsList').innerHTML = '<h5 class="m-0">Accounts connected</h5>';
                    data.accounts.forEach(key => {
                        document.getElementById('profile-accountsList').innerHTML += '<dl>';
                        document.getElementById('profile-accountsList').innerHTML += '<dt class="">' + key.name + '</dt>';
                        document.getElementById('profile-accountsList').innerHTML += '<dd> - Creation Date : ' + timeConverter(key['creationTime']) + '</dd>';
                        document.getElementById('profile-accountsList').innerHTML += '</dl>';
                    });
                } else {
                    document.getElementById('profile-accountsList').innerHTML = '<h5 class="m-0">No accounts connected</h5>';
                }
                document.getElementById('pageLoading').classList.add('d-none');
                document.getElementById('pageContent').classList.remove('d-none');


                if(data.inviteAbility){
                    document.getElementById('profile-inviteCard').classList.remove('d-none');
                }
                if(data.votingAbility){
                    document.getElementById('profile-votedown').disabled = false;
                    document.getElementById('profile-voteup').disabled = false;
                }
                      
            } else {
                document.getElementById('pageLoadingText').innerHTML = 'Error - account not found';
            }

        });
    } else {
        document.getElementById('pageLoadingText').innerHTML = 'Error - ID missing';
    }

}
function vote(type) {
    if (!type == 'up' || !type == 'down') {

        return
    }
    if (type == 'up') {
        type = 1;
    }
    if (type == 'down') {
        type = 0;
    }
    var formData = new FormData();
    formData.append('type', type);
    formData.append('forID',  Number(window.location.pathname.split('/')[2]));
    ajax_post('/api/app/vote.php', formData, function (data) {
        data = JSON.parse(data);
        if (data.error) {
            toastr.error('ERROR : '+data.reason);
        } else {
            
            changeContentProfile('votes', data.votes);
            toastr.success('Vote successfully broadcasted');
        }
    })
}

function sendInvite(){
    var formData = new FormData();
    formData.append('invite', document.getElementById("profile-inviteCode").value);
    formData.append('forID', Number(window.location.pathname.split('/')[2]));
    ajax_post('/api/invite/send.php', formData, function (data) {
        data = JSON.parse(data);
        if (data.error) {
            toastr.error('ERROR : '+data.reason);
        } else {
            toastr.success('Invite sent');
        }
    })
}
function sendReport(){
    var formData = new FormData();
    formData.append('report', document.getElementById("profile-report").value);
    formData.append('forID', Number(window.location.pathname.split('/')[2]));
    ajax_post('/api/app/report.php', formData, function (data) {
        data = JSON.parse(data);
        if (data.error) {
            toastr.error('ERROR : '+data.reason);
        } else {
            toastr.success('Reported');
        }
    })
}