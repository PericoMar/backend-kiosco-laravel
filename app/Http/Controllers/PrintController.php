<?php

namespace App\Http\Controllers;

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\EscposImage;
use Illuminate\Http\Request;
require 'fpdf/fpdf.php';

// use PhpOffice\PhpSpreadsheet\Spreadsheet;
// use Dompdf\Dompdf;
// use Dompdf\Options;
// use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Writer\Html;

class PrintController extends Controller
{

    public function printPDF(Request $request)
    {
        $id = $request->input('id');
        $paymentMethod = $request->input('paymentMethod');
        $consumptionOption = $request->input('consumptionOption');
        $total = $request->input('total');
        $items = $request->input('items');

        // Medir el tiempo de inicio de la generación del PDF
        $startTime = microtime(true);

        // Recoger la ruta del archivo PDF
        $filePath = $this->generarPDF($id, $paymentMethod, $consumptionOption, $total, $items);

        return response()->json(['success' => true, 'pdf_path' => $filePath]);

        // Calcular el tiempo que tardó en generar el PDF
        $pdfGenerationTime = microtime(true) - $startTime;

        // Definir la ruta para el archivo de salida (imagen)
        $imagePath = storage_path('app/public/tickets/ticket.jpg');

        // Comprobar si el archivo PDF existe
        if (!file_exists($filePath)) {
            return response()->json(['success' => false, 'message' => 'El archivo PDF no existe en la ruta especificada.']);
        }

        // Medir el tiempo de inicio de la conversión a imagen
        $startTime = microtime(true);

        // Convertir el PDF a imagen usando Imagick
        try {
            $imagick = new \Imagick();
            $imagick->setResolution(300, 300); // Establecer resolución para 300 DPI
            $imagick->readImage($filePath);

            // Establecer un fondo blanco (elimina la transparencia)
            $imagick->setImageBackgroundColor(new \ImagickPixel('white'));
            $imagick->setImageAlphaChannel(\Imagick::ALPHACHANNEL_REMOVE); // Eliminar canal alfa

            // Ajustar el tamaño de la imagen al tamaño del ticket
            $imagick->resizeImage(550, 1800, \Imagick::FILTER_LANCZOS, 1);

            // Guardar la imagen convertida
            $imagick->writeImage($imagePath);

            // Limpiar los recursos
            $imagick->clear();
            $imagick->destroy();
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al convertir el PDF a imagen: ' . $e->getMessage()]);
        }

        // Calcular el tiempo que tardó en convertir el PDF a imagen
        $imageConversionTime = microtime(true) - $startTime;

        // Comprobar si el archivo de imagen se generó correctamente
        if (!file_exists($imagePath)) {
            return response()->json(['success' => false, 'message' => 'Error al generar la imagen.']);
        }

        // Medir el tiempo de inicio de la impresión
        $startTime = microtime(true);

        // Conectar a la impresora POS-80C
        $connector = new WindowsPrintConnector("POS-80C");

        // Crear un objeto Printer
        $printer = new Printer($connector);

        try {
            // Cargar la imagen generada y enviarla a la impresora
            $image = EscposImage::load($imagePath, false);

            // Imprimir la imagen
            $printer->bitImage($image);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al imprimir la imagen: ' . $e->getMessage()]);
        }

        // Finalizar impresión
        $printer->cut();
        $printer->close();

        // Calcular el tiempo que tardó en imprimir la imagen
        $printTime = microtime(true) - $startTime;

        // Enviar los tiempos de cada proceso
        return response()->json([
            'success' => true,
            'pdf_generation_time' => $pdfGenerationTime,
            'image_conversion_time' => $imageConversionTime,
            'print_time' => $printTime
        ]);
    }

    

