<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require 'phpmailer/src/Exception.php';
    require 'phpmailer/src/PHPMailer.php';
    require 'phpmailer/src/SMTP.php';

    include "connect.php";


    if(isset($_POST)){
        $data = file_get_contents("php://input");

        $approval = json_decode($data, true);

        $instructorID = $approval['insID'];
        $status = $approval['status'];

        $sql = "UPDATE instructors SET approval_status = '$status' WHERE instructor_id = '$instructorID';";
        $result = mysqli_query($conn, $sql);

        $obtainInfo = "SELECT * FROM instructors WHERE instructor_id = '$instructorID';";
        $tempinfo = mysqli_query($conn, $obtainInfo);
        $rawData = mysqli_fetch_array($tempinfo);
        $instructorDetails = [
                "InstructorName" => $rawData['instructor_name'],
                "InstructorEmail" => $rawData['instructor_email']
                ];

        if ($status === '1') {
            $emailBody = "Dear ".$instructorDetails["InstructorName"].",<br><br>
            Your request for signing up as an instructor for Assestify has been approved. 
            Please log in using the credentials you provided.<br><br>
            Regards,<br>
            Assestify Team";

            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'assestifyofficial@gmail.com'; //  email
            $mail->Password = 'crrcfhifiqqplmpt'; //  app password
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;

            $mail->setFrom('assestifyofficial@gmail.com', 'Assestify');
            $mail->addAddress($instructorDetails["InstructorEmail"]);

            $mail->isHTML(true);
            $mail->Subject = "ASSESTIFY SIGN UP REQUEST APPROVED"; // subject
            $mail->Body = $emailBody; // body

            $mail->send();
        }
    }
