<?php
require_once "../backend/checkRegistration.php";
require_once "../backend/classes/Event.php";
require_once "../backend/classes/Attendee.php";

class CheckRegistrationTest extends PHPUnit_Framework_TestCase { //Run this file with phpunit command from command line
    private $event, $registeredAttendee, $nonRegisteredAttendee;

    public function setUp() {
        $this->event                 = new Event();
        $this->registeredAttendee    = new Attendee();
        $this->nonRegisteredAttendee = new Attendee();
        $this->event->createNew(999, "Test Event", "2030-04-01", "");
        $this->registeredAttendee->createNew(10000, "Bob", "Jones", "bjones@gmail.com", "555-5555");
        $this->nonRegisteredAttendee->createNew(10001, "Mary", "Jane", "mjane@gmail.com", "555-5555");

		}
		public function tearDown(){
		}

		function testCheckRegistrationFalse(){
			$this->assertFalse(checkRegistration($this->nonRegisteredAttendee, $this->event));
	
		}

		function testCheckRegistrationTrue(){
			$this->assertTrue(checkRegistration($this->registeredAttendee, $this->event));
		}
	}