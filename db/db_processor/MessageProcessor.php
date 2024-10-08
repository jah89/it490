<?php
/**
 * Class that contains main processor function to call different processors,
 * as well as the processors themselves.
 */
require_once(__DIR__ . '/../connectDB.php');
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
        echo $request;
        $payload = $request['payload'];
        switch ($request['type']) {
            case 'login_request':
                $this->processorLoginRequest($payload);
                break;

            case 'registration_request':
                $this->processorRegistrationRequest($payload);
                break;

            case 'validate_session':
                $this->processorSessionValidation($payload);
                break;

            case 'search_request':
                $this->processorSearchRequest($payload);
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
    private function processorLoginRequest($payload)
    {

        // Connect to the database
        $db = connectDB();

        $email = $payload['email'];
        $hashedPassword = $payload['hashedPassword'];

        // Prepare the SQL statement to check credentials
        $query = $db->prepare('SELECT * FROM users WHERE email = ? AND hashed_password = ? LIMIT 1');
        $query->bind_param("ss", $email, $hashedPassword);
        $query->execute();
        $result = $query->get_result();

        // Check if the user credentials are valid
        if ($result->num_rows > 0) {
            // Authentication successful, generate session token
            $token = uniqid();
            $timestamp = time() + (3 * 3600); // Token expiration set to 3 hours

            // Insert session information into the sessions table
            $insertQuery = $db->prepare('INSERT INTO sessions (session_token, timestamp, email) VALUES (?, ?, ?)');
            $insertQuery->bind_param("sis", $token, $timestamp, $email);

            if ($insertQuery->execute()) {
                // Prepare successful response
                $this->response = [
                    'type' => 'LoginResponse',
                    'status' => 'success',
                    'message' => "Login successful for $email",
                    'session_token' => $token,
                    'expiration_timestamp' => $timestamp
                ];
            } else {
                // Handle insert failure
                $this->response = [
                    'type' => 'LoginResponse',
                    'status' => 'error',
                    'message' => "Login successful, but failed to create session."
                ];
            }
        } else {
            // Invalid credentials
            $this->response = [
                'type' => 'LoginResponse',
                'status' => 'error',
                'message' => "Login failed: Invalid email or password."
            ];
        }

        // Close connection
        $db->close();
    }

    /**
     * Process RegistrationRequest message.
     */
    private function processorRegistrationRequest($payload)
    {
        $email = $payload['email'];
        $hashedPassword = $payload['hashedPassword'];

        // Connect to the database
        $db = connectDB();

        // Check if the email is already registered
        $query = $db->prepare("SELECT email FROM users WHERE email = ?");
        if (!$query) {
            $this->response = [
                'type' => 'RegistrationResponse',
                'status' => 'error',
                'message' => 'Error preparing statement.'
            ];
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
        $this->response = [
            'type' => 'RegistrationResponse',
            'status' => 'failed',
            'message' => 'Email is already registered.'
        ];

        // Close the query and the connection
        $query->close();
        $db->close();
        
        return;
    }

    // Close the select statement
    $query->close();


    // Prepare an insert statement to register the new user
    $insertQuery = $db->prepare("INSERT INTO users (email, hashed_password) VALUES (?, ?)");
    if (!$insertQuery) {
        $this->response = [
            'type' => 'RegistrationResponse',
            'status' => 'error',
            'message' => 'Error preparing insert statement.'
        ];
        return;
    }

    // Bind the parameters (email, hashed password)
    $insertQuery->bind_param("ss", $email, $hashedPassword);

    // Execute the insert statement
    if ($insertQuery->execute()) {
        // Registration successful
        $this->response = [
            'type' => 'RegistrationResponse',
            'status' => 'success',
            'message' => "Registration successful for $email"
        ];
    } else {
        // Registration failed
        $this->response = [
            'type' => 'RegistrationResponse',
            'status' => 'failed',
            'message' => 'Failed to register the user.'
        ];
    }

    // Close the insert statement
    $insertQuery->close();

    // Close the database connection
    $db->close();

    }

    /**
     * Process SessionValidation request.
     */
    private function processorSessionValidation($payload)
    {
       // Retrieve the session token from the payload
        $sessionToken = $payload['session_token'];

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
    private function processorSearchRequest($payload)
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
