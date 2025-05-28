<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Alpine.js -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Global Styles -->
        <style>
            :root {
                /* Base colors */
                --color-primary: 79, 70, 229; /* indigo-600 */
                --color-primary-light: 129, 140, 248; /* indigo-400 */
                --color-primary-dark: 67, 56, 202; /* indigo-700 */

                --color-secondary: 79, 70, 229; /* indigo-600 - can be changed to a different color */
                --color-secondary-light: 129, 140, 248; /* indigo-400 */
                --color-secondary-dark: 67, 56, 202; /* indigo-700 */

                /* Status colors */
                --color-success: 16, 185, 129; /* green-500 */
                --color-success-light: 209, 250, 229; /* green-100 */
                --color-success-dark: 6, 95, 70; /* green-800 */

                --color-warning: 245, 158, 11; /* amber-500 */
                --color-warning-light: 254, 243, 199; /* amber-100 */
                --color-warning-dark: 146, 64, 14; /* amber-800 */

                --color-error: 239, 68, 68; /* red-500 */
                --color-error-light: 254, 226, 226; /* red-100 */
                --color-error-dark: 185, 28, 28; /* red-800 */

                --color-info: 59, 130, 246; /* blue-500 */
                --color-info-light: 219, 234, 254; /* blue-100 */
                --color-info-dark: 30, 64, 175; /* blue-800 */

                /* Neutral colors */
                --color-gray-50: 249, 250, 251;
                --color-gray-100: 243, 244, 246;
                --color-gray-200: 229, 231, 235;
                --color-gray-300: 209, 213, 219;
                --color-gray-400: 156, 163, 175;
                --color-gray-500: 107, 114, 128;
                --color-gray-600: 75, 85, 99;
                --color-gray-700: 55, 65, 81;
                --color-gray-800: 31, 41, 55;
                --color-gray-900: 17, 24, 39;

                /* UI element colors */
                --color-background: 255, 255, 255;
                --color-foreground: 17, 24, 39;
                --color-muted: 107, 114, 128;
                --color-border: 229, 231, 235;
                --color-ring: 79, 70, 229;

                /* Shadows */
                --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
                --shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
                --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
                --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);

                /* Transitions */
                --transition-fast: 150ms cubic-bezier(0.4, 0, 0.2, 1);
                --transition-normal: 200ms cubic-bezier(0.4, 0, 0.2, 1);
                --transition-slow: 300ms cubic-bezier(0.4, 0, 0.2, 1);

                /* Border radius */
                --radius-sm: 0.125rem;
                --radius: 0.25rem;
                --radius-md: 0.375rem;
                --radius-lg: 0.5rem;
                --radius-xl: 0.75rem;
                --radius-2xl: 1rem;
                --radius-full: 9999px;
            }

            .dark {
                /* Dark mode overrides */
                --color-background: 17, 24, 39;
                --color-foreground: 255, 255, 255;
                --color-muted: 156, 163, 175;
                --color-border: 55, 65, 81;

                /* Status colors - dark mode */
                --color-success-light: 6, 95, 70;
                --color-success-dark: 209, 250, 229;

                --color-warning-light: 146, 64, 14;
                --color-warning-dark: 254, 243, 199;

                --color-error-light: 185, 28, 28;
                --color-error-dark: 254, 226, 226;

                --color-info-light: 30, 64, 175;
                --color-info-dark: 219, 234, 254;
            }

            /* Global styles */
            html {
                scroll-behavior: smooth;
            }

            body {
                font-family: 'Figtree', sans-serif;
                color: rgb(var(--color-foreground));
                background-color: rgb(var(--color-gray-100));
                transition: background-color var(--transition-normal);
            }

            .dark body {
                background-color: rgb(var(--color-gray-900));
            }

            /* Layout components */
            .app-container {
                min-height: 100vh;
                display: flex;
                flex-direction: column;
            }

            .content-container {
                max-width: 80rem;
                margin-left: auto;
                margin-right: auto;
                padding-left: 1rem;
                padding-right: 1rem;
            }

            @media (min-width: 640px) {
                .content-container {
                    padding-left: 1.5rem;
                    padding-right: 1.5rem;
                }
            }

            @media (min-width: 1024px) {
                .content-container {
                    padding-left: 2rem;
                    padding-right: 2rem;
                }
            }

            /* Typography */
            .heading-1 {
                font-size: 2.25rem;
                line-height: 2.5rem;
                font-weight: 700;
                letter-spacing: -0.025em;
            }

            .heading-2 {
                font-size: 1.875rem;
                line-height: 2.25rem;
                font-weight: 700;
                letter-spacing: -0.025em;
            }

            .heading-3 {
                font-size: 1.5rem;
                line-height: 2rem;
                font-weight: 600;
            }

            .heading-4 {
                font-size: 1.25rem;
                line-height: 1.75rem;
                font-weight: 600;
            }

            .text-lg {
                font-size: 1.125rem;
                line-height: 1.75rem;
            }

            .text-base {
                font-size: 1rem;
                line-height: 1.5rem;
            }

            .text-sm {
                font-size: 0.875rem;
                line-height: 1.25rem;
            }

            .text-xs {
                font-size: 0.75rem;
                line-height: 1rem;
            }

            /* Navigation */
            .navbar {
                background-color: rgb(var(--color-background));
                border-bottom: 1px solid rgb(var(--color-border));
                padding: 0.75rem 0;
                position: sticky;
                top: 0;
                z-index: 50;
                transition: background-color var(--transition-normal), border-color var(--transition-normal);
            }

            .navbar-container {
                display: flex;
                align-items: center;
                justify-content: space-between;
                height: 4rem;
            }

            /* Tab Navigation */
            .tab-nav {
                display: flex;
                overflow-x: auto;
                border-bottom: 1px solid rgb(var(--color-border));
                margin-bottom: -1px;
                scrollbar-width: none; /* Firefox */
            }

            .tab-nav::-webkit-scrollbar {
                display: none; /* Chrome, Safari, Edge */
            }

            .tab-nav-item {
                position: relative;
                display: inline-flex;
                align-items: center;
                white-space: nowrap;
                border-bottom: 2px solid transparent;
                padding: 1rem 1rem;
                font-size: 0.875rem;
                font-weight: 500;
                transition: all var(--transition-normal);
            }

            .tab-nav-item-active {
                border-color: rgb(var(--color-primary));
                color: rgb(var(--color-primary));
            }

            .dark .tab-nav-item-active {
                color: rgb(var(--color-primary-light));
            }

            .tab-nav-item-inactive {
                border-color: transparent;
                color: rgb(var(--color-muted));
            }

            .tab-nav-item-inactive:hover {
                border-color: rgb(var(--color-gray-300));
                color: rgb(var(--color-foreground));
            }

            .dark .tab-nav-item-inactive:hover {
                border-color: rgb(var(--color-gray-700));
            }

            .tab-nav-badge {
                position: absolute;
                top: 0.5rem;
                right: 0.25rem;
                display: flex;
                align-items: center;
                justify-content: center;
                height: 1rem;
                min-width: 1rem;
                padding: 0 0.25rem;
                border-radius: var(--radius-full);
                background-color: rgb(var(--color-error));
                color: white;
                font-size: 0.75rem;
                font-weight: 600;
            }

            /* Secondary Tab Navigation */
            .secondary-tab-nav {
                display: flex;
                overflow-x: auto;
                border-bottom: 1px solid rgb(var(--color-border));
                margin-bottom: -1px;
                padding-left: 0.25rem;
                scrollbar-width: none;
            }

            .secondary-tab-nav::-webkit-scrollbar {
                display: none;
            }

            .secondary-tab-nav-item {
                position: relative;
                display: inline-flex;
                align-items: center;
                white-space: nowrap;
                border-bottom: 2px solid transparent;
                padding: 0.75rem 0.75rem;
                font-size: 0.875rem;
                font-weight: 500;
                transition: all var(--transition-normal);
            }

            .secondary-tab-nav-item-active {
                border-color: rgb(var(--color-primary));
                color: rgb(var(--color-primary));
            }

            .dark .secondary-tab-nav-item-active {
                color: rgb(var(--color-primary-light));
            }

            .secondary-tab-nav-item-inactive {
                border-color: transparent;
                color: rgb(var(--color-muted));
            }

            .secondary-tab-nav-item-inactive:hover {
                border-color: rgb(var(--color-gray-300));
                color: rgb(var(--color-foreground));
            }

            .dark .secondary-tab-nav-item-inactive:hover {
                border-color: rgb(var(--color-gray-700));
            }

            /* Cards */
            .card {
                background-color: rgb(var(--color-background));
                border-radius: var(--radius-lg);
                box-shadow: var(--shadow-sm);
                overflow: hidden;
                transition: all var(--transition-normal);
            }

            .card:hover {
                box-shadow: var(--shadow-md);
            }

            .card-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 1rem;
                border-bottom: 1px solid rgb(var(--color-border));
            }

            .card-title {
                font-size: 1.125rem;
                font-weight: 600;
                color: rgb(var(--color-foreground));
            }

            .card-content {
                padding: 1rem;
            }

            .card-footer {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 1rem;
                border-top: 1px solid rgb(var(--color-border));
            }

            /* Stat Cards */
            .stat-card {
                background-color: rgb(var(--color-background));
                border-radius: var(--radius-lg);
                box-shadow: var(--shadow-sm);
                padding: 1rem;
                transition: all var(--transition-normal);
            }

            .stat-card:hover {
                transform: translateY(-2px);
                box-shadow: var(--shadow-md);
            }

            .stat-card-icon {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 2.5rem;
                height: 2.5rem;
                border-radius: var(--radius-full);
            }

            .stat-card-icon-primary {
                background-color: rgba(var(--color-primary), 0.1);
                color: rgb(var(--color-primary));
            }

            .stat-card-icon-success {
                background-color: rgba(var(--color-success), 0.1);
                color: rgb(var(--color-success));
            }

            .stat-card-icon-warning {
                background-color: rgba(var(--color-warning), 0.1);
                color: rgb(var(--color-warning));
            }

            .stat-card-icon-error {
                background-color: rgba(var(--color-error), 0.1);
                color: rgb(var(--color-error));
            }

            .stat-card-icon-info {
                background-color: rgba(var(--color-info), 0.1);
                color: rgb(var(--color-info));
            }

            .stat-card-label {
                font-size: 0.75rem;
                font-weight: 500;
                color: rgb(var(--color-muted));
                margin-top: 0.5rem;
            }

            .stat-card-value {
                font-size: 1.5rem;
                font-weight: 700;
                color: rgb(var(--color-foreground));
                margin-top: 0.25rem;
            }

            /* Tables */
            .table-container {
                overflow-x: auto;
            }

            .table {
                width: 100%;
                border-collapse: separate;
                border-spacing: 0;
            }

            .table th {
                padding: 0.75rem 1rem;
                text-align: left;
                font-size: 0.75rem;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                color: rgb(var(--color-muted));
                background-color: rgb(var(--color-background));
                border-bottom: 1px solid rgb(var(--color-border));
            }

            .table td {
                padding: 0.75rem 1rem;
                font-size: 0.875rem;
                color: rgb(var(--color-foreground));
                border-bottom: 1px solid rgb(var(--color-border));
            }

            .table tr:last-child td {
                border-bottom: none;
            }

            .table-row-hover:hover td {
                background-color: rgba(var(--color-gray-100), 0.5);
            }

            .dark .table-row-hover:hover td {
                background-color: rgba(var(--color-gray-800), 0.5);
            }

            /* Status Badges */
            .badge {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border-radius: var(--radius-full);
                padding: 0.125rem 0.5rem;
                font-size: 0.75rem;
                font-weight: 600;
                line-height: 1;
            }

            .badge-success {
                background-color: rgb(var(--color-success-light));
                color: rgb(var(--color-success-dark));
            }

            .badge-warning {
                background-color: rgb(var(--color-warning-light));
                color: rgb(var(--color-warning-dark));
            }

            .badge-error {
                background-color: rgb(var(--color-error-light));
                color: rgb(var(--color-error-dark));
            }

            .badge-info {
                background-color: rgb(var(--color-info-light));
                color: rgb(var(--color-info-dark));
            }

            /* Buttons */
            .btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border-radius: var(--radius-md);
                padding: 0.5rem 1rem;
                font-size: 0.875rem;
                font-weight: 500;
                line-height: 1.25rem;
                transition: all var(--transition-normal);
                cursor: pointer;
            }

            .btn-sm {
                padding: 0.375rem 0.75rem;
                font-size: 0.75rem;
            }

            .btn-lg {
                padding: 0.625rem 1.25rem;
                font-size: 1rem;
            }

            .btn-icon {
                padding: 0.5rem;
            }

            .btn-icon-sm {
                padding: 0.375rem;
            }

            .btn-icon-lg {
                padding: 0.625rem;
            }

            .btn-primary {
                background-color: rgb(var(--color-primary));
                color: white;
            }

            .btn-primary:hover {
                background-color: rgb(var(--color-primary-dark));
            }

            .btn-secondary {
                background-color: rgb(var(--color-gray-200));
                color: rgb(var(--color-gray-800));
            }

            .btn-secondary:hover {
                background-color: rgb(var(--color-gray-300));
            }

            .dark .btn-secondary {
                background-color: rgb(var(--color-gray-700));
                color: rgb(var(--color-gray-200));
            }

            .dark .btn-secondary:hover {
                background-color: rgb(var(--color-gray-600));
            }

            .btn-outline {
                background-color: transparent;
                border: 1px solid rgb(var(--color-border));
                color: rgb(var(--color-foreground));
            }

            .btn-outline:hover {
                background-color: rgb(var(--color-gray-100));
            }

            .dark .btn-outline:hover {
                background-color: rgb(var(--color-gray-800));
            }

            .btn-success {
                background-color: rgb(var(--color-success));
                color: white;
            }

            .btn-success:hover {
                background-color: rgba(var(--color-success), 0.9);
            }

            .btn-warning {
                background-color: rgb(var(--color-warning));
                color: white;
            }

            .btn-warning:hover {
                background-color: rgba(var(--color-warning), 0.9);
            }

            .btn-error {
                background-color: rgb(var(--color-error));
                color: white;
            }

            .btn-error:hover {
                background-color: rgba(var(--color-error), 0.9);
            }

            .btn-info {
                background-color: rgb(var(--color-info));
                color: white;
            }

            .btn-info:hover {
                background-color: rgba(var(--color-info), 0.9);
            }

            /* Forms */
            .form-group {
                margin-bottom: 1rem;
            }

            .form-label {
                display: block;
                font-size: 0.875rem;
                font-weight: 500;
                margin-bottom: 0.5rem;
                color: rgb(var(--color-foreground));
            }

            .form-input {
                display: block;
                width: 100%;
                padding: 0.5rem 0.75rem;
                font-size: 0.875rem;
                line-height: 1.25rem;
                color: rgb(var(--color-foreground));
                background-color: rgb(var(--color-background));
                border: 1px solid rgb(var(--color-border));
                border-radius: var(--radius-md);
                transition: all var(--transition-normal);
            }

            .form-input:focus {
                outline: none;
                border-color: rgb(var(--color-primary));
                box-shadow: 0 0 0 2px rgba(var(--color-primary), 0.25);
            }

            .form-input-error {
                border-color: rgb(var(--color-error));
            }

            .form-input-error:focus {
                border-color: rgb(var(--color-error));
                box-shadow: 0 0 0 2px rgba(var(--color-error), 0.25);
            }

            .form-error {
                font-size: 0.75rem;
                color: rgb(var(--color-error));
                margin-top: 0.25rem;
            }

            .form-select {
                display: block;
                width: 100%;
                padding: 0.5rem 2rem 0.5rem 0.75rem;
                font-size: 0.875rem;
                line-height: 1.25rem;
                color: rgb(var(--color-foreground));
                background-color: rgb(var(--color-background));
                background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
                background-position: right 0.5rem center;
                background-repeat: no-repeat;
                background-size: 1.5em 1.5em;
                border: 1px solid rgb(var(--color-border));
                border-radius: var(--radius-md);
                appearance: none;
                transition: all var(--transition-normal);
            }

            .form-select:focus {
                outline: none;
                border-color: rgb(var(--color-primary));
                box-shadow: 0 0 0 2px rgba(var(--color-primary), 0.25);
            }

            /* Dropdowns */
            .dropdown {
                position: relative;
                display: inline-block;
            }

            .dropdown-menu {
                position: absolute;
                top: 100%;
                right: 0;
                z-index: 50;
                min-width: 10rem;
                padding: 0.5rem 0;
                margin-top: 0.5rem;
                background-color: rgb(var(--color-background));
                border-radius: var(--radius-md);
                box-shadow: var(--shadow-lg);
                border: 1px solid rgb(var(--color-border));
                opacity: 0;
                transform: translateY(-10px);
                pointer-events: none;
                transition: all var(--transition-normal);
            }

            .dropdown-menu-open {
                opacity: 1;
                transform: translateY(0);
                pointer-events: auto;
            }

            .dropdown-item {
                display: block;
                padding: 0.5rem 1rem;
                font-size: 0.875rem;
                color: rgb(var(--color-foreground));
                transition: all var(--transition-fast);
                cursor: pointer;
            }

            .dropdown-item:hover {
                background-color: rgb(var(--color-gray-100));
            }

            .dark .dropdown-item:hover {
                background-color: rgb(var(--color-gray-800));
            }

            /* Alerts */
            .alert {
                position: relative;
                padding: 1rem;
                margin-bottom: 1rem;
                border-radius: var(--radius-md);
                border-left: 4px solid transparent;
            }

            .alert-success {
                background-color: rgb(var(--color-success-light));
                border-left-color: rgb(var(--color-success));
                color: rgb(var(--color-success-dark));
            }

            .alert-warning {
                background-color: rgb(var(--color-warning-light));
                border-left-color: rgb(var(--color-warning));
                color: rgb(var(--color-warning-dark));
            }

            .alert-error {
                background-color: rgb(var(--color-error-light));
                border-left-color: rgb(var(--color-error));
                color: rgb(var(--color-error-dark));
            }

            .alert-info {
                background-color: rgb(var(--color-info-light));
                border-left-color: rgb(var(--color-info));
                color: rgb(var(--color-info-dark));
            }

            /* Modals */
            .modal-backdrop {
                position: fixed;
                top: 0;
                right: 0;
                bottom: 0;
                left: 0;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 40;
                display: flex;
                align-items: center;
                justify-content: center;
                opacity: 0;
                pointer-events: none;
                transition: opacity var(--transition-normal);
            }

            .modal-backdrop-open {
                opacity: 1;
                pointer-events: auto;
            }

            .modal {
                width: 100%;
                max-width: 28rem;
                max-height: calc(100vh - 2rem);
                overflow-y: auto;
                background-color: rgb(var(--color-background));
                border-radius: var(--radius-lg);
                box-shadow: var(--shadow-lg);
                transform: scale(0.95);
                opacity: 0;
                transition: all var(--transition-normal);
            }

            .modal-open {
                transform: scale(1);
                opacity: 1;
            }

            .modal-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 1rem;
                border-bottom: 1px solid rgb(var(--color-border));
            }

            .modal-title {
                font-size: 1.125rem;
                font-weight: 600;
                color: rgb(var(--color-foreground));
            }

            .modal-close {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 2rem;
                height: 2rem;
                border-radius: var(--radius-full);
                background-color: transparent;
                color: rgb(var(--color-muted));
                transition: all var(--transition-fast);
                cursor: pointer;
            }

            .modal-close:hover {
                background-color: rgb(var(--color-gray-100));
                color: rgb(var(--color-foreground));
            }

            .dark .modal-close:hover {
                background-color: rgb(var(--color-gray-800));
            }

            .modal-body {
                padding: 1rem;
            }

            .modal-footer {
                display: flex;
                align-items: center;
                justify-content: flex-end;
                gap: 0.5rem;
                padding: 1rem;
                border-top: 1px solid rgb(var(--color-border));
            }

            /* Tooltips */
            .tooltip {
                position: relative;
                display: inline-block;
            }

            .tooltip-content {
                position: absolute;
                z-index: 30;
                padding: 0.5rem;
                font-size: 0.75rem;
                background-color: rgb(var(--color-gray-900));
                color: white;
                border-radius: var(--radius-md);
                box-shadow: var(--shadow-md);
                white-space: nowrap;
                opacity: 0;
                pointer-events: none;
                transition: all var(--transition-fast);
            }

            .tooltip-top {
                bottom: 100%;
                left: 50%;
                transform: translateX(-50%) translateY(-0.25rem);
                margin-bottom: 0.5rem;
            }

            .tooltip-right {
                left: 100%;
                top: 50%;
                transform: translateY(-50%) translateX(0.25rem);
                margin-left: 0.5rem;
            }

            .tooltip-bottom {
                top: 100%;
                left: 50%;
                transform: translateX(-50%) translateY(0.25rem);
                margin-top: 0.5rem;
            }

            .tooltip-left {
                right: 100%;
                top: 50%;
                transform: translateY(-50%) translateX(-0.25rem);
                margin-right: 0.5rem;
            }

            .tooltip:hover .tooltip-content {
                opacity: 1;
            }

            /* Notification badges */
            .notification-badge {
                position: absolute;
                top: -0.25rem;
                right: -0.25rem;
                display: flex;
                align-items: center;
                justify-content: center;
                min-width: 1rem;
                height: 1rem;
                padding: 0 0.25rem;
                border-radius: var(--radius-full);
                background-color: rgb(var(--color-error));
                color: white;
                font-size: 0.75rem;
                font-weight: 600;
                line-height: 1;
            }

            /* Icons */
            .icon {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 1rem;
                height: 1rem;
            }

            .icon-sm {
                width: 0.875rem;
                height: 0.875rem;
            }

            .icon-lg {
                width: 1.25rem;
                height: 1.25rem;
            }

            .icon-xl {
                width: 1.5rem;
                height: 1.5rem;
            }

            /* Utility classes */
            .flex-center {
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .flex-between {
                display: flex;
                align-items: center;
                justify-content: space-between;
            }

            .flex-start {
                display: flex;
                align-items: center;
                justify-content: flex-start;
            }

            .flex-end {
                display: flex;
                align-items: center;
                justify-content: flex-end;
            }

            .flex-col {
                display: flex;
                flex-direction: column;
            }

            .gap-1 {
                gap: 0.25rem;
            }

            .gap-2 {
                gap: 0.5rem;
            }

            .gap-3 {
                gap: 0.75rem;
            }

            .gap-4 {
                gap: 1rem;
            }

            .gap-5 {
                gap: 1.25rem;
            }

            .gap-6 {
                gap: 1.5rem;
            }

            .truncate {
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .sr-only {
                position: absolute;
                width: 1px;
                height: 1px;
                padding: 0;
                margin: -1px;
                overflow: hidden;
                clip: rect(0, 0, 0, 0);
                white-space: nowrap;
                border-width: 0;
            }

            /* Animations */
            @keyframes spin {
                to {
                    transform: rotate(360deg);
                }
            }

            .animate-spin {
                animation: spin 1s linear infinite;
            }

            @keyframes pulse {
                50% {
                    opacity: .5;
                }
            }

            .animate-pulse {
                animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
            }

            @keyframes bounce {
                0%, 100% {
                    transform: translateY(-25%);
                    animation-timing-function: cubic-bezier(0.8,0,1,1);
                }
                50% {
                    transform: none;
                    animation-timing-function: cubic-bezier(0,0,0.2,1);
                }
            }

            .animate-bounce {
                animation: bounce 1s infinite;
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="app-container bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')

            <!-- Page Heading -->
            <!-- @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="content-container py-6">
                        {{ $header }}
                    </div>
                </header>
            @endisset -->

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
        <div x-data="requests">
            @yield('content')
        </div>

        <!-- Global JavaScript -->
        <script>
            // Notification update function
            function updateNotificationCount() {
                // This would typically fetch from an API endpoint
                // For now, we'll just simulate it
                console.log('Updating notification counts...');
            }

            // Initialize Alpine.js data
            document.addEventListener('alpine:init', () => {
                Alpine.data('tabNavigation', (defaultTab) => ({
                    activeTab: defaultTab,
                    setActiveTab(tab) {
                        this.activeTab = tab;
                    }
                }));

                Alpine.data('userDropdown', () => ({
                    open: false,
                    toggle() {
                        this.open = !this.open;
                    }
                }));

                Alpine.data('filterDropdown', () => ({
                    open: false,
                    toggle() {
                        this.open = !this.open;
                    }
                }));

                Alpine.data('modal', () => ({
                    open: false,
                    show() {
                        this.open = true;
                        document.body.classList.add('overflow-hidden');
                    },
                    hide() {
                        this.open = false;
                        document.body.classList.remove('overflow-hidden');
                    }
                }));

                Alpine.data('darkMode', () => ({
                    dark: localStorage.getItem('darkMode') === 'true' ||
                          (!localStorage.getItem('darkMode') &&
                           window.matchMedia('(prefers-color-scheme: dark)').matches),
                    init() {
                        if (this.dark) {
                            document.documentElement.classList.add('dark');
                        } else {
                            document.documentElement.classList.remove('dark');
                        }
                    },
                    toggle() {
                        this.dark = !this.dark;
                        localStorage.setItem('darkMode', this.dark);

                        if (this.dark) {
                            document.documentElement.classList.add('dark');
                        } else {
                            document.documentElement.classList.remove('dark');
                        }
                    }
                }));
            });

            // Set up polling for notifications (every 30 seconds)
            setInterval(updateNotificationCount, 30000);
        </script>
    </body>
</html>
