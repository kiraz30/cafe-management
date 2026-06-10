@extends('layouts.app')
@section('page-title', 'Dashboard Pelayan')
@section('content')
<h5>Selamat datang, {{ auth()->user()->name }}!</h5>
@endsection