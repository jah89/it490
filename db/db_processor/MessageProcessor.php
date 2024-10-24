<?php
/**
 * Class that contains main processor function to call different processors,
 * as well as the processors themselves.
 */
require_once('/home/enisakil/git/it490/db/connectDB.php');
require_once('/home/enisakil/git/it490/db/get_host_info.inc');


class MessageProcessor
{
    /**
     * Default error response when type information is incorrect.
     *
     * @var array $responseError sent when the request cannot be processed.
     */
    private $responseError;

    /**
     * Default error response when type information is incorrect.
     *
     * @var array $response created by the processor.
     */
    private $response;
    /**
     * Process the message based on its type.  Uses the type in the request array
     * as cases in a switch statement.
     *
     * @param array $request The full decoded request message, consists of type
     *              and nested payload array.
     * @param array $payload The decoded message body.
     */
    public function call_processor($request)
    {
        print_r($request);
        switch ($request['type']) {
            case 'login_request':
                echo("login request received");
                $this->processorLoginRequest($request);
                break;

            case 'register_request':
                $this->processorRegistrationRequest($request);
                break;

            case 'validate_session':
                $this->processorSessionValidation($request);
                break;

            case 'search_request':
                $this->processorSearchRequest($request);
                break;

            case 'chat_message':
                $this->processorChatMessage($request);
                break;

            case 'chat_history':
                $this->processorChatHistory($request);
                break;

            default:
                $this->responseError = ['status' => 'error', 'message' => 'Unknown request type'];
                echo "Unknown request type: {$request['type']}\n";
                break;
        }
    }

    /**
     * Process LoginRequest message.
     */
    private function processorLoginRequest($request)
    {

        // Connect to the database
        echo "Connecting to the database...\n";
        $db = connectDB();
        if ($db === null) {
            $this->response = [
                'type' => 'LoginResponse',
                'status' => 'error',
                'message' => 'Database connection failed.'
            ];
            return;
        }
        echo "Database connection successful.\n";

        echo (print_r($request));
        if(isset($request['email']) && isset($request['password'])) {
            $email = $request['email'];
            $hashedPassword = $request['password'];
            echo "Set Email and Password\n";
        } else {
            echo "Failed to set email and password.\n";
            $this->response = [
                'type' => 'LoginResponse',
                'status' => 'error',
                'message' => 'Missing email or hashedPassword.'
            ];
            return;
        }

        // Prepare the SQL statement to check credentials
        $query = $db->prepare('SELECT * FROM users WHERE email = ? AND hashed_password = ? LIMIT 1');
        if (!$query) {
            echo "Failed to prepare the query: " . $db->error . "\n";
            return;
        }
        $query->bind_param("ss", $email, $hashedPassword);
        $query->execute();
        $result = $query->get_result();
        if (!$result) {
            echo "Query execution failed: " . $db->error . "\n";
            return;
        }
        $num_rows = mysqli_num_rows($result);
        echo "Number of rows found: " . $num_rows . "\n";
        // Check if the user credentials are valid
        if ($num_rows > 0) {
            // Fetch the user_id from the query result
            $userData = $result->fetch_assoc();
            $user_id = $userData['user_id'];  // Fetch the user_id from the users table
            echo "Login successful. User ID: $user_id. Preparing to insert session information.\n";
            
            // Authentication successful, generate session token
            $token = uniqid();
            $timestamp = time() + (6 * 60 * 60);

            // Insert session information into the sessions table
            $insertQuery = $db->prepare('INSERT INTO sessions (session_token, timestamp, email, user_id) VALUES (?, ?, ?, ?)');
            if (!$insertQuery) {
                echo "Failed to prepare the insert query: " . $db->error . "\n";
                return;
            }
            $insertQuery->bind_param("sssi", $token, $timestamp, $email, $user_id);

            // Log the variables for debugging purposes
            echo "Token: $token\nTimestamp: $timestamp\nEmail: $email\nUser ID: $user_id\n";

            if ($insertQuery->execute()) {
                echo "Session information inserted successfully.\n";
                // Prepare successful response
                $this->response = [
                    'type' => 'login_response',
                    'result' => 'true',
                    'message' => "Login successful for $email",
                    'email' => $email,
                    'session_token' => $token,
                    'expiration_timestamp' => $timestamp
                    ]
                ;
            } else {
                echo "Failed to insert session information: " . $db->error . "\n";
                // Handle insert failure
                $this->response = [
                    'type' => 'login_response',
                    'result' => 'false',
                    'message' => "Login successful, but failed to create session."
                    ]
                ;
            }
        } else {
            echo "Login failed: Invalid email or password.\n";
            // Invalid credentials
            $this->response = [
                'type' => 'login_response',
                'status' => 'false',
                'message' => "Login failed: Invalid email or password."
                ]
            ;
        }

        // Close connection
        $db->close();
    }

