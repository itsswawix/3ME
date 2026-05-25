# Profile Photos - Quick Start Guide

## For Users

### Adding a Profile Photo

1. **Open Add/Edit Employee Modal**
   - Click "Add Employee" or "Edit" on an existing employee

2. **Choose Upload Method**
   - **Take Photo:** Click to open webcam
     - Click "Capture Photo"
     - Review the preview
     - Click "Use This Photo" to confirm or "Retake" to try again
   
   - **Upload Photo:** Click to select a file
     - Choose an image file (JPG, PNG, GIF)
     - Maximum size: 5MB
     - Photo will preview automatically

3. **Save**
   - Complete the form
   - Click "Add Employee" or "Save Changes"
   - Photo will be uploaded automatically

### Removing a Profile Photo

1. Open the Edit Employee modal
2. Click the "Remove" button under the photo
3. Save changes

## For Developers

### Quick Integration

```javascript
// Upload photo
const result = await uploadProfilePhotoToServer(photoData, employeeId);
employee.profilePhoto = result.url;

// Delete photo
await deleteProfilePhotoFromServer(employeeId);
employee.profilePhoto = null;

// Display photo
<img src="${employee.profilePhoto || '/default-avatar.png'}" />
```

### API Endpoints

**Upload:**
```bash
POST /api/employees/upload_profile_photo.php
Content-Type: application/json

{
  "photo": "data:image/jpeg;base64,...",
  "employeeId": "EMP-2024-001"
}
```

**Delete:**
```bash
POST /api/employees/delete_profile_photo.php
Content-Type: application/json

{
  "employeeId": "EMP-2024-001"
}
```

## Troubleshooting

### Photo not uploading?
- Check file size (max 5MB)
- Verify file format (JPG, PNG, GIF only)
- Check browser console for errors

### Photo not displaying?
- Verify the file exists in `/uploads/profile_photos/`
- Check the URL in the database
- Clear browser cache

### Permission errors?
```bash
chmod 755 uploads/profile_photos/
chown www-data:www-data uploads/profile_photos/
```

## Maintenance

### Cleanup orphaned photos
```bash
php utils/cleanup_orphaned_photos.php
```

### Check storage usage
```bash
du -sh uploads/profile_photos/
```

## Support

- Full documentation: `/docs/PROFILE_PHOTOS_STORAGE.md`
- API documentation: See individual PHP files
- Issues: Contact system administrator
