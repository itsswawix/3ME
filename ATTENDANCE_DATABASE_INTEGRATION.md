# 🗄️ Attendance Database Integration - Complete Guide

## ✅ What Has Been Created

### Database Migration
- **File:** `migrations/009_create_attendance_tables.sql`
- **Run Script:** `migrations/run_migration_009.php`
- **Tables Created:**
  - `rosters` - Shift rosters and attendance rules
  - `corrections` - Attendance correction requests
  - `import_history` - Import history tracking
  - `import_data` - Imported attendance records

### API Endpoints
- **`api/attendance/rosters.php`** - CRUD operations for rosters
- **`api/attendance/corrections.php`** - CRUD operations for corrections
- **`api/attendance/imports.php`** - Import history and preview data

### Frontend Updates
- **`app/views/attendance.php`** - Updated to load data from API instead of mock data

---

## 🚀 Setup Instructions

### Step 1: Run Database Migration

```bash
# Navigate to migrations folder
cd migrations

# Run the migration
php run_migration_009.php
```

**Expected Output:**
```
Starting Migration 009: Create attendance tables...
✓ Executed statement successfully
✓ Executed statement successfully
✓ Executed statement successfully
✓ Executed statement successfully

✅ Migration 009 completed successfully!
Attendance tables created:
  - rosters
  - corrections
  - import_history
  - import_data
```

### Step 2: Verify Tables Created

```sql
-- Check if tables exist
SHOW TABLES LIKE '%rosters%';
SHOW TABLES LIKE '%corrections%';
SHOW TABLES LIKE '%import%';

-- Check table structure
DESCRIBE rosters;
DESCRIBE corrections;
DESCRIBE import_history;
DESCRIBE import_data;
```

### Step 3: Test API Endpoints

```bash
# Test rosters endpoint
curl http://localhost/3ME/api/attendance/rosters.php

# Test corrections endpoint
curl http://localhost/3ME/api/attendance/corrections.php

# Test imports endpoint
curl http://localhost/3ME/api/attendance/imports.php
```

---

## 📊 Database Schema

### Rosters Table
```sql
CREATE TABLE rosters (
    id VARCHAR(50) PRIMARY KEY,
    shift_name VARCHAR(100) NOT NULL,
    company_id VARCHAR(50) NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    break_duration INT DEFAULT 0,
    overtime_rule VARCHAR(255) NOT NULL,
    late_grace_period INT DEFAULT 0,
    effective_date DATE NOT NULL,
    notes TEXT,
    created_by VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
);
```

### Corrections Table
```sql
CREATE TABLE corrections (
    id VARCHAR(50) PRIMARY KEY,
    employee_id VARCHAR(50) NOT NULL,
    type ENUM('Late', 'Early Departure', 'Missed Entry', 'Overtime Discrepancy') NOT NULL,
    original_date DATE NOT NULL,
    time_in TIME,
    time_out TIME,
    reason TEXT NOT NULL,
    status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
    requested_by VARCHAR(100),
    requested_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    approved_by VARCHAR(100),
    approved_date TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE
);
```

