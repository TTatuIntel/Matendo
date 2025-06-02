<?php

namespace App\Http\Controllers;

use App\Models\FacilityRequest;
use App\Models\IndividualRequest;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    public function index()
    {
        $facilities = FacilityRequest::all();
        $individuals = IndividualRequest::all();
        return view('requests.index', compact('facilities', 'individuals'));
    }

    public function editFacility($id)
    {
        $request = FacilityRequest::findOrFail($id);
        return view('requests.edit', ['type' => 'facility', 'request' => $request]);
    }

    public function updateFacility(Request $request, $id)
    {
        $data = FacilityRequest::findOrFail($id);
        $request->validate(['status' => 'required|in:pending,approved,rejected']);
        $data->status = $request->status;
        $data->save();
        return redirect()->route('requests.index')->with('success', 'Facility request updated.');
    }

    public function editIndividual($id)
    {
        $request = IndividualRequest::findOrFail($id);
        return view('requests.edit', ['type' => 'individual', 'request' => $request]);
    }

    public function updateIndividual(Request $request, $id)
    {
        $data = IndividualRequest::findOrFail($id);
        $request->validate(['status' => 'required|in:pending,approved,rejected']);
        $data->status = $request->status;
        $data->save();
        return redirect()->route('requests.index')->with('success', 'Individual request updated.');
    }
}
