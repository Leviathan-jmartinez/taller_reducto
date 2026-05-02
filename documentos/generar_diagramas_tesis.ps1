$ErrorActionPreference = "Stop"

$out = Join-Path $PSScriptRoot "DiagramasTesis.mdj"

$script:seq = 0
function New-Id {
    $script:seq++
    return ("ID" + $script:seq.ToString("000000000000"))
}

function Ref($id) {
    return [ordered]@{ '$ref' = $id }
}

function Obj($type, $name, $parent) {
    $id = New-Id
    $o = [ordered]@{
        _type = $type
        _id = $id
    }
    if ($parent) { $o._parent = Ref $parent }
    if ($null -ne $name) { $o.name = $name }
    return $o
}

function Add-Owned($parent, $child) {
    if (-not $parent.Contains("ownedElements")) { $parent.ownedElements = @() }
    $parent.ownedElements += $child
}

function Add-NoteView($diagram, [string]$text, [int]$left = 80, [int]$top = 80, [int]$width = 760, [int]$height = 420) {
    if (-not $diagram.Contains("ownedViews")) { $diagram.ownedViews = @() }
    $note = [ordered]@{
        _type = "UMLNoteView"
        _id = New-Id
        _parent = Ref $diagram._id
        font = "Arial;13;0"
        parentStyle = $false
        left = $left
        top = $top
        width = $width
        height = $height
        text = $text
    }
    $diagram.ownedViews += $note
}

function New-Package($parent, $name) {
    $p = Obj "UMLPackage" $name $parent._id
    Add-Owned $parent $p
    return $p
}

function New-UseCasePackage($parent, $moduleName, $actorName, [string[]]$useCases) {
    $pkg = New-Package $parent $moduleName
    $actor = Obj "UMLActor" $actorName $pkg._id
    Add-Owned $pkg $actor
    foreach ($ucName in $useCases) {
        $uc = Obj "UMLUseCase" $ucName $pkg._id
        Add-Owned $pkg $uc
        $assoc = Obj "UMLAssociation" "" $pkg._id
        $assoc.end1 = [ordered]@{ _type="UMLAssociationEnd"; reference=Ref $actor._id; navigable="unspecified"; aggregation="none"; multiplicity="" }
        $assoc.end2 = [ordered]@{ _type="UMLAssociationEnd"; reference=Ref $uc._id; navigable="unspecified"; aggregation="none"; multiplicity="" }
        Add-Owned $pkg $assoc
    }
    $diag = Obj "UMLUseCaseDiagram" ("CU - " + $moduleName) $pkg._id
    $txt = "Actor: $actorName`n`nCasos de uso:`n- " + ($useCases -join "`n- ")
    Add-NoteView $diag $txt 60 60 700 350
    Add-Owned $pkg $diag
    return $pkg
}

function New-ClassPackage($parent, $moduleName, [hashtable]$classes) {
    $pkg = New-Package $parent $moduleName
    $created = @{}
    foreach ($className in $classes.Keys) {
        $c = Obj "UMLClass" $className $pkg._id
        $c.attributes = @()
        foreach ($attr in $classes[$className]) {
            $a = Obj "UMLAttribute" $attr $c._id
            $c.attributes += $a
        }
        Add-Owned $pkg $c
        $created[$className] = $c
    }
    $diag = Obj "UMLClassDiagram" ("Clases - " + $moduleName) $pkg._id
    $lines = @("Entidades principales:")
    foreach ($className in ($classes.Keys | Sort-Object)) {
        $lines += ""
        $lines += $className
        foreach ($attr in $classes[$className]) { $lines += "  - $attr" }
    }
    Add-NoteView $diag ($lines -join "`n") 60 60 760 520
    Add-Owned $pkg $diag
    return $pkg
}

function New-Sequence($parent, $name, [string[]]$entities, [string[]]$steps) {
    $collab = Obj "UMLCollaboration" $name $parent._id
    Add-Owned $parent $collab

    $inter = Obj "UMLInteraction" $name $collab._id
    $inter.participants = @()
    $inter.messages = @()
    Add-Owned $collab $inter

    $lifelineNames = @("Actor", "Vista", "Conexion") + $entities
    $lifelines = @{}
    foreach ($ln in $lifelineNames) {
        $ll = Obj "UMLLifeline" $ln $inter._id
        $inter.participants += $ll
        $lifelines[$ln] = $ll
    }

    foreach ($s in $steps) {
        $parts = $s.Split("|")
        if ($parts.Count -lt 3) { continue }
        $msg = Obj "UMLMessage" $parts[2] $inter._id
        $msg.source = Ref $lifelines[$parts[0]]._id
        $msg.target = Ref $lifelines[$parts[1]]._id
        $msg.messageSort = "synchCall"
        $inter.messages += $msg
    }

    $diag = Obj "UMLSequenceDiagram" $name $inter._id
    $diag.defaultDiagram = $true
    $seqLines = @("Convencion: Actor -> Vista -> Conexion -> Entidades/Tablas", "", "Participantes:", "- Actor", "- Vista", "- Conexion")
    foreach ($e in $entities) { $seqLines += "- $e" }
    $seqLines += ""
    $seqLines += "Mensajes:"
    $n = 1
    foreach ($s in $steps) {
        $parts = $s.Split("|")
        if ($parts.Count -ge 3) {
            $seqLines += ("{0}. {1} -> {2}: {3}" -f $n, $parts[0], $parts[1], $parts[2])
            $n++
        }
    }
    Add-NoteView $diag ($seqLines -join "`n") 50 50 900 620
    Add-Owned $inter $diag
    return $collab
}

