function verifyUser(userid, email, eventid) {
  console.log(email + userid);
	let confirmation = confirm("Check in user with email: " + email + "?");
	if(confirmation === true) {
		let xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
  		if (this.readyState === 4 && this.status === 200) { //If server returns correctly, callback function sets window back to checkin
    		alert("Check in Successful");
    		window.location = ("checkin.php");
		  }
		};
		xhttp.open("GET", "/php/checkAttendeeIn.php?userid=" + userid + "&eventid=" + eventid, true); //AJAX call to checkEmail php script
		xhttp.send();
	}
}
