<?php
require_once "../db/connect.php";
require_once "../db/classes/DbManagerInterface.php";
require_once "TableSummary.php";

class DbClass implements DbManagerInterface
{
    private $tableSummaries;

    public function __construct()
    {
        $this->tableSummaries = TableSummary::getTableSummaries();
    }

    /**
     * Pulls an entry from a table using the id(s) of the entry and returns an array
     *
     * @param int $ids
     * The row entry of the corresponding id(s)
     * @return array
     * The full row data of the corresponding id(s) pulled from the db of the class with corresponding name
     */
    function readById(array $ids)
    {
        //$conditional = join(", ", $this->getColumnEqualsValuePair($this->keyAttributes));
        $conditional = "";
        for ($index=0; $index<count($ids); $index++)
            {
                $conditional .= $this->keyAttributes[$index] . "=" . $ids[$index] . " ";
            }
        $statement = newPDO()->prepare("SELECT * FROM {$this->tableName} WHERE {$conditional}");

        $tableResult = array();
        if($statement->execute())
        {
            while($row = $statement->fetch())
            {
                array_push($tableResult, $row);
            }
            return $tableResult;
        } else {
            trigger_error("Selection statement failed. Could not retrieve entry from database");
        }
    }

    static function readByIdNew(Entry $entry, int $id)
    {
        $tableSummary = self::getTableSummary($entry);

        $conditional = self::getColumnEqualsValuePair($entry, $tableSummary->getDbPrimaryAttributes(), $tableSummary->getPrimaryAttributes());

        $pdo = newPDO();
        $statement = $pdo->prepare("SELECT * FROM ? WHERE ?");
        $statement->bindParam(1, $tableSummary->getTableName());
        $statement->bindParam(2, $conditional);

        $result = array();
        if($statement->execute())
        {
            while($row = $statement->fetch())
            {
                array_push($tableResult, $row);
            }
            var_dump($result);
            return $result;
        } else {
            false;
        }
    }

    /**
     * Inserts a new entry into the table corresponding to the name of the class using the specified attributes in that
     * class
     *
     * @param $entry
     * refers to the entry that needs to be inserted into the database
     * @return bool
     * returns true if the insertion was successful; otherwise returns false.
     */

    function insert()
    {
        $columns = join(", ", $this->attributeDbNames);
        $values = join(", ", $this->getValuesOfAttributes($this->attributeNames));
        $statement = newPDO()->prepare("INSERT INTO {$this->tableName}({$columns}) VALUES ({$values})");
        return $statement->execute();
    }

    static function insertNew(Entry $entry)
    {
        $tableSummaries = TableSummary::getTableSummaries();    //$tableSummaries is type array
        $tableType = $tableSummaries[get_class($entry)];        //$tableType      is type TableSummary

        $columns = join(", ", $tableType->getDbAttributes());
        $values = join(", ", self::getValuesOfAttributes($entry, $tableType->getAttributes()));

        $statement = newPDO()->prepare("INSERT INTO {$tableType->getDbTableName()}({$columns}) VALUES ({$values})");

        return $statement->execute();
    }

    /**
     * Resets all attributes in the database to the current attribute values represented in the class. The entry must exist
     * in the database to update the entry.
     * Throws error if the primary key is not set; indicating that there is no entry in the database.
     *
     * @param $entry
     * @return bool
     * returns true if the update is successful; otherwise returns false.
     */
    function update()
    {
        if (!empty($this->getValueOfAttribute($this->keyAttributes)))
        {
            $columnValuePair = $this->getColumnEqualsValuePair($this->attributeNames);

            $values = join(", ", $columnValuePair);
            $conditional = $this->DbKeyAttributes . "=" . $this->getValuesOfAttributes($this->keyAttributes);
            $statement = newPDO()->prepare("UPDATE {$this->tableName} SET {$values} WHERE {$conditional}");
            return $statement->execute();
        } else {
            trigger_error("Entry does not exist!");
        }
    }

    static function updateNew(Entry $entry)
    {
        $tableSummary = self::getTableSummary($entry);

        if (!empty(self::getValuesOfAttributes($entry, $tableSummary->getPrimaryAttributes())))
        {
            $tableName = $tableSummary->getDbTableName();

            $valuesColumnValuePair = self::getColumnEqualsValuePair($entry, $tableSummary->getDbAttributes(), $tableSummary->getAttributes());
            $values = join(", ", $valuesColumnValuePair);

            $conditionalColumnValuePair = self::getColumnEqualsValuePair($entry, $tableSummary->getDbPrimaryAttributes(), $tableSummary->getPrimaryAttributes());
            $conditional = join(", ", $conditionalColumnValuePair);

            $pdo = newPDO();
            $statement = $pdo->prepare("UPDATE ? SET ? WHERE ?");
            $statement->bindParam(1, $tableName);
            $statement->bindParam(2, $values);
            $statement->bindParam(3, $conditional);
            return $statement->execute();
        } else {
            trigger_error("Entry does not exist!");
        }
        return false;
    }

