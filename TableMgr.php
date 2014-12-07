<?php

require_once("ConnectionMgr.php");

class TableMgr {
    
    //this must match all thet current columns in the table, for this class to work smoothly
    protected $COLUMNS = array("pKey");
    
    //name of db table
    protected $TABLE = "tableName";
    
    //primary key column of this table
    protected $PK = "pKey";
    
    //columns used to order results
    protected $ORDER_COLS = "pKey";
   
    public function getColumns() {
        return $this->COLUMNS;
    }

    protected function createColumnList() {
        $colStr = implode(", ", $this->COLUMNS);
        
        return $colStr;
    }
    
    protected function createValueList( $row ) {
        $valStr = "";
        for($i = 0; $i < count($this->COLUMNS); $i++) {
            $val = $this->disinfect( $row[ $this->COLUMNS[$i] ] );
            if(is_numeric($val)) {
                $valStr = $valStr . $val . ", ";
            }
            else {
                $valStr = $valStr . "'" . $val . "', ";
                //$valStr = $valStr . "'" . str_replace("'", "\'",$val) . "', ";
                
            }
        }
        //trim last comma (and space)
        $valStr = substr($valStr, 0, -2);
        
        return $valStr;
    }
    
    protected function getNewPK() {
        $pk = 0;
        $sql = "SELECT ".$this->PK." FROM ".$this->TABLE." ORDER BY ".$this->PK." DESC";
        try{
            $connectionMgr = ConnectionMgr::singleton();
            $con = $connectionMgr->getConnection();
            //echo("sql: ". $sqlLimited . "<br>\r");
            if($con != null) {
                $results = $con->query($sql);
                $rows = $results->fetchAll();
                
                foreach($rows as $row) {
                    $highestPK = $row[$this->PK];
                    $pk = $highestPK + 1;
                    break;
                }
                
                $connectionMgr->returnConnection($con);
            }
            else {
                //echo("getConnection() returned null<br>");
            }
        }
        catch(Exception $e) {
            echo $e->getMessage();
        }
        return $pk;
        
    }
    
    
    
    public function getPrimaryKeyByMetaData($row) {
        $pk = "";
        $sql = "SELECT ".$this->PK." FROM ".$this->TABLE;
        $columns = $this->getColumns(true);
        $firstItem = true;
        $joinWord = "WHERE";
        foreach($columns as $col) {
            if($col != $this->PK) {
                if($row[$col] == null || strlen($row[$col]) == 0 ) {
                    $sql = $sql . " ".$joinWord." (".$col." IS NULL OR ".$col." = '".$row[$col] . "')";
                }
                else if((substr($row[$col],0,1) != '0' && is_numeric($row[$col]))) {
                    $sql = $sql . " ".$joinWord." ".$col." = ".$row[$col];
                }
                else {
                    $sql = $sql . " ".$joinWord." UPPER(".$col.") = UPPER('".$this->disinfect($row[$col]) . "')";
                }
                
                if($firstItem) {
                    $joinWord = "AND";
                    $firstItem = false;
                }
            }
        }
        
        //echo("check for duplicate sql: ". $sql . "<br>\r");
        $rows = $this->executeQuery( $sql );
        if(count($rows) > 0) {
            $pk = $rows[0][$this->PK];
        }
        
        return $pk;
    }


    protected function createEditList( $row ) {
        $editStr = "";
        $cols = $this->getColumns();
        //echo("CreateEditList: cols: ".count($cols)."<br>");
        for($i = 0; $i < count($cols); $i++) {
            $val = $this->disinfect( $row[ $cols[$i] ] );
            //echo("changing $cols[$i] to ".$val."<br>");
            $editStr = $editStr . $cols[$i] . "='" . $val . "', ";
        }
        //trim last comma (and space)
        $editStr = substr($editStr, 0, -2);
        
        return $editStr;

    }
    