function New-Activity($parent, $name, [string[]]$steps) {
    $act = Obj "UMLActivity" $name $parent._id
    $act.nodes = @()
    foreach ($s in $steps) {
        $n = Obj "UMLAction" $s $act._id
        $act.nodes += $n
    }
    Add-Owned $parent $act
    $diag = Obj "UMLActivityDiagram" ("Actividad - " + $name) $act._id
    $txt = "Actividad: $name`n`nPasos:`n- " + ($steps -join "`n- ")
    Add-NoteView $diag $txt 60 60 760 420
    Add-Owned $act $diag
}

function New-DeploymentDiagram($parent) {
    $pkg = New-Package $parent "05 Diagrama de Despliegue"

    $nodes = @(
        "Cliente Compras",
        "Cliente Servicios / Taller",
        "Cliente Administracion / Reportes",
        "Router / Firewall",
        "Switch administrable LAN",
        "Servidor Web / Aplicacion",
        "Servidor Base de Datos",
        "Servidor Backup / NAS",
        "UPS",
        "Impresora Compras",
        "Impresora Servicios",
        "Impresora Administracion"
    )

    foreach ($nodeName in $nodes) {
        $node = Obj "UMLNode" $nodeName $pkg._id
        Add-Owned $pkg $node
    }

    $artifacts = @(
        "Sistema Taller Reducto - PHP MVC",
        "Apache / PHP",
        "Base de datos taller_reducto - MySQL MariaDB",
        "Archivos generados - PDF reportes facturas",
        "Uploads y comprobantes",
        "Backups automaticos"
    )

    foreach ($artifactName in $artifacts) {
        $artifact = Obj "UMLArtifact" $artifactName $pkg._id
        Add-Owned $pkg $artifact
    }

    $diag = Obj "UMLDeploymentDiagram" "Despliegue - Infraestructura propuesta" $pkg._id

    Add-NoteView $diag "Cliente Compras`n- Navegador web`n- Acceso por HTTPS`n- Gestiona pedidos, presupuestos, ordenes y compras" 60 70 260 130
    Add-NoteView $diag "Cliente Servicios / Taller`n- Navegador web`n- Recepcion, diagnostico, orden de trabajo y registro de servicio" 60 260 260 130
    Add-NoteView $diag "Cliente Administracion / Reportes`n- Navegador web`n- Usuarios, referenciales, reportes y auditoria" 60 450 260 130

    Add-NoteView $diag "Router / Firewall`n- NAT y reglas de acceso`n- VPN para acceso remoto seguro`n- Bloquea MySQL desde Internet" 390 90 240 140
    Add-NoteView $diag "Switch administrable LAN`n- Red cableada interna`n- Segmentacion por areas si aplica`n- WiFi invitados separado" 390 300 240 130
    Add-NoteView $diag "UPS`n- Energia protegida para servidores, switch y router`n- Permite apagado ordenado" 390 500 240 100

    Add-NoteView $diag "Servidor Web / Aplicacion`n- Apache + PHP`n- Proyecto MVC taller_reducto`n- Sesiones y permisos por rol/sucursal`n- Generacion de PDFs/reportes`n- Puertos: 443 HTTPS / 80 interno" 700 70 310 180
    Add-NoteView $diag "Servidor Base de Datos`n- MySQL / MariaDB`n- Tablas del sistema`n- Acceso solo desde red privada/app`n- Puerto 3306 restringido" 1070 90 290 150
    Add-NoteView $diag "Servidor Backup / NAS`n- Respaldo diario de BD`n- Respaldo semanal completo`n- Copia de uploads y comprobantes`n- Retencion y restauracion probada" 1070 300 290 170

    Add-NoteView $diag "Impresoras de red`n- Compras: pedidos/ordenes`n- Servicios: ordenes de trabajo`n- Administracion: reportes/comprobantes" 700 330 310 130
    Add-NoteView $diag "Almacenamiento`n- /uploads para adjuntos`n- /reportes o salida PDF`n- Backups fuera del servidor principal" 700 520 310 120

    $topology = @(
        "Topologia y comunicaciones",
        "",
        "Clientes -> Router/Firewall -> Switch LAN -> Servidor Web/App",
        "Servidor Web/App -> Servidor Base de Datos: consultas, inserts y updates",
        "Servidor Web/App -> Almacenamiento: PDFs, comprobantes y uploads",
        "Servidor Web/App -> Impresoras de red: impresion de documentos",
        "Servidor Base de Datos -> Backup/NAS: dump automatico diario",
        "UPS -> Router, Switch, Servidor Web/App, Servidor BD y Backup/NAS",
        "",
        "Criterio de buena infraestructura:",
        "- App y BD separados para seguridad, mantenimiento y rendimiento",
        "- MySQL/MariaDB no se expone a Internet",
        "- Acceso remoto solo por VPN",
        "- HTTPS para usuarios internos o externos",
        "- Respaldos automaticos con prueba de restauracion",
        "- Permisos del sistema por rol y sucursal"
    )
    Add-NoteView $diag ($topology -join "`n") 60 640 1300 290

    Add-Owned $pkg $diag
    return $pkg
}

