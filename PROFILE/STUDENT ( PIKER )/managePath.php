<?php
session_start();

include("connection.php");
include("header.php");
include("bootstrapFile.html");
include("../../block.php");


$studentId = $_SESSION['user_id'];

$sql = "SELECT lm.material_title, lm.material_subject, s.id, s.material_id, s.sequence, s.due_date, i.instructor_name 
        FROM sequences s
        INNER JOIN learning_materials lm ON lm.material_id = s.material_id
        INNER JOIN learning_pathways lp ON lp.pathway_id = s.pathway_id
        INNER JOIN instructors i ON i.instructor_id = lm.instructor_id
        WHERE lp.student_id = '$studentId' 
        ORDER BY s.sequence ASC";
$result = mysqli_query($conn, $sql);

$learningPaths = [];
$maxSequence = 0;

if ($result->num_rows > 0) {
    $newSequence = 1;
    while ($row = $result->fetch_assoc()) {
        $sequenceId = $row['id'];
        $updateSql = "UPDATE sequences SET sequence = $newSequence WHERE id = $sequenceId"; //rearrange sequence each time reload
        $conn->query($updateSql);

        // Calculate days left
        $dueDate = new DateTime($row['due_date']);
        $currentDate = new DateTime();
        $interval = $currentDate->diff($dueDate);
        $daysLeft = $interval->format('%r%a'); // %r adds a negative sign if overdue

        $row['sequence'] = $newSequence;
        $row['days_left'] = $daysLeft; // Add days left to the row
        $learningPaths[] = $row;

        $newSequence++;

        if ($row['sequence'] > $maxSequence) {
            $maxSequence = $row['sequence']; //for select dropdown usage
        }
    }
} else {
    $message = "Personalize your own learning path now!";
}

$materialSql = "SELECT * FROM learning_materials 
                WHERE material_id NOT IN (
                    SELECT material_id 
                    FROM sequences 
                    WHERE pathway_id = (SELECT pathway_id FROM learning_pathways WHERE student_id = '$studentId')
                )
                AND material_learning_type = (
                    SELECT student_learning_style 
                    FROM students 
                    WHERE student_id = '$studentId'
                )";

$materialResult = mysqli_query($conn, $materialSql);

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Path</title>
    <style>
        body {
            background: var(--grey);
            font-family: "inder";
        }

        .main-timeline {
            position: relative;
        }

        .main-timeline::after {
            content: "";
            position: absolute;
            width: 6px;
            background-color: #445f35;
            top: 0;
            bottom: 0;
            left: 50%;
            margin-left: -3px;
            border-radius: 10px;
        }

        .timeline {
            position: relative;
            background-color: inherit;
            width: 50%;
        }

        .timeline::after {
            content: "";
            position: absolute;
            width: 25px;
            height: 25px;
            right: -13px;
            background-color: #6bf6ae;
            border: 5px solid #445f35;
            top: 15px;
            border-radius: 50%;
            z-index: 1;
        }

        .left {
            padding: 0px 40px 20px 0px;
            left: 0;
        }

        .right {
            padding: 0px 0px 20px 40px;
            left: 50%;
        }

        .left::before {
            content: " ";
            position: absolute;
            top: 18px;
            z-index: 1;
            right: 30px;
            border: medium solid white;
            border-width: 10px 0 10px 10px;
            border-color: transparent transparent transparent var(--dark-grey);
        }

        .right::before {
            content: " ";
            position: absolute;
            top: 18px;
            z-index: 1;
            left: 30px;
            border: medium solid white;
            border-width: 10px 10px 10px 0;
            border-color: transparent var(--dark-grey) transparent transparent;
        }

        .right::after {
            left: -12px;
        }

        @media screen and (max-width: 768px) {

            .main-timeline::after {
                left: 31px;
            }

            .timeline {
                width: 100%;
                padding-left: 70px;
                padding-right: 25px;
            }

            .timeline::before {
                left: 60px;
                border: medium solid white;
                border-width: 10px 10px 10px 0;
                border-color: transparent white transparent transparent;
            }

            .left::after,
            .right::after {
                left: 18px;
            }

            .left::before {
                right: auto;
            }

            .right {
                left: 0%;
            }
        }

        .header-line {
            margin-top: 30px;
            align-items: center;
        }

        .card-body p strong {
            font-size: 14px;
            color: #4CAF50;
        }

        .card-body p strong span {
            color: red;
        }

        .card {
            background: var(--dark-grey);
            color: white;
            border-radius: 10px;
        }

        button,
        input,
        optgroup,
        select,
        textarea {
            margin: 0;
            font-family: inherit;
            font-size: inherit;
            line-height: inherit;
            border: none;
            border-radius: 5px 10px;
            padding: 5px 10px;
            border-radius: 10px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3), 0 6px 6px rgba(0, 0, 0, 0.2), 0 0 0 1px rgba(255, 255, 255, 0.02);
            background: var(--light-green);
        }

        .deleteBtn {
            border: none;
            box-shadow: none;
            background: none;
        }

        .modal-header {
            background: var(--green);
        }

        .btn-primary {
            background: var(--light-green);
            color: black;
            border-color: var(--light-green);
            font-weight: bold;
        }

        .btn-primary:hover,
        .btn-primary:focus,
        .btn-primary:active {
            background-color: var(--green) !important;
            border-color: var(--green) !important;
        }

        .card-body .btn-primary {
            font-weight: normal !important;
        }

        #removePathModal .close,
        #learningPathModal .close {
            box-shadow: none;
            background: #6c757d;
            border-radius: 7px 7px 0px 0px;
        }

        .close:hover {
            background: var(--light-green);
        }

        #addButton {
            font-size: large;
            font-weight: 500;
        }



        #learningPathModal input {
            background: none;
        }
    </style>
