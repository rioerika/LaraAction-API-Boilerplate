<?php

declare(strict_types=1);

namespace App\Actions\Audit;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

final class RecordAuditLogAction
{
    /**
     * @param  array<string, mixed>  $oldValues
     * @param  array<string, mixed>  $newValues
     * @param  array<string, mixed>  $metadata
     */
    public function handle(
        string $event,
        Model $subject,
        array $oldValues = [],
        array $newValues = [],
        array $metadata = [],
    ): AuditLog {
        $request = request();

        return AuditLog::query()->create([
            'actor_id' => $this->resolveActorId(),
            'event' => $event,
            'subject_type' => $subject::class,
            'subject_id' => (string) $subject->getKey(),
            'subject_name' => $this->resolveSubjectName($subject),
            'old_values' => $oldValues !== [] ? $oldValues : null,
            'new_values' => $newValues !== [] ? $newValues : null,
            'metadata' => $metadata !== [] ? $metadata : null,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }

    private function resolveActorId(): ?int
    {
        $actorId = Auth::id();

        if (is_int($actorId)) {
            return $actorId;
        }

        if (is_string($actorId) && ctype_digit($actorId)) {
            return (int) $actorId;
        }

        return null;
    }

    private function resolveSubjectName(Model $subject): ?string
    {
        $subjectName = $subject->getAttribute('name');

        if (is_string($subjectName) && $subjectName !== '') {
            return $subjectName;
        }

        $subjectEmail = $subject->getAttribute('email');

        if (is_string($subjectEmail) && $subjectEmail !== '') {
            return $subjectEmail;
        }

        return null;
    }
}
