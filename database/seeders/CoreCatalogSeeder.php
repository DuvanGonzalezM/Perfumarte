<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Datos mínimos sin los cuales la aplicación no funciona.
 *
 * No es catálogo comercial: es la lista de registros cuyos identificadores están
 * escritos a mano en controladores y componentes. Si faltan, el módulo que los
 * referencia falla en silencio o lanza una excepción.
 *
 * Cada bloque documenta el archivo y la línea que obliga a que ese registro
 * exista. Mientras esas referencias sigan quemadas en el código, este seeder es
 * obligatorio en cualquier base nueva.
 *
 * Los valores de negocio (nombres de zonas, dirección de la planta, precios,
 * datos del proveedor) son PLANTILLAS: ajústelos antes del primer despliegue.
 */
class CoreCatalogSeeder extends Seeder
{
    /**
     * Sede que la aplicación trata como planta central y oculta del listado de
     * sedes (LocationsController::getLocations, whereNot('location_id', 1)).
     */
    private const PLANT_LOCATION_ID = 1;

    /** Bodega de materia prima: esencias, dipropileno y disolvente. */
    private const WAREHOUSE_RAW_MATERIAL = 1;

    /** Bodega de producto terminado. */
    private const WAREHOUSE_FINISHED = 2;

    /** Segunda bodega central desde la que también se despacha. */
    private const WAREHOUSE_SUPPLIES = 3;

    public function run(): void
    {
        DB::transaction(function () {
            $this->seedZones();
            $this->seedPlant();
            $this->seedWarehouses();
            $this->seedSupplier();
            $this->seedRequiredProducts();
        });
    }

    /**
     * No existe ninguna ruta para crear zonas: sin este bloque el rol
     * "Auxiliar administrativo" no se puede crear (exige zone_id) y tampoco se
     * puede dar de alta una sede (LocationsController::storeLocation valida
     * zone_id como required).
     */
    private function seedZones(): void
    {
        $zones = [
            1 => 'Zona Centro',
            2 => 'Zona Norte',
            3 => 'Zona Sur',
        ];

        foreach ($zones as $id => $name) {
            DB::table('zones')->updateOrInsert(
                ['zone_id' => $id],
                ['zone_name' => $name, 'updated_at' => now(), 'created_at' => now()]
            );
        }
    }

    private function seedPlant(): void
    {
        DB::table('locations')->updateOrInsert(
            ['location_id' => self::PLANT_LOCATION_ID],
            [
                'name' => 'Planta central',
                'address' => 'Ajustar a la dirección real de la planta',
                'zone_id' => 1,
                'cash_base' => 0,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }

    /**
     * Las tres bodegas están referenciadas por id fijo:
     *
     *  - 1 : LabTransformationController:50-52,146-160 y RepackageController:113
     *  - 2 : LabTransformationController:105,170 · RepackageController:81,123 ·
     *        DispatchController:291
     *  - 2 y 3 : origen de todo despacho, whereIn('warehouse_id', [2, 3]) en
     *        DispatchController:47,78,124,167,194,281 y
     *        returnedDispatchController:51
     *
     * Los precios son los de venta por presentación en la planta; las sedes
     * fijan los suyos al crearse.
     */
    private function seedWarehouses(): void
    {
        $warehouses = [
            self::WAREHOUSE_RAW_MATERIAL => 'Bodega materia prima',
            self::WAREHOUSE_FINISHED => 'Bodega producto terminado',
            self::WAREHOUSE_SUPPLIES => 'Bodega de insumos',
        ];

        foreach ($warehouses as $id => $name) {
            DB::table('warehouses')->updateOrInsert(
                ['warehouse_id' => $id],
                [
                    'location_id' => self::PLANT_LOCATION_ID,
                    'name' => $name,
                    'price5' => 0,
                    'price30' => 0,
                    'price50' => 0,
                    'price100' => 0,
                    'price_drops' => 0,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }

    /**
     * products.supplier_id es NOT NULL con llave foránea: sin al menos un
     * proveedor no se puede crear ningún producto, ni desde el seeder ni desde
     * la interfaz.
     */
    private function seedSupplier(): void
    {
        DB::table('suppliers')->updateOrInsert(
            ['supplier_id' => 1],
            [
                'nit' => '000000000-0',
                'name' => 'Proveedor por definir',
                'country' => 'Colombia',
                'address' => 'Ajustar a los datos reales del proveedor',
                'phone' => '0000000000',
                'email' => 'proveedor@ajustar.com',
                'status' => 1,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }

    /**
     * Productos que la operación necesita reconocer. Lo que importa no es el
     * id sino la columna operational_role: laboratorio y venta los buscan por
     * ahí. Los ids se conservan solo para no romper el inventario histórico de
     * las bases que ya venían con ellos.
     *
     * Quién consume cada rol:
     *   dipropylene / solvent  LabTransformationController y SaleController
     *   gift_bag               SaleController y CreateSale.vue
     *   container_30/50/100    CreateSale.vue, para ofrecer envases por tamaño
     *
     * Si carga el catálogo real de Perfumarte, borre este bloque y marque los
     * roles desde Productos: el código ya no depende de estos números.
     */
    private function seedRequiredProducts(): void
    {
        $products = [
            1 => ['Dipropileno', 'KG', 'MP-DIPRO', 'dipropylene'],
            2 => ['Disolvente', 'KG', 'MP-DISOL', 'solvent'],
            372 => ['Bolsa de regalo', 'UNIDAD', 'INS-BOLSA', 'gift_bag'],

            385 => ['Envase 30 ml A', 'UNIDAD', 'ENV-030-A', 'container_30'],
            386 => ['Envase 30 ml B', 'UNIDAD', 'ENV-030-B', 'container_30'],

            388 => ['Envase 50 ml A', 'UNIDAD', 'ENV-050-A', 'container_50'],
            389 => ['Envase 50 ml B', 'UNIDAD', 'ENV-050-B', 'container_50'],
            390 => ['Envase 50 ml C', 'UNIDAD', 'ENV-050-C', 'container_50'],
            391 => ['Envase 50 ml D', 'UNIDAD', 'ENV-050-D', 'container_50'],

            392 => ['Envase 100 ml A', 'UNIDAD', 'ENV-100-A', 'container_100'],
            393 => ['Envase 100 ml B', 'UNIDAD', 'ENV-100-B', 'container_100'],
            394 => ['Envase 100 ml C', 'UNIDAD', 'ENV-100-C', 'container_100'],
        ];

        foreach ($products as $id => [$reference, $unit, $code, $role]) {
            DB::table('products')->updateOrInsert(
                ['product_id' => $id],
                [
                    'reference' => $reference,
                    'name' => $reference,
                    'commercial_reference' => $reference,
                    'measurement_unit' => $unit,
                    'category' => 'Insumo',
                    'operational_role' => $role,
                    'code' => $code,
                    'supplier_id' => 1,
                    'status' => 1,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
