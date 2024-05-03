@extends('errors::minimal')

@php
    $code = '429';
    $title = 'Too Many Request';
    $message = 'The request was rejected because too many requests were made';
@endphp

