import {
  ClassicEditor,
  Autoformat,
  AutoImage,
  Autosave,
  Base64UploadAdapter,
  BlockQuote,
  Bold,
  CloudServices,
  Essentials,
  FontBackgroundColor,
  FontColor,
  FontFamily,
  FontSize,
  Heading,
  Highlight,
  ImageBlock,
  ImageCaption,
  ImageInline,
  ImageInsert,
  ImageInsertViaUrl,
  ImageResize,
  ImageStyle,
  ImageTextAlternative,
  ImageToolbar,
  ImageUpload,
  Indent,
  IndentBlock,
  Italic,
  Link,
  LinkImage,
  List,
  ListProperties,
  MediaEmbed,
  Paragraph,
  PasteFromOffice,
  PlainTableOutput,
  SpecialCharacters,
  SpecialCharactersArrows,
  SpecialCharactersCurrency,
  SpecialCharactersEssentials,
  SpecialCharactersLatin,
  SpecialCharactersMathematical,
  SpecialCharactersText,
  Strikethrough,
  Table,
  TableCaption,
  TableCellProperties,
  TableColumnResize,
  TableLayout,
  TableProperties,
  TableToolbar,
  TextTransformation,
  TodoList,
  Underline,
} from "ckeditor5";

const LICENSE_KEY = "GPL"; // or <YOUR_LICENSE_KEY>.

let editorConfig = {
  toolbar: {
    items: [
      "undo",
      "redo",
      "heading",
      "|",
      "fontSize",
      "fontFamily",
      "fontColor",
      "fontBackgroundColor",
      "|",
      "bold",
      "italic",
      "underline",
      "strikethrough",
      "|",
      "specialCharacters",
      "link",
      "insertImage",
      "mediaEmbed",
      "insertTable",
      "insertTableLayout",
      "highlight",
      "blockQuote",
      "|",
      "bulletedList",
      "numberedList",
      "todoList",
      "outdent",
      "indent",
    ],
    shouldNotGroupWhenFull: true,
  },
  plugins: [
    Autoformat,
    AutoImage,
    Autosave,
    Base64UploadAdapter,
    BlockQuote,
    Bold,
    CloudServices,
    Essentials,
    FontBackgroundColor,
    FontColor,
    FontFamily,
    FontSize,
    Heading,
    Highlight,
    ImageBlock,
    ImageCaption,
    ImageInline,
    ImageInsert,
    ImageInsertViaUrl,
    ImageResize,
    ImageStyle,
    ImageTextAlternative,
    ImageToolbar,
    ImageUpload,
    Indent,
    IndentBlock,
    Italic,
    Link,
    LinkImage,
    List,
    ListProperties,
    MediaEmbed,
    Paragraph,
    PasteFromOffice,
    PlainTableOutput,
    SpecialCharacters,
    SpecialCharactersArrows,
    SpecialCharactersCurrency,
    SpecialCharactersEssentials,
    SpecialCharactersLatin,
    SpecialCharactersMathematical,
    SpecialCharactersText,
    Strikethrough,
    Table,
    TableCaption,
    TableCellProperties,
    TableColumnResize,
    TableLayout,
    TableProperties,
    TableToolbar,
    TextTransformation,
    TodoList,
    Underline,
  ],
  fontFamily: {
    supportAllValues: true,
  },
  fontSize: {
    options: [10, 12, 14, "default", 18, 20, 22],
    supportAllValues: true,
  },
  heading: {
    options: [
      {
        model: "paragraph",
        title: "Paragraph",
        class: "ck-heading_paragraph",
      },
      {
        model: "heading1",
        view: "h1",
        title: "Heading 1",
        class: "ck-heading_heading1",
      },
      {
        model: "heading2",
        view: "h2",
        title: "Heading 2",
        class: "ck-heading_heading2",
      },
      {
        model: "heading3",
        view: "h3",
        title: "Heading 3",
        class: "ck-heading_heading3",
      },
      {
        model: "heading4",
        view: "h4",
        title: "Heading 4",
        class: "ck-heading_heading4",
      },
      {
        model: "heading5",
        view: "h5",
        title: "Heading 5",
        class: "ck-heading_heading5",
      },
      {
        model: "heading6",
        view: "h6",
        title: "Heading 6",
        class: "ck-heading_heading6",
      },
    ],
  },
  image: {
    toolbar: [
      "toggleImageCaption",
      "imageTextAlternative",
      "|",
      "imageStyle:inline",
      "imageStyle:wrapText",
      "imageStyle:breakText",
      "|",
      "resizeImage",
    ],
  },
  initialData: "",
  licenseKey: LICENSE_KEY,
  link: {
    addTargetToExternalLinks: true,
    defaultProtocol: "https://",
    decorators: {
      toggleDownloadable: {
        mode: "manual",
        label: "Downloadable",
        attributes: {
          download: "file",
        },
      },
    },
  },
  list: {
    properties: {
      styles: true,
      startIndex: true,
      reversed: true,
    },
  },
  placeholder: "Type or paste your content here!",
  table: {
    contentToolbar: [
      "tableColumn",
      "tableRow",
      "mergeTableCells",
      "tableProperties",
      "tableCellProperties",
    ],
  },
};
// EDITOR STUFF DO NOT EDIT AND/OR REMOVE

