<?php
function connectDB() {
	//Enter your own database connectivity information here.
	$servername = "localhost";
	$username = "root";
	$password = "";
	$db = "test";

	// Create connection
	$conn = new mysqli($servername, $username, $password, $db);

	// Check connection
	if ($conn->connect_error) {
    	die("Database connection failed: " . $conn->connect_error);
	} 
	return $conn;
}// end connectDB		

function console_log($input){
	echo "<script>console.log(" . json_encode($input) . ");</script>";
}

function closeDB ($conn) {
	$conn->close();
}// end function closedb

function echo_multivalued($member_key){
	if (is_array($member_key)){
		$final_string = "";
		foreach($member_key as $val){
			$final_string .= $val. ", ";
		}
		return $final_string;
	} else {
		return $member_key;
	}
}

function get_teams_list($conn, $sortBy) {
	//Fetch the list of teams from the database, use ORDER BY. 
	//Return a list of associative arrays (dict) of team info.
	$sql = "SELECT * from team ORDER BY $sortBy";
	$result = $conn->query($sql);
	
	if ($result->num_rows > 0) {
		// output data of each row
		$teams_array = array();
		while($row = $result->fetch_assoc()) {
			$teams_array[] = $row;
		}
	} else {
		echo "0 results";
	}
	return $teams_array;
	$conn->close();
}// end function get_teams_list

function get_team_info($conn, $teamID) {
	//Fetch team data from database.
	//Return an associative array
	$teams_list = get_teams_list($conn, "ID");
	foreach ($teams_list as $x){
		if ($x["ID"] == $_GET['teamID']) {
			$selected_team = $x;
			break;
		} else {
			$selected_team = False;
		}
	}
	return $selected_team;
}// end function get_team_info

function get_team_players($conn, $teamID) {
	//Fetch the players for a team
	//Return a list of name and ID for each player
	$stmt = $conn->prepare("SELECT player.ID, player.fullName FROM player INNER JOIN teamPlayer ON teamPlayer.player_ID = player.ID WHERE teamPlayer.team_ID = $teamID AND teamPlayer.present = TRUE");
	$stmt->execute();

	$result = $stmt->get_result();
	$players = [];
	while ($row = $result->fetch_assoc()){
		$players[] = $row;
	}

	return $players;
}

function get_team_coaches($conn, $teamID) {
	//Fetch the coaches for a team
	//Return a list of name and ID for each coach
	$stmt = $conn->prepare("SELECT ID, fullName FROM coach WHERE team_ID = $teamID");
	$stmt->execute();

	$result = $stmt->get_result();
	$coaches = [];
	while ($row = $result->fetch_assoc()){
		$coaches[] = $row;
	}

	return $coaches;
}// end function get_team_coaches

function merge_AP($row, $player_AP_array){
	if ($player_AP_array){
		foreach ($row as $key => $value){
			if(!in_array($value,$player_AP_array)){
				if (is_array($player_AP_array[$key])){
					if(!in_array($value, $player_AP_array)){
						array_push($player_AP_array[$key], $row[$key]);
					} else{
						continue;
					}
				} else {
					$player_AP_array[$key] = array($player_AP_array[$key], $row[$key]);
				}
			}
		}
	} else {
		$player_AP_array = $row;
	}

	return $player_AP_array; //DONE
}

function get_previous_debute_present($conn, $teamrow, &$player_team_array){

	$teamID = $teamrow["team_ID"];
	$stmt = $conn->prepare("SELECT fullName FROM team WHERE ID = ?");
	$stmt->bind_param("i", $teamID);
	$stmt->execute();

	$teamNAME = $stmt->get_result()->fetch_column();

	if($teamrow["debute"] == 1){
		$player_team_array["debute"][] = $teamNAME;
	}

	if($teamrow["present"] == 1){
		$player_team_array["present"][] = $teamNAME;
	}

	if($teamrow["previous"] == 1){
		if (!$teamrow["present"] == 1){
			$player_team_array["previous"][] = $teamNAME;
		}
	}
}

