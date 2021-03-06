<?php
require_once "../db/classes/DbClass.php";
require_once "../backend/classes/EventManager.php";

class AnalyticsManager {
    static function getAttendanceProportion($eventid) {
        $attendanceTotals = DbClass::getAttendanceByEventId($eventid);
        $walkin           = $attendanceTotals["walkin"];
        $registered       = $attendanceTotals["registered"];
        $attended         = $attendanceTotals["attended"];

        return $proportions = array(
            "attended"         => $attended - $walkin,
            "attendedAsWalkin" => $walkin,
            "notAttended"      => $registered - $attended,
        );
    }

    static function getGenderDifferences($eventid) {
        $maleCount   = 0;
        $femaleCount = 0;
        $otherCount  = 0;
        $gender      = ["Male" => 0, "Female" => 0, "Other" => 0];
        $attendees   = EventManager::getEvent($eventid)->getAttendees();
        foreach ($attendees as $attendee) {
            if ($attendee->getGender() == "Male") {
                $maleCount++;
            } elseif ($attendee->getGender() == "Female") {
                $femaleCount++;
            } else {
                $otherCount++;
            }
        }
        $totalCount       = $maleCount + $femaleCount + $otherCount;
        $gender["Male"]   = $maleCount/$totalCount;
        $gender["Female"] = $femaleCount/$totalCount;
        $gender["Other"]  = $otherCount/$totalCount;
        return $gender;
    }
}