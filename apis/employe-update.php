<?php
// Enable CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

require_once("../googleApi.config/config.php");
require_once("../googleApi.config/GoogleDriveUploadAPI.php");
require_once("../database/databaseClass.php");
include_once("../sendGrid/sendEmail.php");
require_once("../Redis/RedisServer.php");

$gdriveAPI = new GoogleDriveUploadAPI();
$database = new DatabaseClass();
$storage = new RedisStorage(); //Redis connectivity


try {
   if(!session_id()) session_start();

   extract($_POST);
    $name       = isset($employeName) ? trim($employeName) : '';
    $email      = isset($employeEmail) ? trim($employeEmail) : '';
    $department = isset($employeDepartment) ? trim($employeDepartment) : '';
    $manager    = isset($employeManager) ? trim($employeManager) : '';
    $status     = isset($employeStatus) ? trim($employeStatus) : '';
    $id         = isset($employeId) ? trim($employeId) : '';
    $file       = isset($_FILES['employeFile']) ? $_FILES['employeFile'] : null;
    $fileUrl="";

    // Validate that the required fields are filled
    if (empty($id)||empty($name) || empty($email) || empty($department) || empty($manager)) {
        throw new Exception('All fields (name, email, department, manager) are required.');
    }

    $condition= ['employe_id' => $id];
    $result = $database->viewRecords('employees', '*', $condition);

    if (empty($result)) {
        throw new Exception('Record not found..!'); 
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
         $fileUrl=$gDriveFID;
        // $fileUrl = "https://drive.google.com/file/d/{$gDriveFID}/view";
   

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
    
   $result= $database->updateRecord("employees", $employeData, $condition);
    
    if (!$result) {
        throw new Exception('Data updation failed');
    }
    
    if ($storage->isConnected()) {
    $employeData['employe_id'] = $id;
    $redis = $storage->getRedisInstance();
    if ($redis->exists('employees')) {
        $existingEmployees = json_decode($redis->get('employees'), true);
        foreach ($existingEmployees as $index => $employee) {
            if ($employee['employe_id'] == $employeData['employe_id']) {
                $existingEmployees[$index] = $employeData;
                break;
            }
        }

        $redis->set('employees', json_encode($existingEmployees));
    }
   }

    echo json_encode(['success' => true, 'message' => 'Data has been updated successfully']);
    exit;

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit;
}
?>