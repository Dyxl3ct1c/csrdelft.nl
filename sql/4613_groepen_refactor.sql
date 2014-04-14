CREATE TABLE commissies (id int(11) NOT NULL AUTO_INCREMENT, categorie_id int(11) NOT NULL, naam varchar(255) NOT NULL, samenvatting text NOT NULL, omschrijving text NOT NULL, rechten_bekijken varchar(255) NOT NULL, rechten_aanmelden varchar(255) NOT NULL, rechten_beheren varchar(255) NOT NULL, eigenaar_lid_id varchar(4) NOT NULL, website varchar(255) NOT NULL, familie_id varchar(255) NOT NULL, begin_moment datetime DEFAULT NULL, eind_moment datetime DEFAULT NULL, status varchar(4) NOT NULL, PRIMARY KEY (id)) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
CREATE TABLE besturen (id int(11) NOT NULL AUTO_INCREMENT, categorie_id int(11) NOT NULL, naam varchar(255) NOT NULL, samenvatting text NOT NULL, omschrijving text NOT NULL, rechten_bekijken varchar(255) NOT NULL, rechten_aanmelden varchar(255) NOT NULL, rechten_beheren varchar(255) NOT NULL, eigenaar_lid_id varchar(4) NOT NULL, website varchar(255) NOT NULL, familie_id varchar(255) NOT NULL, begin_moment datetime DEFAULT NULL, eind_moment datetime DEFAULT NULL, status varchar(4) NOT NULL, PRIMARY KEY (id)) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
CREATE TABLE sjaarcies (id int(11) NOT NULL AUTO_INCREMENT, categorie_id int(11) NOT NULL, naam varchar(255) NOT NULL, samenvatting text NOT NULL, omschrijving text NOT NULL, rechten_bekijken varchar(255) NOT NULL, rechten_aanmelden varchar(255) NOT NULL, rechten_beheren varchar(255) NOT NULL, eigenaar_lid_id varchar(4) NOT NULL, website varchar(255) NOT NULL, familie_id varchar(255) NOT NULL, begin_moment datetime DEFAULT NULL, eind_moment datetime DEFAULT NULL, status varchar(4) NOT NULL, PRIMARY KEY (id)) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
CREATE TABLE woonoorden (id int(11) NOT NULL AUTO_INCREMENT, categorie_id int(11) NOT NULL, naam varchar(255) NOT NULL, samenvatting text NOT NULL, omschrijving text NOT NULL, rechten_bekijken varchar(255) NOT NULL, rechten_aanmelden varchar(255) NOT NULL, rechten_beheren varchar(255) NOT NULL, eigenaar_lid_id varchar(4) NOT NULL, website varchar(255) NOT NULL, status_historie text DEFAULT NULL, huis_status varchar(255) DEFAULT NULL, PRIMARY KEY (id)) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
CREATE TABLE werkgroepen (id int(11) NOT NULL AUTO_INCREMENT, categorie_id int(11) NOT NULL, naam varchar(255) NOT NULL, samenvatting text NOT NULL, omschrijving text NOT NULL, rechten_bekijken varchar(255) NOT NULL, rechten_aanmelden varchar(255) NOT NULL, rechten_beheren varchar(255) NOT NULL, eigenaar_lid_id varchar(4) NOT NULL, website varchar(255) NOT NULL, familie_id varchar(255) NOT NULL, begin_moment datetime DEFAULT NULL, eind_moment datetime DEFAULT NULL, status varchar(4) NOT NULL, PRIMARY KEY (id)) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
CREATE TABLE onderverenigingen (id int(11) NOT NULL AUTO_INCREMENT, categorie_id int(11) NOT NULL, naam varchar(255) NOT NULL, samenvatting text NOT NULL, omschrijving text NOT NULL, rechten_bekijken varchar(255) NOT NULL, rechten_aanmelden varchar(255) NOT NULL, rechten_beheren varchar(255) NOT NULL, eigenaar_lid_id varchar(4) NOT NULL, website varchar(255) NOT NULL, PRIMARY KEY (id)) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
CREATE TABLE ketzers (id int(11) NOT NULL AUTO_INCREMENT, categorie_id int(11) NOT NULL, naam varchar(255) NOT NULL, samenvatting text NOT NULL, omschrijving text NOT NULL, rechten_bekijken varchar(255) NOT NULL, rechten_aanmelden varchar(255) NOT NULL, rechten_beheren varchar(255) NOT NULL, eigenaar_lid_id varchar(4) NOT NULL, website varchar(255) NOT NULL, familie_id varchar(255) NOT NULL, begin_moment datetime DEFAULT NULL, eind_moment datetime DEFAULT NULL, status varchar(4) NOT NULL, aanmeld_limiet int(11) DEFAULT NULL, aanmelden_vanaf datetime NOT NULL, aanmelden_tot datetime NOT NULL, PRIMARY KEY (id)) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
CREATE TABLE activiteiten (id int(11) NOT NULL AUTO_INCREMENT, categorie_id int(11) NOT NULL, naam varchar(255) NOT NULL, samenvatting text NOT NULL, omschrijving text NOT NULL, rechten_bekijken varchar(255) NOT NULL, rechten_aanmelden varchar(255) NOT NULL, rechten_beheren varchar(255) NOT NULL, eigenaar_lid_id varchar(4) NOT NULL, website varchar(255) NOT NULL, familie_id varchar(255) NOT NULL, begin_moment datetime DEFAULT NULL, eind_moment datetime DEFAULT NULL, status varchar(4) NOT NULL, aanmeld_limiet int(11) DEFAULT NULL, aanmelden_vanaf datetime NOT NULL, aanmelden_tot datetime NOT NULL, PRIMARY KEY (id)) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
CREATE TABLE conferenties (id int(11) NOT NULL AUTO_INCREMENT, categorie_id int(11) NOT NULL, naam varchar(255) NOT NULL, samenvatting text NOT NULL, omschrijving text NOT NULL, rechten_bekijken varchar(255) NOT NULL, rechten_aanmelden varchar(255) NOT NULL, rechten_beheren varchar(255) NOT NULL, eigenaar_lid_id varchar(4) NOT NULL, website varchar(255) NOT NULL, familie_id varchar(255) NOT NULL, begin_moment datetime DEFAULT NULL, eind_moment datetime DEFAULT NULL, status varchar(4) NOT NULL, aanmeld_limiet int(11) DEFAULT NULL, aanmelden_vanaf datetime NOT NULL, aanmelden_tot datetime NOT NULL, PRIMARY KEY (id)) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;