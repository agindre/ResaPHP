<?php
require_once('inc/config.inc.php');
require_once('inc/connec_bdd.inc.php');
require_once('inc/functions.inc.php');

// Avant toute autre chose, on ouvre la session, pour pouvoir accéder aux variables de session
session_start();
unset($_SESSION['num_vol'], $_SESSION['jour'], $_SESSION['mois']);

$flag_reg = FALSE;
if (isset($_SESSION['id'], $_SESSION['nom'], $_SESSION['prenom'], $_SESSION['mail'])) {
    $flag_reg = TRUE;
}

$date = null;
$destination = null;
$ajout_requete = '';

if (!empty($_GET['destination'])) {
    $destination = pg_escape_string($_GET['destination']);
    $ajout_requete = ' AND upper(destination) LIKE \'%'. strtoupper($destination) .'%\'';
}

if (!empty($_GET['date'])) {
    $date = pg_escape_string($_GET['date']);
    $jour = substr($date, 0, 2);
    $mois = substr($date, -2);
    $ajout_requete .= ' AND jour = '. $jour .' AND mois=' . $mois;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
    <head>
        <title>Page liste_depart.php du projet IBD</title>
        <link rel="stylesheet" type="text/css" href="inc/css/base.css" media="all" />
        <link rel="stylesheet" type="text/css" href="inc/css/modele03.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="inc/css/addon.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="inc/css/jquery-ui-1.8.2.custom.css" media="screen" />
        <script type="text/javascript" src="inc/js/jquery-1.4.2.js" ></script>
        <script type="text/javascript" src="inc/js/jquery.ui.core.js" ></script>
        <script type="text/javascript" src="inc/js/jquery.ui.datepicker.js" ></script>
        <script type="text/javascript">
            $(function() {
                //ajoute la gestion du calendrier au champ date
                $("#date").datepicker();
                //pour chaque changement apporté sur le champ date
                $("#date").change(function() {
                    //on récupère la date américaine
                    date= $("#date").val().split("/", 3);
                    mois = date[0];
                    jour = date[1];
                    //et on met la date en français comme valeur du champ
                    $("#date").val(jour + "/" + mois);
                });
            });
        </script> 
    </head>
    <body>
        <?php
        if ($flag_reg) {
            require_once('inc/header_reg.inc.php');
        } else {
            require_once('inc/header_nreg.inc.php');
        }
        ?>
        <div id="contenu">
            <div>
                <form class="recherche" action="liste_depart.php" method="get">
                    <h4>Filtres</h4>
                    <span>Entrez les crit&egrave;res de recherche dans le tableau </span><br/>
                    <label for="destination">Destination du vol:</label><input id="destination" name="destination" /><br/>
                    <label for="date">Date du vol:</label><input id="date" name="date" /><br/>
                    <input type="submit" value="Rechercher" />
                </form>
            </div>
            <?php
                        
                    if (!empty ($_GET['sizeMax'])) {
                        $pageDebut = $_GET['pageDebut'];
                        $sizeMax = $_GET['sizeMax'];
                    } else {
                        $pageDebut = 1;
                        $req_nb_nuplet = 'SELECT COUNT(*) as nbDepart
                                          FROM depart NATURAL JOIN vol
                                          WHERE mois > '. date('n') .' OR (mois = '. date('n') .' AND jour > '. date('j') .')';
                        $sizeMax = pg_query($req_nb_nuplet);
                        $sizeMax = pg_fetch_array($sizeMax);
                        $sizeMax = $sizeMax[0];
                    }
                    
                    $req_departs = 'SELECT depart.num_vol, jour, mois, nb_places_disp, destination, vol_h_depart, vol_h_arrivee, frequence
						FROM depart NATURAL JOIN vol
						WHERE (mois > '. date('n') .' OR (mois = '. date('n') .' AND jour > '. date('j') .'))'
                                                . $ajout_requete .
						'ORDER BY mois ASC, jour ASC 
                                                 LIMIT 10 
                                                 OFFSET 10 * ' . ($pageDebut - 1) . ';';
                
                    $res_departs = pg_query($req_departs);

                    // On créé une variable booleenne pour changer le style en fonction des lignes
                    $flag_pair = TRUE;
                    $ret_departs = pg_fetch_assoc($res_departs);

                    if (!$ret_departs) { ?>
            <h3>Vous vous &ecirc;tes gravement chi&eacute; dessus</h3>
                    <?php } else { ?>
            <table summary="Voici la liste des prochains departs">
                <caption>Voici la liste des prochains d&eacute;parts : </caption>
                <thead>
                    <tr>
                        <th>N° du vol</th>
                        <th>Date</th>
                        <th>Horaires</th>
                        <th>Destination</th>
                        <th>Places disponibles</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        while ($ret_departs) {
                            if ($flag_pair) {
                                echo("\t\t\t\t\t".'<tr class="pair">'."\n");
                            } else {
                                echo("\t\t\t\t\t".'<tr>'."\n");
                            }
                            $flag_pair = inversBool($flag_pair);
                            echo("\t\t\t\t\t\t".'<td>'. $ret_departs['num_vol'] .'</td>'."\n");
                            echo("\t\t\t\t\t\t".'<td>'. strtolower($ret_departs['frequence']) .' '. str_pad($ret_departs['jour'], 2, '0', STR_PAD_LEFT) .'/'. str_pad($ret_departs['mois'], 2, '0', STR_PAD_LEFT) .'</td>'."\n");
                            $details_depart = date_parse($ret_departs['vol_h_depart']);
                            $details_arrivee = date_parse($ret_departs['vol_h_arrivee']);
                            echo("\t\t\t\t\t\t".'<td>Depart : '. str_pad($details_depart['hour'], 2, '0', STR_PAD_LEFT) .':'. str_pad($details_depart['minute'], 2, '0', STR_PAD_LEFT) .'<br />Arrivée : '. str_pad($details_arrivee['hour'], 2, '0', STR_PAD_LEFT) .':'. str_pad($details_arrivee['minute'], 2, '0', STR_PAD_LEFT) .'</td>'."\n");
                            echo("\t\t\t\t\t\t".'<td>'. $ret_departs['destination'] .'</td>'."\n");
                            echo("\t\t\t\t\t\t".'<td>'. $ret_departs['nb_places_disp'] .'</td>'."\n");
                            echo("\t\t\t\t\t\t".'<td><a href="fiche_depart.php?num='. $ret_departs['num_vol'] .'&jour='. $ret_departs['jour'] .'&mois='. $ret_departs['mois'] .'" title="Cliquez ici pour reserver sur ce depart">Réserver</a></td>'."\n");
                            echo("\t\t\t\t\t".'</tr>'."\n");
                            $ret_departs = pg_fetch_assoc($res_departs);
                        }
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th>N° du vol</th>
                        <th>Date</th>
                        <th>Horaires</th>
                        <th>Destination</th>
                        <th>Places disponibles</th>
                        <th>&nbsp;</th>
                    </tr>
                </tfoot>
            </table>
            <div>
                <?php
                     if ((empty($_GET['destination']) && empty($_GET['date'])) && $pageDebut > 1) {
                        echo("<th><a href=\"liste_depart.php?pageDebut=". ($pageDebut -1) .'&sizeMax='. $sizeMax ."\">pr&eacute;c&eacute;dent</a></th>\t");
                     }

                     if ((empty($_GET['destination']) && empty($_GET['date'])) && $sizeMax - $pageDebut * 10 > 0) {
                        echo('<th><a href="liste_depart.php?pageDebut='.($pageDebut + 1) .'&sizeMax='. $sizeMax .'">suivant</a></th>');
                     }
                ?>
            </div>
            <?php } ?>
        </div>
        <?php require_once('inc/footer.inc.php'); ?>
    </body>
</html>
