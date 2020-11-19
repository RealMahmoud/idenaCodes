function loadHomePage() {

    checkLogged(viewHomePage, false);

    function viewHomePage() {
        loadCounter();
    }
}

function loadCounter() {
    ajax_get('https://api.idena.org/api/OnlineMiners/Count', function (data) {
        data = JSON.parse(data);
        document.getElementById("home-onlineMinersTotal").innerHTML = data.result;
    });

    ajax_get('https://api.idena.org/api/OnlineIdentities/Count', function (data) {
        data = JSON.parse(data);
        document.getElementById("home-validatedTotal").innerHTML = data.result;
    });



    ajax_get('https://api.idena.org/api/epoch/last', function (data) {
        data = JSON.parse(data);
        document.getElementById("home-nextValidationDateTime").innerHTML = moment.utc(data.result.validationTime).local().format('YYYY-MM-DD HH:mm A');
        var countDownDate = new Date(data.result.validationTime).getTime();
        var x = setInterval(function () {
            var now = new Date().getTime();
            var distance = countDownDate - now;
            var days = Math.floor(distance / (1000 * 60 * 60 * 24));
            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);

            if (!(document.getElementById("home-days") == null)) {
                document.getElementById("home-days").innerHTML = days;
                document.getElementById("home-hours").innerHTML = hours;
                document.getElementById("home-minutes").innerHTML = minutes;
                document.getElementById("home-seconds").innerHTML = seconds;
            }

        }, 1000);
    });


}