$project = Obj "Project" "DiagramasTesis - Taller Reducto" $null
$project.ownedElements = @()
$model = Obj "UMLModel" "Modelo de Analisis del Sistema" $project._id
$model.ownedElements = @()
Add-Owned $project $model

$pkgUC = New-Package $model "01 Casos de Uso"
$pkgClases = New-Package $model "02 Diagramas de Clases - Entidades"
$pkgSeq = New-Package $model "03 Diagramas de Secuencia"
$pkgAct = New-Package $model "04 Diagramas de Actividad"
New-DeploymentDiagram $model | Out-Null

$modules = @(
    @{
        Name="Seguridad y Acceso"; Actor="Administrador / Usuario";
        UseCases=@("Iniciar sesion","Cerrar sesion","Gestionar usuarios","Asignar rol a usuario","Asignar sucursal a usuario","Gestionar roles","Asignar permisos");
        Classes=@{
            usuarios=@("id_usuario","usu_nick","usu_clave","usu_estado","usu_nombre","usu_apellido","sucursalid");
            roles=@("id_rol","nombre","descripcion","estado");
            permisos=@("id_permiso","codigo","descripcion");
            usuario_rol=@("id_usuario","id_rol");
            rol_permiso=@("id_rol","id_permiso");
            sucursales=@("id_sucursal","suc_descri","estado");
        };
        Sequences=@(
            @{Name="Iniciar sesion"; Entities=@("usuarios","usuario_rol","rol_permiso","permisos","empresa"); Steps=@(
                "Actor|Vista|ingresa usuario y clave",
                "Vista|Vista|valida campos obligatorios",
                "Vista|Conexion|solicita autenticacion",
                "Conexion|usuarios|consulta usuario activo",
                "usuarios|Conexion|retorna usuario",
                "Conexion|usuario_rol|consulta roles asignados",
                "Conexion|rol_permiso|consulta permisos del rol",
                "Conexion|permisos|obtiene permisos",
                "Conexion|empresa|consulta datos de empresa",
                "Conexion|Vista|retorna sesion y permisos",
                "Vista|Actor|muestra menu principal"
            )}
        )
    },
    @{
        Name="Referenciales"; Actor="Administrador";
        UseCases=@("Gestionar empresa","Gestionar sucursales","Gestionar cargos","Gestionar empleados","Gestionar clientes","Gestionar vehiculos","Gestionar proveedores","Gestionar articulos","Gestionar descuentos","Gestionar promociones","Gestionar equipos de trabajo");
        Classes=@{
            empresa=@("id_empresa","razon_social","ruc","direccion","telefono_empresa");
            sucursales=@("id_sucursal","id_empresa","suc_descri","estado");
            cargos=@("idcargos","descripcion","estado");
            empleados=@("idempleados","idcargos","id_sucursal","nro_cedula","estado");
            clientes=@("id_cliente","id_ciudad","nombre","apellido","doc_number","estado");
            vehiculos=@("id_vehiculo","id_cliente","id_modeloauto","chapa","estado");
            proveedores=@("idproveedores","id_ciudad","razon_social","ruc","estado");
            articulos=@("id_articulo","codigo","desc_articulo","idiva","id_categoria","id_marcas","estado");
            articulo_proveedor=@("id_articulo","idproveedores","precio_compra","activo");
            descuentos=@("id_descuento","descripcion","porcentaje","estado");
            promociones=@("id_promocion","descripcion","fecha_desde","fecha_hasta","estado");
            equipo_trabajo=@("id_equipo","id_sucursal","descripcion","estado");
            equipo_empleado=@("id_equipo","idempleados","estado");
        };
        Sequences=@(
            @{Name="Agregar referencial"; Entities=@("tabla_referencial"); Steps=@(
                "Actor|Vista|ingresa datos del formulario",
                "Vista|Vista|valida campos obligatorios y duplicados",
                "Vista|Conexion|llama conexion para registrar",
                "Conexion|tabla_referencial|inserta datos",
                "tabla_referencial|Conexion|confirma registro",
                "Conexion|Vista|retorna resultado",
                "Vista|Actor|muestra mensaje de registro"
            )},
            @{Name="Modificar referencial"; Entities=@("tabla_referencial"); Steps=@(
                "Actor|Vista|selecciona registro",
                "Vista|Conexion|consulta datos",
                "Conexion|tabla_referencial|busca registro",
                "tabla_referencial|Conexion|retorna datos",
                "Conexion|Vista|carga formulario",
                "Actor|Vista|modifica datos",
                "Vista|Vista|valida cambios",
                "Vista|Conexion|llama conexion para actualizar",
                "Conexion|tabla_referencial|actualiza datos",
                "Conexion|Vista|retorna resultado"
            )},
            @{Name="Eliminar o anular referencial"; Entities=@("tabla_referencial","tabla_relacionada"); Steps=@(
                "Actor|Vista|presiona eliminar",
                "Vista|Vista|solicita confirmacion",
                "Actor|Vista|confirma operacion",
                "Vista|Conexion|verifica uso del registro",
                "Conexion|tabla_relacionada|consulta dependencias",
                "tabla_relacionada|Conexion|retorna dependencias",
                "Conexion|tabla_referencial|actualiza estado o elimina",
                "Conexion|Vista|retorna resultado",
                "Vista|Actor|muestra mensaje"
            )}
        )
    },
    @{
        Name="Compras"; Actor="Personal de Compras";
        UseCases=@("Registrar pedido","Anular pedido","Registrar presupuesto de compra","Anular presupuesto de compra","Generar orden de compra","Anular orden de compra","Registrar compra","Anular compra","Registrar nota de remision","Anular nota de remision","Registrar nota de credito/debito","Anular nota de credito/debito");
        Classes=@{
            pedido_cabecera=@("idpedido_cabecera","id_usuario","id_sucursal","fecha","estado");
            pedido_detalle=@("idpedido_cabecera","id_articulo","cantidad");
            presupuesto_compra=@("idpresupuesto_compra","idPedido","idproveedores","id_sucursal","fecha","fecha_venc","estado","total");
            presupuesto_detalle=@("idpresupuesto_compra","id_articulo","cantidad","precio","subtotal");
            orden_compra=@("idorden_compra","presupuestoid","idproveedores","id_sucursal","id_usuario","fecha","estado","fecha_entrega");
            orden_compra_detalle=@("idorden_compra","id_articulo","cantidad","precio_unitario","cantidad_pendiente");
            compra_cabecera=@("idcompra_cabecera","idproveedores","id_usuario","id_sucursal","nro_factura","nro_timbrado","estado","total_compra");
            compra_detalle=@("idcompra_cabecera","id_articulo","precio_unitario","cantidad_recibida","subtotal","iva");
            cuentas_a_pagar=@("idcuenta","idcompra_cabecera","monto","saldo","estado");
            libro_compra=@("idlibro","idcompra_cabecera","tipo_comprobante","nro_comprobante","total","estado");
            nota_remision=@("idnota_remision","idcompra_cabecera","nro_remision","estado","tipo");
            nota_remision_detalle=@("idnota_remision","id_articulo","cantidad");
            nota_compra=@("idnota_compra","idproveedor","tipo","movimiento_stock","nro_documento","estado");
            nota_compra_detalle=@("idnota_compra","id_articulo","cantidad","precio","subtotal");
        };
        Sequences=@(
            @{Name="Registrar pedido"; Entities=@("articulos","pedido_cabecera","pedido_detalle"); Steps=@(
                "Actor|Vista|ingresa a Nuevo Pedido",
                "Actor|Vista|busca articulo",
                "Vista|Conexion|consulta articulos activos",
                "Conexion|articulos|busca por codigo o descripcion",
                "articulos|Conexion|retorna articulos",
                "Conexion|Vista|muestra articulos",
                "Actor|Vista|ingresa cantidad y agrega",
                "Vista|Vista|valida cantidad y duplicado",
                "Actor|Vista|presiona guardar",
                "Vista|Vista|valida articulos cargados",
                "Vista|Conexion|llama conexion para registrar",
                "Conexion|pedido_cabecera|inserta cabecera",
                "Conexion|pedido_detalle|inserta detalle",
                "Conexion|Vista|retorna registro exitoso"
            )},
            @{Name="Anular pedido"; Entities=@("pedido_cabecera"); Steps=@(
                "Actor|Vista|selecciona pedido",
                "Vista|Vista|valida estado pendiente y permiso",
                "Actor|Vista|confirma anulacion",
                "Vista|Conexion|llama conexion",
                "Conexion|pedido_cabecera|actualiza estado anulado",
                "Conexion|Vista|retorna anulacion exitosa"
            )},
            @{Name="Registrar presupuesto de compra"; Entities=@("proveedores","pedido_cabecera","pedido_detalle","articulos","presupuesto_compra","presupuesto_detalle"); Steps=@(
                "Actor|Vista|selecciona proveedor",
                "Vista|Conexion|consulta proveedores",
                "Conexion|proveedores|busca proveedor",
                "Conexion|Vista|retorna proveedor",
                "Actor|Vista|carga pedido pendiente o articulos",
                "Vista|Conexion|consulta pedido pendiente",
                "Conexion|pedido_cabecera|consulta cabecera",
                "Conexion|pedido_detalle|consulta detalle",
                "Conexion|articulos|consulta articulos",
                "Conexion|Vista|retorna detalle",
                "Vista|Vista|calcula subtotales y total",
                "Actor|Vista|presiona guardar",
                "Vista|Vista|valida proveedor, vencimiento y articulos",
                "Vista|Conexion|llama conexion",
                "Conexion|presupuesto_compra|inserta cabecera",
                "Conexion|presupuesto_detalle|inserta detalle",
                "Conexion|pedido_cabecera|actualiza pedido procesado",
                "Conexion|Vista|retorna registro exitoso"
            )},
            @{Name="Generar orden de compra"; Entities=@("presupuesto_compra","presupuesto_detalle","orden_compra","orden_compra_detalle","articulo_proveedor"); Steps=@(
                "Actor|Vista|selecciona presupuesto",
                "Vista|Conexion|consulta presupuesto activo",
                "Conexion|presupuesto_compra|consulta cabecera",
                "Conexion|presupuesto_detalle|consulta detalle",
                "Conexion|Vista|retorna presupuesto",
                "Actor|Vista|define fecha de entrega",
                "Vista|Vista|valida datos cargados",
                "Actor|Vista|confirma generacion",
                "Vista|Conexion|llama conexion",
                "Conexion|orden_compra|inserta orden",
                "Conexion|orden_compra_detalle|inserta detalle",
                "Conexion|articulo_proveedor|registra precio proveedor",
                "Conexion|presupuesto_compra|actualiza estado procesado",
                "Conexion|Vista|retorna OC generada"
            )},
            @{Name="Registrar compra"; Entities=@("orden_compra","orden_compra_detalle","compra_cabecera","compra_detalle","stock","movimientostock","cuentas_a_pagar","libro_compra"); Steps=@(
                "Actor|Vista|selecciona orden de compra",
                "Vista|Conexion|consulta OC activa",
                "Conexion|orden_compra|consulta cabecera",
                "Conexion|orden_compra_detalle|consulta detalle",
                "Conexion|Vista|retorna OC y detalle",
                "Actor|Vista|carga factura, timbrado, condicion y cuotas",
                "Vista|Vista|valida detalle, factura no duplicada y totales",
                "Actor|Vista|confirma registro",
                "Vista|Conexion|llama conexion",
                "Conexion|compra_cabecera|inserta compra",
                "Conexion|compra_detalle|inserta detalle",
                "Conexion|stock|actualiza stock",
                "Conexion|movimientostock|registra recepcion",
                "Conexion|cuentas_a_pagar|genera cuotas",
                "Conexion|libro_compra|registra libro",
                "Conexion|orden_compra|actualiza estado",
                "Conexion|Vista|retorna compra registrada"
            )},
            @{Name="Anular compra"; Entities=@("compra_cabecera","compra_detalle","stock","movimientostock","cuentas_a_pagar","libro_compra"); Steps=@(
                "Actor|Vista|selecciona compra",
                "Vista|Vista|valida existencia, sucursal, estado y permiso",
                "Actor|Vista|confirma anulacion",
                "Vista|Conexion|llama conexion",
                "Conexion|compra_cabecera|actualiza estado anulado",
                "Conexion|compra_detalle|consulta items",
                "Conexion|stock|descuenta stock",
                "Conexion|movimientostock|registra anulacion",
                "Conexion|cuentas_a_pagar|anula cuentas",
                "Conexion|libro_compra|anula libro",
                "Conexion|Vista|retorna anulacion exitosa"
            )}
        )
    },
    @{
        Name="Stock e Inventario"; Actor="Encargado de Deposito";
        UseCases=@("Generar inventario","Guardar ajuste","Aplicar ajuste de stock","Anular ajuste","Generar transferencia","Recibir transferencia","Consultar movimiento de stock");
        Classes=@{
            stock=@("id_sucursal","id_articulo","stockDisponible","stockUltActualizacion");
            movimientostock=@("MovStockId","id_sucursal","MovStockArticuloId","MovStockCantidad","MovStockTipo","MovStockReferencia");
            ajuste_inventario=@("idajuste_inventario","sucursal_id","id_usuario","tipo_inv","estado","fecha");
            ajuste_inventario_detalle=@("idajuste_inventario","id_articulo","cantidad_teorica","cantidad_fisica","diferencia");
            transferencia_stock=@("idtransferencia","sucursal_origen","sucursal_destino","estado","fecha");
            transferencia_stock_detalle=@("idtransferencia","id_articulo","cantidad_enviada","cantidad_recibida");
            nota_remision=@("idnota_remision","idtransferencia","nro_remision","estado");
        };
        Sequences=@(
            @{Name="Generar y aplicar inventario"; Entities=@("articulos","stock","ajuste_inventario","ajuste_inventario_detalle","movimientostock"); Steps=@(
                "Actor|Vista|selecciona tipo de inventario",
                "Vista|Conexion|consulta articulos y stock",
                "Conexion|articulos|consulta articulos activos",
                "Conexion|stock|consulta stock teorico",
                "Conexion|Vista|retorna detalle",
                "Actor|Vista|carga cantidades fisicas",
                "Vista|Vista|calcula diferencias",
                "Actor|Vista|guarda ajuste",
                "Vista|Conexion|llama conexion",
                "Conexion|ajuste_inventario|inserta cabecera",
                "Conexion|ajuste_inventario_detalle|inserta detalle",
                "Actor|Vista|aplica ajuste",
                "Vista|Vista|valida estado modificado",
                "Vista|Conexion|llama conexion",
                "Conexion|stock|actualiza stock por diferencias",
                "Conexion|movimientostock|registra ajuste",
                "Conexion|ajuste_inventario|actualiza estado ajustado",
                "Conexion|Vista|retorna ajuste aplicado"
            )},
            @{Name="Transferir stock"; Entities=@("sucursales","articulos","stock","transferencia_stock","transferencia_stock_detalle","movimientostock","nota_remision","nota_remision_detalle"); Steps=@(
                "Actor|Vista|selecciona sucursal destino",
                "Vista|Conexion|consulta sucursales activas",
                "Conexion|sucursales|retorna sucursales",
                "Actor|Vista|busca productos",
                "Vista|Conexion|consulta productos con stock",
                "Conexion|stock|consulta stock disponible",
                "Conexion|articulos|consulta articulos",
                "Conexion|Vista|retorna productos",
                "Actor|Vista|ingresa cantidades",
                "Vista|Vista|valida cantidad y stock",
                "Actor|Vista|confirma transferencia",
                "Vista|Conexion|llama conexion",
                "Conexion|transferencia_stock|inserta cabecera en transito",
                "Conexion|transferencia_stock_detalle|inserta detalle",
                "Conexion|stock|descuenta origen",
                "Conexion|movimientostock|registra salida",
                "Conexion|nota_remision|genera remision",
                "Conexion|nota_remision_detalle|inserta detalle remision",
                "Conexion|Vista|retorna transferencia generada"
            )},
            @{Name="Recibir transferencia"; Entities=@("transferencia_stock","transferencia_stock_detalle","stock","movimientostock"); Steps=@(
                "Actor|Vista|abre transferencias por recibir",
                "Vista|Conexion|consulta transferencias en transito",
                "Conexion|transferencia_stock|consulta cabecera",
                "Conexion|transferencia_stock_detalle|consulta detalle",
                "Conexion|Vista|retorna detalle",
                "Actor|Vista|ingresa cantidades recibidas",
                "Vista|Vista|calcula diferencias",
                "Actor|Vista|confirma recepcion",
                "Vista|Conexion|llama conexion",
                "Conexion|transferencia_stock_detalle|actualiza cantidad recibida",
                "Conexion|stock|suma stock destino",
                "Conexion|movimientostock|registra entrada",
                "Conexion|transferencia_stock|actualiza estado recibido o parcial",
                "Conexion|Vista|retorna recepcion confirmada"
            )}
        )
    },
    @{
        Name="Servicios de Taller"; Actor="Recepcionista / Tecnico";
        UseCases=@("Recepcionar servicio","Registrar diagnostico","Anular diagnostico","Generar presupuesto de servicio","Anular presupuesto de servicio","Generar orden de trabajo","Asignar equipo tecnico","Registrar servicio realizado","Finalizar servicio","Registrar reclamo de servicio","Anular reclamo");
        Classes=@{
            recepcion_servicio=@("idrecepcion","id_cliente","id_vehiculo","id_sucursal","tipo_servicio","estado","origen");
            diagnostico_servicio=@("id_diagnostico","idrecepcion","id_usuario","estado","fecha");
            diagnostico_detalle=@("id_diagnostico","descripcion","tipo","estado");
            presupuesto_servicio=@("idpresupuesto_servicio","id_diagnostico","id_usuario","estado","total");
            presupuesto_detalleservicio=@("idpresupuesto_servicio","id_articulo","cantidad","precio","subtotal");
            orden_trabajo=@("idorden_trabajo","idpresupuesto_servicio","id_equipo","estado","fecha");
            orden_trabajo_detalle=@("idorden_trabajo","id_articulo","cantidad","precio","estado");
            registro_servicio=@("idregistro_servicio","idorden_trabajo","id_usuario","estado","fecha");
            registro_servicio_detalle=@("idregistro_servicio","id_articulo","cantidad","estado");
            reclamo_servicio=@("idreclamo_servicio","idregistro_servicio","estado","motivo");
        };
        Sequences=@(
            @{Name="Recepcionar servicio"; Entities=@("clientes","vehiculos","recepcion_servicio"); Steps=@(
                "Actor|Vista|busca cliente",
                "Vista|Conexion|consulta clientes",
                "Conexion|clientes|busca cliente",
                "Conexion|Vista|retorna cliente",
                "Actor|Vista|selecciona vehiculo",
                "Vista|Conexion|consulta vehiculos",
                "Conexion|vehiculos|busca vehiculos del cliente",
                "Conexion|Vista|retorna vehiculo",
                "Actor|Vista|carga datos de recepcion",
                "Vista|Vista|valida datos obligatorios",
                "Vista|Conexion|llama conexion",
                "Conexion|recepcion_servicio|inserta recepcion",
                "Conexion|Vista|retorna recepcion registrada"
            )},
            @{Name="Registrar diagnostico"; Entities=@("recepcion_servicio","diagnostico_servicio","diagnostico_detalle"); Steps=@(
                "Actor|Vista|busca recepcion pendiente",
                "Vista|Conexion|consulta recepciones",
                "Conexion|recepcion_servicio|retorna recepciones",
                "Actor|Vista|carga diagnostico",
                "Vista|Vista|valida detalle",
                "Vista|Conexion|llama conexion",
                "Conexion|diagnostico_servicio|inserta diagnostico",
                "Conexion|diagnostico_detalle|inserta detalle",
                "Conexion|recepcion_servicio|actualiza estado",
                "Conexion|Vista|retorna diagnostico registrado"
            )},
            @{Name="Generar presupuesto de servicio"; Entities=@("diagnostico_servicio","articulos","presupuesto_servicio","presupuesto_detalleservicio"); Steps=@(
                "Actor|Vista|selecciona diagnostico",
                "Vista|Conexion|consulta diagnostico",
                "Conexion|diagnostico_servicio|retorna diagnostico",
                "Actor|Vista|agrega repuestos/servicios",
                "Vista|Conexion|consulta articulos",
                "Conexion|articulos|retorna articulos",
                "Vista|Vista|calcula subtotales y total",
                "Vista|Conexion|llama conexion",
                "Conexion|presupuesto_servicio|inserta presupuesto",
                "Conexion|presupuesto_detalleservicio|inserta detalle",
                "Conexion|diagnostico_servicio|actualiza estado",
                "Conexion|Vista|retorna presupuesto generado"
            )},
            @{Name="Generar orden de trabajo"; Entities=@("presupuesto_servicio","presupuesto_detalleservicio","equipo_trabajo","orden_trabajo","orden_trabajo_detalle"); Steps=@(
                "Actor|Vista|selecciona presupuesto aprobado",
                "Vista|Conexion|consulta presupuesto",
                "Conexion|presupuesto_servicio|retorna presupuesto",
                "Conexion|presupuesto_detalleservicio|retorna detalle",
                "Actor|Vista|selecciona equipo tecnico",
                "Vista|Conexion|consulta equipos",
                "Conexion|equipo_trabajo|retorna equipos",
                "Vista|Vista|valida datos",
                "Vista|Conexion|llama conexion",
                "Conexion|orden_trabajo|inserta orden",
                "Conexion|orden_trabajo_detalle|inserta detalle",
                "Conexion|presupuesto_servicio|actualiza estado",
                "Conexion|Vista|retorna orden generada"
            )},
            @{Name="Registrar servicio realizado"; Entities=@("orden_trabajo","orden_trabajo_detalle","registro_servicio","registro_servicio_detalle","stock","movimientostock"); Steps=@(
                "Actor|Vista|selecciona orden de trabajo",
                "Vista|Conexion|consulta orden",
                "Conexion|orden_trabajo|retorna orden",
                "Conexion|orden_trabajo_detalle|retorna detalle",
                "Actor|Vista|carga trabajo realizado",
                "Vista|Vista|valida detalle y stock",
                "Vista|Conexion|llama conexion",
                "Conexion|registro_servicio|inserta registro",
                "Conexion|registro_servicio_detalle|inserta detalle",
                "Conexion|stock|descuenta repuestos",
                "Conexion|movimientostock|registra consumo",
                "Conexion|orden_trabajo|actualiza estado",
                "Conexion|Vista|retorna servicio registrado"
            )},
            @{Name="Registrar reclamo de servicio"; Entities=@("registro_servicio","reclamo_servicio","recepcion_servicio"); Steps=@(
                "Actor|Vista|selecciona servicio finalizado",
                "Vista|Conexion|consulta registro_servicio",
                "Conexion|registro_servicio|retorna servicio",
                "Actor|Vista|carga motivo de reclamo",
                "Vista|Vista|valida datos",
                "Vista|Conexion|llama conexion",
                "Conexion|reclamo_servicio|inserta reclamo",
                "Conexion|registro_servicio|actualiza estado",
                "Conexion|recepcion_servicio|genera recepcion por reclamo",
                "Conexion|Vista|retorna reclamo registrado"
            )}
        )
    },
    @{
        Name="Reportes"; Actor="Usuario autorizado";
        UseCases=@("Reporte de articulos","Reporte de stock","Reporte de movimiento de stock","Reporte de proveedores","Reporte de clientes","Reporte de empleados","Reporte de pedidos","Reporte de presupuestos","Reporte de ordenes de compra","Reporte de compras","Reporte de libro de compras","Reporte de transferencias","Reporte de recepcion servicio","Reporte de presupuesto servicio","Reporte de orden trabajo","Reporte de registro servicio");
        Classes=@{
            reportes=@("filtros","fecha_desde","fecha_hasta","sucursal","estado");
            articulos=@("id_articulo","codigo","descripcion");
            stock=@("id_sucursal","id_articulo","stockDisponible");
            compra_cabecera=@("idcompra_cabecera","nro_factura","total_compra");
            libro_compra=@("nro_comprobante","total","estado");
            transferencia_stock=@("idtransferencia","estado","fecha");
            recepcion_servicio=@("idrecepcion","estado","fecha_ingreso");
        };
        Sequences=@(
            @{Name="Generar reporte"; Entities=@("tabla_origen","usuarios","sucursales"); Steps=@(
                "Actor|Vista|selecciona tipo de reporte y filtros",
                "Vista|Vista|valida filtros",
                "Vista|Conexion|llama conexion",
                "Conexion|tabla_origen|consulta datos filtrados",
                "Conexion|usuarios|consulta usuarios relacionados",
                "Conexion|sucursales|consulta sucursales relacionadas",
                "Conexion|Vista|retorna registros",
                "Vista|Actor|muestra reporte o genera PDF"
            )}
        )
    }
)

