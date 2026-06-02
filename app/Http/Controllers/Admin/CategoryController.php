<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    public function index() { return view('admin.categories.index'); }
    public function create() { return view('admin.categories.create'); }
    public function store() {}
    public function edit($category) { return view('admin.categories.edit', ['category' => null]); }
    public function update($category) {}
    public function destroy($category) {}
}
