<?php

namespace App\Http\Controllers;

use App\Models\IndividualRequest;
use Illuminate\Http\Request;

class IndividualRequestController extends Controller
{
    public function index()
    {
        $individualRequests = IndividualRequest::paginate(10);
        return view('dashboard', compact('individualRequests'));
    }

    public function updateStatus(Request $request, $id)
    {
        $requestData = IndividualRequest::findOrFail($id);
        $requestData->status = $request->status;
        $requestData->save();

        return redirect()->back()->with('success', 'Individual request status updated successfully.');
    }
}
