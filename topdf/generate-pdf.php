<?php

require __DIR__ . "/vendor/autoload.php";

use Dompdf\Dompdf;
use Dompdf\Options;

$name = $_POST["name"];


$html = '<h1>Example</h1>';
$html = "Hello <em>$name</em>";
$html = '<img src="bu_logo.png">';

/**
 * Set the Dompdf options
 */
$options = new Options;
$options->setChroot(__DIR__);
$options->setIsRemoteEnabled( true);

$dompdf = new Dompdf($options);

$dompdf->setPaper("Legal", "portrait");

/**
 * Load the HTML and replace placeholders with values from the form
 */
$html = file_get_contents("template.html");

$html = str_replace(["{{ name }}"], [$name], $html);

$dompdf->loadHtml($html);
$dompdf->loadHtmlFile("template.html");

/**
 * Create the PDF and set attributes
 */
$dompdf->render();

$dompdf->addInfo("Title", "Paymen & Release Form"); // "add_info" in earlier versions of Dompdf

/**
 * Send the PDF to the browser
 */
$dompdf->stream("file.pdf", ["Attachment" => 0]);

/**
 * Save the PDF file locally
 */
$output = $dompdf->output();
file_put_contents("file.pdf", $output);