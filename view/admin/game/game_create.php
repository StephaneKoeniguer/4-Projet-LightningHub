<!DOCTYPE html>
<html lang="fr" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lightning Hub - Home</title>
    <link rel="icon" type="image/png" href="../assets/images/logo-lightninghub.png">
    <script src="https://kit.fontawesome.com/c608f59341.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

<div class="navbar-brand my-lg-3 ps-lg-3 d-none d-lg-block">Tableau de bord</div>

<div class="d-lg-flex">

    <?php require_once base_path('view/admin/components/nav_admin.php'); ?>


    <section id="dashboard-hub" class="bg-color-purple-faded ms-lg-5 px-3 text-lg-start w-100">

        <div class="d-flex bd-highlight justify-content-between">
            <h2 class="nav-dashboard-title px-lg-3 my-4 py-4 reconstruct">Créer un jeu</h2>
        </div>

        <!-- Update Room Form -->
        <form method="POST" action="<?php echo $actionUrl ?>" class="row m-0 ">
            <input type="text" name="action" value="store" id="update-action-field" hidden>
            <!-- Left Side -->
            <div class="col-lg-5 d-lg-flex flex-column">
                <div>
                    <label for="title" class="mb-2">Titre :</label>
                    <input value=""
                           name="nameGame" id="nameGame"
                           class="input mb-4 w-100" required aria-required="true">
                </div>
                <div>
                    <label for="descriptionShort" class="mb-2">Description courte :</label>
                    <textarea name="descriptionShort" id="descriptionShort" maxlength="100" cols="10" rows="3" class="input mb-4 w-100" required aria-required="true"
                    ></textarea>
                </div>
                <div>
                    <label for="twitch" class="mb-2">Twitch :</label>
                    <input value=""
                           name="twitch" id="twitch"
                           class="input mb-4 w-100" required aria-required="true">
                </div>
                <div>
                    <label for="reddit" class="mb-2">Reddit :</label>
                    <input value=""
                           name="reddit" id="reddit"
                           class="input mb-4 w-100" required aria-required="true">
                </div>
                <div>
                    <label for="officialWebsite" class="mb-2">Twitch :</label>
                    <input value=""
                           name="officialWebsite" id="officialWebsite"
                           class="input mb-4 w-100" required aria-required="true">
                </div>
                <div>
                    <label for="gameModes" class="mb-2">Modes de jeu (séparés d'une virgule) :</label>
                    <input name="gameModes" id="gameModes"
                           class="input mb-4 w-100" required aria-required="true">
                </div>


            </div>

            <!-- Right Side -->
            <div class="col-lg-5 offset-lg-2 d-lg-flex flex-column">

                <div>
                    <label for="title" class="mb-2">Tag :</label>
                    <input
                           name="tag" id="tag"
                           class="input mb-4 w-100" required aria-required="true">
                </div>

                <div>
                    <label for="description" class="mb-2">Description longue :</label>
                    <textarea name="description" id="description" maxlength="100" cols="10" rows="3" class="input mb-4 w-100" required aria-required="true"
                    ></textarea>
                </div>

                <div>
                    <input type="text" name="image" value="image" hidden>

                    <label for="file" class="label-file me-2 p-2 text-center">Choisir une image</label>
                    <input id="file" type="file" name="file">


                </div>
            </div>

            <!-- Buttons -->
            <div class="d-lg-flex col-lg-5 offset-lg-7  flex-lg-row-reverse">
                <button class="btn w-100 lh-buttons-purple mb-3">Valider</button>
                <button class="btn w-100 lh-buttons-purple-faded mb-4 mb-lg-3 me-lg-4">Annuler</button>
            </div>



        </form>



    </section>




</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>