    /**
     * Process RegistrationRequest message.
     */
    private function processorRegistrationRequest($request)
    {
        echo "Starting registration process...\n";
        $email = $request['email'];
        $hashedPassword = $request['password'];

        // Connect to the database
        echo "Connecting to the database...\n";
        $db = connectDB();
        if ($db === null) {
            echo "Failed to connect to the database.\n";
            return;
        }
        echo "Database connection successful.\n";

        // Check if the email is already registered
        $query = $db->prepare("SELECT email FROM users WHERE email = ?");
        if (!$query) {
            echo "Failed to prepare query: " . $db->error . "\n";
            $this->response = [
                'type' => 'register_response',
                'result' => 'false',
                'message' => 'Error preparing statement.'
                ]
            ;
            return;
        }

        // Bind email parameter
        $query->bind_param("s", $email);

        // Execute the statement
        $query->execute();

        // Bind result variable
        $query->bind_result($existingEmail);

        // If email exists, registration should fail
    if ($query->fetch()) {
        echo "Email is already registered: $email\n";
        $this->response = [
            'type' => 'register_response',
            'result' => 'false',
            'message' => 'Email is already registered.'
            ]
            ;

        // Close the query and the connection
        $query->close();
        $db->close();
        
        return;
    }

    // Close the select statement
    $query->close();
    echo "Email is not registered. Proceeding with registration.\n";


    // Prepare an insert statement to register the new user
    $insertQuery = $db->prepare("INSERT INTO users (email, hashed_password) VALUES (?, ?)");
    if (!$insertQuery) {
        echo "Failed to prepare insert query: " . $db->error . "\n";
        $this->response = [
            'type' => 'register_response',
            'result' => 'false',
            'message' => 'Error preparing insert statement.'
            ]
        ;
        return;
    }

    // Bind the parameters (email, hashed password)
    $insertQuery->bind_param("ss", $email, $hashedPassword);

    // Execute the insert statement
    if ($insertQuery->execute()) {
        echo "User registered successfully.\n";
        // Registration successful
        $this->response = [
            'type' => 'register_response',
            'result' => 'true',
            'message' => "Registration successful for $email"
            ]
        ;
    } else {
        echo "Failed to register the user: " . $db->error . "\n";
        // Registration failed
        $this->response = [
            'type' => 'register_response',
            'result' => 'false',
            'message' => 'Failed to register the user.'
            ]
        ;
    }

    // Close the insert statement
    $insertQuery->close();

    // Close the database connection
    $db->close();

    }

