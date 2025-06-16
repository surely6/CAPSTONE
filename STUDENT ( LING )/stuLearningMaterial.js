window.addEventListener("DOMContentLoaded", () => {
  displayRecentMaterial(progress_data);
  displayRecommendMaterial();

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

  // Check if material_id is in URL parameters
  const urlParams = new URLSearchParams(window.location.search);
  const materialId = urlParams.get("material_id");
  if (materialId) {
    // If material ID exists in URL, show that material's modal
    show(materialId);
  }
});

const modal = document.getElementById("modal");
const d = new Date();

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

// Function to compare the current time zone and completed time of reviewing the learning material
function getMostRecent(current_time, recent_data) {
  filtered = recent_data.filter(
    (item) => new Date(item.last_datetime) < new Date(current_time)
  );
  sorted = filtered.sort(
    (a, b) => new Date(b.last_datetime) - new Date(a.last_datetime)
  );
  return sorted;
}

const recent_material_container = document.querySelector(".recent_container");
function displayRecentMaterial(recent_access) {
  const sorted_material = getMostRecent(d, recent_access);
  const limit_material_quantity = 5;
  const recent_material = sorted_material.slice(0, limit_material_quantity);
  let display_data = [];

  for (i = 0; i < recent_material.length; i++) {
    const progress = recent_material[i];
    const materials = material_data.find(
      (m) => m.material_id === progress.material_id
    );
    if (!materials) {
      return;
    }

    const percent = progressBarPercent(materials);

    const isBookmarked = progress_result.some(
      (b) => b.material_id === progress.material_id
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
      `<section class="material ${
        materials.material_learning_type
      }" onclick="show('${materials.material_id}')">
                    <div class="title">
                        <p>${materials.material_title.toUpperCase()}</p>
                        <div class="dropdown">
                            <button onclick="showDropdown(this, event)" class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" width="30" height="30" viewBox="0 0 100 100" id="more">
                                    <g id="_x37_7_Essential_Icons">
                                        <path id="More_Details__x28_3_x29_" d="M50 12c-21 0-38 17-38 38s17 38 38 38 38-17 38-38-17-38-38-38zm0 72c-18.8 0-34-15.2-34-34s15.2-34 34-34 34 15.2 34 34-15.2 34-34 34zm0-41c-3.9 0-7 3.1-7 7s3.1 7 7 7 7-3.1 7-7-3.1-7-7-7zm0 10c-1.7 0-3-1.4-3-3 0-1.7 1.3-3 3-3s3 1.3 3 3c0 1.6-1.3 3-3 3zm20-10c-3.9 0-7 3.1-7 7s3.1 7 7 7 7-3.1 7-7-3.1-7-7-7zm0 10c-1.7 0-3-1.4-3-3 0-1.7 1.3-3 3-3s3 1.3 3 3c0 1.6-1.3 3-3 3zM30 43c-3.9 0-7 3.1-7 7s3.1 7 7 7 7-3.1 7-7-3.1-7-7-7zm0 10c-1.7 0-3-1.4-3-3 0-1.7 1.3-3 3-3s3 1.3 3 3c0 1.6-1.3 3-3 3z"></path>
                                    </g>
                                </svg>
                            </button>
                            <div class="dropdown-menu" event.stopPropagation()>
                                <a class="dropdown-item ${bookmarkClass}" 
                                href="#;" 
                                onclick="toggleBookmark(this, '${
                                  materials.material_id
                                }', '${student_id}'); event.stopPropagation();">
                                    ${bookmarkIcon}
                                </a>
                                <a class="dropdown-item" href="/capstone/PROFILE/STUDENT ( PIKER )/managePath.php" onclick="event.stopPropagation();">
                                    Review Learning Path
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="progress">
                        <div id="show-progress">PROGRESS<div>${percent}%</div></div>
                        <div class="progress-bar"> 
                            <div style="width: ${percent}%;"></div>
                        </div>
                    </div>
                </section>`
    );
  }
  display_data = display_data.join("");
  recent_material_container.innerHTML = display_data;
}

