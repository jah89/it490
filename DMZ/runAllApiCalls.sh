#!/bin/bash

# Get the directory of the current script
cd "$(dirname "$0")"

# Change to the subfolder where your PHP files are located
cd testAPI

echo -n "Running first script..."
/usr/bin/php nbaTeamsEndpoint.php

echo -n "Running second script..."ss
/usr/bin/php nbaGamesEndpoint.php

<<-COMMENT
/usr/bin/php first_api_call_script.php
/usr/bin/php second_api_call_script.php
/usr/bin/php third_api_call_script.php
COMMENT

