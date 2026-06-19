## Context

The `ConversationalAgent` currently has static instructions that describe the role, tool usage rules, and clarification permission — but has no knowledge of the specific candidate, offer, or analysis the user is discussing. The `ConversationController` creates a deterministic conversation ID from `candidate-analysis-{analysis->id}` but never passes the analysis data to the agent.

The agent relies on three tools that require explicit IDs: `GetCandidateAnalysis` (needs `candidat_id`), `GetJobRequirements` (needs `offre_id`), `CompareCandidates` (needs `analyse_id_1` + `analyse_id_2`). Without context injection, the agent must guess or ask for these IDs.

## Goals / Non-Goals

**Goals:**
- Inject candidate name, analysis summary (score, recommendation, key skills), and offer title into the agent's instructions before the first turn
- Inject the current `candidat_id`, `offre_id`, and `analyse_id` so the agent can call tools without asking for them
- Define answer formatting rules for common question types (score, skills, recommendation, comparison)
- Define follow-up question handling (use previously established context)
- Define out-of-scope handling (politely redirect to analysis-related questions)
- Add a `dynamicContext` array parameter to `ConversationalAgent::instructions()` that prepends context to the system prompt
- Update `ConversationController::store()` and `ConversationController::stream()` to pass analysis context

**Non-Goals:**
- No new database tables, columns, or migrations
- No new tools or tool modifications
- No UI changes to the chat interface
- No changes to the memory pipeline or conversation persistence
- No changes to the `CvAnalysisAgent` or structured analysis flow

## Decisions

1. **Dynamic instructions via a setter method** — Add a `setContext()` method to `ConversationalAgent` that accepts an array of context data (candidateName, offerTitle, matchingScore, recommendation, candidatId, offreId, analyseId). The `instructions()` method prepends a context block before the role description. This keeps the agent class clean and testable.

2. **Context block format** — The injected context is a plain-text section at the top of the system prompt:
   ```
   Contexte actuel :
   - Candidat : {nom} (ID: {candidat_id})
   - Offre : {titre} (ID: {offre_id})
   - Analyse ID : {analyse_id}
   - Score de correspondance : {score}/100 ({niveau})
   - Recommandation : {recommandation}
   - Compétences clés : {top 5 compétences}
   ```
   This gives the agent everything it needs for zero-clarification answers and tool calls.

3. **Answer formatting rules** — New rules appended to the instructions section:
   ```
   ## Format des réponses
   - Pour les questions de score : réponds avec le nombre exact et le niveau (Faible/Moyen/Bon/Excellent)
   - Pour les questions de compétences : liste les compétences avec des puces
   - Pour les questions de recommandation : cite la recommandation et la justification
   - Pour les comparaisons : présente un tableau comparatif avec les scores, forces et lacunes
   ```

4. **Follow-up rule** — The instructions already include follow-up context via conversation memory. Add an explicit rule:
   ```
   - L'utilisateur peut poser des questions de suivi sans répéter le nom du candidat
   - Utilise le contexte de la conversation pour les questions de suivi
   ```

5. **Out-of-scope rule** — Add an explicit rule:
   ```
   - Si l'utilisateur pose une question hors sujet (non liée à l'analyse du candidat), réponds poliment que tu es uniquement spécialisé dans l'analyse des candidatures
   ```

6. **Controller wiring** — In `ConversationController::store()` and `stream()`, after loading the analysis but before creating the agent, call `$agent->setContext(...)` with the analysis data. This keeps the controller changes minimal.

## Risks / Trade-offs

- [Context injection overhead] — Adding context to each prompt increases token usage slightly. Mitigation: the context block is ~150 tokens, negligible compared to conversation history.
- [Stale context] — If the analysis is updated mid-conversation (unlikely in practice), the agent would have stale context. Mitigation: context is injected per-turn, so it refreshes with each message.
- [Over-reliance on context] — The agent might skip tool calls because it "knows" the data from context. Mitigation: instructions explicitly state that tool calls are still required for detailed/fresh data, and context is only for zero-clarification answers about the current candidate.
