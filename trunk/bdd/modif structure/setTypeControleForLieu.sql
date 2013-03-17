-- mise à jour des type de contrôle
//
update gevu_lieux
set id_type_controle = 44
where id_lieu in (select id_lieu FROM gevu_etablissements);
update gevu_lieux
set id_type_controle = 45
where id_lieu in (select id_lieu FROM gevu_batiments);
update gevu_lieux
set id_type_controle = 46
where id_lieu in (select id_lieu FROM gevu_niveaux);
update gevu_lieux
set id_type_controle = 50
where id_lieu in (select id_lieu FROM gevu_espacesxinterieurs);
update gevu_lieux
set id_type_controle = 58
where id_lieu in (select id_lieu FROM gevu_objetsxinterieurs);
update gevu_lieux
set id_type_controle = 51
where id_lieu in (select id_lieu FROM gevu_parcelles);
update gevu_lieux
set id_type_controle = 49
where id_lieu in (select id_lieu FROM gevu_espacesxexterieurs);
update gevu_lieux
set id_type_controle = 59
where id_lieu in (select id_lieu FROM gevu_objetsxvoiries);
update gevu_lieux
set id_type_controle = 47
where id_lieu in (select id_lieu FROM gevu_objetsxexterieurs);


-- mise à jour des réponses
update gevu_etablissements set reponse_4 = 1 where reponse_4 = "select_1_1";
update gevu_etablissements set reponse_4 = 2 where reponse_4 = "select_1_2";
update gevu_etablissements set reponse_5 = 1 where reponse_5 = "select_2_1";
update gevu_etablissements set reponse_5 = 2 where reponse_5 = "select_2_2";

-- mise à jour des contacts
update gevu_batiments set contact_proprietaire = null, contact_delegataire=null, contact_gardien =null;
ALTER TABLE  `gevu_etablissements` CHANGE  `contact_proprietaire`  `contact_proprietaire` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL ,
CHANGE  `contact_delegataire`  `contact_delegataire` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL;
update gevu_etablissements set contact_proprietaire = null, contact_delegataire=null;
//



TRUNCATE gevu_diagnosticsxvoirie;

