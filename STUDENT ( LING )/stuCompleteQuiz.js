window.addEventListener("DOMContentLoaded", () => {
  displayQuestion();
});

function displayQuestion() {
  let content = document.querySelector("#content");
  let display_data =
    `
                <div class="title">
                    <h1>` +
    title +
    `</h1>
                </div>
                <div class="title">
                    <h1>SUBJECT: ` +
    subject +
    `</h1>
                    <h1>FORM: ` +
    form +
    `</h1>
                </div>`;

  question_data.forEach((element, index) => {
    display_data =
      display_data +
      `<div id="part">
                                <h1>QUESTION ` +
      (index + 1) +
      `</h1>
                                <div id="question">${element}</div>
                                <div id="answer">
                                </div>
                            </div>`;
  });
  content.innerHTML = display_data;
  displayAnswer();
}

function displayAnswer() {
  let section = document.querySelectorAll("#answer");
  section.forEach((element, index) => {
    let display_data = ``;
    // This is for the review page, not for answering!
    answers[index].forEach((ans, n) => {
      const isCorrect = correct_answers[index].includes(String(n));
      const isSelected = student_answer[index].includes(String(n));
      if (isCorrect && isSelected) {
        display_data += `<div style="background-color: var(--green); color: var(--grey)">${ans}</div>`;
      } else if (!isCorrect && isSelected) {
        display_data += `<div style="background-color: var(--red);">${ans}</div>`;
      } else if (isCorrect) {
        display_data += `<div style="background-color: var(--green); color: var(--grey)">${ans}</div>`;
      } else {
        display_data += `<div>${ans}</div>`;
      }
    });
    element.innerHTML = display_data;
  });
}
