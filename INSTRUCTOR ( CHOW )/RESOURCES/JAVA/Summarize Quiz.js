console.log(StudentFail);

window.addEventListener("load", function () {
  SetContent();
});

let Information = document.querySelector("#information");
let Buttons = document.querySelectorAll(".button span");
let SelectStatus = document.querySelector("#status");
let SelectTime = document.querySelector("#time");

function SetContent() {
  SelectStatus.style.display = "none";
  SelectTime.style.display = "none";
  Buttons[0].style.borderBottom = "4px var(--grey) solid";
  Buttons[1].style.borderStyle = "none";
  Buttons[2].style.borderStyle = "none";
  let content =
    `<div id="content">
                    <div class="title">
                        <h1>SUBJECT: ` +
    Quiz["subject"].toUpperCase() +
    `</h1>
                        <h1>FORM: ` +
    Quiz["form"] +
    `</h1>
                    </div>
                    <div class="section">
                        <h1><span>TITLE:</span></h1>
                        <p id="title">` +
    Quiz["title"] +
    `</p>
                        <br>
                        <h1><span>DESCRIPTION:</span></h1>
                        <p id="description">` +
    Quiz["description"] +
    `</p>
                </div>`;
  Information.innerHTML = content;
  SetQuestions();
}

function SetQuestions() {
  let section = document.querySelector("#content");
  let content = section.innerHTML;
  Quiz["question"].forEach((element, index) => {
    content =
      content +
      `
        <div id="part">
        <h1>QUESTION ${index + 1}<div style="float: right;">${
        Quiz["type"][index]
      }</div></h1>
        <div id="question">${element}</div>
        <div id="answer"></div>
    </div>
    `;
  });
  section.innerHTML = content;
  SetAnswers();
}

function SetAnswers() {
  let section = document.querySelectorAll("#answer");
  section.forEach((element, index) => {
    let inputs = "";
    Quiz["answer"][index].forEach((ans, i) => {
      let isCorrect =
        Quiz["correct"][index] && Quiz["correct"][index].includes(i.toString());
      inputs += `<div style="background-color: ${
        isCorrect ? "var(--green)" : "var(--grey)"
      }; color: black;">${ans}</div>`;
    });
    element.innerHTML = inputs;
  });
}

function SetUser() {
  console.log("user");
  SelectStatus.style.display = "block";
  SelectTime.style.display = "block";
  Buttons[0].style.borderStyle = "none";
  Buttons[1].style.borderBottom = "4px var(--grey) solid";
  Buttons[2].style.borderStyle = "none";
  let content = `<div id="users">
                    <table>
                        <tr>
                            <th style="width: 10%;">NO</th>
                            <th style="width: 45%;">NAME</th>
                            <th style="width: 20%;">SCORE</th>
                            <th style="width: 25%;">DATE DONE</th>
                        </tr>
                    </table>
                </div>`;
  Information.innerHTML = content;
  SetTableRow();
}

