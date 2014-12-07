<?php

//error_reporting(E_ALL);
//ini_set('display_errors', '1');

require_once("TableMgr.php");

class GroupMgr extends TableMgr {
    
    protected $COLUMNS = array(   "groupID",
                                  "name",
                                  "groupOrder");
    
    //name of db table
    protected $TABLE = "groups";
    
    //primary key column of this table
    protected $PK = "groupID";
    
    //columns used to order results
    protected $ORDER_COLS = "groupOrder";
    
    public function getGroup( $groupID ) {
        return $this->getRow($groupID);
    }
    
    public function getAllGroups() {
        return $this->getAllRows();
    }
    
    public function deleteGroup($groupID) {
        return $this->deleteRow($groupID);
    }

    public function addGroup($group) {
        return $this->addRow($group);
    }

    public function editGroup( $groupID, $group ) {
        return $this->editRow($groupID, $group);
    
    }

    public function getGroupIDByMetaData($group) {
        return $this->getPrimaryKeyByMetaData($group);
    }

    public function getLatestGroupID() {
        $groupID = $this->getNewPK();
        return ($groupID - 1);
    }

    public function getGroups() {
        $sql =  "SELECT * FROM ".$this->TABLE." ORDER BY ".$this->ORDER_COLS;
        return $this->executeQuery( $sql );
    }

    public function executeSQL( $sql ) {
        return $this->executeQuery( $sql );
    }

}

?>
