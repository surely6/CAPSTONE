//unfinished.length
console.log(finished);
console.log(unfinished);
let Start = 0;
let End = unfinished.length >= 3 ? 3 : unfinished.length;
let StartFinished = 0;
let EndFinished = finished.length >= 3 ? 3 : finished.length;
let filter = false;
let Search = false;

window.addEventListener("load", function () {
  DisplayUnfinishedQuiz(unfinished);
  DisplayFinishedQuiz(finished);
});

function next(array) {
  Start += 3;
  End += 3;
  if (End > array.length && Start >= array.length) {
    End = array.length >= 3 ? 3 : array.length;
    Start = 0;
  } else if (End >= array.length) {
    End = array.length;
  }
  DisplayUnfinishedQuiz(array);
}

function previous(array) {
  Start -= 3;
  End -= 3;
  if (Start < 0) {
    Start = 0;
    End = array.length >= 3 ? 3 : array.length;
  } else {
    End = Start + 3;
    if (End > array.length) End = array.length;
  }
  DisplayUnfinishedQuiz(array);
}

function nextFinished(array) {
  StartFinished += 3;
  EndFinished += 3;
  if (EndFinished > array.length && StartFinished >= array.length) {
    EndFinished = array.length >= 3 ? 3 : array.length;
    StartFinished = 0;
  } else if (EndFinished >= array.length) {
    EndFinished = array.length;
  }
  DisplayFinishedQuiz(array);
}

function previousFinished(array) {
  StartFinished -= 3;
  EndFinished -= 3;
  if (StartFinished < 0) {
    StartFinished = 0;
    EndFinished = array.length >= 3 ? 3 : array.length;
  } else {
    EndFinished = StartFinished + 3;
    if (EndFinished > array.length) EndFinished = array.length;
  }
  DisplayFinishedQuiz(array);
}

function DisplayUnfinishedQuiz(array) {
  let container = document.getElementById("unfinished");
  let containerData = ``;

  if (filter || Search) {
    if (filter) {
      containerData = `<button id="previous" onclick="previous(unfinishedfiltered)"><span><</span></button>
                       <button id="next" onclick="next(unfinishedfiltered)"><span>></span></button>`;
    } else {
      containerData = `<button id="previous" onclick="previous(unfinishedsearch)"><span><</span></button>
                       <button id="next" onclick="next(unfinishedsearch)"><span>></span></button>`;
    }
  } else {
    containerData = `<button id="previous" onclick="previous(unfinished)"><span><</span></button>
                     <button id="next" onclick="next(unfinished)"><span>></span></button>`;
  }

  for (let i = Start; i < End; i++) {
    let quiz = array[i];
    if (!quiz) continue;
    let data = `<div class="selection admin" onclick="EditQuiz(${
      quiz.quiz_id
    })">
                  <div class="content-title">${quiz.quiz_title.toUpperCase()}
                    <div class="title-detail">Created On ${quiz.date_made}</div>
                  </div>
                  <div class="content"><div>SUBJECT:</div>${quiz.quiz_subject.toUpperCase()}</div>
                  <div class="content"><div>CHAPTER:</div>${
                    quiz.quiz_chapter
                  }</div>
                  <div class="content"><div>FORM:</div>${quiz.quiz_level}</div>
                </div>`;
    containerData += data;
  }
  container.innerHTML = containerData;
}

function DisplayFinishedQuiz(array) {
  let container = document.getElementById("finished");
  let containerData = `<button id="previousFinished" onclick="previousFinished(finished)"><span><</span></button>
                       <button id="nextFinished" onclick="nextFinished(finished)"><span>></span></button>`;

  for (let i = StartFinished; i < EndFinished; i++) {
    let quiz = array[i];
    if (!quiz) continue;
    let data = `<div class="selection admin" onclick="SummaryQuiz(${
      quiz.quiz_id
    })">
                  <div class="content-title">${quiz.quiz_title.toUpperCase()}
                    <div class="title-detail">Created On ${quiz.date_made}</div>
                  </div>
                  <div class="content"><div>SUBJECT:</div>${quiz.quiz_subject.toUpperCase()}</div>
                  <div class="content"><div>CHAPTER:</div>${
                    quiz.quiz_chapter
                  }</div>
                  <div class="content"><div>FORM:</div>${quiz.quiz_level}</div>
                </div>`;
    containerData += data;
  }
  container.innerHTML = containerData;
}

// Filtering and searching logic (similar to View Material.js)
let filterOptions = document.querySelectorAll('input[type="checkbox"]');
let unfinishedfiltered, finishedfiltered, unfinishedsearch, finishedsearch;

