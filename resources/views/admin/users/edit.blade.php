<x-layout title="Edit User">@include('admin.users.partials.form', ['user' => $user ?? null, 'action' => route('admin.users.update', $user ?? 1), 'method' => 'PUT'])</x-layout>
