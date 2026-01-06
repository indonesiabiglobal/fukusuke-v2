@yield('css')
<!-- Layout config Js -->
<script src="{{ URL::asset('build/js/layout.js') }}"></script>
<!-- Bootstrap Css -->
<link rel="stylesheet" href="{{ URL::asset('build/css/bootstrap.min.css') }}" type="text/css" />
<!-- Icons Css -->
<link rel="stylesheet" href="{{ URL::asset('build/css/icons.min.css') }}" type="text/css" />
<!-- App Css-->
<link rel="stylesheet" href="{{ URL::asset('build/css/app.min.css') }}" type="text/css" />
<!-- custom Css-->
<link rel="stylesheet" href="{{ URL::asset('build/css/custom.min.css') }}" type="text/css" />
{{-- file upload --}}
<link href="{{ URL::asset('build/libs/dropzone/dropzone.css') }}" rel="stylesheet">
<!--datatable css-->
{{-- <link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.1.4/datatables.min.css" rel="stylesheet"> --}}
{{-- Select2 --}}
{{-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@3.5.1/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" /> --}}

<!--datatable css-->
<link href="{{ URL::asset('build/libs/datatables/css/datatables.min.css') }}" rel="stylesheet">
{{-- Select2 --}}
<link href="{{ URL::asset('build/libs/select2/css/select2.min.css') }}" rel="stylesheet" />
<link rel="stylesheet" href="{{ URL::asset('build/libs/select2/css/select2-3.5.1.min.css') }}">
<link rel="stylesheet" href="{{ URL::asset('build/libs/select2/css/select2-bootstrap-5-theme.min.css') }}" />
