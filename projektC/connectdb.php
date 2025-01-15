<?php
function connectDB() {
	//Enter your own database connectivity information here.
	$servername = "mariadb.edu.liu.se";
	$username = "klabe908";
	$password = "klabe90879b6";
	$db = "klabe908";

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

function echo_debute($teamName) {
	echo "<li>Debute Team(s): ". echo_team_multivalued($player_info["team_ID"])."</li>"
}

function echo_previous($teamName) {

}


function echo_team_multivalued($member_key){
	if (is_array($member_key)){
		$final_string = "";
		console_log($member_key);
		foreach($member_key as $key => $value){

			$stmt = $conn->prepare("SELECT fullName FROM team WHERE ID = $key");
			$stmt->execute();
			$teamName = $stmt->get_result();

			if ($value["debute"]){
				echo_debute($teamName);
			}

			if ($value["previous"]){
				echo_previous($teamName);
			}


			$final_string .= $value. ", ";
			console_log($key);
			console_log($value);
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
	$player_team_array = array();

	while ($row = $teamresult->fetch_assoc()){
		$player_team_array[$row["team_ID"]] = $row;
	}



	$player_AP_array["team_ID"] = $player_team_array;
	return $player_AP_array; //DONE
}// end function get_player_info

function get_coach_info($conn, $coachID){
	//fetch coach data from database. Don't forget colleges.
	//Return an associative array
	$stmt = $conn->prepare("SELECT * FROM coach LEFT JOIN coachCollege ON ID = coach_ID WHERE ID = $coachID");
	$stmt->execute();

	$result = $stmt->get_result();
	$row = $result->fetch_assoc();
	return $row;
}// end function get_coach_info

//------------------------------------------------------------------------------------
//Funktioner för del D

function add_user($conn, $userInfo) {
	//add a user into the database. Assume data is validated but check for unique name and email.
	//return something that means the user was added or not.
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
