@props(['title'])

@include('layouts.manage', ['title' => $title ?? null])
