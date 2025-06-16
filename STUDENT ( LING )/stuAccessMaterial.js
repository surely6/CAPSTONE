window.addEventListener("DOMContentLoaded", () => {
  displayMaterial();
  displayMaterialPart(id);
  displayRecommendQuiz();
});

const id = material_id;

const detail_container = document.querySelector(".material_details");
function displayMaterial() {
  const find_material = material_data.find((fm) => fm.material_id == id);

  if (find_material) {
    const display_data = `<section>
                                <div class="title_button"><button onclick="window.location.href='StuLearningMaterial.php'"> < </button>${find_material.material_title.toUpperCase()}</div>
                                <hr>
                                <div class="material_detail_container">
                                    <div>SUBJECT: ${find_material.material_subject.toUpperCase()}</div>
                                    <div>CHAPTER: ${find_material.material_chapter.toUpperCase()}</div>
                                    <div>FORM: ${
                                      find_material.material_level
                                    }</div>
                                    <div>LEARNING TYPE: ${find_material.material_learning_type.toUpperCase()}</div>
                                </div>
                              </section>`;

    detail_container.innerHTML = display_data;
  }
}

const material_container = document.querySelector(".material_section");
function displayMaterialPart(m_id) {
  const id = m_id;
  const find_total_parts = parts_data.filter((ftp) => ftp.material_id == id);

  let display_data = "";

  find_total_parts.forEach((part) => {
    const completion_status = markPartComplete(part.part);
    let button_status = "";
    if (completion_status) {
      button_status = `<button disabled>MARK AS COMPLETED</button>`;
    } else {
      button_status = `<button onclick="markPartCompleteHandler('${part.part_id}', '${id}', this)">COMPLETE</button>`;
    }

    if (find_total_parts.length >= 1) {
      display_data += `<main class="material_part_container"> 
                        <div id="part">
                            <h1>PART: ${part.part}</h1>
                            ${button_status}
                        </div>
                        <hr>
                        <div class="content"></div>
                    </main>`;
    }
  });

  material_container.innerHTML = display_data;
  SetPart();
}

function SetPart() {
  let section = document.querySelectorAll(".content");
  section.forEach((element, index) => {
    element.innerHTML = parts_data[index].material_content;
  });
}

function markPartComplete(part) {
  if (
    !progress_data ||
    !Array.isArray(progress_data) ||
    progress_data.length === 0
  ) {
    return false;
  }
  const progress_string = progress_data[0]?.progress || "";
  const completed_parts = progress_string.split(",").map((part) => part.trim());

  return completed_parts.includes(part);
}
function markPartCompleteHandler(part_id, material_id, button) {
  button.disabled = true;
  button.textContent = "MARK AS COMPLETED";

  fetch("markPartComplete.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ part_id: part_id, material_id: material_id }),
  }).catch(() => {
    button.disabled = false;
    button.textContent = "COMPLETE";
  });
}

const recommend_quiz_container = document.querySelector(".recommend_container");
function displayRecommendQuiz() {
  let recommend_quiz = [];

  const current_material = material_data.find((mat) => mat.material_id === id);

  const student_read = material_data.filter(
    (sr) => sr.student_id === student_id && sr.material_id === id
  );

  if (student_read.length > 0) {
    // If the material with the exact same chapter, level, & subject have been read
    // The respective quiz that is created by the same instructor will be put into "recommend_quiz"
    student_read.forEach((material) => {
      const progress_find = progress_data.find(
        (pro) =>
          pro.material_id === id && pro.student_id === material.student_id
      );

      if (progress_find) {
        const material_read = quiz_data.filter(
          (quiz) =>
            quiz.instructor_id === material.instructor_id &&
            quiz.quiz_subject === material.material_subject &&
            quiz.quiz_chapter === material.material_chapter &&
            quiz.quiz_level === material.material_level
        );
        recommend_quiz.push(...material_read);
      }

      const material_read_extra = quiz_data.filter(
        (quiz) =>
          quiz.instructor_id !== material.instructor_id &&
          quiz.quiz_subject === material.material_subject &&
          quiz.quiz_chapter === material.material_chapter &&
          quiz.quiz_level === material.material_level
      );
      recommend_quiz.push(...material_read_extra);
    });
  } else {
    // Recommend based on current material only
    const fallback = quiz_data.filter(
      (quiz) =>
        quiz.quiz_subject === current_material.material_subject &&
        quiz.quiz_chapter === current_material.material_chapter &&
        quiz.quiz_level === current_material.material_level
    );
    recommend_quiz.push(...fallback);
  }

  // Display the recommended materials
  let display_data = [];
  const all_matches = new Set(recommend_quiz);

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
    }
  });
  console.log("Recommended Quizzes:", all_matches);
  display_data = display_data.join("");
  recommend_quiz_container.innerHTML = display_data;
}

const quiz_descript_container = document.querySelector(".modal-content");
function displaySelectedQuiz(quiz) {
  let display_data = quiz.map(function (grid_item) {
    // const q_style = question_data.find(qs => qs.quiz_id === grid_item.quiz_id);
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
  window.location.href = "stuAccessQuiz.php";
}

// Display the selected quiz's details
function show(quiz_id) {
  let quiz_selected = quiz_data.filter(function (data) {
    if (data.quiz_id == quiz_id) {
      return data;
    }
  });
  console.log(quiz_selected);
  displaySelectedQuiz(quiz_selected);
  modal.showModal();
}
// Close the selected quiz's details
function closeModal() {
  modal.close();
}

function isAttempted(quiz_id) {
  return attempt_data.some(
    (a) => a.student_id === student_id && a.quiz_id === quiz_id
  );
}

function showDropdown(button, event) {
  button.classList.toggle("active");
  event.stopPropagation();
}

function toggleBookmark(element, quizID, studentId) {
  console.log("Clicked Quiz ID:", quizID);
  const isBookmarked = element.classList.contains("bookmarked");

  const xhttp = new XMLHttpRequest();
  xhttp.open("POST", "bookmark.php", true);
  xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

  const action = isBookmarked ? "remove" : "add";
  const data =
    "action=" + action + "&quiz_id=" + quizID + "&student_id=" + studentId;

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
