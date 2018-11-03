<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2018 Corrigraphie
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

use Symfony\Component\HttpFoundation\Response;
use Zco\Bundle\ContentBundle\Domain\TagRepository;
use Zco\Bundle\CoreBundle\Paginator\Paginator;
use Zco\Bundle\UserBundle\Domain\UserDAO;

/**
 * Gestion des dictées.
 *
 * @author mwsaz@zcorrecteurs.fr
 */

require_once(__DIR__ . '/statistiques-accueil.php');

function TaggerDictee(Dictee $Dictee, $tags)
{
    if (!is_array($tags)) {
        $tags = explode(',', $tags);
    }
    $repository = TagRepository::instance();
    foreach ($tags as $tag) {
        $tag = trim($tag);
        if (!$tag) {
            continue;
        }

        $id = $repository->create(['nom' => $tag]);

        $Dt = new DicteeTag();
        $Dt->dictee_id = $Dictee->id;
        $Dt->tag_id = $id;
        $Dt->replace();
    }
}

/**
 * Ajoute une dictée.
 *
 * @param AjouterForm $Form Formulaire d'ajout de la dictée.
 * @return bool | int        ID de la dictée créée, false si l'envoi du fichier a échoué.
 */
function AjouterDictee(AjouterForm &$form)
{
    $Dictee = new Dictee;
    $data = $form->getCleanedData();

    if (verifier('dictees_publier') && $data['publique']) {
        $Dictee->etat = DICTEE_VALIDEE;
        Container::cache()->delete('dictees_accueil');
    } else    $Dictee->etat = DICTEE_BROUILLON;

    $tags = $data['tags'];

    unset($data['publique'], $data['lecture_rapide'], $data['lecture_lente'],
        $data['MAX_FILE_SIZE'], $data['tags'], $data['icone']);

    foreach ($data as $k => &$v)
        $Dictee->$k = $v;

    $Dictee->utilisateur_id = $_SESSION['id'];
    $Dictee->creation = new Doctrine_Expression('CURRENT_TIMESTAMP');
    $Dictee->save();

    // Ajout des tags
    TaggerDictee($Dictee, $tags);

    foreach (array('lecture_rapide', 'lecture_lente') as $l)
        if (isset($_FILES[$l]) && $_FILES[$l]['error'] != 4) {
            $r = DicteeEnvoyerSon($Dictee, $l);
            if (!$r || $r instanceof Response)
                return $r;
        }

    // Traitement de l'icône
    if (isset($_FILES['icone']) && $_FILES['icone']['error'] != 4) {
        $ext = strtolower(strrchr($_FILES['icone']['name'], '.'));
        $nom = $Dictee->id . $ext;
        $chemin = BASEPATH . '/web/uploads/dictees';

        if (!File_Upload::Fichier($_FILES['icone'], $chemin, $nom, File_Upload::FILE | File_Upload::IMAGE))
            return redirect(
                'Une erreur est survenue lors de l\'envoi de l\'icône : le format est peut-être invalide.',
                'editer-' . $Dictee->id . '-' . rewrite($Dictee->titre) . '.html',
                MSG_ERROR
            );

        $Dictee->icone = '/uploads/dictees/' . $nom;
    }

    $Dictee->save();
    DicteesEffacerCache();
    return $Dictee->id;
}

/**
 * Modifie une dictée.
 *
 * @param Dictee $Dictee Dictée.
 * @param AjouterForm $Form Formulaire d'édition de la dictée.
 * @return bool                False si l'envoi du fichier a échoué.
 */
