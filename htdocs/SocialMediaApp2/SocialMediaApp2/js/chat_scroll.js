

document.addEventListener('DOMContentLoaded', function() {
    const messageList = document.getElementById('messageList');

    if (messageList) {
        // Function to scroll to the bottom
        const scrollToBottom = () => {
            messageList.scrollTop = messageList.scrollHeight;
        };

        // Scroll to bottom when the page loads if there are messages
        scrollToBottom();


    }
});