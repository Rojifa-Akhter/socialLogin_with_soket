<?php

namespace App\Http\Controllers;

use App\Models\PushNotification;
use Illuminate\Http\Request;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class PushNotificationController extends Controller
{
    public function sendNotification(Request $request)
    {
        // Ensure title and body are provided
        if (!$request->title || !$request->body) {
            return response()->json(['error' => 'Title and Body are required'], 400);
        }
    
        // VAPID Authentication keys
        $auth = [
            'VAPID' => [
                'subject' => 'https://yourwebsite.com/', // Your website or mailto: URL
                'publicKey' => env('PUBLIC_KEY'), // Public key (Base64-URL)
                'privateKey' => env('PRIVATE_KEY'), // Private key (Base64-URL)
            ],
        ];
    
        // Initialize WebPush with VAPID keys
        $webPush = new WebPush($auth);
    
        // Construct payload for push notifications
        $payload = json_encode([
            'title' => $request->title,
            'body' => $request->body,
            'url' => './', // Optional: modify if needed
        ]);
    
        // Get all subscriptions
        $notifications = PushNotification::all();
    
        // If no subscriptions found, return an error message
        if ($notifications->isEmpty()) {
            return response()->json(['error' => 'No subscriptions found'], 404);
        }
    
        // Send the push notification to all subscribers
        foreach ($notifications as $notification) {
            try {
                // Skip json_decode if subscriptions are already an array
                $subscriptionData = $notification->subscriptions;
    
                if (is_array($subscriptionData)) {
                    // Create the subscription object
                    $subscription = Subscription::create($subscriptionData);
    
                    // Send notification
                    $webPush->queueNotification(
                        $subscription,
                        $payload,
                        ['TTL' => 5000] // Time to live
                    );
                } else {
                    return response()->json(['error' => 'Invalid subscription data format'], 400);
                }
    
            } catch (\Exception $e) {
                // Handle subscription sending errors
                return response()->json(['error' => 'Failed to send notification', 'message' => $e->getMessage()], 500);
            }
        }
    
        return response()->json(['message' => 'Notification sent successfully'], 200);
    }
    

    public function saveSubscription(Request $request)
    {
        // Ensure subscription data is provided
        if (!$request->sub) {
            return response()->json(['error' => 'Subscription data is required'], 400);
        }

        // Create new subscription record
        $subscription = new PushNotification();
        $subscription->subscriptions = json_decode($request->sub);
        $subscription->save();

        return response()->json(['message' => 'Subscription saved successfully'], 200);
    }
}
