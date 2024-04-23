<?php

namespace App\Http\Controllers;

use App\Models\Subcontractor;
use Illuminate\Http\Request;

class SubcontractorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $subcontractors = Subcontractor::all();
        return response()->json($subcontractors, 200); // 200 OK
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string',
            'project_type'  => 'required|string',
            'item_deliver'  => 'required|string',
            'delivery_date' => 'required|date',
            'status'        => 'required|string',
        ]);

        $subcontractor = Subcontractor::create($request->all());

        return response()->json($subcontractor, 201); // 201 Created
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $subcontractor = Subcontractor::find($id);

        if (!$subcontractor) {
            return response()->json(['message' => 'Subcontractor not found'], 404); // 404 Not Found
        }

        return response()->json($subcontractor, 200); // 200 OK
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'          => 'string',
            'project_type'  => 'string',
            'item_deliver'  => 'string',
            'delivery_date' => 'date',
            'status'        => 'string',
        ]);

        $subcontractor = Subcontractor::find($id);

        if (!$subcontractor) {
            return response()->json(['message' => 'Subcontractor not found'], 404); // 404 Not Found
        }

        $subcontractor->update($request->all());

        return response()->json($subcontractor, 200); // 200 OK
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $subcontractor = Subcontractor::find($id);

        if (!$subcontractor) {
            return response()->json(['message' => 'Subcontractor not found'], 404); // 404 Not Found
        }

        $subcontractor->delete();

        return response()->json(['message' => 'Subcontractor deleted'], 200); // 200 OK
    }


    /**
     * Search for a name
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $query = $request->input('query');

        $subcontractors = Subcontractor::where('name', 'LIKE', '%' . $query . '%')->get();

        return response()->json($subcontractors, 200); // 200 OK
    }
}
