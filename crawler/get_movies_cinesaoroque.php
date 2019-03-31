<?php
// example of how to use basic selector to retrieve HTML contents

function tirarAcentos($string){
    return preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/"),explode(" ","a A e E i I o O u U n N"),$string);
}

include('simple_html_dom.php');

// get DOM from URL or file
$html = file_get_html('http://www.cinesaoroque.com.br/index.php');

 //SALVAR NO BANCO DE DADOS
 $servername = "localhost";
 $username = "root";
 $password = "979899";
 $dbname = "saoroque_com_br";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$stmt = $conn->prepare("TRUNCATE cinema");
$stmt->execute();
 // prepare and bind
 $stmt = $conn->prepare("INSERT INTO cinema (name,photo,urlIngresso,diasSemana,descricao,audio,video,classificacao,linkTrailer) VALUES (?,?,?,?,?,?,?,?,?)");
 $stmt->bind_param("sssssssss", $titulo,$photo,$urlIngresso,$diasSemana,$descricao,$audio,$video,$classificacao,$linkTrailer);

// find all div tags with id=gbar
foreach($html->find('div.g-array-item-image a') as $e){
    $htmlDetail = file_get_html('http://www.cinesaoroque.com.br'.$e->href);
    
    
    $titulo = strip_tags(trim($htmlDetail->find('div.page-header h2')[0]->innertext));//titulo do filme filme
    $photo = "http://www.cinesaoroque.com.br".$e->find('img')[0]->src;
    $urlIngresso = "https://www.ingresso.com/sao-roque/home/filmes/";//url ingresso
    $urlIngresso .= trim(strtolower(str_replace(" ","-",trim(tirarAcentos($titulo)))));//url ingresso
    $diasSemana = strip_tags(trim($htmlDetail->find('div[itemprop="articleBody"] p')[0]->innertext));//dias em cartaz filme
    $descricao = strip_tags(trim($htmlDetail->find('div[itemprop="articleBody"] p')[4]->innertext));//descricao filme
    $audio = trim($htmlDetail->find('div[itemprop="articleBody"] p img[src*="legendas"]')[0]->src);//audio
    $video = trim($htmlDetail->find('div[itemprop="articleBody"] p img[src*="legendas"]')[1]->src);//video
    $classificacao = trim($htmlDetail->find('div[itemprop="articleBody"] p img[src*="legendas"]')[2]->src);//classificacao
    $linkTrailer = trim($htmlDetail->find('div.video-responsive iframe')[0]->src);//link trailer filme
    $stmt->execute();
}
die("fim");
$stmt->close();
$conn->close();
?>