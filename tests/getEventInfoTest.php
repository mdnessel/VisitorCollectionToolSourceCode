<?php
include_once "../businessLogic/getEventInfo.businessLogic";

class GetEventInfoTest extends PHPUnit_Framework_TestCase { //Run this file with phpunit command from command line
    public function setUp() {
    }

    public function tearDown() {
    }

    function testGetEventByIdSuccess() {
        $this->assertSame(getEvent(999)->getName(), "Test Event");
		}
	}