<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\QueryBuilder;

/**
 * Comment controller.
 *
 * @Route("/comment")
 *
 * @package AppBundle\Controller
 */
class CommentController extends Controller
{
    /**
     * @param Request $request
     *
     * @Route("/{id}", name="comment_index", options={"expose"=true})
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     *
     * @return Response
     */
    public function indexAction(Request $request, $id)
    {
        $isAjax = $request->isXmlHttpRequest();

        if ($isAjax) {
            $options = array('rowId' => $id);
            $datatable = $this->get('app.datatable.comment');
            $datatable->buildDatatable($options);

            $response = $this->renderView('comment/index.html.twig', array(
                'comments_datatable' => $datatable,
            ));


            return new Response($response, 200);
        }

        return new Response('Bad Request', 400);
    }

    /**
     * @param $id
     *
     * @Route("/results/{id}", name="comment_results", options={"expose"=true})
     * @Security("has_role('ROLE_USER')")
     *
     * @return Response
     * @throws \Exception
     */
    public function indexResultsAction($id)
    {
        $options = array('rowId' => $id);
        $datatable = $this->get('app.datatable.comment');
        $datatable->buildDatatable($options);

        $query = $this->get('sg_datatables.query')->getQueryFrom($datatable);

        $function = function (QueryBuilder $qb) use ($id) {
            $qb->andWhere('post.id = :id');
            $qb->setParameter('id', $id);
        };

        $query->addWhereAll($function);

        return $query->getResponse();
    }
}
