<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class StatusReportExport implements FromArray, WithHeadings, ShouldAutoSize, WithTitle
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        return [
            ['Pending Applications', $this->data['pending']],
            ['Approved Applications', $this->data['approved']],
            ['Rejected Applications', $this->data['rejected']],
            ['Oldest Pending Application', $this->data['oldestPending']],
            ['Average Review Time (days)', $this->data['avgReviewTime']]
        ];
    }

    public function headings(): array
    {
        return ['Metric', 'Value'];
    }

    public function title(): string
    {
        return 'Application Status';
    }
}
