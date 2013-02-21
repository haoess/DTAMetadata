/** 
 * To specify which columns are to be visible in the user display 
 * (In the view that lists all database records of a class as a table) 
 */
public function getTableViewColumnNames(){
    return $this->captions;
}

/** 
 * To access the data using the specified column names.
 * @param string columnName 
 */
public function getAttributeByTableViewColumName($columnName){
    $accessor = $this->accessors[$columnName];
    return $this->$accessor;
}    

