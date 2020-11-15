function loadInvitePage() {

    checkLogged(loadInviteList,true);

    function loadInviteList(limit=10, skip=0) {



        ajax_get("/api/app/getList.php?skip=" + skip + "&limit=" + limit, function (data) {
            data = JSON.parse(data);
            if (data.error) {
                return
            }

            data.users.forEach(user => {
                accountsList = '';
                if (user.accounts.length > 0) {
                    accountsList = '<div class="row p-0 m-auto d-inline">';
                    user.accounts.forEach(account => {
                        accountsList += '<i class="fab fa-' + account + ' mr-2 mt-1"></i>';
                    });
                    accountsList += '</div>';

                }



                document.getElementById('usersList').innerHTML += '<div class="col-4 mb-2 mt-2">' +
                    '<div class="card w-100" height="150px"">' +
                    '<div class=" row p-0">' +
                    '<div class="col-4">' +
                    '<img class="rounded-circle border m-3 w-100" style="background-color:#00000057;"' +
                    'src="/api/images/?id=' + user.id + '" />' +
                    '</div>' +
                    '<div class="col-6">' +
                    '<div class="m-3">' +
                    '<p class="m-0">ID : ' + user.id + '</p>' +
                    '<p class="m-0">Votes : ' + user.votes + '</p>' +
                    '<p class="m-0">Score : ' + user.score + '</p>' +
                    accountsList +
                    '</div>' +
                    '</div>' +
                    '<div class="col-2" style="align-self: center;">' +
                    '<a class="text-center pointer" onclick="navigate(`/profile/' + user.id + '`)">' +
                    '<i class="fas fa-external-link-alt"></i>' +
                    '</a>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '</div>';

            });
        });





    }
    
}