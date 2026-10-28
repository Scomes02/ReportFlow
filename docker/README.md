# dcm4chee-arc-light PACS (Docker)

Esta carpeta contiene la configuración oficial de contenedores para ejecutar el servidor PACS **dcm4chee-arc-light 5.x** junto con su base de datos PostgreSQL y directorio LDAP.

## Servicios incluidos

| Contenedor | Imagen | Función | Puertos expuestos |
| :--- | :--- | :--- | :--- |
| **`dcm4chee-arc`** | `dcm4che/dcm4chee-arc-psql:5.31.0` | Servidor PACS DICOM / DICOMweb | `8080` (HTTP/REST), `11112` (DICOM C-STORE), `2575` (HL7) |
| **`pacs-db`** | `dcm4che/postgres-dcm4chee:16.0-31` | Base de datos de metadatos DICOM | Interno (5432) |
| **`pacs-ldap`** | `dcm4che/slapd-dcm4chee:2.6.6-31.0` | Configuración de AETs y nodos DICOM | Interno (389) |

---

## Cómo iniciarlo con Docker Desktop

1. Abre una terminal en esta carpeta (`ReportFlow/docker`):
   ```bash
   cd c:\laragon\www\ReportFlow\docker
   ```

2. Levanta los contenedores en segundo plano:
   ```bash
   docker compose up -d
   ```

3. Verifica que los 3 contenedores estén corriendo:
   ```bash
   docker compose ps
   ```

---

## Acceso al Servidor PACS

- **Panel de Control Web de dcm4chee:** [http://localhost:8080/dcm4chee-arc/ui2](http://localhost:8080/dcm4chee-arc/ui2)
- **AET por defecto:** `DCM4CHEE`
- **DICOMweb Endpoint (QIDO-RS / WADO-RS):** `http://localhost:8080/dcm4chee-arc/aets/DCM4CHEE/rs`

---

## Integración con ReportFlow

El archivo `.env` de ReportFlow ya está configurado para conectarse a este servidor:
```env
PACS_ENABLED=true
PACS_BASE_URL=http://localhost:8080
PACS_AET=DCM4CHEE
PACS_DICOMWEB_URL=http://localhost:8080/dcm4chee-arc/aets/DCM4CHEE/rs
PACS_VIEWER_URL=http://localhost:8080/dcm4chee-arc/ui2
```

Cuando los contenedores estén levantados, el indicador en la Worklist médica de ReportFlow cambiará automáticamente a:
🟢 **`PACS dcm4chee: Conectado (DCM4CHEE)`**
