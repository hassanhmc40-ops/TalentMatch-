## Why

HR agents currently have no way to see an overview of all their job offers. After creating offers (US2), they need a paginated list view showing each offer with key details and the number of candidates already analyzed — so they can decide which offers need attention and navigate to the next step in the workflow (US4: offer detail).

## What Changes

- Add a paginated job offer listing page at `GET /offres`
- Add an `index` action to `JobOfferController` with eager loading to prevent N+1
- Add a `viewAny` authorization policy to scope the list to the authenticated user's offers
- Display a candidate count per offer (via `candidateAnalyses` relation count)
- Add a French-language blade view for the list with navigation from the dashboard
- Update the existing `job-offer-creation` spec to cover listing requirements
- Update routes to enable the `index` route on the existing resourceful controller

## Capabilities

### New Capabilities
- `job-offer-listing`: Paginated listing of the authenticated user's job offers with candidate count per offer, ownership scoping, and N+1 prevention.

### Modified Capabilities
- (none — listed under New Capabilities; `job-offer-creation` spec remains unmodified since listing is a separate capability)

## Impact

- `app/Http/Controllers/JobOfferController.php` — add `index()` method
- `app/Policies/JobOfferPolicy.php` — update `viewAny()` to return `true` (ownership filtered in controller query)
- `routes/web.php` — add `index` to the `only()` array on the resource route
- `resources/views/offres/index.blade.php` — new listing view
- `composer.json` — (no new dependencies)