export let Editors = [];
export let Data = [];
let AnsAmount = [2];
let Answers = [[]];
let CorrectAns = [[]];
let type = [];

let BaseSubjects = ["english", "malay", "math", "science", "history"];
let LowerSubjects = ["geography"];
let UpperSubjects = [
  "accounting",
  "economy",
  "business",
  "add maths",
  "physics",
  "chemistry",
  "biology",
];

let SubjectOptions = document.getElementById("subjects");
let FormSelected = document.getElementById("forms");

let QuestionAmount = 1;

window.addEventListener("load", function () {
  ChangeSubject();
  InsertEditors();
  InsertAnswers();
  CreateEditor();
  DisplayRemoveButton();
});

window.ChangeSubject = () => {
  let data;
  let subject = document.querySelector("#subjects").value;
  BaseSubjects.forEach((element) => {
    if (element == subject) {
      data =
        data +
        '<option value="' +
        element +
        '" selected>' +
        element.charAt(0).toUpperCase() +
        element.slice(1) +
        "</option>";
    } else {
      data =
        data +
        '<option value="' +
        element +
        '">' +
        element.charAt(0).toUpperCase() +
        element.slice(1) +
        "</option>";
    }
  });
  console.log(FormSelected.value);
  if (FormSelected.value > 3) {
    UpperSubjects.forEach((element) => {
      if (element == subject) {
        data =
          data +
          '<option value="' +
          element +
          '" selected>' +
          element.charAt(0).toUpperCase() +
          element.slice(1) +
          "</option>";
      } else {
        data =
          data +
          '<option value="' +
          element +
          '">' +
          element.charAt(0).toUpperCase() +
          element.slice(1) +
          "</option>";
      }
    });
  } else {
    LowerSubjects.forEach((element) => {
      if (element == subject) {
        data =
          data +
          '<option value="' +
          element +
          '" selected>' +
          element.charAt(0).toUpperCase() +
          element.slice(1) +
          "</option>";
      } else {
        data =
          data +
          '<option value="' +
          element +
          '">' +
          element.charAt(0).toUpperCase() +
          element.slice(1) +
          "</option>";
      }
    });
  }
  SubjectOptions.innerHTML = data;
};

window.RemoveEditors = (index) => {
  console.log(Answers);
  console.log(index);
  QuestionAmount--;
  ReadEditors();
  ReadAnswers();
  ReadQuetsionType();
  if (index > -1) {
    Editors.splice(index, 1);
    Data.splice(index, 1);
    CorrectAns.forEach((element, i) => {
      if (i > index) {
        element.forEach((select, e) => {
          console.log(select);
          element[e] = (select - AnsAmount[index]).toString();
          console.log(select);
        });
      }
    });
    CorrectAns.splice(index, 1);
    Answers.splice(index, 1);
    AnsAmount.splice(index, 1);
    type.splice(index, 1);
  }
  console.log(CorrectAns);
  console.log(Answers);
  InsertEditors();
  InsertQuestionType();
  InsertAnswers();
  CreateEditor();
  DisplayRemoveButton();
};

window.ReduceAnswers = (index) => {
  ReadEditors();
  ReadAnswers();
  ReadQuetsionType();
  if (AnsAmount[index] <= 2) {
    alert("Minimum of 2 answers is required");
  } else {
    AnsAmount[index]--;
    console.log(AnsAmount);
  }
  InsertEditors();
  InsertQuestionType();
  InsertAnswers();
  CreateEditor();
  DisplayRemoveButton();
};

