//unfinished.length

let Start = 0;
let End = unfinished.length >= 3 ? 3 : unfinished.length;
let StartFinished = 0;
let EndFinished = finished.length >= 3 ? 3 : finished.length;
let filter = false;
let Search = false;

function next(array) {
  Start = Start + 3;
  End = End + 3;

  if (End > array.length && Start >= array.length) {
    End = array.length >= 3 ? 3 : array.length;
    Start = 0;
  } else if (End >= array.length) {
    End = array.length;
  }

  console.log(Start);
  console.log(End);
  DisplayUnfinishedMaterial(array);
}

function previous(array) {
  Start = Start - 3;
  End = End - 3;

  if (Start < 0) {
    Start = 0;
    End = array.length >= 3 ? 3 : array.length;
  } else {
    End = Start + 3;
    if (End > array.length) End = array.length;
  }

  console.log(Start);
  console.log(End);
  DisplayUnfinishedMaterial(array);
}

window.addEventListener("load", function () {
  DisplayUnfinishedMaterial(unfinished);
  DisplayfinishedMaterial(finished);
});

function nextFinished(array) {
  StartFinished = StartFinished + 3;
  EndFinished = EndFinished + 3;

  if (EndFinished > array.length && StartFinished >= array.length) {
    EndFinished = array.length >= 3 ? 3 : array.length;
    StartFinished = 0;
  } else if (EndFinished >= array.length) {
    EndFinished = array.length;
  }

  DisplayfinishedMaterial(array);
}

function previousFinished(array) {
  StartFinished = StartFinished - 3;
  EndFinished = EndFinished - 3;

  if (StartFinished < 0) {
    StartFinished = 0;
    EndFinished = array.length >= 3 ? 3 : array.length;
  } else {
    EndFinished = StartFinished + 3;
    if (EndFinished > array.length) EndFinished = array.length;
  }

  DisplayfinishedMaterial(array);
}

