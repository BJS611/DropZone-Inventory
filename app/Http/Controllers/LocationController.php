<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLocationRequest;
use App\Http\Requests\UpdateLocationRequest;
use App\Models\Location;
use App\Services\AuditService;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function __construct(private AuditService $audit) {}

    public function index(Request $request)
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $locations = Location::query()
            ->withCount('items')
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = $request->string('search')->toString();
                $query->where('name', 'like', '%'.$search.'%')
                    ->orWhere('code', 'like', '%'.$search.'%');
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('locations.index', ['locations' => $locations]);
    }

    public function create()
    {
        return view('locations.create');
    }

    public function store(StoreLocationRequest $request)
    {
        $location = \DB::transaction(function () use ($request): Location {
            $location = Location::create($request->validatedData());

            $this->audit->log(
                'location_create',
                'Location',
                $location->id,
                ['name' => $location->name],
                $request->user(),
            );

            return $location;
        });

        return redirect()->route('locations.index')->with('success', 'Lokasi berhasil ditambahkan.');
    }

    public function edit(Location $location)
    {
        return view('locations.edit', ['location' => $location]);
    }

    public function update(UpdateLocationRequest $request, Location $location)
    {
        \DB::transaction(function () use ($location, $request): void {
            $location->update($request->validatedData());

            $this->audit->log(
                'location_update',
                'Location',
                $location->id,
                ['name' => $location->name],
                $request->user(),
            );
        });

        return redirect()->route('locations.index')->with('success', 'Lokasi berhasil diperbarui.');
    }

    public function destroy(Request $request, Location $location)
    {
        $this->authorize('delete', $location);

        \DB::transaction(function () use ($location, $request): void {
            $this->audit->log(
                'location_delete',
                'Location',
                $location->id,
                ['name' => $location->name],
                $request->user(),
            );

            $location->delete();
        });

        return redirect()->route('locations.index')->with('success', 'Lokasi berhasil dihapus.');
    }
}
