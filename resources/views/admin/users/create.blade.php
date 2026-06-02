<x-layout title="Add User">@include('admin.users.partials.form', ['user' => null, 'action' => route('admin.users.store'), 'method' => 'POST'])</x-layout>
