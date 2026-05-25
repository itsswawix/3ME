<?php
/**
 * Attendance Upload Portal
 * Allows companies to upload attendance files and redirects to main attendance system
 */

$pageTitle = "Attendance Upload Portal";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&family=Outfit:wght@400;500;600&display=swap" rel="stylesheet">
    <!-- SheetJS for Excel/CSV parsing -->
    <script src="https://cdn.sheetjs.com/xlsx-0.20.1/package/dist/xlsx.full.min.js"></script>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: radial-gradient(circle at 20% 30%, #eef2ff, #e0e7ff);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            font-size: 13px;
        }

        .portal-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #10b981;
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 32px;
            transition: color 0.2s;
        }

        .back-link:hover {
            color: #059669;
        }

        .portal-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .portal-header h1 {
            font-size: 22px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 12px;
        }

        .portal-header p {
            font-size: 14px;
            color: #64748b;
        }

        .portal-card {
            background: rgba(255,255,255,0.85);
            backdrop-filter: blur(8px);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 8px 20px -8px rgba(0,0,0,0.05);
            border: 1px solid rgba(255,255,255,0.7);
            margin-bottom: 32px;
        }

        .upload-zone {
            border: 2px dashed #cbd5e1;
            border-radius: 16px;
            padding: 60px 40px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            background: rgba(248, 250, 252, 0.5);
        }

        .upload-zone:hover,
        .upload-zone.dragover {
            border-color: #10b981;
            background: rgba(16, 185, 129, 0.05);
        }

        .upload-zone.has-file {
            border-color: #10b981;
            background: rgba(16, 185, 129, 0.1);
        }

        .upload-icon {
            font-size: 48px;
            color: #94a3b8;
            margin-bottom: 16px;
        }

        .upload-zone.has-file .upload-icon {
            color: #10b981;
        }

        .upload-text {
            font-size: 18px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }

        .upload-hint {
            color: #64748b;
            margin-bottom: 20px;
        }

        .file-types {
            display: flex;
            justify-content: center;
            gap: 16px;
            margin-top: 20px;
        }

        .file-type {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 8px 12px;
            background: rgba(255,255,255,0.8);
            border-radius: 20px;
            font-size: 12px;
            color: #64748b;
        }

        .file-type i {
            color: #10b981;
        }

        .selected-file {
            display: none;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 12px;
            padding: 20px;
            margin-top: 20px;
        }

        .selected-file.show {
            display: block;
        }

        .file-info {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .file-icon {
            width: 48px;
            height: 48px;
            background: #10b981;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
        }

        .file-details h4 {
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 4px;
        }

        .file-details p {
            color: #64748b;
            font-size: 13px;
        }

        .file-actions {
            margin-left: auto;
            display: flex;
            gap: 8px;
        }

        .btn {
            padding: 8px 16px;
            border-radius: 20px;
            border: none;
            font-weight: 500;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(145deg, #10b981, #059669);
            color: white;
        }

        .btn-primary:hover {
            background: linear-gradient(145deg, #059669, #047857);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: white;
            color: #374151;
            border: 1px solid #e2e8f0;
        }

        .btn-secondary:hover {
            background: #f8fafc;
        }

        .btn-danger {
            background: #ef4444;
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 24px;
            margin-top: 40px;
        }

        .info-card {
            background: rgba(255,255,255,0.9);
            padding: 10px 18px;
            border-radius: 20px;
            border: 1px solid rgba(255,255,255,0.6);
        }

        .info-card h3 {
            font-size: 14px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .info-card h3 i {
            color: #10b981;
            font-size: 14px;
        }

        .info-card ul {
            list-style: none;
            color: #64748b;
        }

        .info-card li {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 2px 0;
            font-size: 11px;
        }

        .info-card li i {
            color: #10b981;
            font-size: 10px;
            width: 12px;
        }

        .progress-section {
            display: none;
            margin-top: 24px;
        }

        .progress-section.show {
            display: block;
        }

        .progress-bar {
            width: 100%;
            height: 8px;
            background: #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 12px;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #10b981, #059669);
            border-radius: 4px;
            transition: width 0.3s ease;
        }

        .progress-text {
            text-align: center;
            color: #64748b;
            font-size: 14px;
        }

        .recent-uploads {
            margin-top: 40px;
        }

        .recent-uploads h3 {
            font-size: 16px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .recent-uploads h3 i {
            color: #10b981;
            font-size: 14px;
        }

        .upload-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 16px;
            background: rgba(255,255,255,0.9);
            border-radius: 20px;
            margin-bottom: 6px;
            border: 1px solid rgba(255,255,255,0.6);
        }

        .upload-item i {
            color: #10b981;
            font-size: 14px;
            width: 16px;
        }

        .upload-item-info {
            flex: 1;
        }

        .upload-item-info h4 {
            font-weight: 500;
            color: #0f172a;
            font-size: 12px;
            margin-bottom: 2px;
        }

        .upload-item-info p {
            color: #64748b;
            font-size: 10px;
        }

        .upload-status {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 500;
        }

        .status-success {
            background: #dcfce7;
            color: #16a34a;
        }

        .status-processing {
            background: #fef3c7;
            color: #b45309;
        }

        @media (max-width: 768px) {
            .portal-container {
                padding: 20px;
            }
            
            .portal-card {
                padding: 24px;
            }
            
            .upload-zone {
                padding: 40px 20px;
            }
            
            .file-info {
                flex-direction: column;
                text-align: center;
            }
            
            .file-actions {
                margin-left: 0;
                margin-top: 16px;
            }
        }

        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <div class="portal-container">
        <!-- Back Link -->
        <a href="index.php" class="back-link">
            <i class="fas fa-arrow-left"></i>
            Back to Portal
        </a>

        <!-- Header -->
        <div class="portal-header">
            <h1><i class="fas fa-cloud-upload-alt" style="color: #10b981; margin-right: 12px;"></i>Attendance Upload Portal</h1>
            <p>Upload attendance data files for processing and import</p>
        </div>

        <!-- Upload Section -->
        <div class="portal-card">
            <div class="upload-zone" id="uploadZone" onclick="document.getElementById('fileInput').click()">
                <div class="upload-icon">
                    <i class="fas fa-cloud-upload-alt" id="uploadIcon"></i>
                </div>
                <div class="upload-text" id="uploadText">Drop your attendance file here</div>
                <div class="upload-hint" id="uploadHint">or click to browse and select a file</div>
                
                <div class="file-types">
                    <div class="file-type">
                        <i class="fas fa-file-csv"></i>
                        CSV Files
                    </div>
                    <div class="file-type">
                        <i class="fas fa-file-excel"></i>
                        Excel Files
                    </div>
                    <div class="file-type">
                        <i class="fas fa-file"></i>
                        Biometric Data
                    </div>
                </div>
            </div>

            <!-- Selected File Info -->
            <div class="selected-file" id="selectedFile">
                <div class="file-info">
                    <div class="file-icon">
                        <i class="fas fa-file" id="fileIcon"></i>
                    </div>
                    <div class="file-details">
                        <h4 id="fileName">filename.csv</h4>
                        <p id="fileSize">0 KB • Ready to upload</p>
                    </div>
                    <div class="file-actions">
                        <button class="btn btn-primary btn-sm" onclick="previewFile()">
                            <i class="fas fa-eye"></i>
                            Preview & Upload
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="removeFile()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Progress Section -->
            <div class="progress-section" id="progressSection">
                <div class="progress-bar">
                    <div class="progress-fill" id="progressFill" style="width: 0%"></div>
                </div>
                <div class="progress-text" id="progressText">Uploading... 0%</div>
            </div>
        </div>

        <!-- Information Cards -->
        <div class="info-grid">
            <div class="info-card">
                <h3><i class="fas fa-file-upload"></i>Supported Formats</h3>
                <ul>
                    <li><i class="fas fa-check"></i>CSV files (.csv)</li>
                    <li><i class="fas fa-check"></i>Excel files (.xlsx, .xls)</li>
                    <li><i class="fas fa-check"></i>Biometric exports (.dat, .txt)</li>
                    <li><i class="fas fa-check"></i>Maximum file size: 50MB</li>
                </ul>
            </div>
            
            <div class="info-card">
                <h3><i class="fas fa-table"></i>Required Columns</h3>
                <ul>
                    <li><i class="fas fa-check"></i>Employee ID or Name</li>
                    <li><i class="fas fa-check"></i>Date (YYYY-MM-DD)</li>
                    <li><i class="fas fa-check"></i>Time In (HH:MM)</li>
                    <li><i class="fas fa-check"></i>Time Out (HH:MM)</li>
                </ul>
            </div>
            
            <div class="info-card">
                <h3><i class="fas fa-shield-alt"></i>Data Security</h3>
                <ul>
                    <li><i class="fas fa-check"></i>Encrypted file transfer</li>
                    <li><i class="fas fa-check"></i>Automatic data validation</li>
                    <li><i class="fas fa-check"></i>Error detection & reporting</li>
                    <li><i class="fas fa-check"></i>Secure data processing</li>
                </ul>
            </div>
        </div>

        <!-- Recent Uploads -->
        <div class="recent-uploads">
            <h3><i class="fas fa-history"></i>Recent Uploads</h3>
            <div class="upload-item">
                <i class="fas fa-file-csv"></i>
                <div class="upload-item-info">
                    <h4>attendance_march_2024.csv</h4>
                    <p>Uploaded 2 hours ago • 1,247 records</p>
                </div>
                <div class="upload-status status-success">Success</div>
            </div>
            <div class="upload-item">
                <i class="fas fa-file-excel"></i>
                <div class="upload-item-info">
                    <h4>timesheet_week12.xlsx</h4>
                    <p>Uploaded yesterday • 856 records</p>
                </div>
                <div class="upload-status status-success">Success</div>
            </div>
            <div class="upload-item">
                <i class="fas fa-file"></i>
                <div class="upload-item-info">
                    <h4>biometric_export_0315.dat</h4>
                    <p>Uploaded 2 days ago • 2,134 records</p>
                </div>
                <div class="upload-status status-processing">Processing</div>
            </div>
        </div>
    </div>

    <!-- Hidden file input -->
    <input type="file" id="fileInput" accept=".csv,.xlsx,.xls,.dat,.txt" style="display: none;">

    <script>
        let selectedFile = null;

        // File input change handler
        document.getElementById('fileInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                handleFileSelect(file);
            }
        });

        // Drag and drop handlers
        const uploadZone = document.getElementById('uploadZone');

        uploadZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            uploadZone.classList.add('dragover');
        });

        uploadZone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            uploadZone.classList.remove('dragover');
        });

        uploadZone.addEventListener('drop', function(e) {
            e.preventDefault();
            uploadZone.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                handleFileSelect(files[0]);
            }
        });

        function handleFileSelect(file) {
            // Validate file type
            const allowedTypes = ['.csv', '.xlsx', '.xls', '.dat', '.txt'];
            const fileExtension = '.' + file.name.split('.').pop().toLowerCase();
            
            if (!allowedTypes.includes(fileExtension)) {
                showToast('Please select a valid file type (CSV, Excel, or Biometric data)', 'error');
                return;
            }

            // Validate file size (50MB limit)
            if (file.size > 50 * 1024 * 1024) {
                showToast('File size must be less than 50MB', 'error');
                return;
            }

            selectedFile = file;
            displaySelectedFile(file);
        }

        function displaySelectedFile(file) {
            const uploadZone = document.getElementById('uploadZone');
            const selectedFileDiv = document.getElementById('selectedFile');
            const fileName = document.getElementById('fileName');
            const fileSize = document.getElementById('fileSize');
            const fileIcon = document.getElementById('fileIcon');
            const uploadIcon = document.getElementById('uploadIcon');
            const uploadText = document.getElementById('uploadText');
            const uploadHint = document.getElementById('uploadHint');

            // Update upload zone
            uploadZone.classList.add('has-file');
            uploadIcon.className = 'fas fa-check-circle';
            uploadText.textContent = 'File selected successfully';
            uploadHint.textContent = 'Click preview to continue or select a different file';

            // Show selected file info
            fileName.textContent = file.name;
            fileSize.textContent = formatFileSize(file.size) + ' • Ready to upload';
            
            // Set appropriate icon
            const extension = file.name.split('.').pop().toLowerCase();
            if (extension === 'csv') {
                fileIcon.className = 'fas fa-file-csv';
            } else if (extension === 'xlsx' || extension === 'xls') {
                fileIcon.className = 'fas fa-file-excel';
            } else {
                fileIcon.className = 'fas fa-file';
            }

            selectedFileDiv.classList.add('show');
        }

        function removeFile() {
            selectedFile = null;
            const uploadZone = document.getElementById('uploadZone');
            const selectedFileDiv = document.getElementById('selectedFile');
            const uploadIcon = document.getElementById('uploadIcon');
            const uploadText = document.getElementById('uploadText');
            const uploadHint = document.getElementById('uploadHint');

            // Reset upload zone
            uploadZone.classList.remove('has-file');
            uploadIcon.className = 'fas fa-cloud-upload-alt';
            uploadText.textContent = 'Drop your attendance file here';
            uploadHint.textContent = 'or click to browse and select a file';

            // Hide selected file info
            selectedFileDiv.classList.remove('show');

            // Clear file input
            document.getElementById('fileInput').value = '';
        }

        function previewFile() {
            if (!selectedFile) return;

            // Show progress
            const progressSection = document.getElementById('progressSection');
            progressSection.classList.add('show');

            // Simulate upload progress
            let progress = 0;
            const progressFill = document.getElementById('progressFill');
            const progressText = document.getElementById('progressText');

            const interval = setInterval(() => {
                progress += Math.random() * 15;
                if (progress > 100) progress = 100;

                progressFill.style.width = progress + '%';
                progressText.textContent = `Processing file... ${Math.round(progress)}%`;

                if (progress >= 100) {
                    clearInterval(interval);
                    progressText.textContent = 'Upload complete! Redirecting...';
                    
                    setTimeout(() => {
                        showToast('File uploaded successfully! Redirecting to attendance system...', 'success');
                        
                        // Redirect to attendance system after 2 seconds
                        setTimeout(() => {
                            window.location.href = '../views/attendance.php';
                        }, 2000);
                    }, 500);
                }
            }, 200);
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.style.cssText = `
                position: fixed; bottom: 24px; right: 24px;
                background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#4f46e5'};
                color: white; padding: 12px 20px; border-radius: 12px;
                font-size: 14px; z-index: 10000; animation: slideIn 0.3s ease;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            `;
            toast.textContent = message;
            document.body.appendChild(toast);
            setTimeout(() => { 
                toast.style.opacity = '0'; 
                toast.style.transition = 'opacity 0.3s';
                setTimeout(() => toast.remove(), 300); 
            }, 3000);
        }

        // Add animation style
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn { 
                from { transform: translateX(100%); opacity: 0; } 
                to { transform: translateX(0); opacity: 1; } 
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>