# Profile Photos Storage

This directory stores employee profile photos uploaded through the system.

## Directory Structure
```
profile_photos/
├── profile_{employeeId}_{timestamp}.{ext}
└── ...
```

## File Naming Convention
- Format: `profile_{employeeId}_{timestamp}.{extension}`
- Example: `profile_EMP-2024-001_1716123456.jpg`

## Supported Formats
- JPEG/JPG
- PNG
- GIF
- WebP

## Security Features
- Directory browsing disabled
- PHP execution disabled
- Only image files accessible
- Automatic cleanup of old photos when new ones are uploaded

## File Size Limits
- Maximum file size: 5MB (enforced by client-side validation)
- Recommended dimensions: 800x800px or smaller

## API Endpoints

### Upload Photo
- **Endpoint:** `/api/employees/upload_profile_photo.php`
- **Method:** POST
- **Content-Type:** application/json
- **Body:**
  ```json
  {
    "photo": "data:image/jpeg;base64,...",
    "employeeId": "EMP-2024-001",
    "filename": "optional_custom_name.jpg"
  }
  ```

### Delete Photo
- **Endpoint:** `/api/employees/delete_profile_photo.php`
- **Method:** POST or DELETE
- **Content-Type:** application/json
- **Body:**
  ```json
  {
    "employeeId": "EMP-2024-001"
  }
  ```
  or
  ```json
  {
    "filename": "profile_EMP-2024-001_1716123456.jpg"
  }
  ```

## Maintenance
- Old photos are automatically deleted when a new photo is uploaded for the same employee
- Manual cleanup can be performed by deleting files older than a certain date
- Orphaned photos (employees no longer in system) should be cleaned up periodically

## Permissions
- Directory: 0755 (rwxr-xr-x)
- Files: 0644 (rw-r--r--)

## Backup
- Include this directory in regular backup procedures
- Photos are critical employee data

## Notes
- Photos are stored as files, not in the database, for better performance
- Database stores only the URL/path to the photo
- Consider implementing image optimization/compression for production use