window.AddAnswers = (index) => {
  ReadEditors();
  ReadAnswers();
  ReadQuetsionType();
  if (AnsAmount[index] >= 4) {
    alert("Maximum amount of answers reached");
  } else {
    AnsAmount[index]++;
    console.log(AnsAmount);
  }
  InsertEditors();
  InsertQuestionType();
  InsertAnswers();
  CreateEditor();
  DisplayRemoveButton();
};

function CreateEditor() {
  Editors = [];
  console.log("creating");
  console.log(document.querySelectorAll("#editor"));
  document.querySelectorAll("#editor").forEach((element, index) => {
    if (index < Data.length) {
      editorConfig["initialData"] = Data[index];
    } else {
      editorConfig["initialData"] = "";
    }
    ClassicEditor.create(element, editorConfig)
      .then((newEditor) => {
        Editors.push(newEditor);
      })
      .catch((error) => {
        console.log(error);
      });
  });
  console.log(Editors);
}

function ReadEditors() {
  console.log("reading");
  Data = [];
  Editors.forEach((element) => {
    console.log(element);
    Data.push(element.getData());
  });
}

function InsertEditors() {
  console.log("inserting");
  let WholePart = document.querySelector("#question");
  let questions = "";
  for (let i = 0; i < QuestionAmount; i++) {
    questions =
      questions +
      `<div class="section">
                    <label for="question">QUESTION ` +
      (i + 1) +
      `:</label> 
                    <button id="remove" onclick="RemoveEditors(` +
      i +
      `)" style="display: block;">DELETE</button>
                    <select id="question-type">
						<option value="Q01">SINGLE ANSWER</option>
                    	<option value="Q02">MULTIPLE ANSWER</option>
                    </select>
                    <textarea name="part ` +
      (i + 1) +
      `" id="editor" required></textarea>
                    <label>ANSWERS:</label>
                    <button id="answer" onclick="ReduceAnswers(` +
      i +
      `)" style="display: block;">REMOVE ANSWER</button>
                    <button id="answer" onclick="AddAnswers(` +
      i +
      `)" style="display: block; background-color: var(--green);">ADD ANSWER</button>
                        <div id="answer-section">
                        </div>
            </div>`;
  }
  WholePart.innerHTML = questions;
}

function ReadAnswers() {
  console.log("reading answer");
  console.log(AnsAmount);
  let AnswerSection = document.querySelectorAll("#ans");
  let n = 0;
  Answers = [];
  AnsAmount.forEach((element) => {
    let temp = [];
    for (let i = 0; i < element; i++) {
      temp.push(AnswerSection[n].value);
      n++;
    }
    Answers.push(temp);
  });

  CorrectAns = [];
  let checkbox = document.querySelectorAll("input[type = checkbox]");
  n = 0;
  AnsAmount.forEach((element) => {
    let temp = [""];
    for (let i = 0; i < element; i++) {
      console.log(n);
      if (checkbox[n].checked) {
        temp.push(i.toString()); // Use i, not n
      }
      n++;
    }
    if (temp.length > 1) {
      temp.splice(0, 1);
    }
    CorrectAns.push(temp);
  });

  console.log(CorrectAns);
}

function InsertAnswers() {
  let AnswerSection = document.querySelectorAll("#answer-section");
  console.log(CorrectAns);
  console.log(Answers);
  let CheckAmount = 0;
  AnswerSection.forEach((element, index) => {
    let answers = "";
    for (let j = 0; j < AnsAmount[index]; j++) {
      // Use j as the value for the checkbox, not CheckAmount
      if (j < Answers[index].length) {
        if (CorrectAns[index].includes(j.toString())) {
          // checked
          answers += `<div class="answer">
          <div class="round">
            <input type="checkbox" id="correct-answer-${index}-${j}" value="${j}" checked/>
            <label for="correct-answer-${index}-${j}"></label>
          </div>
          <input type="text" placeholder="answer" value="${Answers[index][j]}" id="ans">
        </div>`;
        } else {
          answers += `<div class="answer">
          <div class="round">
            <input type="checkbox" id="correct-answer-${index}-${j}" value="${j}"/>
            <label for="correct-answer-${index}-${j}"></label>
          </div>
          <input type="text" placeholder="answer" value="${Answers[index][j]}" id="ans">
        </div>`;
        }
      } else {
        answers += `<div class="answer">
        <div class="round">
          <input type="checkbox" id="correct-answer-${index}-${j}" value="${j}"/>
          <label for="correct-answer-${index}-${j}"></label>
        </div>
        <input type="text" placeholder="answer" value="" id="ans">
      </div>`;
      }
    }
    element.innerHTML = answers;
  });
}

