<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CustomerProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $addresses = CustomerAddress::where('user_id', $user->id)->get();
        $orders = Order::with(['items', 'reviews'])->where('user_id', $user->id)->latest()->get();
        $myStoreReviews = \App\Models\StoreReview::where('user_id', $user->id)->latest()->get();

        return view('client.profile', compact('user', 'addresses', 'orders', 'myStoreReviews'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user->name = $request->name;
        $user->phone_number = $request->phone_number;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar && \Storage::disk('public')->exists($user->avatar)) {
                \Storage::disk('public')->delete($user->avatar);
            }
            
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function storeAddress(Request $request)
    {
        $request->validate([
            'label' => 'required|string|max:50',
            'address' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        CustomerAddress::create([
            'user_id' => Auth::id(),
            'label' => $request->label,
            'full_address' => $request->address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'recipient_name' => Auth::user()->name,
            'phone_number' => Auth::user()->phone_number,
            'is_primary' => false,
        ]);

        return back()->with('success', 'Alamat berhasil ditambahkan.');
    }

    public function destroyAddress($id)
    {
        $address = CustomerAddress::where('user_id', Auth::id())->where('id', $id)->firstOrFail();
        $address->delete();

        return back()->with('success', 'Alamat berhasil dihapus.');
    }

    public function updateStoreReview(Request $request, $id)
    {
        $review = \App\Models\StoreReview::where('user_id', Auth::id())->where('id', $id)->firstOrFail();
        
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review_type' => 'required|string',
            'comment' => 'nullable|string'
        ]);

        $review->update([
            'rating' => $request->rating,
            'review_type' => $request->review_type,
            'comment' => $request->comment,
            // Opsional: Anda bisa mereset is_approved ke false jika Anda butuh review ulang oleh admin saat diedit.
            // Di sini kita biarkan statusnya tetap atau bisa ditambahkan logic notifikasi.
        ]);

        return back()->with('success', 'Ulasan restoran Anda berhasil diperbarui.');
    }

    public function destroyStoreReview($id)
    {
        $review = \App\Models\StoreReview::where('user_id', Auth::id())->where('id', $id)->firstOrFail();
        $review->delete();

        return back()->with('success', 'Ulasan restoran Anda berhasil dihapus.');
    }
}
