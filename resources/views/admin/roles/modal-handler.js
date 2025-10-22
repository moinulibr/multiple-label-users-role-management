// public/js/admin/roles/modal-handler.js

document.addEventListener('DOMContentLoaded', function () {
    const modalOverlay = document.getElementById('ajax-modal-overlay');
    const modalBody = document.getElementById('modal-body-content');
    const closeBtn = document.getElementById('modal-close-btn');
    
    // Function to close the modal
    const closeModal = () => {
        modalOverlay.classList.remove('active');
        modalOverlay.style.display = 'none';
        modalBody.innerHTML = '<p style="text-align: center; color: #555;">Loading...</p>';
    };

    closeBtn.addEventListener('click', (e) => { e.preventDefault(); closeModal(); });
    modalOverlay.addEventListener('click', (e) => {
        if (e.target === modalOverlay) { closeModal(); }
    });
    
    // 1. Open modal and fetch content function
    document.querySelectorAll('.open-modal-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.getAttribute('href');
            
            modalOverlay.style.display = 'flex';
            setTimeout(() => modalOverlay.classList.add('active'), 10);
            
            fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => {
                if (!response.ok) { throw new Error('Network response was not ok'); }
                return response.text();
            })
            .then(html => {
                modalBody.innerHTML = html;
                attachFormListener(); 
            })
            .catch(error => {
                modalBody.innerHTML = `<p style="color:red; text-align:center;">Error loading content: ${error.message}. Check console for details.</p>`;
                console.error('Fetch error:', error);
            });
        });
    });
    
    // 2. Handle Form Submission via AJAX (Redirect on success)
    function attachFormListener() {
        const form = modalBody.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const actionUrl = this.getAttribute('action');
                
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.textContent;
                submitBtn.textContent = 'Saving...';
                submitBtn.disabled = true;

                fetch(actionUrl, {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (response.status === 422) {
                        return response.json().then(data => {
                            alert('Validation failed! Please check the form.');
                            console.error('Validation Errors:', data.errors);
                            submitBtn.textContent = originalText;
                            submitBtn.disabled = false;
                            return Promise.reject('Validation failed');
                        });
                    }
                    if (response.redirected) {
                        window.location.href = response.url;
                        return;
                    }
                    return response.json(); 
                })
                .then(data => {
                    if (data && data.success && data.redirect) {
                        window.location.href = data.redirect; 
                    }
                })
                .catch(error => {
                    console.error('Submission error:', error);
                    if(error !== 'Validation failed') {
                        alert('An unexpected error occurred. Please refresh.');
                    }
                });
            });
        }
    }
});