window.addEventListener("DOMContentLoaded", () => {
  displayRecommendQuiz();

  allMaterial.forEach((checkbox) => {
    checkbox.addEventListener("change", (event) => {
      const isChecked = event.target.checked;
      selectedCheckbox(isChecked);
    });
  });

  textInput.addEventListener("input", applyFilter);
  formCheckbox.forEach((checkbox) =>
    checkbox.addEventListener("change", applyFilter)
  );
  chapCheckbox.forEach((checkbox) =>
    checkbox.addEventListener("change", applyFilter)
  );
  matCheckbox.forEach((checkbox) =>
    checkbox.addEventListener("change", applyFilter)
  );

  const urlParams = new URLSearchParams(window.location.search);
  const quizId = urlParams.get("quiz_id");
  if (quizId) {
    show(quizId);
  }
});

var modal = document.getElementById("modal");

// When the user clicks on show btn, open the filter sec
function ShowFilter() {
  var filter = document.getElementById("filter");
  if (filter.className == "close") {
    filter.classList.replace("close", "show");
  } else {
    filter.classList.add("show");
  }

  var mainList = document.getElementsByTagName("main");
  var main = mainList[0];
  main.style.marginLeft = "17vw";
  main.style.marginRight = "0";
  main.style.width = "80vw";
}
// When the user clicks on close btn, close the filter sec
function CloseFilter() {
  var filter = document.getElementById("filter");
  filter.classList.replace("show", "close");

  var mainList = document.getElementsByTagName("main");
  var main = mainList[0];
  main.style.marginLeft = "5vw";
  main.style.marginRight = "5vw";
  main.style.width = "90vw";
}
function ShowDropdown(selected) {
  selected.classList.toggle("active");
}

