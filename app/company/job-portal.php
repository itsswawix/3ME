<?php
/**
 * Job Requisition Portal
 * Allows companies to post job requisitions and redirects to main requisition system
 */

$pageTitle = "Job Requisition Portal";
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
            max-width: 800px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #4f46e5;
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 32px;
            transition: color 0.2s;
        }

        .back-link:hover {
            color: #4338ca;
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

        .form-section {
            margin-bottom: 32px;
        }

        .form-section h3 {
            font-size: 18px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-section h3 i {
            color: #4f46e5;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-group label {
            font-weight: 500;
            color: #374151;
            font-size: 14px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            padding: 12px 16px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            font-size: 14px;
            background: rgba(255,255,255,0.9);
            transition: all 0.2s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .salary-range {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            gap: 12px;
            align-items: end;
        }

        .salary-separator {
            padding: 12px 8px;
            color: #64748b;
            font-weight: 500;
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
            background: linear-gradient(145deg, #4f46e5, #7c3aed);
            color: white;
        }

        .btn-primary:hover {
            background: linear-gradient(145deg, #4338ca, #6d28d9);
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

        .btn-group {
            display: flex;
            gap: 12px;
            justify-content: center;
            margin-top: 32px;
        }

        .info-box {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 24px;
        }

        .info-box h4 {
            color: #0369a1;
            font-weight: 600;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .info-box p {
            color: #0c4a6e;
            font-size: 14px;
            line-height: 1.5;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-top: 32px;
        }

        .quick-action {
            background: rgba(255,255,255,0.9);
            padding: 10px 18px;
            border-radius: 20px;
            border: 1px solid rgba(255,255,255,0.6);
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.2s;
            cursor: pointer;
        }

        .quick-action:hover {
            background: rgba(255,255,255,0.95);
            transform: translateY(-1px);
        }

        .quick-action i {
            color: #4f46e5;
            font-size: 16px;
        }

        .quick-action h4 {
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 2px;
            font-size: 13px;
        }

        .quick-action p {
            font-size: 11px;
            color: #64748b;
        }

        @media (max-width: 768px) {
            .portal-container {
                padding: 20px;
            }
            
            .portal-card {
                padding: 24px;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .btn-group {
                flex-direction: column;
            }
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
            <h1><i class="fas fa-briefcase" style="color: #4f46e5; margin-right: 12px;"></i>Job Requisition Portal</h1>
            <p>Create and submit job requisitions for your organization</p>
        </div>

        <!-- Info Box -->
        <div class="info-box">
            <h4><i class="fas fa-info-circle"></i>How it works</h4>
            <p>Fill out the job requisition form below and submit it for approval. Once approved, your job posting will be published and you can track applications through the main system.</p>
        </div>

        <!-- Job Requisition Form -->
        <div class="portal-card">
            <form id="jobRequisitionForm">
                <!-- Basic Information -->
                <div class="form-section">
                    <h3><i class="fas fa-clipboard-list"></i>Basic Information</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="jobTitle">Job Title *</label>
                            <input type="text" id="jobTitle" name="jobTitle" required placeholder="e.g. Senior Software Engineer">
                        </div>
                        <div class="form-group">
                            <label for="department">Department *</label>
                            <select id="department" name="department" required>
                                <option value="">Select Department</option>
                                <option value="Engineering">Engineering</option>
                                <option value="Product">Product</option>
                                <option value="Marketing">Marketing</option>
                                <option value="Sales">Sales</option>
                                <option value="HR">Human Resources</option>
                                <option value="Finance">Finance</option>
                                <option value="IT">Information Technology</option>
                                <option value="Operations">Operations</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="employmentType">Employment Type *</label>
                            <select id="employmentType" name="employmentType" required>
                                <option value="">Select Type</option>
                                <option value="Full-time">Full-time</option>
                                <option value="Part-time">Part-time</option>
                                <option value="Contract">Contract</option>
                                <option value="Internship">Internship</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="positionLevel">Position Level *</label>
                            <select id="positionLevel" name="positionLevel" required>
                                <option value="">Select Level</option>
                                <option value="Entry Level">Entry Level</option>
                                <option value="Junior">Junior</option>
                                <option value="Mid-Level">Mid-Level</option>
                                <option value="Senior">Senior</option>
                                <option value="Lead">Lead</option>
                                <option value="Manager">Manager</option>
                                <option value="Director">Director</option>
                                <option value="Executive">Executive</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="vacancies">Number of Vacancies *</label>
                            <input type="number" id="vacancies" name="vacancies" required min="1" value="1">
                        </div>
                        <div class="form-group">
                            <label for="location">Location *</label>
                            <input type="text" id="location" name="location" required placeholder="e.g. Manila, Philippines">
                        </div>
                    </div>
                </div>

                <!-- Salary Information -->
                <div class="form-section">
                    <h3><i class="fas fa-money-bill-wave"></i>Salary Information</h3>
                    <div class="form-group">
                        <label>Salary Range (PHP) *</label>
                        <div class="salary-range">
                            <input type="number" id="salaryMin" name="salaryMin" required placeholder="Minimum" min="0">
                            <div class="salary-separator">to</div>
                            <input type="number" id="salaryMax" name="salaryMax" required placeholder="Maximum" min="0">
                        </div>
                    </div>
                </div>

                <!-- Job Details -->
                <div class="form-section">
                    <h3><i class="fas fa-file-alt"></i>Job Details</h3>
                    <div class="form-group full-width">
                        <label for="jobDescription">Job Description *</label>
                        <textarea id="jobDescription" name="jobDescription" required placeholder="Describe the role, responsibilities, and what the candidate will be doing..."></textarea>
                    </div>
                    <div class="form-group full-width">
                        <label for="requirements">Requirements *</label>
                        <textarea id="requirements" name="requirements" required placeholder="List the required skills, experience, education, and qualifications..."></textarea>
                    </div>
                    <div class="form-group full-width">
                        <label for="benefits">Benefits & Perks</label>
                        <textarea id="benefits" name="benefits" placeholder="Describe the benefits, perks, and what makes this role attractive..."></textarea>
                    </div>
                </div>

                <!-- Urgency & Timeline -->
                <div class="form-section">
                    <h3><i class="fas fa-calendar-alt"></i>Timeline</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="urgency">Urgency Level *</label>
                            <select id="urgency" name="urgency" required>
                                <option value="">Select Urgency</option>
                                <option value="Low">Low - Fill within 3 months</option>
                                <option value="Medium">Medium - Fill within 1 month</option>
                                <option value="High">High - Fill within 2 weeks</option>
                                <option value="Critical">Critical - Fill ASAP</option>
                            </select>
                            <small id="urgencyHint" style="color: #64748b; font-size: 11px; margin-top: 4px; display: block;">
                                <i class="fas fa-magic" style="margin-right: 4px;"></i>
                                Auto-suggested based on start date
                            </small>
                        </div>
                        <div class="form-group">
                            <label for="startDate">Desired Start Date</label>
                            <input type="date" id="startDate" name="startDate">
                            <small style="color: #64748b; font-size: 11px; margin-top: 4px; display: block;">
                                <i class="fas fa-info-circle" style="margin-right: 4px;"></i>
                                Urgency level will be auto-suggested based on this date
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="btn-group">
                    <button type="button" class="btn btn-secondary" onclick="saveDraft()">
                        <i class="fas fa-save"></i>
                        Save as Draft
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i>
                        Submit Requisition
                    </button>
                </div>
            </form>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <div class="quick-action" onclick="viewMyRequisitions()">
                <i class="fas fa-list"></i>
                <div>
                    <h4>My Requisitions</h4>
                    <p>View submitted requests</p>
                </div>
            </div>
            <div class="quick-action" onclick="viewTemplates()">
                <i class="fas fa-copy"></i>
                <div>
                    <h4>Use Template</h4>
                    <p>Start from existing job</p>
                </div>
            </div>
            <div class="quick-action" onclick="viewAnalytics()">
                <i class="fas fa-chart-bar"></i>
                <div>
                    <h4>Hiring Analytics</h4>
                    <p>Track your progress</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Form submission handler
        document.getElementById('jobRequisitionForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validate salary range
            const salaryMin = parseInt(document.getElementById('salaryMin').value);
            const salaryMax = parseInt(document.getElementById('salaryMax').value);
            
            if (salaryMax <= salaryMin) {
                showToast('Maximum salary must be greater than minimum salary', 'error');
                return;
            }
            
            // Show loading
            showToast('Submitting job requisition...', 'info');
            
            // Simulate form submission
            setTimeout(() => {
                showToast('Job requisition submitted successfully!', 'success');
                
                // Redirect to main requisition system after 2 seconds
                setTimeout(() => {
                    window.location.href = '../views/requisition.php';
                }, 2000);
            }, 1500);
        });

        function saveDraft() {
            showToast('Draft saved successfully!', 'success');
        }

        function viewMyRequisitions() {
            window.location.href = '../views/requisition.php';
        }

        function viewTemplates() {
            showToast('Template feature coming soon!', 'info');
        }

        function viewAnalytics() {
            showToast('Analytics feature coming soon!', 'info');
        }

        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            const bgColor = type === 'success' ? '#10b981' : 
                           type === 'error' ? '#ef4444' : 
                           type === 'warning' ? '#f59e0b' : '#4f46e5';
            
            toast.style.cssText = `
                position: fixed; bottom: 24px; right: 24px;
                background: ${bgColor};
                color: white; padding: 12px 20px; border-radius: 12px;
                font-size: 14px; z-index: 10000; animation: slideIn 0.3s ease;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                max-width: 400px;
            `;
            toast.textContent = message;
            document.body.appendChild(toast);
            setTimeout(() => { 
                toast.style.opacity = '0'; 
                toast.style.transition = 'opacity 0.3s';
                setTimeout(() => toast.remove(), 300); 
            }, 4000); // Show longer for urgency messages
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

        // Auto-calculate salary max when min changes
        document.getElementById('salaryMin').addEventListener('input', function() {
            const minValue = parseInt(this.value);
            if (minValue) {
                const maxField = document.getElementById('salaryMax');
                if (!maxField.value || parseInt(maxField.value) <= minValue) {
                    maxField.value = Math.round(minValue * 1.3); // Suggest 30% higher
                }
            }
        });

        // Auto-detect urgency based on desired start date
        document.getElementById('startDate').addEventListener('change', function() {
            const startDate = new Date(this.value);
            const today = new Date();
            const urgencySelect = document.getElementById('urgency');
            const urgencyHint = document.getElementById('urgencyHint');
            
            if (!this.value) {
                urgencyHint.innerHTML = '<i class="fas fa-magic" style="margin-right: 4px;"></i>Auto-suggested based on start date';
                urgencyHint.style.color = '#64748b';
                return; // Don't change urgency if no date selected
            }
            
            // Calculate days difference
            const timeDiff = startDate.getTime() - today.getTime();
            const daysDiff = Math.ceil(timeDiff / (1000 * 3600 * 24));
            
            let suggestedUrgency = '';
            let urgencyMessage = '';
            let urgencyColor = '';
            
            if (daysDiff < 0) {
                // Past date
                suggestedUrgency = 'Critical';
                urgencyMessage = 'Start date is in the past - Critical urgency suggested';
                urgencyColor = '#ef4444';
            } else if (daysDiff <= 7) {
                // Within 1 week
                suggestedUrgency = 'Critical';
                urgencyMessage = `${daysDiff} days until start date - Critical urgency suggested`;
                urgencyColor = '#ef4444';
            } else if (daysDiff <= 14) {
                // Within 2 weeks
                suggestedUrgency = 'High';
                urgencyMessage = `${daysDiff} days until start date - High urgency suggested`;
                urgencyColor = '#f59e0b';
            } else if (daysDiff <= 30) {
                // Within 1 month
                suggestedUrgency = 'Medium';
                urgencyMessage = `${daysDiff} days until start date - Medium urgency suggested`;
                urgencyColor = '#3b82f6';
            } else {
                // More than 1 month
                suggestedUrgency = 'Low';
                urgencyMessage = `${daysDiff} days until start date - Low urgency suggested`;
                urgencyColor = '#10b981';
            }
            
            // Update urgency select if it's not already set or if user wants auto-suggestion
            if (!urgencySelect.value || confirm(`${urgencyMessage}. Would you like to update the urgency level?`)) {
                urgencySelect.value = suggestedUrgency;
                
                // Update hint text with specific information
                urgencyHint.innerHTML = `<i class="fas fa-check-circle" style="margin-right: 4px; color: ${urgencyColor};"></i>${urgencyMessage}`;
                urgencyHint.style.color = urgencyColor;
                
                // Visual feedback on the select field
                urgencySelect.style.borderColor = urgencyColor;
                urgencySelect.style.boxShadow = `0 0 0 3px ${urgencyColor}20`;
                
                setTimeout(() => {
                    urgencySelect.style.borderColor = '#e2e8f0';
                    urgencySelect.style.boxShadow = 'none';
                }, 2000);
                
                showToast(urgencyMessage, daysDiff <= 7 ? 'error' : daysDiff <= 14 ? 'warning' : 'success');
            } else {
                // User declined, but still show the suggestion in the hint
                urgencyHint.innerHTML = `<i class="fas fa-lightbulb" style="margin-right: 4px; color: ${urgencyColor};"></i>Suggestion: ${suggestedUrgency} urgency (${daysDiff} days)`;
                urgencyHint.style.color = urgencyColor;
            }
        });

        // Initialize
        (function() {
            // Set minimum date to today
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('startDate').setAttribute('min', today);
            
            console.log('✅ Job Portal initialized with auto-urgency detection');
        })();
    </script>
</body>
</html>