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
            echo "Login successful. Preparing to insert session information.\n";
            // Authentication successful, generate session token
            $token = uniqid();
            $timestamp = time() + (3 * 3600); // Token expiration set to 3 hours

            // Insert session information into the sessions table
            $insertQuery = $db->prepare('INSERT INTO sessions (session_token, timestamp, email, user_id) VALUES (?, ?, ?, ?)');
            if (!$insertQuery) {
                echo "Failed to prepare the insert query: " . $db->error . "\n";
                return;
            }
            $insertQuery->bind_param("sisi", $token, $timestamp, $email, $user_id);

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
            // Session is valid and not expired
            $this->response = [
                'type' => 'SessionValidationResponse',
                'status' => 'success',
                'message' => "Session is valid for email: " . $sessionData['email']
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
