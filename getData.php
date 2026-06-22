<?php

function stuData($netID, $pwd){
    
    // Example POST request
    $student_data = array(
        'netId' => $netID,
        'password' => $pwd,
        'studentNumber' => ''
    );

    $ch = curl_init();
   
    $delimiterPos = strpos($netID, '-');
    if ($delimiterPos === false) {
        $delimiterPos = strpos($netID, '_');
    }
    
    // If either '-' or '_' is found, extract the two characters after it
    if ($delimiterPos !== false) {
        $netIDSubstring = substr($netID, $delimiterPos + 1, 2);
    } else {
        // If no delimiter is found, set the default substring
        $netIDSubstring = '';
    }

    // Convert the substring to lowercase for case-insensitive comparison
    $netIDSubstring = strtolower($netIDSubstring);
    
    // Check the extracted substring (case-insensitive)
    curl_setopt($ch, CURLOPT_URL, 'http://dt.medicine.kln.ac.lk/exp_ser/hostel.php');

    //Need to Comment out  


    // if ($netIDSubstring === "me" || $netIDSubstring === "fm") {
    // curl_setopt($ch, CURLOPT_URL, 'http://172.18.2.224/exp_ser/hostel.php');
    // } elseif ($netIDSubstring === "sh" || $netIDSubstring === "ot") {
    //     curl_setopt($ch, CURLOPT_URL, 'http://172.18.2.37/exp_ser/hostel.php');
    // } 
    
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($student_data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);

    if ($response === false) {
        $data = [
            'state' => false,
            'error' => 'cURL Error: ' . curl_error($ch)
        ];
    } else {
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($http_code >= 400) {
            $data = [
                'state' => false,
                'error' => 'HTTP Error: ' . $http_code
            ];
        } else {
            // Process response
            $data = json_decode($response, true);
            if (!is_array($data)) {
                $data = [
                    'state' => false,
                    'error' => 'Invalid JSON response'
                ];
            }
        
        }
    }

    // store into session if session is active
    if (session_status() === PHP_SESSION_ACTIVE) {
        $_SESSION['student_data'] = $data;
    }
    
    curl_close($ch);
    
    
    
    
    return $data;

}


