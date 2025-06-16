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

let Editors = [];
let Data = data["content"];

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

let PartAmount = Data.length;

window.addEventListener("load", function () {
  InsertInfo();
  InsertEditors();
  CreateEditor();
});

function InsertInfo() {
  let form = document.querySelectorAll("#forms option");
  form.forEach((options) => {
    options.removeAttribute("selected");
    if (options.value == data["form"]) {
      options.setAttribute("selected", "");
    }
  });
  ChangeSubject();
  let subject = document.querySelectorAll("#subjects option");
  subject.forEach((options) => {
    options.removeAttribute("selected");
    if (options.value == data["subject"]) {
      options.setAttribute("selected", "");
    }
  });
  document.querySelector("#chapter").value = data["chapter"];
  let learn_style = document.querySelectorAll("#learning-style option");
  learn_style.forEach((options) => {
    options.removeAttribute("selected");
    if (options.value == data["learning_style"]) {
      options.setAttribute("selected", "");
    }
  });
  document.querySelector("#title").value = data["title"];
  document.querySelector("textarea#autoresizing").value = data["description"];
}

window.ChangeSubject = () => {
  let data;
  BaseSubjects.forEach((element) => {
    data =
      data +
      '<option value="' +
      element +
      '">' +
      element.charAt(0).toUpperCase() +
      element.slice(1) +
      "</option>";
  });
  console.log(FormSelected.value);
  if (FormSelected.value > 3) {
    UpperSubjects.forEach((element) => {
      data =
        data +
        '<option value="' +
        element +
        '">' +
        element.charAt(0).toUpperCase() +
        element.slice(1) +
        "</option>";
    });
  } else {
    LowerSubjects.forEach((element) => {
      data =
        data +
        '<option value="' +
        element +
        '">' +
        element.charAt(0).toUpperCase() +
        element.slice(1) +
        "</option>";
    });
  }
  SubjectOptions.innerHTML = data;
};

window.RemoveEditors = (index) => {
  PartAmount--;
  ReadEditors();
  if (index > -1) {
    Editors.splice(index, 1);
    Data.splice(index, 1);
  }
  console.log(index);
  console.log(Editors);
  console.log(Data);
  InsertEditors();
  CreateEditor();
  DisplayRemoveButton();
};

function CreateEditor() {
  Editors = [];
  console.log("creating");
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
  let WholePart = document.querySelector("#part");
  let parts = "";
  for (let i = 0; i < PartAmount; i++) {
    parts =
      parts +
      `<div class="section">
                <label for="part ` +
      (i + 1) +
      `">PART ` +
      (i + 1) +
      `:</label> <button id="remove" onclick="RemoveEditors(` +
      i +
      `)" style="display: none;">DELETE</button>
                <textarea name="part ` +
      (i + 1) +
      `" id="editor" required></textarea>
        </div>`;
  }
  WholePart.innerHTML = parts;
}

function DisplayRemoveButton() {
  if (PartAmount == 1) {
    document.querySelectorAll("#remove").forEach((element) => {
      element.style.display = "none";
    });
  } else {
    document.querySelectorAll("#remove").forEach((element) => {
      element.style.display = "block";
    });
  }
}

document.querySelector("#add").addEventListener("click", function () {
  PartAmount++;
  ReadEditors();
  InsertEditors();
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

let Status = true;

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
const url = "Upload Learning Material.php";

function ObtainData() {
  let id = document.querySelector("#id").innerHTML;
  let form = document.querySelector("#forms").value;
  let subject = document.querySelector("#subjects").value;
  let chapter = document.querySelector("#chapter").value;
  let learning_style = document.querySelector("#learning-style").value;
  let title = document.querySelector("#title").value;
  let description = document.querySelector("textarea#autoresizing").value;
  ReadEditors();

  data = {
    ID: id,
    form: form,
    subject: subject,
    chapter: chapter,
    learning_style: learning_style,
    title: title,
    description: description,
    delete: false,
    saved: Status,
    completion: true,
    content: Data,
  };

  console.log(data);
}

document.querySelector("#publish").addEventListener("click", function () {
  ObtainData();
  let chapterValue = data["chapter"].trim();
  if (
    chapterValue === "" ||
    isNaN(chapterValue) ||
    Number(chapterValue) < 0 ||
    !Number.isInteger(Number(chapterValue))
  ) {
    alert("Please Insert a suitable chapter.");
    return;
  }
  if (data["title"] == "" || data["description"] == "") {
    alert("Please Insert a title or description.");
    return;
  }
  data["content"].forEach((element, index) => {
    if (element == "") {
      alert("Please Insert content in Part " + (index + 1) + ".");
      return;
    }
  });
  console.log(data);
  data["completion"] = true;
  postData(url, data);
  alert("Learning Material Made");
  window.location.href = "Learning Material View.php";
});

document.querySelector("#draft").addEventListener("click", function () {
  ObtainData();
  console.log(data);
  if (
    isNaN(data["chapter"]) ||
    data["chapter"] == "" ||
    Number(data["chapter"]) > 0
  ) {
    data["completion"] = false;
    return;
  }
  if (data["title"] == "" || data["description"] == "") {
    data["completion"] = false;
    return;
  }
  data["content"].forEach((element) => {
    if (element == "") {
      data["completion"] = false;
      return;
    }
  });
  postData(url, data);
  alert("Saved");
  Status = true;
});

document.querySelector("#draft").addEventListener("click", function () {
  ObtainData();
  console.log(data);
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
      alert("Material Deleted");
    }
    window.location.href = "Learning Material View.php";
  }
});
