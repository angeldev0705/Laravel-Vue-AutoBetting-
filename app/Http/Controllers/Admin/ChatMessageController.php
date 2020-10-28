<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Queries\ChatMessageQuery;
use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use Illuminate\Http\Request;

class ChatMessageController extends Controller
{
    public function index(Request $request)
    {
        $query = new ChatMessageQuery(new ChatMessage(), $request);

        $items = $query->getBuilder()
            ->with('room', 'user')
            ->get()
            ->map(function ($account) {
                $account->user->append('two_factor_auth_enabled', 'two_factor_auth_passed', 'is_admin', 'is_bot', 'is_active');
                return $account;
            });

        return [
            'count' => $query->getRowsCount(),
            'items' => $items
        ];
    }

    public function destroy(ChatMessage $message)
    {
        return $message->delete();
    }
}
