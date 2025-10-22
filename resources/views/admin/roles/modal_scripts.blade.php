<div id="ajax-modal-overlay" class="modal-overlay">
    <div class="modal-content">
        <a href="#" id="modal-close-btn" class="modal-close">&times;</a>
        <div id="modal-body-content">
            <p style="text-align: center; color: #555;">Loading...</p>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const mainContentArea = document.getElementById('main-ajax-content-area');
        const modalOverlay = document.getElementById('ajax-modal-overlay');
        const modalBody = document.getElementById('modal-body-content');
        const closeBtn = document.getElementById('modal-close-btn');
        
        const closeModal = () => {
            modalOverlay.classList.remove('active');
            modalOverlay.style.display = 'none'; 
            modalBody.innerHTML = '<p style="text-align: center; color: #555;">Loading...</p>';
        };

        closeBtn.addEventListener('click', (e) => { e.preventDefault(); closeModal(); });
        modalOverlay.addEventListener('click', (e) => {
            if (e.target === modalOverlay) { closeModal(); }
        });
        
        // --- Core Content Loader Function ---
        const loadContent = (url, isModal = false) => {
            const container = isModal ? modalBody : mainContentArea;
            container.innerHTML = '<p style="text-align: center; padding: 20px;">Loading Content...</p>';
            
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(response => {
                    if (!response.ok) { throw new Error('Network response was not ok'); }
                    return response.text();
                })
                .then(html => {
                    container.innerHTML = html;
                    attachAllListeners(container);
                    
                    if (!isModal) {
                        // Push state for back/forward buttons
                        history.pushState(null, '', url);
                    }
                })
                .catch(error => {
                    container.innerHTML = `<p style="color:red; text-align:center; padding: 20px;">Error loading content. Please check permissions or server response.</p>`;
                    console.error('Fetch error:', error);
                });
        };
        
        // --- Listener Functions ---
        
        const handleMainLinkClick = function(e) {
            e.preventDefault();
            loadContent(this.getAttribute('href'));
        };

        const handleModalLinkClick = function(e) {
            e.preventDefault();
            const url = this.getAttribute('href');
            modalOverlay.style.display = 'flex';
            setTimeout(() => modalOverlay.classList.add('active'), 10);
            loadContent(url, true); // Load into modal
        };
        
        const handleDeleteSubmit = function(e) {
            e.preventDefault();
            if (!confirm('Are you sure you want to delete this item?')) { return; }
            
            const form = this;
            fetch(form.action, {
                method: 'POST',
                body: new FormData(form)
            })
            .then(response => {
                if (response.redirected) {
                    window.location.href = response.url; // Reload full page to show session message
                    return;
                }
                // If using AJAX only, you would handle the response and reload content here
            })
            .catch(error => console.error('Delete error:', error));
        };
        
        const handleModalFormSubmit = function(e) {
            e.preventDefault();
            const form = this;
            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            
            submitBtn.textContent = 'Saving...';
            submitBtn.disabled = true;

            fetch(form.action, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (response.status === 422) {
                    return response.json().then(data => {
                        alert('Validation failed! Check console for details.');
                        submitBtn.textContent = originalText;
                        submitBtn.disabled = false;
                        return Promise.reject('Validation failed');
                    });
                }
                return response.json(); 
            })
            .then(data => {
                if (data && data.success && data.redirect) {
                    closeModal();
                    // Load the index page content after successful form submission
                    loadContent(data.redirect); 
                }
            })
            .catch(error => {
                if(error !== 'Validation failed') {
                    alert('An unexpected error occurred.');
                }
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
        };
        
        // --- Attach ALL Listeners ---
        function attachAllListeners(container) {
            // 1. Sidebar and Index links (Full Content Reload)
            container.querySelectorAll('.content-load-link').forEach(link => {
                link.removeEventListener('click', handleMainLinkClick);
                link.addEventListener('click', handleMainLinkClick);
            });
            
            // 2. Modal Links (Create/Edit forms)
            container.querySelectorAll('.open-modal-link').forEach(link => {
                link.removeEventListener('click', handleModalLinkClick);
                link.addEventListener('click', handleModalLinkClick);
            });
            
            // 3. Delete Forms
            container.querySelectorAll('.delete-form').forEach(form => {
                form.removeEventListener('submit', handleDeleteSubmit);
                form.addEventListener('submit', handleDeleteSubmit);
            });
            
            // 4. Modal Form Submission
            const modalForm = modalBody.querySelector('form');
            if (modalForm) {
                modalForm.removeEventListener('submit', handleModalFormSubmit);
                modalForm.addEventListener('submit', handleModalFormSubmit);
            }
        }
        
        // Initial listeners on page load
        attachAllListeners(mainContentArea);
    });
</script>