function get_player_info($conn, $playerID){
	//fetch player data from database. Don't forget former teams, aliases and positions.
	//Return an associative array

	$player_AP_info = $conn->prepare("SELECT * FROM player as p LEFT JOIN playerAlias as alias ON p.ID = alias.player_ID LEFT JOIN playerPosition as pos ON p.ID = pos.player_ID WHERE p.ID = $playerID");
	$player_AP_info->execute();

	$APresult = $player_AP_info->get_result();
	$player_AP_array = array();

	while ($row = $APresult->fetch_assoc()){
		$player_AP_array = merge_AP($row, $player_AP_array);
	}

	$player_team_info = $conn->prepare("SELECT * FROM teamPlayer WHERE player_ID = $playerID");
	$player_team_info->execute();

	$teamresult = $player_team_info->get_result();
	$player_team_array = array("debute" => array(), "present" => array(), "previous" => array());

	while ($row = $teamresult->fetch_assoc()){
		get_previous_debute_present(connectDB(), $row, $player_team_array);
	}

	$player_AP_array["debute"] = $player_team_array["debute"];
	$player_AP_array["present"] = $player_team_array["present"];
	$player_AP_array["previous"] = $player_team_array["previous"];
	console_log($player_AP_array);
	return $player_AP_array; //DONE
}// end function get_player_info

function get_coach_info($conn, $coachID){
	//fetch coach data from database. Don't forget colleges.
	//Return an associative array
	$stmt = $conn->prepare("SELECT * FROM coach LEFT JOIN coachCollege ON ID = coach_ID WHERE ID = $coachID");
	$stmt->execute();

	$coachresult = $stmt->get_result();
	$coacharray = array();
	while ($row = $coachresult->fetch_assoc()){
		$coacharray = merge_AP($row, $coacharray);
	}
	return $coacharray;
}// end function get_coach_info

//------------------------------------------------------------------------------------
//Funktioner för del D
function log_in($conn, $userInfo){
	$name = $userInfo['name'];
	$email = $userInfo['email'];
	$password = $userInfo['password'];

	// Step 1: Check for unique username (name) only
	$stmt = $conn->prepare("SELECT * FROM user WHERE userName = ?");
	$stmt->bind_param("s", $name); // Do this on every other prepared statement
	$stmt->execute();
	$result = $stmt->get_result();

	console_log($result->num_rows);
	if ($result->num_rows > 0) {
		$result = $result->fetch_assoc();
		// Username already exists
		if ($result["email"] == $email And password_verify($password, $result["userPassword"])){
			session_start();
			$_SESSION["userName"] = $name;
			return [
				'success' => true,
				'message' => 'You are logged in as '. $_SESSION["userName"] //SESSION är startad och $_SESSION["userName"] är satt
			];
		} else{
			return [
				'success' => false,
				'message' => 'Incorrect Email or Password'
			];
		}
	} else {
		return [
			'success' => false,
			'message' => 'A user with this username does not exists'
		];

	}
		
}
function add_user($conn, $userInfo) {
	//add a user into the database. Assume data is validated but check for unique name and email.
	//return something that means the user was added or not.
	$name = $userInfo['name'];
    $email = $userInfo['email'];
    $password = $userInfo['password'];

    // Step 1: Check for unique username (name) only
    $stmt = $conn->prepare("SELECT * FROM user WHERE userName = ?");
    $stmt->bind_param("s", $name); // Do this on every other prepared statement
    $stmt->execute();
    $result = $stmt->get_result();

	console_log($result->num_rows);
    if ($result->num_rows > 0) {
        // Username already exists
        return [
            'success' => false,
            'message' => 'A user with this username already exists.'
        ];
    }

	// Step 2: Add the user to the database
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Hash the password
    $stmt = $conn->prepare("INSERT INTO user (userName, email, userPassword) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $hashedPassword);

    if ($stmt->execute()) {
        // Successfully added the user
        return [
            'success' => true,
            'message' => "<p style='color: green;'> User successfully added.</p>"
        ];
    } else {
        // Failed to add the user
        return [
            'success' => false,
            'message' => 'Failed to add the user: ' . $stmt->error
        ];
    }
}// end function add_user

function add_player($conn, $team, $playerInfo, $user) {
	//add a player into the database. Assume data is validated and don't forget to add which team the player plays in.
	//return something that means the player was added or not.
}//end function add_player

function update_player($conn, $playerInfo) {
	//update a player in the database. Assume data is validated.
	//return something that means the player was updated or not.
}//end function update player

?>
