<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2019 Corrigraphie
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

namespace Zco\Bundle\ContentBundle\Domain;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Zco\Bundle\ContentBundle\Admin\Admin;
use Zco\Bundle\ContentBundle\DoubleDiff;
use Zco\Container;

/**
 * Gestion des dictées.
 *
 * @author mwsaz@zcorrecteurs.fr
 */
class DictationDAO
{
    /**
     * Creates a new dictation.
     *
     * @param array $data Form data.
     * @return int Identifier of the new dictation.
     */
    public static function AjouterDictee(array $data)
    {
        $Dictee = new \Dictee;
        $Dictee->creation = new \Doctrine_Expression('CURRENT_TIMESTAMP');
        self::handleForm($Dictee, $data);
        $Dictee->save();

        // We proceed in two steps, because we need a stable identifier in order to proceed with uploads.
        self::handleFormUploads($Dictee, $data);
        $Dictee->save();
        self::DicteesEffacerCache();

        return $Dictee->id;
    }

    /**
     * Modifie une dictée.
     *
     * @param \Dictee $Dictee Dictée.
     * @param array $data Form data.
     */
    public static function EditerDictee(\Dictee $Dictee, array $data)
    {
        $Dictee->edition = new \Doctrine_Expression('CURRENT_TIMESTAMP');
        $etat = $Dictee->etat;
        self::handleForm($Dictee, $data);
        self::handleFormUploads($Dictee, $data);

        if ($Dictee->etat != $etat) {
            Container::cache()->delete('dictees_accueil');
        }

        $Dictee->save();
        self::DicteesEffacerCache();

        return true;
    }

    private static function handleForm(\Dictee $Dictee, array $data)
    {
        $Dictee->titre = $data['title'];
        $Dictee->difficulte = $data['level'];
        $Dictee->temps_estime = $data['estimated_time'];
        $Dictee->texte = $data['text'];
        $Dictee->auteur_nom = $data['author_last_name'];
        $Dictee->auteur_prenom = $data['author_first_name'];
        $Dictee->source = $data['source'];
        $Dictee->icone = $data['icon'];
        $Dictee->description = $data['description'];
        $Dictee->indications = $data['indications'];
        $Dictee->commentaires = $data['comments'];
        if ($data['publish']) {
            $Dictee->etat = DICTEE_VALIDEE;
            Container::cache()->delete('dictees_accueil');
        } else {
            $Dictee->etat = DICTEE_BROUILLON;
        }
        $Dictee->utilisateur_id = $_SESSION['id'];
    }

    private static function handleFormUploads(\Dictee $Dictee, array $data)
    {
        if (!is_dir(BASEPATH . '/public/uploads/dictees')) {
            mkdir(BASEPATH . '/public/uploads/dictees', 0777, true);
        }

        if (isset($data['slow_voice'])) {
            self::DicteeEnvoyerSon($Dictee, 'slow_voice', $data['slow_voice']);
        }
        if (isset($data['fast_voice'])) {
            self::DicteeEnvoyerSon($Dictee, 'fast_voice', $data['fast_voice']);
        }
        if (isset($data['icon'])) {
            $ext = $data['icon']->guessExtension();
            if ($Dictee->icone && (strrchr($Dictee->icone, '.') != $ext)) {
                @unlink(BASEPATH . '/web' . $Dictee->icone);
            }
            $directory = BASEPATH . '/public/uploads/dictees';
            $name = $Dictee->id . '.' . $ext;
            $data['icon']->move($directory, $name);
        }
    }

    /**
     * Supprime une dictée.
     *
     * @param Dictee $Dictee Dictée.
     */
    public static function SupprimerDictee(\Dictee $Dictee)
    {
        @unlink(BASEPATH . '/public/uploads/dictees/' . $Dictee->soundFilename('lecture_rapide'));
        @unlink(BASEPATH . '/public/uploads/dictees/' . $Dictee->soundFilename('lecture_lente'));
        \Doctrine_Query::create()
            ->delete('Dictee_Participation')
            ->where('dictee_id = ?', $Dictee->id)
            ->execute();
        self::DicteesEffacerCache();

        return $Dictee->delete();
    }


    /**
     * (dé)Valide une dictée.
     *
     * @param Dictee $Dictee Dictée.
     * @param bool $valide Nouvel état.
     */
    public static function ValiderDictee(\Dictee $Dictee, $valide)
    {
        $Dictee->etat = ($valide ? DICTEE_VALIDEE : DICTEE_BROUILLON);
        $valide && $Dictee->validation = new \Doctrine_Expression('CURRENT_TIMESTAMP');
        $Dictee->save();

        self::DicteesEffacerCache();
        Container::get(Admin::class)->refresh('dictees');
    }

    private static function DicteesEffacerCache()
    {
        foreach (array('accueil', 'statistiques', 'plusJouees') as $c)
            Container::cache()->delete('dictees_' . $c);
    }

    /**
     * Récupère une dictée par son id.
     *
     * @param int $id ID de la dictée à récupérer.
     * @return \Dictee        Dictée.
     */
    public static function Dictee($id)
    {
        $Dictee = \Doctrine_Query::create()
            ->select('d.*, u.id, u.pseudo')
            ->from('Dictee d')
            ->leftJoin('d.Utilisateur u')
            ->where('d.id = ?', $id)
            ->execute();
        $Dictee = $Dictee ? $Dictee[0] : null;

        return $Dictee;
    }

