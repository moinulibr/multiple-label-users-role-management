<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roles List | Permission Manager</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        /* [Core CSS Content Here] */
        /* To ensure the file is self-contained, the Core CSS is repeated here. */
        
        /* Base Styles */
        .curpm-body {
            font-family: 'Inter', sans-serif;
            background-color: #eef1f7; /* Very light, cool background */
            color: #2c3e50;
            padding: 40px 20px;
        }

        /* Container and Structure */
        .curpm-container {
            max-width: 1280px;
            margin: 0 auto;
            background: #ffffff;
            padding: 35px;
            border-radius: 16px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
        }

        .curpm-header {
            margin-bottom: 25px;
        }

        .curpm-header h1 {
            font-size: 2rem;
            font-weight: 800;
            color: #3159A3; /* Deep Blue Primary */
            margin-bottom: 5px;
        }

        .curpm-header p {
            font-size: 1rem;
            color: #7f8c8d;
        }

        /* --- Form & Input Elements --- */
        .curpm-label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #3159A3;
            font-size: 0.95rem;
        }

        .curpm-input, .curpm-textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #dcdfe6;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s, box-shadow 0.3s;
            background-color: #fcfcfc;
        }

        .curpm-input:focus, .curpm-textarea:focus {
            border-color: #3159A3;
            outline: none;
            box-shadow: 0 0 0 3px rgba(49, 89, 163, 0.15);
        }

        /* --- Button Styles --- */
        .curpm-btn {
            background: linear-gradient(135deg, #3159A3 0%, #4a77cc 100%);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 5px 10px rgba(49, 89, 163, 0.4);
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .curpm-btn:hover {
            box-shadow: 0 3px 8px rgba(49, 89, 163, 0.6);
            transform: translateY(-1px);
        }

        .curpm-btn-secondary {
            background: #e0e0e0;
            color: #555;
            box-shadow: none;
        }

        .curpm-btn-secondary:hover {
            background: #d0d0d0;
            transform: translateY(-1px);
        }

        .curpm-btn-action { /* Small action buttons for table rows */
            padding: 6px 10px;
            font-size: 0.85rem;
            font-weight: 600;
            border-radius: 6px;
            margin-left: 5px;
            text-transform: capitalize;
            color: white;
            border: none;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: opacity 0.2s;
        }
        .curpm-btn-action:hover {
            opacity: 0.8;
        }

        .curpm-btn-edit { background-color: #f39c12; }
        .curpm-btn-delete { background-color: #c0392b; }


        /* --- Index Page Table Styles (curpm-index-table) --- */
        .curpm-actions-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding: 15px;
            background-color: #f8f9fb;
            border-radius: 10px;
            border: 1px solid #e0e0e0;
        }

        .curpm-search-bar {
            padding: 10px 15px;
            border: 1px solid #dcdfe6;
            border-radius: 8px;
            font-size: 1rem;
            width: 300px;
        }

        .curpm-index-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            border-radius: 10px;
            overflow: hidden;
        }

        .curpm-index-table th, .curpm-index-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #f0f0f0;
        }

        .curpm-index-table th {
            background-color: #3159A3;
            color: white;
            font-weight: 600;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .curpm-index-table tr:last-child td {
            border-bottom: none;
        }

        .curpm-index-table tbody tr:hover {
            background-color: #f5f8ff;
        }

        .curpm-role-slug {
            font-family: monospace;
            background-color: #e8eaf6;
            color: #3949ab;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
        }

        .curpm-role-status-active {
            background-color: #e6f7ee;
            color: #27ae60;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        /* Pagination (Simple Demo) */
        .curpm-pagination {
            margin-top: 25px;
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .curpm-pagination-link {
            display: block;
            padding: 8px 14px;
            border: 1px solid #dcdfe6;
            border-radius: 6px;
            text-decoration: none;
            color: #3159A3;
            transition: background-color 0.2s, color 0.2s;
        }

        .curpm-pagination-link:hover, .curpm-pagination-link.active {
            background-color: #3159A3;
            color: white;
            border-color: #3159A3;
        }
        
        /* Responsiveness */
        @media (max-width: 1000px) {
            .curpm-grid { grid-template-columns: 1fr; }
            .curpm-permission-panel { border-left: none; padding-left: 0; padding-top: 30px; margin-top: 30px; border-top: 1px solid #e0e0e0; }
        }
        @media (max-width: 600px) {
            .curpm-container { padding: 20px; }
            .curpm-actions-bar {
                flex-direction: column;
                align-items: stretch;
            }
            .curpm-search-bar {
                width: 100%;
                margin-bottom: 15px;
            }
            .curpm-index-table thead {
                display: none; /* Hide table headers on mobile */
            }
            .curpm-index-table, .curpm-index-table tbody, .curpm-index-table tr, .curpm-index-table td {
                display: block;
                width: 100%;
            }
            .curpm-index-table tr {
                margin-bottom: 15px;
                border: 1px solid #dcdfe6;
                border-radius: 10px;
            }
            .curpm-index-table td {
                text-align: right;
                padding-left: 50%;
                position: relative;
            }
            .curpm-index-table td::before {
                content: attr(data-label);
                position: absolute;
                left: 10px;
                width: 45%;
                padding-right: 10px;
                white-space: nowrap;
                text-align: left;
                font-weight: 600;
                color: #3159A3;
            }
        }
    </style>
</head>

<body class="curpm-body">

    <div class="curpm-container">
        <header class="curpm-header">
            <h1>Roles List</h1>
            <p>View all user roles in the system. You can create new roles and edit existing ones here.</p>
        </header>

        <div class="curpm-actions-bar">
            <input type="text" class="curpm-search-bar" id="curpm-search-input" placeholder="Search by Role Name or Slug...">
            <a href="role_edit.html?mode=create" class="curpm-btn curpm-btn-create">
                Create New Role
            </a>
        </div>

        <table class="curpm-index-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Role Name</th>
                    <th>Slug</th>
                    <th>User Count</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td data-label="#">1</td>
                    <td data-label="Role Name">Administrator</td>
                    <td data-label="Slug"><span class="curpm-role-slug">administrator</span></td>
                    <td data-label="User Count">5</td>
                    <td data-label="Status"><span class="curpm-role-status-active">Active</span></td>
                    <td data-label="Action">
                        <button class="curpm-btn-action curpm-btn-edit" onclick="window.location.href='role_edit.html?id=1'">Edit</button>
                        <button class="curpm-btn-action curpm-btn-delete" onclick="curpm_deleteRole(1, 'Administrator')">Delete</button>
                    </td>
                </tr>
                <tr>
                    <td data-label="#">2</td>
                    <td data-label="Role Name">Content Manager</td>
                    <td data-label="Slug"><span class="curpm-role-slug">content-manager</span></td>
                    <td data-label="User Count">12</td>
                    <td data-label="Status"><span class="curpm-role-status-active">Active</span></td>
                    <td data-label="Action">
                        <button class="curpm-btn-action curpm-btn-edit" onclick="window.location.href='role_edit.html?id=2'">Edit</button>
                        <button class="curpm-btn-action curpm-btn-delete" onclick="curpm_deleteRole(2, 'Content Manager')">Delete</button>
                    </td>
                </tr>
                <tr>
                    <td data-label="#">3</td>
                    <td data-label="Role Name">General User</td>
                    <td data-label="Slug"><span class="curpm-role-slug">user</span></td>
                    <td data-label="User Count">345</td>
                    <td data-label="Status"><span class="curpm-role-status-active">Active</span></td>
                    <td data-label="Action">
                        <button class="curpm-btn-action curpm-btn-edit" onclick="window.location.href='role_edit.html?id=3'">Edit</button>
                        <button class="curpm-btn-action curpm-btn-delete" onclick="curpm_deleteRole(3, 'User')">Delete</button>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="curpm-pagination">
            <a href="#" class="curpm-pagination-link">&laquo; Previous</a>
            <a href="#" class="curpm-pagination-link active">1</a>
            <a href="#" class="curpm-pagination-link">2</a>
            <a href="#" class="curpm-pagination-link">3</a>
            <a href="#" class="curpm-pagination-link">Next &raquo;</a>
        </div>
    </div>
    
    <script>
        // Delete confirmation (Using alert for demo purposes; custom modal recommended)
        function curpm_deleteRole(id, name) {
            console.log(`Deletion requested: Role ID ${id}, Name: ${name}`);
            // In a real project, use a custom modal for confirmation.
            alert(`Are you sure you want to delete the role "${name}"?`);
        }
        
        // Search Function (Simple Demo)
        document.getElementById('curpm-search-input').addEventListener('keyup', function() {
            const filter = this.value.toUpperCase();
            const table = document.querySelector('.curpm-index-table tbody');
            const rows = table.getElementsByTagName('tr');
            
            for (let i = 0; i < rows.length; i++) {
                // Role Name (index 1) and Slug (index 2) columns
                const nameCol = rows[i].getElementsByTagName('td')[1];
                const slugCol = rows[i].getElementsByTagName('td')[2];
                
                if (nameCol || slugCol) {
                    const nameText = nameCol.textContent || nameCol.innerText;
                    const slugText = slugCol.textContent || slugCol.innerText;
                    
                    if (nameText.toUpperCase().indexOf(filter) > -1 || slugText.toUpperCase().indexOf(filter) > -1) {
                        rows[i].style.display = "";
                    } else {
                        rows[i].style.display = "none";
                    }
                }
            }
        });
    </script>
</body>
</html>
