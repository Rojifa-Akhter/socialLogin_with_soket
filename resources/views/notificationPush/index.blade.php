<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Push Notifications</title>

    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        @csrf
        <div class="card shadow">
            <div class="card-header text-center">
                <h3>Push Notifications</h3>
                <button onclick="askForPermission()" class="btn btn-success mt-3">Enable Notifications</button>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" placeholder="Enter notification title">
                    </div>
                    <div class="col-md-6">
                        <label for="body" class="form-label">Body</label>
                        <input type="text" class="form-control" id="body" name="body" placeholder="Enter notification body">
                    </div>
                </div>
                <div class="text-center mt-4">
                    <button onclick="sendNotification()" class="btn btn-info">Send Notification</button>
                    <p class="mt-2 text-muted">Please enable push notifications before sending.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Script -->
    <script>
        // Register service worker
        navigator.serviceWorker.register("{{ URL::asset('notification.js') }}");

        // Ask for notification permission
        function askForPermission() {
            Notification.requestPermission().then((permission) => {
                if (permission === 'granted') {
                    navigator.serviceWorker.ready.then((sw) => {
                        sw.pushManager.subscribe({
                            userVisibleOnly: true,
                            applicationServerKey: "BEaCcvE1G3eZiUq6MBpWkyoppncxCpm0ELEw17A7z8VaOvjG05RgFaR7sH5nPWgE2Ww6vmcm23aOoV01593iQ6w"
                        }).then((subscription) => {
                            console.log(JSON.stringify(subscription));
                            saveSub(JSON.stringify(subscription));
                        });
                    });
                }
            });
        }

        // Save subscription to server
        function saveSub(sub) {
            // Ensure jQuery is loaded before using it
            if (typeof $ === 'undefined') {
                console.error("jQuery is not loaded. Please check the script order.");
                return;
            }

            $.ajax({
                type: 'post',
                url: '{{ URL('save-push-notification-sub') }}',
                data: {
                    '_token': "{{ csrf_token() }}",
                    'sub': sub
                },
                success: function(data) {
                    console.log(data);
                }
            });
        }

        // Send notification to server
        function sendNotification() {
            if (typeof $ === 'undefined') {
                console.error("jQuery is not loaded. Please check the script order.");
                return;
            }

            $.ajax({
                type: 'post',
                url: '{{ URL('send-push-notification') }}',
                data: {
                    '_token': "{{ csrf_token() }}",
                    'title': $("#title").val(),
                    'body': $("#body").val()
                },
                success: function(data) {
                    alert('Notification Sent Successfully');
                    console.log(data);
                }
            });
        }
    </script>
</body>
</html>
