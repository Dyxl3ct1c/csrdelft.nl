<?php

use CsrDelft\Orm\PersistenceModel;

class ArchiefMaaltijdModel extends PersistenceModel {
    const ORM = 'ArchiefMaaltijd';
    const DIR = 'maalcie/';

    protected static $instance;

    protected $default_order = 'datum DESC, tijd DESC';

    public function getArchiefMaaltijdenTussen($van = null, $tot = null) {
        if ($van === null) { // RSS
            $van = 0;
        } elseif (!is_int($van)) {
            throw new Exception('Invalid timestamp: $van getArchiefMaaltijden()');
        }
        if ($tot === null) { // RSS
            $tot = time();
        } elseif (!is_int($tot)) {
            throw new Exception('Invalid timestamp: $tot getArchiefMaaltijden()');
        }
        return $this->find('datum >= ? AND datum <= ?', array(date('Y-m-d', $van), date('Y-m-d', $tot)));
    }

    public function vanMaaltijd(Maaltijd $maaltijd) {
        $archief = new ArchiefMaaltijd();
        $archief->maaltijd_id = $maaltijd->maaltijd_id;
        $archief->titel = $maaltijd->titel;
        $archief->datum = $maaltijd->datum;
        $archief->tijd = $maaltijd->tijd;
        $archief->prijs = $maaltijd->prijs;
        $archief->aanmeldingen = '';
        foreach (MaaltijdAanmeldingenModel::instance()->getAanmeldingenVoorMaaltijd($maaltijd) as $aanmelding) {
            if ($aanmelding->uid === '') {
                $archief->aanmeldingen .= 'gast';
            } else {
                $archief->aanmeldingen .= $aanmelding->uid;
            }
            if ($aanmelding->door_abonnement) {
                $archief->aanmeldingen .= '_abo';
            }
            if ($aanmelding->door_uid !== null) {
                $archief->aanmeldingen .= '_' . $aanmelding->door_uid;
            }
            $archief->aanmeldingen .= ',';
        }

        return $archief;
    }
}