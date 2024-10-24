#!/bin/bash

# Step 1: Start RabbitMQ server if it's not running
if ! systemctl is-active --quiet rabbitmq-server; then
    echo "Starting RabbitMQ server..."
    sudo systemctl start rabbitmq-server
else
    echo "RabbitMQ server is already running."
fi

# Step 2: Run the PHP API consumer script
API_CONSUMER_SCRIPT_PATH="./api_consumer_script.php"  # Update the script name/path as needed

# Run the consumer script in the background, and redirect logs to a file
# Optionally, use 'nohup' to ensure the process keeps running even after you log out
nohup php $API_CONSUMER_SCRIPT_PATH > api_consumer_log.txt 2>&1 &

# Print a message to indicate that the API consumer is running
echo "RabbitMQ API consumer script started. Check 'api_consumer_log.txt' for logs."

# To kill the script use <pkill -f '/path/to/api_consumer_script.php'> in your terminal
