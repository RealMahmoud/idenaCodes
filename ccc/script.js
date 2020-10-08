var currentFlipId;
var answers = [];
var FlipsArray = [];

function chooseRight() {
    answers[currentFlipId].answer = 'Right';
    document.getElementById('flipRight').classList.remove('ui-not-selected');
    document.getElementById('flipRight').classList.add('ui-selected');
    document.getElementById('flipLeft').classList.remove('ui-selected');
    document.getElementById('flipLeft').classList.add('ui-not-selected');
    console.log(answers);
}

function chooseLeft() {
    answers[currentFlipId].answer = 'Left';
    document.getElementById('flipLeft').classList.remove('ui-not-selected');
    document.getElementById('flipLeft').classList.add('ui-selected');
    document.getElementById('flipRight').classList.remove('ui-selected');
    document.getElementById('flipRight').classList.add('ui-not-selected');
    console.log(answers);
}

function noLeftnoRight() {
    document.getElementById('flipLeft').classList.remove('ui-selected');
    document.getElementById('flipRight').classList.remove('ui-selected');
}

function nextFlip() {
    if (currentFlipId >= getFlips().length-1) {
        return 'error'
    } else {
       
        loadFlip(currentFlipId + 1);
    }
    
}

function prevFlip() {
    if (currentFlipId <= 0) {
        return 'error'
    } else {
        
        loadFlip(currentFlipId - 1);
    }
}
function checkButtons(){
    if(currentFlipId == getFlips().length-1){
        document.getElementById('prevButton').classList.remove('disabled');
        document.getElementById('nextButton').classList.add('disabled');
    }else if(currentFlipId == 0){
        document.getElementById('nextButton').classList.remove('disabled');
        document.getElementById('prevButton').classList.add('disabled');
    }else{
        document.getElementById('nextButton').classList.remove('disabled');
        document.getElementById('prevButton').classList.remove('disabled');
    }
}
function loadFlip(id) {
    currentFlipId = id;
    loadAnswer(id);
    changeImage(1, getFlips()[id].images[getFlips()[id].orderLeft[0]])
    changeImage(2, getFlips()[id].images[getFlips()[id].orderLeft[1]])
    changeImage(3, getFlips()[id].images[getFlips()[id].orderLeft[2]])
    changeImage(4, getFlips()[id].images[getFlips()[id].orderLeft[3]])

    changeImage(5, getFlips()[id].images[getFlips()[id].orderRight[0]])
    changeImage(6, getFlips()[id].images[getFlips()[id].orderRight[1]])
    changeImage(7, getFlips()[id].images[getFlips()[id].orderRight[2]])
    changeImage(8, getFlips()[id].images[getFlips()[id].orderRight[3]])
    checkButtons();
}

function loadAnswer(id) {
    if (answers[id].answer == 'Right') {
        chooseRight();
    } else if (answers[id].answer == 'Left') {
        chooseLeft();
    } else {
        noLeftnoRight();
    }

}

function changeImage(id, image) {
    document.getElementById('image-' + id).src = image;
}

function getFlips() {
    return FlipsArray

}

function loadFlipsFromDB() {
    FlipsArray = [{

            orderLeft: [0, 1, 2, 3],
            orderRight: [3, 1, 2, 0],
            images: ['./images/image-1.png', './images/image-2.png', './images/image-3.png', './images/image-4.png']
        },
        {
            orderLeft: [0, 1, 2, 3],
            orderRight: [3, 1, 2, 0],
            images: ['./images/image-2.png', './images/image-2.png', './images/image-2.png', './images/image-2.png']
        }, {
            orderLeft: [0, 1, 2, 3],
            orderRight: [3, 1, 2, 0],
            images: ['./images/image-1.png', './images/image-3.png', './images/image-3.png', './images/image-3.png']
        }
    ];
    for (var i = 0; i <= FlipsArray.length-1; i++) {
        answers.push({
            answer: ''
        })
    }

}





//



var totalSeconds = 0;

function setTime() {
  ++totalSeconds;
 
  document.getElementById("validationTime").innerHTML = pad(parseInt(totalSeconds / 60)) +':'+pad(totalSeconds % 60);
}

function pad(val) {
  var valString = val + "";
  if (valString.length < 2) {
    return "0" + valString;
  } else {
    return valString;
  }
}
setInterval(setTime, 1000);
loadFlipsFromDB();
loadFlip(0);