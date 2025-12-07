<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DestinationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $destinations = Destination::orderBy('created_at', 'desc')->get();
        return view('admin.destinations.index', compact('destinations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.destinations.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi input (sesuai mobile: title, description, destination, price, duration, departure_date, rating, total_ratings, rundown, image_url)
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'destination' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|string|max:255',
            'departure_date' => 'nullable|date',
            'rating' => 'nullable|numeric|min:0|max:5',
            'total_ratings' => 'nullable|integer|min:0',
            'rundown' => 'nullable|array',
            'rundown.*' => 'string',
            'image_url' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'title.required' => 'Judul destinasi wajib diisi.',
            'destination.required' => 'Lokasi destinasi wajib diisi.',
            'price.required' => 'Harga wajib diisi.',
            'price.numeric' => 'Harga harus berupa angka.',
            'price.min' => 'Harga tidak boleh negatif.',
            'duration.required' => 'Durasi wajib diisi.',
            'departure_date.date' => 'Tanggal keberangkatan harus valid.',
            'rating.numeric' => 'Rating harus berupa angka.',
            'rating.min' => 'Rating minimal 0.',
            'rating.max' => 'Rating maksimal 5.',
            'total_ratings.integer' => 'Total rating harus berupa angka bulat.',
            'image_url.image' => 'File harus berupa gambar.',
            'image_url.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif.',
            'image_url.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Handle upload image
        $imagePath = null;
        if ($request->hasFile('image_url')) {
            $imagePath = $request->file('image_url')->store('destinations', 'public');
        }

        // Buat destination baru (sesuai mobile)
        $destination = Destination::create([
            'title' => $request->title,
            'description' => $request->description,
            'destination' => $request->destination,
            'price' => $request->price,
            'duration' => $request->duration,
            'departure_date' => $request->departure_date,
            'rating' => $request->rating ?? 0,
            'total_ratings' => $request->total_ratings ?? 0,
            'rundown' => $request->rundown ?? [],
            'image_url' => $imagePath,
        ]);

        return redirect()->route('destinations.index')
            ->with('success', 'Destinasi berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $destination = Destination::findOrFail($id);
        return view('admin.destinations.show', compact('destination'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $destination = Destination::findOrFail($id);
        return view('admin.destinations.edit', compact('destination'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $destination = Destination::findOrFail($id);

        // Validasi input (sesuai mobile)
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'destination' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|string|max:255',
            'departure_date' => 'nullable|date',
            'rating' => 'nullable|numeric|min:0|max:5',
            'total_ratings' => 'nullable|integer|min:0',
            'rundown' => 'nullable|array',
            'rundown.*' => 'string',
            'image_url' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'title.required' => 'Judul destinasi wajib diisi.',
            'destination.required' => 'Lokasi destinasi wajib diisi.',
            'price.required' => 'Harga wajib diisi.',
            'price.numeric' => 'Harga harus berupa angka.',
            'price.min' => 'Harga tidak boleh negatif.',
            'duration.required' => 'Durasi wajib diisi.',
            'departure_date.date' => 'Tanggal keberangkatan harus valid.',
            'rating.numeric' => 'Rating harus berupa angka.',
            'rating.min' => 'Rating minimal 0.',
            'rating.max' => 'Rating maksimal 5.',
            'total_ratings.integer' => 'Total rating harus berupa angka bulat.',
            'image_url.image' => 'File harus berupa gambar.',
            'image_url.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif.',
            'image_url.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Handle upload image baru
        $imagePath = $destination->image_url;
        if ($request->hasFile('image_url')) {
            // Hapus image lama jika ada
            if ($destination->image_url) {
                Storage::disk('public')->delete($destination->image_url);
            }
            // Upload image baru
            $imagePath = $request->file('image_url')->store('destinations', 'public');
        }

        // Update destination (sesuai mobile)
        $destination->update([
            'title' => $request->title,
            'description' => $request->description,
            'destination' => $request->destination,
            'price' => $request->price,
            'duration' => $request->duration,
            'departure_date' => $request->departure_date,
            'rating' => $request->rating ?? $destination->rating,
            'total_ratings' => $request->total_ratings ?? $destination->total_ratings,
            'rundown' => $request->rundown ?? $destination->rundown,
            'image_url' => $imagePath,
        ]);

        return redirect()->route('destinations.index')
            ->with('success', 'Destinasi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $destination = Destination::findOrFail($id);

        // Hapus image jika ada
        if ($destination->image_url) {
            Storage::disk('public')->delete($destination->image_url);
        }

        $destination->delete();

        return redirect()->route('destinations.index')
            ->with('success', 'Destinasi berhasil dihapus.');
    }
}
