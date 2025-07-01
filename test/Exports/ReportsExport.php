<?php

namespace App\Exports;

use App\Models\Application;
use App\Models\FacilityRequest;
use App\Models\IndividualRequest;
use App\Models\Healthworker;
use App\Models\Task;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ReportsExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'Applications' => new class implements FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
                public function collection()
                {
                    return Application::all()->map(function ($application) {
                        return [
                            'Reference Number' => $application->reference_number,
                            'Full Name' => $application->full_name,
                            'Email' => $application->email,
                            'Profession' => $application->profession,
                            'Specialization' => $application->specialization,
                            'Status' => ucfirst($application->status),
                            'Created At' => $application->created_at->format('Y-m-d'),
                        ];
                    });
                }

                public function headings(): array
                {
                    return ['Reference Number', 'Full Name', 'Email', 'Profession', 'Specialization', 'Status', 'Created At'];
                }
            },
            'Facility Requests' => new class implements FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
                public function collection()
                {
                    return FacilityRequest::all()->map(function ($request) {
                        return [
                            'Facility Name' => $request->facility_name,
                            'Contact Person' => $request->contact_person,
                            'Email' => $request->email,
                            'Facility Type' => $request->facility_type,
                            'Status' => ucfirst($request->status),
                            'Submitted At' => $request->submission_date ? $request->submission_date->format('Y-m-d') : 'N/A',
                        ];
                    });
                }

                public function headings(): array
                {
                    return ['Facility Name', 'Contact Person', 'Email', 'Facility Type', 'Status', 'Submitted At'];
                }
            },
            'Individual Requests' => new class implements FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
                public function collection()
                {
                    return IndividualRequest::all()->map(function ($request) {
                        return [
                            'Client Name' => $request->full_name,
                            'Email' => $request->email,
                            'Care Type' => $request->care_type,
                            'Status' => ucfirst($request->status),
                            'Submitted At' => $request->submission_date ? $request->submission_date->format('Y-m-d') : 'N/A',
                        ];
                    });
                }

                public function headings(): array
                {
                    return ['Client Name', 'Email', 'Care Type', 'Status', 'Submitted At'];
                }
            },
            'Health Workers' => new class implements FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
                public function collection()
                {
                    return Healthworker::with('user')->get()->map(function ($healthworker) {
                        return [
                            'Name' => $healthworker->name,
                            'Email' => $healthworker->user->email ?? 'N/A',
                            'Specialty' => $healthworker->specialty,
                            'Status' => ucfirst($healthworker->status),
                            'Verified At' => $healthworker->verified_at ? $healthworker->verified_at->format('Y-m-d') : 'N/A',
                        ];
                    });
                }

                public function headings(): array
                {
                    return ['Name', 'Email', 'Specialty', 'Status', 'Verified At'];
                }
            },
            'Tasks' => new class implements FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
                public function collection()
                {
                    return Task::with('assignedHealthworker')->get()->map(function ($task) {
                        return [
                            'Facility Name' => $task->facility_name,
                            'Positions' => $task->positions,
                            'Status' => ucfirst($task->status),
                            'Priority' => $task->priority ?? 'Medium',
                            'Assigned To' => $task->assignedHealthworker->name ?? 'Unassigned',
                            'Start Date' => $task->start_date ? $task->start_date->format('Y-m-d') : 'N/A',
                        ];
                    });
                }

                public function headings(): array
                {
                    return ['Facility Name', 'Positions', 'Status', 'Priority', 'Assigned To', 'Start Date'];
                }
            },
        ];
    }
}

