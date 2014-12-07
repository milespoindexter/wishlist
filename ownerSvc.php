<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

header('Content-type: application/json');

require_once("OwnerMgr.php");
$ownerMgr = new OwnerMgr();

$groupID = null;
if( isset($_GET["groupID"]) ) {
    $groupID = $_GET["groupID"];
}

$owners_json = json_encode($ownerMgr->getOwnersForGroup($groupID));
echo($owners_json);

?>

