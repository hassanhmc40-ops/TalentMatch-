## MODIFIED Requirements

### Requirement: compareCandidates compares two analyses for the same offer

The `CompareCandidates` tool SHALL accept `analyse_id_1` and `analyse_id_2` parameters and return a JSON comparison string. Both analyses MUST belong to the same job offer and be accessible to the authenticated user. The response SHALL include per-candidate data, score difference, and a multi-dimensional comparison analysis with a verdict.

#### Scenario: Returns enhanced comparison for authorized same-offer analyses
- **WHEN** the tool is called with two valid analysis IDs belonging to the same offer accessible by the user
- **THEN** it SHALL return a JSON string containing each candidate's name, score, strengths, gaps, recommendation, and the score difference
- **AND** the response SHALL also include `comparaison_score` (integer 0-100), `verdict` (French string), `candidat_recommande` (string or null), `competences_exclusives_candidat_1` (array), and `competences_exclusives_candidat_2` (array)

#### Scenario: Returns error for cross-offer comparison
- **WHEN** the two analyses belong to different job offers
- **THEN** it SHALL return "Erreur : Impossible de comparer des candidats de différentes offres."

#### Scenario: Returns error when either analysis is unauthorized or missing
- **WHEN** either analysis ID does not exist or belongs to another user's offer
- **THEN** it SHALL return "Analyse non trouvée ou accès non autorisé."
