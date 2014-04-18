<?php
// Début du fichier histo.xx.php
// Installer ce fichier dans le dossier /data de votre site webtrees sous le nom histo.xx.php
// Fichier utilisé pour afficher les faits historiques sur une page individuelle
// Chaque ligne est un enregistrement de style GEDCOM décrivant un événement, incluant le caractère de nouvelle ligne (\n)
// Fichier à renommer : histo.xx.php où xx est un code de langue
//
// $Id: histo.demo.php 4028 2008-10-07 15:00:38Z fisharebest $

// Source: http://www.lorand.org/spip.php?article194

if (!defined('WT_WEBTREES')) {
   header('HTTP/1.0 403 Forbidden');
   exit;
}

# Histoire locale : Quercy
#$histo[] = "1 EVEN\n2 TYPE Histoire du Quercy\n2 DATE 1541\n2 NOTE Les actes publics doivent être rédigés en français";
#$histo[] = "1 EVEN\n2 TYPE Histoire du Quercy\n2 DATE 1544\n2 NOTE Décès de Clément Marot, poète et écrivain lotois, à Turin";
#$histo[] = "1 EVEN\n2 TYPE Histoire du Quercy\n2 DATE 1593\n2 NOTE Épidémie de peste en Quercy";

# Famines
$histo[] = "1 EVEN\n2 TYPE Famine\n2 DATE 974\n2 NOTE Grands froids suivis de famine et d'épidémies en France, de 974 à 975 (un tiers de la population française)";
$histo[] = "1 EVEN\n2 TYPE Famine\n2 DATE 975\n2 NOTE Grands froids suivis de famine et d'épidémies en France, de 974 à 975 (un tiers de la population française)";
$histo[] = "1 EVEN\n2 TYPE Famine\n2 DATE 1097\n2 NOTE Famine et peste en France (100 000 morts)";
$histo[] = "1 EVEN\n2 TYPE Famine\n2 DATE 1528\n2 NOTE Famine au Languedoc";
$histo[] = "1 EVEN\n2 TYPE Famine\n2 DATE 1650\n2 NOTE Famine dans l'est de la France de 1650 à 1652";
$histo[] = "1 EVEN\n2 TYPE Famine\n2 DATE 1651\n2 NOTE Famine dans l'est de la France de 1650 à 1652";
$histo[] = "1 EVEN\n2 TYPE Famine\n2 DATE 1652\n2 NOTE Famine dans l'est de la France de 1650 à 1652";
$histo[] = "1 EVEN\n2 TYPE Famine\n2 DATE 1693\n2 NOTE Famine en France de 1693 à 1694 (2 millions de morts)";
$histo[] = "1 EVEN\n2 TYPE Famine\n2 DATE 1694\n2 NOTE Famine en France de 1693 à 1694 (2 millions de morts)";
$histo[] = "1 EVEN\n2 TYPE Famine\n2 DATE 1788\n2 NOTE Famines entrainant la révolution française";

# Révolutions
$histo[] = "1 EVEN\n2 TYPE Révolution\n2 DATE 5 MAY 1789\n2 NOTE Début de la Révolution française";
$histo[] = "1 EVEN\n2 TYPE Révolution\n2 DATE 9 NOV 1799\n2 NOTE Fin de la Révolution française";

# Guerres civiles
$histo[] = "1 EVEN\n2 TYPE Guerre civile\n2 DATE 17 JUL 1936\n2 NOTE Guerre civile d'Espagne (http://fr.wikipedia.org/wiki/Guerre_d%27Espagne)";

# Guerres
$histo[] = "1 EVEN\n2 TYPE Guerre mondiale\n2 DATE 1 AUG 1914\n2 NOTE Début de la Première Guerre Mondiale";
$histo[] = "1 EVEN\n2 TYPE Guerre mondiale\n2 DATE 11 NOV 1918\n2 NOTE Armistice - Fin de la Première Guerre Mondiale";
$histo[] = "1 EVEN\n2 TYPE Guerre mondiale\n2 DATE 3 SEP 1939\n2 NOTE Début de la Seconde Guerre Mondiale";
$histo[] = "1 EVEN\n2 TYPE Guerre mondiale\n2 DATE 8 MAY 1945\n2 NOTE Fin de la Seconde Guerre Mondiale";

