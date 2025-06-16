const instructorList = document.getElementsByClassName('userList');
const listLocation = instructorList[0];
let instructorID;

// location is location to append to row ID is the instructorID
// this is just to generate user list
function generateRow(location, rowID, instructor) {
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

        const instructorNumber = instructor['InstructorNum'];
        const instructorName = instructor['InstructorName'];
        const instructorEmail = instructor['InstructorEmail'];

        const li = document.createElement('LI');
        li.setAttribute('id', 'column1')
        const a = document.createElement('A');
        const textNode = document.createTextNode(instructorNumber);
        a.appendChild(textNode);
        a.style.color = "rgb(0,0,0)";
        li.appendChild(a);
        ul.appendChild(li);

        const li2 = document.createElement('LI');
        li2.setAttribute('id', 'column2')
        const a2 = document.createElement('A');
        const textNode2 = document.createTextNode(instructorName);
        a2.appendChild(textNode2);
        a2.style.color = "rgb(0,0,0)";
        li2.appendChild(a2);
        ul.appendChild(li2);

        const li3 = document.createElement('LI');
        li3.setAttribute('id', 'column3')
        const a3 = document.createElement('A');
        const textNode3 = document.createTextNode(instructorEmail);
        a3.appendChild(textNode3);
        a3.style.color = "rgb(0,0,0)";
        li3.appendChild(a3);
        ul.appendChild(li3);
       
        location.appendChild(ul);
}

for (let i = 0; i < PendInstructorData.length; i++) {
    let instructor = PendInstructorData[i];

    let instructorID = instructor['InstructorID'];
    generateRow(listLocation, instructorID, instructor);
}
    
document.addEventListener("DOMContentLoaded", () => {
    const instructorList = document.getElementsByClassName('userList');
    const listLocation = instructorList[0];
    const textInput = document.getElementById("searchName");
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
        let nameMatch = '';
        const searchText = textInput.value.toLowerCase();
        const selectedInput = Array.from(nameOrEmail)
            .filter(type => type.checked)
            .map(type => type.value);

        const filteredInstructors = PendInstructorData.filter(instructor => {
            if (selectedInput[0] == null){
                nameMatch = instructor['InstructorName'].toLowerCase().includes(searchText);
            }
            else{
                switch (selectedInput[0]) {
                    case "name":
                        nameMatch = instructor['InstructorName'].toLowerCase().includes(searchText);
                        break;
                
                    case "email":
                        nameMatch = instructor['InstructorEmail'].toLowerCase().includes(searchText);
                        break;
                }
            }
            return nameMatch;
        });

        clearUserList();
        filteredInstructors.forEach(instructor => {
            generateRow(listLocation, instructor['InstructorID'], instructor);
        });
    }

    
    textInput.addEventListener("input", applyFilters);
    nameOrEmail.forEach(checkbox => checkbox.addEventListener("change", applyFilters));
});


function goToProfile(rowID){
    console.log('Proceeding to profile with '+rowID+'....');
    instructorID = rowID;
    window.location.href = "adminPendInstUserViewSelected.php?instructorID=" + encodeURIComponent(rowID);
}
