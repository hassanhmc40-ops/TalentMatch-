<?php

namespace App\Ai\Agents;

use App\Ai\Middleware\LogToolCalls;
use App\Ai\Tools\CompareCandidates;
use App\Ai\Tools\GetCandidateAnalysis;
use App\Ai\Tools\GetJobRequirements;
use Laravel\Ai\Concerns\RemembersConversations;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasMiddleware;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Promptable;
use Stringable;

class ConversationalAgent implements Agent, Conversational, HasMiddleware, HasTools
{
    use Promptable, RemembersConversations;

    protected ?array $context = null;

    public function setContext(array $context): static
    {
        $this->context = $context;

        return $this;
    }

    public function middleware(): array
    {
        return [
            new LogToolCalls,
        ];
    }

    public function instructions(): Stringable|string
    {
        $parts = [];

        if ($this->context) {
            $ctx = $this->context;
            $parts[] = '## Contexte actuel';
            $parts[] = '- Candidat : '.($ctx['candidate_name'] ?? 'Inconnu').' (ID: '.($ctx['candidat_id'] ?? '?').')';
            $parts[] = '- Offre : '.($ctx['offer_title'] ?? 'Inconnue').' (ID: '.($ctx['offre_id'] ?? '?').')';
            $parts[] = '- Analyse ID : '.($ctx['analyse_id'] ?? '?');
            $parts[] = '- Score de correspondance : '.($ctx['matching_score'] ?? '?').'/100 ('.($ctx['score_level'] ?? '?').')';
            $parts[] = '- Recommandation : '.($ctx['recommendation'] ?? '?');
            if (! empty($ctx['key_skills'])) {
                $parts[] = '- Compétences clés : '.implode(', ', array_slice($ctx['key_skills'], 0, 5));
            }
            $parts[] = '';
        }

        $parts[] = "Tu es un assistant RH spécialisé dans l'analyse de candidats. Ton rôle est d'aider les recruteurs à comprendre les profils des candidats, leurs scores de correspondance, et leurs recommandations.";

        $parts[] = '## Utilisation des outils';
        $parts[] = "Tu DOIS utiliser les outils disponibles pour récupérer les données. N'invente jamais de scores, de compétences manquantes, d'exigences de poste, ou de résultats de comparaison. Les outils retournent des données réelles depuis la base de données.";
        if ($this->context) {
            $parts[] = 'Les IDs du contexte actuel (candidat_id, offre_id, analyse_id) sont déjà connus. Utilise-les directement pour les appels d\'outils sans demander à l\'utilisateur.';
        }

        $parts[] = '## Règles';
        $parts[] = '- Utilise toujours un outil avant de répondre à une question sur un candidat, une offre, ou une comparaison';
        $parts[] = '- Ne réponds jamais à partir de suppositions ou de connaissances générales';
        $parts[] = "- Si un outil retourne une erreur ou une absence de données, informe l'utilisateur en français";
        $parts[] = "- Justifie chaque réponse en t'appuyant sur les données retournées par les outils";
        $parts[] = '- Tu peux poser des questions de clarification si les paramètres sont insuffisants';
        $parts[] = "- L'utilisateur peut poser des questions de suivi sans répéter le nom du candidat. Utilise le contexte de la conversation et le contexte actuel pour répondre.";
        $parts[] = "- Si l'utilisateur pose une question hors sujet (non liée à l'analyse du candidat, aux offres d'emploi, ou aux RH), réponds poliment que tu es uniquement spécialisé dans l'analyse des candidatures et redirige vers le candidat actuel.";

        $parts[] = '## Format des réponses';
        $parts[] = '- Pour les questions de score : réponds avec le nombre exact et le niveau (Faible 0-30, Moyen 31-60, Bon 61-80, Excellent 81-100)';
        $parts[] = '- Pour les questions de compétences : liste les compétences avec des puces et mentionne les compétences manquantes si pertinent';
        $parts[] = '- Pour les questions de recommandation : cite la recommandation et la justification';
        $parts[] = '- Pour les comparaisons : présente un tableau comparatif avec les scores, forces, lacunes, et recommandations de chaque candidat, et mentionne la différence de score';

        return implode("\n\n", $parts);
    }

    public function tools(): iterable
    {
        return [
            new GetCandidateAnalysis,
            new GetJobRequirements,
            new CompareCandidates,
        ];
    }
}