# Présidents de France
$histo[] = "1 EVEN\n2 TYPE Président de la République Française\n2 DATE 10 DEC 1848\n2 NOTE Louis-Napoléon Bonaparte (1er président)";
$histo[] = "1 EVEN\n2 TYPE Président de la République Française\n2 DATE 31 AUG 1871\n2 NOTE Adolphe Thiers (2ème président)";
$histo[] = "1 EVEN\n2 TYPE Président de la République Française\n2 DATE 24 MAY 1873\n2 NOTE Patrice de Mac-Mahon (3ème président)";
$histo[] = "1 EVEN\n2 TYPE Président de la République Française\n2 DATE 30 JAN 1879\n2 NOTE Jules Grévy (4ème président)";
$histo[] = "1 EVEN\n2 TYPE Président de la République Française\n2 DATE 3 DEC 1887\n2 NOTE Sadi Carnot (5ème président)";
$histo[] = "1 EVEN\n2 TYPE Président de la République Française\n2 DATE 27 JUN 1894\n2 NOTE Jean Casimir-Perier (6ème président)";
$histo[] = "1 EVEN\n2 TYPE Président de la République Française\n2 DATE 17 JAN 1895\n2 NOTE Félix Faure (7ème président)";
$histo[] = "1 EVEN\n2 TYPE Président de la République Française\n2 DATE 18 FEB 1899\n2 NOTE Emile Loubet (8ème président)";
$histo[] = "1 EVEN\n2 TYPE Président de la République Française\n2 DATE 18 FEB 1906\n2 NOTE Armand Fallières (9ème président)";
$histo[] = "1 EVEN\n2 TYPE Président de la République Française\n2 DATE 18 FEB 1913\n2 NOTE Raymond Poincaré (10ème président)";
$histo[] = "1 EVEN\n2 TYPE Président de la République Française\n2 DATE 18 FEB 1920\n2 NOTE Paul Deschanel (11ème président)";
$histo[] = "1 EVEN\n2 TYPE Président de la République Française\n2 DATE 23 SEP 1920\n2 NOTE Alexandre Millerand (12ème président)";
$histo[] = "1 EVEN\n2 TYPE Président de la République Française\n2 DATE 13 JUN 1924\n2 NOTE Gaston Doumergue (13ème président)";
$histo[] = "1 EVEN\n2 TYPE Président de la République Française\n2 DATE 13 JUN 1931\n2 NOTE Paul Doumer (14ème président)";
$histo[] = "1 EVEN\n2 TYPE Président de la République Française\n2 DATE 10 MAY 1932\n2 NOTE Albert Lebrun (15ème président)";
$histo[] = "1 EVEN\n2 TYPE Président de la République Française\n2 DATE 16 JAN 1947\n2 NOTE Vincent Auriol (16ème président)";
$histo[] = "1 EVEN\n2 TYPE Président de la République Française\n2 DATE 16 JAN 1954\n2 NOTE René Coty (17ème président)";
$histo[] = "1 EVEN\n2 TYPE Président de la République Française\n2 DATE 8 JAN 1959\n2 NOTE Charles de Gaulle (18ème président)";
$histo[] = "1 EVEN\n2 TYPE Président de la République Française\n2 DATE 20 JUN 1969\n2 NOTE Georges Pompidou (19ème président)";
$histo[] = "1 EVEN\n2 TYPE Président de la République Française\n2 DATE 27 MAY 1974\n2 NOTE Valéry Giscard d'Estaing (20ème président)";
$histo[] = "1 EVEN\n2 TYPE Président de la République Française\n2 DATE 21 MAY 1981\n2 NOTE François Mitterrand (21ème président)";
$histo[] = "1 EVEN\n2 TYPE Président de la République Française\n2 DATE 17 MAY 1995\n2 NOTE Jacques Chirac (22ème président)";
$histo[] = "1 EVEN\n2 TYPE Président de la République Française\n2 DATE 16 MAY 2007\n2 NOTE Nicolas Sarkozy (23ème président)";
$histo[] = "1 EVEN\n2 TYPE Président de la République Française\n2 DATE 15 MAY 2012\n2 NOTE François Hollande (24ème président)";

