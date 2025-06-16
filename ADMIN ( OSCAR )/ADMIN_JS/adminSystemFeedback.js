const feedbackLocation = document.getElementsByClassName("feedbackArea");
const finalLocation = feedbackLocation[0];
let arrangement = "";
let feedbackData = [];

// generating the feedback stuff
function generateItems(location, data) {
  const feedbackID = data["FeedbackID"];
  const feedbackText = data["FeedbackText"];
  const dateOfFeedback = data["DateOfFeedback"];
  const nameOfFeedbacker = data["UserName"];
  const typeOfFeedbacker = data["UserType"];
  let imageLocal = "";

  const div = document.createElement("DIV");
  div.setAttribute("class", "actualFeedbacks");
  div.setAttribute("id", typeOfFeedbacker);

  // image, name, date, delete
  // image sect
  const ul = document.createElement("UL");
  ul.setAttribute("class", "pfpAndOther");
  const li = document.createElement("LI");
  li.setAttribute("id", "pfpBox");
  const a = document.createElement("A");
  if (typeOfFeedbacker == "instructor") {
    imageLocal =
      "/capstone/PROFILE/INSTRUCTOR ( SURELY )/" + data["ProfilePic"];
  } else if (typeOfFeedbacker == "student") {
    imageLocal = "/capstone/PROFILE/STUDENT ( PIKER )/" + data["ProfilePic"];
  }
  const img = document.createElement("IMG");
  img.setAttribute("src", imageLocal);
  a.appendChild(img);
  li.appendChild(a);
  ul.appendChild(li);

  // name and date sect
  const li2 = document.createElement("LI");
  li2.setAttribute("id", "nameDateBox");
  const a2 = document.createElement("A");
  const span = document.createElement("SPAN");
  span.setAttribute("id", "nameText");
  const textNode1 = document.createTextNode(nameOfFeedbacker);
  span.appendChild(textNode1);
  const span2 = document.createElement("SPAN");
  span2.setAttribute("id", "dateText");
  const textNode2 = document.createTextNode(dateOfFeedback);
  span2.appendChild(textNode2);
  a2.appendChild(span);
  a2.appendChild(span2);
  li2.appendChild(a2);
  ul.appendChild(li2);

  div.appendChild(ul);

  // delete sect
  const li3 = document.createElement("LI");
  li3.setAttribute("id", "deleteButton");
  const a3 = document.createElement("A");
  a3.addEventListener("click", function () {
    deleteFeedback(feedbackID);
  });
  const textNode3 = document.createTextNode("DELETE");
  a3.appendChild(textNode3);
  li3.appendChild(a3);
  ul.appendChild(li3);

  // main feedback
  const div2 = document.createElement("DIV");
  div2.setAttribute("id", "feedbackTextArea");
  const a4 = document.createElement("A");
  a4.setAttribute("id", "feedbackText");
  const textNode4 = document.createTextNode(feedbackText);
  a4.appendChild(textNode4);
  div2.appendChild(a4);

  div.appendChild(div2);

  location.appendChild(div);
}

function generateFeedback(feedbackArrangement) {
  arrangement = feedbackArrangement;
  fetch(
    `adminSystemFeedback.php?arrangementType=${encodeURIComponent(
      feedbackArrangement
    )}`
  )
    .then((res) => res.json())
    .then((data) => {
      feedbackData = data;
      console.log(data);
      const feedbackList = finalLocation.querySelectorAll(
        "div.actualFeedbacks"
      );
      feedbackList.forEach((row, i) => {
        row.remove();
      });

      data.forEach((feedbackData) => {
        generateItems(finalLocation, feedbackData);
      });
    });

  const otherCheckboxes = document.querySelectorAll(".searchType");
  otherCheckboxes.forEach((checkbox) => {
    checkbox.checked = false;
  });
}

function deleteFeedback(feedbackID) {
  if (confirm("Are you sure to delete this feedback?") == true) {
    let deletion = {
      feedbackID: feedbackID,
    };
    fetch("adminDeleteFeedback.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(deletion),
    })
      .then((response) => response.text())
      .then((after) => {
        let arrangementType = arrangement;
        generateFeedback(arrangementType);
        console.log("DELETION OF FEEDBACK ID AT", feedbackID, "IS SUCCESSFUL.");
      });
  }
}

document.addEventListener("DOMContentLoaded", () => {
  const feedbackLocation = document.getElementsByClassName("feedbackArea");
  const finalLocation = feedbackLocation[0];
  const textInput = document.getElementById("searchName");
  const searchtype = document.querySelectorAll(".searchType");

  function clearFeedback() {
    const entireFeedback = finalLocation.querySelectorAll(
      "div.actualFeedbacks"
    );
    entireFeedback.forEach((row, i) => {
      {
        row.remove();
      }
    });
  }

  function applyFilters() {
    let nameMatch = "";
    const searchText = textInput.value.toLowerCase();
    const selectedInput = Array.from(searchtype)
      .filter((checkbox) => checkbox.checked)
      .map((checkbox) => checkbox.value);

    const filteredFeedbacks = feedbackData.filter((feedback) => {
      if (selectedInput[0] == null) {
        nameMatch = feedback["UserName"].toLowerCase().includes(searchText);
      } else {
        switch (selectedInput[0]) {
          case "instructor":
            userMatch = feedback["UserType"]
              .toLowerCase()
              .includes("instructor");
            nameMatch = feedback["UserName"].toLowerCase().includes(searchText);
            break;

          case "student":
            userMatch = feedback["UserType"].toLowerCase().includes("student");
            nameMatch = feedback["UserName"].toLowerCase().includes(searchText);
            break;
        }
      }
      return nameMatch && userMatch;
    });

    clearFeedback();
    filteredFeedbacks.forEach((feedback) => {
      generateItems(finalLocation, feedback);
    });
  }

  textInput.addEventListener("input", applyFilters);
  searchtype.forEach((checkbox) =>
    checkbox.addEventListener("change", applyFilters)
  );
});
