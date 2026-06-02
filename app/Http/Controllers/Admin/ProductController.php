<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    public function index() { return view('admin.products.index'); }
    public function create() { return view('admin.products.create'); }
    public function store() {}
    public function edit($product) { return view('admin.products.edit', ['product' => null]); }
    public function update($product) {}
    public function destroy($product) {}
}
