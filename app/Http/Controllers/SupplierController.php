<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Http\Response;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $response = [];

        $suppliers = Supplier::get();
        if ($suppliers->isEmpty()) {
            $response = file_get_contents(resource_path('data/suppliers.json'));

        } else {
            $response['data']['suppliers'] = $suppliers;
        }
        return response($response, 200, [
            'Content-Type' => 'application/json'
        ]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        try {
            // Laravel request validate can be added here $request->validate([])
//            $validated = $request->validate([
//                'name' => ['required', 'unique:suppliers,name'],
//                'rules' => ['required'],
//                'url' => ['required', 'url'],
//                'district' => ['required'],
//                'info' => ['required', 'min:4']]);

            $supplier = Supplier::where('name', $request->name)->first();
            if ($supplier) {
                return response("Supplier already exists", 422, [
                    'Content-Type' => 'application/json'
                ]);
            }
            $supplier = Supplier::create($request->only(['name', 'rules', 'url', 'district', 'info']));
            return response($supplier, 204, [
                'Content-Type' => 'application/json'
            ]);
        } catch (Exception $exception) {
            return response($exception->getMessage(), 500, [
                'Content-Type' => 'application/json'
            ]);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param Supplier $supplier
     * @return Response
     */
    public function show(Supplier $supplier)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Supplier $supplier
     * @return Response
     */
    public function edit(Supplier $supplier)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Supplier $supplier
     * @return Response
     */
    public function update(Request $request, Supplier $supplier)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Supplier $supplier
     * @return Response
     */
    public function destroy(Supplier $supplier)
    {
        //
    }
}
