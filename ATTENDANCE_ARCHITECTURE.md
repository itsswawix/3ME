# 🏗️ Attendance Module Architecture

## 📐 System Overview

```
┌─────────────────────────────────────────────────────────────────┐
│                     attendance.php (Main Page)                   │
│  ┌───────────────────────────────────────────────────────────┐  │
│  │  Tab 1: Time Capture Import                               │  │
│  │  - Import History Table                                   │  │
│  │  - File Upload & Preview                                  │  │
│  │  - Spreadsheet Viewer                                     │  │
│  └───────────────────────────────────────────────────────────┘  │
│  ┌───────────────────────────────────────────────────────────┐  │
│  │  Tab 2: Attendance Rules & Rostering                      │  │
│  │  - Roster Table                                           │  │
│  │  - Filter & Search                                        │  │
│  │  - Stats Summary                                          │  │
│  └───────────────────────────────────────────────────────────┘  │
│  ┌───────────────────────────────────────────────────────────┐  │
│  │  Tab 3: Exceptions & Corrections                          │  │
│  │  - Correction Table                                       │  │
│  │  - Filter & Search                                        │  │
│  │  - Stats Summary                                          │  │
│  └───────────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────┘
                              │
                              │ includes
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│              modals/modal-wrapper.php (Base Modal)               │
│  - Modal overlay & panel structure                               │
│  - Open/close functions                                          │
│  - Unsaved changes tracking                                      │
│  - Confirmation dialog                                           │
└─────────────────────────────────────────────────────────────────┘
                              │
                              │ includes
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│         modals/attendance-modal/ (Modal Components)              │
│                                                                   │
│  ┌────────────────────────────────────────────────────────────┐ │
│  │  modal-attendance-helpers.php                              │ │
│  │  - formatTimeForInput()                                    │ │
│  │  - formatDateForInput()                                    │ │
│  │  - formatDateForDisplay()                                  │ │
│  │  - calculateDuration()                                     │ │
│  │  - isValidTime()                                           │ │
│  │  - showValidationErrors()                                  │ │
│  │  - showToast()                                             │ │
│  │  - viewImportDetails()                                     │ │
│  │  - downloadImportLog()                                     │ │
│  │  - viewErrors()                                            │ │
│  │  - exportPreviewData()                                     │ │
│  └────────────────────────────────────────────────────────────┘ │
│                                                                   │
│  ┌────────────────────────────────────────────────────────────┐ │
│  │  ROSTER MODALS                                             │ │
│  │  ┌──────────────────────────────────────────────────────┐ │ │
│  │  │  modal-add-roster.php                                │ │ │
│  │  │  - openAddRosterModal()                              │ │ │
│  │  │  - handleAddRoster()                                 │ │ │
│  │  │  - updateDurationPreview()                           │ │ │
│  │  └──────────────────────────────────────────────────────┘ │ │
│  │  ┌──────────────────────────────────────────────────────┐ │ │
│  │  │  modal-edit-roster.php                               │ │ │
│  │  │  - editRoster()                                      │ │ │
│  │  │  - handleEditRoster()                                │ │ │
│  │  │  - confirmDeleteRoster()                             │ │ │
│  │  │  - deleteRoster()                                    │ │ │
│  │  │  - duplicateRoster()                                 │ │ │
│  │  │  - assignEmployees()                                 │ │ │
│  │  └──────────────────────────────────────────────────────┘ │ │
│  │  ┌──────────────────────────────────────────────────────┐ │ │
│  │  │  modal-view-roster.php                               │ │ │
│  │  │  - viewRoster()                                      │ │ │
│  │  └──────────────────────────────────────────────────────┘ │ │
│  └────────────────────────────────────────────────────────────┘ │
│                                                                   │
│  ┌────────────────────────────────────────────────────────────┐ │
│  │  CORRECTION MODALS                                         │ │
│  │  ┌──────────────────────────────────────────────────────┐ │ │
│  │  │  modal-add-correction.php                            │ │ │
│  │  │  - openAddCorrectionModal()                          │ │ │
│  │  │  - handleAddCorrection()                             │ │ │
│  │  │  - searchEmployeeForCorrection()                     │ │ │
│  │  │  - selectEmployeeForCorrection()                     │ │ │
│  │  └──────────────────────────────────────────────────────┘ │ │
│  │  ┌──────────────────────────────────────────────────────┐ │ │
│  │  │  modal-edit-correction.php                           │ │ │
│  │  │  - editCorrection()                                  │ │ │
│  │  │  - handleEditCorrection()                            │ │ │
│  │  │  - confirmDeleteCorrection()                         │ │ │
│  │  │  - deleteCorrection()                                │ │ │
│  │  └──────────────────────────────────────────────────────┘ │ │
│  │  ┌──────────────────────────────────────────────────────┐ │ │
│  │  │  modal-view-correction.php                           │ │ │
│  │  │  - viewCorrection()                                  │ │ │
│  │  └──────────────────────────────────────────────────────┘ │ │
│  └────────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────┘
```

