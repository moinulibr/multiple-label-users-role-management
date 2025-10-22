
<!-- RAW JAVASCRIPT for AJAX & Modal Logic -->

<!-- -------------------------------------- -->

<script>
document.addEventListener('DOMContentLoaded', () => {
const modal = document.getElementById('ajax-modal');
const modalBody = document.getElementById('modal-body-content');
const modalTitle = document.getElementById('modal-title');
// Targets the container div defined in index.blade.php
const contentArea = document.getElementById('main-ajax-content-area');

const messageBox = document.getElementById(&#39;ajax-message-box&#39;);
const messageText = document.getElementById(&#39;message-text&#39;);

// Get CSRF Token from the meta tag
const csrfToken = document.querySelector(&#39;meta[name=&quot;csrf-token&quot;]&#39;)?.getAttribute(&#39;content&#39;);


// --- 1. Utility Functions ---

// Function to show transient message
function showMessage(message, type = &#39;success&#39;) {
    // Remove existing classes
    messageBox.classList.remove(&#39;msg-success&#39;, &#39;msg-error&#39;);
    
    // Set new message and class
    messageText.textContent = message;
    messageBox.classList.add(type === &#39;success&#39; ? &#39;msg-success&#39; : &#39;msg-error&#39;);
    
    // Show the box with transition
    messageBox.style.display = &#39;block&#39;;
    setTimeout(() =&gt; messageBox.classList.add(&#39;show&#39;), 10); 

    // Hide after 4 seconds
    setTimeout(() =&gt; {
        messageBox.classList.remove(&#39;show&#39;);
        // Hide display: none after transition
        setTimeout(() =&gt; {
            messageBox.style.display = &#39;none&#39;;
        }, 400); 
    }, 4000);
}

// Function to clear validation errors in the form
function clearValidationErrors() {
    document.querySelectorAll(&#39;.validation-error&#39;).forEach(el =&gt; el.remove());
    document.querySelectorAll(&#39;.form-input&#39;).forEach(input =&gt; input.style.borderColor = &#39;&#39;);
}


// --- 2. Core AJAX Logic ---

// Handles form submission (Create/Update)
async function handleFormSubmission(event) {
    event.preventDefault();
    clearValidationErrors(); // Clear old errors

    const form = event.target;
    const formData = new FormData(form);
    const url = form.action;
    
    // Determine the actual HTTP method (PUT/PATCH/DELETE)
    const methodInput = formData.get(&#39;_method&#39;);
    const method = methodInput ? methodInput : form.method;

    // Loading state
    const submitButton = form.querySelector(&#39;.submit-btn&#39;);
    const originalText = submitButton.textContent;
    if (submitButton) {
        submitButton.disabled = true;
        submitButton.textContent = &#39;Processing...&#39;;
    }

    try {
        const response = await fetch(url, {
            method: method === &#39;GET&#39; ? &#39;POST&#39; : method,
            headers: {
                &#39;X-Requested-With&#39;: &#39;XMLHttpRequest&#39;,
                &#39;Accept&#39;: &#39;application/json&#39;,
                &#39;X-CSRF-TOKEN&#39;: csrfToken 
            },
            body: formData
        });

        // Handle Server Errors or Validation Errors
        if (!response.ok) {
            const errorData = await response.json();
            let errorMessage = &#39;An unexpected error occurred.&#39;;
            
            if (response.status === 422 &amp;&amp; errorData.errors) {
                // Display validation errors below fields
                Object.keys(errorData.errors).forEach(field =&gt; {
                    const input = form.querySelector(`[name=&quot;${field}&quot;], [name=&quot;${field}[]&quot;]`);
                    if (input) {
                        // Find the container group
                        const group = input.closest(&#39;.form-group&#39;) || input.closest(&#39;.form-actions&#39;) || input.closest(&#39;.role-form&#39;);
                        if (group) {
                            // Add error message
                            const errorDiv = document.createElement(&#39;div&#39;);
                            errorDiv.className = &#39;validation-error&#39;;
                            errorDiv.textContent = errorData.errors[field][0];
                            group.appendChild(errorDiv);
                            
                            // Highlight input field
                            if (input.type !== &#39;checkbox&#39;) {
                                input.style.borderColor = &#39;#dc3545&#39;;
                            }
                        }
                    }
                });
                errorMessage = &#39;Please check the form for errors.&#39;;
            } else if (errorData.message) {
                errorMessage = errorData.message;
            }
            
            showMessage(errorMessage, &#39;error&#39;);
            return; // Stop execution on error
        }

        // Successful Submission
        const result = await response.json();
        
        if (result.success) {
            showMessage(&#39;Successfully saved!&#39;, &#39;success&#39;);
            closeModal();
            
            if (result.redirect) {
                // Refresh the main content area (The Role List)
                loadContent(result.redirect, contentArea);
            }
            
        } else {
            showMessage(result.message || &#39;Operation failed on server.&#39;, &#39;error&#39;);
        }


    } catch (error) {
        console.error(&#39;Network Error:&#39;, error);
        showMessage(&#39;Network error: Could not connect to server.&#39;, &#39;error&#39;);
    } finally {
        if (submitButton) {
            submitButton.disabled = false;
            submitButton.textContent = originalText;
        }
    }
}

// Load content into a target element (used for list refresh)
async function loadContent(url, targetElement) {
    if (!targetElement) {
         window.location.href = url;
         return;
    }

    targetElement.innerHTML = &#39;&lt;div class=&quot;modal-loading-text&quot;&gt;Loading content...&lt;/div&gt;&#39;; 
    
    try {
        const response = await fetch(url, {
            headers: {
                &#39;X-Requested-With&#39;: &#39;XMLHttpRequest&#39;,
                &#39;Accept&#39;: &#39;text/html&#39;
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const html = await response.text();
        targetElement.innerHTML = html;
        
        // Re-attach listeners to the newly loaded content
        attachModalOpenListeners(targetElement);

    } catch (error) {
        targetElement.innerHTML = `&lt;div style=&quot;color: red; padding: 20px; text-align: center;&quot;&gt;Failed to load content: ${error.message}&lt;/div&gt;`;
        console.error(&#39;Content loading error:&#39;, error);
    }
}

// --- 3. Modal Control Functions ---

// Fetches modal content (form_content.blade.php)
async function openModal(url) {
    modalTitle.textContent = &#39;Loading...&#39;;
    modalBody.innerHTML = &#39;&lt;div class=&quot;modal-loading-text&quot;&gt;Fetching form data...&lt;/div&gt;&#39;;
    
    // Show the modal backdrop
    modal.style.display = &#39;flex&#39;; 
    setTimeout(() =&gt; modal.classList.add(&#39;active&#39;), 10); 

    try {
        const response = await fetch(url, {
            headers: { &#39;X-Requested-With&#39;: &#39;XMLHttpRequest&#39; }
        });

        if (!response.ok) {
            modalBody.innerHTML = `&lt;div class=&quot;modal-loading-text&quot; style=&quot;color:red;&quot;&gt;Could not load form. Status: ${response.status}&lt;/div&gt;`;
            modalTitle.textContent = &#39;Error&#39;;
            return;
        }

        const html = await response.text();
        modalBody.innerHTML = html;
        
        // Find and extract the title from the loaded content (h3)
        const formTitle = modalBody.querySelector(&#39;.form-title&#39;)?.textContent || &#39;Form&#39;;
        modalTitle.textContent = formTitle;
        
        // Attach form submission handler
        const form = modalBody.querySelector(&#39;.role-form&#39;);
        if (form) {
            form.removeEventListener(&#39;submit&#39;, handleFormSubmission); 
            form.addEventListener(&#39;submit&#39;, handleFormSubmission);
        }

    } catch (error) {
        modalBody.innerHTML = `&lt;div class=&quot;modal-loading-text&quot; style=&quot;color:red;&quot;&gt;An error occurred while loading the modal.&lt;/div&gt;`;
        console.error(&#39;Modal loading error:&#39;, error);
    }
}

function closeModal() {
    modal.classList.remove(&#39;active&#39;);
    clearValidationErrors(); // Clear errors on close
    // Delay display: none until animation finishes
    setTimeout(() =&gt; {
        modal.style.display = &#39;none&#39;;
    }, 300); 
    // Reset modal content
    modalBody.innerHTML = &#39;&lt;div class=&quot;modal-loading-text&quot;&gt;Fetching form data...&lt;/div&gt;&#39;; 
    modalTitle.textContent = &#39;Loading...&#39;;
}

// --- 4. Event Listeners ---

function handleModalOpenClick(event) {
    event.preventDefault();
    openModal(event.currentTarget.href);
}

// Attach listeners to all links with class .open-modal-link
function attachModalOpenListeners(container) {
    container.querySelectorAll(&#39;.open-modal-link&#39;).forEach(link =&gt; {
        // Ensure no duplicate listeners
        link.removeEventListener(&#39;click&#39;, handleModalOpenClick); 
        link.addEventListener(&#39;click&#39;, handleModalOpenClick);
    });
}

// 1. Initial listener for open-modal links (for links existing on page load)
attachModalOpenListeners(document);

// 2. Close modal listeners (Button and Backdrop click)
document.querySelector(&#39;.close-modal-btn&#39;)?.addEventListener(&#39;click&#39;, closeModal);
modal.addEventListener(&#39;click&#39;, (e) =&gt; {
    if (e.target === modal) {
        closeModal();
    }
});

// 3. Simple confirm for delete action (Non-AJAX for simplicity in this module)
document.body.addEventListener(&#39;submit&#39;, (e) =&gt; {
    if (e.target.classList.contains(&#39;delete-form&#39;)) {
        // Note: This delete action reloads the page (non-AJAX) as per the controller structure.
        // For a fully AJAX delete, you would need to modify the controller&#39;s destroy method
        // to return JSON and handle the fetch request here.
        // Example of a minimal AJAX delete handling:
        /*
        e.preventDefault();
        if (confirm(&#39;Are you sure you want to delete this role?&#39;)) {
            // Perform AJAX Delete...
        }
        */
    }
});


});
</script>