<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        if ($user->role === 'admin') {
            return \App\Models\Reservation::with(['user', 'room'])->get();
        }
        return \App\Models\Reservation::with(['room'])->where('user_id', $user->id)->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'total_price' => 'required|numeric',
        ]);

        $reservation = \App\Models\Reservation::create([
            'user_id' => $request->user()->id,
            'room_id' => $validated['room_id'],
            'check_in' => $validated['check_in'],
            'check_out' => $validated['check_out'],
            'total_price' => $validated['total_price'],
            'status' => 'pending',
        ]);

        return response()->json($reservation, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return \App\Models\Reservation::with(['user', 'room', 'payment'])->findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $reservation = \App\Models\Reservation::findOrFail($id);
        
        // Admin can update anything, User can only cancel (maybe)
        // For simplicity, let's allow updating status
        $reservation->update($request->only('status'));

        return response()->json($reservation);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        \App\Models\Reservation::destroy($id);

        return response()->json(null, 204);
    }
}