const recommend_material_container = document.querySelector(
  ".recommend_container"
);
let recommend_list = [];
function displayRecommendMaterial() {
  let recommend_material = [];

  student_data.forEach((data) => {
    student_attempt_data.map((sa) => {
      const match_quiz = quiz_data.filter((mq) => mq.quiz_id === sa.quiz_id);
      match_quiz.forEach((q) => {
        if (data.score <= Math.trunc(q.quiz_total_questions / 2)) {
          const other_instructor_materials = material_data.filter(
            (m) =>
              m.instructor_id !== q.instructor_id &&
              m.material_subject === q.quiz_subject &&
              m.material_chapter === q.quiz_chapter &&
              m.material_level === q.quiz_level
          );
          recommend_material.push(...other_instructor_materials);
        }

        const same_instructor_materials = material_data.filter(
          (m) =>
            m.instructor_id === q.instructor_id &&
            m.material_subject === q.quiz_subject &&
            (m.material_chapter > q.quiz_chapter ||
              m.material_chapter < q.quiz_chapter) &&
            m.material_level === q.quiz_level
        );
        recommend_material.push(...same_instructor_materials);
      });
    });

    const student_read = progress_data
      .filter((pd) => pd.student_id === data.student_id)
      .map((pd) =>
        material_data.find((md) => md.material_id === pd.material_id)
      )
      .filter((mat) => mat !== undefined);

    if (!student_read || student_read.length === 0) {
    } else {
      student_read.forEach((material) => {
        const progress_find = progress_data.find(
          (mm) => mm.material_id === material.material_id
        );
        if (progress_find) {
          const instructor_materials = material_data.filter(
            (m) =>
              m.instructor_id === material.instructor_id &&
              m.material_subject === material.material_subject &&
              (m.material_chapter > material.material_chapter ||
                m.material_chapter < material.material_chapter) &&
              m.material_level === material.material_level
          );
          recommend_material.push(...instructor_materials);
        }
      });
    }

    const match_pathway = pathway_data
      .filter((mp) => mp.student_id === data.student_id)
      .map((mp) =>
        material_data.find((md) => md.material_id === mp.material_id)
      )
      .filter((mat) => mat !== undefined);

    if (!match_pathway || match_pathway.length === 0) {
      // No matching pathway materials for this student in current style
    } else {
      match_pathway.forEach((material) => {
        const pathway_find = pathway_data.find(
          (mm) => mm.material_id === material.material_id
        );
        if (pathway_find) {
          const recommend_pathway_subject = material_data.filter(
            (m) =>
              m.material_subject === material.material_subject &&
              (m.material_chapter > material.material_chapter ||
                m.material_chapter < material.material_chapter) &&
              m.material_level === material.material_level
          );
          recommend_material.push(...recommend_pathway_subject);
        }
      });
    }
  });

  // Display the recommended materials
  let display_data = [];
  const filtered_recommend_material = recommend_material.filter((m) => {
    const percent = progressBarPercent(m);
    return percent == 0;
  });
  const all_matches = new Set(filtered_recommend_material);

  // For filtering process
  recommend_list = [...all_matches];
  console.log("Recommended Materials List:", recommend_list);

  all_matches.forEach((rm) => {
    const percent = progressBarPercent(rm);

    const isBookmarked = progress_result.some(
      (b) => b.material_id === rm.material_id
    ); // Check if material is bookmarked
    const bookmarkClass = isBookmarked ? "bookmarked" : "";
    const bookmarkIcon = isBookmarked
      ? `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-bookmark-fill" viewBox="0 0 16 16">
                        <path d="M2 2v13.5a.5.5 0 0 0 .74.439L8 13.069l5.26 2.87A.5.5 0 0 0 14 15.5V2a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2"/>
                    </svg> Unmark`
      : `<svg id="bookmarkIcon" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="24" height="24" viewBox="0 0 24 24">
                        <path d="M 6 2 C 4.8444444 2 4 2.9666667 4 4 L 4 22.039062 L 12 19.066406 L 20 22.039062 L 20 20.599609 L 20 4 C 20 3.4777778 19.808671 2.9453899 19.431641 2.5683594 C 19.05461 2.1913289 18.522222 2 18 2 L 6 2 z M 6 4 L 18 4 L 18 19.162109 L 12 16.933594 L 6 19.162109 L 6 4 z"></path>
                    </svg> Bookmark`;

    if (percent == 0) {
      display_data.push(
        `<section class="material ${
          rm.material_learning_type
        }" onclick="show('${rm.material_id}')">
                            <div class="title">
                                <p>${rm.material_title.toUpperCase()}</p>
                                <div class="dropdown" event.stopPropagation()>
                                    <button onclick="showDropdown(this, event)" class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" width="30" height="30" viewBox="0 0 100 100" id="more">
                                            <g id="_x37_7_Essential_Icons">
                                                <path id="More_Details__x28_3_x29_" d="M50 12c-21 0-38 17-38 38s17 38 38 38 38-17 38-38-17-38-38-38zm0 72c-18.8 0-34-15.2-34-34s15.2-34 34-34 34 15.2 34 34-15.2 34-34 34zm0-41c-3.9 0-7 3.1-7 7s3.1 7 7 7 7-3.1 7-7-3.1-7-7-7zm0 10c-1.7 0-3-1.4-3-3 0-1.7 1.3-3 3-3s3 1.3 3 3c0 1.6-1.3 3-3 3zm20-10c-3.9 0-7 3.1-7 7s3.1 7 7 7 7-3.1 7-7-3.1-7-7-7zm0 10c-1.7 0-3-1.4-3-3 0-1.7 1.3-3 3-3s3 1.3 3 3c0 1.6-1.3 3-3 3zM30 43c-3.9 0-7 3.1-7 7s3.1 7 7 7 7-3.1 7-7-3.1-7-7-7zm0 10c-1.7 0-3-1.4-3-3 0-1.7 1.3-3 3-3s3 1.3 3 3c0 1.6-1.3 3-3 3z"></path>
                                            </g>
                                        </svg>
                                    </button>
                                <div class="dropdown-menu" event.stopPropagation()>
                                    <a class="dropdown-item ${bookmarkClass}" 
                                    href="#;" 
                                    onclick="toggleBookmark(this, '${
                                      rm.material_id
                                    }', '${student_id}'); event.stopPropagation();">
                                        ${bookmarkIcon}
                                    </a>
                                    <a class="dropdown-item" href="/capstone/PROFILE/STUDENT ( PIKER )/managePath.php" onclick="event.stopPropagation();">
                                        Review Learning Path
                                    </a>
                                </div>
                            </div>
                            </div>
                            <div class="progress">
                                <div id="show-progress">PROGRESS<div>${percent}%</div></div>
                                <div class="progress-bar"> 
                                    <div style="width: ${percent}%;"></div>
                                </div>
                            </div>
                        </section>`
      );
    }
  });

  display_data = display_data.join("");
  recommend_material_container.innerHTML = display_data;
}
// Calculate the Progress Bar Percentage by reviewing the parts of material that have been read by the user
function progressBarPercent(material) {
  const match_id = parts_data.filter(
    (part) => part.material_id === material.material_id
  );
  const total_parts = match_id.length;

  const progress_find = progress_data.find(
    (pro) => pro.material_id === material.material_id
  );
  if (!progress_find || !progress_find.progress) {
    return 0;
  }

  const progress_part = progress_find.progress.split(", ");

  if (progress_part.length > 0) {
    read_count = progress_part.length;
  }

  if (total_parts == 0) {
    return 0;
  }

  const progress_percent = Math.round((read_count / total_parts) * 100);
  return progress_percent;
}