function SetTableRow() {
  let status = SelectStatus.value;
  let time = SelectTime.value;
  let no = 1;
  let table = document.querySelector("#users table");
  let content = table.innerHTML;
  if (status == "all") {
    if (time == "latest") {
      StudentPass.forEach((element) => {
        content =
          content +
          `
                <tr>
                    <td>` +
          no +
          `</td>
                    <td>` +
          element["name"] +
          `</td>
                    <td style="color: var(--green);">` +
          element["score"] +
          `/` +
          TotalQuestion +
          `</td>
                    <td>` +
          element["date"] +
          `</td>
               </tr>
                `;
        no++;
      });
      StudentFail.forEach((element) => {
        content =
          content +
          `
                <tr>
                    <td>` +
          no +
          `</td>
                    <td>` +
          element["name"] +
          `</td>
                    <td style="color: var(--red);">` +
          element["score"] +
          `/` +
          TotalQuestion +
          `</td>
                    <td>` +
          element["date"] +
          `</td>
               </tr>
                `;
        no++;
      });
    } else {
      for (let i = StudentPass.length - 1; i >= 0; i--) {
        content =
          content +
          `
                <tr>
                    <td>` +
          no +
          `</td>
                    <td>` +
          StudentPass[i]["name"] +
          `</td>
                    <td style="color: var(--green);">` +
          StudentPass[i]["score"] +
          `/` +
          TotalQuestion +
          `</td>
                    <td>` +
          StudentPass[i]["date"] +
          `</td>
               </tr>
                `;
        no++;
      }
      for (let i = StudentFail.length - 1; i >= 0; i--) {
        content =
          content +
          `
                <tr>
                    <td>` +
          no +
          `</td>
                    <td>` +
          StudentFail[i]["name"] +
          `</td>
                    <td style="color: var(--red);">` +
          StudentFail[i]["score"] +
          `/` +
          TotalQuestion +
          `</td>
                    <td>` +
          StudentFail[i]["date"] +
          `</td>
               </tr>
                `;
        no++;
      }
    }
  } else if (status == "pass") {
    if (time == "latest") {
      StudentPass.forEach((element) => {
        content =
          content +
          `
                <tr>
                    <td>` +
          no +
          `</td>
                    <td>` +
          element["name"] +
          `</td>
                    <td style="color: var(--green);">` +
          element["score"] +
          `/` +
          TotalQuestion +
          `</td>
                    <td>` +
          element["date"] +
          `</td>
               </tr>
                `;
        no++;
      });
    } else {
      for (let i = StudentPass.length - 1; i >= 0; i--) {
        content =
          content +
          `
                <tr>
                    <td>` +
          no +
          `</td>
                    <td>` +
          StudentPass[i]["name"] +
          `</td>
                    <td style="color: var(--green);">` +
          StudentPass[i]["score"] +
          `/` +
          TotalQuestion +
          `</td>
                    <td>` +
          StudentPass[i]["date"] +
          `</td>
               </tr>
                `;
        no++;
      }
    }
  } else {
    if (time == "latest") {
      StudentFail.forEach((element) => {
        content =
          content +
          `
                <tr>
                    <td>` +
          no +
          `</td>
                    <td>` +
          element["name"] +
          `</td>
                    <td style="color: var(--red);">` +
          element["score"] +
          `/` +
          TotalQuestion +
          `</td>
                    <td>` +
          element["date"] +
          `</td>
               </tr>
                `;
        no++;
      });
    } else {
      for (let i = StudentFail.length - 1; i >= 0; i--) {
        content =
          content +
          `
                <tr>
                    <td>` +
          no +
          `</td>
                    <td>` +
          StudentFail[i]["name"] +
          `</td>
                    <td style="color: var(--red);">` +
          StudentFail[i]["score"] +
          `/` +
          TotalQuestion +
          `</td>
                    <td>` +
          StudentFail[i]["date"] +
          `</td>
               </tr>
                `;
        no++;
      }
    }
  }
  table.innerHTML = content;
}

function SetFeedback() {
  SelectStatus.style.display = "none";
  SelectTime.style.display = "block";
  Buttons[0].style.borderStyle = "none";
  Buttons[1].style.borderStyle = "none";
  Buttons[2].style.borderBottom = "4px var(--grey) solid";
  let content = `<div id="feedback"></div>`;
  Information.innerHTML = content;
  let feedback = document.querySelector("#feedback");
  let detail = "";
  let time = SelectTime.value;
  if (time == "latest") {
    Feedback.forEach((element) => {
      detail =
        detail +
        `<div id="info">
                                    <h1>` +
        element["name"] +
        `</h1>
                                    <span>Created On ` +
        element["date"] +
        `</span>
                                    <p>` +
        element["feedback"] +
        `</p>
                                </div>`;
    });
  } else {
    for (let i = Feedback.length - 1; i >= 0; i--) {
      detail =
        detail +
        `<div id="info">
                                    <h1>` +
        Feedback[i]["name"] +
        `</h1>
                                    <span>Created On ` +
        Feedback[i]["date"] +
        `</span>
                                    <p>` +
        Feedback[i]["feedback"] +
        `</p>
                                </div>`;
    }
  }

  feedback.innerHTML = detail;
}

SelectStatus.addEventListener("change", function () {
  console.log("change");
  SetUser();
});

SelectTime.addEventListener("change", function () {
  if (SelectStatus.style.display == "none") {
    SetFeedback();
  } else {
    SetUser();
  }
});
