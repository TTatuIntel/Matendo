<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class PerformanceReportExport implements FromArray, WithHeadings, ShouldAutoSize, WithTitle
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        return [
            ['Average Review Time (days)', $this->data['avgReviewTime']],
            ['Average Onboarding Time (days)', $this->data['avgOnboardingTime']],
            ['Approval Rate (%)', $this->data['approvalRate']]
        ];
    }

    public function headings(): array
    {
        return ['Metric', 'Value'];
    }

    public function title(): string
    {
        return 'Performance Metrics';
    }
}
