<?php
include 'processor.php'
//VERIFICACION//

//Token nuestro
$token = "ElDeLata";

//Reto de facebook
$challengeWord = $_GET['hub_challenge'];

//Token de verificacion de facebook
$verifyTokenFB = $_GET['hub_verify_token'];

//Verificacion de match
if($token === $verifyTokenFB){
    echo $challengeWord;
    exit;
}



//RECEPCION DE MENSAJES//

//Lectura de datos de whatsapp
$response = file_get_contents('php://input');
//Converimos el JSON en array
$response = json_decode($response, true);
//Extraemos el mensaje
$menssage = $response['entry'][0]['changes'][0]['value']['messages'][0]['text']['body'];
//Extraemos el id de whatsapp
$id = $response['entry'][0]['changes'][0]['value']['messages'][0]['id'];
//Extraemos el tiempo de whatsapp
$timestamp = $response['entry'][0]['cahnges'][0]['value']['messages'][0]['timestamp'];

//Si hay mensaje
if($menssage != null){
    file_put_contents('text.txt', $menssage);
}


// $ch = curl_init();
// curl_setopt($ch, CURLOPT_URL, 'https://graph.facebook.com/v20.0/125021817354805/messages');
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
// curl_setopt($ch, CURLOPT_HTTPHEADER, [
//     'Content-Type: application/json',
//     'Authorization: Bearer EAACZCiryrdiYBO9avU3YkBTrhOZBMkMSYzdi9y649vJOsypr8wBEmrYcW4a30jyFBZCZB8JMXfBGPQOudzOugPZAiCYNY3Hx3ZCuMSCIYkTzLXxqmQ6JEcIpH3MEw1OHVRKnitEnMMgnA9WXvCLFLozZBwuZB4LHzfv2YSw18Jh9xiiSUiKu7BWjZCukOuhMZAZBRF5J1N4w9wJUsUVa4xT2U8qacznq94ZD',
// ]);
// curl_setopt($ch, CURLOPT_POSTFIELDS, "\n{\n  \"messaging_product\": \"whatsapp\",\n  \"recipient_type\": \"individual\",\n  \"to\": \"542325423315\",\n  \"type\": \"text\",\n  \"text\": {\n    \"body\": \"Hola! este es un mensaje personalizado\"\n  }\n}");

// $response = curl_exec($ch);

// curl_close($ch);







// curl -i -X POST https://graph.facebook.com/v20.0/125021817354805/messages `
//   -H 'Authorization: Bearer EAACZCiryrdiYBOZBDsp7ZBXJouxqXmqgb7FwKMZCXFElMvWSVQBvwPWSsbkiv0TOeAkQTvV1yooBLuppHk7k0GQVmF1m5LPJyUhEp6EQW82oGTVjCUbZAsne60C4kyOg2WkZC0siqz6rTgtt84MphS158Bkxkhv2J93cTNGVqVbV9zWYWZAu7AlAPh4AdSld8j9mLny7HbMflgEBNHCvSfAjJvfLVkZD' `
//   -H 'Content-Type: application/json' `
//   -d '{ "messaging_product": "whatsapp", "to": "541170598173", "type": "template", "template": { "name": "hello_world", "language": { "code": "en_US" } } }'
