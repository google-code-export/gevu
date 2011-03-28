SELECT *
from  gevutrouville1.spip_forms_donnees_articles fda
inner join gevutrouville1.spip_forms_donnees_champs fdc ON fdc.id_donnee = fda.id_donnee
where id_article = 4705;