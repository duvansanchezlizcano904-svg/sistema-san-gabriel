<?php
/**
 * Controlador de Proveedores
 * CRUD completo: listar, crear, editar, eliminar.
 */

require_once __DIR__ . '/../config/helpers.php';
require_once __DIR__ . '/../models/Proveedor.php';

class ProveedorController {
    private Proveedor $modelo;

    public function __construct() {
        require_admin();
        $this->modelo = new Proveedor();
    }

    /* -------------------------------------------------------
       INDEX — Listado con búsqueda y filtro de estado
    ------------------------------------------------------- */
    public function index(): void {
        $busqueda = sanitize($_GET['busqueda'] ?? '');
        $estado   = sanitize($_GET['estado']   ?? '');

        $proveedores = $this->modelo->listarTodos($busqueda, $estado);
        $stats       = $this->modelo->obtenerEstadisticas();

        $tituloPagina = 'Proveedores - San Gabriel';
        $menuActivo   = 'proveedores';

        require_once __DIR__ . '/../views/proveedores/index.php';
    }

    /* -------------------------------------------------------
       CREAR — GET muestra form / POST procesa
    ------------------------------------------------------- */
    public function crear(): void {
        $errores = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = [
                'nombre'    => sanitize($_POST['nombre']    ?? ''),
                'contacto'  => sanitize($_POST['contacto']  ?? ''),
                'telefono'  => sanitize($_POST['telefono']  ?? ''),
                'email'     => sanitize($_POST['email']     ?? ''),
                'direccion' => sanitize($_POST['direccion'] ?? ''),
                'nit'       => sanitize($_POST['nit']       ?? ''),
                'notas'     => sanitize($_POST['notas']     ?? ''),
                'estado'    => sanitize($_POST['estado']    ?? 'activo'),
            ];

            // Validaciones
            if (empty($datos['nombre'])) {
                $errores[] = 'El nombre del proveedor es obligatorio.';
            }

            if (!empty($datos['email']) && !filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
                $errores[] = 'El correo electrónico no tiene un formato válido.';
            }

            if (!empty($datos['nit']) && $this->modelo->existeNit($datos['nit'])) {
                $errores[] = 'Ya existe un proveedor registrado con ese NIT.';
            }

            if (empty($errores)) {
                $this->modelo->crear($datos);
                set_flash('success', "Proveedor <strong>{$datos['nombre']}</strong> registrado exitosamente.");
                redirect('index.php?c=proveedores');
            }
        } else {
            $datos = [];
        }

        $tituloPagina = 'Nuevo Proveedor - San Gabriel';
        $menuActivo   = 'proveedores';

        require_once __DIR__ . '/../views/proveedores/crear.php';
    }

    /* -------------------------------------------------------
       EDITAR — GET carga datos / POST guarda cambios
    ------------------------------------------------------- */
    public function editar(): void {
        $id = (int)($_GET['id'] ?? 0);
        $proveedor = $this->modelo->obtenerPorId($id);

        if (!$proveedor) {
            set_flash('danger', 'Proveedor no encontrado.');
            redirect('index.php?c=proveedores');
        }

        $errores = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = [
                'nombre'    => sanitize($_POST['nombre']    ?? ''),
                'contacto'  => sanitize($_POST['contacto']  ?? ''),
                'telefono'  => sanitize($_POST['telefono']  ?? ''),
                'email'     => sanitize($_POST['email']     ?? ''),
                'direccion' => sanitize($_POST['direccion'] ?? ''),
                'nit'       => sanitize($_POST['nit']       ?? ''),
                'notas'     => sanitize($_POST['notas']     ?? ''),
                'estado'    => sanitize($_POST['estado']    ?? 'activo'),
            ];

            if (empty($datos['nombre'])) {
                $errores[] = 'El nombre del proveedor es obligatorio.';
            }

            if (!empty($datos['email']) && !filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
                $errores[] = 'El correo electrónico no tiene un formato válido.';
            }

            if (!empty($datos['nit']) && $this->modelo->existeNit($datos['nit'], $id)) {
                $errores[] = 'Ya existe otro proveedor registrado con ese NIT.';
            }

            if (empty($errores)) {
                $this->modelo->actualizar($id, $datos);
                set_flash('success', "Proveedor <strong>{$datos['nombre']}</strong> actualizado correctamente.");
                redirect('index.php?c=proveedores');
            }

            // Si hay errores, mezclar datos POSTeados para repoblar el form
            $proveedor = array_merge($proveedor, $datos);
        }

        $tituloPagina = 'Editar Proveedor - San Gabriel';
        $menuActivo   = 'proveedores';

        require_once __DIR__ . '/../views/proveedores/editar.php';
    }

    /* -------------------------------------------------------
       ELIMINAR — Solo POST por seguridad
    ------------------------------------------------------- */
    public function eliminar(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?c=proveedores');
        }

        $id = (int)($_POST['id'] ?? 0);
        $proveedor = $this->modelo->obtenerPorId($id);

        if (!$proveedor) {
            set_flash('danger', 'Proveedor no encontrado.');
            redirect('index.php?c=proveedores');
        }

        $this->modelo->eliminar($id);
        set_flash('success', "Proveedor <strong>{$proveedor['nombre']}</strong> eliminado correctamente.");
        redirect('index.php?c=proveedores');
    }
}
