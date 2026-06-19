<?php

namespace App\Ai\Middleware;

use App\Models\ToolExecution;
use Closure;
use Illuminate\Support\Facades\Event;
use Laravel\Ai\Events\InvokingTool;
use Laravel\Ai\Events\ToolInvoked;
use Laravel\Ai\Prompts\AgentPrompt;

class LogToolCalls
{
    public function handle(AgentPrompt $prompt, Closure $next)
    {
        $startTime = microtime(true);

        $invokingCallback = function (InvokingTool $event) {
            $event->tool->startTime = microtime(true);
        };

        $invokedCallback = function (ToolInvoked $event) use ($startTime) {
            $duration = (int) ((microtime(true) - ($event->tool->startTime ?? $startTime)) * 1000);

            $resultStr = is_string($event->result) ? $event->result : (is_array($event->result) ? json_encode($event->result, JSON_UNESCAPED_UNICODE) : (string) ($event->result ?? ''));

            ToolExecution::create([
                'tool_name' => $event->tool->name(),
                'arguments' => $event->arguments,
                'result_summary' => mb_strlen($resultStr) > 500 ? mb_substr($resultStr, 0, 500).'...' : $resultStr,
                'duration_ms' => $duration,
                'success' => true,
                'error_message' => null,
            ]);
        };

        $invokingId = $prompt->agent::class;
        $invokedId = $prompt->agent::class;

        Event::listen(
            InvokingTool::class,
            $invokingCallback
        );

        Event::listen(
            ToolInvoked::class,
            $invokedCallback
        );

        try {
            return $next($prompt);
        } catch (\Throwable $e) {
            ToolExecution::create([
                'tool_name' => 'unknown',
                'arguments' => [],
                'result_summary' => $e->getMessage(),
                'duration_ms' => (int) ((microtime(true) - $startTime) * 1000),
                'success' => false,
                'error_message' => $e->getMessage(),
            ]);

            throw $e;
        } finally {
            Event::forget(InvokingTool::class);
            Event::forget(ToolInvoked::class);
        }
    }
}
