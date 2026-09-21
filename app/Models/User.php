<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
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
        ];
    }

    // ============================================
    // RELACIONES 
    // ============================================

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

    /**
     * Especialidad del médico (si existe la relación)
     * Nota: Como no tenemos especialidad_id en users, 
     * obtenemos la especialidad de los estudios que ha firmado
     */
    public function especialidad()
    {
        // Obtener la especialidad más frecuente de sus estudios informados
        return $this->hasManyThrough(
            Especialidad::class,
            Estudio::class,
            'medico_id',        // Foreign key en estudios
            'id',               // Local key en especialidades
            'id',               // Local key en users
            'tipo_estudio_id'   // Foreign key en estudios (para unir con tipos_estudio)
        )->distinct();
    }
}
