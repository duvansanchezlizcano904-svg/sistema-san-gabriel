# Módulo Proveedores

Este módulo gestiona el catálogo de empresas y productores que suministran leche y otros insumos.

## ¿Quién lo usa?

- **Administradores** exclusivamente.

## Cómo se entra

| Acción | URL | Método | Requiere sesión |
|--------|-----|--------|-----------------|
| Listar proveedores | `index.php?c=proveedores` | GET | Sí (admin) |
| Formulario crear | `index.php?c=proveedores&a=crear` | GET | Sí (admin) |
| Guardar proveedor | `index.php?c=proveedores&a=guardar` | POST |Sí (admin) |
| Formulario editar | `index.php?c=proveedores&a=editar&id=XXX` | GET | Sí (admin) |
| Actualizar proveedor | `index.php?c=proveedores&a=actualizar&id=XXX` | POST | Sí (admin) |
| Cambiar estado | `index.php?c=proveedores&a=cambiarEstado&id=XXX` | GET | Sí (admin) |
| Eliminar proveedor | `index.php?c=proveedores&a=eliminar&id=XXX` | GET | Sí (admin) |

## Archivos que lo componen

- **Controlador**: `controllers/ProveedorController.php`
- **Modelo**: `models/Proveedor.php`
- **Vistas**:
  - `views/proveedores/index.php`
  - `views/proveedores/crear.php`
  - `views/proveedores/editar.php`

## Métodos del controlador

### index()
Lista proveedores con filtros por búsqueda (nombre, contacto, teléfono, email, NIT) y estado.

### crear() / guardar()
Muestra y procesa el formulario de nuevo proveedor. Valida que el NIT sea único.

### editar() / actualizar()
Carga y actualiza datos de un proveedor existente.

### cambiarEstado()
Alterna entre activo e inactivo.

### eliminar()
Borra el registro físicamente.

## Validaciones del servidor

- NIT único en la base de datos.
- Todos los campos obligatorios.

## Cómo se protege

- `require_admin()` al inicio de cada método.

## Tablas de la base de datos

### proveedores
| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | INT (PK) | Identificador |
| nombre | VARCHAR(100) | Nombre de la empresa |
| contacto | VARCHAR(100) | Nombre del contacto |
| telefono | VARCHAR(30) | Teléfono |
| email | VARCHAR(100) | Correo electrónico |
| direccion | VARCHAR(255) | Dirección |
| nit | VARCHAR(20) | Número de identificación fiscal |
| notas | TEXT | Notas adicionales |
| estado | ENUM | activo, inactivo |

## Cómo probarlo

1. Iniciar sesión como admin.
2. Ir a `index.php?c=proveedores`.
3. Crear un nuevo proveedor, editarlo, cambiar su estado.