<!-- -------------------------------------- -->

<!-- RAW CSS STYLES for Role Module & Modal -->

<!-- -------------------------------------- -->

<style>
/* Base Layouts for Module Content */
.role-management-container {
padding: 20px;
background-color: #fff;
border-radius: 8px;
box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.role-header-actions {
display: flex;
justify-content: space-between;
align-items: center;
margin-bottom: 25px;
border-bottom: 2px solid #eee;
padding-bottom: 15px;
}

.role-header-actions h2 {
margin: 0;
font-size: 1.8rem;
color: #333;
}

/* Custom Button Style */
.module-btn {
padding: 10px 18px;
border: none;
border-radius: 6px;
cursor: pointer;
text-decoration: none;
display: inline-block;
font-weight: bold;
background-color: #007bff;
color: white;
transition: background-color 0.2s, transform 0.1s;
text-align: center;
font-size: 1rem;
}

.module-btn:hover {
background-color: #0056b3;
transform: translateY(-1px);
}

.submit-btn {
width: 100%;
margin-top: 20px;
}

/* Table Styles */
.roles-table {
width: 100%;
border-collapse: collapse;
margin-top: 15px;
font-size: 0.95rem;
}

.roles-table th, .roles-table td {
border: 1px solid #e9ecef;
padding: 12px 15px;
text-align: left;
}

.roles-table th {
background-color: #f8f9fa;
color: #495057;
font-weight: 600;
}

.roles-table tbody tr:hover {
background-color: #f1f1f1;
}

.action-cell {
white-space: nowrap;
}

.action-link {
padding: 5px 10px;
margin-right: 8px;
border-radius: 4px;
text-decoration: none;
font-weight: 500;
transition: background-color 0.2s;
font-size: 0.9rem;
}

.edit-link {
color: #007bff;
border: 1px solid #007bff;
}
.edit-link:hover {
background-color: #007bff;
color: white;
}

.delete-link {
color: #dc3545;
border: 1px solid #dc3545;
background: none;
cursor: pointer;
}
.delete-link:hover {
background-color: #dc3545;
color: white;
}

.empty-state {
text-align: center;
padding: 40px;
border: 2px dashed #ccc;
color: #888;
border-radius: 8px;
font-size: 1.1rem;
}

/* Form Styles */
.form-title {
margin-top: 0;
margin-bottom: 20px;
font-size: 1.5rem;
color: #007bff;
}

.form-group {
margin-bottom: 15px;
}

.form-group label {
display: block;
margin-bottom: 5px;
font-weight: 600;
color: #555;
}

.form-input {
width: 100%;
padding: 10px;
border: 1px solid #ccc;
border-radius: 4px;
box-sizing: border-box;
font-size: 1rem;
}

.form-separator {
margin: 25px 0;
border: 0;
border-top: 1px solid #eee;
}

/* Permission Grid Styles */
.permission-heading {
margin-bottom: 15px;
font-size: 1.2rem;
border-left: 4px solid #007bff;
padding-left: 10px;
}

.select-all-group {
margin-bottom: 20px;
font-weight: bold;
padding: 10px;
background-color: #f9f9f9;
border: 1px solid #eee;
border-radius: 6px;
}

.permission-grid {
display: grid;
grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
gap: 20px;
}

.module-card {
border: 1px solid #ddd;
padding: 15px;
border-radius: 8px;
background-color: #fefefe;
}

.module-title {
margin-top: 0;
margin-bottom: 10px;
font-size: 1.1rem;
color: #333;
border-bottom: 1px dashed #eee;
padding-bottom: 5px;
}

.checkbox-group {
margin-bottom: 8px;
}

.permission-checkbox {
margin-right: 8px;
transform: scale(1.1);
}

.permission-label {
cursor: pointer;
user-select: none;
}

/* Responsive adjustments */
@media (max-width: 600px) {
.role-header-actions {
flex-direction: column;
align-items: flex-start;
}
.role-header-actions .module-btn {
margin-top: 15px;
width: 100%;
}
.permission-grid {
grid-template-columns: 1fr;
}
}

/* -------------------------------------- /
/ Custom AJAX Modal Styles (RAW) /
/ -------------------------------------- /
.custom-modal-backdrop {
display: none; / Hidden by default /
position: fixed;
z-index: 10000; / High Z-index */
left: 0;
top: 0;
width: 100%;
height: 100%;
overflow: auto;
background-color: rgba(0,0,0,0.6);
justify-content: center;
align-items: center;
opacity: 0;
transition: opacity 0.3s ease-in-out;
}

.custom-modal-backdrop.active {
display: flex;
opacity: 1;
}

.custom-modal-content {
background-color: #fefefe;
padding: 0;
border-radius: 12px;
box-shadow: 0 8px 30px rgba(0,0,0,0.4);
width: 95%;
max-width: 650px;
margin: 20px; /* Space from edges on mobile /
animation: dropin 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); / Springy effect /
max-height: 90vh; / Max height for scrolling */
overflow-y: auto;
}

.custom-modal-header {
padding: 15px 20px;
background-color: #007bff;
color: white;
border-top-left-radius: 12px;
border-top-right-radius: 12px;
display: flex;
justify-content: space-between;
align-items: center;
position: sticky;
top: 0;
z-index: 10;
}

.modal-loading-text {
padding: 40px;
text-align: center;
color: #666;
font-style: italic;
}

.custom-modal-body {
padding: 25px;
}

.close-modal-btn {
color: #fff;
font-size: 30px;
font-weight: 300;
cursor: pointer;
transition: color 0.3s;
line-height: 1;
}

.close-modal-btn:hover,
.close-modal-btn:focus {
color: #cce0ff;
}

/* Animation */
@keyframes dropin {
from {transform: scale(0.9) translateY(-10px);}
to {transform: scale(1) translateY(0);}
}

/* -------------------------------------- /
/ Message Box for Notifications /
/ -------------------------------------- */
#ajax-message-box {
display: none;
position: fixed;
top: 20px;
right: 20px;
z-index: 10001;
padding: 12px 20px;
border-radius: 6px;
box-shadow: 0 4px 15px rgba(0,0,0,0.2);
transition: opacity 0.4s ease-in-out, transform 0.4s;
opacity: 0;
min-width: 280px;
color: white;
font-weight: 500;
transform: translateX(100%);
}
#ajax-message-box.show {
opacity: 1;
transform: translateX(0);
}
.msg-success { background-color: #28a745; }
.msg-error { background-color: #dc3545; }

/* Validation Error Styling (within form) */
.validation-error {
color: #dc3545;
font-size: 0.85rem;
margin-top: 5px;
font-weight: 500;
}
</style>

