<?php
require_once "Entry.php";
require_once "../db/classes/DbClass.php";
require_once "../backend/classes/Attendee.php";

class Event extends Entry {
    private $id;
    private $name;
    private $date;
    private $description;
    private $eventbriteId;
    private $attendees = array();

    /**
     * Event constructor.
     * @param $id
     */
    public function __construct(int $id = 0) {
        if ($id) {
            $dbEvent = DbClass::readById($this, array($id));
            /*if ($id != $dbEvent[""]) {
                echo "There is no event with the given id";
                trigger_error("There is no event with the given id");
            }*/

            if (!$dbEvent["Description"]) {
                $dbEvent["Description"] = "";
            }

            $this->create($dbEvent["Name"], $dbEvent["Date"], $dbEvent["Description"], $dbEvent["Ebid"]);
            $this->id = $id;
            $this->populateAttendeeList();
        } else {
            $this->create("", "");
        }
    }

    public function create(string $name, string $date, string $description = "", int $eventbriteId = null) {
        $this->id           = null;
        $this->name         = $name;
        $this->date         = $date;
        $this->description  = $description;
        $this->eventbriteId = $eventbriteId;
    }

    public function populateAttendeeList() {
        $attendees   = [];
        $dbAttendees = DbClass::getAttendeesForEvent($this->id);
        foreach ($dbAttendees as $dbAttendee) {
            $attendee = new Attendee($dbAttendee["Id"]);
            array_push($attendees, $attendee);
        }
        $this->attendees = $attendees;
    }

    /**
     * @param $name
     * @param $date // TODO add regex before setting date in constructor and setDate().
     * return an invalid format exception if wrong format. see line 52
     * @param $description
     * @param null $eventbriteId
     */

    public function createNew($id, $name, $date, $description, $eventbriteId = null) {
        //unset($this->id);
        $this->id   = $id;
        $this->name = $name;

        //$date = date_format($date, "Y-m-d");
        $this->date        = $date;
        $this->description = $description;
        if (!empty($eventbriteId)) {
            $this->eventbriteId = $eventbriteId;
        }
    }

    public function save() {
        if ($this->id) {
            return DbClass::update($this);
        } else {
            return DbClass::insert($this);
        }
    }

    public function delete() {
        // TODO: Implement delete() method.
    }

    public function addAttendee(Attendee $attendee) {
        array_push($this->attendees, $attendee);
    }

    /**
     * returns true if removal is sucessful. Returns false if there is no attendee to be removed. or the removal failed.
     *
     * @param Attendee $attendee
     * @return bool
     */
    public function removeAttendee(Attendee $attendee) {
        $index = array_search($attendee, $this->attendees);
        if ($index) {
            $this->attendees = array_splice($this->attendees, $index, $index);
            return true;
        } else {
            return false;
        }
    }

    public function getName() : string {
        return $this->name;
    }

    public function setName(string $name) {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getDate() {
        return $this->date;
    }

    public function setDate(string $date) : void // TODO add regex checking for date format
    {
        $this->date = $date;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setDescription(String $description) : void {
        $this->description = $description;
    }

    public function getId() {
        return $this->id;
    }

    public function setId(int $id) : void {
        $this->id = $id;
    }

    public function getEventbriteId() {
        return $this->eventbriteId ? $this->eventbriteId : null;
    }

    /**
     * @return array
     */
    public function getAttendees() : array {
        return $this->attendees;
    }
}