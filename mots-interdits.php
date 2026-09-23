<?php

/* ==================================================
   FILTRE AUTOMATIQUE DE MODÉRATION
================================================== */

function normaliserTexteModeration(string $texte): string
{
    $texte = mb_strtolower($texte, 'UTF-8');

    $texte = strtr($texte, [
        'à'=>'a','á'=>'a','â'=>'a','ä'=>'a','ã'=>'a','å'=>'a',
        'ç'=>'c','è'=>'e','é'=>'e','ê'=>'e','ë'=>'e',
        'ì'=>'i','í'=>'i','î'=>'i','ï'=>'i','ñ'=>'n',
        'ò'=>'o','ó'=>'o','ô'=>'o','ö'=>'o','õ'=>'o',
        'ù'=>'u','ú'=>'u','û'=>'u','ü'=>'u',
        'ý'=>'y','ÿ'=>'y','œ'=>'oe','æ'=>'ae'
    ]);

    $texte = strtr($texte, [
        '0'=>'o','1'=>'i','3'=>'e','4'=>'a',
        '5'=>'s','7'=>'t','@'=>'a','$'=>'s'
    ]);

    return $texte;
}

function contientTermeInterdit(string $texte): bool
{
    $texte = normaliserTexteModeration($texte);

    $termesInterdits = [
        /*
         * INSULTES / VULGARITÉS
         * Certaines racines permettent de couvrir plusieurs variantes.
         */
        'abruti', 'abrutie', 'abrutis',
        'con', 'conne', 'connard', 'connasse',
        'encule', 'enculee', 'enculer',
        'salope', 'salopard',
        'pute', 'putain', 'putasse',
        'fdp', 'fils de pute',
        'batard', 'batarde',
        'merde', 'emmerde',
        'nique ta mere', 'niquer ta mere', 'ntm',
        'trou du cul', 'ducon',
        'debile', 'cretin', 'cretine',
        'ordure', 'pourriture',
        'ta gueule', 'tg',

        /*
         * CONTENU SEXUEL EXPLICITE / OBSCÈNE
         * Le but est de bloquer le contenu explicitement sexuel,
         * pas les mots neutres utilisés dans un contexte normal.
         */
        'bite', 'teub', 'queue',
        'chatte', 'foufoune',
        'couilles', 'burnes',
        'branler', 'branlette', 'masturbe', 'masturbation',
        'sucer', 'suce moi', 'pipe',
        'baiser', 'baise moi',
        'sodomie', 'sodomiser',
        'ejacule', 'ejaculation',
        'orgasme',
        'porn', 'porno', 'pornographie',
        'sextape', 'nudes', 'nude',
        'gangbang', 'bukkake',
        'fellatio', 'cunnilingus',

        /*
         * HOMOPHOBIE / TRANSPHOBIE
         * Termes employés comme insultes ou attaques.
         */
        'pd', 'pede', 'pedale',
        'tapette', 'gouine', 'fiotte',
        'travelo',
        'sale homo', 'sale gay',
        'sale lesbienne', 'sale trans',
        'mort aux gays', 'mort aux homos',
        'tuer les gays', 'tuer les homos',

        /*
         * RACISME / XÉNOPHOBIE / ANTISÉMITISME
         */
        'negre', 'negro',
        'bougnoule', 'bamboula',
        'youpin', 'youpine',
        'chinetoque',
        'sale arabe', 'sale noir', 'sale noire',
        'sale africain', 'sale africaine',
        'sale asiatique',
        'sale juif', 'sale juive',
        'sale musulman', 'sale musulmane',
        'sale rom', 'sale gitan',
        'retourne dans ton pays',
        'retourne en afrique',
        'retourne chez toi',
        'mort aux juifs', 'mort aux arabes',
        'mort aux noirs', 'mort aux musulmans',
        'tuer les juifs', 'tuer les arabes',
        'tuer les noirs', 'tuer les musulmans',

        /*
         * SEXISME / MISOGYNIE
         */
        'sale femme',
        'femme inferieure',
        'femmes inferieures',
        'retourne a la cuisine',
        'toutes des putes',
        'toutes des salopes',

        /*
         * VALIDISME / ATTAQUES DÉGRADANTES
         */
        'mongol', 'mongolien',
        'attarde', 'attardee',
        'sale handicape', 'sale handicapee',

        /*
         * APPELS EXPLICITES À LA VIOLENCE / HAINE
         */
        'va te tuer',
        'tue toi',
        'tuez les',
        'exterminer les',
        'extermination des',
        'bruler les',
        'il faut tuer les',
        'il faut exterminer les'
    ];

    foreach ($termesInterdits as $terme) {
        $terme = normaliserTexteModeration($terme);

        if (preg_match('/^[a-z0-9]+$/', $terme)) {
            $motif = '/(?<![a-z0-9])'
                . preg_quote($terme, '/')
                . '(?![a-z0-9])/u';

            if (preg_match($motif, $texte)) {
                return true;
            }
        } elseif (mb_strpos($texte, $terme, 0, 'UTF-8') !== false) {
            return true;
        }
    }

    return false;
}
