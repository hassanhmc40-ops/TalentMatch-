<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ToolExecution extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'conversation_message_id',
        'tool_name',
        'arguments',
        'result_summary',
        'duration_ms',
        'success',
        'error_message',
    ];

    protected function casts(): array
    {
        return [
            'arguments' => 'array',
            'success' => 'boolean',
        ];
    }
}