# Rois de France
$histo[] = "1 EVEN\n2 TYPE Roi de France\n2 DATE 3 JUL 987\n2 NOTE Hugues Ier Capet";
$histo[] = "1 EVEN\n2 TYPE Roi de France\n2 DATE 24 OCT 996\n2 NOTE Robert II le Pieux";
$histo[] = "1 EVEN\n2 TYPE Roi de France\n2 DATE 20 JUL 1031\n2 NOTE Henri Ier";
$histo[] = "1 EVEN\n2 TYPE Roi de France\n2 DATE 4 AUG 1060\n2 NOTE Philippe Ier";
$histo[] = "1 EVEN\n2 TYPE Roi de France\n2 DATE 22 JUL 1108\n2 NOTE Louis VI le Gros";
$histo[] = "1 EVEN\n2 TYPE Roi de France\n2 DATE 1 AUG 1137\n2 NOTE Louis VII le Jeune";
$histo[] = "1 EVEN\n2 TYPE Roi de France\n2 DATE 18 SEP 1180\n2 NOTE Philippe II Auguste";
$histo[] = "1 EVEN\n2 TYPE Roi de France\n2 DATE 14 JUL 1223\n2 NOTE Louis VIII le Lion";
$histo[] = "1 EVEN\n2 TYPE Roi de France\n2 DATE 8 NOV 1226\n2 NOTE Louis IX Saint Louis";
$histo[] = "1 EVEN\n2 TYPE Roi de France\n2 DATE 25 AUG 1270\n2 NOTE Philippe III le Hardi";
$histo[] = "1 EVEN\n2 TYPE Roi de France\n2 DATE 5 OCT 1285\n2 NOTE Philippe IV le Bel";
$histo[] = "1 EVEN\n2 TYPE Roi de France\n2 DATE 29 NOV 1314\n2 NOTE Louis X le Hutin";
$histo[] = "1 EVEN\n2 TYPE Roi de France\n2 DATE 5 JUN 1315\n2 NOTE Jean Ier le Posthume (né le 15 novembre 1316)";
$histo[] = "1 EVEN\n2 TYPE Roi de France\n2 DATE 19 NOV 1316\n2 NOTE Philippe V le Long";
$histo[] = "1 EVEN\n2 TYPE Roi de France\n2 DATE 3 JAN 1322\n2 NOTE Charles IV le Bel";
$histo[] = "1 EVEN\n2 TYPE Roi de France\n2 DATE 1 FEB 1328\n2 NOTE Philippe VI de Valois";
$histo[] = "1 EVEN\n2 TYPE Roi de France\n2 DATE 22 AUG 1350\n2 NOTE Jean II le Bon";
$histo[] = "1 EVEN\n2 TYPE Roi de France\n2 DATE 8 APR 1364\n2 NOTE Charles V le Sage";
$histo[] = "1 EVEN\n2 TYPE Roi de France\n2 DATE 16 SEP 1380\n2 NOTE Charles VI le Fol";
$histo[] = "1 EVEN\n2 TYPE Roi de France\n2 DATE 21 OCT 1422\n2 NOTE Charles VII le Victorieux";
$histo[] = "1 EVEN\n2 TYPE Roi de France\n2 DATE 22 JUL 1461\n2 NOTE Louis XI le Prudent";
$histo[] = "1 EVEN\n2 TYPE Roi de France\n2 DATE 30 AUG 1483\n2 NOTE Charles VIII l'Affable";
$histo[] = "1 EVEN\n2 TYPE Roi de France\n2 DATE 7 APR 1498\n2 NOTE Louis XII le Père du Peuple";
$histo[] = "1 EVEN\n2 TYPE Roi de France\n2 DATE 1 JAN 1515\n2 NOTE François Ier";
$histo[] = "1 EVEN\n2 TYPE Roi de France\n2 DATE 1 MAR 1547\n2 NOTE Henri II";
$histo[] = "1 EVEN\n2 TYPE Roi de France\n2 DATE 10 JUL 1559\n2 NOTE François II";
$histo[] = "1 EVEN\n2 TYPE Roi de France\n2 DATE 5 DEC 1560\n2 NOTE Charles IX";
$histo[] = "1 EVEN\n2 TYPE Roi de France\n2 DATE 30 MAY 1574\n2 NOTE Henri III";
$histo[] = "1 EVEN\n2 TYPE Roi de France\n2 DATE 1 AUG 1589\n2 NOTE Henri IV le Grand";
$histo[] = "1 EVEN\n2 TYPE Roi de France\n2 DATE 14 MAY 1610\n2 NOTE Louis XIII le Juste";
$histo[] = "1 EVEN\n2 TYPE Roi de France\n2 DATE 14 MAY 1643\n2 NOTE Louis XIV le Grand";
$histo[] = "1 EVEN\n2 TYPE Roi de France\n2 DATE 1 SEP 1715\n2 NOTE Louis XV le Bien-Aimé";
$histo[] = "1 EVEN\n2 TYPE Roi de France\n2 DATE 10 MAY 1774\n2 NOTE Louis XVI";
$histo[] = "1 EVEN\n2 TYPE Roi de France\n2 DATE 6 APR 1814\n2 NOTE Louis XVIII";
$histo[] = "1 EVEN\n2 TYPE Roi de France\n2 DATE 22 JUN 1815\n2 NOTE Louis XVIII";
$histo[] = "1 EVEN\n2 TYPE Roi de France\n2 DATE 16 SEP 1824\n2 NOTE Charles X";
$histo[] = "1 EVEN\n2 TYPE Roi des Français\n2 DATE 9 AUG 1830\n2 NOTE Louis-Philippe Ier (dernier roi de France)";

