<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ClientPortalController extends Controller
{
    public function magicLogin(string $token)
    {
        $client = \App\Models\Client::where('magic_login_token', $token)
            ->where('magic_token_expires_at', '>', now())
            ->first();

        if (!$client || !$client->client_user_id) {
            return redirect('/')->with('error', 'This login link has expired or is invalid. Please log in manually.');
        }

        // Invalidate the token after use
        $client->update(['magic_login_token' => null, 'magic_token_expires_at' => null]);

        // Log the user in
        Auth::loginUsingId($client->client_user_id);

        return redirect()->route('portal.dashboard')->with('success', 'Welcome! You have been logged in successfully.');
    }

    private function client()
    {
        return Auth::user()->clientProfile->load(['subscriptionRate', 'billings', 'payments', 'tickets', 'installationJobs.technician']);
    }

    public function dashboard()
    {
        $client   = $this->client();
        $lastPay  = $client->payments->first();
        $balance  = $client->billings->whereIn('status', ['pending', 'overdue'])->sum('total_amount');
        $overdue  = $client->billings->where('status', 'overdue')->count();
        return view('client.dashboard', compact('client', 'lastPay', 'balance', 'overdue'));
    }

    public function billing()
    {
        $client   = $this->client();
        $billings = $client->billings()->paginate(10);
        return view('client.billing', compact('client', 'billings'));
    }

    public function payments()
    {
        $client   = $this->client();
        $payments = $client->payments()->paginate(10);
        $unpaid   = $client->billings->whereIn('status', ['pending', 'overdue']);
        return view('client.payments', compact('client', 'payments', 'unpaid'));
    }

    public function submitPaymentProof(Request $request)
    {
        $request->validate([
            'billing_id' => ['required', 'exists:billings,id'],
            'proof'      => ['required', 'image', 'max:5120'],
            'method'     => ['required', 'in:gcash,bank_transfer,cash,paymaya,other'],
            'reference'  => ['nullable', 'string', 'max:255'],
        ]);

        $client = Auth::user()->clientProfile;
        $billing = $client->billings()->findOrFail($request->billing_id);

        $path = $request->file('proof')->store('payment-proofs', 'public');

        \App\Models\Payment::create([
            'client_id'         => $client->id,
            'billing_id'        => $billing->id,
            'user_id'           => Auth::id(),
            'receipt_number'    => \App\Models\Payment::generateReceiptNumber(),
            'amount'            => $billing->total_amount,
            'total_paid'        => $billing->total_amount,
            'change_amount'     => 0,
            'payment_method'    => $request->method,
            'payment_reference' => $request->reference,
            'payment_date'      => now()->toDateString(),
            'attachment_path'   => $path,
            'notes'             => 'Payment proof submitted by client — pending admin verification.',
        ]);

        return back()->with('success', 'Payment proof submitted! Admin will verify and update your billing status.');
    }

    public function tickets()
    {
        $client  = $this->client();
        $tickets = $client->tickets()->paginate(10);
        return view('client.tickets', compact('client', 'tickets'));
    }

    public function storeTicket(Request $request)
    {
        $request->validate([
            'subject'  => ['required', 'string', 'max:255'],
            'message'  => ['required', 'string'],
            'priority' => ['required', 'in:low,medium,high'],
        ]);

        $client = Auth::user()->clientProfile;
        SupportTicket::create([
            'client_id' => $client->id,
            'subject'   => $request->subject,
            'message'   => $request->message,
            'priority'  => $request->priority,
        ]);

        return back()->with('success', 'Support ticket submitted successfully!');
    }

    public function profile()
    {
        $client = $this->client();
        return view('client.profile', compact('client'));
    }

    public function updateProfile(Request $request)
    {
        $user   = Auth::user();
        $client = $user->clientProfile;

        $request->validate([
            'name'   => ['required', 'string', 'max:255'],
            'phone'  => ['nullable', 'string', 'max:20'],
            'photo'  => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('photo')) {
            if ($user->photo) Storage::disk('public')->delete($user->photo);
            $user->photo = $request->file('photo')->store('avatars', 'public');
        }
        $user->name = $request->name;
        $user->save();

        if ($request->filled('phone')) {
            $client->update(['phone_number' => $request->phone]);
        }

        return back()->with('success', 'Profile updated successfully.');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'min:8', 'confirmed'],
        ]);

        Auth::user()->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Password changed successfully.');
    }
}
