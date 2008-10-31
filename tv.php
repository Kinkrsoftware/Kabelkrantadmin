<?php
  require_once('functions.php');
?>
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" >
  <head>
    <title><?php echo OWNER; ?></title>
    <link rel="stylesheet" href="toevoegen.css" type="text/css" />
  </head>
  <body>
    <form method="post">
      <fieldset>
        <legend>TV Geluid (Stream)</legend>
	<fieldset>
	  <legend>URI</legend>
	  <input type="text" name="stream-uri" />
	</fieldset>
        <fieldset class="buttons">
	  <legend>Acties</legend>
          <input type="submit" name="action-stream" value="Start" />
          <input type="submit" name="action-stream" value="Stop" />
          <input type="submit" name="action-stream" value="Herstart" />
        </fieldset>
      </fieldset>
      <fieldset>
        <legend>TV Uitzendingen</legend>
	<fieldset>
	  <legend>Planning</legend>
	</fieldset>
      </fieldset>
      <fieldset>
        <legend>Schijven</legend>
	<fieldset>
	  <legend>Overzicht</legend>
	  <table>
	    <tr><th>Mountpunt</th><th>Gebruikt</th><th>Vrije ruimte</th></tr>
	    <tr><td>/mnt/media</td><td></td><td></td></tr>
	  </table>
	</fieldset>
	<fieldset class="buttons">
	  <legend>Acties (/mnt/media)</legend>
          <input type="submit" name="action-umount" value="Ontkoppel" />
          <input type="submit" name="action-mount" value="Koppel" />
        </fieldset>
	<fieldset>
	  <legend>Opmerkingen</legend>
	  Ontkoppel een schijf voordat hij uit de PC wordt gehaald, danwel
	  de USB/Firewire kabel wordt verwijderd.
	</fieldset>
      </fieldset>

      <fieldset>
        <legend>Playlists</legend>
	<fieldset>
	  <legend>Bestandsnaam</legend>
	  <input type="text" name="playlist-name" />
	</fieldset>
	<fieldset class="buttons">
	  <legend>Acties</legend>
	  <input type="submit" name="action-playlist" value="Openen" />
	  <input type="submit" name="action-playlist" value="Opslaan" />
	  <input type="submit" name="action-playlist" value="Nieuw" />
        </fieldset>
	<fieldset>
	  <legend>Bestanden in playlist</legend>
	  <textarea></textarea>
	</fieldset>
      </fieldset>
      <fieldset>
        <legend>Radiokijken</legend>
	<fieldset>
	  <legend>Vormgeving</legend>
	  <select></select>
	</fieldset>
	<fieldset class="buttons">
	  <legend>Acties</legend>
          <input type="submit" name="action-rtv" value="Start" />
          <input type="submit" name="action-rtv" value="Stop" />
          <input type="submit" name="action-rtv" value="Herstart" />
	</fieldset>
      </fieldset>
      <fieldset>
        <legend>Noodsysteem</legend>
	<fieldset class="buttons">
	  <legend>Acties</legend>
          <input type="submit" name="action-eb" value="Activeer" />
          <input type="submit" name="action-eb" value="Deactiveer" />
	</fieldset>
	<fieldset>
	  <legend>Opmerkingen</legend>
	 Op misbruik van de bovenstaande twee knoppen staat volledige
	 ontzegging van dit systeem. Om het systeem te kunnen gebruiken
	 moet je eerst een pagina hebben gemaakt met het 'Nood'-sjabloon.
	 Andere sjablonen worden <b>niet</b> weergegeven.
	 Mocht het nodig zijn, houd je hoofd koel en breng jezelf en
	 anderen niet in gevaar.
        </fieldset>
      </fieldset>
    </form>
  </body>
</html>