function EditerDictee(Dictee $Dictee, AjouterForm &$Form)
{
    $data = $Form->getCleanedData();
    $etat = $Dictee->etat;

    if (verifier('dictees_publier')) {
        if ($data['publique'])
            $Dictee->etat = DICTEE_VALIDEE;
        elseif ($Dictee->etat != DICTEE_PROPOSEE)
            $Dictee->etat = DICTEE_BROUILLON;
        if ($Dictee->etat != $etat)
            Container::cache()->delete('dictees_accueil');
    }

    // Tags
    Doctrine_Query::create()
        ->delete()
        ->from('DicteeTag dt')
        ->where('dt.dictee_id = ?', $Dictee->id)
        ->execute();
    TaggerDictee($Dictee, $data['tags']);

    unset($data['publique'], $data['lecture_rapide'], $data['lecture_lente'],
        $data['MAX_FILE_SIZE'], $data['tags'], $data['icone']);
    foreach ($data as $k => $v) {
        $Dictee->$k = $v;
    }

    $Dictee->edition = new Doctrine_Expression('CURRENT_TIMESTAMP');

    foreach (array('lecture_rapide', 'lecture_lente') as $l)
        if (isset($_FILES[$l]) && $_FILES[$l]['error'] != 4) {
            $r = DicteeEnvoyerSon($Dictee, $l);
            if (!$r || $r instanceof Response)
                return $r;
        }

    // Edition de l'icône
    if (isset($_FILES['icone']) && $_FILES['icone']['error'] != 4) {
        $ext = strtolower(strrchr($_FILES['icone']['name'], '.'));
        $nom = $Dictee->id . $ext;
        $chemin = BASEPATH . '/web/uploads/dictees';

        if ($Dictee->icone && (strrchr($Dictee->icone, '.') != $ext))
            @unlink(BASEPATH . '/web' . $Dictee->icone);


        if (!File_Upload::Fichier($_FILES['icone'], $chemin, $nom, File_Upload::FILE | File_Upload::IMAGE))
            return redirect(
                'Une erreur est survenue lors de l\'envoi de l\'icône : le format est peut-être invalide.',
                'editer-' . $Dictee->id . '-' . rewrite($Dictee->titre) . '.html',
                MSG_ERROR
            );

        $Dictee->icone = '/uploads/dictees/' . $nom;
    }


    $Dictee->save();
    DicteesEffacerCache();
    return true;
}

/**
 * Supprime une dictée.
 *
 * @param Dictee $Dictee Dictée.
 */
function SupprimerDictee(Dictee $Dictee)
{
    @unlink(BASEPATH . '/web/uploads/dictees/' . DicteeSon($Dictee, 'lecture_rapide'));
    @unlink(BASEPATH . '/web/uploads/dictees/' . DicteeSon($Dictee, 'lecture_lente'));
    Doctrine_Query::create()
        ->delete('Dictee_Participation')
        ->where('dictee_id = ?', $Dictee->id)
        ->execute();
    DicteesEffacerCache();
    return $Dictee->delete();
}


/**
 * (dé)Valide une dictée.
 *
 * @param Dictee $Dictee Dictée.
 * @param bool $valide Nouvel état.
 */
function ValiderDictee(Dictee $Dictee, $valide)
{
    $Dictee->etat = ($valide ? DICTEE_VALIDEE : DICTEE_BROUILLON);
    $valide && $Dictee->validation = new Doctrine_Expression('CURRENT_TIMESTAMP');
    $Dictee->save();

    DicteesEffacerCache();
    \Container::get('zco.admin')->get('dictees', true);
}

/**
 * Approuve / Refuse une proposition et envoie un MP à l'auteur.
 *
 * @param Dictee $Dictee Dictée.
 * @param ReponseForm $Form Formulaire de réponse.
 */
function RepondreDictee(Dictee $Dictee, RepondreForm &$Form)
{
    $data = $Form->getCleanedData();
    if ($data['accepter']) {
        $Dictee->validation = new Doctrine_Expression('CURRENT_TIMESTAMP');
        $Dictee->etat = DICTEE_VALIDEE;
        $mp = 'dictee_acceptee';
        $titre = 'Votre dictée a été acceptée';
        Container::cache()->delete('dictees_accueil');
    } else {
        $Dictee->etat = DICTEE_BROUILLON;
        $mp = 'dictee_refusee';
        $titre = 'Votre dictée a été refusée';
    }

    $message = render_to_string('ZcoDicteesBundle:Mp:' . $mp . '.html.php', array(
        'id' => $_SESSION['id'],
        'pseudo' => $_SESSION['pseudo'],
        'url' => '/dictees/dictee-' . $Dictee->id . '-' . rewrite($Dictee->titre) . '.html',
        'texte' => $data['commentaire'],
    ));

    UserDAO::AjouterMPAuto($titre,
        $Dictee->titre,
        $Dictee->utilisateur_id,
        $message);
    $Dictee->save();

    DicteesEffacerCache();
    return $data['accepter'];
}