    function delete()
    {
        // TODO: Implement delete() method.
    }

    static function save(Entry $entry)
    {
        $tableSummary = self::getTableSummary($entry);

        if (self::getValuesOfAttributes($entry, $tableSummary->getPrimaryAttributes())) // if the value of the primary key exists (is truthy)
        {
            self::updateNew($entry);
        } elseif (false) { // TODO change "false" to fit the following condition: if the value of a secondary key matches a value of a database secondary key
            self::updateNew($entry);
        } else {
            self::insertNew($entry);
            // TODO set the subclass id(s) to the newly created entry id(s)
            //see https://www.w3schools.com/php/php_mysql_insert_lastid.asp
        }
    }

    /**
     * Takes a class defined attribute <name> and returns get<name>() for that attribute in that class.
     * Example: an attribute named "email" will return getEmail().
     *
     * @param string $attributeName
     * @return mixed
     */
    private static function getValueOfAttribute(Entry $entry, string $attributeName)
    {
        $getAttribute = "get".ucfirst($attributeName);
        return $entry->$getAttribute();
    }

    /**
     * Takes an array of strings where each string is a class defined attribute <name> and returns an array of the
     * result of the get<name>() function declared in that class
     * @param array $attributeNames
     * @return array
     */
    private static function getValuesOfAttributes(Entry $entry, array $attributeNames)
    {
        $tableSummaries = TableSummary::getTableSummaries();
        $tableType = $tableSummaries[get_class($entry)];

        $attributeValues = array();
        foreach ($attributeNames as $attrName)
        {
            array_push($attributeValues, self::getValueOfAttribute($entry, $attrName));
        }

        return $attributeValues;
    }

    private static function getTableSummary(Entry $entry) : TableSummary
    {
        $tableSummaries = TableSummary::getTableSummaries();
        return $tableSummaries[get_class($entry)];
    }

    private static function getColumnEqualsValuePair(Entry $entry, array $dbColumns, array $values)
    {
        if (count($dbColumns) != count($values))
        {
            return new Exception("columns and values arrays must be the same length");
        }

        $columnValuePairs = array();
        for ($i=0; $i<count($dbColumns); $i++)
        {
            $columnValuePair = $dbColumns[$i] . "=" . self::getValueOfAttribute($entry, $values[$i]) . " ";
            array_push($columnValuePairs, $columnValuePair);
        }

        return $columnValuePairs;
    }

    static function getAllEventsAfterCurrentDate(){
        $statement = newPDO()->prepare("SELECT * FROM event WHERE Date >= DATE(NOW())"); //Fetch all events after current date
        $info = array();
        if($statement->execute()) {
            while($row = $statement->fetch()) {
                array_push($info, $row);
            }
        }
        return $info;
    }

    static function getAttendeesForEvent($eventid){
        $pdo = newPDO();
        $statement = $pdo->prepare("SELECT Id, Fname, Lname, Email, Phone FROM attendee, attendance, event WHERE event.Eventid = attendance.Eventid AND attendee.Id = attendance.Attendeeid AND event.Eventid = ?");
        $statement->bindParam(1, $eventid);
        $info = array();
        if($statement->execute()) {
            while($row = $statement->fetch()) {
                array_push($info, $row);
            }
        }
        return $info;
    }

    static function getEventByID($id) {
        $pdo = newPDO();
        $statement = $pdo->prepare("SELECT * FROM event WHERE Eventid=?"); //Fetch specific event by id
        $statement->bindParam(1, $id);
        $statement->execute();
        return $info = $statement->fetch();
    }

    static function checkAttendanceByID($attendeeID, $eventID){
        $pdo = newPDO();
        $statement = $pdo->prepare("SELECT COUNT(*) AS num FROM attendance WHERE Attendeeid = ? AND Eventid = ? AND Attended = TRUE"); //Find all attendees with similar first and last names entered
        $statement->bindParam(1, $attendeeID);
        $statement->bindParam(2, $eventID);
        if($statement->execute()){
            $result = $statement->fetch();
            if($result['num'] > 0){
                return TRUE;
            }
            else{
                return FALSE;
            }
        }
    }

    static function setAttendedTrue($attendeeID, $eventID){
        $pdo = newPDO();
        $statement = $pdo->prepare("UPDATE attendance SET Attended = TRUE WHERE Eventid = ? AND Attendeeid = ?");
        $statement->bindParam(1, $eventID);
        $statement->bindParam(2, $attendeeID);
        if($statement->execute()){
            return TRUE;
        }
        else{
            return FALSE;
        }
    }
}