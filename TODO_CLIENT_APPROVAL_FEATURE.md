# Client Approval & Task Assignment Feature

## Implementation Plan

### Step 1: Update ClientController.php
- Add `approveAndAssign()` method that approves client and creates InstallationJob

### Step 2: Update routes/web.php
- Add route for `/clients/{client}/approve-and-assign`
- Add routes for technician job status updates

### Step 3: Update pending.blade.php
- Add technician dropdown in modal
- Add "Approve & Assign Task" button with modal

### Step 4: Update TechnicianDashboardController.php
- Add `updateJobStatus()` method for technicians to update task status

### Step 5: Update tasks.blade.php
- Add action buttons for status updates (Start, Complete)

## Status
- [ ] Step 1: ClientController - approveAndAssign method
- [ ] Step 2: routes/web.php - new routes
- [ ] Step 3: pending.blade.php - add modal with technician dropdown
- [ ] Step 4: TechnicianDashboardController - updateJobStatus method
- [ ] Step 5: tasks.blade.php - add action buttons
