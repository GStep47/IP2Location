<html>
<head></head>
<body>
<h2>IP2Location with MySQL Database</h2>
<h3>Overview</h3>
<p>This is a simple .PHP file demonstrating how the databases at <a href="ip2location.com">IP2LOCATION.com</a> can be used to determine a website visitor's city and state.</p>
<p>This demonstration file uses the <a href="https://www.ip2location.com/samples/db3-ip-country-region-city.txt">sample version</a> of <a href="https://www.ip2location.com/databases/db3-ip-country-region-city">DB3, "IP-Country-Region-City Database"</a> which returns a city-state pair. Full specifications for the ip-country-region-city database are <a href="https://www.ip2location.com/docs/db3-ip-country-region-city-specification.pdf">here</a>.</p>
<p>I have added functionality that converts the state/province name to its two-digit code, and limits 
<h3>About the IP2Location.com database</h3>
<p> This sample file runs against a MySQL database table based on this information.</p> 
<p>The IP2Location database includes full state names ("Nebraska") while the DTN AgHost Weather API requires the two-letter abbreviation ("NE"). To convert this, my database contains a second table <b>ip2locst</b> which converts the full state name to the code via a sub-query.</p>
<p>The sample database has a very limited IP range. This demonstration displays your IP address and queries the database for it. If your IP address is not in the demo range (which it probably isn't), it generates a random IP address that is within the range and queries that, just to demonstrate the database query. This code could be adapted to choose a default city/state if the database lookup fails for any reason.</p>
<hr />

<?php

$yourIPaddress = $_SERVER['REMOTE_ADDR'];

echo "<p><b>You are visiting from</b>: " . $yourIPaddress. "</p>";

//convert IP address to a number.
//If the Address is A.B.C.D.
//The IP number X is X = A x (256*256*256) + B x (256*256) + C x 256 + D

//find positions of . in IP address
$position = array(3);
$position[0]=stripos($yourIPaddress, ".");
$position[1]=stripos($yourIPaddress, ".", $position[0]+1);
$position[2]=stripos($yourIPaddress, ".", $position[1]+1);

//create variables
$a = substr($yourIPaddress, 0, $position[0]);
$b = substr($yourIPaddress, $position[0]+1, $position[1]-$position[0]-1);
$c = substr($yourIPaddress, $position[1]+1, $position[2]-$position[1]-1);
$d = substr($yourIPaddress, $position[2]+1);

//do the math
$IPNumber = ($a*256*256*256) + ($b*256*256) + ($c*256) + $d;

//connect to database
$conn = mysqli_connect("mysql6.brinkster.com","garystephen","gfobIXYW.6","garystephen");

// Check connection
if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }

//create query
$query1 = "SELECT CITY, (SELECT CODE FROM ip2locst WHERE NAME=REGION) AS STATE FROM ip2loc WHERE COUNTRY_CODE IN (\"US\", \"CA\") AND (" . $IPNumber .  " BETWEEN ip2loc.IP_FROM AND ip2loc.IP_TO)=1";

if(!$result = $conn->query($query1)){
    die('There was an error running the query [' . $conn->error . ']');
}

//echo "<p>Total results: " . $result->num_rows . "</p>";
  
if ($result->num_rows == 0) {
	echo "<p>Your IP address is not in the database, so we will query a random location that is.</p>";
	$IPNumber = mt_rand(67260672, 67339519); //the range of IP numbers that is in the demo database
	//In a live environment, you would use this section to set a default location. I'm doing this to demonstrate that the DB lookup works.
	
	//run the query again with the random $IPNumber
	$query1 = "SELECT CITY, (SELECT CODE FROM ip2locst WHERE NAME=REGION) AS STATE FROM ip2loc WHERE COUNTRY_CODE IN (\"US\", \"CA\") AND (" . $IPNumber .  " BETWEEN ip2loc.IP_FROM AND ip2loc.IP_TO)=1";	
	echo "<p><b>Random IP address</b>: " . $IPNumber . "</p>";
	
	$result->free(); //clear result to re-run
	
	if(!$result = $conn->query($query1)){
		die('There was an error running the query [' . $conn->error . ']');
	}
}

//whichever you did, display the results
$row = $result->fetch_row();
$city = urlencode($row[0]);
$state = urlencode($row[1]);
echo "<p>Location is " . urldecode($city) . ", " . $state . "</p>";

//free result
$result->free();

// Get thread id and use it to kill database connection
$t_id=mysqli_thread_id($conn);
mysqli_kill($conn,$t_id);


?>

</body>
</html>
