<?php
namespace App\Core;

use duncan3dc\Laravel\BladeInstance;

class View {
    private $blade;

    public function __construct() {
        $this->blade = new BladeInstance(__DIR__ . "/../../views", __DIR__ . "/../../cache");
    }

    public function render($view, $data = []) {
        echo $this->blade->render($view, $data);
    }
}
