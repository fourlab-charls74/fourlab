
        <link rel="shortcut icon" href="/theme/{{ config('shop.theme')}}/favicon.ico"/>
        <style>
            body{padding-top:500%;}
        </style>
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

        <link href="{{ URL::asset('/with/css/bootstrap.min.css')}}" id="bootstrap-light" rel="stylesheet" type="text/css" />
        <link href="{{ URL::asset('/with/css/bootstrap_dark.min.css')}}" id="bootstrap-dark" rel="stylesheet" type="text/css" disabled />

        <link href="{{ URL::asset('/with/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
        <link href="/handle/grid/ag-grid/community/dist/styles/ag-grid.css" rel="stylesheet" />
        <link href="/handle/grid/ag-grid/community/dist/styles/ag-theme-alpine.css" rel="stylesheet" />
        <link href="{{ URL::asset('/skin/libs/select2/select2.min.css')}}" type="text/css" rel="stylesheet" />
        <link href="{{ URL::asset('/with/css/datepicker.css')}}" type="text/css" rel="stylesheet" />

        <link href="{{ URL::asset('/with/css/app.css')}}?v=2023082312" id="app-light" rel="stylesheet" type="text/css" />
        <link href="{{ URL::asset('/with/css/app_dark.css')}}?v=2023082312" id="app-dark" rel="stylesheet" type="text/css" disabled />

        @if (env('GRID_LICENSE') != "")
            <script src="/handle/grid/ag-grid/enterprise/dist/ag-grid-enterprise.min.js"></script>
            <script>
                agGrid.LicenseManager.setLicenseKey("{{env('GRID_LICENSE')}}");
            </script>
        @else
            <script src="/handle/grid/ag-grid/community/dist/ag-grid-community.min.js"></script>
        @endif
        <script src="/handle/grid/grid.js?v=20220707"></script>
        <script src="/handle/grid/functions.js?v=2023061615"></script>
        <script src="/handle/libs.js"></script>
        <script src="/js/init.js"></script>
        <link rel="stylesheet" href="/handle/grid/style.css" type="text/css">
