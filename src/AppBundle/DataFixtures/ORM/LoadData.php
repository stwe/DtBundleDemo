<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Comment;
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

            // Comments
            for ($c = 1; $c <= 4; $c++) {
                /** @var Comment[] $comment */
                $comment[$c] = new Comment();
                $comment[$c]->setTitle('Comment ' . $c);
                $comment[$c]->setContent($this->getCommentContent());
                $post[$i]->addComment($comment[$c]);
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

    /**
     * Get comment content.
     *
     * @return string
     */
    private function getCommentContent()
    {
        $content = "Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab
        illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit,
        sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur,
        adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum
        exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit
        esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur? At vero eos et accusamus et iusto odio dignissimos ducimus
        qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in
        culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta
        nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere";

        return $content;
    }
}
