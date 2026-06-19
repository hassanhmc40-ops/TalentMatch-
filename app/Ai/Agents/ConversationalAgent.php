<?php

namespace App\Ai\Agents;

use App\Ai\Tools\CompareCandidates;
use App\Ai\Tools\GetCandidateAnalysis;
use App\Ai\Tools\GetJobRequirements;
use Laravel\Ai\Concerns\RemembersConversations;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Promptable;
use Stringable;

// Agent lifecycle: make() → continue()/forUser() → prompt()/stream()/queue()
// Memory pipeline (auto-registered by SDK when RemembersConversations is used
// and a conversation participant is set):
//   RemembersConversations trait ─→ RememberConversation middleware
//     ─→ DatabaseConversationStore (agent_conversations + messages tables)
class ConversationalAgent implements Agent, Conversational, HasTools
{
    use Promptable, RemembersConversations;

    public function instructions(): Stringable|string
    {
        return implode("\n\n", [
            "Tu es un assistant RH spécialisé dans l'analyse de candidats. Ton rôle est d'aider les recruteurs à comprendre les profils des candidats, leurs scores de correspondance, et leurs recommandations.",
            '## Utilisation des outils',
            "Tu DOIS utiliser les outils disponibles pour récupérer les données. N'invente jamais de scores, de compétences manquantes, d'exigences de poste, ou de résultats de comparaison. Les outils retournent des données réelles depuis la base de données.",
            '## Règles',
            '- Utilise toujours un outil avant de répondre à une question sur un candidat, une offre, ou une comparaison',
            '- Ne réponds jamais à partir de suppositions ou de connaissances générales',
            "- Si un outil retourne une erreur ou une absence de données, informe l'utilisateur en français",
            "- Justifie chaque réponse en t'appuyant sur les données retournées par les outils",
            '- Tu peux poser des questions de clarification si les paramètres sont insuffisants',
        ]);
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
