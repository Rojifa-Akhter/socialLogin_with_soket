self.addEventListener("push", (event) => {
    // Make sure event.data is not null and is in the correct JSON format
    if (event.data) {
        const notification = event.data.json();

        // Check the content of the notification
        console.log("Notification received:", notification);

        event.waitUntil(
            self.registration.showNotification(notification.title, {
                body: notification.body,
                icon: "./img/8.png", // Use the correct path to the icon
                data: {
                    url: notification.url
                }
            })
        );
    }
});

self.addEventListener("notificationclick", (event) => {
    event.notification.close();  // Close the notification after click

    // Open the URL in a new window (or bring focus to an existing tab)
    event.waitUntil(
        clients.openWindow(event.notification.data.url)
    );
});
