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
<link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.1.4/datatables.min.css" rel="stylesheet">
