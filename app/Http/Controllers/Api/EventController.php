<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Event;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreEventRequest;

class EventController extends Controller
{
    /**
     * Store account event
     */
    public function store(StoreEventRequest $request)
    
    {
       

          $validated = $request->validated();
        try {

            $result = DB::transaction(function () use ($validated) {

                // Check duplicate event
                $existingEvent = Event::where('event_id', $validated['id'])
                    ->first();

                if ($existingEvent) {

                    return [
                        'duplicate' => true,
                        'event' => $existingEvent
                    ];
                }


                // Find account or create new account
                // $account = Account::firstOrCreate(
                //     [
                //         'account_id' => $validated['account_id']
                //     ],
                //     [
                //         'balance' => 0
                //     ]
                // );


                // // Update account balance
                // $account->balance += $validated['amount'];
                // $account->save();
                $account = Account::where('account_id', $validated['account_id'])
    ->lockForUpdate()
    ->first();

if (!$account) {

    $account = Account::create([
        'account_id' => $validated['account_id'],
        'balance' => 0
    ]);

}

$account->balance += $validated['amount'];
$account->save();

                // Store event history
                $event = Event::create([
                    'event_id' => $validated['id'],
                    'account_id' => $validated['account_id'],
                    'amount' => $validated['amount'],
                    'occurred_at' => $validated['occurred_at'],
                ]);


                return [
                    'duplicate' => false,
                    'event' => $event,
                    'account' => $account
                ];

            });


            if ($result['duplicate']) {

                return response()->json([
                    'message' => 'Event already processed',
                    'data' => $result['event']
                ], 200);

            }


            return response()->json([
                'message' => 'Event processed successfully',
                'data' => $result
            ], 201);


        } catch (\Exception $e) {

            return response()->json([
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}