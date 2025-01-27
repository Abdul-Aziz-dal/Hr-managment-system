<?php 

class GoogleDriveUploadAPI{
    function __construct(){
     
    }

    // public function GetAccessToken() {
    //     try {
    //         $curlPost = 'client_id=' . GCLIENT_ID . 
    //                     '&redirect_uri=' . GCLIENT_REDIRECT . 
    //                     '&client_secret=' . GCLIENT_SECRET . 
    //                     '&code=' . $_SESSION['code'] . 
    //                     '&grant_type=authorization_code';
            
    //         $ch = curl_init();         
    //         curl_setopt($ch, CURLOPT_URL, OAUTH2_TOKEN_URI);         
    //         curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);         
    //         curl_setopt($ch, CURLOPT_POST, 1);         
    //         curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
    //         curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
    //         $response = curl_exec($ch);
            
    //         if (curl_errno($ch)) {
    //             $error_msg = 'cURL error: ' . curl_error($ch);
    //             curl_close($ch);
    //             throw new Exception($error_msg);
    //         }
            
    //         $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    //         curl_close($ch);
            
    //         if ($http_code != 200) {
    //             $error_msg = 'HTTP Code: ' . $http_code;
    //             $response_data = json_decode($response, true);
    //             if (isset($response_data['error'])) {
    //                 $error_msg .= ", Error: " . $response_data['error'] . ", Description: " . $response_data['error_description'];
    //             }
    //             throw new Exception($error_msg);
    //         }
            
    //         $data = json_decode($response, true);
    //         //&& isset($data['refresh_token'])
    //         if (isset($data['access_token'])) {
    //             $_SESSION['access_token'] = $data['access_token'];
    //             $_SESSION['refresh_token'] = $data['refresh_token'];
    //             return $data;
    //         } else {
    //             throw new Exception("Missing access_token or refresh_token in response.");
    //         }
    
    //     } catch (Exception $e) {
    //         error_log("Error in GetAccessToken: " . $e->getMessage());
    //         // echo json_encode(['success' => false, 'message' => $e->getMessage()]); 
    //         // exit; // Stop further execution
    //     }
    // }

