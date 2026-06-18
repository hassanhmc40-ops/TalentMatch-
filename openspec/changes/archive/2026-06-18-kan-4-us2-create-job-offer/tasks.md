## 1. Database & Migration

- [x] 1.1 Run `php artisan make:migration create_job_offers_table` to create the migration for `job_offers` table
- [x] 1.2 Add columns: `user_id` (foreign to `users`), `title`, `description`, `required_skills` (JSON), `min_experience_years` (integer, default 0), timestamps
- [x] 1.3 Add composite index on `(user_id, created_at)` and foreign key with `ON DELETE CASCADE`
- [x] 1.4 Run `php artisan migrate` to apply the migration

## 2. Model & Factory

- [x] 2.1 Run `php artisan make:model JobOffer -mf` to create model with factory
- [x] 2.2 Add `$fillable` or `$guarded` properties to `JobOffer` model
- [x] 2.3 Add `required_skills` array cast and `$casts` property
- [x] 2.4 Add `belongsTo(User::class)` relationship on `JobOffer`
- [x] 2.5 Add `hasMany` relationship placeholder for candidate analyses (optional, for future)
- [x] 2.6 Add `hasMany(JobOffer::class)` on `User` model
- [x] 2.7 Configure `JobOfferFactory` with fake French data (title, description, skills array, experience years)

## 3. Form Request Validation

- [x] 3.1 Run `php artisan make:request StoreJobOfferRequest` to create the Form Request
- [x] 3.2 Add validation rules: title (required, string, max:255), description (required, string, min:10), required_skills (required, array, min:1), required_skills.* (required, string, max:100, distinct), min_experience_years (required, integer, min:0, max:50)
- [x] 3.3 Add French validation error messages for each rule using `messages()` method
- [x] 3.4 Override `authorize()` to return `true` (access check is handled by auth middleware + policy)

## 4. Authorization Policy

- [x] 4.1 Run `php artisan make:policy JobOfferPolicy --model=JobOffer` to create the policy
- [x] 4.2 Add `create()` method returning `true` (any authenticated user can create)
- [x] 4.3 Register policy in `AuthServiceProvider`

## 5. Controller & Routes

- [x] 5.1 Run `php artisan make:controller JobOfferController` to create the controller
- [x] 5.2 Add `create()` method returning the `offres.create` view
- [x] 5.3 Add `store(StoreJobOfferRequest $request)` method: create `JobOffer` with `user_id = auth()->id()`, redirect to route with success flash message
- [x] 5.4 Register routes in `web.php`: `Route::resource('offres', JobOfferController::class)->only(['create', 'store'])->middleware(['auth', 'verified'])`

## 6. View Layer

- [x] 6.1 Create `resources/views/offres/create.blade.php` extending `layouts/app`
- [x] 6.2 Add form with fields: title (text), description (textarea), required_skills (repeater or textarea for comma-separated), min_experience_years (number)
- [x] 6.3 Add French labels: "Titre de l'offre", "Description", "Compétences requises", "Années d'expérience minimum"
- [x] 6.4 Display validation errors using `$errors` and `@error` Blade directives
- [x] 6.5 Display success flash message using `x-auth-session-status` or `@if(session('success'))`
- [x] 6.6 Add submit button with French label "Créer l'offre"

## 7. Navigation

- [x] 7.1 Add "Créer une offre" navigation link in `resources/views/layouts/navigation.blade.php` pointing to `route('offres.create')`

## 8. Testing

- [x] 8.1 Run `php artisan make:test JobOfferCreationTest --pest` to create the feature test
- [x] 8.2 Test: authenticated user can view the creation form (200 status, correct view, French labels present)
- [x] 8.3 Test: unauthenticated user is redirected to login when accessing creation form
- [x] 8.4 Test: successful creation stores job offer with correct data and associates it with the authenticated user
- [x] 8.5 Test: created offer `user_id` is set from `auth()->id()` not from request input
- [x] 8.6 Test: validation errors for missing title, short description, empty skills, duplicate skills
- [x] 8.7 Test: validation errors for negative and excessive `min_experience_years`
- [x] 8.8 Test: required skills are stored as JSON and return as PHP array via cast
- [x] 8.9 Test: success flash message is displayed after creation
- [x] 8.10 Run `php artisan test --compact --filter=JobOfferCreation` to verify all tests pass

## 9. Code Quality

- [x] 9.1 Run `vendor/bin/pint --format agent` to fix PHP code style
- [x] 9.2 Verify no debug/dd/dump statements remain in code
- [x] 9.3 Manually visit `/offres/creer` and submit a valid form to confirm end-to-end flow (manual step)
- [x] 9.4 Run `php artisan test --compact` to confirm no existing tests are broken

## 10. QA Checklist

> ⚠️ Manual verification steps — run these in the browser before archiving.

- [ ] 10.1 The creation form renders with French labels and no JS errors
- [ ] 10.2 Submitting empty form shows all validation errors in French
- [ ] 10.3 Submitting valid data persists the offer and shows success message
- [ ] 10.4 The new offer is linked to the authenticated user in the database
- [ ] 10.5 Unauthenticated access is redirected to login
- [ ] 10.7 The navigation shows the "Créer une offre" link
