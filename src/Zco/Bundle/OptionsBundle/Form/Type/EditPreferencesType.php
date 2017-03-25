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

namespace Zco\Bundle\OptionsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulaire permettant de modifier le profil d'un utilisateur.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
class EditPreferencesType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email_on_mp', null, array(
            'label' => 'M\'avertir par courriel quand je reçois un MP',
            'required' => false,
        ));
        $builder->add('time_difference', 'choice', array(
            'label' => 'Mon fuseau horaire',
            'choices' => array(
                -43200 => '(GMT -12:00) Eniwetok, Kwajalein',
                -39600 => '(GMT -11:00) Midway Island, Samoa',
                -36000 => '(GMT -10:00) Hawaii',
                -32400 => '(GMT -9:00) Alaska',
                -28800 => '(GMT -8:00) Pacific Time (US &amp; Canada)',
                -25200 => '(GMT -7:00) Mountain Time (US &amp; Canada)',
                -21600 => '(GMT -6:00) Central Time (US &amp; Canada), Mexico City',
                -18000 => '(GMT -5:00) Eastern Time (US &amp; Canada), Bogota, Lima',
                -14400 => '(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz',
                -12600 => '(GMT -3:30) Newfoundland',
                -10800 => '(GMT -3:00) Brazil, Buenos Aires, Georgetown',
                -7200 => '(GMT -2:00) Mid-Atlantic',
                -3600 => '(GMT -1:00) Azores, Cape Verde Islands',
                0 => '(GMT) Western Europe Time, London, Lisbon, Casablanca',
                3600 => '(GMT +1:00) Brussels, Copenhagen, Madrid, Paris',
                7200 => '(GMT +2:00) Kaliningrad, South Africa',
                10800 => '(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg',
                12600 => '(GMT +3:30) Tehran',
                14400 => '(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi',
                16200 => '(GMT +4:30) Kabul',
                18000 => '(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent',
                19800 => '(GMT +5:30) Bombay, Calcutta, Madras, New Delhi',
                14950 => '(GMT +5:45) Kathmandu',
                21600 => '(GMT +6:00) Almaty, Dhaka, Colombo',
                25200 => '(GMT +7:00) Bangkok, Hanoi, Jakarta',
                28800 => '(GMT +8:00) Beijing, Perth, Singapore, Hong Kong',
                32400 => '(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk',
                34200 => '(GMT +9:30) Adelaide, Darwin',
                36000 => '(GMT +10:00) Eastern Australia, Guam, Vladivostok',
                39600 => '(GMT +11:00) Magadan, Solomon Islands, New Caledonia',
                43200 => '(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka',
            )
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'UserPreference',
        ]);
    }
}