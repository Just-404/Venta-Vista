<?php

namespace app\controllers;

use app\core\Controller;
use app\models\Configuracion;
use app\models\Administrador;
use app\models\Vendedor;
use app\models\Cliente;
use app\models\Usuario;
use app\models\Direccion;

class ConfiguracionController extends Controller {

    public function index(): void {
        $this->requireAuth();
        $usuario = $this->usuarioActual();
        $rol     = (int) ($usuario['rol'] ?? 0);
        $id      = (int) ($usuario['id']  ?? 0);

        $config  = Configuracion::obtenerTodas();
        $prefs   = Configuracion::obtenerPreferencias($id);

        $perfil = match ($rol) {
            1 => Administrador::obtenerPorUsuario($id),
            2 => Vendedor::obtenerPorUsuario($id),
            3 => Cliente::obtenerPorUsuario($id),
            default => []
        };

        // Solo clientes tienen direcciones
        $direcciones = [];
        if ($rol === 3 && !empty($perfil['idCliente'])) {
            $direcciones = Direccion::obtenerPorCliente((int) $perfil['idCliente']);
        }

        $vista = match ($rol) {
            1       => 'configuracion/admin',
            2       => 'configuracion/vendedor',
            default => 'configuracion/cliente',
        };

        $this->render($vista, [
            'usuario'     => $usuario,
            'perfil'      => $perfil ?: [],
            'config'      => $config,
            'prefs'       => $prefs,
            'direcciones' => $direcciones,
            'flash'       => $this->getFlash(),
        ]);
    }

    // POST /configuracion/fiscal  (admin)
    public function fiscal(): void {
        $this->requireAuth();
        $this->requireRolId(1);

        $ok = Configuracion::guardarMultiple([
            'negocio_nombre'    => $this->post('negocio_nombre'),
            'negocio_rnc'       => $this->post('negocio_rnc'),
            'negocio_telefono'  => $this->post('negocio_telefono'),
            'negocio_direccion' => $this->post('negocio_direccion'),
            'negocio_email'     => $this->post('negocio_email'),
        ]);

        $this->setFlash($ok ? 'success' : 'error',
            $ok ? 'Datos fiscales actualizados.' : 'Error al guardar datos fiscales.');
        $this->redirect('configuracion');
    }

    // POST /configuracion/impuestos  (admin)
    public function impuestos(): void {
        $this->requireAuth();
        $this->requireRolId(1);

        $ok = Configuracion::guardarMultiple([
            'itbis_porcentaje' => $this->post('itbis_porcentaje'),
            'envio_costo_base' => $this->post('envio_costo_base'),
        ]);

        $this->setFlash($ok ? 'success' : 'error',
            $ok ? 'Configuración de impuestos actualizada.' : 'Error al guardar.');
        $this->redirect('configuracion');
    }

    // POST /configuracion/perfil  (todos los roles)
    public function perfil(): void {
        $this->requireAuth();
        $usuario = $this->usuarioActual();
        $rol     = (int) ($usuario['rol'] ?? 0);
        $id      = (int) ($usuario['id']  ?? 0);

        $datos = [
            'nombre'    => $this->post('nombre'),
            'apellidos' => $this->post('apellidos'),
            'cedula'    => $this->post('cedula'),
            'telefono'  => $this->post('telefono'),
        ];

        $ok = match ($rol) {
            1 => $this->actualizarPerfilAdmin($id, $datos),
            2 => $this->actualizarPerfilVendedor($id, $datos),
            3 => $this->actualizarPerfilCliente($id, $datos),
            default => false,
        };

        $email = $this->post('email');
        if ($email) Usuario::actualizarEmail($id, $email);

        $this->setFlash($ok ? 'success' : 'error',
            $ok ? 'Perfil actualizado correctamente.' : 'Error al actualizar el perfil.');
        $this->redirect('configuracion');
    }

    // POST /configuracion/password
    public function password(): void {
        $this->requireAuth();
        $usuario  = $this->usuarioActual();
        $id       = (int) ($usuario['id'] ?? 0);
        $actual   = $this->post('password_actual');
        $nueva    = $this->post('password_nueva');
        $confirma = $this->post('password_confirma');

        $user = Usuario::obtenerPorId($id);
        if (!$user || !password_verify($actual, $user['contrasena'])) {
            $this->setFlash('error', 'La contraseña actual no es correcta.');
            $this->redirect('configuracion');
            return;
        }
        if ($nueva !== $confirma) {
            $this->setFlash('error', 'Las contraseñas nuevas no coinciden.');
            $this->redirect('configuracion');
            return;
        }
        if (strlen($nueva) < 6) {
            $this->setFlash('error', 'La contraseña debe tener al menos 6 caracteres.');
            $this->redirect('configuracion');
            return;
        }

        $ok = Usuario::actualizarContrasena($id, password_hash($nueva, PASSWORD_DEFAULT));
        $this->setFlash($ok ? 'success' : 'error',
            $ok ? 'Contraseña actualizada correctamente.' : 'Error al actualizar la contraseña.');
        $this->redirect('configuracion');
    }

