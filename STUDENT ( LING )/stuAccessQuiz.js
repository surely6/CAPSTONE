window.addEventListener("DOMContentLoaded", () => {
  displayQuiz();
  displayQuestions();

  // const urlParams = new URLSearchParams(window.location.search);
  // const quizId = urlParams.get("quiz_id");
  // if (quizId) {
  //   displayQuiz();
  //   displayQuestions();
  // }
});

const id = quiz_id;

const detail_container = document.querySelector(".material_details");
function displayQuiz() {
  const display_data =
    `<section>
                                <div class="title_button"><button onclick="window.location.href='StuQuiz.php'"> < </button>` +
    quiz_data[0]["quiz_title"].toUpperCase() +
    `</div>
                                <hr>
                                <div class="material_detail_container">
                                    <div>SUBJECT: ` +
    quiz_data[0]["quiz_subject"].toUpperCase() +
    `</div>
                                    <div>CHAPTER: ` +
    quiz_data[0]["quiz_chapter"] +
    `</div>
                                    <div>FORM: ` +
    quiz_data[0]["quiz_level"] +
    ` </div>
                                </div>
                              </section>`;

  detail_container.innerHTML = display_data;
}

function Submit(quiz_id) {
  console.log(quiz_id);
  document.cookie = "Quiz_ID = " + quiz_id;
  window.location.href = "StuAccessQuiz.php";
}

const material_container = document.querySelector(".container");
function displayQuestions() {
  let display_data = '<button onclick="SubmitAnswer()">SUBMIT</button>';
  question_data.forEach((element, index) => {
    display_data =
      display_data +
      `<div class="quiz_section" id="` +
      element["question_id"] +
      `">
                                            <div id="head">
                                                <h1>QUESTION ` +
      (index + 1) +
      `:</h1>
                                            </div>
                                            <div class="questions">
                                                ` +
      element["question"] +
      `
                                            </div>
                                            <div class="answer_section">
                                            </div>
                                        </div>`;
  });
  material_container.innerHTML = display_data;
  displayAnswers();
}

function displayAnswers() {
  let answer_container = document.querySelectorAll(".answer_section");
  answer_container.forEach((element, index) => {
    let display_data = "";
    let answers = question_data[index]["answer_list"].split(",");
    let n = 0; // Reset n for each question!
    answers.forEach((options, i) => {
      if (question_data[index]["question_style_id"] == "Q02") {
        display_data += `<div class="answer">
            <div class="square">
              <input type="checkbox" id="correct-answer${index}-${n}" name="${index}" value="${n}" />
              <label for="correct-answer${index}-${n}"></label>
            </div>
            <p class="ans">${options}</p>
          </div>`;
      } else {
        display_data += `<div class="answer">
            <div class="round">
              <input type="radio" id="correct-answer${index}-${n}" name="${index}" value="${n}" />
              <label for="correct-answer${index}-${n}"></label>
            </div>
            <p class="ans">${options}</p>
          </div>`;
      }
      n++;
    });
    element.innerHTML = display_data;
  });
}

function SubmitAnswer() {
  let question_id = [];
  let StudentAnswer = [];
  let AnswerSection = document.querySelectorAll(".quiz_section");
  console.log(AnswerSection);
  AnswerSection.forEach((element) => {
    question_id.push(element.id);
    let temp = [""];
    element.querySelectorAll("input").forEach((input) => {
      if (input.checked) {
        temp.push(input.value);
      }
    });
    if (temp.length > 1) {
      temp.splice(0, 1);
    }
    StudentAnswer.push(temp);
  });
  console.log("Student Answers :", StudentAnswer);

  //finding the amount correct
  CorrectAmmount = 0;
  question_data.forEach((element, index) => {
    let correct = element["correct_answer"].split(",");
    if (JSON.stringify(correct) == JSON.stringify(StudentAnswer[index])) {
      CorrectAmmount++;
    }
  });

  console.log(CorrectAmmount);

  document.cookie = "attempt-id = " + attempt_id + ";";

  $.ajax({
    method: "POST",
    url: "stuCompleteQuiz.php",
    data: {
      attempt: attempt_id,
      quiz_id: quiz_id,
      student_id: student_id,
      questions: question_id,
      score: CorrectAmmount,
      answer: StudentAnswer,
    },
  }).done(function (response) {
    window.location.href = "stuCompleteQuiz.php";
    console.log(response);
  });
}