    protected function disinfect( $str ) {
        //escape single quotes
        $str = str_replace("'","\'",$str);
        //$str = str_replace("&","&'||'",$str);
        return $str;
        
    }
    
    
    //returns new primary key of row if successful, otherwise, returns blank string
    public function addRow( $row ) {
        $columnList = $this->createColumnList();
        $valueList = $this->createValueList($row);

        if($row != null) {
            $sql = "INSERT INTO ".$this->TABLE." (".$columnList.") VALUES (".$valueList.");";
            //echo("sql: ". $sql . "<br>\r");
            return $this->executeUpdate( $sql );
            
        }
        
        return false;
        
    }
    
    
    
    public function deleteRow( $pk ) {
        if($pk != null) {
            $sql = "DELETE FROM ".$this->TABLE." WHERE ".$this->PK." = ".$pk;
            //echo("sql: ". $sql . "<br>\r");
            return $this->executeUpdate( $sql );
        }
        
        return false;
        
    }
    
    
    public function editRow( $pk, $row ) {
        if($row != null) {
            $editList = $this->createEditList($row);
        
            $sql = "UPDATE ".$this->TABLE." SET ".$editList." WHERE ".$this->PK." = ".$pk;
            //echo("sql: ". $sql . "<br>\r");
            return $this->executeUpdate( $sql );
        }
        return false;
        
    }

    public function getRow( $pk ) {
    
        //echo("id: ".$pk."<br>\r");
        $rows = $this->getRows( $this->PK, $pk );
        //echo(count($rows)." rows found<br>\r");
        if(count($rows) > 0) {
            return $rows[0];
        }
        return false;
        //return $rows[0];
        
    }
    
    //get all rows with min and max rows defined (for pagination of results)
    public function getRowsMinMax($startIndex, $count) {
        $sql = "SELECT * FROM ".$this->TABLE." ORDER BY ".$this->ORDER_COLS." LIMIT ".$startIndex.",".$count;
        return $this->executeQuery( $sql );
        
    }

    //get all rows
    public function getAllRows() {
        $sql = "SELECT * FROM ".$this->TABLE." ORDER BY ".$this->ORDER_COLS;
        //echo("sql: ".$sql."<br>\r");
        return $this->executeQuery( $sql );
        
    }
    
    
    public function getRowCount() {
        $rowCount = 0;
        $sql = "SELECT COUNT(*) AS ROWCOUNT FROM ".$this->TABLE;
        
        $rows = $this->executeQuery( $sql );
        if(count($rows) > 0) {
            $rowCount = $rows[0]['ROWCOUNT'];
        }
    
        //echo($rowCount." rows for ".$this->TABLE."<br>\r");
        return $rowCount;
        
    }

    public function createEqualsQuery( $key, $val ) {
        $sql =  "SELECT * FROM ".$this->TABLE." \r".
                "WHERE UPPER(".$key.") = UPPER('".$val."')";
        return $sql;
    }

    public function createStartsWithQuery( $key, $val ) {
        $sql =  "SELECT * FROM ".$this->TABLE." \r".
                "WHERE UPPER(".$key.") LIKE UPPER('".$val."%')";
        if($val == "") {
            //echo("val was blank: ".$val."<br>\r");
            $sql = "SELECT * FROM ".$this->TABLE." WHERE ".$key." = ''";
        }
        else if($val == "0-9") { //get rows where key starts with a number
            //echo("val was blank: ".$val."<br>\r");
            $sql = "SELECT * FROM ".$this->TABLE." WHERE (\r".
                    $key." LIKE '0%' OR \r".
                    $key." LIKE '1%' OR \r".
                    $key." LIKE '2%' OR \r".
                    $key." LIKE '3%' OR \r".
                    $key." LIKE '4%' OR \r".
                    $key." LIKE '5%' OR \r".
                    $key." LIKE '6%' OR \r".
                    $key." LIKE '7%' OR \r".
                    $key." LIKE '8%' OR \r".
                    $key." LIKE '9%' OR \r".
                    $key." LIKE '*%' )";
                    
        }
        return $sql;
        
    }

