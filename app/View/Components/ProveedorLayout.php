<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ProveedorLayout extends Component
{
    public function __construct(public string $title = 'Portal') {}

    public function render()
    {
        return view('layouts.proveedor');
    }
}
