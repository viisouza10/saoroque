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
// disparo_somarme
// EE8DBFF1-6AD5-4C64-915C-A28865B22D98

sendGCM("disparo_somarme","1e0d332dd27b83d6e908996ded1f4d7e180b880988ba0035f153a8e091a7d9dd");

?>