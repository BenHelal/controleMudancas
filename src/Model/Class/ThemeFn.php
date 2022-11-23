<?php

namespace App\Model\Class;

use App\Entity\Theme;
use App\Entity\ThemeAdmin;

class ThemeFn
{

    public function getTheme($em, $type)
    {
        if ($type == 'admin') {
            $theme = $em->getRepository(ThemeAdmin::class)->findAll();
            if ($theme == null) {
                $theme = new ThemeAdmin();
                $theme->setColorBar('#d1e9c9');
                $theme->setTitleColor('#212529');
                $theme->setErrorColor('#b0413e');
                $theme->setWarrningColor('#ffe5c2');
                $theme->setValidColor('#4f805d');
                $theme->setLogo('logoAdmin.png');
                $em->persist($theme);
                $em->flush();
            } else {
                $theme = $theme[0];
            }
            return $theme;
        } else {
            $theme = $em->getRepository(Theme::class)->findAll();
            if ($theme == null) {
                $theme = new Theme();
                $theme->setColorBar('#d4eee5');
                $theme->setTitleColor('#000000');
                $theme->setErrorColor('#894b84');
                $theme->setWarrningColor('#e87373');
                $theme->setValidColor('#90f8b5');
                $theme->setLogo('logoUser.png');
                $em->persist($theme);
                $em->flush();
            } else {
                $theme = $theme[0];
            }
            return $theme;
        }
    }
}
