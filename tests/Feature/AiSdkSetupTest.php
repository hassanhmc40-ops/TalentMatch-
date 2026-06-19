<?php

use Illuminate\Support\Facades\Config;

test('ai config is published and loads correctly', function () {
    expect(Config::get('ai.default'))->toBe('groq');
    expect(Config::get('ai.providers.groq.driver'))->toBe('groq');
    expect(Config::get('ai.providers.anthropic.driver'))->toBe('anthropic');
});

test('default models are configured', function () {
    expect(Config::get('ai.models.text'))->toBe('meta-llama/llama-4-scout-17b-16e-instruct');
    expect(Config::get('ai.models.embeddings'))->toBe('text-embedding-3-small');
});

test('agent conversations table exists after migration', function () {
    $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name='agent_conversations'");
    expect($tables)->not->toBeEmpty();
});

test('agent conversation messages table exists after migration', function () {
    $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name='agent_conversation_messages'");
    expect($tables)->not->toBeEmpty();
});

test('package is registered in composer', function () {
    $composer = json_decode(file_get_contents(base_path('composer.json')), true);
    expect($composer['require'])->toHaveKey('laravel/ai');
});

test('stubs were published', function () {
    expect(file_exists(base_path('stubs/agent.stub')))->toBeTrue();
    expect(file_exists(base_path('stubs/structured-agent.stub')))->toBeTrue();
    expect(file_exists(base_path('stubs/tool.stub')))->toBeTrue();
});
