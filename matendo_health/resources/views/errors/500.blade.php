@extends('layouts.app')

@section('title', '500 - Internal Server Error')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-server"></i>
                        500 - Internal Server Error
                    </h4>
                </div>

                <div class="card-body text-center">
                    <div class="error-icon mb-4">
                        <i class="fas fa-server text-danger" style="font-size: 4rem;"></i>
                    </div>

                    <h2 class="mb-4">Server Error</h2>
                    
                    <p class="lead mb-4">{{ $message ?? 'Something went wrong on our end. Please try again later.' }}</p>
                    
                    <div class="mb-4">
                        <p>We're experiencing technical difficulties. Our development team has been notified and is working to resolve the issue.</p>
                        <p>Please try again in a few minutes.</p>
                    </div>

                    <div class="d-flex justify-content-center gap-3">
                        <a href="javascript:location.reload()" class="btn btn-primary">
                            <i class="fas fa-redo"></i> Try Again
                        </a>
                        
                        <a href="javascript:history.back()" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Go Back
                        </a>
                        
                        @auth
                            @if(auth()->user()->role === 'admin')
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-success">
                                    <i class="fas fa-home"></i> Admin Dashboard
                                </a>
                            @elseif(auth()->user()->role === 'doctor')
                                <a href="{{ route('doctor.dashboard') }}" class="btn btn-success">
                                    <i class="fas fa-home"></i> Doctor Dashboard
                                </a>
                            @else
                                <a href="{{ route('patient.dashboard') }}" class="btn btn-success">
                                    <i class="fas fa-home"></i> Patient Dashboard
                                </a>
                            @endif
                        @else
                            <a href="{{ route('home') }}" class="btn btn-success">
                                <i class="fas fa-home"></i> Home
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
