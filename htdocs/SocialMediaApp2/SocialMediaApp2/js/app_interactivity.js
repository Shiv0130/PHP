

document.addEventListener('DOMContentLoaded', function() {
  

    const notificationLink = document.querySelector('nav a[href="notifications.php"]'); // Assumes you'll add a link
    const notificationBadge = document.createElement('span'); // To show count of unread notifications

    const fetchNotifications = async () => {
        try {
            const response = await fetch('ajax_notifications.php'); // AJAX call to backend
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();

            if (data.error) {
                console.error("Notification error:", data.error);
                return;
            }

            const unreadCount = data.filter(n => !n.read_status).length; // Count unread

            if (notificationLink) {
                // Clear previous badge if exists
                const existingBadge = notificationLink.querySelector('.notification-badge');
                if (existingBadge) {
                    existingBadge.remove();
                }

                if (unreadCount > 0) {
                    // Create and append new badge
                    notificationBadge.textContent = unreadCount;
                    notificationBadge.className = 'notification-badge'; // Add class for styling
                    notificationLink.appendChild(notificationBadge);
                }
            }

  

        } catch (error) {
            console.error("Failed to fetch notifications:", error);
        }
    };

    // Poll for notifications every 60 seconds
    setInterval(fetchNotifications, 60000);

 
    fetchNotifications();
});