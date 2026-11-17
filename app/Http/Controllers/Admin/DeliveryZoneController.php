<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryZone;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DeliveryZoneController extends Controller
{
    public function index(Request $request)
    {
        $zones = DeliveryZone::query()
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%' . $request->q . '%';
                $q->where(fn ($w) => $w->where('city', 'like', $term)->orWhere('province', 'like', $term));
            })
            ->when($request->filled('status'), fn ($q) => $q->where('is_active', $request->status === 'active'))
            ->orderBy('province')
            ->orderBy('city')
            ->paginate(15)
            ->withQueryString();

        return view('admin.delivery-zones.index', compact('zones'));
    }

    public function create()
    {
        return view('admin.delivery-zones.create', ['zone' => null]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules($request));
        $data['is_active'] = $request->boolean('is_active');

        DeliveryZone::create($data);

        return redirect()->route('admin.delivery-zones.index')->with('success', 'Zona pengiriman ditambahkan.');
    }

    public function edit(DeliveryZone $deliveryZone)
    {
        return view('admin.delivery-zones.edit', ['zone' => $deliveryZone]);
    }

    public function update(Request $request, DeliveryZone $deliveryZone)
    {
        $data = $request->validate($this->rules($request, $deliveryZone->id));
        $data['is_active'] = $request->boolean('is_active');

        $deliveryZone->update($data);

        return redirect()->route('admin.delivery-zones.index')->with('success', 'Zona pengiriman diperbarui.');
    }

    public function toggle(DeliveryZone $deliveryZone)
    {
        $deliveryZone->update(['is_active' => ! $deliveryZone->is_active]);

        return back()->with('success', 'Status zona diperbarui.');
    }

    public function destroy(DeliveryZone $deliveryZone)
    {
        $deliveryZone->delete();

        return redirect()->route('admin.delivery-zones.index')->with('success', 'Zona pengiriman dihapus.');
    }

    private function rules(Request $request, ?int $ignoreId = null): array
    {
        return [
            'province' => ['required', 'string', 'max:100'],
            'city'     => [
                'required', 'string', 'max:100',
                Rule::unique('delivery_zones', 'city')
                    ->where('province', $request->input('province'))
                    ->ignore($ignoreId),
            ],
            'shipping_cost' => ['required', 'numeric', 'min:0', 'max:9999999999'],
        ];
    }
}