// Prevent selected material to show up, while user click on dropdown button
function showDropdown(button, event) {
  button.classList.toggle("active");
  event.stopPropagation();
}

// Inherit the material id and send it to the next page
function Begin(material_id) {
  console.log(material_id);
  document.cookie = "Material_ID=" + material_id;
  window.location.href = "stuAccessMaterial.php";
}

function toggleBookmark(element, materialId, studentId) {
  console.log("Clicked Material ID:", materialId);
  const isBookmarked = element.classList.contains("bookmarked");

  const xhttp = new XMLHttpRequest();
  xhttp.open("POST", "bookmark.php", true);
  xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

  const action = isBookmarked ? "remove" : "add";
  const data =
    "action=" +
    action +
    "&material_id=" +
    materialId +
    "&student_id=" +
    studentId;

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

// SEARCH & FILTER FUNCTIONS
const textInput = document.getElementById("search");
const allMaterial = document.querySelectorAll(".all");
const formCheckbox = document.querySelectorAll(".formCheckbox");
const chapCheckbox = document.querySelectorAll(".chapCheckbox");
const matCheckbox = document.querySelectorAll(".matCheckbox");
const selected_mat = document.getElementById("selected_mat");
const all_mat = document.getElementById("all_mat");
const all_material_container = document.querySelector(".everything_container");

function clearMaterialList() {
  const isAllVisible = all_mat.style.display === "block";
  const container = isAllVisible
    ? all_material_container
    : recommend_material_container;
  const entireMaterialList = container.querySelectorAll("section.material");
  entireMaterialList.forEach((row, i) => {
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
  const dataList = isAllVisible ? allMaterialData : recommend_list;
  const container = isAllVisible
    ? all_material_container
    : recommend_material_container;

  const filterMaterial = dataList.filter((material) => {
    if (selectedInput[0] == null) {
      titleMatch = material["material_title"]
        .toLowerCase()
        .includes(searchText);
    } else {
      switch (selectedInput[0]) {
        case "title":
          titleMatch = material["material_title"]
            .toLowerCase()
            .includes(searchText);
          break;
      }
    }

    const formMatch =
      selectedForms.length === 0 ||
      selectedForms.includes(material["material_level"]);
    const chapMatch =
      selectedChap.length === 0 ||
      selectedChap.includes(material["material_chapter"]);
    const matMatch =
      selectedMat.length === 0 ||
      selectedMat.includes(material["material_subject"].toLowerCase());
    return titleMatch && formMatch && chapMatch && matMatch;
  });
  let display_data = [];
  clearMaterialList();

  filterMaterial.forEach((material) => {
    const percent = progressBarPercent(material);

    const isBookmarked = progress_result.some(
      (b) => b.material_id === material.material_id
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
      `<section class="material ${
        material.material_learning_type
      }", onclick="show('${material.material_id}')">
                    <div class="title">
                        <p>${material.material_title.toUpperCase()}</p>
                        <div class="dropdown">
                            <button onclick="showDropdown(this, event)" class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" width="30" height="30" viewBox="0 0 100 100" id="more">
                                    <g id="_x37_7_Essential_Icons">
                                        <path id="More_Details__x28_3_x29_" d="M50 12c-21 0-38 17-38 38s17 38 38 38 38-17 38-38-17-38-38-38zm0 72c-18.8 0-34-15.2-34-34s15.2-34 34-34 34 15.2 34 34-15.2 34-34 34zm0-41c-3.9 0-7 3.1-7 7s3.1 7 7 7 7-3.1 7-7-3.1-7-7-7zm0 10c-1.7 0-3-1.4-3-3 0-1.7 1.3-3 3-3s3 1.3 3 3c0 1.6-1.3 3-3 3zm20-10c-3.9 0-7 3.1-7 7s3.1 7 7 7 7-3.1 7-7-3.1-7-7-7zm0 10c-1.7 0-3-1.4-3-3 0-1.7 1.3-3 3-3s3 1.3 3 3c0 1.6-1.3 3-3 3zM30 43c-3.9 0-7 3.1-7 7s3.1 7 7 7 7-3.1 7-7-3.1-7-7-7zm0 10c-1.7 0-3-1.4-3-3 0-1.7 1.3-3 3-3s3 1.3 3 3c0 1.6-1.3 3-3 3z"></path>
                                    </g>
                                </svg>
                            </button>
                            <div class="dropdown-menu")>
                                <a class="dropdown-item ${bookmarkClass}" 
                                href="#;" 
                                onclick="toggleBookmark(this, '${
                                  material.material_id
                                }', '${student_id}');>
                                    ${bookmarkIcon}
                                </a>
                                <a class="dropdown-item" href="/capstone/PROFILE/STUDENT ( PIKER )/managePath.php" onclick="event.stopPropagation();">
                                    Review Learning Path
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="progress">
                        <div id="show-progress">PROGRESS<div>${percent}%</div></div>
                        <div class="progress-bar"> 
                            <div style="width: ${percent}%;"></div>
                        </div>
                    </div>
                </section>`
    );
  });
  container.innerHTML = display_data.join("");
}
let allMaterialData = [];
function selectedCheckbox(all) {
  let display_data = [];
  if (all) {
    all_mat.style.display = "block";
    selected_mat.style.display = "none";
    allMaterialData = material_data.filter((data) => {
      const percent = progressBarPercent(data);
      return percent == 0;
    });

    display_data = allMaterialData.map((data) => {
      const percent = progressBarPercent(data);

      const isBookmarked = progress_result.some(
        (b) => b.material_id === data.material_id
      ); // Check if material is bookmarked
      const bookmarkClass = isBookmarked ? "bookmarked" : "";
      const bookmarkIcon = isBookmarked
        ? `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-bookmark-fill" viewBox="0 0 16 16">
                        <path d="M2 2v13.5a.5.5 0 0 0 .74.439L8 13.069l5.26 2.87A.5.5 0 0 0 14 15.5V2a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2"/>
                    </svg> Unmark`
        : `<svg id="bookmarkIcon" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="24" height="24" viewBox="0 0 24 24">
                        <path d="M 6 2 C 4.8444444 2 4 2.9666667 4 4 L 4 22.039062 L 12 19.066406 L 20 22.039062 L 20 20.599609 L 20 4 C 20 3.4777778 19.808671 2.9453899 19.431641 2.5683594 C 19.05461 2.1913289 18.522222 2 18 2 L 6 2 z M 6 4 L 18 4 L 18 19.162109 L 12 16.933594 L 6 19.162109 L 6 4 z"></path>
                    </svg> Bookmark`;

      return `
                    <section class="material ${
                      data.material_learning_type
                    }" onclick="show(${data.material_id})">
                        <div class="title">
                            <p>${data.material_title.toUpperCase()}</p>
                            <div class="dropdown">
                                <button onclick="showDropdown(this, event)" class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" width="30" height="30" viewBox="0 0 100 100" id="more">
                                        <g id="_x37_7_Essential_Icons">
                                            <path id="More_Details__x28_3_x29_" d="M50 12c-21 0-38 17-38 38s17 38 38 38 38-17 38-38-17-38-38-38zm0 72c-18.8 0-34-15.2-34-34s15.2-34 34-34 34 15.2 34 34-15.2 34-34 34zm0-41c-3.9 0-7 3.1-7 7s3.1 7 7 7 7-3.1 7-7-3.1-7-7-7zm0 10c-1.7 0-3-1.4-3-3 0-1.7 1.3-3 3-3s3 1.3 3 3c0 1.6-1.3 3-3 3zm20-10c-3.9 0-7 3.1-7 7s3.1 7 7 7 7-3.1 7-7-3.1-7-7-7zm0 10c-1.7 0-3-1.4-3-3 0-1.7 1.3-3 3-3s3 1.3 3 3c0 1.6-1.3 3-3 3zM30 43c-3.9 0-7 3.1-7 7s3.1 7 7 7 7-3.1 7-7-3.1-7-7-7zm0 10c-1.7 0-3-1.4-3-3 0-1.7 1.3-3 3-3s3 1.3 3 3c0 1.6-1.3 3-3 3z"></path>
                                        </g>
                                    </svg>
                                </button>
                                <div class="dropdown-menu" event.stopPropagation()>
                                    <a class="dropdown-item ${bookmarkClass}" 
                                    href="#;" 
                                    onclick="toggleBookmark(this, '${
                                      data.material_id
                                    }', '${student_id}'); event.stopPropagation();">
                                        ${bookmarkIcon}
                                    </a>
                                    <a class="dropdown-item" href="/capstone/PROFILE/STUDENT ( PIKER )/managePath.php" onclick="event.stopPropagation();">
                                        Review Learning Path
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="progress">
                            <div id="show-progress">PROGRESS<div>${percent}%</div></div>
                            <div class="progress-bar"> 
                                <div style="width: ${percent}%;"></div>
                            </div>
                        </div>
                    </section>`;
    });
    all_material_container.innerHTML = display_data.join("");
  } else {
    all_mat.style.display = "none";
    selected_mat.style.display = "block";
  }
}

// Display the selected learning material's details
function show(material_id) {
  console.log("Material ID passed to show:", material_id); // Debugging
  let material_selected = material_data.filter(function (data) {
    if (data.material_id == material_id) {
      return data;
    }
  });
  displaySelectedMaterial(material_selected);
  console.log("Material selected:", material_selected); // Debugging
  modal.showModal();
}
// Close the selected learning material's details
function closeModal() {
  modal.close();
}

// A small window that display details about the selected material
const material_descript_container = document.querySelector(".modal-content");
function displaySelectedMaterial(material) {
  let total_parts = 0;

  if (material.length > 0) {
    const selected = material[0]; // only 1 item shown in modal
    const match_parts = parts_data.filter(
      (part) => part.material_id === selected.material_id
    );
    total_parts = match_parts.length;
  }

  let display_data = material.map(function (grid_item) {
    modal.className = "modal-content " + `${grid_item.material_title}`;
    return `<p><span class="close_button ${
      grid_item.material_title
    }" onclick="closeModal()">&#10006;</span></p>
                <center>
                    <div class="title">
                        <h1>${grid_item.material_title.toUpperCase()}</h1>
                    </div>
                    <div class="subject">
                        <h3>SUBJECT: ${grid_item.material_subject.toUpperCase()}</h3>
                    </div>
                    <div class="subject">
                        <h3>INSTRUCTOR NAME: ${grid_item.instructor_name.toUpperCase()}</h3>
                    </div>
                    <div class="learning_type">
                        <h3>LEARNING TYPE: ${grid_item.material_learning_type.toUpperCase()}</h3>
                    </div>
                    <div class="chapter">
                        <h3>CHAPTER: ${grid_item.material_chapter}</h3>
                    </div>
                    <div class="parts">
                        <h3>TOTAL PARTS: ${total_parts}</h3>
                    </div>
                    <div class="summary">
                        <h1>${grid_item.material_description}</h1>
                    </div>  
                    <button class="start" onclick="Begin('${
                      grid_item.material_id
                    }')">BEGIN READING</button>  
                </center>`;
  });
  display_data = display_data.join("");
  material_descript_container.innerHTML = display_data;
}
