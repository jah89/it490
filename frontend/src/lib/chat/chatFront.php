<?php
/**
 * server-side chat logic
 * source from https://www.geeksforgeeks.org/online-group-chat-application-using-php/
 */
namespace nba\frontend\src\chat;
use nba\src\lib\SessionHandler;
$session = SessionHandler::getSession();
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

        <!-- Chat History -->
        <div id="chathist" class="inner_div p-4 h-80 overflow-auto bg-cover" style="background-image:url('https://media.geeksforgeeks.org/wp-content/cdn-uploads/20200911064223/bg.jpg');">
            <!-- Chat history will be dynamically inserted here using JavaScript -->
        </div>

        <footer class="bg-purple-600 p-4 flex items-center justify-between">
            <input class="w-2/5 p-2 rounded-md" type="text" id="uname" name="uname" placeholder="From" value="<?php echo htmlspecialchars($session->getEmail(),ENT_QUOTES, 'UTF-8'); ?>" readonly>
            <textarea id="msg" name="msg" rows="2" class="w-1/2 p-2 rounded-md ml-2" placeholder="Type your message"></textarea>
            <button id="sendMessage" class="w-20 p-2 bg-black text-white rounded-md cursor-pointer">Send</button>
        </footer>
    </main> 
</div>
</body>
</html>
