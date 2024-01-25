
 <meta content="" name="description">
  <meta content="" name="keywords">
<meta name="csrf-token" content="{{ csrf_token() }}" />




  <!-- Favicons -->
  <link href="<?=url('')?>/dashboard/assets/img/favicon.png" rel="icon">
  <link href="<?=url('')?>/dashboard/assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="<?=url('')?>/dashboard/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?=url('')?>/dashboard/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="<?=url('')?>/dashboard/assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="<?=url('')?>/dashboard/assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="<?=url('')?>/dashboard/assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="<?=url('')?>/dashboard/assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="<?=url('')?>/dashboard/assets/vendor/simple-datatables/style.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <!-- Template Main CSS File -->
  <link href="<?=url('')?>/dashboard/assets/css/style.css" rel="stylesheet">

  <!-- =======================================================
  * Template Name: NiceAdmin - v2.5.0
  * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
  <style>

      
      label.form-label {
    font-weight: bold;
    color: #795548;
}


.btn-primary, .btn-primary:hover, .btn-primary:active, .btn-primary:visited {
    background-color: #795548 !important;
    border-color: #795548 !important;
}
 .btn-primary:hover {
     background-color: #be681f !important;
    border-color: #be681f !important;
}

body {
  
    background: url("{{url('dashboard/assets/img/nature-colorful-landscape-dusk-cloud.jpg')}}");
    background-attachment: fixed;
    background-size: 100% 100%;
 

}

.sidebar-nav .nav-link {

    color: #000 ;
  
}


.sidebar-nav .nav-link :hover {

    color: #795548;

}

.sidebar-nav .nav-link.collapsed {
    color: #000;
   background: #ffffff1f;
}
.sidebar-nav .nav-link.collapsed :hover{
      color: #795548;

}
 

.logo span {
  
    color: #795548;
  
}

.logo img {
    max-height: 45px;

}




.sidebar {

    /* box-shadow: 0px 0px 20px rgba(1, 41, 112, 0.1); */
   
  background: #fff;
background: -moz-linear-gradient(270deg, transparent 0%, white 98%);
background: -webkit-linear-gradient(270deg, transparent 0%, white 98%);
background: linear-gradient(270deg, transparent 0%, white 98%);
filter: progid:DXImageTransform.Microsoft.gradient(startColorstr="#ffffff",endColorstr="#000000",GradientType=1);
     box-shadow: none;
}

.sidebar-nav .nav-link.collapsed i ,.sidebar-nav .nav-link i   {
  color: #795548;
}
.sidebar-nav .nav-link.collapsed:hover i ,.sidebar-nav .nav-link:hover i {
  color: brown;
}

.pagetitle h1 {
   
    color: #ffffff;
    margin-bottom: 1rem;
}



.breadcrumb .active {
   font-weight: bold;
   color: #795548
   
}
.breadcrumb  {
  
   color: #b55b0e; 
}

.breadcrumb a {
    color: #000; 
   
}


.card {
     
  background: #fff;
background: -moz-linear-gradient(0deg, transparent 0%, #ffffff6b 98%);
background: -webkit-linear-gradient(0deg, transparent 0%, #ffffff6b 98%);
background: linear-gradient(0deg, transparent 0%, #ffffff6b 98%);
filter: progid:DXImageTransform.Microsoft.gradient(startColorstr="#ffffff",endColorstr="#000000",GradientType=1);
box-shadow: none;
    
}
.bg-trans {
     
  background: #fff;
background: -moz-linear-gradient(180deg, transparent 0%, #ffffff6b 98%);
background: -webkit-linear-gradient(180deg, transparent 0%, #ffffff6b 98%);
background: linear-gradient(180deg, transparent 0%, #ffffff6b 98%);
filter: progid:DXImageTransform.Microsoft.gradient(startColorstr="#ffffff",endColorstr="#000000",GradientType=1);
box-shadow: none;
    
}
.bg-trans{
      border-radius: 0.3rem;
    margin-bottom: 1rem;
}
.table-container{
        min-height: 78vh;
}
.form-control{
        border: none;
}
.modal-content{
        background-color: #ffffffde;
}



    /* width */
    ::-webkit-scrollbar {
        width: 0px;
          transition: all 0.5s ease-in-out;
    }

    /* Track */
    ::-webkit-scrollbar-track {
        background: #e9e9e9;
    }

    /* Handle */
    ::-webkit-scrollbar-thumb {
        background: #fff;
        border-radius: 5px;
    }

    /* Handle on hover */
    ::-webkit-scrollbar-thumb:hover {
        background: #ccc;
      
        

    }
    a {
    color: #2196F3;
  
}

.header{
    
  background: #fff;
/*background: -moz-linear-gradient(270deg, transparent 0%, white 98%);
background: -webkit-linear-gradient(270deg, transparent 0%, white 98%);
background: linear-gradient(270deg, transparent 0%, white 98%);
filter: progid:DXImageTransform.Microsoft.gradient(startColorstr="#ffffff",endColorstr="#000000",GradientType=1);*/
    
}

.header .search-form input {

    border-radius: 1rem;
    border:  none;
}
#main{
    min-height: 84vh;
}
.indi{
        position: absolute;
    right: 30px;
    color: #fff;
    font-size: 12px;
}


input.form-checkbox {
    transform: scale(1.9);
    margin: 5px;
}
input.form-radio {
    transform: scale(1.5);
    margin: 5px;
}
  </style>