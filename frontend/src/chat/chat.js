/**
 * JavaScript that handles chat functionality
 */

/* Fetch chat history when the page loads*/
document.addEventListener("DOMContentLoaded", function() {
    fetchChatHistory(); // Load chat history on page load
});

/*Function to fetch chat history from the server*/
function fetchChatHistory() {
    fetch('../api/fetch_chat_history.php')  // Relative path to the API
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();  // Expecting JSON response
        })
        .then(data => {
            const chatHistoryDiv = document.getElementById('chathist');
            chatHistoryDiv.innerHTML = ''; // Clear the chat history before loading new data
            data.forEach(chat => {
                const messageDiv = document.createElement('div');
                
                // Differentiating the display of user messages
                if (chat.uname === sessionUser.userId) {
                    messageDiv.classList.add('bg-blue-400', 'text-white', 'p-2', 'rounded-md', 'mb-2', 'float-right', 'max-w-xs', 'clear-both');
                } else {
                    messageDiv.classList.add('bg-green-400', 'text-white', 'p-2', 'rounded-md', 'mb-2', 'float-left', 'max-w-xs', 'clear-both');
                }

                messageDiv.innerHTML = `
                    <span>${chat.msg}</span><br/>
                    <span class="text-black text-xs">${chat.uname}, ${chat.dt}</span>
                `;
                chatHistoryDiv.appendChild(messageDiv);
            });
        })
        .catch(error => {
            console.error('Error fetching chat history:', error);
        });
}

// Function to send a new message
document.getElementById('sendMessage').addEventListener('click', function() {
    const uname = document.getElementById('uname').value;  // User's name or ID
    const msg = document.getElementById('msg').value;      // Message content

    if (msg.trim() === '') {
        alert('Message cannot be empty!');
        return;  // Do not send empty messages
    }

    const messageData = {
        uname: uname,
        msg: msg,
        timestamp: new Date().toISOString()  // Add a timestamp to the message
    };

    // Send the message via a POST request
    fetch('../api/send_chat_message.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(messageData)  // Send message data as JSON
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();  // Expecting a JSON response
    })
    .then(data => {
        if (data.success) {
            document.getElementById('msg').value = '';  // Clear the message input
            fetchChatHistory();  // Reload chat history after sending a message
        } else {
            console.error('Error sending message:', data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
});
