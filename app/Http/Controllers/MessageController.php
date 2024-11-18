<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupMember;
use App\Models\GroupMessage;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    //message 
    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000',
        ]);

        $message = Message::create([
            'sender_id'=>auth()->user()->id,
            'receiver_id'=>$request->receiver_id,
            'message'=>$request->message,
            
        ]);
        return response()->json($message);
    }
    public function getMessages(Request $request)
    {
        $messages = Message::where('receiver_id', auth()->user()->id)
                           ->orWhere('sender_id', auth()->user()->id)
                           ->orderBy('created_at', 'desc')
                           ->get();

        return response()->json($messages);
    }

    public function markAsRead($messageId)
    {
        $message = Message::find($messageId);
        if ($message && $message->receiver_id == auth()->user()->id) {
            $message->update(['is_read' => true]);
        }
        
        return response()->json(['message' => 'Message marked as read']);
    }
    //group
    public function createGroup(Request $request)
    {
        $group = Group::create([
            'name' => $request->name,
        ]);

        // Add the creator as a member
        GroupMember::create([
            'user_id' => auth()->user()->id,
            'group_id' => $group->id,
        ]);

        return response()->json($group);
    }

    public function joinGroup($groupId)
    {
        $group = Group::find($groupId);
        if ($group) {
            GroupMember::create([
                'user_id' => auth()->user()->id,
                'group_id' => $group->id,
            ]);

            return response()->json(['message' => 'Joined the group']);
        }

        return response()->json(['message' => 'Group not found'], 404);
    }

    public function leaveGroup($groupId)
    {
        GroupMember::where('user_id', auth()->user()->id)
                   ->where('group_id', $groupId)
                   ->delete();

        return response()->json(['message' => 'Left the group']);
    }

    public function sendGroupMessage(Request $request, $groupId)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $message = GroupMessage::create([
            'group_id' => $groupId,
            'sender_id' => auth()->user()->id,
            'message' => $request->message,
            'read_by' => json_encode([]), // Empty initially
        ]);

        return response()->json($message);
    }

    public function markGroupMessageAsRead($messageId)
    {
        $message = GroupMessage::find($messageId);
        if ($message && !in_array(auth()->user()->id, $message->read_by)) {
            $readBy = json_decode($message->read_by, true);
            $readBy[] = auth()->user()->id;
            $message->update(['read_by' => json_encode($readBy)]);
        }

        return response()->json(['message' => 'Message marked as read']);
    }
}
