<?php
/* Based on code from https://www.geeksforgeeks.org/online-group-chat-application-using-php/ */
namespace nba\frontend\src\chat;
require_once __DIR__ . '/vendor/autoload.php'; // Assuming you're using composer for php-amqplib
?>


<html>
<head>

</head>
<body class="bg-blue-200 font-sans">
<div id="container" class="w-96 mx-auto bg-white rounded-lg overflow-hidden shadow-lg mt-8">
    <main>
        <header class="flex justify-between items-center bg-purple-600 p-4 text-white">
            <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/1940306/ico_star.png" alt="" class="w-6">
            <h2 class="text-xl font-bold">GROUP CHAT</h2>
            <img src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/1940306/ico_star.png" alt="" class="w-6">
        </header>

        <script>
        function show_func() {
            var element = document.getElementById("chathist");
            element.scrollTop = element.scrollHeight;
        }
        </script>

        <form id="myform" action="Group_chat.php" method="POST">
            <div class="inner_div p-4 h-80 overflow-auto bg-cover" id="chathist" style="background-image:url('https://media.geeksforgeeks.org/wp-content/cdn-uploads/20200911064223/bg.jpg');">
                <?php 
                //TODO: adjust below logic to only use response from backend
                // $host = "localhost"; 
                // $user = "root"; 
                // $pass = ""; 
                // $db_name = "chat_app"; 
                // $con = new mysqli($host, $user, $pass, $db_name);

                // $query = "SELECT * FROM chats";
                // $run = $con->query($query); 
                // $i=0;

                // while($row = $run->fetch_array()) : 
                // if($i==0){
                // $i=5;
                // $first=$row;
                    
                ?>
                <div class="triangle1 float-right"></div>
                <div class="bg-blue-400 text-white p-2 rounded-md mb-2 float-right max-w-xs clear-both"> 
                    <span><?php echo $row['msg']; ?></span> <br/>
                    <span class="text-black text-xs"><?php echo $row['uname']; ?>, <?php echo $row['dt']; ?></span>
                </div>
                <br/><br/>
                <?php
                }
                else
                {
                if($row['uname']!=$first['uname'])
                {
                ?>
                <div class="triangle float-left"></div>
                <div class="bg-green-400 text-white p-2 rounded-md mb-2 float-left max-w-xs clear-both"> 
                    <span><?php echo $row['msg']; ?></span> <br/>
                    <span class="text-black text-xs float-right"><?php echo $row['uname']; ?>, <?php echo $row['dt']; ?></span>
                </div>
                <br/><br/>
                <?php
                } 
                else
                {
                ?>
                <div class="triangle1 float-right"></div>
                <div class="bg-blue-400 text-white p-2 rounded-md mb-2 float-right max-w-xs clear-both"> 
                    <span><?php echo $row['msg']; ?></span> <br/>
                    <span class="text-black text-xs"><?php echo $row['uname']; ?>, <?php echo $row['dt']; ?></span>
                </div>
                <br/><br/>
                <?php
                }
                }
                endwhile;
                ?>
            </div>
            <footer class="bg-purple-600 p-4 flex items-center justify-between">
                <input class="w-2/5 p-2 rounded-md" type="text" id="uname" name="uname" placeholder="From">
                <textarea id="msg" name="msg" rows="2" class="w-1/2 p-2 rounded-md ml-2" placeholder="Type your message"></textarea>
                <input class="w-20 p-2 bg-black text-white rounded-md cursor-pointer" type="submit" id="submit" name="submit" value="Send">
            </footer>
        </form>
    </main> 
</div>

</body>
</html>



<script>
    document.getElementById('myform').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent the form from submitting normally

    const uname = document.getElementById('uname').value;
    const msg = document.getElementById('msg').value;

    // Send the message to the server via AJAX
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'front_chat.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            document.getElementById('msg').value = ''; // Clear the message input field
            loadMessages(); // Refresh the chat history
        }
    };
    xhr.send('uname=' + encodeURIComponent(uname) + '&msg=' + encodeURIComponent(msg));
});

function loadChatHistory() {
    const xhr = new XMLHttpRequest();
    xhr.open('GET', 'fetch_chat_history.php', true); // Send request to PHP script to fetch history via rabbit
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            const chatHistory = JSON.parse(xhr.responseText);
            displayChatHistory(chatHistory); // Call function to publish chat messages to HTML
        }
    };
    xhr.send();
}

function displayChatHistory(chatHistory) {
    const chatHistElement = document.getElementById("chathist");
    chatHistElement.innerHTML = ''; // Clear existing messages

    chatHistory.forEach(message => {
        const msgElement = document.createElement('div');
        msgElement.className = 'message'; // Tailwind for styling

        msgElement.innerHTML = `
            <strong>${message.uname}:</strong> ${message.msg}<br/>
            <small>${message.timestamp}</small>
        `;
        chatHistElement.appendChild(msgElement);
    });

    chatHistElement.scrollTop = chatHistElement.scrollHeight; // Auto-scroll to the bottom
}

// Calls function every few seconds to update the chat
setInterval(loadMessages, 3000); // Fetch new messages every 3 seconds

</script>