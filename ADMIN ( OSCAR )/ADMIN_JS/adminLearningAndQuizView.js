
function goToMaterialPage(id){
    console.log(id);
    window.location.href = "adminLearningSelected.php?materialID=" + encodeURIComponent(id);
}

function goToQuizPage(id){
    console.log(id);
    window.location.href = "adminQuizSelected.php?quizID=" + encodeURIComponent(id);
}


function displayLearningMaterial(obtainedData){
    let container = document.getElementById("areaOfInfo");
    let containerData = "";
    obtainedData.forEach(element => {
        let data = 
        `
            <div class="selection admin" onclick="goToMaterialPage(`+element['id']+`)"> <!-- options -->
                <div class="content-title">
                    `+element['title'].toUpperCase()+` 
                    <div class="title-detail">`+element['instructor_name']+`</div>
                </div>
                    <div class="content">SUBJECT:<div>`+element['subject'].toUpperCase()+`</div></div>
                    <div class="content">CHAPTER:<div>`+element['chapter']+`</div></div>
                    <div class="content">FORM:<div>`+element['level']+`</div>
                </div>
            </div>
                `;

        containerData = containerData + data;
    });
    console.log(containerData);
    container.innerHTML = containerData;
}

function displayQuiz(obtainedData){
    let container = document.getElementById("areaOfInfo");
    let containerData = "";
    obtainedData.forEach(element => {
        let data = 
        `
            <div class="selection admin" onclick="goToQuizPage(`+element['id']+`)"> <!-- options -->
                <div class="content-title">
                    `+element['title'].toUpperCase()+` 
                    <div class="title-detail">`+element['instructor_name']+`</div>
                </div>
                    <div class="content">SUBJECT:<div>`+element['subject'].toUpperCase()+`</div></div>
                    <div class="content">FORM:<div>`+element['level']+`</div>
                </div>
            </div>
                `;

        containerData = containerData + data;
    });
    console.log(containerData);
    container.innerHTML = containerData;
}



document.addEventListener("DOMContentLoaded", () => {
    const textInput = document.getElementById("searchName");
    const searchtype = document.querySelectorAll(".searchType");
    const learningStyle = document.querySelectorAll(".learningStyle");
    const formCheckbox = document.querySelectorAll(".formCheckbox");
    const subjectType = document.querySelectorAll(".subjectType");

    function applyFilters(){
        let nameMatch = '';
        const searchText = textInput.value.toLowerCase();
        const selectedInput = Array.from(searchtype)
            .filter(checkbox => checkbox.checked)
            .map(checkbox => checkbox.value);
        const selectedLearningStyle = Array.from(learningStyle)
            .filter(type => type.checked)
            .map(type => type.value);
        const selectedForms = Array.from(formCheckbox)
            .filter(checkbox => checkbox.checked)
            .map(checkbox => checkbox.value);
        const selectedSubject = Array.from(subjectType)
            .filter(type => type.checked)
            .map(type => type.value);
        if(currentDisplay === "learningMaterial"){
            const filteredLearningMaterials = learningMaterialDetails.filter(material => {
                if (selectedInput[0] == null){
                    nameMatch = material['title'].toLowerCase().includes(searchText);
                }
                else{
                    switch (selectedInput[0]) {
                        case "title":
                            nameMatch = material['title'].toLowerCase().includes(searchText);
                            break;
                    
                        case "instructor":
                            nameMatch = material['instructor_name'].toLowerCase().includes(searchText);
                            break;
                    }
                }
                const styleMatch = selectedLearningStyle.length === 0 || selectedLearningStyle.includes(material['learning_type']);
                const formMatch = selectedForms.length === 0 || selectedForms.includes(material['level']);
                const subjectMatch = selectedSubject.length === 0 || selectedSubject.includes(material['subject']);
                return nameMatch && styleMatch && formMatch && subjectMatch;
            });

            displayLearningMaterial(filteredLearningMaterials);
        }
        else if(currentDisplay === "quiz"){
            const filteredQuizzes = quizDetails.filter(quiz => {
                if (selectedInput[0] == null){
                    nameMatch = quiz['title'].toLowerCase().includes(searchText);
                }
                else{
                    switch (selectedInput[0]) {
                        case "title":
                            nameMatch = quiz['title'].toLowerCase().includes(searchText);
                            break;
                    
                        case "instructor":
                            nameMatch = quiz['instructor_name'].toLowerCase().includes(searchText);
                            break;
                    }
                }
                const formMatch = selectedForms.length === 0 || selectedForms.includes(quiz['level']);
                const subjectMatch = selectedSubject.length === 0 || selectedSubject.includes(quiz['subject']);
                return nameMatch && formMatch && subjectMatch;
            });

            displayQuiz(filteredQuizzes);
        }
    }

    textInput.addEventListener("input", applyFilters);
    searchtype.forEach(checkbox => checkbox.addEventListener("change", applyFilters));
    learningStyle.forEach(checkbox => checkbox.addEventListener("change", applyFilters));
    formCheckbox.forEach(checkbox => checkbox.addEventListener("change", applyFilters));
    subjectType.forEach(checkbox => checkbox.addEventListener("change", applyFilters));
});
