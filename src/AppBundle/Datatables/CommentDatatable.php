<?php

namespace AppBundle\Datatables;

use Sg\DatatablesBundle\Datatable\View\AbstractDatatableView;
use Sg\DatatablesBundle\Datatable\View\Style;

/**
 * Class CommentDatatable
 *
 * @package AppBundle\Datatables
 */
class CommentDatatable extends AbstractDatatableView
{
    /**
     * {@inheritdoc}
     */
    public function buildDatatable(array $options = array())
    {
        $this->features->set(array(
            'scroll_x' => false,
            'searching' => true,
        ));

        $this->ajax->set(array(
            'url' => $this->router->generate('comment_results', array('id' => $options['rowId'])),
            'type' => 'GET',
            'pipeline' => 0
        ));

        $this->options->set(array(
            'class' => Style::BOOTSTRAP_3_STYLE,
            'individual_filtering' => false,
            'individual_filtering_position' => 'head',
            'use_integration_options' => true,
            'row_id' => 'id'
        ));

        $this->columnBuilder
            ->add('id', 'column', array(
                'title' => 'Id',
            ))
            ->add('post.id', 'column', array(
                'title' => 'Post Id',
            ))
            ->add('title', 'column', array(
                'title' => 'Title',
            ))
            ->add('publishedAt', 'datetime', array(
                'title' => 'PublishedAt',
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return 'AppBundle\Entity\Comment';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'comment_datatable';
    }
}
