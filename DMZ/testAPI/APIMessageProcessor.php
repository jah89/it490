<?php
require_once('/home/enisakil/git/it490/db/connectDB.php');

class APIMessageProcessor
{
    private $responseError;
    private $response;

    public function call_processor($request)
    {

        // Debugging: Log the raw request
        echo "Received request: " . $request . "\n";


        // Decode the request if it's in JSON format
        if (is_string($request)) {
            $request = json_decode($request, true);

            // Check for JSON decoding errors
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "Error decoding JSON: " . json_last_error_msg() . "\n";
            return;
        }
    }


        // Debugging: Check if $request is null
        if (is_null($request)) {
            echo "Error: Received null request.\n";
            return;
        }
        // Check if $request is an array and contains the 'type' key
        if (!is_array($request) || !isset($request['type'])) {
            echo "Error: Invalid request format. Expected an array with 'type'.\n";
            return;
        }

        // Double-decode the 'data' field if it's still a JSON string
        if (isset($request['data']) && is_string($request['data'])) {
            $request['data'] = json_decode($request['data'], true);

        // Check for JSON decoding errors in the 'data' field
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "Error decoding the 'data' field: " . json_last_error_msg() . "\n";
            return;
        }
    }

        // At this point, both 'type' and 'data' should be properly decoded and accessible
        echo "Request type: " . $request['type'] . "\n";
        echo "Decoded data: ";
        print_r($request['data']);

        switch ($request['type']) {
            case 'api_player_data_request':
                echo("API Player Data request received");
                $this->processAPIPlayerDataRequest($request);
                break;
            
            case 'api_player_stats_request':
                echo("API Player Stats request received\n");
                $this->processAPIPlayerStatsRequest($request);
                break;

            case 'api_team_data_request':
                echo("API Team Data request received");
                $this->processAPITeamsDataRequest($request);
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
private function processAPIPlayerDataRequest($request)
{
    // Check if 'data' key exists and is valid
    if (!isset($request['data']) || !isset($request['data']['response'])) {
        echo "Error: Invalid data format.\n";
        return;
    }

        // Access the response directly
        $data = $request['data']['response']; // Correctly access the response array

        // Create a database connection
        $conn = connectDb();  // Use the existing database connection

        // Insert into players table
        $stmt = $conn->prepare("INSERT INTO players (player_id, name, country, position, weight) 
                                VALUES (?, ?, ?, ?, ?)
                                ON DUPLICATE KEY UPDATE name = ?, country = ?, position = ?, weight = ?");

        // Loop through the players array
        foreach ($data as $player) {
            if (!isset($player['id'])) {
                echo "Error: Player ID not found.\n";
                continue; // Skip this player
            }

            $playerId = $player['id']; // Assign the player ID
            echo "Processing player ID: $playerId\n"; // Debugging output

            // Extract other player details (you can add checks for these too)
            $name = $player['firstname'] . ' ' . $player['lastname'];
            $position = isset($player['leagues']['standard']['pos']) ? $player['leagues']['standard']['pos'] : 'N/A';
            $weight = isset($player['weight']['pounds']) ? $player['weight']['pounds'] : 0;  // Default to 0 if not available
            $country = isset($player['birth']['country']) ? $player['birth']['country'] : 'Unknown';


        // Bind parameters
        $stmt->bind_param(
            "isssisssi",
            $playerId,
            $name,
            $country,
            $position,
            $weight,
            $name, 
            $country, 
            $position, 
            $weight
        );

        // Execute the statement
        if ($stmt->execute()) {
            echo "Player data inserted/updated successfully\n";
        } else {
            echo "Error executing statement: " . $stmt->error . "\n";
        }
    }

        $stmt->close(); // Close the statement
        $conn->close(); // Close the connection
    }


private function processAPIPlayerStatsRequest($request)
{
    // Check if 'data' key exists and is valid
    if (!isset($request['data']) || !isset($request['data']['response'])) {
        echo "Error: Invalid data format.\n";
        return;
    }

    // Access the response directly
    $data = $request['data']['response']; // Correctly access the response array

    // Create a database connection
    $conn = connectDb();  // Reuse existing database connection

    // Get the player ID from parameters
    $playerId = $request['data']['parameters']['id'] ?? null;
    if (!$playerId) {
        echo "Error: Player ID not found in parameters.\n";
        return;
    }

    // Get the season from parameters
    $season = $request['data']['parameters']['season'] ?? null;
    if (!$season) {
        echo "Error: Season not found in parameters.\n";
        return;
    }

    // Prepare the SQL statement for inserting player stats
    $stmt = $conn->prepare("INSERT INTO player_stats (player_id, season, game_id, points, rebounds, assists, blocks, steals) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                            ON DUPLICATE KEY UPDATE 
                            season = ?, game_id = ?, points = ?, rebounds = ?, assists = ?, blocks = ?, steals = ?");
    
    // Loop through the player stats data
    foreach ($data as $stat) {
        // Get the game ID from the response
        $gameId = $stat['game']['id'] ?? null; // Default to NULL if not available
        $points = isset($stat['points']) ? (int)$stat['points'] : null; // Default to NULL if not available
        $rebounds = isset($stat['totReb']) ? (int)$stat['totReb'] : null; // Extract total rebounds
        $assists = isset($stat['assists']) ? (int)$stat['assists'] : null; // Default to NULL if not available
        $blocks = isset($stat['blocks']) ? (int)$stat['blocks'] : null; // Default to NULL if not available
        $steals = isset($stat['steals']) ? (int)$stat['steals'] : null; // Default to NULL if not available

    
        // Bind parameters
        $stmt->bind_param(
            "isiiiiiisiiiiii",  // Updated parameter types: i = integer, s = string
            $playerId,
            $season,
            $gameId,
            $points,
            $rebounds,
            $assists,
            $blocks,
            $steals,
            $season,
            $gameId,
            $points,
            $rebounds,
            $assists,
            $blocks,
            $steals
        );

    // Execute the statement
    if ($stmt->execute()) {
        echo "Player stats inserted successfully\n";
    } else {
        echo "Error executing statement: " . $stmt->error . "\n";
    }
}
    // Close the statement and connection
    $stmt->close(); // Close the statement
    $conn->close(); // Close the connection
}

    private function processAPITeamsDataRequest($request)
{
    
    // Check if 'data' key exists and is valid
    if (!isset($request['data']) || !isset($request['data']['response'])) {
        echo "Error: Invalid data format.\n";
        return;
    }

    // Access the response directly
    $data = $request['data'];

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

private function processAPIGameDataRequest($request) {

    // Check if 'data' key exists and is valid
    if (!isset($request['data']) || !isset($request['data']['response'])) {
        echo "Error: Invalid data format.\n";
        return;
    }

    // Access the response directly
    $data = $request['data']['response']; // Correctly access the response array
    

    // Create a database connection
    $conn = connectDb(); // Ensure this function is defined to return a valid DB connection
    

    // Assuming $data contains the response from the API
    foreach ($data as $game) {
        // Extract the game details
        $gameId = $game['id'] ?? null;
        $homeTeamId = $game['teams']['home']['id'] ?? null;
        $visitorTeamId = $game['teams']['visitors']['id'] ?? null;

        // Extract the start date
        $date = $game['date']['start'] ?? null; // This will give you the start date in ISO 8601 format
        if ($date) {
            // Convert the ISO 8601 date to MySQL DATETIME format
            $dateTime = date('Y-m-d H:i:s', strtotime($date));
        } else {
            // Handle the case where date is not available
            $dateTime = null; // Or set a default value
        }
        $homeTeamScore = $game['scores']['home']['points'] ?? null;
        $visitorTeamScore = $game['scores']['visitors']['points'] ?? null;

        //Prepare the SQL statement for inserting or updating game data
        $stmt = $conn->prepare("
            INSERT INTO games (game_id, home_team_id, visitor_team_id, game_date, home_team_points, visitor_team_points) 
            VALUES (?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
                home_team_id = VALUES(home_team_id),
                visitor_team_id = VALUES(visitor_team_id),
                game_date = VALUES(game_date),
                home_team_points = VALUES(home_team_points),
                visitor_team_points = VALUES(visitor_team_points)
        ");

        // Bind parameters
        $stmt->bind_param("iiisii", $gameId, $homeTeamId, $visitorTeamId, $dateTime, $homeTeamScore, $visitorTeamScore);

        // Execute the statement
        if ($stmt->execute()) {
            echo "Game inserted successfully\n";
        } else {
            echo "Error executing statement: " . $stmt->error . "\n";
        }
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}

public function getResponse()
    {
        return $this->response;
    }



}