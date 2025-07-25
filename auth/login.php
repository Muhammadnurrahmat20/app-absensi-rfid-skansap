<?php
session_start();
if (isset($_SESSION['username'])) {
    header('location:../index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Login - Absensi SKANSAP</title>
    <link href="../css/styles.css" rel="stylesheet">
    </script>
</head>

<body class="bg-primary">
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container mt-5">
                    <div class="row justify-content-center">
                        <div class="col-lg-5 mt-5">
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
                                </div>
                                <div class="card-header">
                                    <div class="text-center mt-3">
                                        <img src="https://smkn1pangkep.sch.id/assets/media/logos/logo.png" alt="logo" width="150">
                                    </div>
                                    <h1 class="text-center font-bold my-4">SISTEM ABSENSI PEGAWAI SKANSAP</h1>
                                </div>
                                
                                <div class="card-body">
                                    <!-- START MESSAGE -->
                                    <?php
                                        if(isset($_GET['msg']) == 1 && $_SERVER['HTTP_REFERER']){
                                    ?>
                                    <div class="alert alert-danger alert-dismissable" role="alert">
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        <p class="mb-0">Silahkan isi Username & Password!</p>
                                    </div>
                                    <?php
                                        } else if(isset($_GET['msg']) == 2 && $_SERVER['HTTP_REFERER']){
                                    ?>
                                    <div class="alert alert-danger alert-dismissable" role="alert">
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        <p class="mb-0">Username/Password salah!</p>
                                    </div>
                                    <?php
                                        } else if(isset($_GET['msg']) == 3 && $_SERVER['HTTP_REFERER']){
                                    ?>
                                    <div class="alert alert-danger alert-dismissable" role="alert">
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        <p class="mb-0">Terjadi Kesalahan!</p>
                                    </div>
                                    <?php
                                        }
                                    ?>
                                    <!-- END MESSAGE -->
                                    <form action="./login_post.php" method="POST">
                                        <div class="form-group">
                                            <label class="small mb-1" for="inputUsername">Username</label>
                                            <input class="form-control py-4" id="inputUsername" name="username" type="text"
                                                placeholder="Masukkan username">
                                        </div>
                                        <div class="form-group">
                                            <label class="small mb-1" for="inputPassword">Password</label>
                                            <input class="form-control py-4" id="inputPassword" name="password" type="password"
                                                placeholder="Masukkan password">
                                        </div>
                                        <div class="form-group">
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col">
                                                        <button class="btn btn-primary form-control btn-block"
                                                            type="submit">Login</button>
                                                    </div>
                                                    <div class="col">
                                                        <button class="btn btn-danger form-control  btn-block"
                                                            type="reset">Reset</button>
                                                    </div>    
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="card-header">
                                    <div class="text-center">
                                        &copy;2025 SMK Negeri 1 Pangkep
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>

        <script src="../assets/js/jquery-3.5.1.min.js"></script>
        <script src="../assets/js/bootstrap.bundle.min.js"></script>
        <!-- <script src="../assets/js/scripts.js"></script> -->
</body>
</html>