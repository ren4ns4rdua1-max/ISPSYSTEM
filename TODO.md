# Client Photo Feature - Show View Fix

## Current Issue
- Show view avatar shows letter (gradient) instead of photo
- Code has @if($client->photo) but may have path/onerror issue

## Plan
1. ✅ Updated resources/views/clients/show.blade.php: Fixed Profile Card photo onerror logic to match index.blade.php exactly
2. ✅ Verified resources/views/clients/edit.blade.php: Fixed photo preview structure with proper relative positioning
3. [ ] Test: Upload photo → Show view displays img not letter

## Steps
**Step 1**: Edit show.blade.php profile avatar (lines ~390-410)
**Step 2**: Test upload/edit
**Step 3**: Complete
