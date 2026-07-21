<?php

require 'vendor/autoload.php'; // Load the Composer libraries
$client = new Google_Client();
$client->setAccessType('offline');
$client->setApplicationName('Hostel Management System -FoM');
$client->setScopes([
	Google_Service_Drive::DRIVE_FILE,
    Google_Service_Gmail::GMAIL_SEND
]);
//commented because error in my local hostel project related to this path
// $client->setAuthConfig('/var/www/client_secret.json');
$client->setAuthConfig(__DIR__ . '/client_secret.json');

// mail\token.json.backup

// $tokenFile = '/var/www/html/hostel/admin/mail/token.json';
$tokenFile = __DIR__ . '/token.json';




if(isset($_GET['code'])){
$authCode = $_GET['code'] ?? null;

    if ($authCode) {
        // $client->authenticate($_GET['code'], ['access_type' => 'offline']);
// //         // Exchange the authorization code for a token
        $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
// //         // Save the token to a file
        $tokenPath = 'token.json';
        file_put_contents($tokenPath, json_encode($accessToken));
        echo "\n\n";
        echo "Token saved to: " . $tokenPath;

        echo $client->getRefreshToken();
    }
}elseif(file_exists($tokenFile)==FALSE){
	$authUrl = $client->createAuthUrl();
	header("Location:".$authUrl);
}

if (file_exists($tokenFile)) {
    $accessToken = json_decode(file_get_contents($tokenFile), true);
    // print_r($accessToken);
    $client->setAccessToken($accessToken);
}
// if ($client->isAccessTokenExpired()) {
//     $client->fetchAccessTokenWithRefreshToken($client->getAccessToken()); 
//     file_put_contents($tokenFile, json_encode($client->getAccessToken()));
// }

if ($client->isAccessTokenExpired()) {
    $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
    file_put_contents($tokenFile, json_encode($client->getAccessToken()));
}

   function api_sendMail($to,$cc,$sub,$body){  
            //Gmail
            global $client;
            $email = 'hosmed@kln.ac.lk';
            $srvGmail = new Google_Service_Gmail($client);
            
            $msgBody  = "From: Hostel Management System <{$email}>\r\n";
            $msgBody .= "To: {$to}\r\n";
            $msgBody .= "CC: {$cc}\r\n";
            $msgBody .= "Subject: {$sub}\r\n";
            $msgBody .= "MIME-Version: 1.0\r\n";
            $msgBody .= "Content-Type: text/html; charset=utf-8\r\n\r\n";
            $msgBody .= $body;
            
            $message = new Google_Service_Gmail_Message();
            $message->setRaw(base64_encode($msgBody));
            $message = $srvGmail->users_messages->send("me", $message);
            if($message) return true;
    }

// api_sendMail("chamika@kln.ac.lk","piumem@kln.ac.lk","Test Mail","content");

function uploadFile($fileName,$folderId){
    global $client; 
    $driveService = new Google_Service_Drive($client); 
    $fileMetadata = new Google_Service_Drive_DriveFile([
    'name' => $fileName,
    'parents' => [$folderId],
]);
   $content = file_get_contents('tmp_files/' .$fileName);

   $file = $driveService->files->create($fileMetadata, [
    'data' => $content,
    'mimeType' => 'application/pdf',
    'uploadType' => 'multipart',
    'fields' => 'id,webViewLink,webContentLink',
]);
    $webViewLink = $file->getWebViewLink();
    
    //if (strpos($webViewLink, 'usp=drivesdk') !== false) {
   //     $webViewLink = str_replace('usp=drivesdk', 'usp=drive_link', $webViewLink);
    //}

    //foreach ($emailList as $val) {
    //    setPermission($file->getId(), $val, $driveService);
    //}

    return $webViewLink; 
}

//uploadFile("file_6746c3330f73f6.93609627.pdf","13Laqaz1JOoMbP2877N9F_qpiNpfgMsCo");







?>