let recommend_list = [];
const recommend_quiz_container = document.querySelector(".recommend_container");
function displayRecommendQuiz() {
  let recommend_quiz = [];

  student_data.forEach((data) => {
    const attempted_quiz = attempt_data.filter(
      (aq) => aq.student_id === data.student_id
    );
    if (!attempted_quiz) {
      return;
    }
    attempted_quiz.forEach((att) => {
      const stu_attempt = quiz_data.find((sa) => sa.quiz_id === att.quiz_id);

      if (!stu_attempt) {
        return;
      }
      // Find whether the quiz has been attempted by the student & if the score higher then half of the total questions?
      // if YES, find quiz created by the same instructor & put into "recommend_quiz"
      // if NO, find quiz created by the other instrcutor & put into "recommend_quiz"
      const match = quiz_data.find((q) => q.quiz_id === stu_attempt.quiz_id);
      if (match) {
        const total_questions = match.quiz_total_questions;

        if (att.score > Math.round(total_questions / 2)) {
          const same_instructor_quizzes = quiz_data.filter(
            (quiz) =>
              quiz.instructor_id === stu_attempt.instructor_id &&
              quiz.quiz_subject === stu_attempt.quiz_subject &&
              (quiz.quiz_chapter > stu_attempt.quiz_chapter ||
                quiz.quizhapter < stu_attempt.quiz_chapter) &&
              quiz.quiz_level === stu_attempt.quiz_level
          );
          recommend_quiz.push(...same_instructor_quizzes);
        } else {
          const other_instructor_quizzes = quiz_data.filter(
            (quiz) =>
              quiz.instructor_id !== stu_attempt.instructor_id &&
              quiz.quiz_subject === stu_attempt.quiz_subject &&
              quiz.quiz_chapter === stu_attempt.quiz_chapter &&
              quiz.quiz_level === stu_attempt.quiz_level
          );
          recommend_quiz.push(...other_instructor_quizzes);
        }
      }
    });

    const student_read = material_data.filter(
      (sr) => sr.student_id === data.student_id
    );

    if (!student_read) {
      return;
    }
    // If the material with the exact same chapter, level, & subject have been read
    // The respective quiz that is created by the same instructor will be put into "recommend_quiz"
    const progress_find = progress_data.find(
      (pro) => pro.material_id === student_read.material_id
    );
    if (progress_find) {
      const total_progress = progress_find.progress.split(", ");

      if (total_progress.length > 0) {
        const material_read = quiz_data.filter(
          (quiz) =>
            quiz.instructor_id === student_read.instructor_id &&
            quiz.quiz_subject === student_read.material_subject &&
            quiz.quiz_chapter === student_read.material_chapter &&
            quiz.quiz_level === student_read.material_level
        );
        recommend_quiz.push(...material_read);
      }
    }

    const match_pathway = pathway_data
      .filter((mp) => mp.student_id === data.student_id)
      .map((mp) =>
        material_data.find((md) => md.material_id === mp.material_id)
      );

    if (!match_pathway.length === 0) {
      return;
    }
    match_pathway.forEach((quiz) => {
      // const pathway_find = pathway_data.find(mm => mm.material_id === quiz.material_id);
      if (quiz) {
        const recommend_pathway_subject = quiz_data.filter(
          (q) =>
            q.quiz_subject === quiz.material_subject &&
            q.quiz_chapter === quiz.material_chapter &&
            q.quiz_level === quiz.material_level
        );
        recommend_quiz.push(...recommend_pathway_subject);
      }
    });
  });

  // Display the recommended materials
  let display_data = [];
  const all_matches = new Set(recommend_quiz);
  recommend_list = [...all_matches];
  all_matches.forEach((rq) => {
    const isBookmarked = progress_result.some((b) => b.quiz_id === rq.quiz_id); // Check if material is bookmarked
    const bookmarkClass = isBookmarked ? "bookmarked" : "";
    const bookmarkIcon = isBookmarked
      ? `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-bookmark-fill" viewBox="0 0 16 16">
                        <path d="M2 2v13.5a.5.5 0 0 0 .74.439L8 13.069l5.26 2.87A.5.5 0 0 0 14 15.5V2a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2"/>
                    </svg> Unmark`
      : `<svg id="bookmarkIcon" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="24" height="24" viewBox="0 0 24 24">
                        <path d="M 6 2 C 4.8444444 2 4 2.9666667 4 4 L 4 22.039062 L 12 19.066406 L 20 22.039062 L 20 20.599609 L 20 4 C 20 3.4777778 19.808671 2.9453899 19.431641 2.5683594 C 19.05461 2.1913289 18.522222 2 18 2 L 6 2 z M 6 4 L 18 4 L 18 19.162109 L 12 16.933594 L 6 19.162109 L 6 4 z"></path>
                    </svg> Bookmark`;
    if (!isAttempted(rq.quiz_id)) {
      display_data.push(
        `<section class="material ${rq.quiz_id}" onclick="show('${
          rq.quiz_id
        }')">
                        <div class="title">
                            <p>${rq.quiz_title.toUpperCase()}</p>

                            <div class="dropdown">
                                <button onclick="showDropdown(this, event)" class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" width="30" height="30" viewBox="0 0 100 100" id="more">
                                        <g id="_x37_7_Essential_Icons">
                                            <path id="More_Details__x28_3_x29_" d="M50 12c-21 0-38 17-38 38s17 38 38 38 38-17 38-38-17-38-38-38zm0 72c-18.8 0-34-15.2-34-34s15.2-34 34-34 34 15.2 34 34-15.2 34-34 34zm0-41c-3.9 0-7 3.1-7 7s3.1 7 7 7 7-3.1 7-7-3.1-7-7-7zm0 10c-1.7 0-3-1.4-3-3 0-1.7 1.3-3 3-3s3 1.3 3 3c0 1.6-1.3 3-3 3zm20-10c-3.9 0-7 3.1-7 7s3.1 7 7 7 7-3.1 7-7-3.1-7-7-7zm0 10c-1.7 0-3-1.4-3-3 0-1.7 1.3-3 3-3s3 1.3 3 3c0 1.6-1.3 3-3 3zM30 43c-3.9 0-7 3.1-7 7s3.1 7 7 7 7-3.1 7-7-3.1-7-7-7zm0 10c-1.7 0-3-1.4-3-3 0-1.7 1.3-3 3-3s3 1.3 3 3c0 1.6-1.3 3-3 3z"></path>
                                        </g>
                                    </svg>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item ${bookmarkClass}" 
                                    href="#;" 
                                    onclick="toggleBookmark(this, '${
                                      rq.quiz_id
                                    }', '${student_id}'); event.stopPropagation();">
                                        ${bookmarkIcon}
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="progress">
                            <div id="show-progress">SCORE: <div>0</div></div>
                            <button>NOT ATTEMPT</button>
                        </div>
                    </section>`
      );
    } else {
      return;
    }
  });

  console.log("Recommended Quizzes:", all_matches);
  display_data = display_data.join("");
  recommend_quiz_container.innerHTML = display_data;
}

