# Orden de Importación de la Base de Datos — Quesos San Gabriel

## IMPORTANTE: Dos escenarios

### Escenario A: Nueva base de datos (recomendado)

Si vas a crear la base de datos desde cero, solo necesitas estos dos archivos:

---

## Paso 1: Crear la Base de Datos y Estructura Completa

**Archivo**: `database/database.sql`

**Qué hace**:
- Crea la base de datos `gabriel_lacteos`
- Crea las tablas: `usuarios`, `productos`, `ventas`, `detalle_ventas`, `proveedores`, `pedidos`, `detalle_pedidos`
- Ya incluye todas las columnas de categorización: `categoria`, `tipo_leche`, `intensidad_sabor`, `tiempo_maduracion`, `peso_presentacion`, `origen`, `imagen_url`, `imagen`
- Inserta usuarios por defecto (admin/cliente)
- Inserta 10 productos iniciales (LAC-001 a LAC-010) con todas las columnas de categorización ya asignadas

**Cómo ejecutar**:
1. Abrir phpMyAdmin o HeidiSQL
2. Seleccionar servidor MySQL (sin base de datos específica)
3. Importar `database/database.sql`
4. Ejecutar

**Resultado**: Base de datos `gabriel_lacteos` con 10 productos.

---

## Paso 2: Agregar Más Productos (opcional)

**Archivo**: `database/seeds/inserts_nuevos_quesos.sql`

**Qué hace**:
- Inserta 25 productos nuevos organizados en 6 categorías:
  - Quesos Frescos
  - Quesos Madurados
  - Quesos Cremosos
  - Quesos Azules
  - Quesos de Cabra
  - Quesos Artesanales

**Cómo ejecutar**:
1. Seleccionar la base de datos `gabriel_lacteos`
2. Importar `database/seeds/inserts_nuevos_quesos.sql`
3. Ejecutar

**Resultado**: Catálogo con 35 productos.

---

### Escenario B: Base de datos existente (antigua)

Si ya tienes una base de datos con la estructura anterior (sin las columnas de categorización), usa las migraciones:

**Archivos en `database/migrations/**`**:
- `migration_categorias_quesos.sql` — Agrega columnas de categorización
- `migration_imagen_productos.sql` — Agrega columna imagen
- `migration_telefono.sql` — Agrega columna telefono a usuarios
- `migration_recuperar_password.sql` — Agrega columnas de recuperación de contraseña

**Nota**: Cada migración se ejecuta una sola vez. Si la columna ya existe, MySQL lanzara error "Duplicate column name". En ese caso, esa migración ya se ejecutó previamente.

---

## Verificación

Después de ejecutar, verificar que hay productos activos:

```sql
USE gabriel_lacteos;

SELECT COUNT(*) as total_productos 
FROM productos 
WHERE estado = 'activo';
```

**Resultado esperado**: 10 (solo database.sql) o 35 (con inserts).

---

## Resetear la Base de Datos

```sql
DROP DATABASE IF EXISTS gabriel_lacteos;
```

Luego ejecutar los pasos nuevamente.