    // POST /configuracion/preferencias
    public function preferencias(): void {
        $this->requireAuth();
        $id = (int) ($this->usuarioActual()['id'] ?? 0);

        $ok = Configuracion::guardarPreferencias($id, [
            'confirmar_pedido'    => $this->post('confirmar_pedido',    0),
            'alerta_stock'        => $this->post('alerta_stock',        0),
            'factura_automatica'  => $this->post('factura_automatica',  0),
            'notif_estado_pedido' => $this->post('notif_estado_pedido', 0),
            'registro_publico'    => $this->post('registro_publico',    0),
        ]);

        $this->setFlash($ok ? 'success' : 'error',
            $ok ? 'Preferencias guardadas.' : 'Error al guardar preferencias.');
        $this->redirect('configuracion');
    }

    // POST /configuracion/direccion/agregar  (clientes)
    public function agregarDireccion(): void {
        $this->requireAuth();
        $this->requireRolId(3);
        $usuario = $this->usuarioActual();
        $id      = (int) ($usuario['id'] ?? 0);

        $perfil = Cliente::obtenerPorUsuario($id);
        if (!$perfil) {
            $this->setFlash('error', 'Perfil de cliente no encontrado.');
            $this->redirect('configuracion');
            return;
        }

        $ok = Direccion::crear([
            'calle'        => $this->post('calle'),
            'ciudad'       => $this->post('ciudad'),
            'provincia'    => $this->post('provincia'),
            'codigoPostal' => $this->post('codigoPostal'),
            'esPrincipal'  => (int) ($this->post('esPrincipal') === '1'),
            'idCliente'    => (int) $perfil['idCliente'],
        ]);

        $this->setFlash($ok ? 'success' : 'error',
            $ok ? 'Dirección agregada correctamente.' : 'Error al agregar la dirección.');
        $this->redirect('configuracion');
    }

    // POST /configuracion/direccion/eliminar  (clientes)
    public function eliminarDireccion(): void {
        $this->requireAuth();
        $this->requireRolId(3);

        $ok = Direccion::eliminar((int) $this->post('idDireccion'));
        $this->setFlash($ok ? 'success' : 'error',
            $ok ? 'Dirección eliminada.' : 'Error al eliminar la dirección.');
        $this->redirect('configuracion');
    }

    // POST /configuracion/direccion/principal  (clientes)
    public function setPrincipal(): void {
        $this->requireAuth();
        $this->requireRolId(3);
        $usuario = $this->usuarioActual();
        $id      = (int) ($usuario['id'] ?? 0);

        $perfil = Cliente::obtenerPorUsuario($id);
        if (!$perfil) {
            $this->redirect('configuracion');
            return;
        }

        $ok = Direccion::establecerPrincipal(
            (int) $this->post('idDireccion'),
            (int) $perfil['idCliente']
        );

        $this->setFlash($ok ? 'success' : 'error',
            $ok ? 'Dirección principal actualizada.' : 'Error al actualizar.');
        $this->redirect('configuracion');
    }

    // ── Helpers privados ─────────────────────────────────────────────────────

    private function requireRolId(int $idRol): void {
        $usuario = $this->usuarioActual();
        if (!$usuario || (int) ($usuario['rol'] ?? 0) !== $idRol) {
            $this->redirect('dashboard');
        }
    }

    private function actualizarPerfilAdmin(int $idUsuario, array $datos): bool {
        $admin = Administrador::obtenerPorUsuario($idUsuario);
        if (!$admin) return false;
        return Administrador::actualizar(array_merge($datos, ['idAdmin' => $admin['idAdmin']]));
    }

    private function actualizarPerfilVendedor(int $idUsuario, array $datos): bool {
        $vendedor = Vendedor::obtenerPorUsuario($idUsuario);
        if (!$vendedor) return false;
        return Vendedor::actualizar(array_merge($datos, ['idVendedor' => $vendedor['idVendedor']]));
    }

    private function actualizarPerfilCliente(int $idUsuario, array $datos): bool {
        $cliente = Cliente::obtenerPorUsuario($idUsuario);
        if (!$cliente) return false;
        return Cliente::actualizar(array_merge($datos, ['idCliente' => $cliente['idCliente']]));
    }
}