function DisplayUnfinishedMaterial(array) {
  let container = document.getElementById("unfinished");
  let containerData = ``;

  if (filter || Search) {
    console.log(filter);
    console.log(Search);
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
  console.log(array);

  for (let i = Start; i < End; i++) {
    let data =
      `<div class="selection admin" onclick="EditMaterial(` +
      array[i]["material_id"] +
      `)"> <!-- options -->
                        <div class="content-title">
                            ` +
      array[i]["material_title"].toUpperCase() +
      ` 
                            <div class="title-detail">Created On ` +
      array[i]["date_made"] +
      `</div>
                        </div>
                        <div class="content"><div>SUBJECT:</div>` +
      array[i]["material_subject"].toUpperCase() +
      `</div>
                        <div class="content"><div>CHAPTER:</div>` +
      array[i]["material_chapter"] +
      `</div>
                        <div class="content"><div>FORM:</div>` +
      array[i]["material_level"] +
      `</div>
                    </div>
                </div>`;

    containerData = containerData + data;
  }
  console.log(containerData);
  container.innerHTML = containerData;
}

function DisplayfinishedMaterial(array) {
  let container = document.getElementById("finished");
  let containerData = `<button id="previousFinished" onclick="previousFinished(finished)"><span><</span></button>
                       <button id="nextFinished" onclick="nextFinished(finished)"><span>></span></button>`;

  for (let i = StartFinished; i < EndFinished; i++) {
    let element = array[i];
    if (!element) continue;
    let data =
      `<div class="selection admin" onclick="SummaryMaterial(` +
      element["material_id"] +
      `)"> <!-- options -->
                        <div class="content-title">
                            ` +
      element["material_title"].toUpperCase() +
      ` 
                            <div class="title-detail">Created On ` +
      element["date_made"] +
      `</div>
                        </div>
                        <div class="content"><div>SUBJECT:</div>` +
      element["material_subject"].toUpperCase() +
      `</div>
                        <div class="content"><div>CHAPTER:</div>` +
      element["material_chapter"] +
      `</div>
                        <div class="content"><div>FORM:</div>` +
      element["material_level"] +
      `</div>
                    </div>
                </div>`;

    containerData = containerData + data;
  }
  container.innerHTML = containerData;
}

let filterOptions = document.querySelectorAll('input[type="checkbox"]');
let unfinishedfiltered;
let finishedfiltered;
let unfinishedsearch;
let finishedsearch;

filterOptions.forEach((option) => {
  option.addEventListener("change", FilterMaterial);
});

function search() {
  let SearchBar = document.querySelector("#search").value.toUpperCase();
  console.log(SearchBar);
  let title;
  if (SearchBar == "") {
    Search = false;
    console.log("empty");
    if (filter) {
      Start = 0;
      End = unfinishedfiltered.length >= 3 ? 3 : unfinishedfiltered.length;
      DisplayUnfinishedMaterial(unfinishedfiltered);
      DisplayfinishedMaterial(finishedfiltered);
    } else {
      Start = 0;
      End = unfinished.length >= 3 ? 3 : unfinishedlength;
      DisplayUnfinishedMaterial(unfinished);
      DisplayfinishedMaterial(finished);
    }
  } else {
    Search = true;
    if (filter) {
      unfinishedsearch = [];
      finishedsearch = [];
      unfinishedfiltered.forEach((element) => {
        if (element["material_title"].toUpperCase.includes(SearchBar)) {
          unfinishedsearch.push(element);
        }
      });
      finishedfiltered.forEach((element) => {
        if (element["material_title"].toUpperCase.includes(SearchBar)) {
          finishedsearch.push(element);
        }
      });
      Start = 0;
      End = unfinishedsearch.length >= 3 ? 3 : unfinishedsearch.length;
    } else {
      unfinishedsearch = [];
      finishedsearch = [];
      unfinished.forEach((element) => {
        title = element["material_title"].toUpperCase();
        if (title.includes(SearchBar)) {
          unfinishedsearch.push(element);
        }
      });
      finished.forEach((element) => {
        title = element["material_title"].toUpperCase();
        console.log(title);
        if (title.includes(SearchBar)) {
          finishedsearch.push(element);
        }
      });
      Start = 0;
      End = unfinishedsearch.length >= 3 ? 3 : unfinishedsearch.length;
    }
    console.log(unfinishedsearch);
    console.log(finishedsearch);
    DisplayUnfinishedMaterial(unfinishedsearch);
    DisplayfinishedMaterial(finishedsearch);
  }
}

function FilterMaterial() {
  let CheckedOptions = {};
  console.log(Object.entries(CheckedOptions).length);

  filterOptions.forEach((option) => {
    if (option.checked) {
      if (!Object.hasOwn(CheckedOptions, option.name)) {
        CheckedOptions[option.name] = [];
      }
      CheckedOptions[option.name].push(option.value);
    }
  });

  console.log(CheckedOptions);

  if (Object.entries(CheckedOptions).length < 1) {
    Start = 0;
    filter = false;
    if (Search) {
      End = unfinishedsearch.length >= 3 ? 3 : unfinishedsearch.length;
      DisplayUnfinishedMaterial(unfinishedsearch);
      DisplayfinishedMaterial(finishedsearch);
    } else {
      End = unfinished.length >= 3 ? 3 : unfinished.length;
      DisplayUnfinishedMaterial(unfinished);
      DisplayfinishedMaterial(finished);
    }
  } else {
    if (Search) {
      console.log("search");
      unfinishedfiltered = [];
      unfinishedsearch.forEach((element) => {
        let within = false;
        for (let key in CheckedOptions) {
          console.log(key);
          if (CheckedOptions[key].includes(element[key])) {
            within = true;
          } else {
            within = false;
            break;
          }
        }
        if (within) {
          unfinishedfiltered.push(element);
        }
      });

      Start = 0;
      End = unfinishedfiltered.length >= 3 ? 3 : unfinishedfiltered.length;
      filter = true;

      finishedfiltered = [];
      finishedsearch.forEach((element) => {
        within = false;
        for (let key in CheckedOptions) {
          if (CheckedOptions[key].includes(element[key])) {
            within = true;
          } else {
            within = false;
            break;
          }
        }
        console.log(within);
        if (within) {
          finishedfiltered.push(element);
        }
      });

      console.log(finishedfiltered);
    } else {
      // time to filter
      unfinishedfiltered = [];
      unfinished.forEach((element) => {
        let within = false;
        for (let key in CheckedOptions) {
          console.log(key);
          if (CheckedOptions[key].includes(element[key])) {
            within = true;
          } else {
            within = false;
            break;
          }
        }
        if (within) {
          unfinishedfiltered.push(element);
        }
      });

      Start = 0;
      End = unfinishedfiltered.length >= 3 ? 3 : unfinishedfiltered.length;
      filter = true;

      finishedfiltered = [];
      finished.forEach((element) => {
        within = false;
        for (let key in CheckedOptions) {
          if (CheckedOptions[key].includes(element[key])) {
            within = true;
          } else {
            within = false;
            break;
          }
        }
        if (within) {
          finishedfiltered.push(element);
        }
      });
    }
    DisplayUnfinishedMaterial(unfinishedfiltered);
    DisplayfinishedMaterial(finishedfiltered);
  }
}
