 
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title> Backend | Dashboard</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="shortcut icon" href="<?php echo base_url().BASE_PIC;?>front/icon/favicon.png">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo $base_bn;?>plugins/fontawesome-free/css/all.min.css">
  <!-- <link rel="shortcut icon" href="<?php echo base_url().BASE_PIC;?>front/icon/favicon.png" type="image/png"> -->
  <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">

  <link href="https://fonts.googleapis.com/css?family=Poppins:100,200,400,300,500,600,700" rel="stylesheet"> 
  <link href="https://fonts.googleapis.com/css?family=Prompt:400,400i,700,700i,900,900i|Roboto:400,400i,900,900i" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Kanit&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">

  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bbootstrap 4 -->
  <link rel="stylesheet" href="<?php echo $base_bn;?>plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="<?php echo $base_bn;?>plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- JQVMap -->
  <link rel="stylesheet" href="<?php echo $base_bn;?>plugins/jqvmap/jqvmap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo $base_bn;?>dist/css/adminlte.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="<?php echo $base_bn;?>plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="<?php echo $base_bn;?>plugins/daterangepicker/daterangepicker.css">
  <!-- summernote -->
  <link rel="stylesheet" href="<?php echo $base_bn;?>plugins/summernote/summernote-bs4.css">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
   
  <!-- DataTables -->
  <link rel="stylesheet" href="<?php echo base_url().BASE_BN;?>plugins/datatables-bs4/css/dataTables.bootstrap4.css">

  <!--
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.4/css/jquery.dataTables.min.css">
   -->
	
  <!-- Bootstrap Color Picker -->
  <link rel="stylesheet" href="<?php echo $base_bn;?>plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css">
   
  <!-- Select2 -->
  <link rel="stylesheet" href="<?php echo $base_bn;?>plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="<?php echo $base_bn;?>plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
  <!-- Bootstrap4 Duallistbox -->
  <link rel="stylesheet" href="<?php echo $base_bn;?>plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css">
  <!-- Ekko Lightbox -->
  <link rel="stylesheet" href="<?php echo $base_bn;?>plugins/ekko-lightbox/ekko-lightbox.css">
   <!-- SweetAlert2 -->
  <link rel="stylesheet" href="<?php echo $base_bn;?>plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
 <!--
  <link rel="stylesheet" href="<?php echo $base_bn;?>plugins/sweetalert2/sweetalert2.min.css">
 -->
 <!-- Toastr -->
  <link rel="stylesheet" href="<?php echo $base_bn;?>plugins/toastr/toastr.min.css">
  <style>
        .min-height {min-height: 300px;}
        .titel {padding: 10px; background-color: #343a40; color: #FFF; margin-bottom: 25px;}
        body,td,th,ul,li,em,p,h2 {
            /* font-family: 'Kanit', sans-serif; */
            font-size: 10pt;
        }
 
        .imagepro {
        display: block;
        width: 100%;
        height: auto;
        }

        .overlay {
        /* position: absolute; */
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        height: 100%;
        width: 100%;
        opacity: 0;
        transition: .5s ease;
        background-color: #008CBA;
        }

        .containertext:hover .overlay {
            opacity: 1;
        }

        .text005 {
        color: white;
        text-shadow: 4px 4px 4px #000000;
        font-size: 20px;
        position: absolute;
        top: 50%;
        left: 50%;
        -webkit-transform: translate(-50%, -50%);
        -ms-transform: translate(-50%, -50%);
        transform: translate(-50%, -50%);
        text-align: center;
        }
        .btn-app2 {
            border-radius: 3px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            color: #6c757d;
            font-size: 12px;
            height: 60px;
            min-width: 80px;
            padding: 15px 5px;
            position: relative;
            text-align: center;
        }

        .modal-content {
            position: relative;
            padding: 20px;
            display: -ms-flexbox;
            display: flex;
            -ms-flex-direction: column;
            flex-direction: column;
            width: 100%;
            pointer-events: auto;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid rgba(0,0,0,.2);
            border-radius: .3rem;
            box-shadow: 0 0.25rem 0.5rem rgba(0,0,0,.5);
            outline: 0;
        }
        .bgtxt-warning {
            color: #ff5434!important;
        }
    </style>

  