foreach ($m in $modules) {
    New-UseCasePackage $pkgUC $m.Name $m.Actor $m.UseCases | Out-Null
    New-ClassPackage $pkgClases $m.Name $m.Classes | Out-Null
    $seqPkg = New-Package $pkgSeq $m.Name
    foreach ($s in $m.Sequences) {
        New-Sequence $seqPkg $s.Name $s.Entities $s.Steps | Out-Null
    }
}

New-Activity $pkgAct "Proceso general de compras" @(
    "Registrar pedido",
    "Generar presupuesto de compra",
    "Generar orden de compra",
    "Registrar compra",
    "Actualizar stock",
    "Generar cuentas a pagar",
    "Registrar libro de compras"
)

New-Activity $pkgAct "Proceso general de servicio de taller" @(
    "Recepcionar vehiculo",
    "Registrar diagnostico",
    "Generar presupuesto de servicio",
    "Generar orden de trabajo",
    "Registrar servicio realizado",
    "Finalizar servicio",
    "Atender reclamo si corresponde"
)

New-Activity $pkgAct "Proceso de stock" @(
    "Registrar compra o transferencia",
    "Registrar movimiento de stock",
    "Actualizar stock disponible",
    "Generar inventario",
    "Aplicar ajuste",
    "Emitir reportes"
)

$json = $project | ConvertTo-Json -Depth 100
$utf8NoBom = New-Object System.Text.UTF8Encoding($false)
[System.IO.File]::WriteAllText($out, $json, $utf8NoBom)

Write-Output "Generado: $out"
