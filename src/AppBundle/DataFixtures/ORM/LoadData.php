<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Post;
use AppBundle\Entity\User;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class LoadData
 *
 * @package AppBundle\DataFixtures\ORM
 */
class LoadData implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        // Admin
        $userAdmin = new User();
        $userAdmin->setUsername('root');
        $userAdmin->setPlainPassword('root');
        $userAdmin->setEmail('root@randomemail.re');
        $userAdmin->setEnabled(true);
        $userAdmin->setRoles(array('ROLE_ADMIN'));
        $manager->persist($userAdmin);

        // User
        $user1 = new User();
        $user1->setUsername('user');
        $user1->setPlainPassword('user');
        $user1->setEmail('user@randomemail.re');
        $user1->setEnabled(true);
        $user1->setRoles(array('ROLE_USER'));
        $manager->persist($user1);

        // Posts
        $now = new \DateTime();
        for ($i = 1; $i <= 100; $i++) {
            /** @var Post[] $post */
            $post[$i] = new Post();
            $post[$i]->setTitle('Title ' . $i);
            $post[$i]->setContent($this->getPostContent());
            $post[$i]->setVisible(rand(0, 1));
            $post[$i]->setRating(rand(1, 5));
            $mod = clone $now;
            $post[$i]->setPublishedAt($mod->add(new \DateInterval("P{$i}D")));

            if (rand(0, 1)) {
                $post[$i]->setCreatedBy($user1);
            } else {
                $post[$i]->setCreatedBy($userAdmin);
            }

            $manager->persist($post[$i]);
        }

        $manager->flush();
    }

    /**
     * Get post content.
     *
     * @return text
     */
    private function getPostContent()
    {
        $content = "But I must explain to you how all this mistaken idea of denouncing pleasure and praising pain was born and I will give you a complete account of
        the system, and expound the actual teachings of the great explorer of the truth, the master-builder of human happiness. No one rejects, dislikes, or avoids
        pleasure itself, because it is pleasure, but because those who do not know how to pursue pleasure rationally encounter consequences that are extremely painful.
        Nor again is there anyone who loves or pursues or desires to obtain pain of itself, because it is pain, but because occasionally circumstances occur in which toil
        and pain can procure him some great pleasure. To take a trivial example, which of us ever undertakes laborious physical exercise, except to obtain some advantage
        from it? But who has any right to find fault with a man who chooses to enjoy a pleasure that has no annoying consequences, or one who avoids a pain that produces
        no resultant pleasure?";

        return $content;
    }
}