function isAttempted(quiz_id) {
  let att = false;
  student_data.forEach((data) => {
    const attempted = attempt_data.filter(
      (aq) => aq.student_id === data.student_id && aq.quiz_id === quiz_id
    );
    if (attempted.length > 0) {
      att = true;
    }
  });
  return att;
}

// A small window that display details about the selected quiz
const quiz_descript_container = document.querySelector(".modal-content");
function displaySelectedQuiz(quiz) {
  let display_data = quiz.map(function (grid_item) {
    modal.className = "modal-content " + `${grid_item.quiz_title}`;
    return `<p><span class="close_button ${
      grid_item.quiz_title
    }" onclick="closeModal()">&#10006;</span></p>
                <center>
                    <div class="title">
                        <h1>${grid_item.quiz_title.toUpperCase()}</h1>
                    </div>
                    <div class="subject">
                        <h3>SUBJECT: ${grid_item.quiz_subject.toUpperCase()}</h3>
                    </div>
                    <div class="name">
                        <h3>INSTRUCTOR NAME: ${grid_item.instructor_name.toUpperCase()}</h3>
                    </div>
                    <div class="subject">
                        <h3>CHAPTER: ${grid_item.quiz_chapter}</h3>
                    </div>
                    <div class="parts">
                        <h3>TOTAL QUESTIONS: ${
                          grid_item.quiz_total_questions
                        }</h3>
                    </div>
                    <button class="start" onclick="Begin(${
                      grid_item.quiz_id
                    })">BEGIN ATTEMPT</button>  
                </center>`;
  });
  display_data = display_data.join("");
  quiz_descript_container.innerHTML = display_data;
}

function Begin(quiz_id) {
  console.log(quiz_id);
  document.cookie = "Quiz_ID = " + quiz_id;
  window.location.href = "StuAccessQuiz.php";
}

function showDropdown(button, event) {
  button.classList.toggle("active");
  event.stopPropagation();
}

// Display the selected quiz's details
function show(quiz_id) {
  let quiz_selected = quiz_data.filter(function (data) {
    if (data.quiz_id == quiz_id) {
      return data;
    }
  });
  displaySelectedQuiz(quiz_selected);
  modal.showModal();
}
// Close the selected quiz's details
function closeModal() {
  modal.close();
}

const textInput = document.getElementById("search");
const allMaterial = document.querySelectorAll(".all");
const formCheckbox = document.querySelectorAll(".formCheckbox");
const chapCheckbox = document.querySelectorAll(".chapCheckbox");
const matCheckbox = document.querySelectorAll(".matCheckbox");
const selected_mat = document.getElementById("selected_mat");
const all_mat = document.getElementById("all_mat");
const all_quiz_container = document.querySelector(".everything_container");