    /**
     * Liste les dictées.
     */
    public static function ListerDictees($published = true)
    {
        $q = \Doctrine_Query::create()
            ->select('d.*, u.id, u.pseudo')
            ->from('Dictee d')
            ->leftJoin('d.Utilisateur u');
        if ($published) {
            $q->where('d.etat = ?', DICTEE_VALIDEE);
            $q->orderBy('d.creation DESC');
        } else {
            $q->orderBy('etat ASC, edition DESC');
        }

        return $q->execute();
    }

    /**
     * Envoie un fichier audio.
     *
     * @param \Dictee $Dictee Dictee.
     * @param string $field
     * @param UploadedFile $file
     */
    private static function DicteeEnvoyerSon(\Dictee $Dictee, string $field, UploadedFile $file)
    {
        /*$ext = $file->guessExtension();
        if ($ext != '.mp3' && $ext != '.ogg')
            return redirect(
                'Format du fichier audio invalide.',
                'editer-' . $Dictee->id . '-' . rewrite($Dictee->titre) . '.html',
                MSG_ERROR
            );*/
        $ext = $file->guessExtension();
        $Dictee->format = $ext;
        $name = $Dictee->soundFilename($field);
        $file->move(BASEPATH . '/public/uploads/dictees', $name);
    }

    /**
     * Corrige une propostion.
     *
     * @param Dictee $Dictee Dictee.
     * @return array(DicteeDiff diff, int note)    Diff et note obtenue
     */
    public static function CorrigerDictee(\Dictee $Dictee, $texte)
    {
        $Dictee->participations = \Doctrine_Query::create()
            ->select('COUNT(*) AS participations')
            ->from('Dictee_Participation')
            ->where('dictee_id = ?', $Dictee->id)
            ->execute()
            ->offsetGet(0)
            ->participations;
        $diff = DoubleDiff::doubleDiff($Dictee, $texte);
        $note = max(0, 20 - $diff->fautes());
        $Dictee->note = (int)(
            ($Dictee->note * $Dictee->participations + $note)
            / ++$Dictee->participations);
        $Dictee->save();

        if (verifier('connecte')) {
            $Participation = new \Dictee_Participation;
            $Participation->dictee_id = $Dictee->id;
            $Participation->utilisateur_id = verifier('connecte') ? $_SESSION['id'] : null;
            $Participation->date = new \Doctrine_Expression('CURRENT_TIMESTAMP');
            $Participation->fautes = $diff->fautes();
            $Participation->note = $note;
            $Participation->save();
        }
        self::DicteesEffacerCache();

        return array($diff, $note);
    }

    /**
     * Statistiques sur les dictées en général.
     *
     * @return object    Statistiques.
     */
    public static function DicteesStatistiques()
    {
        $cache = Container::cache();
        if (!$Stats = $cache->fetch('dictees_statistiques')) {
            $Stats = new \StdClass;
            $Stats->nombreDictees = \Doctrine_Query::create()
                ->select('COUNT(*) AS total')
                ->from('Dictee')
                ->where('etat = ?', DICTEE_VALIDEE)
                ->execute()
                ->offsetGet(0)
                ->total;
            $d = \Doctrine_Query::create()
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
     * Liste les 3 dernières dictées.
     *
     * @return array Les dictées.
     */
    public static function DicteesAccueil()
    {
        $cache = Container::cache();
        if (!$d = $cache->fetch('dictees_accueil')) {
            $dictees = \Doctrine_Query::create()
                ->from('Dictee')
                ->where('etat = ?', DICTEE_VALIDEE)
                ->orderBy('creation DESC')
                ->limit(3)
                ->execute();

            $d = array();
            foreach ($dictees as $dictee)
                $d[] = $dictee;
            $cache->save('dictees_accueil', $d, 120);
        }
        return $d;
    }

    /**
     * Liste les 3 dictées les plus jouées.
     *
     * @return array Les dictées.
     */
    public static function DicteesLesPlusJouees()
    {
        $cache = Container::cache();
        if (!$d = $cache->fetch('dictees_plusJouees')) {
            $dictees = \Doctrine_Query::create()
                ->from('Dictee')
                ->where('etat = ?', DICTEE_VALIDEE)
                ->orderBy('participations DESC')
                ->limit(3)
                ->execute();
            $d = array();
            foreach ($dictees as $dictee)
                $d[] = $dictee;
            $cache->save('dictees_plusJouees', $d, 3600);
        }
        return $d;
    }


    /**
     * Choisit une dictée au hasard.
     *
     * @return \Dictee    La dictée choisie.
     */
    public static function DicteeHasard()
    {
        $cache = Container::cache();
        if (!$d = $cache->fetch('dictees_hasard')) {
            $d = \Doctrine_Query::create()
                ->from('Dictee')
                ->where('etat = ?', DICTEE_VALIDEE)
                ->orderBy('RAND()')
                ->limit(1)
                ->fetchOne();
            $cache->save('dictees_hasard', $d ?: false, 120);
        }
        return $d;
    }
}