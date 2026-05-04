# TODO - Welcome Page Plan Apply Feature

## Task
Fix the welcome.blade.php in the plans part that by clicking the apply in the plan it can show the fill up form of the new client to apply and then it will go to the admin that can decide if they approved the information and also the admin can set the technician to work it

## Completed Steps

### 1. ✅ Created storeGuest method in ClientController
- Added `storeGuest()` method in `app/Http/Controllers/ClientController.php`
- Handles guest client registration (no authentication required)
- Creates client with `pending_approval` status by default
- Returns JSON response for AJAX submission

### 2. ✅ Added guest route in web.php
- Added POST route `/clients/guest` pointing to `ClientController@storeGuest`
- Route name: `clients.storeGuest`

### 3. ✅ Added client registration modal in welcome.blade.php
- Added new `applyClientModal` HTML with client registration form
- Fields: name, email, phone_number, pppoe_name, barangay, nap_box, notes
- Modal styled to match the existing design

### 4. ✅ Added JavaScript for AJAX form submission
- Functions:
  - `openApplyModal(plan)` - Opens the modal
  - `closeApplyModal()` - Closes the modal
  - `submitApplication(event)` - Handles AJAX form submission
  - Modified `handleSubscribe(plan)` to open apply modal instead of login modal

### 5. ✅ Admin approval workflow (already exists)
- Pending clients page at `/clients/pending`
- Admin can approve or reject applications
- **Approve & Assign** feature that:
  - Approves the client (sets status to 'active')
  - Creates an InstallationJob
  - Assigns a technician
  - Sets scheduled date and job type

### 6. ✅ Technician job workflow (already exists)
- Technicians can view assigned jobs
- Start/complete job functionality
- Job history tracking

## How it works

1. **Public User Flow:**
   - User visits homepage (welcome.blade.php)
   - browses available plans in the Plans section
   - clicks "Apply Now" or "Get [Plan] Plan" button
   - fills out the client registration form in the modal
   - submits the application via AJAX
   - sees success toast message
   - Client is saved with `pending_approval` status

2. **Admin Flow:**
   - Admin logs in and goes to `/clients/pending`
   - sees list of pending client applications
   - clicks "Approve & Assign" button
   - selects a technician from dropdown
   - selects job type (new installation, repair, etc.)
   - sets scheduled date
   - clicks "Approve & Assign"
   - Client status changed to `active`
   - InstallationJob created and assigned to technician

3. **Technician Flow:**
   - Technician logs in and goes to `/technician/tasks`
   - sees assigned jobs
   - can start job and mark as complete

## Files Modified

- `app/Http/Controllers/ClientController.php` - Added storeGuest()
- `routes/web.php` - Added guest route
- `resources/views/welcome.blade.php` - Added apply modal and JS

## Files Already in Place (unchanged but used)

- `resources/views/clients/pending.blade.php` - Admin approval UI
- `app/Models/InstallationJob.php` - Job model
- `app/Models/Client.php` - Client model with approve/reject methods
- `app/Http/Controllers/TechnicianDashboardController.php` - Technician tasks
