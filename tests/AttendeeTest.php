<?php
require_once "../backend/classes/Attendee.php";


class AttendeeTest extends PHPUnit_Framework_TestCase {
    private $attendee;

    public function setUp() {
        $this->attendee = new Attendee();
        $this->attendee->createNew(9999, "Bob", "Jones", "bjones@gmail.com", "555-5555");
    }

    public function tearDown() {

    }

    function testCreateNewAttendee() {
        $this->assertInstanceOf(Attendee::class, $attendee = new Attendee());
    }

    function testGetFname() {
        $this->assertSame("Bob", $this->attendee->getFirstName());
    }
}