<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Models\Technician;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TechnicianSupportTicketController extends Controller
{
    private function technicianOrFail(): ?Technician
    {
        return Auth::user()->technicians()->first();
    }

    public function index(Request $request)
    {
        $technician = $this->technicianOrFail();
        $query = SupportTicket::with('client')
            ->where('technician_id', $technician?->id);

        $status = $request->get('status');
        if ($status) {
            $query->where('status', $status);
        }

        $tickets = $query->latest()->paginate(10);

        return view('technician.tickets.index', compact('tickets', 'status') + compact('technician'));
    }

    public function show(SupportTicket $ticket)
    {
        $technician = $this->technicianOrFail();

        if ($ticket->technician_id !== $technician?->id) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $ticket->load(['client', 'technician']);

        return view('technician.tickets.show', compact('ticket', 'technician'));
    }

    public function updateStatus(Request $request, SupportTicket $ticket)
    {
        $technician = $this->technicianOrFail();

        if ($ticket->technician_id !== $technician?->id) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $request->validate([
            'status' => ['required', 'in:open,in_progress,resolved,closed'],
            'troubleshooting_notes' => ['nullable', 'string', 'max:10000'],
        ]);

        $wasResolvedBefore = $ticket->status === 'resolved';

        $ticket->update([
            'status' => $request->status,
            'technician_id' => $technician?->id,
            'troubleshooting_notes' => $request->troubleshooting_notes,
            'assigned_at' => $ticket->assigned_at ?? now(),
            'resolved_at' => $request->status === 'resolved' ? now() : $ticket->resolved_at,
            'closed_at' => $request->status === 'closed' ? now() : $ticket->closed_at,
        ]);

        // If technician marks as resolved, notify client + create admin notification
        if ($request->status === 'resolved' && $ticket->client && !$wasResolvedBefore) {
            try {
                \Mail::to($ticket->client->email)->send(new \App\Mail\ClientTicketResolvedMail($ticket->client, $ticket));
            } catch (\Exception $e) {
                \Log::error('Failed to send ticket resolved mail: ' . $e->getMessage());
            }

            if (class_exists(\App\Models\AdminNotification::class)) {
                // Optional: if AdminNotification supports notify pattern, call it
                // Fallback: create a basic notification row if model is available.
                try {
                    \App\Models\AdminNotification::query()->insert([
                        'type' => 'ticket_resolved',
                        'title' => 'Support ticket resolved',
                        'message' => "Ticket '{$ticket->subject}' was marked resolved by technician.",
                        'data' => json_encode(['ticket_id' => $ticket->id, 'client_id' => $ticket->client_id]),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Failed to create admin notification for ticket resolved: ' . $e->getMessage());
                }
            }
        }

        return redirect()->route('technician.tickets.show', $ticket)->with('success', 'Ticket updated.');
    }

    public function addSolution(Request $request, SupportTicket $ticket)
    {
        $technician = $this->technicianOrFail();

        if ($ticket->technician_id !== $technician?->id) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $request->validate([
            'solution' => ['required', 'string', 'max:10000'],
            'solution_status' => ['nullable', 'in:resolved,in_progress,open'],
        ]);

        $ticket->update([
            'solution' => $request->solution,
            'status' => $request->solution_status ?: $ticket->status,
            'resolved_at' => ($request->solution_status ?? null) === 'resolved' ? now() : $ticket->resolved_at,
        ]);

        return redirect()->route('technician.tickets.show', $ticket)->with('success', 'Solution saved.');
    }

    public function create()
    {
        $technician = $this->technicianOrFail();
        $clients = \App\Models\Client::orderBy('name')->get();

        return view('technician.tickets.create', compact('technician', 'clients'));
    }

    public function store(Request $request)
    {
        $technician = $this->technicianOrFail();

        $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:10000'],
            'priority' => ['required', 'in:low,medium,high'],
        ]);

        $ticket = SupportTicket::create([
            'client_id' => $request->client_id,
            'technician_id' => $technician->id,
            'subject' => $request->subject,
            'message' => $request->message,
            'priority' => $request->priority,
            'status' => 'open',
            'assigned_at' => now(),
        ]);

        return redirect()->route('technician.tickets.show', $ticket)->with('success', 'Ticket created and assigned successfully.');
    }

    public function addReply(Request $request, SupportTicket $ticket)
    {
        $technician = $this->technicianOrFail();

        if ($ticket->technician_id !== $technician?->id) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $request->validate([
            'admin_reply' => ['required', 'string', 'max:10000'],
        ]);

        $ticket->update([
            'admin_reply' => $request->admin_reply,
            'replied_at' => now(),
        ]);

        return redirect()->route('technician.tickets.show', $ticket)->with('success', 'Reply sent to client.');
    }
}

