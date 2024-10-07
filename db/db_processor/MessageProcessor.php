<?php
/**
 * Class that contains main processor function to call different processors,
 * as well as the processors themselves.
 */
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
    public function call_processor($request ,$payload)
    {
        switch ($request['type']) {
            case 'LoginRequest':
                $this->processorLoginRequest($payload);
                break;

            case 'RegistrationRequest':
                $this->processorRegistrationRequest($payload);
                break;

            case 'SearchRequest':
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
        $email = $payload['email'];
        $hashedPassword = $payload['hashedPassword'];

        // Perform login logic here (e.g., check credentials)
        $loginStatus = "success";  // Assume success for now

        // Prepare the response
        $this->response = [
            'type' => 'LoginResponse',
            'status' => $loginStatus,
            'message' => "Login successful for $email"
        ];
    }

    /**
     * Process RegistrationRequest message.
     */
    private function processorRegistrationRequest($payload)
    {
        $username = $payload['username'];
        $email = $payload['email'];

        // Perform registration logic here

        $this->response = [
            'type' => 'RegistrationResponse',
            'status' => 'success',
            'message' => "Registration successful for $username"
        ];
    }

    /**
     * Process SessionValidation request.
     */
    private function processorSessionValidation($payload)
    {
        $username = $payload['username'];
        $email = $payload['email'];

        // Perform registration logic here

        $this->response = [
            'type' => 'RegistrationResponse',
            'status' => 'success',
            'message' => "Registration successful for $username"
        ];
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
