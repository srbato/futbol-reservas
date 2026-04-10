<?php

namespace App\Services;

use App\Models\Tournament;
use App\Models\User;
use Illuminate\Support\Str;

class TournamentService
{
    /**
     * Crear torneo en estado draft.
     * Genera slug unico desde el nombre.
     */
    public function createTournament(User $user, array $data): Tournament
    {
        $data['organizer_user_id'] = $user->id;
        $data['status'] = Tournament::STATUS_DRAFT;
        $data['slug'] = $this->generateUniqueSlug($data['name']);

        return Tournament::create($data);
    }

    /**
     * Publicar torneo (draft -> open_registration).
     * Valida que tenga datos minimos.
     */
    public function publishTournament(Tournament $tournament): void
    {
        abort_if($tournament->status !== Tournament::STATUS_DRAFT, 422, 'El torneo no esta en borrador.');

        // Verificar que tenga al menos una cancha asignada
        $hasFields = $tournament->fields()->count() > 0 || $tournament->external_venue_name;
        abort_if(!$hasFields, 422, 'El torneo necesita al menos una cancha asignada.');

        // Si tiene canchas de la plataforma, todas las solicitudes deben estar aprobadas
        if ($tournament->fields()->count() > 0) {
            $tournament->recalculateApprovalStatus();
            $tournament->refresh();

            if ($tournament->venue_approval_status !== 'approved') {
                $pendingCount = $tournament->venueRequests()->where('status', 'pending')->count();
                $rejectedCount = $tournament->venueRequests()->where('status', 'rejected')->count();

                if ($rejectedCount > 0) {
                    abort(422, 'Una o mas canchas rechazaron la solicitud. Eliminalas o elegi otras antes de publicar.');
                }
                abort(422, "Todavia hay {$pendingCount} cancha(s) sin aprobar. Espera la confirmacion antes de publicar.");
            }
        }

        $tournament->update(['status' => Tournament::STATUS_OPEN_REGISTRATION]);
    }

    /**
     * Cerrar inscripcion (open_registration -> registration_closed).
     * Requiere al menos 2 equipos confirmados.
     */
    public function closeRegistration(Tournament $tournament): void
    {
        abort_if($tournament->status !== Tournament::STATUS_OPEN_REGISTRATION, 422, 'El torneo no está abierto a inscripciones.');
        abort_if($tournament->confirmedTeams()->count() < 2, 422, 'Se necesitan al menos 2 equipos para cerrar la inscripción.');

        $tournament->update(['status' => Tournament::STATUS_REGISTRATION_CLOSED]);
    }

    /**
     * Iniciar torneo (registration_closed -> in_progress).
     */
    public function startTournament(Tournament $tournament): void
    {
        abort_if($tournament->status !== Tournament::STATUS_REGISTRATION_CLOSED, 422, 'El torneo no tiene la inscripción cerrada.');

        $tournament->update([
            'status' => Tournament::STATUS_IN_PROGRESS,
            'actual_start_date' => now()->toDateString(),
        ]);
    }

    /**
     * Cancelar torneo.
     */
    public function cancelTournament(Tournament $tournament, ?string $reason = null): void
    {
        abort_if(in_array($tournament->status, [Tournament::STATUS_FINISHED, Tournament::STATUS_CANCELLED]), 422, 'El torneo ya finalizó o fue cancelado.');

        $tournament->update([
            'status' => Tournament::STATUS_CANCELLED,
            'cancelled_at' => now(),
            'cancel_reason' => $reason,
        ]);
    }

    /**
     * Finalizar torneo (in_progress -> finished).
     * Solo si la final tiene ganador.
     */
    public function finishTournament(Tournament $tournament): void
    {
        abort_if($tournament->status !== Tournament::STATUS_IN_PROGRESS, 422, 'El torneo no está en curso.');

        $tournament->update(['status' => Tournament::STATUS_FINISHED]);
    }

    /**
     * Generar slug unico.
     */
    private function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $original = $slug;
        $count = 1;

        while (Tournament::where('slug', $slug)->exists()) {
            $count++;
            $slug = $original . '-' . $count;
        }

        return $slug;
    }
}
