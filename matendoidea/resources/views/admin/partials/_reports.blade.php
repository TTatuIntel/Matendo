<h2 class="text-xl font-semibold mb-6">Reports & Analytics</h2>
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="font-semibold text-gray-700 mb-3">Monthly Application Trends</h3>
        <p class="text-gray-600 text-sm mb-4">Track the number of applications over the last 12 months.</p>
        <div class="h-40 bg-gray-100 rounded flex items-center justify-center text-gray-400 italic">
            @if(isset($applicationTrendsChart))
                {!! $applicationTrendsChart !!}
            @else
                [Chart Placeholder]
            @endif
        </div>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="font-semibold text-gray-700 mb-3">Facility Request Stats</h3>
        <p class="text-gray-600 text-sm mb-4">Overview of request volumes and response times.</p>
        <div class="h-40 bg-gray-100 rounded flex items-center justify-center text-gray-400 italic">
            @if(isset($facilityRequestsChart))
                {!! $facilityRequestsChart !!}
            @else
                [Chart Placeholder]
            @endif
        </div>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="font-semibold text-gray-700 mb-3">Health Worker Activity</h3>
        <p class="text-gray-600 text-sm mb-4">Engagement and verification status summaries.</p>
        <div class="h-40 bg-gray-100 rounded flex items-center justify-center text-gray-400 italic">
            @if(isset($healthWorkerActivityChart))
                {!! $healthWorkerActivityChart !!}
            @else
                [Chart Placeholder]
            @endif
        </div>
    </div>
</div>