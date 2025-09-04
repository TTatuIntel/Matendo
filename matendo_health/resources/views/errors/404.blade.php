@extends('errors.generic')

@section('title', '404 - Page Not Found')

@push('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0">
                        <i class="fas fa-search"></i>
                        404 - Page Not Found
                    </h4>
                </div>

                <div class="card-body text-center">
                    <div class="error-icon mb-4">
                        <i class="fas fa-search text-warning" style="font-size: 4rem;"></i>
                    </div>

                    <h2 class="mb-4">Page Not Found</h2>
                    
                    <p class="lead mb-4">{{ $message ?? 'The page you are looking for could not be found.' }}</p>
                    
                    <div class="mb-4">
                        <p>The page you requested might have been moved, deleted, or you might have entered an incorrect URL.</p>
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
        </div>
    </div>
</div>
@endpush
