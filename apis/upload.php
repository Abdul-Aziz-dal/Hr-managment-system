<?php
// Enable CORS if needed
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

require_once("../googleApi.config/config.php");
require_once("../googleApi.config/GoogleDriveUploadAPI.php");
require_once("../database/databaseClass.php");
include_once("../sendGrid/sendEmail.php");
require_once("../Redis/RedisServer.php");

// Initialize necessary classes
$gdriveAPI = new GoogleDriveUploadAPI();
$database = new DatabaseClass();
$mail= new SendGridSendEmail();
$storage = new RedisStorage(); //Redis connectivity


try {
   if(!session_id()) session_start();
    // Validate and retrieve form data
    extract($_POST);
    $name       = isset($employeName) ? trim($employeName) : '';
    $email      = isset($employeEmail) ? trim($employeEmail) : '';
    $department = isset($employeDepartment) ? trim($employeDepartment) : '';
    $manager    = isset($employeManager) ? trim($employeManager) : '';
    $status     = isset($employeStatus) ? trim($employeStatus) : '';
    $file       = isset($_FILES['employeFile']) ? $_FILES['employeFile'] : null;
    $fileUrl="";
    // Validate that the required fields are filled
    if (empty($name) || empty($email) || empty($department) || empty($manager) || empty($status)) {
        throw new Exception('All fields (name, email, department, manager, status) are required.');
    }

    $condition= ['employe_email' => $email];
    $result = $database->viewRecords('employees', '*', $condition);

    if (!empty($result)) {
        throw new Exception('Email already exist..!'); 
    }


    if (isset($file) && !empty($file['tmp_name'])) {
        $fname = $file['name'];

        $upload = move_uploaded_file($file['tmp_name'], '../assets/temp/' . $fname);
        if (!$upload) {
            throw new Exception('File upload failed during temporary storage.');
        }

        $access_token = $_SESSION['access_token'] ?? '';
        if (empty($access_token)) {
            throw new Exception('Invalid access token. File upload failed.');
        }

        $mimeType = mime_content_type("../assets/temp/" . $file['name']);
        $FileContents = file_get_contents("../assets/temp/" . $file['name']);

        // Upload File to Google Drive
        $gDriveFID = $gdriveAPI->toDrive($FileContents, $mimeType);
        if (!$gDriveFID) {
            throw new Exception('File upload failed in Google Drive.');
        }

        $meta = ["name" => $file['name']];
        $gDriveMeta = $gdriveAPI->FileMeta($gDriveFID, $meta);
        if (!$gDriveMeta) {
            throw new Exception('Failed to update the file meta in Google Drive.');
        }
      
        $fileUrl = "https://drive.google.com/file/d/{$gDriveFID}/view";


        unlink('../assets/temp/' . $fname);
    }

       $employeData = [
        'employe_name' => $name,
        'employe_email' => $email,
        'employe_department' => $department,
        'employe_manager' => $manager,
        'employe_status' => $status,
        'employe_file_path' => $fileUrl,
        'employe_added_by' => $_SESSION['user_id'],
        'employe_added_on' => date('Y-m-d H:i:s')
    ];
    

    $last_inser_id = $database->addRecord('employees', $employeData);

    if (!$last_inser_id) {
        throw new Exception('Data insertion failed');
    }
    
    if ($storage->isConnected()) {
    $employeData['employe_id'] = $last_inser_id;
    $redis = $storage->getRedisInstance();
    if ($redis->exists('employees')) {
        $existingEmployees = json_decode($redis->get('employees'), true);
        $existingEmployees[] = $employeData;
        $redis->set('employees', json_encode($existingEmployees));
    }
   }

   
    $response=$mail->sendEmailWithCurl($email, "Hr Management", "Congratulations your Account Created", "You are now a registered employee in the HR department."); 

    echo json_encode(['success' => true, 'message' => 'Data has been uploaded successfully']);
    exit;

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit;
}
?>