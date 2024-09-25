<?php

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\EscposImage;
use Illuminate\Http\Request;

class PrintController extends Controller
{
    public function printReceipt(Request $request)
    {
        // Recibe el objeto Order desde la request
        $order = $request->input('order'); 

        try {
            // Conectar a la impresora en red
            $connector = new NetworkPrintConnector("192.168.1.100", 9100); // IP de la impresora
            $printer = new Printer($connector);

            // Imprimir el logo (asegúrate de que el archivo está en la ruta correcta)
            $logo = EscposImage::load(public_path('logos/logo.png'), false); 
            $printer->setJustification(Printer::JUSTIFY_CENTER); // Centrar
            $printer->bitImage($logo); // Imprimir imagen

            // Imprimir cabecera del ticket
            $printer->setTextSize(2, 2); // Texto grande
            $printer->text("Pedido N° " . $order['id'] . "\n");
            $printer->setTextSize(1, 1); // Texto normal
            $printer->text("Fecha: " . date('Y-m-d H:i:s') . "\n");
            $printer->text("Método de pago: " . $order['paymentMethod'] . "\n");
            $printer->text("Opción de consumo: " . $order['consumptionOption'] . "\n\n");

            // Imprimir los artículos del pedido
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("Artículos:\n");
            foreach ($order['items'] as $item) {
                $this->printOrderItem($printer, $item);
            }

            // Imprimir el total
            $printer->text("\n---------------------------\n");
            $printer->setTextSize(2, 2); // Texto grande
            $printer->text("Total: " . $order['total'] . " €\n");
            $printer->setTextSize(1, 1); // Texto normal

            // Finalizar el ticket
            $printer->text("\nGracias por su compra\n");
            $printer->cut();

            // Cerrar la conexión con la impresora
            $printer->close();
        } catch (Exception $e) {
            // Manejo de errores
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    // Función para imprimir un artículo (producto o menú)
    private function printOrderItem(Printer $printer, $item)
    {
        $printer->text($item['details']['name'] . " x" . $item['quantity'] . " - " . $item['details']['price'] . "€\n");

        // Imprimir personalizaciones si las hay
        if (!empty($item['details']['customizations'])) {
            foreach ($item['details']['customizations'] as $customization) {
                $this->printCustomizations($printer, $customization);
            }
        }

        // Si es un menú, imprimir los productos del menú
        if ($item['type'] == 'menu' && !empty($item['details']['products'])) {
            foreach ($item['details']['products'] as $menuProduct) {
                $printer->text("\tProducto del menú: " . $menuProduct['name'] . " - " . $menuProduct['price'] . "€\n");

                // Imprimir personalizaciones de productos en el menú
                if (!empty($menuProduct['customizations'])) {
                    foreach ($menuProduct['customizations'] as $customization) {
                        $this->printCustomizations($printer, $customization);
                    }
                }
            }
        }
    }

    // Función para imprimir personalizaciones
    private function printCustomizations(Printer $printer, $customization)
    {
        // Imprimir el nombre de la pregunta de personalización
        $printer->text("\t" . $customization['name'] . ":\n");

        // Imprimir las respuestas seleccionadas
        foreach ($customization['responses'] as $response) {
            $printer->text("\t\t- " . $response['value'] . "\n");
        }
    }

    // public function printReceipt(Request $request)
    // {
    //     // Recibe el objeto Order desde la request
    //     $order = $request->input('order'); 

    //     // Crear un nuevo documento Excel
    //     $spreadsheet = new Spreadsheet();
    //     $sheet = $spreadsheet->getActiveSheet();

    //     try {
    //         // Cargar el logo
    //         $logoPath = public_path('logos/logo.png');
    //         $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
    //         $drawing->setName('Logo');
    //         $drawing->setDescription('Logo');
    //         $drawing->setPath($logoPath);
    //         $drawing->setHeight(50);
    //         $drawing->setCoordinates('A1');
    //         $drawing->setWorksheet($sheet);

    //         // Imprimir cabecera del ticket
    //         $sheet->setCellValue('A3', "Pedido N° " . $order['id']);
    //         $sheet->setCellValue('A4', "Fecha: " . date('Y-m-d H:i:s'));
    //         $sheet->setCellValue('A5', "Método de pago: " . $order['paymentMethod']);
    //         $sheet->setCellValue('A6', "Opción de consumo: " . $order['consumptionOption']);
    //         $sheet->setCellValue('A8', "Artículos:");

    //         // Imprimir los artículos del pedido
    //         $row = 9; // Comenzar en la fila 9
    //         foreach ($order['items'] as $item) {
    //             $this->addOrderItemToSheet($sheet, $item, $row);
    //         }

    //         // Imprimir el total
    //         $row++; // Mover a la siguiente fila
    //         $sheet->setCellValue("A$row", "---------------------------");
    //         $row++;
    //         $sheet->setCellValue("A$row", "Total: " . $order['total'] . " €");

    //         // Guardar el archivo Excel
    //         $writer = new Xlsx($spreadsheet);
    //         $filePath = public_path('tickets/ticket_' . $order['id'] . '.xlsx');
    //         $writer->save($filePath);

    //         return response()->json(['success' => 'Ticket generado correctamente', 'file' => $filePath]);
    //     } catch (Exception $e) {
    //         // Manejo de errores
    //         return response()->json(['error' => $e->getMessage()]);
    //     }
    // }

    // // Función para añadir un artículo (producto o menú) al documento Excel
    // private function addOrderItemToSheet($sheet, $item, &$row)
    // {
    //     $sheet->setCellValue("A$row", $item['details']['name'] . " x" . $item['quantity'] . " - " . $item['details']['price'] . "€");
    //     $row++;

    //     // Imprimir personalizaciones si las hay
    //     if (!empty($item['details']['customizations'])) {
    //         foreach ($item['details']['customizations'] as $customization) {
    //             $this->addCustomizationsToSheet($sheet, $customization, $row);
    //         }
    //     }

    //     // Si es un menú, imprimir los productos del menú
    //     if ($item['type'] == 'menu' && !empty($item['details']['products'])) {
    //         foreach ($item['details']['products'] as $menuProduct) {
    //             $sheet->setCellValue("A$row", "\tProducto del menú: " . $menuProduct['name'] . " - " . $menuProduct['price'] . "€");
    //             $row++;

    //             // Imprimir personalizaciones de productos en el menú
    //             if (!empty($menuProduct['customizations'])) {
    //                 foreach ($menuProduct['customizations'] as $customization) {
    //                     $this->addCustomizationsToSheet($sheet, $customization, $row);
    //                 }
    //             }
    //         }
    //     }
    // }

    // // Función para añadir personalizaciones al documento Excel
    // private function addCustomizationsToSheet($sheet, $customization, &$row)
    // {
    //     // Imprimir el nombre de la pregunta de personalización
    //     $sheet->setCellValue("A$row", "\t" . $customization['name'] . ":");
    //     $row++;

    //     // Imprimir las respuestas seleccionadas
    //     foreach ($customization['responses'] as $response) {
    //         $sheet->setCellValue("A$row", "\t\t- " . $response['value']);
    //         $row++;
    //     }
    // }
}
