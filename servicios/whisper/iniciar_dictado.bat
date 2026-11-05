@echo off
title ReportFlow — Servicio Local de Dictado Medico (Whisper)
echo ==========================================================
echo   ReportFlow - Microservicio Local de Dictado Medico
echo   Motor: faster-whisper (CPU / Local On-Premise)
echo ==========================================================
echo.
cd /d "%~dp0"
py servidor_dictado.py
pause
