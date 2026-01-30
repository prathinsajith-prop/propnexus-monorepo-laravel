// Layout Modal JavaScript
// Add this to your application's JavaScript bundle

document.addEventListener('DOMContentLoaded', function() {
    // Open modal
    document.querySelectorAll('[data-modal-open]').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const modalId = this.getAttribute('data-modal-open');
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }
        });
    });

    // Close modal
    document.querySelectorAll('[data-modal-close]').forEach(button => {
        button.addEventListener('click', function() {
            const modalId = this.getAttribute('data-modal-close');
            const modal = document.getElementById(modalId);
            if (modal) {
                closeModal(modal);
            }
        });
    });

    // Close on backdrop click
    document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
        backdrop.addEventListener('click', function() {
            const modal = this.closest('.layout-modal');
            if (modal) {
                closeModal(modal);
            }
        });
    });

    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const openModal = document.querySelector('.layout-modal[style*="display: flex"]');
            if (openModal) {
                closeModal(openModal);
            }
        }
    });

    // Submit modal
    document.querySelectorAll('[data-modal-submit]').forEach(button => {
        button.addEventListener('click', function() {
            const modalId = this.getAttribute('data-modal-submit');
            const modal = document.getElementById(modalId);
            const form = modal.querySelector('.modal-form');
            
            if (form.checkValidity()) {
                // Get form data
                const formData = new FormData(form);
                const data = Object.fromEntries(formData.entries());
                
                // Get action URL from button's parent action
                const actionButton = document.querySelector(`[data-modal-open="${modalId}"]`);
                const url = actionButton ? actionButton.getAttribute('data-action-url') : null;
                const method = actionButton ? (actionButton.getAttribute('data-action-method') || 'POST') : 'POST';
                
                if (url) {
                    // Submit via AJAX
                    fetch(url, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(data)
                    })
                    .then(response => response.json())
                    .then(result => {
                        // Handle success
                        closeModal(modal);
                        form.reset();
                        
                        // Trigger custom event
                        document.dispatchEvent(new CustomEvent('modal-submitted', {
                            detail: { modalId, data, result }
                        }));
                        
                        // Reload page or show success message
                        if (result.redirect) {
                            window.location.href = result.redirect;
                        } else if (result.reload) {
                            window.location.reload();
                        } else if (result.message) {
                            showMessage(result.message, 'success');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showMessage('An error occurred. Please try again.', 'error');
                    });
                }
            } else {
                form.reportValidity();
            }
        });
    });

    function closeModal(modal) {
        modal.style.display = 'none';
        document.body.style.overflow = '';
        
        // Reset form
        const form = modal.querySelector('.modal-form');
        if (form) {
            form.reset();
        }
    }

    function showMessage(message, type = 'info') {
        // You can customize this to use your preferred notification system
        alert(message);
    }
});
