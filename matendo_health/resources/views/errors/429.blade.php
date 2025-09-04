@extends('layouts.app')

@section('title', '429 - Too Many Requests')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0">
                        <i class="fas fa-tachometer-alt"></i>
                        429 - Too Many Requests
                    </h4>
                </div>

                <div class="card-body text-center">
                    <div class="error-icon mb-4">
                        <i class="fas fa-tachometer-alt text-warning" style="font-size: 4rem;"></i>
                    </div>

                    <h2 class="mb-4">Slow Down!</h2>
                    
                    <p class="lead mb-4">{{ $message ?? 'You have made too many requests in a short period of time.' }}</p>
                    
                    <div class="mb-4">
                        <p>To protect our servers and ensure fair usage for all users, we have temporarily limited your access.</p>
                        <p><strong>Please wait a few minutes before trying again.</strong></p>
                    </div>

                    <div class="mb-4">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Tips to avoid this error:</strong>
                            <ul class="list-unstyled mt-2 mb-0">
                                <li>• Wait between requests</li>
                                <li>• Avoid refreshing pages rapidly</li>
                                <li>• Use browser back button instead of reloading</li>
                            </ul>
                        </div>
                    </div>

                    <div class="d-flex justify-content-center gap-3">
                        <a href="javascript:history.back()" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Go Back
                        </a>
                        
                        @auth
                            @if(auth()->user()->role === 'admin')
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">
                                    <i class="fas fa-home"></i> Admin Dashboard
                                </a>
                            @elseif(auth()->user()->role === 'doctor')
                                <a href="{{ route('doctor.dashboard') }}" class="btn btn-primary">
                                    <i class="fas fa-home"></i> Doctor Dashboard
                                </a>
                            @else
                                <a href="{{ route('patient.dashboard') }}" class="btn btn-primary">
                                    <i class="fas fa-home"></i> Patient Dashboard
                                </a>
                            @endif
                        @else
                            <a href="{{ route('home') }}" class="btn btn-primary">
                                <i class="fas fa-home"></i> Home
                            </a>
                        @endauth
                    </div>

                    <div class="mt-4">
                        <small class="text-muted">
                            If you continue to experience this issue, please contact support.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Auto-refresh after 60 seconds
setTimeout(function() {
    if (confirm('Would you like to try accessing the page again?')) {
        window.history.back();
    }
}, 60000);
</script>
@endpush
