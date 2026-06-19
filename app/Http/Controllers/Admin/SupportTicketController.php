<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\Technician;
use App\Models\Client;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    public function index(Request $request)
    {
        $query = SupportTicket::with('client', 'technician')->latest();

        // Filter by status
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        // Filter by priority
        if ($priority = $request->get('priority')) {
            $query->where('priority', $priority);
        }

        // Filter by technician
        if ($technician_id = $request->get('technician_id')) {
            $query->where('technician_id', $technician_id);
        }

        // Search by client name or ticket subject
        if ($search = $request->get('search')) {
            $query->whereHas('client', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhere('subject', 'like', "%{$search}%");
        }

        $tickets = $query->paginate(15);
        $technicians = Technician::orderBy('name')->get();

        // Statistics
        $stats = [
            'total' => SupportTicket::count(),
            'open' => SupportTicket::where('status', 'open')->count(),
            'in_progress' => SupportTicket::where('status', 'in_progress')->count(),
            'resolved' => SupportTicket::where('status', 'resolved')->count(),
            'closed' => SupportTicket::where('status', 'closed')->count(),
            'unassigned' => SupportTicket::whereNull('technician_id')->count(),
        ];

        return view('admin.support-tickets.index', compact('tickets', 'technicians', 'stats', 'status', 'priority'));
    }

    public function show(SupportTicket $ticket)
    {
        $ticket->load('client', 'technician');
        $technicians = Technician::orderBy('name')->get();

        return view('admin.support-tickets.show', compact('ticket', 'technicians'));
    }

    public function assignTechnician(Request $request, SupportTicket $ticket)
    {
        $request->validate([
            'technician_id' => ['required', 'exists:technicians,id'],
        ]);

        $ticket->update([
            'technician_id' => $request->technician_id,
            'assigned_at' => now(),
            'status' => 'open',
        ]);

        return redirect()->route('admin.support-tickets.show', $ticket)->with('success', 'Ticket assigned to technician.');
    }

    public function updateStatus(Request $request, SupportTicket $ticket)
    {
        $request->validate([
            'status' => ['required', 'in:open,in_progress,resolved,closed'],
        ]);

        $wasResolvedBefore = $ticket->status === 'resolved';

        $ticket->update([
            'status' => $request->status,
            'resolved_at' => $request->status === 'resolved' ? ($ticket->resolved_at ?? now()) : $ticket->resolved_at,
            'closed_at' => $request->status === 'closed' ? now() : $ticket->closed_at,
        ]);

        // When admin marks it resolved/closed, notify client (optional email already exists for technician resolved)
        // You requested: if technician marks solve/complete, admin should show completed.
        // So here we just ensure resolved/closed are reflected.
        if ($request->status === 'resolved' && !$wasResolvedBefore && $ticket->client) {
            // Admin can reuse the same mail as technician resolved.
            try {
                \Mail::to($ticket->client->email)->send(new \App\Mail\ClientTicketResolvedMail($ticket->client, $ticket));
            } catch (\Exception $e) {
                \Log::error('Failed to send admin resolved ticket mail: ' . $e->getMessage());
            }
        }

        return redirect()->route('admin.support-tickets.show', $ticket)->with('success', 'Ticket status updated.');
    }

    public function report(Request $request)
    {
        $startDate = $request->get('start_date') ? now()->parse($request->get('start_date')) : now()->subDays(30);
        $endDate = $request->get('end_date') ? now()->parse($request->get('end_date')) : now();

        $tickets = SupportTicket::whereBetween('created_at', [$startDate, $endDate])
            ->with('client', 'technician')
            ->latest()
            ->get();

        // Statistics by status
        $statusStats = [
            'open' => SupportTicket::where('status', 'open')->whereBetween('created_at', [$startDate, $endDate])->count(),
            'in_progress' => SupportTicket::where('status', 'in_progress')->whereBetween('created_at', [$startDate, $endDate])->count(),
            'resolved' => SupportTicket::where('status', 'resolved')->whereBetween('created_at', [$startDate, $endDate])->count(),
            'closed' => SupportTicket::where('status', 'closed')->whereBetween('created_at', [$startDate, $endDate])->count(),
        ];

        // Statistics by priority
        $priorityStats = [
            'low' => SupportTicket::where('priority', 'low')->whereBetween('created_at', [$startDate, $endDate])->count(),
            'medium' => SupportTicket::where('priority', 'medium')->whereBetween('created_at', [$startDate, $endDate])->count(),
            'high' => SupportTicket::where('priority', 'high')->whereBetween('created_at', [$startDate, $endDate])->count(),
        ];

        // Average resolution time
        $avgResolutionTime = SupportTicket::whereNotNull('resolved_at')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_hours')
            ->first();

        // Top technicians
        $topTechnicians = SupportTicket::whereNotNull('technician_id')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('technician_id')
            ->selectRaw('technician_id, COUNT(*) as ticket_count, COUNT(CASE WHEN status = "closed" THEN 1 END) as closed_count')
            ->with('technician')
            ->orderByRaw('COUNT(*) DESC')
            ->limit(5)
            ->get();

        return view('admin.support-tickets.report', compact(
            'tickets', 'statusStats', 'priorityStats', 'avgResolutionTime',
            'topTechnicians', 'startDate', 'endDate'
        ));
    }
}
