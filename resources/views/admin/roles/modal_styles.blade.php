<style>
    /* MODAL STYLES (Popup) - Framework-Agnostic CSS */
    .modal-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background-color: rgba(0, 0, 0, 0.7); z-index: 1000;
        display: none; justify-content: center; align-items: center;
        opacity: 0; transition: opacity 0.3s ease-in-out;
    }
    .modal-overlay.active { opacity: 1; display: flex; }
    .modal-content {
        background: white; padding: 25px; border-radius: 8px;
        width: 90%; max-width: 800px; max-height: 90vh; overflow-y: auto;
        position: relative; box-shadow: 0 5px 25px rgba(0,0,0,0.8);
        transform: scale(0.9); transition: transform 0.3s ease-out;
    }
    .modal-overlay.active .modal-content { transform: scale(1); }
    .modal-close { 
        position: absolute; top: 10px; right: 20px; font-size: 24px; 
        cursor: pointer; color: #aaa; text-decoration: none; 
    }
    
    /* Content Specific Styles */
    .permission-grid { 
        display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); 
        gap: 20px; margin-top: 15px; 
    }
    .module-card { border: 1px solid #e9ecef; padding: 15px; border-radius: 6px; background-color: #fff; }
    .module-title { font-weight: bold; margin-bottom: 10px; border-bottom: 2px solid #e9ecef; padding-bottom: 5px; color: #007bff; text-transform: capitalize; }
    .table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    .table th, .table td { border: 1px solid #eee; padding: 12px; text-align: left; }
    .tag { display: inline-block; padding: 5px 10px; border-radius: 15px; font-size: 0.8em; margin-right: 5px; background-color: #17a2b8; color: white; }

</style>