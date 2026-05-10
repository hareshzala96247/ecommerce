<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AttributeController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => Attribute::with('values')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:100']);
        $data['slug'] = Str::slug($data['name']).'-'.Str::random(4);

        $attribute = Attribute::create($data);

        return response()->json(['data' => $attribute->load('values'), 'message' => 'Attribute created.'], 201);
    }

    public function update(Request $request, Attribute $attribute)
    {
        $data = $request->validate(['name' => 'required|string|max:100']);
        $attribute->update($data);

        return response()->json(['data' => $attribute->load('values'), 'message' => 'Attribute updated.']);
    }

    public function destroy(Attribute $attribute)
    {
        $attribute->delete();

        return response()->json(['message' => 'Attribute deleted.']);
    }

    public function addValue(Request $request, Attribute $attribute)
    {
        $data = $request->validate(['value' => 'required|string|max:100']);

        $value = $attribute->values()->create([
            'value' => $data['value'],
            'slug'  => Str::slug($data['value']).'-'.Str::random(4),
        ]);

        return response()->json(['data' => $value, 'message' => 'Value added.'], 201);
    }

    public function removeValue(Attribute $attribute, AttributeValue $value)
    {
        $value->delete();

        return response()->json(['message' => 'Value removed.']);
    }
}
