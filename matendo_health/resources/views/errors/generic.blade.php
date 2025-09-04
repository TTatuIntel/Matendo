@extends('layouts.app')

@section('title', 'Error ' . ($status ?? '500'))

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-exclamation-triangle"></i>
                        Error {{ $status ?? '500' }}
                    </h4>
                </div>

                <div class="card-body text-center">
                    <div class="error-icon mb-4">
                        <i class="fas fa-exclamation-triangle text-danger" style="font-size: 4rem;"></i>
                    </div>

                    <h2 class="mb-4">Something went wrong</h2>
                    
                    <p class="lead mb-4">{{ $message ?? 'An unexpected error occurred.' }}</p>
                    
                    <div class="mb-4">
                        <p>We apologize for the inconvenience. Our team has been notified of this issue.</p>
                        @if(config('app.debug'))
                            <div class="alert alert-info mt-3">
                                <strong>Debug Mode:</strong> This error page is showing because debug mode is enabled.
                            </div>
                        @endif
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
                            <a href="{{ route('login') }}" class="btn btn-primary">
                                <i class="fas fa-sign-in-alt"></i> Login
                            </a>
                        @endauth
                        
                        <a href="{{ route('home') }}" class="btn btn-success">
                            <i class="fas fa-home"></i> Home
                        </a>
                    </div>
                </div>
            </div>

            @if(config('app.debug') && isset($exception))
                <div class="card mt-4">
                    <div class="card-header bg-warning">
                        <h5 class="mb-0">Debug Information</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Exception:</strong> {{ get_class($exception) }}</p>
                        <p><strong>File:</strong> {{ $exception->getFile() }}</p>
                        <p><strong>Line:</strong> {{ $exception->getLine() }}</p>
                        <p><strong>Message:</strong> {{ $exception->getMessage() }}</p>
                        <details class="mt-3">
                            <summary>Stack Trace</summary>
                            <pre class="mt-2">{{ $exception->getTraceAsString() }}</pre>
                        </details>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.error-icon {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.1);
    }
    100% {
        transform: scale(1);
    }
}

.gap-3 {
    gap: 1rem;
}
</style>
@endpush
