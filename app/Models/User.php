<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'email', 'password', 'role', 'especialidad_id', 'two_factor_code', 'two_factor_expires_at', 'two_factor_enabled'])]
#[Hidden(['password', 'remember_token', 'two_factor_code'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_expires_at' => 'datetime',
            'two_factor_enabled' => 'boolean',
        ];
    }

    /**
     * Genera un código de 6 dígitos seguro y define su vencimiento (10 min).
     */
    public function generarCodigo2FA(): string
    {
        $codigo = sprintf('%06d', random_int(100000, 999999));

        $this->forceFill([
            'two_factor_code' => $codigo,
            'two_factor_expires_at' => now()->addMinutes(10),
        ])->save();

        return $codigo;
    }

    /**
     * Valida si el código proporcionado coincide y no está vencido.
     */
    public function validarCodigo2FA(string $codigo): bool
    {
        if (empty($this->two_factor_code) || empty($this->two_factor_expires_at)) {
            return false;
        }

        if ($this->two_factor_expires_at->isPast()) {
            return false;
        }

        return hash_equals((string) $this->two_factor_code, (string) $codigo);
    }

    /**
     * Limpia los datos de verificación tras un inicio exitoso.
     */
    public function limpiarCodigo2FA(): void
    {
        $this->forceFill([
            'two_factor_code' => null,
            'two_factor_expires_at' => null,
        ])->save();
    }

    // ============================================
    // RELACIONES 
    // ============================================

    /**
     * Especialidad asignada al médico
     */
    public function especialidad(): BelongsTo
    {
        return $this->belongsTo(Especialidad::class, 'especialidad_id');
    }

    /**
     * Estudios donde este usuario es el técnico
     */
    public function estudiosComoTecnico(): HasMany
    {
        return $this->hasMany(Estudio::class, 'tecnico_id');
    }

    /**
     * Estudios donde este usuario es el médico
     */
    public function estudiosComoMedico(): HasMany
    {
        return $this->hasMany(Estudio::class, 'medico_id');
    }
}