### Import History Table
```sql
CREATE TABLE import_history (
    id VARCHAR(50) PRIMARY KEY,
    file_name VARCHAR(255) NOT NULL,
    file_type VARCHAR(50) NOT NULL,
    import_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    imported_by VARCHAR(100) NOT NULL,
    total_records INT DEFAULT 0,
    successful INT DEFAULT 0,
    failed INT DEFAULT 0,
    status ENUM('Success', 'Partial', 'Failed') DEFAULT 'Success',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Import Data Table
```sql
CREATE TABLE import_data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    import_id VARCHAR(50) NOT NULL,
    employee_id VARCHAR(50),
    employee_name VARCHAR(255),
    date DATE,
    time_in TIME,
    time_out TIME,
    total_hours DECIMAL(5,2),
    status VARCHAR(50),
    remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (import_id) REFERENCES import_history(id) ON DELETE CASCADE,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE SET NULL
);
```

---

## 🔌 API Documentation

### Rosters API (`/api/attendance/rosters.php`)

#### GET - Fetch All Rosters
```javascript
fetch('/3ME/api/attendance/rosters.php')
  .then(response => response.json())
  .then(data => console.log(data));
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": "RST-1234567890",
      "shiftName": "Morning Shift",
      "companyId": "COMP-001",
      "company": "NovaCore Solutions Inc.",
      "startTime": "08:00",
      "endTime": "17:00",
      "breakDuration": 60,
      "overtimeRule": "After 8 hours - 1.25x rate",
      "lateGracePeriod": 15,
      "effectiveDate": "Jan 15, 2024",
      "notes": "",
      "createdBy": "Current User"
    }
  ]
}
```

#### POST - Create Roster
```javascript
fetch('/3ME/api/attendance/rosters.php', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    shiftName: "Morning Shift",
    companyId: "COMP-001",
    startTime: "08:00",
    endTime: "17:00",
    breakDuration: 60,
    overtimeRule: "After 8 hours - 1.25x rate",
    lateGracePeriod: 15,
    effectiveDate: "2024-01-15",
    notes: "",
    createdBy: "Current User"
  })
});
```

#### PUT - Update Roster
```javascript
fetch('/3ME/api/attendance/rosters.php', {
  method: 'PUT',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    id: "RST-1234567890",
    shiftName: "Updated Shift",
    // ... other fields
  })
});
```

#### DELETE - Delete Roster
```javascript
fetch('/3ME/api/attendance/rosters.php?id=RST-1234567890', {
  method: 'DELETE'
});
```

---

### Corrections API (`/api/attendance/corrections.php`)

#### GET - Fetch All Corrections
```javascript
fetch('/3ME/api/attendance/corrections.php')
  .then(response => response.json())
  .then(data => console.log(data));
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": "COR-1234567890",
      "employeeId": "EMP-001",
      "employeeName": "Sarah Miller",
      "employeeEmail": "sarah@example.com",
      "avatar": "SM",
      "color": "linear-gradient(145deg, #4f46e5, #7c3aed)",
      "type": "Late",
      "originalDate": "Jan 15, 2024",
      "timeIn": "08:30",
      "timeOut": "17:00",
      "reason": "Traffic delay",
      "status": "Pending",
      "requestedBy": "Current User",
      "requestedDate": "Jan 15, 2024"
    }
  ]
}
```

#### POST - Create Correction
```javascript
fetch('/3ME/api/attendance/corrections.php', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    employeeId: "EMP-001",
    type: "Late",
    originalDate: "2024-01-15",
    timeIn: "08:30",
    timeOut: "17:00",
    reason: "Traffic delay",
    requestedBy: "Current User"
  })
});
```

---

### Imports API (`/api/attendance/imports.php`)

#### GET - Fetch Import History
```javascript
fetch('/3ME/api/attendance/imports.php')
  .then(response => response.json())
  .then(data => console.log(data));
```

#### GET - Fetch Import Preview Data
```javascript
fetch('/3ME/api/attendance/imports.php?action=preview&import_id=IMP-2024-001')
  .then(response => response.json())
  .then(data => console.log(data));
