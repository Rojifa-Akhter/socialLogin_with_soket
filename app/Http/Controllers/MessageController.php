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
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        // Handle image upload 
        $imagePaths = [];
        if ($request->has('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('img', 'public');
                $imagePaths[] = asset('storage/' . $path);
            }
        }

        // Create the message
        $message = Message::create([
            'sender_id' => auth()->user()->id,
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
            'images' => json_encode($imagePaths),
        ]);

        // Lazy load sender and receiver after creating the message

        $receiver = $message->receiver;

        return response()->json([
            'message' => $message,
            'receiver' => $receiver
        ]);
    }
    
    public function getMessages(Request $request)
    {
        // Eager load the sender and receiver relationships
        $messages = Message::with(['sender'])
            ->where(function ($query) {
                $query->where('receiver_id', auth()->user()->id)
                    ->orWhere('sender_id', auth()->user()->id);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Automatically mark messages 
        $messages->each(function ($message) {
            if ($message->receiver_id == auth()->user()->id && !$message->is_read) {
                $message->update(['is_read' => true]);
            }
        });

        // Decode the image URLs
        $messages->each(function ($message) {
            $message->images = json_decode($message->images);
        });

        return response()->json($messages);
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
            'content' => 'required|string|max:1000',
        ]);

        // member of the group
        $isMember = GroupMember::where('group_id', $groupId)
            ->where('user_id', auth()->user()->id)
            ->exists();

        if (!$isMember) {
            return response()->json(['message' => 'You are not a member of this group.'], 403);
        }

        // Create the group message
        $message = GroupMessage::create([
            'group_id' => $groupId,
            'sender_id' => auth()->user()->id,
            'content' => $request->content,
            'read_by' => json_encode([]),
            'read_count' => 0,
        ]);

        return response()->json($message);
    }
    public function getGroupMessages($groupId)
    {
        $isMember = GroupMember::where('group_id', $groupId)
            ->where('user_id', auth()->user()->id)
            ->exists();

        if (!$isMember) {
            return response()->json(['message' => 'You are not a member of this group.'], 403);
        }

        $messages = GroupMessage::where('group_id', $groupId)->get();

        $messages->each(function ($message) {
            $readBy = json_decode($message->read_by, true) ?: [];
            if (!in_array(auth()->user()->id, $readBy)) {
                $readBy[] = auth()->user()->id;
                $message->update([
                    'read_by' => json_encode($readBy),
                    'read_count' => count($readBy),
                ]);
            }
        });

        return response()->json($messages);
    }
}
