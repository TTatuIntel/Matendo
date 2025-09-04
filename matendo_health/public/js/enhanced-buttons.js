/**
 * Enhanced Button Functionality and UI Interactions
 * Medical Care System - Advanced Admin Interface
 */

// Global utility functions for button interactions
window.ButtonUtils = {
    // Loading state management
    showLoading: function(button) {
        if (!button) return;
        
        button.disabled = true;
        button.dataset.originalContent = button.innerHTML;
        button.classList.add('loading');
        
        const spinner = '<svg class="animate-spin h-4 w-4 mr-2" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>';
        button.innerHTML = spinner + 'Loading...';
    },
    
    hideLoading: function(button) {
        if (!button) return;
        
        button.disabled = false;
        button.classList.remove('loading');
        
        if (button.dataset.originalContent) {
            button.innerHTML = button.dataset.originalContent;
        }
    },
    
    // Success state
    showSuccess: function(button, message = 'Success!', duration = 2000) {
        if (!button) return;
        
        const originalContent = button.innerHTML;
        const successIcon = '<svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';
        
        button.innerHTML = successIcon + message;
        button.classList.add('btn-success');
        button.classList.remove('btn-primary', 'btn-secondary', 'btn-danger');
        
        setTimeout(() => {
            button.innerHTML = originalContent;
            button.classList.remove('btn-success');
            button.classList.add('btn-primary');
        }, duration);
    },
    
    // Error state
    showError: function(button, message = 'Error!', duration = 2000) {
        if (!button) return;
        
        const originalContent = button.innerHTML;
        const errorIcon = '<svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
        
        button.innerHTML = errorIcon + message;
        button.classList.add('btn-danger');
        button.classList.remove('btn-primary', 'btn-secondary', 'btn-success');
        
        setTimeout(() => {
            button.innerHTML = originalContent;
            button.classList.remove('btn-danger');
            button.classList.add('btn-primary');
        }, duration);
    },
    
    // Confirmation dialog
    confirmAction: function(message, onConfirm, onCancel = null) {
        return new Promise((resolve) => {
            const confirmed = confirm(message);
            if (confirmed && onConfirm) {
                onConfirm();
                resolve(true);
            } else if (!confirmed && onCancel) {
                onCancel();
                resolve(false);
            } else {
                resolve(confirmed);
            }
        });
    },
    
    // Enhanced toast notifications
    showToast: function(message, type = 'info', duration = 5000) {
        // Create toast container if it doesn't exist
        let toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            toastContainer.className = 'fixed top-4 right-4 z-50 space-y-2';
            document.body.appendChild(toastContainer);
        }
        
        // Create toast element
        const toast = document.createElement('div');
        const toastId = 'toast-' + Date.now();
        toast.id = toastId;
        
        const typeStyles = {
            success: 'bg-green-500 text-white',
            error: 'bg-red-500 text-white',
            warning: 'bg-yellow-500 text-black',
            info: 'bg-blue-500 text-white',
            default: 'bg-gray-500 text-white'
        };
        
        const iconMap = {
            success: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>',
            error: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>',
            warning: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0l-7.918 8.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>',
            info: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
        };
        
        toast.className = `flex items-center p-4 rounded-lg shadow-lg transform translate-x-full transition-transform duration-300 ${typeStyles[type] || typeStyles.default}`;
        toast.innerHTML = `
            <div class="flex items-center">
                <div class="mr-3">${iconMap[type] || iconMap.info}</div>
                <div class="flex-1">${message}</div>
                <button onclick="ButtonUtils.hideToast('${toastId}')" class="ml-4 hover:opacity-75">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        `;
        
        toastContainer.appendChild(toast);
        
        // Show toast
        setTimeout(() => {
            toast.classList.remove('translate-x-full');
            toast.classList.add('translate-x-0');
        }, 100);
        
        // Auto hide
        setTimeout(() => {
            this.hideToast(toastId);
        }, duration);
        
        return toastId;
    },
    
    hideToast: function(toastId) {
        const toast = document.getElementById(toastId);
        if (toast) {
            toast.classList.add('translate-x-full');
            setTimeout(() => {
                toast.remove();
            }, 300);
        }
    },
    
    // AJAX helper with automatic loading states
    ajaxRequest: function(options) {
        const {
            url,
            method = 'GET',
            data = null,
            button = null,
            successMessage = 'Success!',
            errorMessage = 'An error occurred',
            onSuccess = null,
            onError = null,
            onComplete = null
        } = options;
        
        if (button) {
            this.showLoading(button);
        }
        
        const fetchOptions = {
            method: method.toUpperCase(),
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        };
        
        if (data) {
            if (data instanceof FormData) {
                fetchOptions.body = data;
            } else {
                fetchOptions.headers['Content-Type'] = 'application/json';
                fetchOptions.body = JSON.stringify(data);
            }
        }
        
        return fetch(url, fetchOptions)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success !== false) {
                    if (button) {
                        this.showSuccess(button, successMessage);
                    } else {
                        this.showToast(successMessage, 'success');
                    }
                    
                    if (onSuccess) {
                        onSuccess(data);
                    }
                    
                    return data;
                } else {
                    throw new Error(data.message || errorMessage);
                }
            })
            .catch(error => {
                console.error('AJAX Error:', error);
                
                if (button) {
                    this.showError(button, errorMessage);
                } else {
                    this.showToast(error.message || errorMessage, 'error');
                }
                
                if (onError) {
                    onError(error);
                }
                
                throw error;
            })
            .finally(() => {
                if (button) {
                    setTimeout(() => {
                        this.hideLoading(button);
                    }, 1000);
                }
                
                if (onComplete) {
                    onComplete();
                }
            });
    }
};