    /**
     * Process SessionValidation request.
     */
    private function processorSessionValidation($request)
    {
       // Retrieve the session token from the payload
        $sessionToken = $request['session_token'];

        // Connect to the database
        $db = connectDB();

        // Prepare a query to check if the session token exists and is valid
        $query = $db->prepare('SELECT * FROM sessions WHERE session_token = ?');
        $query->bind_param("s", $sessionToken);

        // Execute the query
        $query->execute();
        $result = $query->get_result();

        // Check if the session token exists
    if ($result->num_rows > 0) {
        $sessionData = $result->fetch_assoc();
        $currentTimestamp = time();

        // Check if the session is expired
        if ($sessionData['timestamp'] > $currentTimestamp) {
            /*Session is valid and not expired
            Now now return data from db to validate/return session*/
            
            $this->response = [
                'type' => 'SessionValidationResponse',
                'status' => 'success',
                'message' => "Session is valid for email: " . $sessionData['email'],
                'email' => $sessionData['email'],
                'session_token' => $sessionData['token'],
                'expiration_timestamp' => $sessionData['timestamp']
            ];
        } else {
            // Session is expired
            $this->response = [
                'type' => 'SessionValidationResponse',
                'status' => 'error',
                'message' => "Session has expired for token: $sessionToken"
            ];
            }
    } else {
        // Invalid session token
        $this->response = [
            'type' => 'SessionValidationResponse',
            'status' => 'error',
            'message' => "Invalid session token: $sessionToken"
        ];
        }

        // Close the database connection
        $db->close();

    }

    /**
     * Process SearchRequest message.
     */
    private function processorSearchRequest($request)
    {

        // Perform search logic here
    }

    /**
     * Process chat history request
     */
    private function processorChatMessage($request)
    {

        // Check if all required fields are present
        if (empty($request['uname']) || empty($request['msg']) || empty($request['timestamp'])) {
            $this->response = [
                'type' => 'ChatResponse',
                'status' => 'error',
                'message' => 'Missing required fields.'
            ];
            return;
        }

        // Connect to the database
        echo "Connecting to the database...\n";
        $db = connectDB();
        if ($db === null) {
            $this->response = [
                'type' => 'ChatResponse',
                'status' => 'error',
                'message' => 'Database connection failed.'
            ];
            return;
        }
        echo "Database connection successful.\n";

        $username = $request['uname'];
        $message = $request['msg'];
        $timestamp = $request['timestamp'];


        // Prepare and execute insert query
        $insertQuery = $db->prepare('INSERT INTO chat_messages (username, message, timestamp) VALUES (?, ?, ?)');
        if (!$insertQuery) {
            $this->response = [
                'type' => 'ChatResponse',
                'status' => 'error',
                'message' => 'Failed to prepare the insert query.'
            ];
            return;
        }

        $insertQuery->bind_param("sss", $username, $message, $timestamp);
        if ($insertQuery->execute()) {
            $this->response = [
                'type' => 'ChatResponse',
                'status' => 'success',
                'message' => 'Message stored successfully.'
            ];
        } else {
            $this->response = [
                'type' => 'ChatResponse',
                'status' => 'error',
                'message' => 'Failed to store the message.'
            ];
        }
        
        //TODO: write logic to check request fields and query/bind for db 
        //$query = "SELECT username? user id? messages?"

        $insertQuery->close();
        $db->close();
    }

    /**
     * Function to return chat history
     *
     * @param integer $limit the number of messages returned
     * @return mixed $chatHistory an array of usernames:messages from oldest to newest by timestamp
     */
    function processorChatHistory($limit = 10) {
        
        // Connect to the database
        echo "Connecting to the database...\n";
        $db = connectDB();
        if ($db === null) {
            $this->response = [
                'type' => 'ChatHistoryResponse',
                'status' => 'error',
                'message' => 'Database connection failed.'
            ];
            return;
        }
        echo "Database connection successful.\n";
    
        // Fetch the most recent X messages, ordered by timestamp
        $stmt = $db->prepare("SELECT uname, msg, timestamp FROM chat_messages ORDER BY timestamp DESC LIMIT ?");
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        $result = $stmt->get_result();
    
        $chatHistory = [];
        while ($row = $result->fetch_assoc()) {
            $chatHistory[] = $row;  // Add each row to the chat history array
        }
    
        $stmt->close();
        $db->close();

        //we flip the array to make the oldest messages go first.
        return array_reverse($chatHistory);
    }
    

    /**
     * Get the response to send back to the client.
     *
     * @return array The response array.
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Get the response to send back to the client.
     *
     * @return array The response array.
     */
    public function getResponseError()
    {
        return $this->responseError;
    }
}
