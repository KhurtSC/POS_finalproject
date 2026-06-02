<x-layout title="Add Category">@include('admin.categories.partials.form', ['category' => null, 'action' => route('admin.categories.store'), 'method' => 'POST'])</x-layout>
