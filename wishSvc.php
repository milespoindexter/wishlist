<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

header('Content-type: application/json');

require_once("WishMgr.php");
$wishMgr = new WishMgr();

$operation = null;
$ownerID = null;
$wishID = null;
$json_response = "";

//var_dump($_POST);
$postData = json_decode(file_get_contents('php://input'), true);
//print_r($data);
//echo $postData["update"]["title"];

if( isset($_GET["wishID"]) ) {
	$wishID = $_GET["wishID"];
	$json_response = json_encode($wishMgr->getWish($wishID));
}
elseif( isset($_GET["ownerID"]) ) {
    $ownerID = $_GET["ownerID"];
    $json_response = json_encode($wishMgr->getWishesForOwner($ownerID));

}
elseif( isset($postData["create"]) ) {
	//echo("create POST found . . .");
	$wish = $postData["create"];
	//echo( json_encode("create new wish: ".$wish["title"]));
	$result["success"] = $wishMgr->addWish($wish);
	$json_response = json_encode($result);
}
elseif( isset($postData["delete"]) ) {
	$wishID = $postData["delete"];
	//echo("delete wishID: ".$wishID);
	$result["success"] = $wishMgr->deleteWish($wishID);
	$json_response = json_encode($result);
}
elseif( isset($postData["update"]) ) {
	$wish = $postData["update"];
	$wishID = $wish["wishID"];
	//echo("update wishID: ".$wishID);
	//echo(", wish title: ".$wish["title"]);
	$result["success"] = $wishMgr->updateWish($wishID, $wish);
	$json_response = json_encode($result);
	
}


echo($json_response);





?>