    public function createContainsQuery( $key, $val ) {
        $sql =  "SELECT * FROM ".$this->TABLE." \r".
                " WHERE UPPER(".$key.") LIKE UPPER('%".$val."%')";
        if($val == "") {
            //echo("val was blank: ".$val."<br>\r");
            $sql = "SELECT * FROM ".$this->TABLE." WHERE ".$key." = ''";
        }
        return $sql;
        
    }
    
    public function createEndsWithQuery( $key, $val ) {
        $sql =  "SELECT * FROM ".$this->TABLE." \r".
                "WHERE UPPER(".$key.") LIKE UPPER('%".$val."')";
        if($val == "") {
            //echo("val was blank: ".$val."<br>\r");
            $sql = "SELECT * FROM ".$this->TABLE." WHERE ".$key." = ''";
        }
        return $sql;
        
    }

    public function getRows( $key, $val ) {
        $sql =  "SELECT * FROM ".$this->TABLE." \r".
                "WHERE UPPER(".$key.") LIKE UPPER('".$val."%') \r".
                "ORDER BY ".$this->ORDER_COLS;
        
        if($key == $this->PK) {
            $sql = "SELECT * FROM ".$this->TABLE." WHERE ".$this->PK." = '".$val."'";
        }
        //echo("sql: ".$sql."<br>\r");
        return $this->executeQuery( $sql );
        
    }
    
    public function getRowsWhereColumnEquals( $key, $val, $startIndex, $count) {
        $sql = $this->createEqualsQuery( $key, $val ) . " ORDER BY ".$this->ORDER_COLS." LIMIT ".$startIndex.",".$count;
        //echo("sql: ".$sql."<br>\r");
        return $this->executeQuery($sql);
        
    }

    public function getRowsWhereColumnStartsWith( $key, $val, $startIndex, $count) {
        $sql = $this->createStartsWithQuery( $key, $val ) . " ORDER BY ".$this->ORDER_COLS." LIMIT ".$startIndex.",".$count;
        //echo("sql: ".$sql."<br>\r");
        return $this->executeQuery($sql);
        
    }


    public function getRowsWhereColumnContains( $key, $val, $startIndex, $count) {
        $sql = $this->createContainsQuery( $key, $val ) . " ORDER BY ".$this->ORDER_COLS." LIMIT ".$startIndex.",".$count;
        //echo("sql: ".$sql."<br>\r");
        return $this->executeQuery($sql);
        
    }
    
    public function getRowsWhereColumnEndsWith( $key, $val, $startIndex, $count) {
        $sql = $this->createEndsWithQuery( $key, $val ) . " ORDER BY ".$this->ORDER_COLS." LIMIT ".$startIndex.",".$count;
        //echo("sql: ".$sql."<br>\r");
        return $this->executeQuery($sql);
        
    }

    protected function executeQuery( $sql ) {
        $rows = array();
        try {
            $connectionMgr = ConnectionMgr::singleton();
            $con = $connectionMgr->getConnection();
            
            if($con != null) {
                $rows = $con->query($sql);
                $connectionMgr->returnConnection($con);
            }
            else {
                echo("getConnection() returned null<br>");
            }
        }
        catch(Exception $e) {
            echo $e->getMessage();
        }
        //return $rows;
        return $rows->fetchAll();
    }


    protected function executeUpdate( $sql ) {
        $success = false;
        try{
            $connectionMgr = ConnectionMgr::singleton();
            $con = $connectionMgr->getConnection();
            
            if($con != null) {
                //echo("SQL UPDATE: ".$sql."<br>\r");
                $con->exec($sql);
                $success = true;
                $connectionMgr->returnConnection($con);
            }
            else {
                echo("getConnection() returned null<br>");
            }
        }
        catch(Exception $e) {
            echo $e->getMessage();
        }
        
        return $success;
    }


    
    /* returns number of rows of results for given query */
    public function getResultCount( $sql ) {
        return count( $this->executeQuery( $sql ) );
    
    }

    
}



?>