function ReadQuetsionType() {
  type = [];
  let QuestionType = document.querySelectorAll("#question-type");
  QuestionType.forEach((element) => {
    type.push(element.value);
  });
}

function InsertQuestionType() {
  let info;
  let QuestionType = document.querySelectorAll("#question-type");
  QuestionType.forEach((element, index) => {
    if (type[index] == "Q02") {
      info = `<option value="Q01">SINGLE ANSWER</option>
                    <option value="Q02" selected>MULTIPLE ANSWER</option>`;
    } else {
      info = `<option value="Q01" selected>SINGLE ANSWER</option>
                    <option value="Q02">MULTIPLE ANSWER</option>`;
    }
    element.innerHTML = info;
  });
}

function DisplayRemoveButton() {
  if (QuestionAmount == 1) {
    document.querySelectorAll("#remove").forEach((element) => {
      element.style.display = "none";
      element.style.marginTop = 0;
    });
  } else {
    document.querySelectorAll("#remove").forEach((element) => {
      element.style.display = "block";
      element.style.marginTop = 0;
    });
  }
}

document.querySelector("#add").addEventListener("click", function () {
  QuestionAmount++;
  ReadEditors();
  ReadAnswers();
  ReadQuetsionType();
  AnsAmount.push(2);
  Answers.push([]);
  InsertEditors();
  InsertQuestionType();
  InsertAnswers();
  CreateEditor();
  DisplayRemoveButton();
});

window.onbeforeunload = function (event) {
  var s = "You have unsaved changes. Really leave?";
  event = event || window;
  if (event) {
    event.returnValue = s;
  }
  return s;
};

let Status = false;

async function postData(url, data) {
  $.ajax({
    method: "POST",
    url: url,
    data: data,
  }).done(function (response) {
    console.log(response);
  });
}

// Example usage:
const url = "Upload Quiz.php";
let data = {
  ID: "",
  form: "",
  subject: "",
  title: "",
  description: "",
  delete: "",
  saved: "",
  completion: "",
  question: [],
  question_type: [],
  answer: [],
  correct: [],
};

function ObtainData() {
  let id = 0;
  let form = document.querySelector("#forms").value;
  let subject = document.querySelector("#subjects").value;
  let title = document.querySelector("#title").value;
  let description = document.querySelector("textarea#autoresizing").value;
  let chapter = document.querySelector("#chapter").value;

  ReadQuetsionType();
  ReadEditors();
  ReadAnswers();

  data = {
    ID: id,
    form: form,
    subject: subject,
    title: title,
    description: description,
    delete: false,
    saved: Status,
    completion: false,
    question: Data,
    question_type: type,
    chapter: chapter,
    answer: Answers,
    correct: CorrectAns,
  };

  console.log(data);
}

document.querySelector("#publish").addEventListener("click", function () {
  ObtainData();

  if (data["title"] == "" || data["description"] == "") {
    alert("Please Insert a title or description.");
    return;
  }

  for (let index = 0; index < data["question"].length; index++) {
    if (data["question"][index] == "") {
      alert("Please Insert content in Question " + (index + 1) + ".");
      return;
    }

    if (data["answer"][index].includes("")) {
      alert("Please Insert answer in Question " + (index + 1) + ".");
      return;
    }

    if (data["correct"][index][0] == "") {
      alert(
        "Please select at least one correct answer in Question " +
          (index + 1) +
          "."
      );
      return;
    }

    if (
      data["question_type"][index] == "Q01" &&
      data["correct"][index].length > 1
    ) {
      alert(
        "Only one correct answer is allowed in Question " + (index + 1) + "."
      );
      return;
    }
  }
  data["completion"] = true;
  postData(url, data);
  alert("Quiz Made");
  window.location.href = "Quiz View.php";
});

document.querySelector("#draft").addEventListener("click", function () {
  ObtainData();
  postData(url, data);
  alert("Saved");
  Status = true;
});

document.querySelector("#delete").addEventListener("click", function () {
  if (
    confirm(
      "Are you sure you want to delete? All Content Made and saved will be deleted."
    )
  ) {
    if (Status) {
      ObtainData();
      data["delete"] = true;
      postData(url, data);
      alert("Quiz Deleted");
    }
    window.location.href = "Learning Material View.php";
  }
});