// Initialize enhanced button functionality
document.addEventListener('DOMContentLoaded', function() {
    // Add ripple effect to all buttons
    document.querySelectorAll('button, .btn-primary, .btn-secondary, .btn-danger, .btn-warning, .btn-success, .btn-info').forEach(button => {
        button.addEventListener('click', function(e) {
            // Skip if already has ripple or is disabled
            if (this.classList.contains('no-ripple') || this.disabled) {
                return;
            }
            
            const rect = this.getBoundingClientRect();
            const ripple = document.createElement('span');
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.cssText = `
                position: absolute;
                width: ${size}px;
                height: ${size}px;
                left: ${x}px;
                top: ${y}px;
                background: rgba(255, 255, 255, 0.3);
                border-radius: 50%;
                transform: scale(0);
                animation: ripple-effect 0.6s ease-out;
                pointer-events: none;
                z-index: 1;
            `;
            
            // Ensure button has relative positioning
            if (getComputedStyle(this).position === 'static') {
                this.style.position = 'relative';
            }
            this.style.overflow = 'hidden';
            
            this.appendChild(ripple);
            
            // Remove ripple after animation
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });
    
    // Add ripple animation CSS
    if (!document.getElementById('ripple-styles')) {
        const rippleStyles = document.createElement('style');
        rippleStyles.id = 'ripple-styles';
        rippleStyles.textContent = `
            @keyframes ripple-effect {
                to {
                    transform: scale(2);
                    opacity: 0;
                }
            }
            
            .loading {
                pointer-events: none;
                opacity: 0.7;
            }
            
            .btn-primary, .btn-secondary, .btn-danger, .btn-warning, .btn-success, .btn-info, .btn-ghost {
                position: relative;
                overflow: hidden;
            }
            
            .btn-primary:active, .btn-secondary:active, .btn-danger:active,
            .btn-warning:active, .btn-success:active, .btn-info:active {
                transform: translateY(0) scale(0.98);
            }
        `;
        document.head.appendChild(rippleStyles);
    }
    
    // Auto-enhance forms with AJAX submission
    document.querySelectorAll('form[data-ajax="true"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitButton = form.querySelector('button[type="submit"]');
            const formData = new FormData(form);
            const action = form.getAttribute('action') || window.location.href;
            const method = form.getAttribute('method') || 'POST';
            
            ButtonUtils.ajaxRequest({
                url: action,
                method: method,
                data: formData,
                button: submitButton,
                successMessage: 'Form submitted successfully!',
                onSuccess: (data) => {
                    // Handle success response
                    if (data.redirect) {
                        setTimeout(() => {
                            window.location.href = data.redirect;
                        }, 1500);
                    }
                }
            });
        });
    });
    
    // Add keyboard shortcuts for common actions
    document.addEventListener('keydown', function(e) {
        // Ctrl+S to save (if there's a save button)
        if (e.ctrlKey && e.key === 's') {
            e.preventDefault();
            const saveButton = document.querySelector('button[type="submit"], .btn-save');
            if (saveButton) {
                saveButton.click();
            }
        }
        
        // Escape to close modals
        if (e.key === 'Escape') {
            const openModal = document.querySelector('.modal:not(.hidden), .modal.show, .modal.flex');
            if (openModal) {
                const closeButton = openModal.querySelector('.btn-close, .modal-close, [onclick*="close"]');
                if (closeButton) {
                    closeButton.click();
                }
            }
        }
    });
    
    // Auto-hide alerts after 5 seconds
    document.querySelectorAll('.alert').forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'all 0.3s ease';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-20px)';
            setTimeout(() => {
                alert.remove();
            }, 300);
        }, 5000);
    });
});

// Global shortcuts
window.showToast = ButtonUtils.showToast;
window.showLoading = ButtonUtils.showLoading;
window.hideLoading = ButtonUtils.hideLoading;
window.ajaxRequest = ButtonUtils.ajaxRequest;

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ButtonUtils;
}
