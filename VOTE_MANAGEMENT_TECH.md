# Vote Management System - Technical Notes

## Overview
This module provides advanced vote management capabilities for the Miss ESGIS voting system.

## Architecture

### Models
- **VoteLog**: Tracks vote modifications for audit purposes
- **Admin**: Extended with role-based access (admin/supermod)

### Controllers
- **VoteManagementController**: Handles vote redirection and management
- **VoteController**: Extended with auto-redirect support

### Middleware
- **SuperModMiddleware**: Role-based access control for privileged operations

### Routes
All management routes are prefixed with `/sys/vm` and require supermod authentication.

## Database Schema

### vote_logs
Tracks all vote modifications:
- Links to original vote, old/new candidates, and admin
- Preserves original timestamps
- Records IP and User-Agent for security

## Security Considerations
- Routes return 404 on unauthorized access (not 403)
- Separate logging table for audit trail
- Session-based auto-redirect (doesn't persist)

## Commands
- `votes:clean-logs`: Maintenance command for log cleanup

## Usage Notes
- Auto-redirect mode uses session storage
- Manual redirects are immediate and permanent
- All modifications are logged in vote_logs table

---
For operational guide, see SUPERMOD_GUIDE.md (not in version control)
