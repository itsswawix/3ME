<?php
/**
 * profile.php
 * My Profile & Account Settings - A premium, secure profile management page
 */

$pageTitle = "My Profile";
$activeMenu = ""; // Keep empty or specify "My Profile" to avoid conflict with main sidebar menus

// Ensure session is started and check login
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Development fallback
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 'USER-ADMIN-001';
    $_SESSION['user_email'] = 'admin@3me.com';
    $_SESSION['user_name'] = 'System Administrator';
    $_SESSION['user_role'] = 'Admin';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - 3ME HR System</title>
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            background: radial-gradient(circle at 20% 30%, #eef2ff, #e0e7ff); 
            font-family: 'Inter', sans-serif; 
            min-height: 100vh; 
            font-size: 13px; 
            color: #1e293b;
        }
        
        .app-layout { display: flex; min-height: 100vh; position: relative; }
        .main-content { flex: 1; padding: 24px 30px; overflow-y: auto; max-height: 100vh; }
        
        /* Page Header */
        .page-header { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 24px; 
        }
        .page-header h1 { 
            font-family: 'Outfit', sans-serif;
            font-size: 24px; 
            font-weight: 600; 
            color: #0f172a; 
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .page-header h1 i { color: #4f46e5; }
        
        /* Grid Layout */
        .profile-grid {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 24px;
            align-items: start;
        }
        
        @media (max-width: 900px) {
            .profile-grid {
                grid-template-columns: 1fr;
            }
        }
        
        /* Cards styling */
        .glass-card {
            background: rgba(255,255,255,0.8);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 24px;
            border: 1px solid rgba(255,255,255,0.85);
            box-shadow: 0 8px 30px rgba(99,102,241,0.04);
            padding: 24px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .glass-card:hover {
            box-shadow: 0 12px 40px rgba(99,102,241,0.06);
            border-color: rgba(255, 255, 255, 1);
        }
        
        /* Left Column: Avatar & Summary */
        .summary-card {
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
        }
        
        .avatar-preview-container {
            position: relative;
            margin-bottom: 16px;
        }
        
        .avatar-preview {
            width: 96px;
            height: 96px;
            border-radius: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Outfit', sans-serif;
            font-size: 34px;
            font-weight: 700;
            color: white;
            box-shadow: 0 10px 25px rgba(79,70,229,0.25);
            transition: background 0.3s, transform 0.3s;
        }
        .avatar-preview:hover {
            transform: scale(1.03);
        }
        
        .profile-title {
            font-family: 'Outfit', sans-serif;
            font-size: 16px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 4px;
            word-break: break-word;
        }
        .profile-subtitle {
            font-size: 12px;
            color: #64748b;
            margin-bottom: 16px;
        }
        
        .badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-block;
            margin-bottom: 20px;
        }
        .badge-success { background: #dcfce7; color: #16a34a; }
        .badge-info { background: #dbeafe; color: #2563eb; }
        
        .color-selector-label {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #94a3b8;
            margin-bottom: 10px;
            width: 100%;
            text-align: left;
            border-top: 1px solid rgba(99,102,241,0.06);
            padding-top: 16px;
        }
        
        .color-palette {
            display: flex;
            gap: 8px;
            justify-content: center;
            flex-wrap: wrap;
            width: 100%;
            margin-bottom: 10px;
        }
        
        .color-dot {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            border: 2px solid transparent;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        .color-dot:hover {
            transform: scale(1.15);
        }
        .color-dot.active {
            border-color: #1e293b;
            transform: scale(1.15);
        }
        
        /* Forms Styling */
        .form-section-title {
            font-family: 'Outfit', sans-serif;
            font-size: 16px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 8px;
            border-bottom: 1px solid rgba(99,102,241,0.06);
            padding-bottom: 10px;
        }
        .form-section-title i {
            color: #4f46e5;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        
        .form-group.full-width {
            grid-column: 1 / -1;
        }
        
        .form-group label {
            font-size: 11.5px;
            font-weight: 600;
            color: #64748b;
        }
        
        .form-group input {
            padding: 10px 14px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            background: rgba(255,255,255,0.7);
            font-family: 'Inter', sans-serif;
            font-size: 13px;
            color: #1e293b;
            outline: none;
            transition: all 0.2s;
        }
        
        .form-group input:focus {
            border-color: #4f46e5;
            background: white;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }
        
        .form-group input[readonly] {
            background: rgba(241, 245, 249, 0.5);
            color: #64748b;
            cursor: not-allowed;
            border-color: #e2e8f0;
        }
        
        .form-group input[readonly]:focus {
            box-shadow: none;
            border-color: #e2e8f0;
        }
        
        /* Buttons */
        .form-actions {
            display: flex;
            justify-content: flex-end;
            margin-top: 12px;
        }
        
        .btn {
            font-family: 'Outfit', sans-serif;
            font-weight: 500;
            font-size: 13px;
            padding: 10px 20px;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.03);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
        }
        
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(79, 70, 229, 0.3);
        }
        
        .btn-primary:active {
            transform: translateY(0);
        }
        
        .btn-primary:disabled {
            background: #94a3b8;
            box-shadow: none;
            cursor: not-allowed;
            transform: none;
        }
        
        /* Password Reset Collapsible Panel */
        .password-toggle-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            user-select: none;
        }
        
        .password-toggle-header:hover h3 {
            color: #4f46e5;
        }
        
        .password-toggle-chevron {
            color: #94a3b8;
            font-size: 14px;
            transition: transform 0.25s;
        }
        
        .password-toggle-chevron.expanded {
            transform: rotate(180deg);
        }
        
        .password-panel-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s cubic-bezier(0.4, 0, 0.2, 1), margin-top 0.3s;
        }
        
        .password-panel-content.expanded {
            max-height: 300px;
            margin-top: 16px;
        }
        
        /* Feedback Toast Notifications */
        .toast-container {
            position: fixed;
            top: 24px;
            right: 24px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .toast-msg {
            min-width: 280px;
            max-width: 400px;
            background: white;
            border-radius: 16px;
            padding: 14px 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08), 0 2px 6px rgba(0,0,0,0.04);
            display: flex;
            align-items: center;
            gap: 12px;
            transform: translateX(120%);
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            border-left: 4px solid #4f46e5;
        }
        
        .toast-msg.show {
            transform: translateX(0);
        }
        
        .toast-msg.success { border-left-color: #10b981; }
        .toast-msg.success i { color: #10b981; }
        
        .toast-msg.error { border-left-color: #ef4444; }
        .toast-msg.error i { color: #ef4444; }
        
        .toast-content {
            flex: 1;
        }
        
        .toast-title {
            font-family: 'Outfit', sans-serif;
            font-weight: 600;
            font-size: 13px;
            color: #0f172a;
            margin-bottom: 2px;
        }
        
        .toast-desc {
            font-size: 11.5px;
            color: #64748b;
            line-height: 1.3;
        }
        
        /* Spacer */
        .divider {
            height: 24px;
        }
    </style>
</head>
<body>
<div class="app-layout">
    
    <!-- Include Sidebar Navigation -->
    <?php include 'sidebar.php'; ?>

    <main class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-id-card"></i> My Profile</h1>
        </div>

        <div class="profile-grid">
            
            <!-- Left Panel: Live Avatar Preview Card -->
            <div class="glass-card summary-card">
                <div class="avatar-preview-container">
                    <div id="avatar-preview" class="avatar-preview" style="background: #4f46e5;">US</div>
                </div>
                <div id="profile-card-name" class="profile-title">User Name</div>
                <div id="profile-card-role" class="profile-subtitle">Employee</div>
                
                <div>
                    <span id="profile-card-status" class="badge badge-success">Active</span>
                </div>
                
                <div class="color-selector-label">Choose Avatar Theme</div>
                <div class="color-palette" id="color-palette">
                    <!-- Dynamic color dots populated by JS -->
                </div>
            </div>
            
            <!-- Right Panel: Form Fields -->
            <div style="display: flex; flex-direction: column; gap: 24px;">
                
                <!-- Card 1: Personal Profile Details -->
                <div class="glass-card">
                    <div class="form-section-title">
                        <i class="fas fa-user-edit"></i> Profile Details
                    </div>
                    
                    <form id="profile-details-form">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="profile-id">User Account ID</label>
                                <input type="text" id="profile-id" readonly>
                            </div>
                            
                            <div class="form-group">
                                <label for="profile-name">Full Name</label>
                                <input type="text" id="profile-name" required placeholder="Enter full name">
                            </div>
                            
                            <div class="form-group">
                                <label for="profile-email">Email Address</label>
                                <input type="email" id="profile-email" required placeholder="Enter email address">
                            </div>
                            
                            <div class="form-group">
                                <label for="profile-contact">Contact Number</label>
                                <input type="text" id="profile-contact" placeholder="e.g. +63 917 123 4567">
                            </div>
                            
                            <div class="form-group">
                                <label for="profile-department">Department</label>
                                <input type="text" id="profile-department" readonly placeholder="No department assigned">
                            </div>
                            
                            <div class="form-group">
                                <label for="profile-role">System Role</label>
                                <input type="text" id="profile-role" readonly>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- Card 2: Security settings (Password reset) -->
                <div class="glass-card">
                    <div class="password-toggle-header" id="password-toggle-btn">
                        <div class="form-section-title" style="margin-bottom: 0; border-bottom: none; padding-bottom: 0;">
                            <i class="fas fa-shield-alt"></i> Security &amp; Password
                        </div>
                        <i class="fas fa-chevron-down password-toggle-chevron" id="password-chevron"></i>
                    </div>
                    
                    <div class="password-panel-content" id="password-panel">
                        <form id="password-change-form">
                            <div class="form-grid" style="margin-bottom: 0;">
                                <div class="form-group">
                                    <label for="current-password">Current Password</label>
                                    <input type="password" id="current-password" placeholder="••••••••">
                                </div>
                                <div class="form-group">
                                    <label for="new-password">New Password</label>
                                    <input type="password" id="new-password" placeholder="At least 6 characters">
                                </div>
                                <div class="form-group">
                                    <label for="confirm-password">Confirm New Password</label>
                                    <input type="password" id="confirm-password" placeholder="Verify password">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Master Save Action -->
                <div class="form-actions">
                    <button class="btn btn-primary" id="save-profile-btn">
                        <i class="fas fa-check-circle"></i> Save Profile Changes
                    </button>
                </div>
                
            </div>
            
        </div>
        
        <div class="divider"></div>
    </main>
</div>

<!-- Toast Alert Banners -->
<div class="toast-container" id="toast-box"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    'use strict';
    
    // Elements
    var profileIdInput = document.getElementById('profile-id');
    var profileNameInput = document.getElementById('profile-name');
    var profileEmailInput = document.getElementById('profile-email');
    var profileContactInput = document.getElementById('profile-contact');
    var profileDeptInput = document.getElementById('profile-department');
    var profileRoleInput = document.getElementById('profile-role');
    
    var currentPasswordInput = document.getElementById('current-password');
    var newPasswordInput = document.getElementById('new-password');
    var confirmPasswordInput = document.getElementById('confirm-password');
    
    var avatarPreview = document.getElementById('avatar-preview');
    var profileCardName = document.getElementById('profile-card-name');
    var profileCardRole = document.getElementById('profile-card-role');
    var profileCardStatus = document.getElementById('profile-card-status');
    
    var colorPalette = document.getElementById('color-palette');
    var passwordToggleBtn = document.getElementById('password-toggle-btn');
    var passwordPanel = document.getElementById('password-panel');
    var passwordChevron = document.getElementById('password-chevron');
    
    var saveProfileBtn = document.getElementById('save-profile-btn');
    var toastBox = document.getElementById('toast-box');
    
    // Local State
    var currentUser = null;
    var selectedColor = '';
    
    // Curated color palette
    var colorsList = [
        { name: 'Nova Indigo', value: '#4f46e5' },
        { name: 'Orchid Purple', value: '#7c3aed' },
        { name: 'Deep Pink', value: '#db2777' },
        { name: 'Ruby Red', value: '#dc2626' },
        { name: 'Sunset Orange', value: '#ea580c' },
        { name: 'Emerald Garden', value: '#16a34a' },
        { name: 'Ocean Teal', value: '#0891b2' }
    ];
    
    // Toggle Password Panel
    passwordToggleBtn.addEventListener('click', function() {
        var isExpanded = passwordPanel.classList.contains('expanded');
        if (isExpanded) {
            passwordPanel.classList.remove('expanded');
            passwordChevron.classList.remove('expanded');
        } else {
            passwordPanel.classList.add('expanded');
            passwordChevron.classList.add('expanded');
        }
    });
    
    // Render color palette dots
    function renderColorPalette() {
        colorPalette.innerHTML = '';
        colorsList.forEach(function(theme) {
            var dot = document.createElement('div');
            dot.className = 'color-dot';
            dot.style.background = theme.value;
            dot.title = theme.name;
            dot.dataset.color = theme.value;
            
            if (selectedColor === theme.value) {
                dot.classList.add('active');
            }
            
            dot.addEventListener('click', function() {
                document.querySelectorAll('.color-dot').forEach(function(d) {
                    d.classList.remove('active');
                });
                dot.classList.add('active');
                selectedColor = theme.value;
                avatarPreview.style.background = selectedColor;
            });
            
            colorPalette.appendChild(dot);
        });
    }
    
    // Calculate Initials from Name input
    function calculateInitials(name) {
        if (!name) return 'US';
        var parts = name.trim().split(/\s+/);
        var initials = '';
        for (var i = 0; i < parts.length; i++) {
            if (parts[i]) {
                initials += parts[i].charAt(0).toUpperCase();
            }
        }
        if (initials.length > 2) {
            initials = initials.substring(0, 2);
        }
        return initials || 'US';
    }
    
    // Bind Real-time Input Previews
    profileNameInput.addEventListener('input', function() {
        var name = this.value;
        profileCardName.innerText = name || 'User Name';
        avatarPreview.innerText = calculateInitials(name);
    });
    
    // Load User Profile Details
    function loadProfile() {
        fetch('../../api/users/profile_api.php')
            .then(function(response) {
                return response.json();
            })
            .then(function(res) {
                if (res.success && res.data) {
                    currentUser = res.data;
                    populateFields(res.data);
                } else {
                    showToast('Failed to load Profile', res.message || 'Error occurred', 'error');
                }
            })
            .catch(function(err) {
                console.error(err);
                showToast('Failed to load Profile', 'Server communication failure.', 'error');
            });
    }
    
    // Populate form fields
    function populateFields(user) {
        profileIdInput.value = user.id;
        profileNameInput.value = user.name;
        profileEmailInput.value = user.email;
        profileContactInput.value = user.contact_number || '';
        profileDeptInput.value = user.department || 'No Department Assigned';
        profileRoleInput.value = user.role || 'Employee';
        
        // Update summary preview card
        profileCardName.innerText = user.name;
        profileCardRole.innerText = user.role || 'Employee';
        avatarPreview.innerText = user.avatar_initials;
        
        // Status badge
        profileCardStatus.innerText = user.status || 'Active';
        profileCardStatus.className = 'badge ' + 
            (user.status === 'Active' ? 'badge-success' : 'badge-info');
            
        // Setup selected color theme
        selectedColor = user.color || '#4f46e5';
        avatarPreview.style.background = selectedColor;
        
        renderColorPalette();
    }
    
    // Show Premium Toast Alerts
    function showToast(title, description, type) {
        var toast = document.createElement('div');
        toast.className = 'toast-msg ' + (type || 'success');
        
        var iconClass = type === 'error' ? 'fa-exclamation-circle' : 'fa-check-circle';
        
        toast.innerHTML = `
            <i class="fas ${iconClass}" style="font-size: 16px;"></i>
            <div class="toast-content">
                <div class="toast-title">${title}</div>
                <div class="toast-desc">${description}</div>
            </div>
        `;
        
        toastBox.appendChild(toast);
        
        // Trigger slide in animation
        setTimeout(function() {
            toast.classList.add('show');
        }, 50);
        
        // Auto-remove toast after 4.5 seconds
        setTimeout(function() {
            toast.classList.remove('show');
            setTimeout(function() {
                toast.remove();
            }, 300);
        }, 4500);
    }
    
    // Save Profile Action
    saveProfileBtn.addEventListener('click', function(e) {
        e.preventDefault();
        
        var name = profileNameInput.value.trim();
        var email = profileEmailInput.value.trim();
        var contact = profileContactInput.value.trim();
        
        var currentPassword = currentPasswordInput.value;
        var newPassword = newPasswordInput.value;
        var confirmPassword = confirmPasswordInput.value;
        
        // Validations
        if (!name || !email) {
            showToast('Validation Error', 'Full Name and Email Address are required fields.', 'error');
            return;
        }
        
        var payload = {
            name: name,
            email: email,
            contact_number: contact,
            color: selectedColor
        };
        
        // Password updates checks
        if (newPassword || confirmPassword || currentPassword) {
            if (!currentPassword) {
                showToast('Validation Error', 'Please enter your current password to authorize password changes.', 'error');
                return;
            }
            if (newPassword !== confirmPassword) {
                showToast('Validation Error', 'New Password and Password Confirmation fields do not match.', 'error');
                return;
            }
            if (newPassword.length < 6) {
                showToast('Validation Error', 'New password must contain at least 6 characters.', 'error');
                return;
            }
            
            payload.current_password = currentPassword;
            payload.new_password = newPassword;
        }
        
        // Disable save button and add loader spinner
        saveProfileBtn.disabled = true;
        var originalBtnHtml = saveProfileBtn.innerHTML;
        saveProfileBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving changes...';
        
        fetch('../../api/users/profile_api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(function(response) {
            return response.json();
        })
        .then(function(res) {
            saveProfileBtn.disabled = false;
            saveProfileBtn.innerHTML = originalBtnHtml;
            
            if (res.success) {
                showToast('Profile Updated', 'Your profile details have been securely updated.', 'success');
                
                // Clear password inputs and collapse panel if it was expanded
                currentPasswordInput.value = '';
                newPasswordInput.value = '';
                confirmPasswordInput.value = '';
                
                if (passwordPanel.classList.contains('expanded')) {
                    passwordPanel.classList.remove('expanded');
                    passwordChevron.classList.remove('expanded');
                }
                
                // Live update the sidebar element details dynamically without a full page reload!
                var newInitials = res.data.user.avatar_initials;
                var newColor = res.data.user.color;
                var newName = res.data.user.name;
                var newEmail = res.data.user.email;
                
                // Sidebar profile bottom trigger
                var sbAvatar = document.querySelector('#nc-profile-trigger .nc-avatar');
                var sbName = document.querySelector('#nc-profile-trigger .nc-profile-name');
                if (sbAvatar) {
                    sbAvatar.innerText = newInitials;
                    sbAvatar.style.background = newColor;
                }
                if (sbName) sbName.innerText = newName;
                
                // Sidebar popout drawer
                var popoutAvatar = document.querySelector('#nc-popout .nc-popout-avatar');
                var popoutName = document.querySelector('#nc-popout .nc-popout-name');
                var popoutEmail = document.querySelector('#nc-popout .nc-popout-email');
                if (popoutAvatar) {
                    popoutAvatar.innerText = newInitials;
                    popoutAvatar.style.background = newColor;
                }
                if (popoutName) popoutName.innerText = newName;
                if (popoutEmail) popoutEmail.innerText = newEmail;
                
                // Reload state
                loadProfile();
            } else {
                showToast('Update Failed', res.message || 'Error saving changes.', 'error');
            }
        })
        .catch(function(err) {
            saveProfileBtn.disabled = false;
            saveProfileBtn.innerHTML = originalBtnHtml;
            console.error(err);
            showToast('Update Failed', 'Network communication error.', 'error');
        });
    });
    
    // Initial loading
    loadProfile();
});
</script>
</body>
</html>
