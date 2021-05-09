<?php
require_once './Geohash.php';
require_once './GeoRedis.php';

$geohash = new Geohash();

//echo  json_encode($geohash->binary(23.117596,[],$geohash->baseLengthGetNums(4, 1), $geohash->interval[0],1, []));

//var_dump($geohash->combination('1010000011', '1101000010'));


//var_dump($geohash->encode('11100110000000001101')); // ws0e

//var_dump($geohash->decode('ws0e'));

//var_dump($geohash->decodeCombination('11100110000000001101'));
/**
 * array(2) {
    [0]=>
    string(10) "1010000011"
    [1]=>
    string(10) "1101000010"
    }
 */


//var_dump($geohash->reductionBinary('1101000010', 1, $geohash->interval[1]));


//$res = new GeoRedis();
// -155.331, 21.798, 1003, -157.331,22.798,  1004,  -151.331, 25.798, 1005
//echo $res->insert(1005, 25.798, -151.331);

//var_dump($res->searchNearby(300, 21.306,-157.858));

//var_dump($res->georadiusbymember(1005));