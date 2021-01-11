<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');

        // create fake category
        for ($i = 1; $i<=3; $i++) {
            $category = new Category();
            $category->setTitle($faker->sentence(1, true))
                     ->setDescription($faker->realText($faker->numberBetween(60, 60)));

            $manager->persist($category);
            $manager->flush();

            // create fake articles
            for ($j = 1; $j<=mt_rand(4,6); $j++) {
                $article = new Article();

                $content = '<p>' . join((array)$faker->realText($faker->numberBetween(60, 60)), '</p><p>') . '</p>';

                $article->setTitle($faker->sentence(3, true))
                    ->setContent($content)
                    ->setImage("https://source.unsplash.com/random/300x150")
                    ->setCreatedBy($faker->userName)
                    ->setCreatedAt($faker->dateTimeBetween('-6 months'))
                    ->setCategory($category);

                $manager->persist($article);

                // create fake comments
                for ($k = 1; $k<=mt_rand(4,10); $k++) {
                    $comment = new Comment();

                    $content = '<p>' . join((array)$faker->realText($faker->numberBetween(60, 60)), '</p><p>') . '</p>';
                    $days = (new \DateTime())->diff($article->getCreatedAt())->days;

                    $comment->setAuthor($faker->userName)
                            ->setContent($content)
                            ->setCreatedAt($faker->dateTimeBetween('-' . $days . 'days'))
                            ->setArticle($article);

                    $manager->persist($comment);
                }
            }
        }

        $manager->flush();
    }
}