# Empereurs de France
$histo[] = "1 EVEN\n2 TYPE Empereur des Français\n2 DATE 18 MAY 1804\n2 NOTE Napoléon Ier";
$histo[] = "1 EVEN\n2 TYPE Empereur des Français\n2 DATE 4 DEC 1852\n2 NOTE Napoléon III";

# Régimes politiques
$histo[] = "1 EVEN\n2 TYPE Régime politique\n2 DATE 4 SEP 1791\n2 NOTE Monarchie Constitutionnelle";
$histo[] = "1 EVEN\n2 TYPE Régime politique\n2 DATE 22 SEP 1792\n2 NOTE Ire République";
$histo[] = "1 EVEN\n2 TYPE Régime politique\n2 DATE 26 OCT 1795\n2 NOTE Directoire";
$histo[] = "1 EVEN\n2 TYPE Régime politique\n2 DATE 9 NOV 1799\n2 NOTE Consulat";
$histo[] = "1 EVEN\n2 TYPE Régime politique\n2 DATE 22 MAR 1815\n2 NOTE Cent-Jours";
$histo[] = "1 EVEN\n2 TYPE Régime politique\n2 DATE 25 FEB 1848\n2 NOTE Gouvernement Provisoire de 1848";
$histo[] = "1 EVEN\n2 TYPE Régime politique\n2 DATE 4 NOV 1848\n2 NOTE IIe République";
$histo[] = "1 EVEN\n2 TYPE Régime politique\n2 DATE 4 SEP 1870\n2 NOTE IIIe République";
$histo[] = "1 EVEN\n2 TYPE Régime politique\n2 DATE 11 JUL 1940\n2 NOTE Gouvernement de Vichy";
$histo[] = "1 EVEN\n2 TYPE Régime politique\n2 DATE 2 JUN 1944\n2 NOTE Gouvernement Provisoire de la République Française";
$histo[] = "1 EVEN\n2 TYPE Régime politique\n2 DATE 24 OCT 1946\n2 NOTE IVe République";
$histo[] = "1 EVEN\n2 TYPE Régime politique\n2 DATE 5 OCT 1958\n2 NOTE Ve République";

# Événements politiques
$histo[] = "1 EVEN\n2 TYPE Parti Communiste Français\n2 DATE 25 DEC 1920 Congrès de Tours\n2 NOTE Naissance du Parti Communiste Français";
$histo[] = "1 EVEN\n2 TYPE Front populaire\n2 DATE 03 MAY 1936\n2 NOTE Victoire du Peuple (http://fr.wikipedia.org/wiki/Front_populaire_%28France%29)";

# Religieux
$histo[] = "1 EVEN\n2 TYPE Séparation des Églises et de l'État Française\n2 DATE 09 DEC 1905\n2 NOTE Loi du 9 décembre 1905 relative à la séparation des Églises et de l'État";

?>
