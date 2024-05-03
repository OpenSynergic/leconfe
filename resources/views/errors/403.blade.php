@extends('errors::minimal')

@php
    $code = '403';
    $title = $exception->getMessage() ?: 'Forbidden';
    $message = 'Access denied, you do not have permission to access this page';
@endphp
