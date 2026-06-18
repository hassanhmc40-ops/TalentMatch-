## Why

HR agents need to create job offers before they can submit candidates for AI analysis. Without a structured job offer creation flow, the system has no reference criteria (required skills, minimum experience) against which candidate CVs can be evaluated. This change implements the foundational CRUD operation — creating a job offer — which is the entry point for the entire candidate screening workflow defined by user story US2.

## What Changes

- New `JobOffer` (Offre) model with Eloquent casts for required skills array
- New database migration for `job_offers` table with title, description, required_skills (JSON), min_experience_years, and user_id foreign key
- New `StoreJobOfferRequest` Form Request with validation rules
- New `JobOfferPolicy` for authorization (user owns their offers)
- New `JobOfferController` with `create` and `store` methods
- New Blade views for the job offer creation form (French UI)
- New named routes for job offer creation under `offres.*` prefix
- Integration into the authenticated dashboard navigation
- Factory and tests for the creation flow (validation, authorization, edge cases)
- French locale UI labels and error messages for the form

## Capabilities

### New Capabilities
- `job-offer-creation`: HR user can create a job offer with title, description, required skills list, and minimum experience level. Includes form validation, authorization, and success feedback.

### Modified Capabilities

*None — this is the first capability being built.*

## Impact

- **Models**: New `App\Models\JobOffer` with `belongsTo User`, `casts` for `required_skills`, and `hasMany CandidateAnalysis`
- **Database**: New `job_offers` migration adding table with foreign key to `users`
- **Controllers**: New `App\Http\Controllers\JobOfferController`
- **Requests**: New `App\Http\Requests\StoreJobOfferRequest`
- **Policies**: New `App\Policies\JobOfferPolicy`
- **Views**: New Blade files under `resources/views/offres/` (French UI)
- **Routes**: New resourceful routes under `Route::resource('offres', JobOfferController::class)->only(['create', 'store'])`
- **Tests**: New `tests/Feature/JobOfferCreationTest.php`
- **Navigation**: Update `resources/views/layouts/navigation.blade.php` to link to offer creation
- **Dependencies**: None new — all within Laravel core + Breeze
