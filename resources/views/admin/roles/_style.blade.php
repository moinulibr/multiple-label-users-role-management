<style>
.role-page { padding: 20px; font-family: 'Roboto', sans-serif; }
.role-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
.role-header h2 { margin: 0; font-size: 20px; }

.role-table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 5px; overflow: hidden; }
.role-table th, .role-table td { padding: 10px; border-bottom: 1px solid #eee; text-align: left; }
.role-table th { background: #f4f4f4; text-transform: uppercase; font-size: 13px; }
.role-table .badge { background: #27ae60; color: #fff; padding: 3px 7px; border-radius: 3px; font-size: 12px; }
.role-table .badge-gray { background: #aaa; }

.btn { background: #3498db; color: #fff; border: none; padding: 6px 12px; cursor: pointer; border-radius: 4px; font-size: 13px; }
.btn:hover { background: #2980b9; }
.btn.small { padding: 4px 8px; font-size: 12px; }
.btn.danger { background: #e74c3c; }
.btn.primary { background: #2ecc71; }

.inline { display: inline; }

.empty { text-align: center; color: #777; padding: 15px; }

.modal { position: fixed; top: 0; left: 0; width: 100%; height: 100%; display: none; align-items: center; justify-content: center; }
.modal.show { display: flex; }
.modal-overlay { position: absolute; width: 100%; height: 100%; background: rgba(0,0,0,0.5); }
.modal-box { background: #fff; width: 600px; max-height: 90vh; overflow-y: auto; border-radius: 6px; z-index: 10; position: relative; animation: fadeIn .3s ease; }
.modal-header { padding: 10px 15px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; }
.modal-body { padding: 15px; }

.close-btn { background: transparent; border: none; font-size: 20px; cursor: pointer; }
.form-group { margin-bottom: 15px; }
.form-group label { display: block; margin-bottom: 5px; font-weight: 600; }
.form-group input[type="text"] { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; }

.permission-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 10px; }
.module { background: #fafafa; border: 1px solid #eee; padding: 8px; border-radius: 4px; }
.module strong { display: block; margin-bottom: 5px; }
.perm { display: block; font-size: 13px; }

.form-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 15px; }
.loading { text-align: center; padding: 40px; color: #555; }
.error { color: #e74c3c; text-align: center; }

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
