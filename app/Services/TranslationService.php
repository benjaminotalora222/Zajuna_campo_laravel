<?php

namespace App\Services;

class TranslationService
{
    /**
     * Diccionario maestro de traducción Español -> Inglés.
     */
    public static function getDictionary(): array
    {
        return [
            // ── Menú Lateral y Roles ──
            'PANEL SUPERADMINISTRADOR' => 'SUPERADMIN PANEL',
            'PANEL OPERATIVO'          => 'OPERATIVE PANEL',
            'PANEL PROVEEDOR'          => 'SUPPLIER PANEL',
            'Panel general'            => 'Dashboard',
            'MINIMARKET'               => 'MINIMARKET',
            'Proveedores'              => 'Suppliers',
            'Productos'                => 'Products',
            'Categorías'               => 'Categories',
            'Inventario'               => 'Inventory',
            'Ventas'                   => 'Sales',
            'Órdenes de pedido'        => 'Purchase Orders',
            'TRANSFERENCIAS Y SHOWS'   => 'TRANSFERS & SHOWS',
            'Cronograma de espacios'   => 'Space Schedule',
            'Ejecución de actividades' => 'Activity Execution',
            'INVESTIGACIÓN'            => 'RESEARCH',
            'Proyectos'                => 'Projects',
            'Tareas y avances'         => 'Tasks & Progress',
            'AUDITORÍA'                => 'AUDIT',
            'Movimientos'              => 'Movements',
            'Reportes'                 => 'Reports',
            'Configuración'            => 'Settings',
            'Superadministrador'       => 'Superadministrator',
            'Operativo'                => 'Operative',
            'Proveedor'                => 'Supplier',
            'Cerrar sesión'            => 'Log out',
            'Colapsar menú'            => 'Collapse menu',
            'Expandir menú'            => 'Expand menu',

            // ── Módulo de Configuración ──
            'Configuración del Sistema' => 'System Settings',
            'Administra las preferencias y ajustes generales de la plataforma.' => 'Manage general platform preferences and settings.',
            'Ajustes Generales' => 'General Settings',
            'Configura las preferencias básicas del sistema.' => 'Configure basic system preferences.',
            'Nombre de la Plataforma' => 'Platform Name',
            'Este nombre se mostrará en todo el sistema.' => 'This name will be displayed throughout the system.',
            'Zona Horaria' => 'Timezone',
            'Selecciona la zona horaria predeterminada para la plataforma.' => 'Select the default timezone for the platform.',
            'Idioma' => 'Language',
            'Selecciona el idioma predeterminado para la plataforma.' => 'Select the default language for the platform.',
            'Guardar Cambios' => 'Save Changes',
            'Guardar cambios' => 'Save changes',
            'Configuración guardada correctamente.' => 'Settings saved successfully.',
            'General' => 'General',
            'Notificaciones' => 'Notifications',
            'Seguridad' => 'Security',
            'Módulos y Permisos' => 'Modules & Permissions',
            'Módulos y permisos' => 'Modules & permissions',
            'Control de Módulos y Accesos' => 'Module & Access Control',
            'Gestiona las funciones y módulos que pueden ver y operar los diferentes roles del sistema.' => 'Manage features and modules that different system roles can view and operate.',
            'Gestiona las preferencias de notificaciones del sistema.' => 'Manage system notification preferences.',
            'Configura las opciones de seguridad de la plataforma.' => 'Configure platform security options.',
            'Notificaciones por correo' => 'Email notifications',
            'Notificaciones por Correo' => 'Email Notifications',
            'Recibe notificaciones importantes por correo electrónico.' => 'Receive important notifications by email.',
            'Recordatorios de tareas' => 'Task reminders',
            'Recordatorios Automáticos' => 'Automatic Reminders',
            'Recibe recordatorios sobre tareas y actividades pendientes.' => 'Receive reminders about pending tasks and activities.',
            'Alertas críticas' => 'Critical alerts',
            'Alertas Críticas' => 'Critical Alerts',
            'Recibe alertas inmediatas sobre situaciones críticas.' => 'Receive immediate alerts about critical situations.',
            'Autenticación en dos pasos' => 'Two-factor authentication',
            'Autenticación de Dos Pasos' => 'Two-Factor Authentication',
            'Añade una capa adicional de seguridad a tu cuenta.' => 'Add an extra layer of security to your account.',
            'Sesión automática' => 'Automatic session',
            'Mantener la sesión iniciada en este dispositivo.' => 'Keep session logged in on this device.',
            'Módulo de Ventas para Rol Operativo' => 'Sales Module for Operative Role',
            'Rol Operativo' => 'Operative Role',
            'Permite o restringe completamente el acceso al módulo de Ventas (pedidos, clientes, cotizaciones y catálogo de ventas) para los usuarios con rol Operativo.' => 'Completely allows or restricts access to the Sales module for users with the Operative role.',
            'Efecto inmediato:' => 'Immediate effect:',
            'Al desactivarlo, el enlace desaparece del menú del Operativo, se ocultan los accesos directos de ventas y cualquier intento de ingreso por URL responderá con' => 'When disabled, the link disappears from the Operative menu, shortcuts are hidden, and any URL access will return',
            'Error 403 (Acceso Denegado)' => 'Error 403 (Access Denied)',
            'Ventas Operativo:' => 'Operative Sales:',
            'Permitido' => 'Allowed',
            'Bloqueado' => 'Blocked',
            'Permisos Individuales por Usuario Operativo' => 'Individual Permissions per Operative User',
            'Selecciona qué usuarios operativos tienen habilitado el módulo de ventas y quiénes lo tienen bloqueado.' => 'Select which operative users have the sales module enabled and who has it blocked.',
            'Acceso Permitido' => 'Access Allowed',
            'Acceso Bloqueado' => 'Access Blocked',
            'Permitir Ventas' => 'Allow Sales',
            'No hay usuarios registrados con el rol Operativo en el sistema.' => 'No users registered with the Operative role in the system.',

            // ── Dashboard General ──
            'Panel de Control' => 'Dashboard',
            'Bienvenido' => 'Welcome',
            'Hoy es' => 'Today is',
            'alerta sin leer' => 'unread alert',
            'alertas sin leer' => 'unread alerts',
            'Ventas del mes' => 'Monthly sales',
            'Ventas del Mes' => 'Monthly Sales',
            'Ventas de hoy' => 'Today\'s sales',
            'Últimas ventas' => 'Recent sales',
            'Activos registrados' => 'Registered active',
            'acumulado' => 'accumulated',
            'Sin ventas este mes' => 'No sales this month',
            'En investigación' => 'Under research',
            'Usuarios' => 'Users',
            'Alertas del Sistema' => 'System Alerts',
            'Sin alertas pendientes' => 'No pending alerts',
            'Ver todos' => 'View all',
            'Ver detalles' => 'View details',
            'Accesos Rápidos' => 'Quick Access',
            'Accesos rápidos' => 'Quick access',

            // ── Calendario y Cronograma ──
            'Calendario' => 'Calendar',
            'Organiza y visualiza tus actividades y eventos.' => 'Organize and view your activities and events.',
            'Hoy' => 'Today',
            'Informe PDF' => 'PDF Report',
            'Informe Excel' => 'Excel Report',
            'Nuevo evento' => 'New event',
            'Nuevo Evento' => 'New Event',
            'Editar Evento' => 'Edit Event',
            'Crear evento' => 'Create event',
            'Todos los eventos' => 'All events',
            'Visita Técnica' => 'Technical Visit',
            'Visita técnica' => 'Technical visit',
            'Capacitación' => 'Training',
            'Reunión' => 'Meeting',
            'Show / Evento' => 'Show / Event',
            'Transferencia' => 'Transfer',
            'Otro' => 'Other',
            'Título del evento' => 'Event title',
            'Tipo de evento' => 'Event type',
            'Espacio o lugar' => 'Space or location',
            'Fecha' => 'Date',
            'Hora inicio' => 'Start time',
            'Hora fin' => 'End time',
            'Descripción' => 'Description',
            'El título y la fecha son obligatorios.' => 'Title and date are required.',
            '¿Eliminar este evento?' => 'Delete this event?',
            'DOM' => 'SUN',
            'LUN' => 'MON',
            'MAR' => 'TUE',
            'MIÉ' => 'WED',
            'JUE' => 'THU',
            'VIE' => 'FRI',
            'SÁB' => 'SAT',
            'Enero' => 'January',
            'Febrero' => 'February',
            'Marzo' => 'March',
            'Abril' => 'April',
            'Mayo' => 'May',
            'Junio' => 'June',
            'Julio' => 'July',
            'Agosto' => 'August',
            'Septiembre' => 'September',
            'Octubre' => 'October',
            'Noviembre' => 'November',
            'Diciembre' => 'December',

            // ── Minimarket, Inventario, Productos y Lotes ──
            'Catálogo de Productos' => 'Product Catalog',
            'Catálogo de productos' => 'Product Catalog',
            'Nuevo Producto' => 'New Product',
            'Nuevo producto' => 'New Product',
            'Editar Producto' => 'Edit Product',
            'Buscar producto...' => 'Search product...',
            'Buscar producto por nombre o código...' => 'Search product by name or barcode...',
            'Código de barras' => 'Barcode',
            'Código de Barras' => 'Barcode',
            'Generar código' => 'Generate code',
            'Generar Código' => 'Generate Code',
            'Imprimir código' => 'Print code',
            'Precio de Venta' => 'Sale Price',
            'Precio de compra' => 'Purchase price',
            'Precio de Compra' => 'Purchase Price',
            'Precio' => 'Price',
            'Stock total' => 'Total stock',
            'Stock Total' => 'Total Stock',
            'Stock actual' => 'Current stock',
            'Stock Actual' => 'Current Stock',
            'Stock mínimo' => 'Minimum stock',
            'Stock Mínimo' => 'Minimum Stock',
            'Stock bajo' => 'Low stock',
            'Stock Bajo' => 'Low Stock',
            'Agotado' => 'Out of Stock',
            'Próximo a vencer' => 'Expiring soon',
            'Próximo a Vencer' => 'Expiring Soon',
            'Vencido' => 'Expired',
            'Disponible' => 'Available',
            'En stock' => 'In stock',
            'Registrar Entrada' => 'Register Inflow',
            'Registrar Salida' => 'Register Outflow',
            'Dar de baja' => 'Decommission / Write-off',
            'Dar de Baja' => 'Decommission / Write-off',
            'Lotes de este producto' => 'Batches of this product',
            'Historial de movimientos' => 'Movement history',
            'Historial de Movimientos' => 'Movement History',
            'Fecha de entrada' => 'Entry date',
            'Fecha de vencimiento' => 'Expiration date',
            'Cantidad inicial' => 'Initial quantity',
            'Cantidad disponible' => 'Available quantity',
            'Movimiento de inventario' => 'Inventory movement',
            'Salida rápida' => 'Quick outflow',
            'Entrada de inventario' => 'Inventory Inflow',
            'Salida de inventario' => 'Inventory Outflow',

            // ── Ventas y Facturación POS ──
            'Módulo de Ventas' => 'Sales Module',
            'Punto de Venta' => 'Point of Sale',
            'Registrar Venta' => 'Register Sale',
            'Nueva Venta' => 'New Sale',
            'Nueva venta' => 'New sale',
            'Ventas Realizadas' => 'Sales History',
            'Factura POS' => 'POS Receipt',
            'Imprimir Ticket' => 'Print Ticket',
            'Descargar PDF' => 'Download PDF',
            'Ticket de venta térmica' => 'Thermal sale receipt (80mm)',
            'Total a pagar' => 'Total to pay',
            'Total a Pagar' => 'Total to Pay',
            'Método de pago' => 'Payment method',
            'Método de Pago' => 'Payment Method',
            'Efectivo' => 'Cash',
            'Transferencia bancaria' => 'Bank transfer',
            'Transferencia' => 'Transfer',
            'Venta registrada correctamente' => 'Sale registered successfully',
            'Cliente' => 'Customer',
            'Vendedor' => 'Seller',
            'Subtotal' => 'Subtotal',
            'Descuento' => 'Discount',
            'Total' => 'Total',
            'Carrito de venta' => 'Sale cart',
            'Vaciar carrito' => 'Empty cart',
            'Agregar producto' => 'Add product',
            'Cobrar' => 'Charge / Checkout',

            // ── Proveedores ──
            'Gestión de Proveedores' => 'Supplier Management',
            'Nuevo Proveedor' => 'New Supplier',
            'Nuevo proveedor' => 'New supplier',
            'Editar Proveedor' => 'Edit Supplier',
            'Razón Social' => 'Business Name',
            'Teléfono' => 'Phone',
            'Correo electrónico' => 'Email address',
            'Dirección' => 'Address',
            'Activo' => 'Active',
            'Inactivo' => 'Inactive',

            // ── Proyectos, Actividades y Tareas ──
            'Proyectos de Investigación' => 'Research Projects',
            'Proyectos de investigación' => 'Research Projects',
            'Nuevo Proyecto' => 'New Project',
            'Nuevo proyecto' => 'New Project',
            'Editar Proyecto' => 'Edit Project',
            'Líder de Proyecto' => 'Project Lead',
            'Línea de investigación' => 'Research line',
            'Fecha de inicio' => 'Start date',
            'Fecha de cierre' => 'End date',
            'Avance' => 'Progress',
            'Mayor avance' => 'Highest progress',
            'Menor avance' => 'Lowest progress',
            'Más recientes' => 'Most recent',
            'Nombre A-Z' => 'Name A-Z',
            'Aprendices asignados' => 'Assigned apprentices',
            'Registrar aprendiz' => 'Register apprentice',
            'Ejecución de Actividades' => 'Activity Execution',
            'Nueva Actividad' => 'New Activity',
            'Nueva actividad' => 'New Activity',
            'Tareas y avances' => 'Tasks & progress',
            'Tareas y Avances' => 'Tasks & Progress',
            'Nueva Tarea' => 'New Task',
            'Nueva tarea' => 'New Task',
            'Responsable' => 'Assignee',
            'Fecha límite' => 'Deadline',
            'Fecha Límite' => 'Deadline',
            'Pendiente' => 'Pending',
            'En progreso' => 'In progress',
            'En Progreso' => 'In Progress',
            'Completada' => 'Completed',
            'Prioridad' => 'Priority',
            'Alta' => 'High',
            'Media' => 'Medium',
            'Baja' => 'Low',
            'Notificar pendientes' => 'Notify pending',
            'Notificar Pendientes' => 'Notify Pending',

            // ── Auditoría y Reportes ──
            'Auditoría del Sistema' => 'System Audit',
            'Registro de Actividades' => 'Activity Log',
            'Reportes y Estadísticas' => 'Reports & Statistics',
            'Exportar a PDF' => 'Export to PDF',
            'Exportar a Excel' => 'Export to Excel',
            'Módulo' => 'Module',
            'Acción' => 'Action',
            'Usuario' => 'User',
            'Detalle' => 'Detail',
            'Fecha y Hora' => 'Date & Time',
            'Período' => 'Period',
            'Mensual' => 'Monthly',
            'Anual' => 'Annual',
            'Año actual' => 'Current year',
            'Día actual' => 'Current day',

            // ── Botones, Filtros y Textos Comunes ──
            'Buscar...' => 'Search...',
            'Filtrar' => 'Filter',
            'Limpiar' => 'Clear',
            'Guardar' => 'Save',
            'Actualizar' => 'Update',
            'Eliminar' => 'Delete',
            'Cancelar' => 'Cancel',
            'Cerrar' => 'Close',
            'Confirmar' => 'Confirm',
            'Aceptar' => 'Accept',
            'Volver' => 'Back',
            'Acciones' => 'Actions',
            'Estado' => 'Status',
            'Todos' => 'All',
            'Todas' => 'All',
            'Sin registros' => 'No records',
            'Sin datos disponibles' => 'No data available',
            'Mostrando' => 'Showing',
            'de' => 'of',
            'resultados' => 'results',
            'Anterior' => 'Previous',
            'Siguiente' => 'Next',
            'Permisos' => 'Permissions',
            'El nombre no puede comenzar con números y solo debe contener letras.' => 'The name cannot start with numbers and must only contain letters.',
            'El nombre completo no puede comenzar con números y solo debe contener letras.' => 'The full name cannot start with numbers and must only contain letters.',
            'El nombre del cliente no puede comenzar con números y solo debe contener letras.' => 'The customer name cannot start with numbers and must only contain letters.',
            'El nombre del producto no puede comenzar con números y solo debe contener letras.' => 'The product name cannot start with numbers and must only contain letters.',
            'El nombre de la categoría no puede comenzar con números y solo debe contener letras.' => 'The category name cannot start with numbers and must only contain letters.',
            'El nombre del proveedor no puede comenzar con números.' => 'The supplier name cannot start with numbers.',
            'El nombre de contacto no puede comenzar con números y solo debe contener letras.' => 'The contact name cannot start with numbers and must only contain letters.',
            'El nombre del proyecto no puede comenzar con números.' => 'The project name cannot start with numbers.',
            'El tema o nombre de la actividad no puede comenzar con números.' => 'The activity topic or name cannot start with numbers.',
            'El título del evento no puede comenzar con números.' => 'The event title cannot start with numbers.',
            'El nombre de la tarea no puede comenzar con números.' => 'The task name cannot start with numbers.',
            'Este campo no puede comenzar con números.' => 'This field cannot start with numbers.',
        ];
    }