</head>

<body>

    <div class="row header-line text-center">
        <h2>Your Learning Path</h2>
    </div>

    <div class="text-center mt-5">
        <button type="button" id="addButton" data-bs-toggle="modal" data-bs-target="#moduleModal"><svg
                xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-plus"
                viewBox="0 0 16 16">
                <path
                    d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4" />
            </svg> Add Materials into
            Path</button>
    </div>


    <section style="background-color: var(--grey);">
        <div class="container py-5">
            <div class="main-timeline">
                <?php if (!empty($learningPaths)): ?>
                    <?php
                    $counter = 0;
                    foreach ($learningPaths as $path):
                        $position = ($counter % 2 == 0) ? 'left' : 'right';
                        ?>
                        <div class="timeline <?= $position ?>">
                            <div class="card">
                                <div class="card-body p-4">
                                    <div class="row">
                                        <div class="col-9" onclick="redirectToMaterial('<?php echo $path['material_id']; ?>')"
                                            style="cursor: pointer;">
                                            <h3><?php echo $path['material_title']; ?></h3>
                                        </div>
                                        <div class="col text-end">
                                            <button type="button" style="color:red;" class="deleteBtn"
                                                onclick="event.stopPropagation(); showRemoveModal('<?php echo $path['material_id']; ?>', '<?php echo $path['material_title']; ?>')">
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
                                                    <path
                                                        d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    <div onclick="redirectToMaterial('<?php echo $path['material_id']; ?>')"
                                        style="cursor: pointer;">
                                        <p class="mb-0"><?php echo $path['material_subject']; ?></p>
                                        <p class="mb-0"><i><?php echo $path['instructor_name']; ?></i></p>
                                        <p class="mb-4">
                                            <strong>
                                                <?php if ($path['days_left'] > 0): ?>
                                                    <?php echo $path['days_left'] . " days left"; ?>
                                                <?php else: ?>
                                                    <span style="color: red;">Overdue</span>
                                                <?php endif; ?>
                                            </strong>
                                        </p>
                                    </div>

                                    <p class="mb-0">
                                        <select name="sequence_<?php echo $path['material_id']; ?>"
                                            id="sequence_<?php echo $path['material_id']; ?>" onclick="event.stopPropagation()">
                                            <?php for ($i = 1; $i <= $maxSequence; $i++): ?>
                                                <option value="<?php echo $i; ?>" <?php echo ($i == $path['sequence']) ? 'selected' : ''; ?>>
                                                    <?php echo $i; ?>
                                                </option>
                                            <?php endfor; ?>
                                        </select>
                                        <button type="button" class="ms-2"
                                            onclick="event.stopPropagation(); updateSequence('<?php echo $path['material_id']; ?>', '<?php echo $path['sequence']; ?>')">
                                            Confirm Edit
                                        </button>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <?php
                        $counter++;
                    endforeach;
                    ?>
                <?php else: ?>
                    <p><?php echo $message ?></p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <div class="modal fade" id="moduleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">TABLE OF MODULES</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row flex-row mb-3">
                            <button id="toggleButton" class="btn btn-primary me-2 mb-3" onclick="toggleView()">Learning
                                Materials</button>
                            <input type="text" id="searchInput" class="form-control" placeholder="Search materials..."
                                oninput="searchResult()">
                        </div>
                        <div class="row" id="modalContent">
                            <!--  js on bookmark & all materials -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- add modal-->
    <div class="modal fade" id="learningPathModal" tabindex="-1" aria-labelledby="learningPathModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered justify-content-center" role="document">
            <form id="addLearningPathForm" method="POST" onsubmit="sendForm(event, 'add')">
                <div class="modal-content">
                    <button type="button" class="close btn" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="font-weight: bold; font-size: larger;">&times;</span>
                    </button>

                    <div class="modal-body d-flex flex-column align-items-center">
                        <input type="hidden" id="addMaterialIdInput" name="material_id" value="">
                        <input type="hidden" id="addStudentIdInput" name="student_id" value="<?php echo $studentId; ?>">
                        <input type="hidden" id="addActionInput" name="action" value="add">
                        <input type="hidden" id="addMaterialTitleInput" name="material_title" value="">
                        <strong><label for="dueDate" style="font-size:larger;">Select a Due Date</label></strong>
                        <input type="date" id="dueDate" name="dueDate" min="" class="my-3" required>
                        <i>Adding <strong><span id="materialTitle"></span></strong> to your learning path.</i>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn" style="background: var(--green); color:white;">Update
                            Path</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- remove modal-->
    <div class="modal fade" id="removePathModal" tabindex="-1" aria-labelledby="removePathModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form id="removeLearningPathForm" method="POST" onsubmit="sendForm(event, 'remove')">
                <div class="modal-content">
                    <button type="button" class="close btn" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="font-weight: bold; font-size: larger;">&times;</span>
                    </button>
                    <div class="modal-body">
                        <input type="hidden" id="removeMaterialIdInput" name="material_id" value="">
                        <input type="hidden" id="removeStudentIdInput" name="student_id" value="<?php echo $studentId; ?>">
                        <input type="hidden" id="removeActionInput" name="action" value="remove">
                        <p>Are you sure you want to remove <strong><span id="removeMaterialTitle"></span></strong> from
                            your learning path?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger" id="removeButton">Remove</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

        <?php include("../../footer.php"); ?>


    <script>
        function updateSequence(materialId, currentSequence) {
            const selectMaterial = document.getElementById(`sequence_${materialId}`);
            const selectedSequence = selectMaterial.value;

            if (selectedSequence == currentSequence) {
                alert("You are selecting same sequence. Try with another sequence :)");
                return;
            }

            const xhttp = new XMLHttpRequest();
            xhttp.open("POST", "updateSequence.php", true);
            xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhttp.onreadystatechange = function () {
                if (xhttp.readyState === 4 && xhttp.status === 200) {
                    location.reload();
                }
            };
            xhttp.send(`material_id=${materialId}&new_sequence=${selectedSequence}`);
        }

        function showRemoveModal(materialId, materialTitle) {
            document.getElementById('removeMaterialIdInput').value = materialId;
            document.getElementById('removeMaterialTitle').textContent = materialTitle;

            const removeModal = new bootstrap.Modal(document.getElementById('removePathModal'));
            removeModal.show();
        }

        function addToPath(materialId, materialTitle) {
            document.getElementById('addMaterialIdInput').value = materialId;
            document.getElementById('addMaterialTitleInput').value = materialTitle;
            document.getElementById('materialTitle').textContent = materialTitle;

            const today = new Date().toISOString().split('T')[0];
            document.getElementById('dueDate').setAttribute('min', today);

            const addModal = new bootstrap.Modal(document.getElementById('learningPathModal'));
            addModal.show();
        }

        let isBookmarkView = false;

        document.getElementById('moduleModal').addEventListener('shown.bs.modal', function () {
            fetchModalContent('fetchMaterials');
        });

        function toggleView() {
            const toggleButton = document.getElementById('toggleButton');
            const modalContent = document.getElementById('modalContent');

            isBookmarkView = !isBookmarkView;

            toggleButton.textContent = isBookmarkView ? "Your Bookmark" : "All Learning Materials";

            const action = isBookmarkView ? "fetchBookmarks" : "fetchMaterials";
            fetchModalContent(action);
        }

        function fetchModalContent(action) {
            const modalContent = document.getElementById('modalContent');

            const xhttp = new XMLHttpRequest();
            xhttp.open("POST", "retrievePathModal.php", true);
            xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhttp.onreadystatechange = function () {
                if (xhttp.readyState === 4 && xhttp.status === 200) {
                    modalContent.innerHTML = xhttp.responseText;
                }
            };
            xhttp.send(`action=${action}`);
        }

        function sendForm(event, formType) {
            event.preventDefault();

            let materialId, studentId, action, dueDate, materialTitle;

            if (formType === 'add') {
                materialId = document.getElementById('addMaterialIdInput').value;
                studentId = document.getElementById('addStudentIdInput').value;
                action = document.getElementById('addActionInput').value;
                materialTitle = document.getElementById('addMaterialTitleInput').value;
                dueDate = document.getElementById('dueDate').value;

                if (!dueDate) {
                    alert("Please select a due date.");
                    return;
                }
            } else if (formType === 'remove') {
                materialId = document.getElementById('removeMaterialIdInput').value;
                studentId = document.getElementById('removeStudentIdInput').value;
                action = document.getElementById('removeActionInput').value;
            }

            let requestData = `material_id=${materialId}&student_id=${studentId}&action=${action}`;
            if (formType === 'add') {
                requestData += `&material_title=${materialTitle}&dueDate=${dueDate}`;
            }

            const xhttp = new XMLHttpRequest();
            xhttp.open("POST", "addLearningPath.php", true);
            xhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhttp.onreadystatechange = function () {
                if (xhttp.readyState === 4 && xhttp.status === 200) {
                    location.reload();
                }
            };
            xhttp.send(requestData);
        }

        function searchResult() {
            const searchInput = document.getElementById('searchInput').value.toLowerCase().trim();
            const modalContent = document.getElementById('modalContent');

            if (searchInput === '') {
                const action = isBookmarkView ? "fetchBookmarks" : "fetchMaterials";
                fetchModalContent(action);
                return;
            }

            const materials = Array.from(document.querySelectorAll('#modalContent .card'));

            const matchingMaterials = materials.filter(material => {
                const title = material.querySelector('.card-title').textContent.toLowerCase();
                const subject = material.querySelector('.card-text').textContent.toLowerCase();

                return title.includes(searchInput) || subject.includes(searchInput);
            });

            modalContent.innerHTML = '';

            if (matchingMaterials.length === 0) {
                modalContent.innerHTML = '<p>No matching materials found.</p>';
                return;
            }

            let row;
            matchingMaterials.forEach((material, index) => {
                if (index % 2 === 0) {
                    row = document.createElement('div');
                    row.className = 'row mb-3';
                    modalContent.appendChild(row);
                }

                const col = document.createElement('div');
                col.className = 'col-md-6';
                col.appendChild(material);
                row.appendChild(col);
            });
        }

        function redirectToMaterial(materialId) {
            window.location.href = `/capstone/STUDENT ( LING )/stuLearningMaterial.php?material_id=${materialId}`;
        }

    </script>
</body>

</html>