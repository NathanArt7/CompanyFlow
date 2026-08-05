<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ticket\CloseTicketRequest;
use App\Http\Requests\Ticket\FilterTicketRequest;
use App\Http\Requests\Ticket\StoreTicketRequest;
use App\Models\Ticket;
use Illuminate\Http\Request;
use App\Services\TicketService;
use Illuminate\Http\JsonResponse;

class TicketController extends Controller
{
    public function __construct(
        private TicketService $ticketService
    ) {
    }

    /**
     * Liste des tickets.
     */
    public function index(
        FilterTicketRequest $request
    ): JsonResponse {

        $tickets =
            $this->ticketService
                ->getTickets(
                    $request->validated(),
                    $request->user()
                );

        return response()->json(
            $tickets
        );

    }

    /**
     * Statistiques des tickets.
     */
    public function stats(
        FilterTicketRequest $request
    ): JsonResponse {

        return response()->json([

            'data' => $this->ticketService
                ->getStats(
                    $request->user()
                ),

        ]);

    }

    /**
     * Crée un ticket.
     */
    public function store(
        StoreTicketRequest $request
    ): JsonResponse {

        $ticket =
            $this->ticketService
                ->createTicket(
                    $request->validated(),
                    $request->user()
                );

        return response()->json([

            'message' =>
                'Ticket créé avec succès.',

            'data' =>
                $ticket,

        ], 201);

    }

    /**
     * Prend en charge un ticket.
     */
    public function accept(
        Request $request,
        Ticket $ticket
    ): JsonResponse {

        $ticket =
            $this->ticketService
                ->acceptTicket(
                    $ticket,
                    $request->user()
                );

        return response()->json([

            'message' =>
                'Ticket pris en charge avec succès.',

            'data' =>
                $ticket,

        ]);

    }

    /**
     * Ferme un ticket.
     */
    public function close(
        CloseTicketRequest $request,
        Ticket $ticket
    ): JsonResponse {

        $ticket =
            $this->ticketService
                ->closeTicket(
                    $ticket,
                    $request->validated('equipment_state'),
                    $request->user()
                );

        return response()->json([

            'message' =>
                'Ticket fermé avec succès.',

            'data' =>
                $ticket,

        ]);

    }
}
