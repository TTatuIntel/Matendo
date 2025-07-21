<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ApplicationsExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data['applications'])->map(function ($app) {
            return [
                'ID' => $app->id,
                'Reference' => $app->reference_number,
                'Name' => $app->first_name . ' ' . $app->last_name,
                'Email' => $app->email,
                'Status' => $app->status,
                'Applied On' => $app->created_at->format('Y-m-d'),
                'Specialization' => $app->specialization
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Reference',
            'Name',
            'Email',
            'Status',
            'Applied On',
            'Specialization'
        ];
    }
}
