<?php
// https://github.com/joshtronic/php-googleplaces
require_once 'googleplaces/GooglePlaces.php';
require_once 'googleplaces/GooglePlacesClient.php';
// $google_places->radius   = 800;
$next_page_token = false;
function getPlace($results,$google_places){
    $keyGoogle = 'AIzaSyAd7C7vzuFsMGxPrvDMMVpqQdu1iyCwxuA';
    $lat = -23.5294771;
    $lng = -47.138349;
    $google_places = new joshtronic\GooglePlaces($keyGoogle);
    $google_places->location = array($lat, $lng);
    $google_places->rankby   = 'prominence';
    $google_places->types    = 'restaurant'; // Requires keyword, name or types
    // $resultsTemp = $results;
    if(!$results){
        $results                 = $google_places->nearbySearch();
    }else{
        $google_places->pagetoken = $results['next_page_token'];
        $results                 = $google_places->nearbySearch();
    }
    
    foreach ($results['results'] as $key => $value) {
        $google_places->reference = $value['reference']; // Reference from search results
        $details                  = $google_places->details();
        $results['results'][$key] = array_merge($results['results'][$key],$details['result']);
        $results['results'][$key]['photo'] = "https://maps.googleapis.com/maps/api/place/photo?maxwidth=250&maxheight=156&photoreference=".$results['results'][$key]['photos'][0]['photo_reference']."&sensor=false&key=".$keyGoogle;
        foreach ($results['results'][$key]['photos'] as $key2 => $value2) {
            $results['results'][$key]['photos'][$key2]['img'] = "https://maps.googleapis.com/maps/api/place/photo?maxwidth=".$results['results'][$key]['photos'][$key2]['height']."&maxheight=".$results['results'][$key]['photos'][$key2]['width']."&photoreference=".$results['results'][$key]['photos'][$key2]['photo_reference']."&sensor=false&key=".$keyGoogle;
        }
    }
    if($resultsTemp)$results = array_merge($results,$resultsTemp);
    return $results;
}

for ($i=0; $i < 5; $i++) { 
    $resultstemp = getPlace($resultstemp,$google_places);
    if($results){
        $results = array_merge($results,$resultstemp['results']);
    }else{ 
        $results = $resultstemp['results'];
    }
}

echo '<pre>';
print_r(count($results));
exit;
die("xxx");