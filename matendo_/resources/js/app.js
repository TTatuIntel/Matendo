import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.data('requests', () => ({
    activeMainTab: 'facility',
    showOverlay: false,
    selectedApplication: null,

    openOverlay(application) {
        this.selectedApplication = application;
        this.showOverlay = true;
    },

    closeOverlay() {
        this.showOverlay = false;
        this.selectedApplication = null;
    },

    approveApplication(id) {
        const form = this.$refs.statusForm;
        form.querySelector('input[name="status"]').value = 'approved';
        form.submit();
    },

    rejectApplication(id) {
        const form = this.$refs.statusForm;
        form.querySelector('input[name="status"]').value = 'rejected';
        form.submit();
    },
}));

Alpine.start();
