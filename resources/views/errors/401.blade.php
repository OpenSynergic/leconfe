@extends('errors::minimal')

@php
    $code = '401';
    $title = 'Unauthorized';
    $message = 'Access is denied, you need to log in or provide valid credentials';
@endphp
