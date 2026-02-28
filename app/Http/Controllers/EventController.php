<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\Event;

class EventController extends Controller
{

    public function index() 
    {
        try {
            Gate::authorize('view-events');

            $event = Event::all();

            return response()->json($event, 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function myEvent()
    {
        try {
            $event = Event::where('user_id', auth()->id())->get();

            return response()->json($event, 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            Gate::authorize('create-event');

            $event = Event::create([
                'title' => $request->title,
                'event_date' => $request->event_date,
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'your data stored successfully' => $event,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong',
            ], 500);
        }
        
    }

    public function update(Request $request, Event $event)
    {
        try {
            $this->authorize('update', $event);

            $event->update($request->only('title','event_date'));

            return response()->json([
                'message' => 'Updated',
                'Detail' => $event,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function destroy(Event $event)
    {
        try {
            $this->authorize('delete', $event);

            $event->delete();

            return response()->json([
                'message' => 'Event deleted',
            ]);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }
}
