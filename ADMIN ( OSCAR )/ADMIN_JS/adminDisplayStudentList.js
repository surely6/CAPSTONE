const studentList = document.getElementsByClassName('userList');
const listLocation = studentList[0];
let studentID;

// location is location to append to row ID is the studentID
// this is just to generate user list
function generateRow(location, rowID, student) {
        const ul = document.createElement('UL');
        ul.setAttribute('class', 'columnLabels');
        ul.addEventListener('click', function(){
            goToProfile(rowID);
        });
        ul.addEventListener('mouseover', function(){
            ul.style.backgroundColor = "rgb(186, 185, 185)";
        });
        
        ul.addEventListener('mouseout', function(){
            ul.style.backgroundColor = '';
        });
        ul.style.cursor = "pointer";

        const studentNumber = student['StudentNum'];
        const studentName = student['StudentName'];
        const studentEmail = student['StudentEmail'];
        const studentLevel = student['StudentLevel'];

        const li = document.createElement('LI');
        li.setAttribute('id', 'column1')
        const a = document.createElement('A');
        const textNode = document.createTextNode(studentNumber);
        a.appendChild(textNode);
        a.style.color = "rgb(0,0,0)";
        li.appendChild(a);
        ul.appendChild(li);

        const li2 = document.createElement('LI');
        li2.setAttribute('id', 'column2')
        const a2 = document.createElement('A');
        const textNode2 = document.createTextNode(studentName);
        a2.appendChild(textNode2);
        a2.style.color = "rgb(0,0,0)";
        li2.appendChild(a2);
        ul.appendChild(li2);

        const li3 = document.createElement('LI');
        li3.setAttribute('id', 'column3')
        const a3 = document.createElement('A');
        const textNode3 = document.createTextNode(studentEmail);
        a3.appendChild(textNode3);
        a3.style.color = "rgb(0,0,0)";
        li3.appendChild(a3);
        ul.appendChild(li3);

        const li4 = document.createElement('LI');
        li4.setAttribute('id', 'column4')
        const a4 = document.createElement('A');
        const textNode4 = document.createTextNode(studentLevel);
        a4.appendChild(textNode4);
        a4.style.color = "rgb(0,0,0)";
        li4.appendChild(a4);
        ul.appendChild(li4);
       
        location.appendChild(ul);
}

for (let i = 0; i < StudentData.length; i++) {
    let student = StudentData[i];

    let studentID = student['StudentID'];
    generateRow(listLocation, studentID, student);
}

// filtering
document.addEventListener("DOMContentLoaded", () => {
    const studentList = document.getElementsByClassName('userList');
    const listLocation = studentList[0];
    const textInput = document.getElementById("searchName");
    const formCheckbox = document.querySelectorAll(".formCheckbox");
    const nameOrEmail = document.querySelectorAll(".nameOrEmail");

    function clearUserList() {
        const entireUserList = listLocation.querySelectorAll("ul.columnLabels");
        entireUserList.forEach((row, i) => {
            if (i > 0) {
                row.remove(); 
            }   
        });
    }

    function applyFilters() {
        let nameMatch = "";
        const searchText = textInput.value.toLowerCase();
        const selectedForms = Array.from(formCheckbox)
            .filter(checkbox => checkbox.checked)
            .map(checkbox => checkbox.value);
        const selectedInput = Array.from(nameOrEmail)
            .filter(type => type.checked)
            .map(type => type.value);

        const filteredStudents = StudentData.filter(student => {
            if (selectedInput[0] == null){
                nameMatch = student['StudentName'].toLowerCase().includes(searchText);
            }
            else{
                switch (selectedInput[0]) {
                    case "name":
                        nameMatch = student['StudentName'].toLowerCase().includes(searchText);
                        break;
                
                    case "email":
                        nameMatch = student['StudentEmail'].toLowerCase().includes(searchText);
                        break;
                }
            }
            const formMatch = selectedForms.length === 0 || selectedForms.includes(student['StudentLevel']);
            return nameMatch && formMatch;
        });

        clearUserList();
        filteredStudents.forEach(student => {
            generateRow(listLocation, student['StudentID'], student);
        });
    }

    
    textInput.addEventListener("input", applyFilters);
    formCheckbox.forEach(checkbox => checkbox.addEventListener("change", applyFilters));
    nameOrEmail.forEach(checkbox => checkbox.addEventListener("change", applyFilters));
});


function goToProfile(rowID){
    console.log('Proceeding to profile with '+rowID+'....');
    studentID = rowID;
    window.location.href = "adminStudentUserViewSelected.php?studentID=" + encodeURIComponent(rowID);
}
