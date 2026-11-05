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
            'paciente_telefono' => ['nullable', 'string', 'max:30'],
            'paciente_email' => ['nullable', 'email', 'max:150'],
            'especialidad_id' => ['required', 'exists:especialidades,id'],
            'tipo_estudio_id' => ['required', 'exists:tipos_estudio,id'],
            'fecha_estudio' => ['required', 'date'],
            'study_instance_uid' => ['nullable', 'string', 'max:255'],
            'accession_number' => ['nullable', 'string', 'max:64'],
            'modalidad' => ['nullable', 'string', 'max:16'],
            'archivos' => ['nullable', 'array', 'required_without:study_instance_uid'],
            'archivos.*' => ['file', 'mimes:pdf,jpg,jpeg,png,dcm', 'max:51200'],
        ];
    }

    public function messages(): array
    {
        return [
            'archivos.required_without' => 'Adjuntá al menos un archivo o especificá el StudyInstanceUID de PACS.',
            'archivos.*.mimes' => 'Solo se aceptan archivos PDF, JPG, PNG o DICOM (.dcm).',
            'archivos.*.max' => 'Cada archivo no puede superar los 50MB.',
            'paciente_email.email' => 'Ingresá un email válido para el paciente.',
        ];
    }
}