function clearQuizList() {
  const isAllVisible = all_mat.style.display === "block";
  const container = isAllVisible
    ? all_quiz_container
    : recommend_quiz_container;
  const entireQuizList = container.querySelectorAll("section.material");
  entireQuizList.forEach((row, i) => {
    if (i > 0) {
      row.remove();
    }
  });
}
function applyFilter() {
  let titleMatch = "";
  const searchText = textInput.value.toLowerCase();
  const selectedForms = Array.from(formCheckbox)
    .filter((checkbox) => checkbox.checked)
    .map((checkbox) => checkbox.value);
  const selectedChap = Array.from(chapCheckbox)
    .filter((checkbox) => checkbox.checked)
    .map((checkbox) => checkbox.value);
  const selectedMat = Array.from(matCheckbox)
    .filter((checkbox) => checkbox.checked)
    .map((checkbox) => checkbox.value);
  const selectedInput = Array.from(
    document.querySelectorAll('input[name="search"]')
  ).map((type) => type.value);

  const isAllVisible = all_mat.style.display === "block";
  const dataList = isAllVisible ? allQuizData : recommend_list;
  const container = isAllVisible
    ? all_quiz_container
    : recommend_quiz_container;

  const filterQuiz = dataList.filter((quiz) => {
    if (selectedInput[0] == null) {
      titleMatch = quiz["quiz_title"].toLowerCase().includes(searchText);
    } else {
      switch (selectedInput[0]) {
        case "title":
          titleMatch = quiz["quiz_title"].toLowerCase().includes(searchText);
          break;
      }
    }
    const formMatch =
      selectedForms.length === 0 || selectedForms.includes(quiz["quiz_level"]);
    const chapMatch =
      selectedChap.length === 0 || selectedChap.includes(quiz["quiz_chapter"]);
    const matMatch =
      selectedMat.length === 0 ||
      selectedMat.includes(quiz["quiz_subject"].toLowerCase());
    return titleMatch && formMatch && chapMatch && matMatch;
  });
  let display_data = [];
  clearQuizList();
  filterQuiz.forEach((quiz) => {
    const isBookmarked = progress_result.some(
      (b) => b.quiz_id === quiz.quiz_id
    ); // Check if material is bookmarked
    const bookmarkClass = isBookmarked ? "bookmarked" : "";
    const bookmarkIcon = isBookmarked
      ? `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-bookmark-fill" viewBox="0 0 16 16">
                        <path d="M2 2v13.5a.5.5 0 0 0 .74.439L8 13.069l5.26 2.87A.5.5 0 0 0 14 15.5V2a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2"/>
                    </svg> Unmark`
      : `<svg id="bookmarkIcon" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="24" height="24" viewBox="0 0 24 24">
                        <path d="M 6 2 C 4.8444444 2 4 2.9666667 4 4 L 4 22.039062 L 12 19.066406 L 20 22.039062 L 20 20.599609 L 20 4 C 20 3.4777778 19.808671 2.9453899 19.431641 2.5683594 C 19.05461 2.1913289 18.522222 2 18 2 L 6 2 z M 6 4 L 18 4 L 18 19.162109 L 12 16.933594 L 6 19.162109 L 6 4 z"></path>
                    </svg> Bookmark`;

    display_data.push(
      `<section class="material ${quiz.quiz_id}" onclick="show('${
        quiz.quiz_id
      }')">
                        <div class="title">
                            <p>${quiz.quiz_title.toUpperCase()}</p>
                            <div class="dropdown">
                                <button onclick="showDropdown(this, event)" class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" width="30" height="30" viewBox="0 0 100 100" id="more">
                                        <g id="_x37_7_Essential_Icons">
                                            <path id="More_Details__x28_3_x29_" d="M50 12c-21 0-38 17-38 38s17 38 38 38 38-17 38-38-17-38-38-38zm0 72c-18.8 0-34-15.2-34-34s15.2-34 34-34 34 15.2 34 34-15.2 34-34 34zm0-41c-3.9 0-7 3.1-7 7s3.1 7 7 7 7-3.1 7-7-3.1-7-7-7zm0 10c-1.7 0-3-1.4-3-3 0-1.7 1.3-3 3-3s3 1.3 3 3c0 1.6-1.3 3-3 3zm20-10c-3.9 0-7 3.1-7 7s3.1 7 7 7 7-3.1 7-7-3.1-7-7-7zm0 10c-1.7 0-3-1.4-3-3 0-1.7 1.3-3 3-3s3 1.3 3 3c0 1.6-1.3 3-3 3zM30 43c-3.9 0-7 3.1-7 7s3.1 7 7 7 7-3.1 7-7-3.1-7-7-7zm0 10c-1.7 0-3-1.4-3-3 0-1.7 1.3-3 3-3s3 1.3 3 3c0 1.6-1.3 3-3 3z"></path>
                                        </g>
                                    </svg>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item ${bookmarkClass}" 
                                    href="#;" 
                                    onclick="toggleBookmark(this, '${
                                      quiz.quiz_id
                                    }', '${student_id}'); event.stopPropagation();">
                                        ${bookmarkIcon}
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="progress">
                            <div id="show-progress">SCORE: <div>0</div></div>
                            <button>NOT ATTEMPT</button>
                        </div>
                    </section>`
    );
  });
  container.innerHTML = display_data.join("");
}
let allQuizData = [];
function selectedCheckbox(all) {
  let display_data = [];
  if (all) {
    all_mat.style.display = "block";
    selected_mat.style.display = "none";
    allQuizData = quiz_data.filter((data) => {
      const attempted = isAttempted(data.quiz_id);
      return !attempted;
    });

    display_data = allQuizData.map((data) => {
      const isBookmarked = progress_result.some(
        (b) => b.quiz_id === data.quiz_id
      ); // Check if material is bookmarked
      const bookmarkClass = isBookmarked ? "bookmarked" : "";
      const bookmarkIcon = isBookmarked
        ? `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-bookmark-fill" viewBox="0 0 16 16">
                            <path d="M2 2v13.5a.5.5 0 0 0 .74.439L8 13.069l5.26 2.87A.5.5 0 0 0 14 15.5V2a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2"/>
                        </svg> Unmark`
        : `<svg id="bookmarkIcon" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="24" height="24" viewBox="0 0 24 24">
                            <path d="M 6 2 C 4.8444444 2 4 2.9666667 4 4 L 4 22.039062 L 12 19.066406 L 20 22.039062 L 20 20.599609 L 20 4 C 20 3.4777778 19.808671 2.9453899 19.431641 2.5683594 C 19.05461 2.1913289 18.522222 2 18 2 L 6 2 z M 6 4 L 18 4 L 18 19.162109 L 12 16.933594 L 6 19.162109 L 6 4 z"></path>
                        </svg> Bookmark`;

      return `<section class="material ${data.quiz_id}" onclick="show('${
        data.quiz_id
      }')">
                        <div class="title">
                            <p>${data.quiz_title.toUpperCase()}</p>
                            <div class="dropdown">
                                <button onclick="showDropdown(this, event)" class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" width="30" height="30" viewBox="0 0 100 100" id="more">
                                        <g id="_x37_7_Essential_Icons">
                                            <path id="More_Details__x28_3_x29_" d="M50 12c-21 0-38 17-38 38s17 38 38 38 38-17 38-38-17-38-38-38zm0 72c-18.8 0-34-15.2-34-34s15.2-34 34-34 34 15.2 34 34-15.2 34-34 34zm0-41c-3.9 0-7 3.1-7 7s3.1 7 7 7 7-3.1 7-7-3.1-7-7-7zm0 10c-1.7 0-3-1.4-3-3 0-1.7 1.3-3 3-3s3 1.3 3 3c0 1.6-1.3 3-3 3zm20-10c-3.9 0-7 3.1-7 7s3.1 7 7 7 7-3.1 7-7-3.1-7-7-7zm0 10c-1.7 0-3-1.4-3-3 0-1.7 1.3-3 3-3s3 1.3 3 3c0 1.6-1.3 3-3 3zM30 43c-3.9 0-7 3.1-7 7s3.1 7 7 7 7-3.1 7-7-3.1-7-7-7zm0 10c-1.7 0-3-1.4-3-3 0-1.7 1.3-3 3-3s3 1.3 3 3c0 1.6-1.3 3-3 3z"></path>
                                        </g>
                                    </svg>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item ${bookmarkClass}" 
                                    href="#;" 
                                    onclick="toggleBookmark(this, '${
                                      data.quiz_id
                                    }', '${student_id}'); event.stopPropagation();">
                                        ${bookmarkIcon}
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="progress">
                            <div id="show-progress">SCORE: <div>0</div></div>
                            <button>NOT ATTEMPT</button>
                        </div>
                    </section>`;
    });
    all_quiz_container.innerHTML = display_data.join("");
  } else {
    all_mat.style.display = "none";
    selected_mat.style.display = "block";
  }
}

