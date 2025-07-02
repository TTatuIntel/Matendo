<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class MetricsReportExport implements FromArray, WithHeadings, ShouldAutoSize, WithTitle
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        return [
            ['New Approvals This Month', $this->data['newApproved']],
            ['Average Onboarding Time (days)', $this->data['avgOnboardingTime']],
            ['Common Rejection Reasons', $this->data['commonRejectionReasons']]
        ];
    }

    public function headings(): array
    {
        return ['Metric', 'Value'];
    }

    public function title(): string
    {
        return 'Approval Metrics';
    }
}
