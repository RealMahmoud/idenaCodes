function loadFCPage() {
    checkFCAvailability();
}
let flipsData;
let fcIndex = 0;
let fcAnswers = [];
var fctotalSeconds = 0;

function getFlips() {
    ajax_get('/api/flipsChallenge/getFlips.php', function (data) {
        data = JSON.parse(data);
        if (!data.error) {
            showFCForm();
            flipsData = data.flips;
            loadFlip(fcIndex);
            return
        } else {
            hideFCStartForm();
            toastr.error("Error : " + data.reason);
        }
    })
}

function startFC() {
    hideFCStartForm();
    getFlips();
}

function checkFCAvailability() {
    ajax_get('/api/flipsChallenge/checkScore.php', function (data) {
        data = JSON.parse(data);
        if (!data.score) {
            showFCStartForm();
        } else {
            showFCErrorForm();
        }
    });
}

function chooseRight() {
    console.log(fcAnswers);
    document.getElementById('fc-flipRight').classList.remove('ui-not-selected');
    document.getElementById('fc-flipRight').classList.add('ui-selected');
    document.getElementById('fc-flipLeft').classList.remove('ui-selected');
    document.getElementById('fc-flipLeft').classList.add('ui-not-selected');
    addFCAnswer(fcIndex, 1);
}

function chooseLeft() {
    console.log(fcAnswers);
    document.getElementById('fc-flipLeft').classList.remove('ui-not-selected');
    document.getElementById('fc-flipLeft').classList.add('ui-selected');
    document.getElementById('fc-flipRight').classList.remove('ui-selected');
    document.getElementById('fc-flipRight').classList.add('ui-not-selected');

    addFCAnswer(fcIndex, 0);
}

function noRightNoLeft() {
    document.getElementById('fc-flipLeft').classList.remove('ui-selected');
    document.getElementById('fc-flipRight').classList.remove('ui-selected');
    document.getElementById('fc-flipLeft').classList.add('ui-not-selected');
    document.getElementById('fc-flipRight').classList.add('ui-not-selected');
}

function nextFlip() {

    if (fcIndex >= (flipsData.length - 1)) {
        toastr.error('ERROR');
    } else {
        fcIndex = fcIndex + 1;
        loadFlip(fcIndex);
    }
}

function previousFlip() {
    if (fcIndex > 0) {


        fcIndex = fcIndex - 1;
        loadFlip(fcIndex);

    } else {
        toastr.error('ERROR');
    }
}

function submitFCAnswers() {
    ajax_post('/api/flipsChallenge/submitAnswers.php', JSON.stringify(fcAnswers), function (data) {
        data = JSON.parse(data);
        if (!data.error) {
            hideFCFrom();
            showFCEndForm();
            toastr.info("Submitted Successfully");
            if (data.score > 75) {
                toastr.success("Score : " + data.score);
            } else {
                toastr.warning("Score : " + data.score);
            }

        } else {
            toastr.error("Error : " + data.reason);
        }
    });
}

function loadFlip(fcIndex) {


    document.getElementById('fc-flipLeft').src = flipsData[fcIndex].url;
    document.getElementById('fc-flipRight').src = flipsData[fcIndex].url2;
    if (fcIndex == 0) {
        document.getElementById('fc-previousFlipButton').classList.add('disabled');
        document.getElementById('fc-nextFlipButton').classList.remove('disabled');

    } else if (fcIndex == flipsData.length - 1) {
        document.getElementById('fc-previousFlipButton').classList.remove('disabled');
        document.getElementById('fc-nextFlipButton').classList.add('disabled');
        

    } else {
        document.getElementById('fc-previousFlipButton').classList.remove('disabled');
        document.getElementById('fc-nextFlipButton').classList.remove('disabled');

    }


    document.getElementById('fc-count').innerHTML = '( ' + (fcIndex + 1) + ' of ' + (flipsData.length) + ' )';
    loadFCAnswer(flipsData[fcIndex].id);
}

function addFCAnswer(fcIndex, answer) {
    if (answer == null) {
        return
    }
    let duplicate = false;
    fcAnswers.forEach(answer => {
        if (answer.id == flipsData[fcIndex].id) {
            duplicate = true;
        }
    });
    if (duplicate) {
        fcAnswers.forEach(elem => {
            if (elem.id == flipsData[fcIndex].id) {
                elem.answer = answer;
            }
        });
    } else {
        fcAnswers.push({
            "id": flipsData[fcIndex].id,
            "answer": answer
        });
    }

}

function loadFCAnswer(id) {
    let c = null;
    fcAnswers.forEach(answer => {
        if (answer.id == id) {
            if (answer.answer == 0) {
                c = 1;
                chooseLeft();
            } else if (answer.answer == 1) {
                c = 1;
                chooseRight();
            }
        }
    });
    return c || noRightNoLeft();
}

function hideFCStartForm() {
    document.getElementById('fc-startForm').classList.add('d-none');
}

function showFCStartForm() {
    document.getElementById('fc-startForm').classList.remove('d-none');
}

function hideFCFrom() {
    document.getElementById('fc-testForm').classList.add('d-none');

}

function showFCForm() {
    document.getElementById('fc-testForm').classList.remove('d-none');

    setInterval(setTime, 1000);

}

function hideFCEndForm() {
    document.getElementById('fc-endForm').classList.add('d-none');
}

function showFCEndForm() {
    document.getElementById('fc-endForm').classList.remove('d-none');
}

function showFCErrorForm() {
    document.getElementById('fc-errorForm').classList.remove('d-none');
}






function setTime() {
    ++fctotalSeconds;
    if (flipsData.length == fcAnswers.length) {
        document.getElementById('fc-submitButton').disabled = false;
    }
    document.getElementById("fc-time").innerHTML = pad(parseInt(fctotalSeconds / 60)) + ':' + pad(fctotalSeconds % 60);
}

function pad(val) {
    var valString = val + "";
    if (valString.length < 2) {
        return "0" + valString;
    } else {
        return valString;
    }
}