<?php

/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012 Corrigraphie
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

namespace Zco\Bundle\QuizBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ApiController extends Controller
{
    /**
     * Se charge de la soumission du quiz en Ajax.
     */
    public function correctionAction()
    {
        $manager = $this->get('zco_quiz.manager.quiz');
        $quiz = $manager->get($_POST['quiz_id']);
        if ($quiz === false || !$quiz->visible) {
            throw new NotFoundHttpException();
        }

        $questions = $manager->findQuestions($quiz['id'], $_POST['rep']);
        $note = $quiz->Soumettre($questions);
        $note = 'Vous avez obtenu <strong>' . $note . '/20</strong>.';

        $ret = array('note' => $note, 'reponses' => array());

        foreach ($questions as $question) {
            if ($_POST['rep' . $question['id']] == $question['reponse_juste'] && $_POST['rep' . $question['id']] != 0) {
                $tmp = '<div class="correction juste"><span class="type">Bonne réponse</span><br />';
            } else {
                $tmp = '<div class="correction faux"><span class="type">Mauvaise réponse</span><br />' .
                    'La bonne réponse était : <em>' . $this->get('zco_parser.parser')->parse($question['reponse' . $question['reponse_juste']]) . '</em></p>';
            }

            if (!empty($question['explication'])) {
                $tmp .= '<p>Cette question dispose d\'une explication lui étant associée.' .
                    //' - <a href="#" onclick="$(\'explication_'.$question['id'].'\').slide(); return false;" class="gras">'.
                    //'Afficher</a>'.
                    '</p>' .
                    '<div id="explication_' . $question['id'] . '" class="explication">' .
                    $this->get('zco_parser.parser')->parse($question['explication']) . '</div>';
            }

            $ret['reponses'][$question['id']] = $tmp . '</div>';
            $ret['achoisi'][$question['id']] = $_POST['rep' . $question['id']];
            $ret['enfait'][$question['id']] = $question['reponse_juste'];
        }

        return new Response(json_encode($ret));
    }
}