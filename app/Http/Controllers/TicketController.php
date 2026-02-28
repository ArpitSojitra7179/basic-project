<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\Event;
use App\Models\Ticket;

class TicketController extends Controller
{
    public function index()
    {
        Gate::authorize('view-tickets');

        $tickets = Ticket::all();

        return response()->json($tickets, 200);
    }

    public function store(Event $event) 
    {
        Gate::authorize('book-ticket');

        $ticket = Ticket::create([
            'event_id' => $event->id,
            'user_id' => auth()->id(),
        ]);

        return response()->json($ticket, 201);
    }

    public function show(Ticket $ticket)
    {
        $this->authorize('view', $ticket);

        return response()->json($ticket);
    }
}
