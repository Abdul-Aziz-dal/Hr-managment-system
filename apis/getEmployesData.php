<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once("../googleApi.config/config.php");
require_once("../googleApi.config/GoogleDriveUploadAPI.php");
require_once("../database/databaseClass.php");
require_once("../Redis/RedisServer.php");

$response = [
    'status' => 'error',
    'message' => 'An unknown error occurred',
    'data' => null
];

try {
    $database = new DatabaseClass();
    $storage = new RedisStorage(); //Redis connectivity
    $employeesData = [];

    if ($storage->isConnected()) {
        $redis = $storage->getRedisInstance();
        if ($redis->exists('employees')) {
            // Fetch data from Redis
            $employeesData = json_decode($redis->get('employees'), true);
            $response['status'] = 'success';
            $response['message'] = 'Data retrieved from Redis';
            $response['data'] = $employeesData;
        } else {
            // Redis key does not exist, fetch data from the database
            $condition = [];
            $result = $database->viewRecords('employees', '*', $condition);

            if (!empty($result)) {
                // Save database data to Redis
                $redis->set('employees', json_encode($result));
                $redis->expire('employees', 3600); // 1 hour expiry time

                $response['status'] = 'success';
                $response['message'] = 'Records retrieved from database and cached in Redis';
                $response['data'] = $result;
            } else {
                $response['status'] = 'success';
                $response['message'] = 'No records found in the database';
                $response['data'] = [];
            }
        }
    } else {
        $condition = [];
        $result = $database->viewRecords('employees', '*', $condition);
        $response['status'] = 'success';
        $response['data'] = $result;
        $response['message'] = 'Failed to connect to Redis , fetched data from database';
    }
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
} finally {
    echo json_encode($response);
}

?>
