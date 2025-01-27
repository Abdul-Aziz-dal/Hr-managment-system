<?php
class SendGridSendEmail {
    function __construct(){
     
    }

    // function sendEmailWithCurl($toEmail, $toName, $subject, $content) {
        
    //     $apiKey = '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';
    //     $url = 'https://api.sendgrid.com/v3/mail/send';
    
    //     $emailData = [
    //         'personalizations' => [[
    //             'to' => [[
    //                 'email' => $toEmail,
    //                 'name' => $toName
    //             ]],
    //             'subject' => $subject
    //         ]],
    //         'from' => [
    //             'email' => 'report-services@retailistan.com', 
    //             'name' => 'Abdul Aziz' 
    //         ],
    //         'content' => [[
    //             'type' => 'text/plain',
    //             'value' => $content
    //         ]]
    //     ];
    
    //     $ch = curl_init();
    //     curl_setopt($ch, CURLOPT_URL, $url);
    //     curl_setopt($ch, CURLOPT_POST, true);
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, [
    //         'Authorization: Bearer ' . $apiKey,
    //         'Content-Type: application/json',
    //     ]);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($emailData));
    
    //     $response = curl_exec($ch);
         
        
    //     if (curl_errno($ch)) {
    //         echo 'Error: ' . curl_error($ch);
    //     } else {
    //         $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    //         echo "HTTP Status Code: $httpCode\n";
    //         echo "Response: $response\n";
    //     }
        
    //     curl_close($ch);
    //     return $httpCode;
    // }

    function sendEmailWithCurl($toEmail, $toName="", $subject="", $content="") {
        $apiKey = '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';
        $url = 'https://api.sendgrid.com/v3/mail/send';
    
        if (empty($toEmail) || empty($content)) {
            return $this->handleError("Invalid input data");
        }
    
        // Prepare email data
        $emailData = [
            'personalizations' => [[
                'to' => [[
                    'email' => $toEmail,
                    'name' => $toName
                ]],
                'subject' => $subject
            ]],
            'from' => [
                'email' => 'report-services@retailistan.com',
                'name' => 'Abdul Aziz'
            ],
            'content' => [[
                "type" => "text/html",
                'value' => $content
            ]]
        ];
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($emailData));
    
        $response = curl_exec($ch);        
        if (curl_errno($ch)) {
            return $this->handleError('cURL Error: ' . curl_error($ch));
        }
    
        // Get HTTP response status code
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
       if ($httpCode !== 202) {
            return $this->handleError("Failed to send email. HTTP Status Code: $httpCode. Response: $response");
        }

        curl_close($ch);
    
        return [
            'status' => 'success',
            'message' => 'Email sent successfully!',
            'httpCode' => $httpCode,
            'response' => $response
        ];
    }
        //helper function
    function handleError($errorMessage) {
        error_log($errorMessage);
            return [
            'status' => 'error',
            'message' => $errorMessage
        ];
    }
}
?>