filterOptions.forEach((option) => {
  option.addEventListener("change", FilterQuiz);
});

function search() {
  let SearchBar = document.querySelector("#search").value.toUpperCase();
  let title;
  if (SearchBar == "") {
    Search = false;
    if (filter) {
      Start = 0;
      End = unfinishedfiltered.length >= 3 ? 3 : unfinishedfiltered.length;
      DisplayUnfinishedQuiz(unfinishedfiltered);
      DisplayFinishedQuiz(finishedfiltered);
    } else {
      Start = 0;
      End = unfinished.length >= 3 ? 3 : unfinished.length;
      DisplayUnfinishedQuiz(unfinished);
      DisplayFinishedQuiz(finished);
    }
  } else {
    Search = true;
    if (filter) {
      unfinishedsearch = [];
      finishedsearch = [];
      unfinishedfiltered.forEach((quiz) => {
        if (quiz.quiz_title.toUpperCase().includes(SearchBar)) {
          unfinishedsearch.push(quiz);
        }
      });
      finishedfiltered.forEach((quiz) => {
        if (quiz.quiz_title.toUpperCase().includes(SearchBar)) {
          finishedsearch.push(quiz);
        }
      });
      Start = 0;
      End = unfinishedsearch.length >= 3 ? 3 : unfinishedsearch.length;
    } else {
      unfinishedsearch = [];
      finishedsearch = [];
      unfinished.forEach((quiz) => {
        title = quiz.quiz_title.toUpperCase();
        if (title.includes(SearchBar)) {
          unfinishedsearch.push(quiz);
        }
      });
      finished.forEach((quiz) => {
        title = quiz.quiz_title.toUpperCase();
        if (title.includes(SearchBar)) {
          finishedsearch.push(quiz);
        }
      });
      Start = 0;
      End = unfinishedsearch.length >= 3 ? 3 : unfinishedsearch.length;
    }
    DisplayUnfinishedQuiz(unfinishedsearch);
    DisplayFinishedQuiz(finishedsearch);
  }
}

function FilterQuiz() {
  let CheckedOptions = {};
  filterOptions.forEach((option) => {
    if (option.checked) {
      if (!Object.hasOwn(CheckedOptions, option.name)) {
        CheckedOptions[option.name] = [];
      }
      CheckedOptions[option.name].push(option.value);
    }
  });

  if (Object.entries(CheckedOptions).length < 1) {
    Start = 0;
    filter = false;
    if (Search) {
      End = unfinishedsearch.length >= 3 ? 3 : unfinishedsearch.length;
      DisplayUnfinishedQuiz(unfinishedsearch);
      DisplayFinishedQuiz(finishedsearch);
    } else {
      End = unfinished.length >= 3 ? 3 : unfinished.length;
      DisplayUnfinishedQuiz(unfinished);
      DisplayFinishedQuiz(finished);
    }
  } else {
    if (Search) {
      unfinishedfiltered = [];
      unfinishedsearch.forEach((quiz) => {
        let within = true;
        for (let key in CheckedOptions) {
          if (!CheckedOptions[key].includes(quiz[key])) {
            within = false;
            break;
          }
        }
        if (within) {
          unfinishedfiltered.push(quiz);
        }
      });
      Start = 0;
      End = unfinishedfiltered.length >= 3 ? 3 : unfinishedfiltered.length;
      filter = true;

      finishedfiltered = [];
      finishedsearch.forEach((quiz) => {
        let within = true;
        for (let key in CheckedOptions) {
          if (!CheckedOptions[key].includes(quiz[key])) {
            within = false;
            break;
          }
        }
        if (within) {
          finishedfiltered.push(quiz);
        }
      });
    } else {
      unfinishedfiltered = [];
      unfinished.forEach((quiz) => {
        let within = true;
        for (let key in CheckedOptions) {
          if (!CheckedOptions[key].includes(quiz[key])) {
            within = false;
            break;
          }
        }
        if (within) {
          unfinishedfiltered.push(quiz);
        }
      });
      Start = 0;
      End = unfinishedfiltered.length >= 3 ? 3 : unfinishedfiltered.length;
      filter = true;

      finishedfiltered = [];
      finished.forEach((quiz) => {
        let within = true;
        for (let key in CheckedOptions) {
          if (!CheckedOptions[key].includes(quiz[key])) {
            within = false;
            break;
          }
        }
        if (within) {
          finishedfiltered.push(quiz);
        }
      });
    }
    DisplayUnfinishedQuiz(unfinishedfiltered);
    DisplayFinishedQuiz(finishedfiltered);
  }
}
