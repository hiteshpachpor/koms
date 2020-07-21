<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Supplier::paginate();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate general fields
        $request->validate([
            'name' => ['required', 'unique:\App\Supplier', 'max:255'],
        ]);

        // Create the supplier
        $supplier = Supplier::create($request->all());
        return $supplier;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function show(Supplier $supplier)
    {
        return $supplier;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Supplier $supplier)
    {
        // Check if the name is already taken
        if (
            $request->has('name') &&
            Supplier::where('id', '!=', $supplier->id)
                ->where('name', $request->get('name'))
                ->exists()
        ) {
            abort(
                \Illuminate\Http\Response::HTTP_UNPROCESSABLE_ENTITY,
                "Another supplier with the name '{$request->get(
                    'name'
                )}' already exists."
            );
        }

        $fields = ["name", "description"];

        // Only assign valid fields to the model
        foreach ($fields as $field) {
            if ($request->has($field)) {
                $supplier->{$field} = $request->get($field);
            }
        }

        $supplier->save();
        return $supplier;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function destroy(Supplier $supplier)
    {
        $id = $supplier->id;
        $supplier->delete();
        return ['id' => $id];
    }
}
