<?php
	function getAttendeeInfoFromName($event, $fname, $lname){
		$pdo = new PDO('mysql:host=localhost;dbname=icdb', "root", "");
		$info = array();
		$stmt = $pdo->prepare("SELECT Id, Fname, Lname, Email FROM attendee WHERE Eventid = ? AND Attended = 0 AND Fname LIKE CONCAT('%',?,'%') AND Lname LIKE CONCAT('%',?,'%')");
	    $stmt->bindParam(1, $event);
	    $stmt->bindParam(2, $fname);
	    $stmt->bindParam(3, $lname);
	    if($stmt->execute()){
	        while($row = $stmt->fetch()){
	            array_push($info, $row);
	        }
	    }
	    return $info;
	}

	function getAttendeeCount($fname, $lname, $email, $event){
		$pdo = new PDO('mysql:host=localhost;dbname=icdb', "root", "");
		$info = array();
		$stmt = $pdo->prepare("SELECT COUNT(*) AS num FROM attendee WHERE Eventid = ? AND Fname = ? AND Lname = ? AND Email = ?");
	    $stmt->bindParam(1, $event);
	    $stmt->bindParam(2, $fname);
	    $stmt->bindParam(3, $lname);
	    $stmt->bindParam(4, $email);
	    if($stmt->execute()){
	        $info = $stmt->fetch();
	    }
	    return $info;

	}
?>