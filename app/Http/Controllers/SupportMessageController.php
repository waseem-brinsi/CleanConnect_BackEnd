<?php

namespace App\Http\Controllers;

use App\Models\SupportMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupportMessageController extends Controller
{
    /**
     * Store a new support message.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function support_messages(Request $request):JsonResponse
    {
        // Validate input
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'phoneNumber' => 'required|string|max:15',
            'email' => 'required|email|max:255',
            'message' => 'required|string|max:1000',
        ]);

        // Save to database
        $supportMessage = SupportMessage::create($validatedData);

        // Respond with success
        return response()->json([
            'message' => 'Support message successfully created!',
            'data' => $supportMessage,
        ], 201);
    }
}
