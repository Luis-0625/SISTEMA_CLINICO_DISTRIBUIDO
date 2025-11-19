from fastapi import FastAPI, HTTPException, Depends, Header
from fastapi.middleware.cors import CORSMiddleware
from fastapi.responses import StreamingResponse
from jose import jwt
from datetime import datetime, timedelta
import psycopg2
import io
from fpdf import FPDF
from sqlalchemy import create_engine, Column, String, Boolean
from sqlalchemy.orm import sessionmaker, declarative_base, Session
from passlib.context import CryptContext

# ===========================================================
# 🚀 CONFIGURACIÓN GENERAL
# ===========================================================

app = FastAPI()

SECRET_KEY = "5tEsOad7hLcSHvti5yRCWYAO4NHuIqNsFyCvemv1omdfmiiaNv9LUfNFXFLvjQQ8"
ALGORITHM = "HS256"
ACCESS_TOKEN_EXPIRE_MINUTES = 60

# CORS para permitir acceso desde Laravel
origins = [
    "http://127.0.0.1:8000",
    "http://localhost:8000",
]
app.add_middleware(
    CORSMiddleware,
    allow_origins=origins,
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# ===========================================================
# 🔑 VALIDACIÓN DEL JWT QUE ENVÍA LARAVEL
# ===========================================================

def verify_token(authorization: str = Header(None)):
    if not authorization or not authorization.startswith("Bearer "):
        raise HTTPException(status_code=401, detail="Token requerido")

    token = authorization.split(" ")[1]

    try:
        payload = jwt.decode(token, SECRET_KEY, algorithms=[ALGORITHM])
        return payload  # Retorna claims como: documento_id, nombre, rol, sub
    except:
        raise HTTPException(status_code=401, detail="Token inválido o expirado")

# ===========================================================
# 🧩 CONEXIÓN A BASE DE DATOS (Citus)
# ===========================================================

def get_db_connection():
    return psycopg2.connect(
        host="citus-coordinator",
        port="5432",
        dbname="postgres",
        user="postgres",
        password="postgres"
    )

# ===========================================================
# 👥 LOGIN USANDO SQLALCHEMY (Módulo login_usuario)
# ===========================================================

DATABASE_URL = "postgresql://usuario:password@citus-coordinator:5432/hcd"

engine = create_engine(DATABASE_URL)
SessionLocal = sessionmaker(bind=engine)
Base = declarative_base()

pwd_context = CryptContext(schemes=["bcrypt"], deprecated="auto")

class LoginUsuario(Base):
    __tablename__ = "login_usuario"
    nombre_usuario = Column(String, primary_key=True)
    contrasena = Column(String)
    rol_id = Column(String)
    documento_id = Column(String)
    activo = Column(Boolean)

def get_db():
    db = SessionLocal()
    try:
        yield db
    finally:
        db.close()

def verify_password(plain_password, hashed_password):
    return pwd_context.verify(plain_password, hashed_password)

def create_token(username: str, rol_id: str, documento_id: str):
    expire = datetime.utcnow() + timedelta(minutes=ACCESS_TOKEN_EXPIRE_MINUTES)
    payload = {
        "sub": username,
        "rol": rol_id,
        "documento_id": documento_id,
        "exp": expire
    }
    return jwt.encode(payload, SECRET_KEY, algorithm=ALGORITHM)

# ===========================================================
# 🔐 LOGIN API
# ===========================================================

@app.post("/auth/login")
def login(username: str, password: str, db: Session = Depends(get_db)):
    user = db.query(LoginUsuario).filter(LoginUsuario.nombre_usuario == username).first()

    if not user or not user.activo:
        raise HTTPException(status_code=401, detail="Usuario no encontrado o inactivo")

    if not verify_password(password, user.contrasena):
        raise HTTPException(status_code=401, detail="Contraseña incorrecta")

    token = create_token(user.nombre_usuario, user.rol_id, user.documento_id)

    return {
        "access_token": token,
        "token_type": "bearer",
        "role": user.rol_id,
        "documento_id": user.documento_id
    }

# ===========================================================
# 🧍 PACIENTE
# ===========================================================

@app.get("/patient/{id}")
def get_paciente(id: int, user=Depends(verify_token)):
    conn = get_db_connection()
    cur = conn.cursor()
    cur.execute("SELECT * FROM hcd.usuario WHERE documento_id = %s;", (id,))
    paciente = cur.fetchone()
    cur.close()
    conn.close()

    if not paciente:
        raise HTTPException(status_code=404, detail="Paciente no encontrado")

    return {"paciente": paciente, "usuario_fastapi": user}

# ===========================================================
# 🏥 ADMISIONISTA — Registrar ingreso
# ===========================================================

@app.post("/admission/nuevo_ingreso")
def registrar_ingreso(data: dict, user=Depends(verify_token)):

    if user["rol"] != "admisionista":
        raise HTTPException(status_code=403, detail="Acceso denegado")

    conn = get_db_connection()
    cur = conn.cursor()
    cur.execute("""
        INSERT INTO hcd.ingresos (paciente_id, fecha_ingreso, motivo)
        VALUES (%s, NOW(), %s)
    """, (data["paciente_id"], data["motivo"]))
    conn.commit()

    cur.close()
    conn.close()

    return {"mensaje": "Ingreso registrado correctamente"}

@app.get("/admissions")
def listar_admisiones(user=Depends(verify_token)):
    conn = get_db_connection()
    cur = conn.cursor()
    cur.execute("SELECT * FROM hcd.ingresos ORDER BY fecha_ingreso DESC;")
    resultados = cur.fetchall()
    cur.close()
    conn.close()

    return {"admissions": resultados, "usuario_fastapi": user}


# ===========================================================
# 🩺 MÉDICO — Registrar observación
# ===========================================================

@app.post("/doctor/observacion")
def registrar_observacion(data: dict, user=Depends(verify_token)):

    if user["rol"] != "medico":
        raise HTTPException(status_code=403, detail="Acceso denegado")

    conn = get_db_connection()
    cur = conn.cursor()
    cur.execute("""
        INSERT INTO hcd.observaciones (paciente_id, descripcion, fecha)
        VALUES (%s, %s, NOW())
    """, (data["paciente_id"], data["descripcion"]))
    conn.commit()

    cur.close()
    conn.close()

    return {"mensaje": "Observación registrada exitosamente"}

# ===========================================================
# 📄 EXPORTAR PDF
# ===========================================================

@app.get("/results/export_pdf/{paciente_id}")
def exportar_pdf(paciente_id: int, user=Depends(verify_token)):

    if user["rol"] not in ["medico", "resultados"]:
        raise HTTPException(status_code=403, detail="Acceso denegado")

    conn = get_db_connection()
    cur = conn.cursor()
    cur.execute("""
        SELECT nombre_completo, edad, sexo 
        FROM hcd.usuario 
        WHERE documento_id = %s;
    """, (paciente_id,))
    data = cur.fetchone()
    cur.close()
    conn.close()

    if not data:
        raise HTTPException(status_code=404, detail="Paciente no encontrado")

    pdf = FPDF()
    pdf.add_page()
    pdf.set_font("Arial", size=12)

    pdf.cell(200, 10, txt="Historia Clínica del Paciente", ln=True, align="C")
    pdf.cell(200, 10, txt=f"Nombre: {data[0]}", ln=True)
    pdf.cell(200, 10, txt=f"Edad: {data[1]}", ln=True)
    pdf.cell(200, 10, txt=f"Sexo: {data[2]}", ln=True)

    output = io.BytesIO()
    pdf.output(output)
    output.seek(0)

    return StreamingResponse(
        output,
        media_type="application/pdf",
        headers={"Content-Disposition": f"attachment; filename=paciente_{paciente_id}.pdf"}
    )

# ===========================================================
# 🏁 INDEX
# ===========================================================

@app.get("/")
def index():
    return {"mensaje": "API HCD con Roles, JWT y FastAPI funcionando correctamente ✔️"}
