<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Google\Client;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GlobalFunction extends Model
{
    use HasFactory;

    public static function sendPushNotificationToAllUsers($title, $description)
    {
        $client = new Client();
        $client->setAuthConfig('googleCredentials.json');
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->fetchAccessTokenWithAssertion();
        $accessToken = $client->getAccessToken();
        $accessToken = $accessToken['access_token'];

        $contents = File::get(base_path('googleCredentials.json'));
        $json = json_decode($contents, true);

        $url = 'https://fcm.googleapis.com/v1/projects/' . $json['project_id'] . '/messages:send';
        $notificationArray = array('title' => $title, 'body' => $description);

        // Construct message for iOS
        $fields_ios = array(
            'message'=> [
                'topic'=> env('NOTIFICATION_TOPIC') .'_ios',
                'data' => $notificationArray,
                'notification' => $notificationArray,
                'apns' => [
                    'payload' => [
                        'aps' => ['sound' => 'default']
                    ]
                ]
            ],   
        );

        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $accessToken
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields_ios));

        $result = curl_exec($ch);


        $fields_android = array(
            'message' => [
                'topic' => env('NOTIFICATION_TOPIC') .'_android',
                'data' => $notificationArray,
                'apns' => [
                    'payload' => [
                        'aps' => ['sound' => 'default']
                    ]
                ]
            ],
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields_android));

        $result = curl_exec($ch);

        if ($result === false) {
            die('FCM Send Error: ' . curl_error($ch));
        }

        curl_close($ch);

        if ($result) {
            return json_encode(['status' => true, 'message' => 'Notification sent successfully']);
        } else {
            return json_encode(['status' => false, 'message' => 'Not sent!']);
        }
    }

    public static function sendPushNotificationToUser($notificationDesc, $deviceToken, $device_type)
    {
        $client = new Client();
        $client->setAuthConfig('googleCredentials.json');
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->fetchAccessTokenWithAssertion();
        $accessToken = $client->getAccessToken();
        $accessToken = $accessToken['access_token'];

        // Log::info($accessToken);
        $contents = File::get(base_path('googleCredentials.json'));
        $json = json_decode(json: $contents, associative: true);

        $url = 'https://fcm.googleapis.com/v1/projects/'.$json['project_id'].'/messages:send';
       
        $notificationArray = array('title' => env('APP_NAME'), 'body' => $notificationDesc);

        $fields = array(
            'message' => [
                'token' => $deviceToken,
                'data' => $notificationArray,
                'apns' => [
                    'payload' => [
                        'aps' => ['sound' => 'default']
                    ]
                ]
            ],
        );

        if ($device_type == Constants::iOS) {

            $fields = array(
                'message' => [
                    'token' => $deviceToken,
                    'data' => $notificationArray,
                    'notification' => $notificationArray,
                    'apns' => [
                        'payload' => [
                            'aps' => ['sound' => 'default']
                        ]
                    ]
                ],
            );

        } 

        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $accessToken
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        $result = curl_exec($ch);

        Log::debug($result);

        if ($result === FALSE) {
            die('FCM Send Error: ' . curl_error($ch));
        }
        curl_close($ch);

        // return $response;
        return response()->json([
            'result'=> $result
        ]);
    }

    public static function deleteFile($url)
    {
        if ($url == null) {
            return;
        }
        
        // Define the base URLs for AWS, DigitalOcean, and Local storage
        $baseURLAWS = rtrim(env('ITEM_BASE_URL'), '/') . '/';
        $baseURLDO = rtrim(env('DO_SPACE_URL'), '/') . '/';
        $baseURLLocal = rtrim(env('APP_URL'), '/') . '/storage/';

        // Remove the base URLs from the given URL to get the file paths
        $fileNameAWS = str_replace($baseURLAWS, '', $url);
        $fileNameDO = str_replace($baseURLDO, '', $fileNameAWS);
        $fileNameLocal = str_replace($baseURLLocal, '', $url);

        // Check and delete the file from local storage
        if (Storage::disk('local')->exists('public/' . $fileNameLocal)) {
            Storage::disk('local')->delete('public/' . $fileNameLocal);
            return;
        }

        try {
            if (Storage::disk('digitalocean')->exists($fileNameDO)) {
                Storage::disk('digitalocean')->delete($fileNameDO);
                return;
            }
        } catch (\Exception $e) {

        }

        try {
            if (Storage::disk('s3')->exists($fileNameAWS)) {
                Storage::disk('s3')->delete($fileNameAWS);
            }
        } catch (\Exception $e) {
            
        }
    }

    public static function saveFileAndGivePath($file)
    {
        $storageType = Setting::first()->storage_type;

        $storageConfig = [
            1 => ['disk' => 's3', 'base_url' => env('ITEM_BASE_URL')],
            2 => ['disk' => 'digitalocean', 'base_url' => env('DO_SPACE_URL')],
        ];

        $storageDisk = $storageConfig[$storageType]['disk'] ?? 'public';
        $baseUrl = $storageConfig[$storageType]['base_url'] ?? env('APP_URL') . 'storage/';

        $fileName = time() . '_' . env('APP_NAME') . '_' . str_replace(" ", "_", $file->getClientOriginalName());
        $appName = env('APP_NAME') ? env('APP_NAME') . '/' : '';
        $filePath = ($storageDisk === 'public') ? 'uploads/' . $fileName : $appName . 'uploads/' . $fileName;

        Storage::disk($storageDisk)->put($filePath, file_get_contents($file), 'public');

        return $baseUrl . $filePath;
    }

    public static function saveFileInLocal($file, $filename)
    {
        if ($file != null) {
            // Define the full path to the public/assets/img directory
            $filePath = public_path('asset/img/' . $filename);

            // Check if the file already exists and delete it if necessary
            if (file_exists($filePath)) {
                unlink($filePath);  // Remove the existing file
            }

            // Move the uploaded file to the public/assets/img directory with the specified name
            $file->move(public_path('asset/img'), $filename);

            return $filePath; // Return the full path to the file
        } else {
            return null;
        }
    }
}