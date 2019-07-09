<?php
// example of how to use basic selector to retrieve HTML contents

function tirarAcentos($string){
    return preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/"),explode(" ","a A e E i I o O u U n N"),$string);
}
$meses = array(
    "JAN" => "01",
    "FEV" => "02",
    "MAR" => "03",
    "ABR" => "04",
    "MAI" => "05",
    "JUN" => "06",
    "JUL" => "07",
    "AGO" => "08",
    "SET" => "09",
    "OUT" => "10",
    "NOV" => "11",
    "DEZ" => "12",

);
include('simple_html_dom.php');

// get DOM from URL or file
$html = file_get_html('https://www.guiasaoroque.com.br/eventos/index.asp');
 //SALVAR NO BANCO DE DADOS
 $servername = "localhost";
 $username = "vinic952";
 $password = "Champions5@";
 $dbname = "vinic952_sao_roque";

 // Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$stmt = $conn->prepare("TRUNCATE eventos");
$stmt->execute();

//  // prepare and bind
$stmt = $conn->prepare("INSERT INTO eventos (name,photo,local,horario,dia,mesStr,mes,data_evento) VALUES (?,?,?,?,?,?,?,?)");
$stmt->bind_param("ssssssss", $titulo,$img,$local,$horario,$dia,$mesStr,$mes,$data_evento);

foreach($html->find('div.box-evento') as $e){
    $titulo = utf8_encode($e->find('p.titulo-eventos a')[0]->innertext);
    $img = $e->find('img')[0]->src;
    
    // donwload img
    mkdir("img",0777);
    $path = 'img/flower.jpg';
    file_put_contents($path, file_get_contents($img));
    


    $local = utf8_encode(str_replace("<b>Local:</b>","",$e->find('p.local-horario-eventos a')[0]->innertext));
    $horario = utf8_encode(str_replace("<b>Horario:</b>","",$e->find('p.local-horario-eventos a')[1]->innertext));
    $dia = $e->find('div.data-box-evento p.dia-data')[0]->innertext;
    $mesStr = $e->find('div.data-box-evento p.mes-data')[0]->innertext;
    $mes = $meses[$mesStr];
    $data_evento = "2019-".$mes."-".$dia;
    $conn->set_charset("utf8");
    $stmt->execute();
}
$stmt->close();
$conn->close();
die("fim");
?>