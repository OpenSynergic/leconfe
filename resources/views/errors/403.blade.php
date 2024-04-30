@extends('errors::minimal')

@section('code', '403')
@php
    $message = $exception->getMessage() ?: 'Forbidden';
@endphp
