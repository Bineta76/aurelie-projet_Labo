<?php
session_start();
include 'includes/header.php';
?>
<div class="container">
   

    <!-- Section principale -->
    <h3>Laboratoires disponibles</h3>

    <!-- Galerie d'images avec Bootstrap -->
    <div class="row">
        <!-- Image 1 -->
        <div class="col-sm-4">
            <img src="assets/images/bordeaux.jpg" alt="Bordeaux" class="img-responsive img-thumbnail" style="width:100%; height:200px;">
        </div>

        <!-- Image 2 -->
        <div class="col-sm-4">
            <img src="assets/images/cliniqueduparc.jpg" alt="Clinique du Parc" class="img-responsive img-thumbnail" style="width:100%; height:200px;">
        </div>

        <!-- Image 3 -->
        <div class="col-sm-4">
            <img src="assets/images/hopitalcentral.jpg" alt="Hôpital Central" class="img-responsive img-thumbnail" style="width:100%; height:200px;">
        </div>

        <!-- Image 4 -->
        <div class="col-sm-4">
            <img src="assets/images/lille.jpg" alt="Lille" class="img-responsive img-thumbnail" style="width:100%; height:200px;">
        </div>

        <!-- Image 5 -->
        <div class="col-sm-4">
            <img src="assets/images/roubaix.jpg" alt="Roubaix" class="img-responsive img-thumbnail" style="width:100%; height:200px;">
        </div>
    </div>

</div>

<?php include 'includes/footer.php'; ?>