/**
 * Propose une dictée.
 *
 * @param Dictee $Dictee Dictée.
 */
function ProposerDictee(Dictee $Dictee)
{
    $Dictee->etat = DICTEE_PROPOSEE;
    $Dictee->save();
    \Container::get('zco.admin')->get('dictees', true);
}


function DicteesEffacerCache()
{
    foreach (array('accueil', 'statistiques', 'plusJouees') as $c)
        Container::cache()->delete('dictees_' . $c);
}

/**
 * Récupère une dictée par son id.
 *
 * @param int $id ID de la dictée à récupérer.
 * @return Dictee        Dictée.
 */
function Dictee($id)
{
    $Dictee = Doctrine_Query::create()
        ->select('d.*, u.id, u.pseudo')
        ->from('Dictee d')
        ->leftJoin('d.Utilisateur u')
        ->where('d.id = ?', $id)
        ->execute();
    $Dictee = $Dictee ? $Dictee[0] : null;

    if (!$Dictee || !DicteeDroit($Dictee, 'voir'))
        return null;
    return $Dictee;
}

/**
 * Liste les dictées.
 *
 * @param $tri string Colonne selon laquelle trier les dictées.
 * @return Doctrine_Collection    Les dictées
 */
function ListerDictees($page, $tri = null)
{
    $query = Doctrine_Query::create()
        ->select('d.*, u.id, u.pseudo')
        ->from('Dictee d')
        ->leftJoin('d.Utilisateur u')
        ->where('d.etat = ?', DICTEE_VALIDEE);

    $tri = $tri ?: '-edition';
    $ordre = 'ASC';
    if ($tri[0] == '-') {
        $ordre = 'DESC';
        $tri = substr($tri, 1);
    }

    $triable = array('difficulte', 'participations', 'temps_estime',
        'note', 'titre', 'creation');
    if (in_array($tri, $triable))
        $query->orderBy('d.' . $tri . ' ' . $ordre);

    return new Paginator($query, 30);
}


/**
 * Cherche les dictées en fonction d'un titre donné.
 *
 * @param  string $name
 * @return array    Les dictées trouvées
 */
function searchDictees($name)
{
    $query = Doctrine_Query::create()
        ->select('d.*')
        ->from('Dictee d')
        ->where('d.titre LIKE ?', '%' . $name . '%');

    return $query->fetchArray();
}

/**
 * Liste les dictées proposées
 *
 * @return Doctrine_Collection    Les dictées
 */
function DicteesProposees()
{
    return Doctrine_Query::create()
        ->from('Dictee d')
        ->leftJoin('d.Utilisateur u')
        ->where('d.etat = ?', DICTEE_PROPOSEE)
        ->orderBy('d.edition ASC')
        ->execute();
}

/**
 * Liste les dictées d'un utilisateur
 *
 * @return Doctrine_Collection    Les dictées
 */
function DicteesUtilisateur()
{
    return Doctrine_Query::create()
        ->from('Dictee')
        ->addWhere('utilisateur_id = ?', $_SESSION['id'])
        ->orderBy('etat ASC, edition DESC')
        ->execute();
}

/**
 * Évite la redondance pour les vérifications de droit un peu compliquées.
 *
 * @param Dictee $Dictee Dictee.
 * @param string $droit Droit à tester.
 * @return bool                L'utilisateur peut / ne peut pas.
 */
function DicteeDroit(Dictee $Dictee, $droit)
{
    if ($droit === 'voir')
        return $Dictee->etat == DICTEE_VALIDEE ||
            $Dictee->utilisateur_id == $_SESSION['id'] ||
            verifier('dictees_voir_toutes');

    if ($droit === 'editer')
        return (verifier('dictees_publier') ||
                $Dictee->etat == DICTEE_BROUILLON
            ) && (($Dictee->utilisateur_id == $_SESSION['id'] &&
                    verifier('dictees_editer')
                ) || verifier('dictees_editer_toutes')
            );
    if ($droit === 'supprimer')
        return (verifier('dictees_publier') ||
                $Dictee->etat == DICTEE_BROUILLON
            ) && ($Dictee->utilisateur_id == $_SESSION['id'] ||
                verifier('dictees_supprimer_toutes')
            );
}

