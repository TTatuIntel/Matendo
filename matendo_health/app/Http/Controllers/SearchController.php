<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Document;
use App\Models\VitalSign;
use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    public function index()
    {
        return view('search.index');
    }

    public function global(Request $request)
    {
        $query = $request->input('q', $request->input('query', ''));
        
        if (empty($query)) {
            return response()->json([
                'results' => [],
                'message' => 'Please enter a search term'
            ]);
        }

        try {
            $user = Auth::user();
            $results = [];

            // Role-based search
            switch ($user->role) {
                case 'admin':
                    $results = $this->adminSearch($query);
                    break;
                case 'doctor':
                    $results = $this->doctorSearch($query, $user);
                    break;
                case 'patient':
                    $results = $this->patientSearch($query, $user);
                    break;
            }

            return response()->json([
                'success' => true,
                'results' => $results,
                'query' => $query
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Search failed. Please try again.',
                'results' => []
            ], 500);
        }
    }

    public function suggestions(Request $request)
    {
        $query = $request->input('q', $request->input('query', ''));
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $user = Auth::user();
        $suggestions = [];

        try {
            if ($user->role === 'admin') {
                $suggestions = User::where('name', 'LIKE', "{$query}%")->limit(5)->pluck('name')->toArray();
            } elseif ($user->role === 'doctor') {
                $doctor = $user->doctor;
                if ($doctor) {
                    $suggestions = Patient::whereHas('doctorPatients', function($q) use ($doctor) {
                        $q->where('doctor_id', $doctor->id);
                    })->whereHas('user', function($q) use ($query) {
                        $q->where('name', 'LIKE', "{$query}%");
                    })->with('user')->limit(5)->get()->pluck('user.name')->toArray();
                }
            } elseif ($user->role === 'patient') {
                $patient = $user->patient;
                if ($patient) {
                    $suggestions = Document::where('patient_id', $patient->id)
                        ->where('title', 'LIKE', "{$query}%")
                        ->limit(5)->pluck('title')->toArray();
                }
            }
        } catch (\Exception $e) {
            // Return empty suggestions on error
        }

        return response()->json($suggestions);
    }

    public function filterOptions()
    {
        $user = Auth::user();
        $options = [];

        switch ($user->role) {
            case 'admin':
                $options = [
                    'types' => ['users', 'patients', 'doctors', 'documents'],
                    'statuses' => ['active', 'inactive']
                ];
                break;
            case 'doctor':
                $options = [
                    'types' => ['patients', 'documents', 'appointments'],
                    'date_ranges' => ['today', 'week', 'month']
                ];
                break;
            case 'patient':
                $options = [
                    'types' => ['documents', 'appointments', 'medications'],
                    'date_ranges' => ['today', 'week', 'month']
                ];
                break;
        }

        return response()->json($options);
    }

    public function recentSearches()
    {
        return response()->json([
            'recent' => []
        ]);
    }

    public function analytics()
    {
        $user = Auth::user();
        
        if ($user->role !== 'admin') {
            return response()->json([], 403);
        }

        return response()->json([
            'popular_searches' => [],
            'search_trends' => [],
            'total_searches' => 0
        ]);
    }

    public function clearCache()
    {
        $user = Auth::user();
        
        if ($user->role !== 'admin') {
            return response()->json(['success' => false], 403);
        }

        return response()->json(['success' => true]);
    }

    public function exportResults(Request $request)
    {
        $query = $request->input('query', '');
        $format = $request->input('format', 'json');
        
        if ($format === 'json') {
            return response()->json(['exported' => true]);
        }
        
        return response()->json(['error' => 'Unsupported format']);
    }

    // Private helper methods
    private function adminSearch($query)
    {
        return [
            'users' => User::where('name', 'LIKE', "%{$query}%")
                          ->orWhere('email', 'LIKE', "%{$query}%")
                          ->limit(10)->get(),
            'patients' => Patient::whereHas('user', function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%");
            })->with('user')->limit(10)->get(),
            'doctors' => Doctor::whereHas('user', function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%");
            })->with('user')->limit(10)->get()
        ];
    }

    private function doctorSearch($query, $user)
    {
        $doctor = $user->doctor;
        if (!$doctor) return [];

        return [
            'patients' => Patient::whereHas('doctorPatients', function($q) use ($doctor) {
                $q->where('doctor_id', $doctor->id);
            })->whereHas('user', function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%");
            })->with('user')->limit(10)->get(),
            'documents' => Document::whereHas('patient.doctorPatients', function($q) use ($doctor) {
                $q->where('doctor_id', $doctor->id);
            })->where('title', 'LIKE', "%{$query}%")->limit(10)->get()
        ];
    }

    private function patientSearch($query, $user)
    {
        $patient = $user->patient;
        if (!$patient) return [];

        return [
            'documents' => Document::where('patient_id', $patient->id)
                ->where('title', 'LIKE', "%{$query}%")
                ->limit(10)->get(),
            'appointments' => Appointment::where('patient_id', $patient->id)
                ->where('type', 'LIKE', "%{$query}%")
                ->with('doctor.user')->limit(10)->get()
        ];
    }
}
