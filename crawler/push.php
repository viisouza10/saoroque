<?php

function sendGCM($message, $id) {
    $url = 'https://fcm.googleapis.com/fcm/send';

    $fields = array (
            'registration_ids' => array (
                    $id
            ),
            'data' => array (
                "message" => $message,
                "sound" =>  $message
            )
    );
    $fields = json_encode ( $fields );

    $headers = array (
            'Authorization: key=' . "AIzaSyDE_27Pisz8JP6R6wdEPDhnHyjyF8UmHZo",
            'Content-Type: application/json'
    );

    $ch = curl_init ();
    curl_setopt ( $ch, CURLOPT_URL, $url );
    curl_setopt ( $ch, CURLOPT_POST, true );
    curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
    curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt ( $ch, CURLOPT_POSTFIELDS, $fields );

    $result = curl_exec ( $ch );
    echo $result;
    curl_close ( $ch );
}
// somarme
// somdisparado
sendGCM("somdisparado","cu-KM7i9l-s:APA91bHDKK5p3OamzIQj9_btRenR-y-8kN0gMzlZgyf2DNLOXR-LFRjWnGd5yvGyoIBBv_auJAgSh7GnL3boRNcOG_rCpFcd0PkYqVx-eH-AZLFobKag8agULGcGxwJ_7ifFVzzXt_W8");

?>