## 🔄 Data Flow

### Roster Creation Flow
```
User Action                 Function Call              Data Update
───────────                ──────────────             ────────────
Click "New Roster"    →    openAddRosterModal()   →   Modal opens
Fill form             →    (user input)           →   Change tracking
Click "Save"          →    handleAddRoster()      →   Validation
                      →    (if valid)             →   window.rosters.push()
                      →    renderRosterTable()    →   Table refresh
                      →    closeModal(true)       →   Modal closes
                      →    showToast()            →   Success message
```

### Correction Approval Flow
```
User Action                 Function Call              Data Update
───────────                ──────────────             ────────────
Click approve icon    →    approveCorrection(id)  →   Find correction
                      →    (update status)        →   status = 'Approved'
                      →    (set approval info)    →   approvedBy, approvedDate
                      →    renderCorrectionTable()→   Table refresh
                      →    showToast()            →   Success message
```

### Import Details Flow
```
User Action                 Function Call              Data Update
───────────                ──────────────             ────────────
Click view icon       →    viewImportDetails(id)  →   Find import
                      →    (build modal content)  →   Statistics calculated
                      →    openModal()            →   Modal opens
Click "View Data"     →    navigateToPreviewData()→   Drill-down view
Click "Download Log"  →    downloadImportLog()    →   File download
```

## 🗂️ Data Structure

### Global State
```javascript
window.rosters = [
    {
        id: 'RST-1234567890',
        shiftName: 'Morning Shift',
        companyId: 'NovaCore Solutions Inc.',
        startTime: '08:00',
        endTime: '17:00',
        breakDuration: 60,
        overtimeRule: 'After 8 hours - 1.25x rate',
        lateGracePeriod: 15,
        effectiveDate: 'Jan 15, 2024',
        notes: 'Standard morning shift',
        createdBy: 'Current User',
        createdDate: 'Jan 15, 2024'
    }
];

window.corrections = [
    {
        id: 'COR-1234567890',
        employeeId: 1,
        employeeName: 'Sarah Miller',
        employeeEmail: 'sarah.miller@novacore.com',
        avatar: 'SM',
        color: 'linear-gradient(145deg, #4f46e5, #7c3aed)',
        type: 'Late',
        originalDate: 'Jan 15, 2024',
        timeIn: '08:30',
        timeOut: '17:00',
        reason: 'Traffic delay',
        status: 'Pending',
        requestedBy: 'Current User',
        requestedDate: 'Jan 15, 2024',
        approvedBy: null,
        approvedDate: null
    }
];

window.importHistory = [
    {
        id: 'IMP-2024-001',
        fileName: 'attendance_mar_15_2024.csv',
        fileType: 'CSV',
        importDate: 'Mar 15, 2024 09:30 AM',
        importedBy: 'Monica White',
        totalRecords: 520,
        successful: 520,
        failed: 0,
        status: 'Success'
    }
];

window.importPreviewData = {
    'IMP-2024-001': {
        headers: ['Employee ID', 'Name', 'Date', 'Time In', 'Time Out'],
        rows: [
            ['EMP-001', 'Sarah Miller', '2024-03-15', '08:00', '17:00']
        ]
    }
};
```

## 🎨 Component Hierarchy

```
attendance.php
├── sidebar.php (navigation)
├── modals/modal-wrapper.php (base modal system)
│   ├── Modal overlay
│   ├── Modal panel
│   ├── Modal header
│   ├── Modal content
│   ├── Modal footer
│   └── Confirmation dialog
└── modals/attendance-modal/
    ├── modal-attendance-helpers.php (utilities)
    ├── Roster Modals
    │   ├── modal-add-roster.php
    │   ├── modal-edit-roster.php
    │   └── modal-view-roster.php
    └── Correction Modals
        ├── modal-add-correction.php
        ├── modal-edit-correction.php
        └── modal-view-correction.php
```

## 🔌 Function Dependencies

