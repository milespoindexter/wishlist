<?php

//error_reporting(E_ALL);
//ini_set('display_errors', '1');

require_once("TableMgr.php");

class WishMgr extends TableMgr {

    protected $COLUMNS = array(   //"wishID",
                                  "rank",
                                  "title",
                                  "description",
                                  "link",
                                  "ownerID",
                                  "purchased");

    //name of db table
    protected $TABLE = "wishes";
    
    //primary key column of this table
    protected $PK = "wishID";
    
    //columns used to order results
    protected $ORDER_COLS = "rank, title";
    
    public function getWish( $wishID ) {
        return $this->getRow($wishID);
    }
    
    public function getAllWishes() {
        return $this->getAllRows();
    }
    
    public function deleteWish($wishID) {
        return $this->deleteRow($wishID);
    }

    public function addWish($wish) {
        return $this->addRow($wish);
    }

    public function updateWish( $wishID, $wish ) {
        return $this->editRow($wishID, $wish);
    
    }

    public function getWishIDByMetaData($wish) {
        return $this->getPrimaryKeyByMetaData($wish);
    }

    public function getLatestWishID() {
        $wishID = $this->getNewPK();
        return ($wishID - 1);
    }

    public function getWishes() {
        $sql =  "SELECT * FROM ".$this->TABLE." ORDER BY ".$this->ORDER_COLS;
        return $this->executeQuery( $sql );
    }

    public function executeSQL( $sql ) {
        return $this->executeQuery( $sql );
    }
    
    public function changeToPurchased($wishID) {
        $sql = "UPDATE ".$this->TABLE." SET purchased='true' WHERE wishID = " . $wishID;
        return $this->executeUpdate($sql);
    }
    
    public function changeToNotPurchased($wishID) {
        $sql = "UPDATE ".$this->TABLE." SET purchased='' WHERE wishID = " . $wishID;
        return $this->executeUpdate($sql);
    }
    
    public function getWishesForOwner($ownerID) {
        return $this->getRowsWhereColumnEquals("ownerID", $ownerID, 0, 200);
    }

}

?>
