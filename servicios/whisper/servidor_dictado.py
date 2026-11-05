import os
import sys
import tempfile
import time
from typing import Optional
from fastapi import FastAPI, File, UploadFile, Form, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from faster_whisper import WhisperModel

app = FastAPI(
    title="ReportFlow — Servicio Local de Dictado Médico (Whisper)",
    description="Microservicio local de transcripción médica de alta precisión para ReportFlow",
    version="1.0.0"
)

# Habilitar CORS para peticiones locales desde el navegador o Laravel
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Configuración del modelo Whisper
# Tamaños: 'tiny', 'base', 'small', 'medium'
MODELO_WHISPER = os.environ.get("WHISPER_MODELO", "small")
DISPOSITIVO = "cpu"
COMPUTE_TYPE = "int8" # 4x más rápido en CPU con bajo uso de memoria

print(f"[*] Inicializando Whisper Local con modelo '{MODELO_WHISPER}' en '{DISPOSITIVO}' ({COMPUTE_TYPE})...")
t0 = time.time()
try:
    modelo = WhisperModel(
        MODELO_WHISPER,
        device=DISPOSITIVO,
        compute_type=COMPUTE_TYPE,
        cpu_threads=4,
        num_workers=2
    )
    print(f"[+] Modelo Whisper '{MODELO_WHISPER}' cargado con éxito en {time.time() - t0:.2f} segundos.")
except Exception as e:
    print(f"[!] Error al cargar modelo Whisper: {e}")
    modelo = None

# Contexto radiológico para orientar la transcripción y terminología técnica
PROMPT_MEDICO_DEFAULT = (
    "Informe médico formal de diagnóstico por imágenes. Radiología, tomografía computada, resonancia magnética, "
    "ecografía doppler, densitometría, tórax, cerebro, columna lumbar, parénquima, hipodenso, hiperdenso, "
    "líquido cefalorraquídeo, derrame pleural, esteatosis, nódulo, calcificación, conclusión diagnóstica."
)

@app.get("/salud")
def estado_salud():
    return {
        "estado": "activo" if modelo is not None else "error_modelo",
        "modelo": MODELO_WHISPER,
        "dispositivo": DISPOSITIVO,
        "compute_type": COMPUTE_TYPE
    }

@app.post("/transcribir")
async def transcribir_audio(
    audio: UploadFile = File(...),
    prompt_personalizado: Optional[str] = Form(None)
):
    if modelo is None:
        raise HTTPException(status_code=500, detail="El modelo Whisper no está inicializado.")

    inicio = time.time()
    
    # Determinar extensión del archivo temporal
    nombre_archivo = audio.filename or "dictado.webm"
    _, extension = os.path.splitext(nombre_archivo)
    if not extension:
        extension = ".webm"

    temp_path = None
    try:
        # Guardar audio recibido en archivo temporal
        with tempfile.NamedTemporaryFile(delete=False, suffix=extension) as temp_file:
            contenido = await audio.read()
            temp_file.write(contenido)
            temp_path = temp_file.name

        prompt_final = prompt_personalizado if prompt_personalizado else PROMPT_MEDICO_DEFAULT

        # Ejecutar transcripción con faster-whisper
        segmentos, info = modelo.transcribe(
            temp_path,
            language="es",
            task="transcribe",
            initial_prompt=prompt_final,
            beam_size=5,
            vad_filter=True, # Filtro de silencio automático
            vad_parameters=dict(min_silence_duration_ms=500)
        )

        partes_texto = []
        for segmento in segmentos:
            partes_texto.append(segmento.text.strip())

        texto_final = " ".join(partes_texto).strip()
        duracion_proceso = round(time.time() - inicio, 2)

        return {
            "exito": True,
            "texto": texto_final,
            "idioma": info.language,
            "probabilidad_idioma": round(info.language_probability, 2),
            "duracion_audio": round(info.duration, 2),
            "tiempo_procesamiento_seg": duracion_proceso
        }

    except Exception as e:
        print(f"[!] Error durante la transcripción: {e}")
        raise HTTPException(status_code=500, detail=f"Error al procesar el audio: {str(e)}")

    finally:
        if temp_path and os.path.exists(temp_path):
            try:
                os.remove(temp_path)
            except Exception:
                pass

if __name__ == "__main__":
    import uvicorn
    puerto = int(os.environ.get("WHISPER_PUERTO", 8000))
    print(f"[*] Iniciando servidor FastAPI en http://127.0.0.1:{puerto}")
    uvicorn.run(app, host="127.0.0.1", port=puerto, log_level="info")
