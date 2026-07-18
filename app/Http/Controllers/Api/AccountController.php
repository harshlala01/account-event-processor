<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Event;

class AccountController extends Controller
{
    /**
     * Get current account balance
     */
    public function balance($account_id)
    {
        $account = Account::where('account_id', $account_id)->first();

        if (!$account) {
            return response()->json([
                'success' => false,
                'message' => 'Account not found.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'account_id' => $account->account_id,
            'balance' => $account->balance
        ], 200);
    }

    /**
     * Get account event history
     */
    public function events($account_id)
    {
        $account = Account::where('account_id', $account_id)->first();

        if (!$account) {
            return response()->json([
                'success' => false,
                'message' => 'Account not found.'
            ], 404);
        }

        $events = Event::where('account_id', $account_id)
            ->orderBy('occurred_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'account_id' => $account->account_id,
            'current_balance' => $account->balance,
            'total_events' => $events->count(),
            'events' => $events
        ], 200);
    }
}