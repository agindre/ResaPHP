<div id="entete">
	<!--<a href="home.php"><img src="" alt="" title="Retour &aacute; l'accueil" /></a>-->
	<p class="droite">Bienvenue <?php echo($_SESSION['prenom'] .' '. $_SESSION['nom']); ?></p>
	<br style="clear: both" />
</div>
<div id="navigation">
	<ul id="menu">
		<li><a href="home.php">Accueil</a></li>
		<li><a href="liste_depart.php">Liste des vols</a></li>
	</ul>
</div>
