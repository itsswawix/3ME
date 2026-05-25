# 🚀 Attendance Modals - Quick Reference

## 📞 Function Calls

### Roster Management
```javascript
// Create new roster
openAddRosterModal();

// View roster details
viewRoster('RST-1234567890');

// Edit roster
editRoster('RST-1234567890');

// Duplicate roster
duplicateRoster('RST-1234567890');

// Assign employees (coming soon)
assignEmployees('RST-1234567890');
```

### Correction Management
```javascript
// Create correction request
openAddCorrectionModal();

// View correction details
viewCorrection('COR-1234567890');

// Edit correction
editCorrection('COR-1234567890');

// Approve correction
approveCorrection('COR-1234567890');

// Reject correction
rejectCorrection('COR-1234567890');
```

### Import Management
```javascript
// View import details
viewImportDetails('IMP-2024-001');

// Download import log
downloadImportLog('IMP-2024-001');

// View import errors
viewErrors('IMP-2024-001');

// Export preview data
exportPreviewData('IMP-2024-001');
```

### Helper Functions
```javascript
// Format time for input (HH:MM)
formatTimeForInput('8:30 AM'); // Returns '08:30'

// Format date for input (YYYY-MM-DD)
formatDateForInput('Jan 15, 2024'); // Returns '2024-01-15'

// Format date for display
formatDateForDisplay('2024-01-15'); // Returns 'Jan 15, 2024'

// Calculate duration
calculateDuration('08:00', '17:00'); // Returns '9h 0m'

// Validate time
isValidTime('08:30'); // Returns true

// Show validation errors
showValidationErrors(['Error 1', 'Error 2']);

// Show toast notification
showToast('Success!', 'success');
showToast('Warning!', 'warning');
showToast('Error!', 'error');
showToast('Info!', 'info');
```

## 📁 File Locations

```
app/views/modals/attendance-modal/
├── modal-wrapper.php              # Include this to load all modals
├── modal-attendance-helpers.php   # Helper functions
├── modal-add-roster.php          # Create roster
├── modal-edit-roster.php         # Edit roster
├── modal-view-roster.php         # View roster
├── modal-add-correction.php      # Create correction
├── modal-edit-correction.php     # Edit correction
└── modal-view-correction.php     # View correction
```

## 🎯 Common Use Cases

### Creating a New Roster
```javascript
// 1. User clicks "New Roster" button
<button onclick="openAddRosterModal()">New Roster</button>

// 2. Modal opens with form
// 3. User fills form and submits
// 4. handleAddRoster() validates and saves
// 5. Table refreshes automatically
```

### Approving a Correction
```javascript
// 1. User clicks approve icon in table
<i onclick="approveCorrection('COR-123')"></i>

// 2. Status updates to "Approved"
// 3. Approval info is set
// 4. Table refreshes
// 5. Success toast appears
```

### Viewing Import Details
```javascript
// 1. User clicks view icon in import table
<i onclick="viewImportDetails('IMP-2024-001')"></i>

// 2. Modal opens with statistics
// 3. Shows success rate, file details
// 4. Provides action buttons
```

## 🎨 Status Badges

### Correction Status
- **Pending** - Yellow badge with hourglass icon
- **Approved** - Green badge with check icon
- **Rejected** - Red badge with X icon

### Correction Type
- **Late** - Yellow badge with clock icon
- **Early Departure** - Blue badge with door icon
- **Missed Entry** - Purple badge with warning icon
- **Overtime Discrepancy** - Gray badge with business icon

### Import Status
- **Success** - Green badge with check icon
- **Partial** - Yellow badge with warning icon
- **Failed** - Red badge with X icon

## 🔧 Validation Rules

### Roster Form
- ✅ Shift name required
- ✅ Company required
- ✅ Start time required (HH:MM format)
- ✅ End time required (HH:MM format)
- ✅ Overtime rule required
- ✅ Effective date required

### Correction Form
- ✅ Employee required (must select from search)
- ✅ Correction type required
- ✅ Original date required
- ✅ Reason required (min length)

## 🎭 Modal Behavior

### Opening
- Slides in from right
- Backdrop appears
- Focus on first input
- Change tracking starts

### Closing
- X button click
- ESC key press
- Backdrop click
- Checks for unsaved changes

### Unsaved Changes
- Tracks input/change events
- Shows confirmation dialog
- "Continue Editing" or "Exit Anyway"
- Resets on successful save

## 📊 Data Arrays

### Global Data
```javascript
window.rosters = [];        // All rosters
window.corrections = [];    // All corrections
window.importHistory = [];  // All imports
window.importPreviewData = {}; // Import preview data
```

### Filtered Data
```javascript
filteredRosters = [];       // After search/filter
filteredCorrections = [];   // After search/filter
filteredImports = [];       // After search/filter
```

## 🎨 CSS Classes

### Buttons
```css
.btn-primary    /* Blue - primary actions */
.btn-secondary  /* Gray - cancel actions */
.btn-success    /* Green - approve actions */
.btn-warning    /* Orange - warning actions */
.btn-danger     /* Red - delete actions */
.btn-info       /* Light blue - info actions */
```

### Badges
```css
.badge-success  /* Green background */
.badge-warning  /* Yellow background */
.badge-danger   /* Red background */
.badge-info     /* Blue background */
.badge-secondary /* Gray background */
.badge-purple   /* Purple background */
```

### Form Elements
```css
.form-group     /* Form field container */
.form-row       /* Two-column layout */
.required-star  /* Red asterisk */
.modal-footer   /* Button container */
```

## 🧪 Testing Commands

### Browser Console
```javascript
// Test roster creation
openAddRosterModal();

// Test with mock data
viewRoster('RST-' + Date.now());

// Test correction approval
approveCorrection('COR-' + Date.now());

// Test import details
viewImportDetails('IMP-2024-001');

// Check if functions exist
console.log(typeof openAddRosterModal); // Should be 'function'
```

## 🐛 Troubleshooting

### Modal doesn't open
```javascript
// Check if function exists
console.log(typeof openAddRosterModal);

// Check if modal files are included
console.log('Modal files loaded');

// Check for JavaScript errors
// Open browser console (F12)
```

### Data not saving
```javascript
// Check if data arrays exist
console.log(window.rosters);
console.log(window.corrections);

// Check if render functions exist
console.log(typeof renderRosterTable);
console.log(typeof renderCorrectionTable);
```

### Validation not working
```javascript
// Check if helper functions exist
console.log(typeof showValidationErrors);

// Test validation manually
showValidationErrors(['Test error']);
```

## 📱 Responsive Design

### Desktop (> 768px)
- Full 460px modal width
- Two-column form layout
- All features visible

### Mobile (< 768px)
- 95vw modal width
- Single-column form layout
- Scrollable content

## ⌨️ Keyboard Shortcuts

- **ESC** - Close modal or confirmation
- **Enter** - Submit form (when focused)
- **Tab** - Navigate form fields

## 🎯 Best Practices

1. **Always validate** before saving
2. **Show feedback** with toasts
3. **Confirm destructive** actions
4. **Track changes** for unsaved warning
5. **Use helpers** for formatting
6. **Handle errors** gracefully
7. **Keep modals** focused and simple

---

**Quick Tip:** Use `test-attendance-modals.html` to see all functions in action!

**Last Updated:** May 21, 2026
