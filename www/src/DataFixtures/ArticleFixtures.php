<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');

        // Create admin
        $admin = new User();
        $admin->setUsername('Admin')
            ->setEmail('admin@admin.fr')
            ->setPassword('admin')
            ->setIsAdmin(1);
        $manager->persist($admin);

        // create fake category
        for ($i = 1; $i<=3; $i++) {
            $category = new Category();
            $category->setTitle($faker->sentence(1, true))
                     ->setDescription($faker->realText($faker->numberBetween(60, 60)));

            $manager->persist($category);
            $manager->flush();

            $currentPlaces = ['toreview', 'rejected', 'published'];

            // create fake users
            $users = [];

            for ($j = 1; $j<=mt_rand(4,6); $j++) {
                $user = new User();
                $user->setUsername($faker->userName)
                    ->setEmail($faker->email)
                    ->setPassword($faker->password)
                    ->setIsAdmin(0);

                $manager->persist($user);

                $users[] = $user;
            }

            // create fake articles
            for ($j = 1; $j<=mt_rand(4,6); $j++) {
                $article = new Article();

                $content = '<p>' . join('</p><p>', (array)$faker->realText($faker->numberBetween(60, 60))) . '</p>';

                $article->setTitle($faker->sentence(3, true))
                    ->setContent($content)
                    ->setImage("https://source.unsplash.com/random/300x150")
                    ->setUser($users[rand(0, count($users) - 1)])
                    ->setCreatedAt($faker->dateTimeBetween('-6 months'))
                    ->setCurrentPlace($currentPlaces[rand(0, 2)])
                    ->setCategory($category);

                $manager->persist($article);

                // create fake comments
                for ($k = 1; $k<=mt_rand(4,10); $k++) {
                    $comment = new Comment();

                    $content = '<p>' . join('</p><p>', (array)$faker->realText($faker->numberBetween(60, 60))) . '</p>';
                    $days = (new \DateTime())->diff($article->getCreatedAt())->days;

                    $comment->setUser($users[rand(0, count($users) - 1)])
                            ->setContent($content)
                            ->setCreatedAt($faker->dateTimeBetween('-' . $days . 'days'))
                            ->setCurrentPlace($currentPlaces[rand(0, 2)])
                            ->setArticle($article);

                    $manager->persist($comment);
                }
            }
        }

        $manager->flush();
    }
}
