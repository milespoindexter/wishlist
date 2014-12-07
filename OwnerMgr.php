<?php

//error_reporting(E_ALL);
//ini_set('display_errors', '1');

require_once("TableMgr.php");

class OwnerMgr extends TableMgr {
    
    protected $COLUMNS = array(   "ownerID",
                                  "groupID",
                                  "firstname",
                                  "lastname");

    //name of db table
    protected $TABLE = "owners";
    
    //primary key column of this table
    protected $PK = "ownerID";
    
    //columns used to order results
    protected $ORDER_COLS = "lastname, firstname";
    
    public function getOwner( $ownerID ) {
        return $this->getRow($ownerID);
    }
    
    public function getAllOwners() {
        return $this->getAllRows();
    }
    
    public function deleteOwner($ownerID) {
        return $this->deleteRow($ownerID);
    }

    public function addOwner($owner) {
        return $this->addRow($owner);
    }

    public function editOwner( $ownerID, $owner ) {
        return $this->editRow($ownerID, $owner);
    
    }

    public function getOwnerIDByMetaData($owner) {
        return $this->getPrimaryKeyByMetaData($owner);
    }

    public function getLatestOwnerID() {
        $ownerID = $this->getNewPK();
        return ($ownerID - 1);
    }

    public function getOwners() {
        $sql =  "SELECT * FROM ".$this->TABLE." ORDER BY ".$this->ORDER_COLS;
        return $this->executeQuery( $sql );
    }
    
    public function getOwnersForGroup($groupID) {
        return $this->getRowsWhereColumnContains("groupID", $groupID, 0, 100);
    }

    public function executeSQL( $sql ) {
        return $this->executeQuery( $sql );
    }

}

?>