    /**
     * Traduce el HTML completo antes de entregarlo al navegador.
     */
    public static function translateHtml(string $html): string
    {
        $dictionary = static::getDictionary();

        // Ordenar por longitud descendente para traducir primero las frases más largas
        uksort($dictionary, fn($a, $b) => mb_strlen($b) <=> mb_strlen($a));

        // 1. Proteger bloques que NO deben traducirse (scripts, styles, svgs, pre, code)
        $protected = [];
        $html = preg_replace_callback('/<(script|style|svg|pre|code)[\s\S]*?<\/\1>/i', function ($m) use (&$protected) {
            $key = '___PROTECTED_BLOCK_' . count($protected) . '___';
            $protected[$key] = $m[0];
            return $key;
        }, $html);

        // 2. Reemplazar texto dentro de nodos HTML (> ... <)
        $html = preg_replace_callback('/(>)([^<]+)(<)/u', function ($m) use ($dictionary) {
            $prefix  = $m[1];
            $text    = $m[2];
            $suffix  = $m[3];
            $trimmed = trim($text);

            if ($trimmed === '' || is_numeric($trimmed)) {
                return $m[0];
            }

            // Coincidencia exacta con el texto recortado
            if (isset($dictionary[$trimmed])) {
                return $prefix . str_replace($trimmed, $dictionary[$trimmed], $text) . $suffix;
            }

            // Coincidencias de subfrases
            foreach ($dictionary as $es => $en) {
                if (mb_stripos($text, $es) !== false) {
                    $text = str_ireplace($es, $en, $text);
                }
            }

            return $prefix . $text . $suffix;
        }, $html);

        // 3. Reemplazar atributos traducibles (placeholder, title, aria-label)
        $html = preg_replace_callback('/(placeholder|title|aria-label)="([^"]+)"/u', function ($m) use ($dictionary) {
            $attr = $m[1];
            $val  = $m[2];

            if (isset($dictionary[$val])) {
                return $attr . '="' . $dictionary[$val] . '"';
            }

            foreach ($dictionary as $es => $en) {
                if (mb_stripos($val, $es) !== false) {
                    $val = str_ireplace($es, $en, $val);
                }
            }

            return $attr . '="' . $val . '"';
        }, $html);

        // 4. Actualizar tag <html lang="es"> a <html lang="en">
        $html = preg_replace('/<html(\s+[^>]*)?(\s+lang=")[^"]*(")/i', '<html$1$2en$3', $html);

        // 5. Restaurar bloques protegidos
        if (!empty($protected)) {
            $html = strtr($html, $protected);
        }

        return $html;
    }
}
