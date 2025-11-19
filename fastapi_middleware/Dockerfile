# ---- Etapa base ----
FROM python:3.11-slim

# Configuración de entorno
ENV PYTHONUNBUFFERED=1 \
    PYTHONDONTWRITEBYTECODE=1

# Directorio de trabajo dentro del contenedor
WORKDIR /app

# Copiar dependencias
COPY requirements.txt .

# Instalar dependencias (incluye FastAPI y extras)
RUN pip install --no-cache-dir --upgrade pip && \
    pip install --no-cache-dir -r requirements.txt

# Copiar el resto del proyecto
COPY . .

# Exponer el puerto donde corre FastAPI
EXPOSE 8020

# Comando por defecto (Uvicorn)
CMD ["uvicorn", "main:app", "--host", "0.0.0.0", "--port", "8020"]