    public function GetAccessToken() {
        try {
            $curlPost = 'client_id=' . GCLIENT_ID . 
                        '&redirect_uri=' . GCLIENT_REDIRECT . 
                        '&client_secret=' . GCLIENT_SECRET . 
                        '&code=' . $_SESSION['code'] . 
                        '&grant_type=authorization_code';
            
            $ch = curl_init();         
            curl_setopt($ch, CURLOPT_URL, OAUTH2_TOKEN_URI);         
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);         
            curl_setopt($ch, CURLOPT_POST, 1);         
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
            curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
            $response = curl_exec($ch);
            
            if (curl_errno($ch)) {
                throw new Exception('cURL error: ' . curl_error($ch));
            }
            
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($http_code != 200) {
                $response_data = json_decode($response, true);
                $error_msg = 'HTTP Code: ' . $http_code;
                if (isset($response_data['error'])) {
                    $error_msg .= ", Error: " . $response_data['error'] . ", Description: " . $response_data['error_description'];
                }
                throw new Exception($error_msg);
            }
            
            $data = json_decode($response, true);
            if (isset($data['access_token']) && isset($data['refresh_token'])) {
                $_SESSION['access_token'] = $data['access_token'];
                $_SESSION['refresh_token'] = $data['refresh_token'];
                $_SESSION['expires_at'] = time() + $data['expires_in']; // Save expiry time
                return $data;
            } else {
                throw new Exception("Missing access_token or refresh_token in response.");
            }
    
        } catch (Exception $e) {
            error_log("Error in GetAccessToken: " . $e->getMessage());
            return false;
        }
    }

    public function RefreshAccessToken() {
        try {
            $refreshToken ='1//03JiTpsTuuqxVCgYIARAAGAMSNwF-L9Ir70Im7zKug3T_DqFicpCP35Z69H18jPxectPlsd4rzvhOlvbzPQwoxeEuooEK4kn3WG0'; //$_SESSION['refresh_token'];
            $url = 'https://oauth2.googleapis.com/token';
            $postData = [
                'client_id' => GCLIENT_ID,
                'client_secret' => GCLIENT_SECRET,
                'refresh_token' => $refreshToken,
                'grant_type' => 'refresh_token'
            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/x-www-form-urlencoded',
            ]);

            $response = curl_exec($ch);
            if (curl_errno($ch)) {
                throw new Exception('Curl error: ' . curl_error($ch));
            }

            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($http_code != 200) {
                throw new Exception("Failed to refresh access token. HTTP Code: $http_code, Response: $response");
            }

            $data = json_decode($response, true);

            if (isset($data['access_token'])) {
                $_SESSION['access_token'] = $data['access_token'];
                $_SESSION['expires_at'] = time() + $data['expires_in']; // Update expiry time
                return $data['access_token'];
            } else {
                throw new Exception("Missing access_token in response.");
            }
        } catch (Exception $e) {
            error_log("Error in RefreshAccessToken: " . $e->getMessage());
            return false;
        }
    }

    public function GetValidAccessToken() {
        // Check if the current access token is valid
        if (isset($_SESSION['access_token']) && isset($_SESSION['expires_at']) && time() < $_SESSION['expires_at']) {
            return $_SESSION['access_token'];
        }

        // If expired, try refreshing the token
        //isset($_SESSION['refresh_token'])
        if (time() > $_SESSION['expires_at']) {
            return $this->RefreshAccessToken();
        }
      
        // If refresh fails, reauthenticate the user
        return $this->GetAccessToken();
    }



    public function toDrive($FileContents, $MimeType) { 
        $API_URL = DRIVE_FILE_UPLOAD_URI . '?uploadType=media'; 
         
        $ch = curl_init();         
        curl_setopt($ch, CURLOPT_URL, $API_URL);         
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);         
        curl_setopt($ch, CURLOPT_POST, 1);         
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: '.$MimeType, 'Authorization: Bearer '. $_SESSION['access_token'])); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, $FileContents); 
        $data = json_decode(curl_exec($ch), true); 
        $http_code = curl_getinfo($ch,CURLINFO_HTTP_CODE);         
         
        if ($http_code != 200) { 
            $error_msg = 'Failed to upload file to Google Drive'; 
            if (curl_errno($ch)) { 
                $error_msg = curl_error($ch); 
            } 
            throw new Exception('Error '.$http_code.': '.$error_msg); 
        } 
 
        return $data['id']; 
    } 

    public function FileMeta($FileID, $FileMetaData) { 
        $API_URL = DRIVE_FILE_META_URI . $FileID; 
         
        $ch = curl_init();         
        curl_setopt($ch, CURLOPT_URL, $API_URL);         
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);         
        curl_setopt($ch, CURLOPT_POST, 1);         
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer '. $_SESSION['access_token'])); 
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH'); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($FileMetaData)); 
        $data = json_decode(curl_exec($ch), true); 
        $http_code = curl_getinfo($ch,CURLINFO_HTTP_CODE);         
         
        if ($http_code != 200) { 
            $error_msg = 'Failed to update file metadata'; 
            if (curl_errno($ch)) { 
                $error_msg = curl_error($ch); 
            } 
            print_r($data);
            throw new Exception('Error '.$http_code.': '.$error_msg); 
        } 
 
        return $data; 
    } 

    public function getResources($query = '', $pageToken = '') {
    
        $API_URL = 'https://www.googleapis.com/drive/v3/files';
        $API_URL .= '?q=' . urlencode($query);
        if ($pageToken) {
            $API_URL .= '&pageToken=' . $pageToken;
        }
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $API_URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $_SESSION['access_token']));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    
        $data = json_decode(curl_exec($ch), true);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
        if ($http_code != 200) {
            $error_msg = 'Failed to fetch resources from Google Drive';
            if (curl_errno($ch)) {
                $error_msg = curl_error($ch);
            }
            throw new Exception('Error ' . $http_code . ': ' . $error_msg);
        }
        
        return $data;
    }
    
}