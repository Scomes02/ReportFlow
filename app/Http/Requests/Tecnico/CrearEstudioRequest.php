<?php

namespace App\Http\Requests\Tecnico;

use Illuminate\Foundation\Http\FormRequest;

class CrearEstudioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'paciente_nombre' => ['required', 'string', 'max:150'],
            'paciente_dni' => ['required', 'string', 'max:15'],
            'paciente_edad' => ['required', 'integer', 'min:0', 'max:120'],
            'especialidad_id' => ['required', 'exists:especialidades,id'],
            'tipo_estudio_id' => ['required', 'exists:tipos_estudio,id'],
            'fecha_estudio' => ['required', 'date'],
            'archivos' => ['required', 'array', 'min:1'],
            'archivos.*' => ['file', 'mimes:pdf,jpg,jpeg,png', 'max:20480'],
        ];
    }

    public function messages(): array
    {
        return [
            'archivos.required' => 'Adjuntá al menos un archivo del estudio.',
            'archivos.*.mimes' => 'Solo se aceptan archivos PDF, JPG o PNG.',
            'archivos.*.max' => 'Cada archivo no puede superar los 20MB.',
        ];
    }
}