function toggleBookmark(element, quizId, studentId) {
  console.log("Clicked Quiz ID:", quizId);
  const isBookmarked = element.classList.contains("bookmarked");

  const xhttp = new XMLHttpRequest();
  xhttp.open("POST", "bookmark.php", true);
  xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

  const action = isBookmarked ? "remove" : "add";
  const data =
    "action=" + action + "&quiz_id=" + quizId + "&student_id=" + studentId;

  xhttp.onload = function () {
    if (xhttp.status === 200) {
      if (isBookmarked) {
        element.classList.remove("bookmarked");
        element.innerHTML = `
                    <svg id="bookmarkIcon" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="24" height="24" viewBox="0 0 24 24">
                        <path d="M 6 2 C 4.8444444 2 4 2.9666667 4 4 L 4 22.039062 L 12 19.066406 L 20 22.039062 L 20 20.599609 L 20 4 C 20 3.4777778 19.808671 2.9453899 19.431641 2.5683594 C 19.05461 2.1913289 18.522222 2 18 2 L 6 2 z M 6 4 L 18 4 L 18 19.162109 L 12 16.933594 L 6 19.162109 L 6 4 z"></path>
                    </svg> Bookmark`;
      } else {
        element.classList.add("bookmarked");
        element.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-bookmark-fill" viewBox="0 0 16 16">
                        <path d="M2 2v13.5a.5.5 0 0 0 .74.439L8 13.069l5.26 2.87A.5.5 0 0 0 14 15.5V2a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2"/>
                    </svg> Unmark`;
      }
    } else {
      console.error("Failed to toggle bookmark:", xhttp.responseText);
    }
  };

  xhttp.send(data);
}
