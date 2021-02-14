<?php
//
// Author: Alejandro Landini
// from previewImages.php 7/4/18
// update 10/9/20

if (!function_exists('getMemberInfo')) {
    die('{ "error": "Invalid way to access." }');
}
require 'handlebars-php/src/Handlebars/Autoloader.php';
//load handlebars php library
Handlebars\Autoloader::register();

use Handlebars\Handlebars;
use Handlebars\Loader\FilesystemLoader;

$currDir = dirname(__FILE__);

# Set the partials files
$partialsDir = [__DIR__ . "/templates",__DIR__ . "/templates/elements"];
$partialsLoader = new FilesystemLoader(
    $partialsDir,
    [
        "extension" => "hbs",
        "prefix"    => "bs3_"
    ]
);

# We'll use $handlebars throughout this the examples, assuming the will be all set this way
$handlebars = new Handlebars([
    "loader" => $partialsLoader,
    "partials_loader" => $partialsLoader,
    "enableDataVariables" => false
]);

$handlebars = registerHelpers($handlebars);

function registerHelpers($handlebars)
{
    $handlebars->addHelper(
        "filemtime",
        function ($template, $context, $args, $source) {
            $data = ($context->get($args));
            $file = $data['folder'] . $data['name'] . '_th.'.$data['extension'];
            if (file_exists($file)) {
                return filemtime($file);
            }
            return 0;
        }
    );

    $handlebars->addHelper(
        "when",
        function ($template, $context, $args, $source) {
            $m = explode(" ", $args);
            $keyname = $m[0];
            $when = $m[1];
            $compare = $m[2];
            $data = $context->get($keyname);

            switch ($when) {
                case 'eq':
                    if ($data == $compare) {
                        return $template->render($context);
                    }
                    break;
                case '!eq':
                    if ($data !== $compare) {
                        return $template->render($context);
                    }
                    // no break
                default:
                    break;
            }
            return false; // $data.' ::: '.$when.':::'.$comapare.':::'.count($m);
        }
    );

    $handlebars->addHelper(
        "admin",
        function ($template, $context, $args, $source) {
            $mi = getMemberInfo();
            return $mi['admin'] ? $template->render($context) : false;
        }
    );
    
    $handlebars->addHelper(
        "app_datetime",
        function ($template, $context, $args, $source) {
            return app_datetime($context->get($args), 'dt');
        }
    );

    return $handlebars;
}
