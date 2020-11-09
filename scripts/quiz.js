function loadQuizPage() {
    checkAvailability();
}
let questionsData;
let index = 0;
let answers = [];

function getQuestions() {
    ajax_get('/api/quiz/getQuestions.php', function (data) {
        data = JSON.parse(data);
        if (!data.error) {
            showTestForm();
            questionsData = data.questions;
            loadQuestion(index);
            return
        } else {
            hideStartForm();
            toastr.error("Error : " + data.reason);
        }
    })
}

function startQuiz() {
    hideStartForm();
    getQuestions();
}

function checkAvailability() {
    ajax_get('/api/quiz/checkScore.php', function (data) {
        data = JSON.parse(data);
        if (!data.score) {
            showStartForm();
        } else {
            showErrorForm();
        }
    });
}

function nextQuestion() {

    if (index >= (questionsData.length - 1)) {
        document.querySelectorAll('input[name="option"]').forEach(element => {
            if (element.checked) {
                addAnswer(index, element.value);
            }
        });

        submitAnswers();
    } else {

        document.querySelectorAll('input[name="option"]').forEach(element => {
            if (element.checked) {
                addAnswer(index, element.value);
            }
        });



        index = index + 1;
        loadQuestion(index);
    }
}

function previousQuestion() {
    if (index > 0) {


        index = index - 1;
        loadQuestion(index);

    } else {
        toastr.error('ERROR - First question');
    }
}

function submitAnswers() {
    ajax_post('/api/quiz//submitAnswers.php', JSON.stringify(answers), function (data) {
        data = JSON.parse(data);
        if (!data.error) {
            hideTestFrom();
            showEndForm();
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

function loadQuestion(index) {
    console.log(answers);
    document.getElementById('quiz-question').innerHTML = questionsData[index].question;
    if (index == 0) {
        document.getElementById('quiz-previousQuestionButton').disabled = true;
        document.getElementById('quiz-nextQuestionButton').disabled = false;
    } else if (index == questionsData.length - 1) {
        document.getElementById('quiz-previousQuestionButton').disabled = false;

        document.getElementById('quiz-nextQuestionButton').innerHTML = 'Submit';
    } else {
        document.getElementById('quiz-previousQuestionButton').disabled = false;
        document.getElementById('quiz-nextQuestionButton').disabled = false;

        document.getElementById('quiz-nextQuestionButton').innerHTML = 'Next <i class="fa fa-angle-right ml-2"></i>';
    }
    document.getElementById("quiz-questions").innerHTML = "";
    let count = 0;
    questionsData[index].options.forEach(option => {
        document.getElementById("quiz-questions").innerHTML += `<div class="form-check ml-2">` +
            `<input class="form-check-input" type="radio" name="option" id="option-${count}"` +
            `value="${count}" >` +
            `<label class="form-check-label" for="option-${count}">` + option +
            `</label>` +
            `</div>`;
        count++;
    });

    document.getElementById('quiz-count').innerHTML = '( ' + (index + 1) + ' of ' + (questionsData.length) + ' )';
    loadAnswer(questionsData[index].id);
}

function addAnswer(index, answer) {
    if (answer == null) {
        return
    }
    let duplicate = false;
    answers.forEach(answer => {
        if (answer.id == questionsData[index].id) {
            duplicate = true;
        }
    });
    if (duplicate) {
        answers.forEach(elem => {
            if (elem.id == questionsData[index].id) {
                elem.answer = answer;
            }
        });
    } else {
        answers.push({
            "id": questionsData[index].id,
            "answer": answer
        });
    }

}

function loadAnswer(id) {
    answers.forEach(answer => {
        if (answer.id == id) {
            document.getElementById("option-" + answer.answer).checked = true;
        }
    });
}

function hideStartForm() {
    document.getElementById('quiz-startForm').classList.add('d-none');
}

function showStartForm() {
    document.getElementById('quiz-startForm').classList.remove('d-none');
}

function hideTestFrom() {
    document.getElementById('quiz-testFrom').classList.add('d-none');
}

function showTestForm() {
    document.getElementById('quiz-testFrom').classList.remove('d-none');
}

function hideEndForm() {
    document.getElementById('quiz-endForm').classList.add('d-none');
}

function showEndForm() {
    document.getElementById('quiz-endForm').classList.remove('d-none');
}

function showErrorForm() {
    document.getElementById('quiz-errorForm').classList.remove('d-none');
}