/**
 * Envoie un fichier audio.
 *
 * @param Dictee $Dictee Dictee.
 */
function DicteeEnvoyerSon(Dictee $Dictee, $field = false)
{
    if ($field === false) {
        $r = DicteeEnvoyerSon($Dictee, 'lecture_rapide');
        if (!$r || $r instanceof Response)
            return $r;
        return DicteeEnvoyerSon($Dictee, 'lecture_lente');
    }

    if (!isset($_FILES[$field]))
        return false;
    $ext = strtolower(strrchr($_FILES[$field]['name'], '.'));
    if ($ext != '.mp3' && $ext != '.ogg')
        return redirect(
            'Format du fichier audio invalide.',
            'editer-' . $Dictee->id . '-' . rewrite($Dictee->titre) . '.html',
            MSG_ERROR
        );
    $Dictee->format = substr($ext, 1);
    $path = BASEPATH . '/web/uploads/dictees';
    $name = DicteeSon($Dictee, $field);
    return File_Upload::Fichier($_FILES[$field], $path, $name);
}

/**
 * Renvoie le nom du fichier audio associé.
 *
 * @param Dictee $Dictee Dictee.
 */
function DicteeSon(Dictee $Dictee, $field)
{
    return sha1(
            'sdfgurIR}J?F4'
            . $Dictee->id
            . '$' . $field
        ) . '.' . $Dictee->format;
}

/**
 * Corrige une propostion.
 *
 * @param Dictee $Dictee Dictee.
 * @return array(DicteeDiff diff, int note)    Diff et note obtenue
 */
function CorrigerDictee(Dictee $Dictee, $texte)
{
    require_once(__DIR__ . '/Diff.php');

    $Dictee->participations = Doctrine_Query::create()
        ->select('COUNT(*) AS participations')
        ->from('Dictee_Participation')
        ->where('dictee_id = ?', $Dictee->id)
        ->execute()
        ->offsetGet(0)
        ->participations;
    $diff = DicteeDiff::doubleDiff($Dictee, $texte);
    $note = max(0, 20 - $diff->fautes());
    $Dictee->note = (int)(
        ($Dictee->note * $Dictee->participations + $note)
        / ++$Dictee->participations);
    $Dictee->save();

    if (verifier('connecte')) {
        $Participation = new Dictee_Participation;
        $Participation->dictee_id = $Dictee->id;
        $Participation->utilisateur_id = verifier('connecte') ? $_SESSION['id'] : null;
        $Participation->date = new Doctrine_Expression('CURRENT_TIMESTAMP');
        $Participation->fautes = $diff->fautes();
        $Participation->note = $note;
        $Participation->save();
    }
    DicteesEffacerCache();
    return array($diff, $note);
}

/**
 * Statistiques sur les dictées en général.
 *
 * @return object    Statistiques.
 */
function DicteesStatistiques()
{
    $cache = Container::cache();
    if (!$Stats = $cache->fetch('dictees_statistiques')) {
        $Stats = new StdClass;
        $Stats->nombreDictees = Doctrine_Query::create()
            ->select('COUNT(*) AS total')
            ->from('Dictee')
            ->where('etat = ?', DICTEE_VALIDEE)
            ->execute()
            ->offsetGet(0)
            ->total;
        $d = Doctrine_Query::create()
            ->select('SUM(participations) AS total, AVG(note) AS moyenne')
            ->from('Dictee')
            ->where('etat = ?', DICTEE_VALIDEE)
            ->andWhere('participations > 0')
            ->execute()
            ->offsetGet(0);
        $Stats->noteMoyenne = round($d->moyenne, 2);
        $Stats->nombreParticipations = $d->total;

        $cache->save('dictees_statistiques', $Stats, 3600);
    }

    return $Stats;
}


/**
 * Renvoie les tags associés à une dictée.
 *
 * @param  Dictee $Dictee Dictee.
 * @return Doctrine_Collection   Tags.
 */
function DicteeTags(Dictee $Dictee)
{
    return Doctrine_Core::getTable('Dictee')->getTags($Dictee);
}
