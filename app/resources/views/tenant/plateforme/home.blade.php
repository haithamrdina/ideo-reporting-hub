@extends('master')
@section('title')
    Tableau de bord
@stop
@section('ideoreport_css')
@stop
@section('header')
    @include('tenant.partials.navbar.overlap-topbar')
@stop
@section('page-header')
<div class="page-header d-print-none text-white">
    <div class="container-xl">
      <div class="row g-2 align-items-center">
        <div class="col">
          <div class="page-pretitle">
            IDEO Reporting
          </div>
          <h2 class="page-title">
            Tableau de bord plateforme
          </h2>
        </div>
      </div>
    </div>
  </div>
@stop
@section('page-content')
@stop
@section('footer')
    @include('tenant.partials.footer.bottom')
@stop
@section('ideoreport_libs')
@stop
@section('ideoreport_js')
@stop
