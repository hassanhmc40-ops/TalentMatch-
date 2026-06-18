## 1. Database & Migration

- [x] 1.1 Run `php artisan make:migration create_candidates_table` to create the migration
- [x] 1.2 Add columns: `name` (string, 255), `cv_text` (text), timestamps
- [x] 1.3 Run `php artisan migrate` to apply the migration

## 2. Candidate Model

- [x] 2.1 Run `php artisan make:model Candidate` to create the model
- [x] 2.2 Add `$fillable` with `name`, `cv_text`
- [x] 2.3 Add `CandidateFactory` for tests

## 3. Update StoreJobOfferRequest

- [x] 3.1 Add `use App\Models\JobOffer; use Illuminate\Support\Facades\Gate;` imports
- [x] 3.2 Change `authorize()` to return `Gate::allows('create', JobOffer::class)` using the policy
- [x] 3.3 Add `prepareForValidation()` method: trim `title` and `description`, filter empty strings from `required_skills`
- [x] 3.4 Add `attributes()` method returning French field names

## 4. Create SubmitCandidateRequest

- [x] 4.1 Run `php artisan make:request SubmitCandidateRequest` to create the Form Request
- [x] 4.2 Add `authorize()` returning `true` (auth handled by middleware)
- [x] 4.3 Add validation rules: `nom`, `cv_text`, `offre_id`
- [x] 4.4 Add `withValidator()` for duplicate candidate check
- [x] 4.5 Add `prepareForValidation()` to trim
- [x] 4.6 Add French `messages()`
- [x] 4.7 Add French `attributes()`

## 5. Testing

- [x] 5.1 Run `php artisan make:test FormRequestsValidationTest --pest` to create the test
- [x] 5.2-5.16 All 14 tests written and passing
- [x] 5.17 `php artisan test --compact --filter=FormRequestsValidation` — 14/14 passed

## 6. Code Quality

- [x] 6.1 Run `vendor/bin/pint --format agent` — style fixed
- [x] 6.2 No debug statements remain
- [x] 6.3 Full test suite: 52/52 passed

## 7. QA Checklist

> ⚠️ Manual verification steps — run these in the browser or via artisan tinker.

- [ ] 7.1 StoreJobOfferRequest authorization works for authenticated users
- [ ] 7.2 StoreJobOfferRequest French attribute names appear in error messages
- [ ] 7.3 StoreJobOfferRequest trims whitespace input before validation
- [ ] 7.4 SubmitCandidateRequest accepts valid submissions
- [ ] 7.5 SubmitCandidateRequest French error messages display correctly
- [ ] 7.6 SubmitCandidateRequest duplicate detection works
- [ ] 7.7 All tests pass with no regressions
