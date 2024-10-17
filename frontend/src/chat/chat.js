/**
 * JavaScript that handles chat functionality
 */

    function show_func() {
        var element = document.getElementById("chathist");
        element.scrollTop = element.scrollHeight;
    }


    
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