    public function generarPDF($id, $paymentMethod, $consumptionOption, $total, $items)
    {
        // Crear nuevo documento PDF
        $pdf = new \FPDF('P', 'mm', array(80, 200));
        $pdf->AddPage();
        $pdf->SetMargins(1, 1, 1);
        $pdf->SetFont('Arial', 'B', 9);

        $logoPath = public_path('storage/logos/logo_18.png');
        if (file_exists($logoPath)) {
            // Obtener las dimensiones de la imagen
            list($logoWidth, $logoHeight) = getimagesize($logoPath);

            // Definir el ancho máximo permitido para el logo
            $maxLogoWidth = 35; // 35 mm de ancho máximo
            $maxLogoHeight = 20; // Puedes establecer un máximo de altura si lo necesitas

            // Calcular el ratio de la imagen para mantener la proporción
            $ratio = min($maxLogoWidth / $logoWidth, $maxLogoHeight / $logoHeight);

            // Redimensionar la imagen manteniendo la proporción
            $newWidth = $logoWidth * $ratio;
            $newHeight = $logoHeight * $ratio;

            // Ajustar la posición después del logo
            $pdf->Image($logoPath, 17.5, 5, $newWidth); // Cambiar el tamaño (ancho redimensionado)

            // Ajustar la posición del texto después del logo
            $pdf->SetY(5 + $newHeight + 5); // Mover la posición para que el texto quede justo debajo de la imagen
        }

        $pdf->Ln(7);
        $pdf->MultiCell(70, 5, 'KIOSCO KONG CONSULTING', 0, 'C');
        $pdf->Ln(1);

        // Número de ticket
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(17, 5, mb_convert_encoding('Núm ticket: ', 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(53, 5, $id, 0, 1, 'L');

        $pdf->Cell(70, 2, '-------------------------------------------------------------------------', 0, 1, 'L');

        // Encabezado de la tabla de productos
        $pdf->Cell(10, 4, 'Cant.', 0, 0, 'L');
        $pdf->Cell(30, 4, mb_convert_encoding('Descripción', 'ISO-8859-1', 'UTF-8'), 0, 0, 'L');
        $pdf->Cell(15, 4, 'Precio', 0, 0, 'C');
        $pdf->Cell(15, 4, 'Importe', 0, 1, 'C');
        $pdf->Cell(70, 2, '-------------------------------------------------------------------------', 0, 1, 'L');

        // Recorrer los items y mostrar sus datos
        $totalProductos = 0;
        $pdf->SetFont('Arial', '', 7);

        foreach ($items as $item) {
            // Obtener cantidad, nombre y precio de los detalles
            $cantidad = $item['quantity'];
            $nombre = $item['details']['name'];
            $precio = $item['details']['price'];
            $importe = number_format($cantidad * $precio, 2, '.', ',');

            $totalProductos += $cantidad;

            $pdf->Cell(10, 4, $cantidad, 0, 0, 'L');
            $yInicio = $pdf->GetY();
            $pdf->MultiCell(30, 4, mb_convert_encoding($nombre, 'ISO-8859-1', 'UTF-8'), 0, 'L');
            $yFin = $pdf->GetY();

            $pdf->SetXY(45, $yInicio);
            $pdf->Cell(15, 4, chr(128) . ' ' . number_format($precio, 2, '.', ','), 0, 0, 'C');

            $pdf->SetXY(60, $yInicio);
            $pdf->Cell(15, 4, chr(128) . ' ' . $importe, 0, 1, 'R');
            $pdf->SetY($yFin);
        }

        // Mostrar el total de productos
        $pdf->Ln();
        $pdf->Cell(70, 4, mb_convert_encoding('Número de artículos:  ', 'ISO-8859-1', 'UTF-8') . $totalProductos, 0, 1, 'L');

        // Total de la venta
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(70, 5, 'Total: ' . chr(128) . ' ' . number_format($total, 2, '.', ','), 0, 1, 'R');

        $pdf->Ln(2);

        // Consumido en letras
        $pdf->SetFont('Arial', '', 8);
        $pdf->MultiCell(70, 4, 'Son '. chr(128) . ' ' . number_format($total, 2, '.', ','), 0, 'L', 0);

        // Método de pago y consumo
        $pdf->Ln();
        $pdf->Cell(35, 5, mb_convert_encoding('Método de pago: ', 'ISO-8859-1', 'UTF-8'). $paymentMethod, 0, 0, 'C');
        $pdf->Cell(35, 5, 'Consumo: ' . $consumptionOption, 0, 1, 'C');

        // Fecha y hora
        $pdf->Ln();
        $pdf->Cell(35, 5, 'Fecha: ' . date('d/m/Y'), 0, 0, 'C');
        $pdf->Cell(35, 5, 'Hora: ' . date('H:i'), 0, 1, 'C');

        $pdf->Ln();

        // Mensaje de agradecimiento
        $pdf->MultiCell(70, 5, 'GRACIAS POR VENIR... VUELVA PRONTO!!!', 0, 'C');

        // Salida del PDF
        $filePath = storage_path('app/public/tickets/invoice.pdf');
        $pdf->Output('F', $filePath);

        // Devolver la ruta del archivo PDF
        return $filePath;
    }


    public function printReceiptPlainText(Request $request)
    {

        $validated = $request->validate([
            'id' => 'required|numeric',
            'paymentMethod' => 'nullable|string',
            'consumptionOption' => 'required|string',
            'total' => 'required|numeric',
            'items' => 'required|array'
        ]);


        // Recibe el objeto Order desde la request
        $id = $request->input('id');
        $paymentMethod = $request->input('paymentMethod');
        $consumptionOption = $request->input('consumptionOption');
        $total = $request->input('total');
        $items = $request->input('items');

        try {

            // Conectar a la impresora en red
            // $connector = new NetworkPrintConnector("192.168.0.230", 9100); // IP de la impresora
            $connector = new WindowsPrintConnector("POS-80C"); //XP-80C
            $printer = new Printer($connector);


            // if (!file_exists(public_path('storage/logos/LogoKC.png'))) {
            //     return response()->json(['error' => 'El archivo de logo no existe.']);
            // }            

            // // Imprimir el logo (asegúrate de que el archivo está en la ruta correcta)
            // $logo = EscposImage::load(public_path('storage/logos/LogoKC.png'), true); 
            // $printer->setJustification(Printer::JUSTIFY_CENTER); // Centrar
            // $printer->bitImage($logo); // Imprimir imagen

            // Imprimir cabecera del ticket
            $printer->setTextSize(2, 2); // Texto grande
            $printer->text("Pedido N° " . $id . "\n");
            $printer->setTextSize(1, 1); // Texto normal
            $printer->text("Fecha: " . date('Y-m-d H:i:s') . "\n");
            $printer->text("Método de pago: " . $paymentMethod . "\n");
            $printer->text("Opción de consumo: " . $consumptionOption . "\n\n");

            // Imprimir los artículos del pedido
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->text("Artículos:\n");
            foreach ($items as $item) {
                $this->printOrderItem($printer, $item);
            }

            // Imprimir el total
            $printer->text("\n---------------------------\n");
            $printer->setTextSize(2, 2); // Texto grande
            $printer->text("Total: " . $total . " €\n");
            $printer->setTextSize(1, 1); // Texto normal

            // Finalizar el ticket
            $printer->text("\nGracias por su compra\n");
            $printer->cut();

            // Cerrar la conexión con la impresora
            $printer->close();

            return response()->json(['success' => 'Ticket generado correctamente']);
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

    /********************************************************************* */

}