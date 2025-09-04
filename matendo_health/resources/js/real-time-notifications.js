/**
 * Real-time Notifications JavaScript Module
 * Handles WebSocket connections and notification management
 */

class RealTimeNotifications {
    constructor() {
        this.notifications = [];
        this.unreadCount = 0;
        this.isConnected = false;
        this.reconnectAttempts = 0;
        this.maxReconnectAttempts = 5;
        this.userId = null;
        this.userRole = null;
        
        // Initialize when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.init());
        } else {
            this.init();
        }
    }

    /**
     * Initialize the notification system
     */
    init() {
        // Get user info from meta tags
        this.getUserInfo();
        
        // Initialize UI elements
        this.initializeUI();
        
        // Setup WebSocket connection (using Pusher/Echo)
        this.setupWebSocket();
        
        // Load initial notifications
        this.loadInitialNotifications();
        
        // Setup periodic refresh fallback
        this.setupPeriodicRefresh();
    }

    /**
     * Get user information from meta tags
     */
    getUserInfo() {
        const userMeta = document.querySelector('meta[name="user-id"]');
        const roleMeta = document.querySelector('meta[name="user-role"]');
        
        this.userId = userMeta ? userMeta.getAttribute('content') : null;
        this.userRole = roleMeta ? roleMeta.getAttribute('content') : null;
    }

    /**
     * Initialize UI elements
     */
    initializeUI() {
        // Notification bell icon
        this.notificationBell = document.getElementById('notificationsButton');
        this.notificationCounter = document.getElementById('notificationCounter');
        this.notificationsPanel = document.getElementById('notificationsPanel');
        this.notificationsContainer = document.getElementById('notificationsContainer');
        
        // Setup click handlers
        if (this.notificationBell) {
            this.notificationBell.addEventListener('click', () => this.toggleNotificationPanel());
        }
        
        // Mark all as read button
        const markAllBtn = document.querySelector('[onclick="markAllAsReadVisually()"]');
        if (markAllBtn) {
            markAllBtn.onclick = () => this.markAllAsRead();
        }
    }

    /**
     * Setup WebSocket connection using Laravel Echo
     */
    setupWebSocket() {
        // Check if Laravel Echo is available
        if (typeof Echo !== 'undefined' && this.userId) {
            try {
                // Listen for personal notifications
                Echo.private(`user.${this.userId}`)
                    .listen('.notification', (data) => {
                        this.handleNewNotification(data);
                    })
                    .error((error) => {
                        console.error('WebSocket connection error:', error);
                        this.handleConnectionError();
                    });

                // Listen for medical alerts if user is medical staff
                if (['doctor', 'admin'].includes(this.userRole)) {
                    Echo.channel('medical-alerts')
                        .listen('.medical-alert', (data) => {
                            this.handleMedicalAlert(data);
                        });
                }

                // Listen for admin-specific alerts
                if (this.userRole === 'admin') {
                    Echo.private('admin.alerts')
                        .listen('.medical-alert', (data) => {
                            this.handleMedicalAlert(data);
                        });
                }

                this.isConnected = true;
                this.reconnectAttempts = 0;
                console.log('Real-time notifications connected');

            } catch (error) {
                console.error('Failed to setup WebSocket:', error);
                this.handleConnectionError();
            }
        } else {
            console.warn('Laravel Echo not available, using polling fallback');
            this.setupPollingFallback();
        }
    }

    /**
     * Handle new notification received via WebSocket
     */
    handleNewNotification(data) {
        console.log('New notification received:', data);
        
        // Add to notifications array
        this.notifications.unshift(data);
        this.unreadCount++;
        
        // Update UI
        this.updateNotificationCounter();
        this.addNotificationToUI(data);
        
        // Show toast notification
        this.showToastNotification(data);
        
        // Play sound for important notifications
        if (['medical_alert', 'emergency'].includes(data.type)) {
            this.playNotificationSound();
        }
    }

    /**
     * Handle medical alert
     */
    handleMedicalAlert(data) {
        console.log('Medical alert received:', data);
        
        // Create notification object
        const notification = {
            id: data.id,
            type: 'medical_alert',
            title: 'Medical Alert',
            message: data.message,
            data: data,
            created_at: data.timestamp,
            read_at: null,
            severity: data.severity
        };
        
        // Handle based on severity
        if (['high', 'critical'].includes(data.severity)) {
            this.showCriticalAlert(notification);
        } else {
            this.handleNewNotification(notification);
        }
    }

    /**
     * Show critical alert modal
     */
    showCriticalAlert(notification) {
        const modal = this.createCriticalAlertModal(notification);
        document.body.appendChild(modal);
        
        // Auto-remove after 30 seconds
        setTimeout(() => {
            if (document.body.contains(modal)) {
                document.body.removeChild(modal);
            }
        }, 30000);
        
        // Play urgent sound
        this.playUrgentSound();
    }

    /**
     * Create critical alert modal
     */
    createCriticalAlertModal(notification) {
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 z-[9999] flex items-center justify-center bg-black bg-opacity-50 animate-pulse';
        
        modal.innerHTML = `
            <div class="bg-white rounded-xl p-6 max-w-md mx-4 shadow-2xl border-4 border-red-500">
                <div class="flex items-center mb-4">
                    <div class="bg-red-500 rounded-full p-2 mr-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-red-600">CRITICAL ALERT</h3>
                </div>
                <p class="text-gray-800 font-semibold mb-4">${notification.message}</p>
                <div class="flex justify-end space-x-3">
                    <button onclick="this.closest('.fixed').remove()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                        Dismiss
                    </button>
                    <button onclick="window.location.href='${this.getActionUrl(notification)}'" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                        View Patient
                    </button>
                </div>
            </div>
        `;
        
        return modal;
    }

    /**
     * Load initial notifications from server
     */
    async loadInitialNotifications() {
        try {
            const response = await fetch('/notifications/dashboard', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': window.csrfToken,
                    'Accept': 'application/json'
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                this.notifications = data.notifications || [];
                this.unreadCount = data.unread_count || 0;
                
                this.updateNotificationCounter();
                this.renderNotifications();
            }
        } catch (error) {
            console.error('Failed to load initial notifications:', error);
        }
    }

    /**
     * Update notification counter in UI
     */
    updateNotificationCounter() {
        if (this.notificationCounter) {
            if (this.unreadCount > 0) {
                this.notificationCounter.textContent = this.unreadCount > 99 ? '99+' : this.unreadCount;
                this.notificationCounter.style.display = 'flex';
                this.notificationCounter.classList.add('animate-pulse');
            } else {
                this.notificationCounter.style.display = 'none';
                this.notificationCounter.classList.remove('animate-pulse');
            }
        }
    }

    /**
     * Add notification to UI
     */
    addNotificationToUI(notification) {
        if (!this.notificationsContainer) return;
        
        const notificationElement = this.createNotificationElement(notification);
        
        // Remove empty message if exists
        const emptyMessage = this.notificationsContainer.querySelector('.text-center');
        if (emptyMessage) {
            emptyMessage.remove();
        }
        
        // Add to top of list
        this.notificationsContainer.insertAdjacentHTML('afterbegin', notificationElement);
        
        // Limit to 20 notifications in UI
        const notificationElements = this.notificationsContainer.querySelectorAll('.notification-item');
        if (notificationElements.length > 20) {
            notificationElements[20].remove();
        }
    }

    /**
     * Create notification element HTML
     */
    createNotificationElement(notification) {
        const timeAgo = this.getTimeAgo(new Date(notification.created_at));
        const isUnread = !notification.read_at;
        const iconClass = this.getNotificationIcon(notification.type);
        const bgClass = isUnread ? 'bg-blue-50' : 'bg-white';
        
        return `
            <div class="notification-item ${bgClass} p-4 border-b border-gray-100 hover:bg-gray-50 transition-colors cursor-pointer"
                 onclick="notificationSystem.markAsRead('${notification.id}', this)"
                 data-notification-id="${notification.id}">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 rounded-full ${this.getNotificationBgColor(notification.type)} flex items-center justify-center">
                            ${iconClass}
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-semibold text-gray-900 truncate">
                                ${notification.title}
                            </p>
                            ${isUnread ? '<div class="w-2 h-2 bg-blue-500 rounded-full"></div>' : ''}
                        </div>
                        <p class="text-sm text-gray-600 mt-1">${notification.message}</p>
                        <p class="text-xs text-gray-400 mt-1">${timeAgo}</p>
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * Get notification icon based on type
     */
    getNotificationIcon(type) {
        const icons = {
            medical_alert: '<svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>',
            appointment: '<svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>',
            system: '<svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>',
            default: '<svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
        };
        
        return icons[type] || icons.default;
    }

    /**
     * Get notification background color based on type
     */
    getNotificationBgColor(type) {
        const colors = {
            medical_alert: 'bg-red-500',
            appointment: 'bg-blue-500',
            system: 'bg-yellow-500',
            default: 'bg-gray-500'
        };
        
        return colors[type] || colors.default;
    }

    /**
     * Show toast notification
     */
    showToastNotification(notification) {
        if (typeof window.showToast === 'function') {
            const type = this.getToastType(notification.type);
            window.showToast(notification.message, type, 5000);
        }
    }

    /**
     * Get toast type for notification
     */
    getToastType(notificationType) {
        const typeMap = {
            medical_alert: 'error',
            appointment: 'info',
            system: 'warning',
            default: 'info'
        };
        
        return typeMap[notificationType] || typeMap.default;
    }

    /**
     * Play notification sound
     */
    playNotificationSound() {
        const audio = new Audio('/sounds/notification.mp3');
        audio.volume = 0.5;
        audio.play().catch(e => console.log('Could not play notification sound:', e));
    }

    /**
     * Play urgent notification sound
     */
    playUrgentSound() {
        const audio = new Audio('/sounds/urgent-alert.mp3');
        audio.volume = 0.8;
        audio.play().catch(e => console.log('Could not play urgent sound:', e));
    }

    /**
     * Mark notification as read
     */
    async markAsRead(notificationId, element = null) {
        try {
            const response = await fetch(`/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': window.csrfToken,
                    'Accept': 'application/json'
                }
            });
            
            if (response.ok) {
                // Update local state
                const notification = this.notifications.find(n => n.id === notificationId);
                if (notification && !notification.read_at) {
                    notification.read_at = new Date().toISOString();
                    this.unreadCount = Math.max(0, this.unreadCount - 1);
                    this.updateNotificationCounter();
                }
                
                // Update UI element
                if (element) {
                    element.classList.remove('bg-blue-50');
                    element.classList.add('bg-white');
                    const unreadDot = element.querySelector('.bg-blue-500');
                    if (unreadDot) {
                        unreadDot.remove();
                    }
                }
            }
        } catch (error) {
            console.error('Failed to mark notification as read:', error);
        }
    }

    /**
     * Mark all notifications as read
     */
    async markAllAsRead() {
        try {
            const response = await fetch('/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': window.csrfToken,
                    'Accept': 'application/json'
                }
            });
            
            if (response.ok) {
                // Update local state
                this.notifications.forEach(notification => {
                    if (!notification.read_at) {
                        notification.read_at = new Date().toISOString();
                    }
                });
                this.unreadCount = 0;
                this.updateNotificationCounter();
                
                // Update UI
                this.renderNotifications();
                
                if (typeof window.showToast === 'function') {
                    window.showToast('All notifications marked as read', 'success');
                }
            }
        } catch (error) {
            console.error('Failed to mark all notifications as read:', error);
        }
    }

    /**
     * Toggle notification panel visibility
     */
    toggleNotificationPanel() {
        if (this.notificationsPanel) {
            this.notificationsPanel.classList.toggle('hidden');
            
            if (!this.notificationsPanel.classList.contains('hidden')) {
                this.loadInitialNotifications();
            }
        }
    }

    /**
     * Render all notifications
     */
    renderNotifications() {
        if (!this.notificationsContainer) return;
        
        if (this.notifications.length === 0) {
            this.notificationsContainer.innerHTML = `
                <div class="p-4 text-center text-gray-500">
                    No notifications found
                </div>
            `;
        } else {
            const notificationsHtml = this.notifications
                .slice(0, 20) // Limit to 20
                .map(notification => this.createNotificationElement(notification))
                .join('');
            
            this.notificationsContainer.innerHTML = notificationsHtml;
        }
    }

    /**
     * Get action URL for notification
     */
    getActionUrl(notification) {
        if (notification.data && notification.data.action_url) {
            return notification.data.action_url;
        }
        
        // Default based on type
        const defaults = {
            medical_alert: '/doctor/patients',
            appointment: '/appointments',
            system: '/admin/monitoring'
        };
        
        return defaults[notification.type] || '#';
    }

    /**
     * Get time ago string
     */
    getTimeAgo(date) {
        const now = new Date();
        const diff = now - date;
        const minutes = Math.floor(diff / 60000);
        const hours = Math.floor(minutes / 60);
        const days = Math.floor(hours / 24);
        
        if (minutes < 1) return 'Just now';
        if (minutes < 60) return `${minutes}m ago`;
        if (hours < 24) return `${hours}h ago`;
        if (days < 7) return `${days}d ago`;
        return date.toLocaleDateString();
    }

    /**
     * Setup polling fallback when WebSocket is not available
     */
    setupPollingFallback() {
        setInterval(() => {
            this.loadInitialNotifications();
        }, 30000); // Poll every 30 seconds
    }

    /**
     * Setup periodic refresh
     */
    setupPeriodicRefresh() {
        // Refresh notifications every 5 minutes
        setInterval(() => {
            if (this.isConnected) {
                this.loadInitialNotifications();
            }
        }, 300000);
    }

    /**
     * Handle connection errors
     */
    handleConnectionError() {
        this.isConnected = false;
        this.reconnectAttempts++;
        
        if (this.reconnectAttempts <= this.maxReconnectAttempts) {
            console.log(`Attempting to reconnect (${this.reconnectAttempts}/${this.maxReconnectAttempts})...`);
            setTimeout(() => this.setupWebSocket(), 5000 * this.reconnectAttempts);
        } else {
            console.log('Max reconnection attempts reached. Using polling fallback.');
            this.setupPollingFallback();
        }
    }
}

// Initialize the notification system
const notificationSystem = new RealTimeNotifications();

// Make it globally available
window.notificationSystem = notificationSystem;
