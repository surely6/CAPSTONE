<?php
    include "connect.php";

    $StudentInfo = [];
    $InstructorInfo = [];
    $PendInstructorInfo = [];

    function obtainData($tableDestination){
        global $conn, $StudentInfo, $InstructorInfo, $PendInstructorInfo;

        $StudentInfo = [];
        switch ($tableDestination) {
            case 'students':
                $sql = "SELECT * FROM students";
                $result = mysqli_query($conn, $sql);
                $StudentNumber = 0;
                if(mysqli_num_rows($result) > 0){
                    while($StudentRow = mysqli_fetch_array($result)){
                        $StudentNumber+=1;
                        $data = ["StudentID" => $StudentRow['student_id'],
                                "StudentNum" => $StudentNumber,
                                "StudentName" => $StudentRow['student_name'],
                                "StudentEmail" => $StudentRow['student_email'],
                                "StudentLevel" => $StudentRow['student_level']
                                ];

                        array_push($StudentInfo, $data);
                    }; 
                };
                break;
            
            case 'instructors':
                $sql = "SELECT * FROM instructors WHERE approval_status = '1'";
                $result = mysqli_query($conn, $sql);
                $InstructorNumber = 0;
                if(mysqli_num_rows($result) > 0){
                    while($InstructorRow = mysqli_fetch_array($result)){
                        $InstructorNumber+=1;
                        $data = ["InstructorID" => $InstructorRow['instructor_id'],
                                "InstructorNum" => $InstructorNumber,
                                "InstructorName" => $InstructorRow['instructor_name'],
                                "InstructorEmail" => $InstructorRow['instructor_email']
                                ];

                        array_push($InstructorInfo, $data);
                    }

                }
                break;
            
            case 'pend_instructors':
                $sql = "SELECT * FROM instructors WHERE approval_status = '0'";
                $result = mysqli_query($conn, $sql);
                $InstructorNumber = 0;
                if(mysqli_num_rows($result) > 0){
                    while($InstructorRow = mysqli_fetch_array($result)){
                        $InstructorNumber+=1;
                        $data = ["InstructorID" => $InstructorRow['instructor_id'],
                                "InstructorNum" => $InstructorNumber,
                                "InstructorName" => $InstructorRow['instructor_name'],
                                "InstructorEmail" => $InstructorRow['instructor_email']
                                ];

                        array_push($PendInstructorInfo, $data);
                    }

                }
                break;
                
            case 'default':
                echo "<script> console.log('db error'); </script>";
                break;
        }
    }
?>

