<?php

namespace Modules\AIBot\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AIBotChatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'messages' => ['required', 'array', 'min:1', 'max:50'],
            'messages.*.role' => ['required', 'string', 'in:user,assistant'],
            'messages.*.content' => ['required', 'string', 'max:24000'],
        ];
    }

    /** @return list<array{role: string, content: string}> */
    public function conversationMessages(): array
    {
        /** @var list<array{role?: string, content?: string}> $raw */
        $raw = $this->validated('messages');

        $out = [];
        foreach ($raw as $row) {
            $role = strtolower((string) ($row['role'] ?? '')) === 'assistant' ? 'assistant' : 'user';
            $out[] = [
                'role' => $role,
                'content' => (string) ($row['content'] ?? ''),
            ];
        }

        return $out;
    }
}
