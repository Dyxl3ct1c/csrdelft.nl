<?php

require_once 'MVC/model/entity/Foto.class.php';

/**
 * FotoAlbumModel.class.php
 * 
 * @author P.W.G. Brussee <brussee@live.nl>
 * 
 */
class FotoAlbumModel {

	public static function getFotoAlbum(Map $parent, $naam) {
		return new FotoAlbum($parent, $naam);
	}

	public static function verwerkFotos(FotoAlbum $album) {
		if (!file_exists($album->locatie . '_thumbs')) {
			mkdir($album->locatie . '_thumbs');
			chmod($album->locatie . '_thumbs', 0755);
		}
		if (!file_exists($album->locatie . '_resized')) {
			mkdir($album->locatie . '_resized');
			chmod($album->locatie . '_resized', 0755);
		}
		foreach ($album->getFotos(true) as $foto) {
			if (!$foto->bestaatThumb()) {
				$foto->maakThumb();
			}
			if (!$foto->bestaatResized()) {
				$foto->maakResized();
			}
		}
	}

	public static function getMostRecentFotoAlbum() {
		$map = new Map();
		$map->locatie = PICS_PATH . '/';
		$album = FotoAlbumModel::getFotoAlbum($map, 'fotoalbum');
		return $album->getMostRecentSubAlbum();
	}

	public static function verwijderFoto(Foto $foto) {
		$ret = true;
		$ret &= unlink($foto->map->locatie . $foto->bestandsnaam);
		$ret &= unlink($foto->getResizedPad());
		$ret &= unlink($foto->getThumbPad());
		return $ret;
	}

	public static function hernoemAlbum(FotoAlbum $album, $nieuwenaam) {
		if (!preg_match('/^(?:[a-z0-9_-]|\.(?!\.))+$/iD', $nieuwenaam)) {
			throw new Exception('Ongeldige naam');
		}
		return rename($album->locatie, str_replace($album->mapnaam, $nieuwenaam, $album->locatie));
	}

	public static function setAlbumCover(FotoAlbum $album, Foto $cover) {
		$ret = true;
		// find old cover
		foreach ($album->getFotos() as $foto) {
			if (strpos($foto->bestandsnaam, 'folder')) {
				$old = $foto->getPad();
				$ret &= rename($old, str_replace('folder', '', $old));
				$old = $foto->getResizedPad();
				$ret &= rename($old, str_replace('folder', '', $old));
				$old = $foto->getThumbPad();
				$ret &= rename($old, str_replace('folder', '', $old));
			}
		}
		// set new cover
		$old = $cover->getPad();
		$ret &= rename($old, substr_replace($old, 'folder', strrpos($old, '.'), 0));
		$old = $cover->getResizedPad();
		$ret &= rename($old, substr_replace($old, 'folder', strrpos($old, '.'), 0));
		$old = $cover->getThumbPad();
		$ret &= rename($old, substr_replace($old, 'folder', strrpos($old, '.'), 0));
		return $ret;
	}

}
