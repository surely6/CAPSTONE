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
    Material["subject"] +
    `</h1>
                        <h1>CHAPTER: ` +
    Material["chapter"] +
    `</h1>
                        <h1>FORM: ` +
    Material["form"] +
    `</h1>
                        <h1>LEARNING STYLE: ` +
    Material["learning_style"] +
    `</h1>
                    </div>
                    <div class="section">
                        <h1><span>TITLE:</span></h1>
                        <p id="title">` +
    Material["title"] +
    `</p>
                        <br>
                        <h1><span>DESCRIPTION:</span></h1>
                        <p id="description">` +
    Material["description"] +
    `</p>
                </div>`;
  Information.innerHTML = content;
  SetPart();
}

function SetPart() {
  let section = document.querySelector("#content");
  let content = section.innerHTML;
  Material["content"].forEach((element, index) => {
    content =
      content +
      `
        <div id="part">
            <h1>PART ` +
      (index + 1) +
      `</h1>
            <div class="part-content">` +
      element +
      `</div>
        </div>
        `;
  });
  section.innerHTML = content;
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
                            <th style="width: 20%;">COMPLETION</th>
                            <th style="width: 25%;">LAST DONE</th>
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
      StudentCompleted.forEach((element) => {
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
                    <td style="color: var(--green);">100%</td>
                    <td>` +
          element["date"] +
          `</td>
               </tr>
                `;
        no++;
      });
      StudentInProgress.forEach((element) => {
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
                    <td style="color: var(--orange);">` +
          element["progress"] +
          `%</td>
                    <td>` +
          element["date"] +
          `</td>
               </tr>
                `;
        no++;
      });
    } else {
      for (let i = StudentCompleted.length - 1; i >= 0; i--) {
        content =
          content +
          `
                <tr>
                    <td>` +
          no +
          `</td>
                    <td>` +
          StudentCompleted[i]["name"] +
          `</td>
                    <td style="color: var(--green);">100%</td>
                    <td>` +
          StudentCompleted[i]["date"] +
          `</td>
               </tr>
                `;
        no++;
      }
      for (let i = StudentInProgress.length - 1; i >= 0; i--) {
        content =
          content +
          `
                <tr>
                    <td>` +
          no +
          `</td>
                    <td>` +
          StudentInProgress[i]["name"] +
          `</td>
                    <td style="color: var(--orange);">` +
          StudentInProgress[i]["progress"] +
          `%</td>
                    <td>` +
          StudentInProgress[i]["date"] +
          `</td>
               </tr>
                `;
        no++;
      }
    }
  } else if (status == "completed") {
    if (time == "latest") {
      StudentCompleted.forEach((element) => {
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
                    <td style="color: var(--green);">100%</td>
                    <td>` +
          element["date"] +
          `</td>
               </tr>
                `;
        no++;
      });
    } else {
      for (let i = StudentCompleted.length - 1; i >= 0; i--) {
        content =
          content +
          `
                <tr>
                    <td>` +
          no +
          `</td>
                    <td>` +
          StudentCompleted[i]["name"] +
          `</td>
                    <td style="color: var(--green);">100%</td>
                    <td>` +
          StudentCompleted[i]["date"] +
          `</td>
               </tr>
                `;
        no++;
      }
    }
  } else {
    if (time == "latest") {
      for (let i = StudentInProgress.length - 1; i >= 0; i--) {
        content =
          content +
          `
                <tr>
                    <td>` +
          no +
          `</td>
                    <td>` +
          StudentInProgress[i]["name"] +
          `</td>
<td style="color: var(--orange);">` +
          StudentInProgress[i]["progress"] +
          `%</td>                    <td>` +
          StudentInProgress[i]["date"] +
          `</td>
               </tr>
                `;
        no++;
      }
    } else {
      StudentInProgress.forEach((element) => {
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
                    <td style="color: var(--orange);">` +
          element["progress"] +
          `%</td>
                    <td>` +
          element["date"] +
          `</td>
               </tr>
                `;
        no++;
      });
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
