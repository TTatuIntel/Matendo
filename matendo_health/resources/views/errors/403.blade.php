@extends('layouts.app')

@section('title', '403 - Access Forbidden')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-ban"></i>
                        403 - Access Forbidden
                    </h4>
                </div>

                <div class="card-body text-center">
                    <div class="error-icon mb-4">
                        <i class="fas fa-ban text-danger" style="font-size: 4rem;"></i>
                    </div>

                    <h2 class="mb-4">Access Denied</h2>
                    
                    <p class="lead mb-4">{{ $message ?? 'You are not authorized to access this resource.' }}</p>
                    
                    <div class="mb-4">
                        <p>You don't have the necessary permissions to view this page. If you believe this is an error, please contact your administrator.</p>
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
