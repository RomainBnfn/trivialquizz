<?php
  session_start();
  //TODO: Changer la location
  $index_location = "/trivial/trivialquizz/admin/index.php";

  //TODO: Changer àa
  $_SESSION["is_admin"] = true;
  if(!isset($_SESSION['is_admin']))
  {
    header("Location: ".$index_location);
    exit();
  }

  require_once "../include/liaisonbdd.php";
  require_once "../include/functions.php";

  if(empty($_GET['id']) || !is_numeric($_GET['id']) )
  {
    header("Location: ".$index_location);
    exit();
  }

  $_QUIZZ = tryLoadQuizz($bdd, $_GET["id"]);
  if(empty($_QUIZZ))
  {
    header("Location: ".$index_location);
    exit();
  }



  $_THEME = tryLoadTheme($bdd, $_QUIZZ["id_theme"]);
  if(empty($_THEME))
  {
    header("Location: ".$index_location);
    exit();
  }

  $_QUESTIONS = tryLoadQuizzQuestion($bdd, $_QUIZZ["id"]);
?>
<!doctype html>
<html lang="fr">
<head>
  <title>Edition de Quizz</title>
  <?php require_once "../include/header.html"?>
</head>
<body>
  <?php require_once "../include/navbar.php"?>
  <!-- TODO: Ajouter une couleur en fct de $couleur -->
  <div id="titreGeneral" class="bandeau-principal">Edition de Quizz : <?= $_QUIZZ["nom"] ?></div>

  <div class="cadre-global">
    <div class="cadre-central">

      <?php require_once "include/admin-navbar.php"?>

      <!-- DEBUT : Cadre des options générales -->
      <div>
        <h2 class="titre1">
          <div>Paramètres généraux</div>
          <div>
            <!-- Quand on appuie sur le bouton, on envoie une requête -->
            <button type="button" class="btn btn-danger">Supprimer le quizz</button>
          </div>
        </h2>
        <div class="container">
          <div>


            <!-- A gauche : Changer le nom... -->
            <!-- A Rajouter

            Bouton Radio (Une après l'autre / En même temps) :
            Bouton Radio Temps Fixe / Décroissant avec difficulté
              Temps max:
            A chaque niveau de difficulté : -5% à -10% (Afficher : 1e : 100% (5min) 5e : 60% (3 min))
            Sauvegarder les modifications
          -->
            <form id="editGeneral" method="POST">
              <div>
                <!-- Le message est par défaut pas affiché-->
                <div id="errorGeneral_Nom" style="color: red; visibility: hidden;"> Ce nom est déjà utilisé !</div>
                <label name="nom">Nom : </label>
                <input id="editGeneral_Nom" type="text" name="nom" value="<?= $_QUIZZ["nom"] ?>" required/>
              </div>
              <br/>

              <div>
                <div id="errorGeneral_Desc"></div>
                <label name="desc">Description : </label>
                <textarea id="formGeneralDesc" type="text" name="desc" rows="5" draggable="false" required ><?= $_QUIZZ["desc"] ?></textarea>
              </div> <br/>

              <div>
                <label name="id_theme">Thème :</label>
                <select name="id_theme" size="1">
                  <?php
                  foreach (getAllThemesInfos($bdd) as $_THEME)
                  {
                  ?>
                    <option value="<?= $_THEME["id"] ?>" <?php if($_THEME["id"] == $_QUIZZ["id_theme"]) echo "selected"; ?>><?= $_THEME["nom"] ?></option>
                  <?php
                  }
                  ?>
                </select>
              </div> <br/>

              <input type="hidden" name="ancien_nom" value="<?= $_QUIZZ["nom"] ?>" />
              <input type="hidden" name="id" value="<?= $_QUIZZ["id"] ?>" />

              <input class="btn btn-success" value="Sauvegarder" type="submit" />
            </form>

          </div>
          <div>
            <!-- A droite : Les statistiques générales: Chargé en dernier
            pour pas prendre trop de temps à la génération -->
            Nb de fois effectué:
            Score moyen:
            Temps moyen:
          </div>
        </div>
      </div>
      <!-- FIN : Cadre des options générales -->

<!-- ================================================================================= -->

      <!-- DEBUT : Cadre de la liste des Questions -->
      <div>
        <h2 class="titre1">
          <div>Les questions</div>
          <div>
            <button type="button" class="btn btn-success">Ajouter</button>
            <button type="button" class="btn btn-success">Importer</button>
          </div>
        </h2>

        <div id="containerQuestions">
          <?php
            $_QUESTIONS = tryLoadQuizzQuestion($bdd, $_QUIZZ["id"]);
            if (empty($_QUESTIONS) || $_QUESTIONS == null )
            {
              echo "Ce quizz n'a aucune question ! Pensez à en rajouter !";
            }
            else
            {
              print_r($_QUESTIONS);
              foreach ($_QUESTIONS as $_QUESTION)
              { ?>
                <form id="editQestionN<?= $_QUESTION["id"] ?>" method="" onsubmit="">
                  <div>
                    <div id="errorQuestion_LibelleN<?= $_QUESTION["id"] ?>" style="color: red; visibility: hidden;">Vous ne pouvez pas laisser de libelle vide..</div>
                    <label name="libelle">Libelle : </label>
                    <input id="editQuestion_LibelleN<?= $_QUESTION["id"] ?>" type="text" name="libelle" value="<?= $_QUESTION["lib"] ?>" required/>
                  </div>
                </form>
                <?php
              }
            }
          ?>
        </div>
      </div>
      <!-- FIN : Cadre de la liste des Questions -->

    </div>
  </div>

  <?php require_once "../include/script.html"?>
  <script>
    var listeNoms = <?=json_encode(getAllQuizzNames($bdd))?>,
        nameQuizz = <?=json_encode($_QUIZZ["nom"])?>;
    listeNoms = listeNoms.filter(function(value, index, arr){ return value != nameQuizz;})

    $(document).ready(function(){

      $("#editGeneral").submit((e) => {

        e.preventDefault();

        var form = new FormData(document.getElementById("editGeneral"));
        fetch("ajax/quizz-save-edit.php", {
          method: "POST",
          body: form
        })
        .then((response) => {
          response.text()
          .then((resp) => {
            $("#editGeneral").text(resp);
          })
        });
      });

      $("#editGeneral_Nom").keyup(() => {
        $("#titreGeneral").text("Edition de Quizz : "+ $("#editGeneral_Nom").val());
        if( listeNoms.includes( $("#editGeneral_Nom").val() ))
        {
          $("#errorGeneral_Nom").css("visibility", "visible");
        } else {
          $("#errorGeneral_Nom").css("visibility", "collapse");
        }
      });

    });
  </script>
</body>
</html>