```
openAddRosterModal()
├── openModal() [from modal-wrapper.php]
├── formatDateForInput() [from helpers]
└── handleAddRoster()
    ├── showValidationErrors() [from helpers]
    ├── formatDateForDisplay() [from helpers]
    ├── renderRosterTable() [from attendance.php]
    ├── closeModal() [from modal-wrapper.php]
    └── showToast() [from helpers]

editRoster(id)
├── openModal() [from modal-wrapper.php]
├── formatDateForInput() [from helpers]
├── calculateDuration() [from helpers]
└── handleEditRoster()
    ├── showValidationErrors() [from helpers]
    ├── formatDateForDisplay() [from helpers]
    ├── renderRosterTable() [from attendance.php]
    ├── closeModal() [from modal-wrapper.php]
    └── showToast() [from helpers]

viewRoster(id)
├── openModal() [from modal-wrapper.php]
├── calculateDuration() [from helpers]
└── (read-only display)

approveCorrection(id)
├── renderCorrectionTable() [from attendance.php]
└── showToast() [from helpers]
```

## 📦 Module Exports

### Global Functions (window object)
```javascript
// Roster Management
window.openAddRosterModal
window.viewRoster
window.editRoster
window.duplicateRoster
window.assignEmployees
window.handleAddRoster
window.handleEditRoster
window.confirmDeleteRoster
window.deleteRoster
window.updateDurationPreview
window.updateDurationPreviewEdit

// Correction Management
window.openAddCorrectionModal
window.viewCorrection
window.editCorrection
window.approveCorrection
window.rejectCorrection
window.handleAddCorrection
window.handleEditCorrection
window.confirmDeleteCorrection
window.deleteCorrection
window.searchEmployeeForCorrection
window.selectEmployeeForCorrection

// Import Management
window.viewImportDetails
window.downloadImportLog
window.viewErrors
window.downloadErrorReport
window.exportPreviewData

// Helper Functions
window.formatTimeForInput
window.formatDateForInput
window.formatDateForDisplay
window.calculateDuration
window.isValidTime
window.showValidationErrors
window.showToast
```

## 🎯 Event Flow

### Modal Lifecycle
```
1. OPEN
   ├── User clicks button/icon
   ├── Function called (e.g., openAddRosterModal())
   ├── Content HTML generated
   ├── openModal(title, content) called
   ├── Modal overlay displayed
   ├── Modal panel slides in
   ├── Change tracking initialized
   └── Focus on first input

2. INTERACT
   ├── User fills form
   ├── Input events tracked
   ├── modalHasChanges = true
   ├── Real-time validation (optional)
   └── Preview updates (e.g., duration)

3. SUBMIT
   ├── Form submit event
   ├── Validation runs
   ├── If errors: showValidationErrors()
   ├── If valid: Save data
   ├── Update global arrays
   ├── Refresh tables
   ├── closeModal(true)
   └── showToast('Success')

4. CLOSE
   ├── User clicks X, ESC, or backdrop
   ├── attemptCloseModal() called
   ├── If modalHasChanges: Show confirmation
   ├── If confirmed or no changes: closeModal()
   ├── Modal panel slides out
   ├── Overlay fades out
   └── modalHasChanges = false
```

## 🔐 Security Considerations

### Input Sanitization
```javascript
// All user input is escaped before display
function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>]/g, m => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;'
    })[m] || m);
}
```

### Validation
- Client-side validation for UX
- Server-side validation required (future)
- XSS prevention via escapeHtml()
- SQL injection prevention (backend)

## 📊 Performance Optimization

### Lazy Loading
- Modals loaded on page load (small footprint)
- Content generated on demand
- Tables paginated (8 items per page)

### Event Delegation
- Single event listener for table actions
- Prevents memory leaks
- Efficient for dynamic content

### Data Management
- In-memory arrays for fast access
- Filtered arrays for search/filter
- Pagination for large datasets

## 🧩 Integration Points

### With Backend (Future)
```javascript
// Example API integration
async function handleAddRoster(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    
    // Client-side validation
    const errors = validateRoster(formData);
    if (errors.length > 0) {
        showValidationErrors(errors);
        return;
    }
    
    // API call
    try {
        const response = await fetch('/api/rosters/create', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            window.rosters.unshift(result.data);
            renderRosterTable(window.rosters);
            closeModal(true);
            showToast('Roster created successfully!', 'success');
        } else {
            showValidationErrors(result.errors);
        }
    } catch (error) {
        showToast('Failed to create roster', 'error');
    }
}
```

---

**Last Updated:** May 21, 2026
**Version:** 1.0.0
