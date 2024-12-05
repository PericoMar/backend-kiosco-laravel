const express = require('express');
const bodyParser = require('body-parser');
const path = require('path');
const fs = require('fs');
const axios = require('axios');
const temp = require('temp'); // Para manejar archivos temporales
const printer = require('printer'); // node-printer

const app = express();
const port = 3000; // El puerto que deseas usar para tu servidor Node

// Middleware para parsear JSON en el cuerpo de la solicitud
app.use(bodyParser.json());

// Ruta para recibir la solicitud de impresión
app.post('/imprimir', async (req, res) => {
    const { pdfPath, printerName } = req.body;

    // Verificamos si la URL del PDF es válida
    if (!pdfPath) {
        return res.status(400).send({ error: 'URL de PDF no proporcionada' });
    }

    // Medir el tiempo de inicio de la descarga del PDF
    const downloadStartTime = Date.now();

    try {
        // Descargar el PDF desde la URL proporcionada
        const response = await axios({
            method: 'get',
            url: pdfPath,
            responseType: 'arraybuffer'
        });

        // Crear un archivo temporal para guardar el PDF descargado
        const tempFilePath = path.join(__dirname, 'temp_invoice.pdf');
        fs.writeFileSync(tempFilePath, response.data);
        console.log(`Archivo PDF guardado en: ${tempFilePath}`);

        // Calcular el tiempo que tardó en descargar el PDF
        const downloadTime = Date.now() - downloadStartTime;
        console.log(`Tiempo de descarga del PDF: ${downloadTime} ms`);

        // Medir el tiempo de inicio de la impresión
        const printStartTime = Date.now();

        // Verificar si la impresora está disponible
        const printerNameToUse = printerName || "POS-80C"; // Asegúrate de que se utilice el nombre correcto

        // Intentar imprimir el archivo PDF usando node-printer
        printer.printFile({
            filename: tempFilePath,
            printer: printerNameToUse,
            type: 'PDF',
            success: function (jobID) {
                console.log(`Impresión enviada correctamente con JobID: ${jobID}`);
            },
            error: function (err) {
                console.error('Error al enviar el trabajo de impresión:', err);
                return res.status(500).send({ error: 'Hubo un error al intentar imprimir el PDF' });
            }
        });

        // Calcular el tiempo que tardó en enviar el trabajo de impresión
        const printTime = Date.now() - printStartTime;
        console.log(`Tiempo de impresión: ${printTime} ms`);

        // Responder al cliente
        res.send({
            success: true,
            message: 'Impresión exitosa',
            downloadTime,
            printTime
        });
    } catch (error) {
        console.error('Error al descargar o imprimir el PDF:', error);
        res.status(500).send({ error: 'Hubo un error al intentar imprimir' });
    }
});

// Iniciar el servidor
app.listen(port, () => {
    console.log(`Servidor Node corriendo en http://localhost:${port}`);
});

// const express = require('express');
// const bodyParser = require('body-parser');
// const pdfToPrinter = require('pdf-to-printer');
// const path = require('path');
// const fs = require('fs');
// const axios = require('axios');
// const temp = require('temp'); // Para manejar archivos temporales

// const app = express();
// const port = 3000; // El puerto que deseas usar para tu servidor Node

// // Middleware para parsear JSON en el cuerpo de la solicitud
// app.use(bodyParser.json());

// // Ruta para recibir la solicitud de impresión
// app.post('/imprimir', async (req, res) => {
//     const { pdfPath, printerName } = req.body;

//     const printers = await pdfToPrinter.getPrinters();
//     console.log('Impresoras disponibles:', printers);

//     // Verificamos si la URL del PDF es válida
//     if (!pdfPath) {
//         return res.status(400).send({ error: 'URL de PDF no proporcionada' });
//     }

//     // Medir el tiempo de inicio de la descarga del PDF
//     const downloadStartTime = Date.now();

//     try {
//         // Descargar el PDF desde la URL proporcionada
//         const response = await axios({
//             method: 'get',
//             url: pdfPath,
//             responseType: 'arraybuffer'
//         });

//         // Crear un archivo temporal para guardar el PDF descargado
//         const tempFilePath = path.join(__dirname, 'temp_invoice.pdf');
//         fs.writeFileSync(tempFilePath, response.data);
//         console.log(`Archivo PDF guardado en: ${tempFilePath}`);

//         // Calcular el tiempo que tardó en descargar el PDF
//         const downloadTime = Date.now() - downloadStartTime;
//         console.log(`Tiempo de descarga del PDF: ${downloadTime} ms`);

//         // Medir el tiempo de inicio de la impresión
//         const printStartTime = Date.now();

//         // Imprimir el archivo PDF descargado
//         const options = {
//             printer: printerName || "POS-80C", // Asegúrate de que se utilice el nombre correcto
//             scale: 'shrink',  // Usa 'noscale', 'shrink' o 'fit' como valor
//             win32: {
//                 printer: printerName || "POS-80C"
//             }
//         };

//         const msg = await pdfToPrinter.print(tempFilePath, options);

//         // Calcular el tiempo que tardó en imprimir
//         const printTime = Date.now() - printStartTime;
//         console.log(`Tiempo de impresión: ${printTime} ms`);

//         res.send({
//             success: true,
//             message: 'Impresión exitosa',
//             msg,
//             downloadTime,
//             printTime
//         });
//     } catch (error) {
//         console.error('Error al descargar o imprimir el PDF:', error);
//         res.status(500).send({ error: 'Hubo un error al intentar imprimir' });
//     }
// });

// // Iniciar el servidor
// app.listen(port, () => {
//     console.log(`Servidor Node corriendo en http://localhost:${port}`);
// });