```

#### POST - Create Import
```javascript
fetch('/3ME/api/attendance/imports.php', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    fileName: "attendance_jan_15.csv",
    fileType: "CSV",
    importedBy: "Current User",
    data: [
      {
        employee_id: "EMP-001",
        employee_name: "Sarah Miller",
        date: "2024-01-15",
        time_in: "08:00",
        time_out: "17:00",
        total_hours: 9.0,
        status: "Present",
        remarks: ""
      }
    ]
  })
});
```

---

## 🔄 Frontend Integration

### Data Loading
The attendance.php file now loads data from the API on page load:

```javascript
// Load data from API
async function loadAttendanceData() {
    try {
        // Load imports
        const importsResponse = await fetch(`${API_BASE}/imports.php`);
        const importsData = await importsResponse.json();
        if (importsData.success) {
            window.importHistory = importsData.data;
        }
        
        // Load rosters
        const rostersResponse = await fetch(`${API_BASE}/rosters.php`);
        const rostersData = await rostersResponse.json();
        if (rostersData.success) {
            window.rosters = rostersData.data;
        }
        
        // Load corrections
        const correctionsResponse = await fetch(`${API_BASE}/corrections.php`);
        const correctionsData = await correctionsResponse.json();
        if (correctionsData.success) {
            window.corrections = correctionsData.data;
        }
        
        // Refresh all tables
        renderImportHistoryLevel();
        renderRosterTable(filteredRosters);
        renderCorrectionTable(filteredCorrections);
        
    } catch (error) {
        console.error('Error loading attendance data:', error);
        showToast('Error loading data from server', 'error');
    }
}
```

### Modal Updates Needed

Update the following modal files to use API calls instead of local array manipulation:

1. **`modal-add-roster.php`** - Update `handleAddRoster()` to POST to API
2. **`modal-edit-roster.php`** - Update `handleEditRoster()` to PUT to API
3. **`modal-edit-roster.php`** - Update `deleteRoster()` to DELETE from API
4. **`modal-add-correction.php`** - Update `handleAddCorrection()` to POST to API
5. **`modal-edit-correction.php`** - Update `handleEditCorrection()` to PUT to API
6. **`modal-edit-correction.php`** - Update `deleteCorrection()` to DELETE from API

**Example Pattern:**
```javascript
// Instead of:
window.rosters.unshift(newRoster);
renderRosterTable(window.rosters);

// Use:
fetch('/3ME/api/attendance/rosters.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(rosterData)
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        loadAttendanceData(); // Reload from API
        closeModal(true);
        showToast('Roster created successfully!', 'success');
    }
});
```

---

## ✅ Testing Checklist

### Database
- [ ] Run migration successfully
- [ ] Verify all 4 tables created
- [ ] Check foreign key constraints
- [ ] Test table relationships

### API Endpoints
- [ ] GET rosters returns empty array initially
- [ ] POST roster creates new record
- [ ] PUT roster updates existing record
- [ ] DELETE roster removes record
- [ ] GET corrections returns empty array initially
- [ ] POST correction creates new record
- [ ] PUT correction updates existing record
- [ ] DELETE correction removes record
- [ ] GET imports returns empty array initially
- [ ] POST import creates history and data records

### Frontend
- [ ] Page loads without errors
- [ ] Empty state shows correctly
- [ ] Create roster modal works
- [ ] Edit roster modal works
- [ ] Delete roster works
- [ ] Create correction modal works
- [ ] Edit correction modal works
- [ ] Delete correction works
- [ ] Import file uploads and processes
- [ ] Preview data displays correctly

---

## 🐛 Troubleshooting

### Migration Fails
```bash
# Check if database exists
mysql -u root -p -e "SHOW DATABASES LIKE '3me_hr';"

# Check if companies table exists (required for foreign key)
mysql -u root -p 3me_hr -e "SHOW TABLES LIKE 'companies';"

# If companies table missing, create it first
```

### API Returns 500 Error
```bash
# Check PHP error log
tail -f /path/to/php/error.log

# Check Apache error log
tail -f /path/to/apache/error.log

# Enable error display temporarily
# In api file, add:
ini_set('display_errors', 1);
error_reporting(E_ALL);
```

### Data Not Loading
```javascript
// Open browser console (F12)
// Check for errors
console.log('Import History:', window.importHistory);
console.log('Rosters:', window.rosters);
console.log('Corrections:', window.corrections);

// Test API directly
fetch('/3ME/api/attendance/rosters.php')
  .then(r => r.json())
  .then(d => console.log(d));
```

---

## 📝 Next Steps

1. **Run the migration** to create database tables
2. **Test API endpoints** using curl or Postman
3. **Update modal files** to use API calls (I've started with modal-add-roster.php)
4. **Test the complete flow** from UI to database
5. **Add sample data** for testing
6. **Implement file upload** processing for imports

---

## 🎯 Summary

✅ **Database tables created** - 4 tables for attendance management
✅ **API endpoints created** - Full CRUD operations
✅ **Frontend updated** - Loads data from API
⏳ **Modal updates needed** - Convert to API calls
⏳ **Testing required** - End-to-end testing

**Status:** Ready for migration and testing!

---

**Created:** May 21, 2026
**Version:** 1.0.0
