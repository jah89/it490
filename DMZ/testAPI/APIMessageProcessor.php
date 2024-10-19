<?php
require_once('/home/enisakil/git/it490/db/connectDB.php');

class APIMessageProcessor
{
    private $responseError;
    private $response;

    public function call_processor($request)
    {
        switch ($request['type']) {
            case 'api_player_data_request':
                echo("API Player Data request received");
                $this->processorAPIPlayerDataRequest($request);
                break;
            
            case 'api_player_stats_request':
                echo("API Player Stats request received\n");
                $this->processAPIPlayerStatsRequest($request);
                break;

            case 'api_team_data_request':
                echo("API Team Data request received");
                $this->processorAPITeamsDataRequest($request);
                break;

            case 'api_game_data_request':
                echo("API Game Data request received\n");
                $this->processAPIGameDataRequest($request);
                break;

            // Add cases for other types of API requests if needed
            default:
                $this->responseError = ['status' => 'error', 'message' => 'Unknown request type'];
                echo "Unknown request type: {$request['type']}\n";
                break;
        }
    }
private function processAPIPlayerDataRequest($jsonData)
{
    $data = json_decode($jsonData, true);  // Decode the JSON data
    
    if (!$data) {
        echo "Error decoding JSON data\n";
        return;
    }

        // Create a database connection
        $conn = connectDb();  // Use the existing database connection

        // Insert into players table
        $stmt = $conn->prepare("INSERT INTO players (player_id, name, team_id, season, country) 
                                VALUES (?, ?, ?, ?, ?)
                                ON DUPLICATE KEY UPDATE name = ?, team_id = ?, season = ?, country = ?");

        // Bind parameters
        $stmt->bind_param(
            "issssisss",  // Parameter types: i = integer, s = string
            $data['id'],
            $data['name'],
            $data['team'],
            $data['season'],
            $data['country'],
            $data['name'],     // For ON DUPLICATE KEY UPDATE
            $data['team'],     // For ON DUPLICATE KEY UPDATE
            $data['season'],   // For ON DUPLICATE KEY UPDATE
            $data['country']   // For ON DUPLICATE KEY UPDATE
        );

        // Execute the statement
        if ($stmt->execute()) {
            echo "Player data inserted/updated successfully\n";
        } else {
            echo "Error executing statement: " . $stmt->error . "\n";
        }

        $stmt->close(); // Close the statement
        $conn->close(); // Close the connection
    }

private function processAPIPlayerStatsRequest($jsonData)
{
    $data = json_decode($jsonData, true);  // Decode JSON data
        
    if (!$data) {
        echo "Error decoding JSON data\n";
        return;
    }

    // Create a database connection
    $conn = connectDb();  // Reuse existing database connection

    // Prepare the SQL statement for inserting player stats
    $stmt = $conn->prepare("INSERT INTO player_stats (player_id, season, game_date, points, rebounds, assists, blocks, steals) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    // Bind parameters
    $stmt->bind_param(
        "issiiiii",  // Parameter types: i = integer, s = string
        $data['player_id'],
        $data['season'],
        $data['game_date'],
        $data['points'],
        $data['rebounds'],
        $data['assists'],
        $data['blocks'],
        $data['steals']
    );

    // Execute the statement
    if ($stmt->execute()) {
        echo "Player stats inserted successfully\n";
    } else {
        echo "Error executing statement: " . $stmt->error . "\n";
    }
    // Close the statement and connection
    $stmt->close(); // Close the statement
    $conn->close(); // Close the connection
}

    private function processAPITeamsDataRequest($jsonData)
{
    // Decode the JSON data
    $data = json_decode($jsonData, true);  
    
    if (!$data || !isset($data['response'])) {
        echo "Error decoding JSON data or invalid format\n";
        return;
    }

    // Create a database connection
    $conn = connectDb();  // Reuse the existing database connection

    // Prepare the SQL statement for inserting/updating teams
    $stmt = $conn->prepare("INSERT INTO teams (team_id, name, city, conference, division) 
                            VALUES (?, ?, ?, ?, ?)
                            ON DUPLICATE KEY UPDATE name = ?, city = ?, conference = ?, division = ?");

    // Loop through the 'response' array and process each team
    foreach ($data['response'] as $team) {
        $teamId = $team['id'];
        $name = $team['name'];
        $city = $team['city'];
        $conference = $team['leagues']['standard']['conference'] ?? null;
        $division = $team['leagues']['standard']['division'] ?? null;

        // Bind parameters
        $stmt->bind_param(
            "issssssss",  // Parameter types: i = integer, s = string
            $teamId,
            $name,
            $city,
            $conference,
            $division,
            $name,
            $city,
            $conference,
            $division
        );

        // Execute the statement
        if (!$stmt->execute()) {
            // Handle error
            echo "Error executing statement: " . $stmt->error . "\n";
        }
    }

        echo "Teams data inserted/updated successfully\n";

        // Close the statement and connection
        $stmt->close(); // Close the statement
        $conn->close(); // Close the connection
}

private function processAPIGameDataRequest($jsonData) {
    // Decode the JSON data
    $games = json_decode($jsonData, true); // Decoding as an associative array

    // Check if decoding was successful
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "Error decoding JSON: " . json_last_error_msg();
        return;
    }

    // Create a database connection
    $conn = connectDb(); // Ensure this function is defined to return a valid DB connection

    // Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO games (game_id, home_team_id, visitor_team_id, date, home_team_score, visitor_team_score) VALUES (?, ?, ?, ?, ?, ?)");

    // Loop through the games data
    foreach ($games as $game) {
        // Extract relevant data
        $game_id = $game['id'];
        $home_team_id = $game['teams']['home']['id'];
        $visitor_team_id = $game['teams']['visitors']['id'];
        $date = $game['date']['start']; // Start date of the game
        $home_team_score = $game['scores']['home']['points'];
        $visitor_team_score = $game['scores']['visitors']['points'];

        // Bind parameters
        $stmt->bind_param("iiisii", $game_id, $home_team_id, $visitor_team_id, $date, $home_team_score, $visitor_team_score);

        // Execute the statement
        if (!$stmt->execute()) {
            // Handle error
            echo "Error: " . $stmt->error;
        }
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}



}