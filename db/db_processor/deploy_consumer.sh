#!/bin/bash

# Step 1: Start RabbitMQ server if it's not running
if ! systemctl is-active --quiet rabbitmq-server; then
    echo "Starting RabbitMQ server..."
    sudo systemctl start rabbitmq-server
else
    echo "RabbitMQ server is already running."
fi

# Step 2: Run the PHP consumer script
CONSUMER_SCRIPT_PATH="./consumer_script.php"

# Run the consumer script in the background, and redirect logs to a file
# Optionally, use 'nohup' to ensure the process keeps running even after you log out
nohup php $CONSUMER_SCRIPT_PATH > consumer_log.txt 2>&1 &

# Print a message to indicate that the consumer is running
echo "RabbitMQ consumer script started. Check 'consumer_log.txt' for logs."
