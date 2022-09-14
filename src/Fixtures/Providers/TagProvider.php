<?php

namespace App\Fixtures\Providers;

class TagProvider
{
    public function randomTag(): string
    {
        $tagList = [
            'C',
            'C++',
            'C#',
            'Python',
            'Ruby',
            'React',
            'Carbon',
            '.NET',
            'Ruby on Rails',
            'MongoDB',
            'MariaDB',
        ];

        return $tagList[array_rand($tagList)];
    }
}
